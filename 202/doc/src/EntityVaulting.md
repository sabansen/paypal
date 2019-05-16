---
name: 4. PayPal vaulting
category: Entities
---

## Definition
Only for Braintree. Require activation in BO. Stock information about vaulted credit cards 
or paypal accounts for each BT customer.  

* Entity name: PaypalVaulting
* Table: paypal_vaulting
* Fields:

|Name|Type|Description|Validateur|
|------|------|------|------|
|id_paypal_vaulting|integer|ID of the record|isUnsignedId|
|token|string|unique token for each vaulted method (creditCard/paypal))||
|id_paypal_customer|integer|key from paypal_customer|isUnsignedId|
|name|string|Custom defined name of payment source||
|info|string|Card or account info||
|payment_tool|string|Method alias||
|sandbox|boolean|Sandbox or live|Added in v. 4.5|
|date_add|datetime|Date of the creation||
|date_upd|datetime|Date of the update||


