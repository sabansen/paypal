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


## Methods


- **getByOrderId(id_order)**

    - **Parametres**
    
        - id_order: `integer` ID of the prestashop order
    
    - **Return Values**
    
      `array` return array of the PaypalCapture entity
      
- **loadByOrderPayPalId(orderPayPalId)**

    - **Parametres**
    
        - orderPayPalId: `integer` ID of the PayPal order
    
    - **Return Values**
    
      `object` return the PaypalCapture object by ID of the PayPal order
      
- **updateCapture(transaction_id, amount, status, id_paypal_order)**

    - **Parametres**
    
        - transaction_id: `string` Transaction ID
        
        - amount: `float` the amount of the transaction
        
        - id_paypal_order: `integer` ID of the PayPal order
    
    - **Return Values**
      
      `void` update the information of the capture
    
    
    
    
    
    
      