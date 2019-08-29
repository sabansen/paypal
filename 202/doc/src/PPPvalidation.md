---
name: PPPvalidation
category: Front controllers
---

## Definition

PaypalPppValidationModuleFrontController

This controller is used like handler. PayPal does the redirection to this controller when
the customer confirms the payment.

####  init()
This method call the `parent::init()` and set the variables that are necessary for correct 
work of the controller

#### postProcess()
This method call the `MethodPPP::validation` method and handle the errors


