---
name: 3. PayPal customer
category: Entities
---

## Definition
Only for Braintree.

* Entity name: PaypalCustomer
* Table: paypal_customer
* Fields:

|Name|Type|Description|Validateur|
|------|------|------|------|
|id_paypal_customer|integer|ID of the record|isUnsignedId|
|id_customer|integer|PS customer ID|isUnsignedId|
|reference|string|Unique customer reference in Braintree|
|method|string|Method alias||
|mode|boolean|Sandbox or live|Added in v. 4.5|
|date_add|datetime|Date of the creation||
|date_upd|datetime|Date of the update||

#### loadCustomerByMethod()

Load customer object by ID.
Params : 
- integer $id_customer PrestaShop Customer ID
- string $method method alias
- integer $mode Sandbox/Live

Check if mode  = Sandbox/Live or Empty. If empty we load this customer. 
If MethodBt->updateCustomer() is successful, we update customer Mode to the current mode Sandbox or Live.
If we arrive catch Braintree\Exception\NotFound Exception, we update customer Mode to opposite mode value. 
In case of error, client retry to order, so new search by loadCustomerByMethod() function, don't return the customer. 
The new PayPal Customer will be created. 
