<?php


namespace PaypalAddons\classes\API;


use PaypalAddons\classes\API\Builder\AmountBuilder;
use PaypalAddons\classes\API\Builder\BuilderAbstract;
use PaypalAddons\classes\API\Builder\ItemListBuilder;
use PaypalAddons\classes\API\Builder\PayerBuilder;
use PaypalAddons\classes\API\Builder\PayerInfoBuilder;
use PaypalAddons\classes\API\Builder\PaymentBuilder;
use PaypalAddons\classes\API\Builder\RedirectUrlsBuilder;
use PaypalAddons\classes\API\Builder\ShippingAddressBuilder;
use PaypalAddons\classes\API\Builder\TransactionBuilder;
use PaypalAddons\classes\API\Builder\WebProfileBuilder;
use PaypalPPBTlib\AbstractMethod;

class PaypalApiManager
{
    /** @var \Context*/
    protected $context;

    /** @var AbstractMethod*/
    protected $method;

    public function __construct(\Context $context, AbstractMethod $method)
    {
        $this->setContext($context);
        $this->setMethod($method);
    }

    /**
     * @param String $builderKey
     * @return BuilderAbstract
     * */
    public function get($class)
    {
        $builder = new $class($this->context, $this->method, $this);
        return $builder;
    }

    public function setContext(\Context $context)
    {
        $this->context = $context;
    }

    public function setMethod(AbstractMethod $method)
    {
        $this->method = $method;
    }
}
