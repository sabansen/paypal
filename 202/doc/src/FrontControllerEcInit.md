---
name: EcInit
category: Front controllers
---

## Definition
PaypalEcInitModuleFrontController

Init EC payment. Don't use redirects and dies in this controller.

### init

First call parents init function.
Set all your POST and GET values in abstract class variable. 

### postProcess
Load method class, set values from POST and GET, init payment.
Set jsonValues if controller is called by ajax, otherwise set redirectUrl to be redirected 
to PayPal or error page.

