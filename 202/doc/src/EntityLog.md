---
name: 5. PayPal Log
category: Entities
---

## Definition

Before all logs was savec in Prestashop Messages (SAV). In 4.5 we don't more use PS messages and create our system of logs.
Everywhere we used Messages we replace by saving the logs to our table. All front errors during the checkout must be also logged 
(use Error controller to save the logs). 

* Entity name: ProcessLogger
* Table: paypal_processlogger
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
|sandbox|boolean|Sandbox or Live||
|tools|string|Cards, paypal, google or apple pay|Not required|
|date_add|datetime|Date of the creation||

### Hooks associated

#### displayAdminOrderContentOrder, displayAdminOrderTabOrder & displayAdminCartsView

Display log recap on order page in the order tab. 
Add tab with table :

|DATE|Timestamp PayPal|Transaction|Description|Payment tool|
|------|------|------|------|------|


## Methods


- **getDateTransaction()**
    
    - **Return Values**
    
      `string` return the date of the transaction on the PayPal with the time zone
      
- **getLinkToTransaction()**

    - **Return Values**
        
      `string` return the link to the page of the transaction on PayPal