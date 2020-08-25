---
name: ScInit
category: Front controllers
---

## Definition

PaypalScInitModuleFrontController

This controller serves for handling Ajax requests

####  init()
This method call the `parent::init()` and set the variables that are necessary for correct 
work of the controller

#### prepareProduct()
Create or update the object of the cart

#### displayAjaxCheckAvailability()
The method serves for management of the displaying of the shortcut button 
(showing button or not)

#### displayAjaxCreateOrder()
The method create paypal transaction/order and returns `ID Payment`

