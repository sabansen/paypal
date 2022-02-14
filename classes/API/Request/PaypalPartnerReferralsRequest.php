<?php
/**
 * 2007-2022 PayPal
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the Academic Free License (AFL 3.0)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://opensource.org/licenses/afl-3.0.php
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *
 *  DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 2007-2022 PayPal
 *  @author 202 ecommerce <tech@202-ecommerce.com>
 *  @copyright PayPal
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace PaypalAddons\classes\API\Request;

use PaypalAddons\classes\AbstractMethodPaypal;
use PaypalAddons\classes\API\ExtensionSDK\PartnerReferrals;
use PaypalAddons\classes\API\Response\Error;
use PaypalAddons\classes\API\Response\ResponsePartnerReferrals;
use PaypalAddons\services\Builder\PartnerReferralsRequestBody;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalHttp\HttpException;
use Symfony\Component\VarDumper\VarDumper;

class PaypalPartnerReferralsRequest extends RequestAbstract
{
    protected $bodyBuilder;

    public function __construct(PayPalHttpClient $client, AbstractMethodPaypal $method)
    {
        parent::__construct($client, $method);

        $this->bodyBuilder = new PartnerReferralsRequestBody($method);
    }

    public function execute()
    {
        $response = $this->getResponse();
        $partnerReferral = new PartnerReferrals();
        $partnerReferral->headers = array_merge($partnerReferral->headers, $this->getHeaders());
        $partnerReferral->body = $this->buildRequestBody();

        try {
            $exec = $this->client->execute($partnerReferral);
        } catch (\Throwable $e) {
            return $response->setSuccess(false);
        }

        //todo: parse action_url and self link from paypal response
        $response->setSuccess(true);
        $response->setData($exec);

        return $response;
    }

    public function buildRequestBody()
    {
        return $this->bodyBuilder->build();
    }

    /** @return ResponsePartnerReferrals*/
    protected function getResponse()
    {
        return new ResponsePartnerReferrals();
    }
}
