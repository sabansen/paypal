<?php
/**
 * 2007-2020 PayPal
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
 *  @author 2007-2020 PayPal
 *  @author 202 ecommerce <tech@202-ecommerce.com>
 *  @copyright PayPal
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace PaypalAddons\classes\Shortcut\Form\Field;

use Context;

class TextInput implements FieldInteface
{
    /** @var string*/
    protected $name;

    /** @var string*/
    protected $value;

    /** @var string*/
    protected $label;

    /** @var string*/
    protected $type;

    public function __construct(string $name, string $value, string $label, $type = null)
    {
        $this->setName($name);
        $this->setValue($value);
        $this->setLabel($label);
        $this->setType($type);
    }

    public function render()
    {
        return Context::getContext()->smarty
            ->assign('name', $this->getName())
            ->assign('value', $this->getValue())
            ->assign('label', $this->getLabel())
            ->assign('configType', $this->getType())
            ->fetch(_PS_MODULE_DIR_ . 'paypal/views/templates/admin/_partials/form/fields/textInput.tpl');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return (string) $this->name;
    }

    /**
     * @param string $name
     * @return TextInput
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return (string) $this->value;
    }

    /**
     * @param string $value
     * @return TextInput
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return (string) $this->label;
    }

    /**
     * @param string $label
     * @return TextInput
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }


    /**
     * @return string
     */
    public function getType()
    {
        return (string) $this->type;
    }

    /**
     * @param string $type
     * @return TextInput
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
}
