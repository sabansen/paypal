---
name: 5. PayPal Log
category: Entities
---

## Definition
Save log in database.

* Entity name: PaypalLog
* Table: paypal_log
* Fields:

|Name|Type|Description|Validateur|
|------|------|------|------|
|id_paypal_log|integer|ID of the record|isUnsignedId|
|id_order|integer|ID of PS order|Not empty if order exist.|
|id_cart|integer|ID of PS cart|Not empty if order doesn't exist yet.|
|id_shop|integer|Shop ID||
|id_transaction|string|transaction id from API response||
|log|string|Log message, like API response or error. Example: error code - short message - message long|Required |
|status|string|Info or Error||
|mode|string|Sandbox or Live||
|tools|string|Cards, paypal, google or apple pay|Not required|
|date_add|datetime|Date of the creation||

### Hooks associated

displayAdminOrderContentOrder

displayAdminOrderTabOrder

Display log recap on order page in the order tab. 
Add tab with table :

|DATE|Transaction|Description|Payment tool|
|------|------|------|------|

