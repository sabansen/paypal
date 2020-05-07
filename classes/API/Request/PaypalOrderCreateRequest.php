<?php


namespace PaypalAddons\classes\API\Request;


use PaypalAddons\classes\AbstractMethodPaypal;
use PaypalAddons\classes\API\Request\RequestAbstract;
use PaypalAddons\classes\API\Response\Error;
use PaypalAddons\classes\API\Response\ResponseOrderCreate;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalHttp\HttpException;
use Symfony\Component\VarDumper\VarDumper;

class PaypalOrderCreateRequest extends RequestAbstract
{
    public function execute()
    {
        $response = new ResponseOrderCreate();
        $order = new OrdersCreateRequest();
        $order->body = $this->buildRequestBody();
        $order->headers = array_merge($this->getHeaders(), $order->headers);

        try {
            $exec = $this->client->execute($order);

            if (in_array($exec->statusCode, [200, 201, 202])) {
                $response->setSuccess(true)
                    ->setData($exec)
                    ->setPaymentId($exec->result->id)
                    ->setStatusCode($exec->statusCode)
                    ->setApproveLink($this->getLink('approve', $exec->result->links));
            } elseif ($exec->statusCode == 204) {
                $response->setSuccess(true);
            } else {
                $error = new Error();
                $resultDecoded = json_decode($exec->message);
                $error->setMessage($resultDecoded->message);
                $response->setSuccess(false)
                    ->setError($error);
            }
        } catch (HttpException $e) {
            $error = new Error();
            $resultDecoded = json_decode($e->getMessage());
            $error->setMessage($resultDecoded->details[0]->description)->setErrorCode($e->getCode());
            $response->setSuccess(false)
                ->setError($error);
        } catch (\Exception $e) {
            $error = new Error();
            $error->setMessage($e->getMessage())
                ->setErrorCode($e->getCode());
            $response->setSuccess(false)
                ->setError($error);
        }
        return $response;
    }

    /**
     * @param $nameLink string
     * @param $links array
     * @return string
     */
    protected function getLink($nameLink, $links)
    {
        foreach ($links as $link) {
            if ($link->rel == $nameLink) {
                return $link->href;
            }
        }

        return '';
    }

    /**
     * @return array
     */
    protected function buildRequestBody()
    {
        $currency = $this->getCurrency();
        $productItmes = $this->getProductItems($currency);
        $wrappingItems = $this->getWrappingItems($currency);
        $items = array_merge($productItmes, $wrappingItems);
        $payer = $this->getPayer();
        $shippingInfo = $this->getShippingInfo();

        $body = [
            'intent' => $this->getIntent(),
            'application_context' => $this->getApplicationContext(),
            'purchase_units' => [
                [
                    'amount' => $this->getAmount($currency),
                    'items' => $items,
                    'custom_id' => $this->getCustomId()
                ],
            ],
        ];

        if (empty($payer) == false) {
            $body['payer'] = $payer;
        }

        if (empty($shippingInfo) == false) {
            $body['purchase_units'][0]['shipping'] = $shippingInfo;
        }

        return $body;
    }

    /**
     * @return array
     */
    protected function getPayer()
    {
        $payer = [];

        if (\Validate::isLoadedObject($this->context->customer) == false) {
            return $payer;
        }

        $payer['name'] = [
            'given_name' => $this->context->customer->firstname,
            'surname' => $this->context->customer->lastname
        ];
        $payer['email'] = $this->context->customer->email;
        $payer['address'] = $this->getAddress();

        if ($this->method instanceof \MethodMB) {
            $taxInfo = $this->method->getPayerTaxInfo();

            if (empty($taxInfo) == false) {
                $payer['tax_info'] = $taxInfo;
            }
        }

        return $payer;
    }

    /**
     * @return string
     */
    protected function getCurrency()
    {
        return $this->module->getPaymentCurrencyIso();
    }

    /**
     * @param $currency string Iso code
     * @return array
     */
    protected function getProductItems($currency)
    {
        $items = [];

        $products = $this->context->cart->getProducts();
        foreach ($products as $product) {
            $item = [];
            $productObj = new \Product((int)$product['id_product'], null, $this->context->cart->id_lang);
            $priceIncl = $this->formatPrice($productObj->getPrice(true, $product['id_product_attribute']));
            $priceExcl = $this->formatPrice($productObj->getPrice(false, $product['id_product_attribute']));
            $productTax = $this->formatPrice($priceIncl - $priceExcl);

            $item['name'] = \Tools::substr($productObj->name, 0, 126);
            $item['description'] = isset($product['attributes']) ? $product['attributes'] : '';;
            $item['sku'] = $productObj->id;
            $item['unit_amount'] = [
                'currency_code' => $currency,
                'value' => $priceExcl
            ];
            $item['tax'] = [
                'currency_code' => $currency,
                'value' => $productTax
            ];
            $item['quantity'] = $product['quantity'];

            $items[] = $item;
        }

        return $items;
    }

