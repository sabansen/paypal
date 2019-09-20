---
name: ScInit
category: Front controllers
---

## Definition

PaypalScInitModuleFrontController

This controller serves for handling ajax requests

####  init()
This method call the `parent::init()` and set the variables that are necessary for correct 
work of the controller

#### postProcess()
This method call the `AbstractPayPalMethod::init` method and handle the errors

#### prepareProduct()
Create or update the object of the cart

#### checkAvailability()
The method serves for management of the displaying of the shortcut button 
(showing button or not)

