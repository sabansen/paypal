---
name: Abstract method
category: Classes
---

The AbstractMethodPaypal extends the abstract class AbstractMethod from PPBTlib.

## Definition
The AbstractMethodPaypal extends the abstract class AbstractMethod from PPBTlib.

#### paypalApiManager

Protected property.  
Type: PaypalApiManagerInterface  
Must be defined in each children class  

#### isSandbox

Protected property.  
Type: bool  

#### doOrderPatch()

Public method.  
Returns Response object.  
Template method that is used for update an order on paypal

#### formatPrice($price \[,$isoCurrency = null\])

Public method.  
price: float  
isoCurrency: string  
Returns float.

#### getBrandName()

Public method.  
Returns a value of a configuration "Brand Name"

#### getCancelUrl()

Abstract public method.  
Must be defined in each children class.  
That is address where a client will be redirected after he click cancel 
button in paypal checkout page.  
Returns `string`

#### getClientId()

Abstract public method.  
Must be defined in each children class.  
Returns string

#### getCustomFieldInformation($cart)

Public method.  
cart: Cart object  
Returns string

#### getInfo(\[$paymentId\])

Public method.  
paymentId: sting 
returns ResponseOrderGet object that consists the information about a paypal order

#### getIntent()

Abstract public method.  
returns `string` a value of a configuration "Payment mode" (sale / autorization)

#### getLandingPage()

Public method.  
Returns `string`

#### getLinkToTransaction($log)

Public method.  
log: PaypalLog object
returns `string` the link to paypal transaction

#### getPaypalPartnerId()

Abstract public method.  
return `string`

#### getReturnUrl()

Abstract public method.  
Must be defined in each children class.  
That is address where a client will be redirected after he confirm a transaction.  
Returns `string`

#### getSecret()

Abstract public method.  
return `string`

#### getSellerNonce()

Abstract public method.  
return `string`

#### getUrlJsSdkLib()

Public method.  
returns `string` a address / url of a JS lib

#### init()

Public method.  
Return ResponseOrderCreate object.  
Template method that creates an order on PayPal

#### isConfigured()

Abstract public method.  
returns `bool` true if module is configured and false if it is not

#### load(\[$method = null\])

Static function.  
method: string like BT, EC, PPP.  
Return an object. 

#### partialRefund($params)

Public method  
params: `array` hookActionOrderSlipAdd parameters.  
returns `ResponseOrderRefund` object.  
That is a template method that is used for do a partial refund request

#### refund($paypalOrder)

Public method  
params: `PaypalOrder` object.  
returns `ResponseOrderRefund` object.  
That is a template method that is used for do a full refund request

#### setDetailsTransaction($data) 

Public method.  
data: `ResponseOrderCapture` object  
return void  
That is a template method that is encapsulates a setting of transaction 
data after a client confirm an order

#### validation()

Public method.  
returns `void`  
That is a template method that is used for create an order in Prestashop



 
