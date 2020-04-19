<?php


namespace PaypalAddons\classes\API\Builder;


use PaypalAddons\classes\API\PaypalApiManager;
use PaypalPPBTlib\AbstractMethod;

abstract class BuilderAbstract implements BuilderInteface
{
    /** @var \Context*/
    protected $context;

    /** @var AbstractMethod*/
    protected $method;

    /** @var \Module*/
    protected $module;

    /** @var PaypalApiManager*/
    protected $paypalApiManager;

    public function __construct(\Context $context, AbstractMethod $method, PaypalApiManager $paypalApiManager)
    {
        $this->context = $context;
        $this->method = $method;
        $this->module = \Module::getInstanceByName($method->name);
        $this->paypalApiManager = $paypalApiManager;
    }

    abstract public function build();

}
