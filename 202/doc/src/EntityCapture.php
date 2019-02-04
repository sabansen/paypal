---
name: 2. PayPal capture
category: Entities
---

## Definition
Only for payments in mode "Authorization"

* Entity name: PaypalCapture
* Table: paypal_capture
* Fields:

|Name|Type|Description|Validateur|
|------|------|------|------|
|id_paypal_capture|integer|ID of the record|isUnsignedId|
|id_capture|string|Capture ID set after transaction is captured|isString|
|id_paypal_order|integer|Key for paypal_order|isUnsignedId|
|capture_amount|float|Must be equal to total_paid||
|result|string|Transaction status| |
|date_add|datetime|Date of the creation||
|date_upd|datetime|Date of the update||


