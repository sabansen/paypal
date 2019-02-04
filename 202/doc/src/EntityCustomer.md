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
|date_add|datetime|Date of the creation||
|date_upd|datetime|Date of the update||