    /**
     * @param $currency string Iso code
     * @return array
     */
    protected function getDiscountItems($currency)
    {
        return [];
        $items = [];
        $totalDiscountsInc = $this->context->cart->getOrderTotal(true, \Cart::ONLY_DISCOUNTS);

        if ($totalDiscountsInc > 0) {
            $item = [];
            $totalDiscountsExcl = $this->context->cart->getOrderTotal(false, \Cart::ONLY_DISCOUNTS);
            $totalTaxDiscounts = $totalDiscountsInc - $totalDiscountsExcl;

            $item['name'] = $this->module->l('Total discount', get_class($this));
            $item['sku'] = $this->context->cart->id;
            $item['unit_amount'] = [
                'currency_code' => $currency,
                'value' => $this->method->formatPrice(-1 * $totalDiscountsExcl)
            ];
            $item['tax'] = [
                'currency_code' => $currency,
                'value' => $this->method->formatPrice(-1 * $totalTaxDiscounts)
            ];
            $item['quantity'] = 1;

            $items[] = $item;
        }

        return $items;
    }

    /**
     * @param $currency string Iso code
     * @return array
     */
    protected function getAmount($currency)
    {
        $shippingTotal = $this->context->cart->getTotalShippingCost();
        $subTotalExcl = $this->context->cart->getOrderTotal(false, \Cart::ONLY_PRODUCTS);
        $subTotalIncl = $this->context->cart->getOrderTotal(true, \Cart::ONLY_PRODUCTS);
        $subTotalTax = $subTotalIncl - $subTotalExcl;
        $totalOrder = $this->context->cart->getOrderTotal(true, \Cart::BOTH);
        $discountTotal = $this->context->cart->getOrderTotal(true, \Cart::ONLY_DISCOUNTS);

        $amount = array(
            'currency_code' => $currency,
            'value' => $this->method->formatPrice($totalOrder),
            'breakdown' =>
                array(
                    'item_total' => array(
                            'currency_code' => $currency,
                            'value' => $this->method->formatPrice($subTotalExcl),
                        ),
                    'shipping' => array(
                            'currency_code' => $currency,
                            'value' => $this->method->formatPrice($shippingTotal),
                        ),
                    'tax_total' => array(
                            'currency_code' => $currency,
                            'value' => $this->method->formatPrice($subTotalTax),
                        ),
                    'discount' => array(
                            'currency_code' => $currency,
                            'value' => $discountTotal
                        )
                ),
        );

        return $amount;
    }

    protected function getWrappingItems($currency)
    {
        $items = [];

        if ($this->context->cart->gift && $this->context->cart->getGiftWrappingPrice()) {
            $item = [];
            $priceIncl = $this->context->cart->getGiftWrappingPrice(true);
            $priceExcl = $this->context->cart->getGiftWrappingPrice(false);
            $tax = $priceIncl - $priceExcl;

            $item['name'] = $this->module->l('Gift wrapping', get_class($this));
            $item['sku'] = $this->context->cart->id;
            $item['unit_amount'] = [
                'currency_code' => $currency,
                'value' => $this->method->formatPrice($priceExcl)
            ];
            $item['tax'] = [
                'currency_code' => $currency,
                'value' => $this->method->formatPrice($tax)
            ];
            $item['quantity'] = 1;

            $items[] = $item;
        }

        return $items;
    }

    /**
     * @return array
     */
    protected function getApplicationContext()
    {
        $applicationContext = [
            'locale' => $this->context->language->iso_code,
            'landing_page' => 'BILLING',
            'shipping_prefernces' => 'SET_PROVIDED_ADDRESS',
            'return_url' => $this->method->getReturnUrl(),
            'cancel_url' => $this->method->getCancelUrl(),
            'brand_name' => $this->getBrandName()
        ];

        return $applicationContext;
    }

    /**
     * @return array
     */
    protected function getShippingInfo()
    {
        if ($this->context->cart->id_address_delivery == false) {
            return [];
        }
        $shippingInfo = [
            'address' => $this->getAddress()
        ];

        return $shippingInfo;
    }

    /**
     * @return array
     */
    protected function getAddress()
    {
        $address = new \Address($this->context->cart->id_address_delivery);
        $country = new \Country($address->id_country);

        $addressArray = [
            'address_line_1' => $address->address1,
            'address_line_2' => $address->address2,
            'postal_code' => $address->postcode,
            'country_code' => $country->iso_code,
            'admin_area_2' => $address->city,
        ];

        if ($address->id_state) {
            $state = new \State($address->id_state);
            $addressArray['address_line_1'] = $state->iso_code;
        }

        return $addressArray;
    }

    /**
     * @return string
     */
    protected function getIntent()
    {
        return $this->method->getIntent();
    }

    protected function getCustomId()
    {
        return $this->method->getCustomFieldInformation($this->context->cart);
    }

    protected function getBrandName()
    {
        return $this->method->getBrandName();
    }
}
