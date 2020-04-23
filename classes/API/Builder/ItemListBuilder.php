<?php


namespace PaypalAddons\classes\API\Builder;


use PayPal\Api\Item;
use PayPal\Api\ItemList;

class ItemListBuilder extends BuilderAbstract
{
    /**
     * @return ItemList
     */
    public function build()
    {
        $currency = $this->module->getPaymentCurrencyIso();
        $productItems = $this->getProductItems($currency);
        $discountItems = $this->getDiscountItmes($currency);
        $itemsLists = new ItemList();
        $itemsLists->setItems(array_merge($productItems, $discountItems));

        if ($this->context->cart->gift && $this->context->cart->getGiftWrappingPrice()) {
            $itemsLists->addItem($this->getWrappingItem($currency));
        }

        return $itemsLists;
    }

    /**
     * @param $currency String
     * @return Item[]
     */
    protected function getProductItems($currency)
    {
        $items = [];
        $products = $this->context->cart->getProducts();
        foreach ($products as $product) {
            $product['product_tax'] = $this->method->formatPrice($product['price_wt']) - $this->method->formatPrice($product['price']);
            $item = new Item();
            $item->setName(\Tools::substr($product['name'], 0, 126))
                ->setCurrency($currency)
                ->setDescription(isset($product['attributes']) ? $product['attributes'] : '')
                ->setQuantity($product['quantity'])
                ->setSku($product['id_product'])
                ->setPrice($this->method->formatPrice($product['price']));

            $items[] = $item;
        }

        return $items;
    }

    /**
     * @param $currency String
     * @return Item[]
     */
    protected function getDiscountItmes($currency)
    {
        $totalDiscounts = $this->context->cart->getOrderTotal(false, \Cart::ONLY_DISCOUNTS);
        $items = [];
        if ($totalDiscounts > 0) {
            $discountItem = new Item();
            $discountItem->setName($this->module->l('Total discounts', get_class($this)))
                ->setCurrency($currency)
                ->setQuantity(1)
                ->setSku('discounts')
                ->setPrice($this->method->formatPrice(-1 * $totalDiscounts));

            $items[] = $discountItem;
        }

        return $items;
    }

    /**
     * @param $currency String
     * @return Item
     */
    protected function getWrappingItem($currency)
    {
        $wrapping_price = $this->formatPrice($this->context->cart->getGiftWrappingPrice());
        $item = new Item();
        $item->setName('Gift wrapping')
            ->setCurrency($currency)
            ->setQuantity(1)
            ->setSku('wrapping')
            ->setPrice($wrapping_price);

        return $item;
    }
}
