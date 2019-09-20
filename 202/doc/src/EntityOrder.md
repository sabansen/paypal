---
name: 1. PayPal order
category: Entities
---

## Definition
Entity for orders payment related information.

* Entity name: PaypalOrder
* Table: paypal_order
* Fields:

|Name|Type|Description|Validateur|
|------|------|------|------|
|id_paypal_order|integer|ID of the record|isUnsignedId|
|id_order|integer|ID of PS order|isUnsignedId|
|id_cart|integer|ID of PS cart|isUnsignedId|
|id_transaction|string|The payment-related transaction id|isString|
|id_payment|string|ID of payment| |
|payment_method|string|Transaction type returned by API||
|currency|string|Currency iso code|
|total_paid|float|Amount really paid by customer||
|payment_status|string|Status of payment||
|total_prestashop|float|Total amount calculating by PS||
|method|string|Method alias||
|payment_tool|string|BT tool (cards or paypal)||
|sandbox|boolean|Sandbox or live|Added in v. 4.5|
|date_add|datetime|Date of the creation||
|date_upd|datetime|Date of the update||

## Methods


- **getIdOrderByTransactionId(id_transaction)**

    - **Parametres**
    
        - id_transaction: `string` Transaction ID
    
    - **Return Values**
    
      `integer` ID of the prestashop order


- **getOrderById(id_order)**

    - **Parametres**

        - id_order: `integer` ID of the prestashop order
    
    - **Return Values**
    
      `array` PayPal order entity
      
- **getPaypalBtOrdersIds()**
    
    - **Return Values**
    
      return array of the PaypalOrder objects that were created by the braintree method

- **loadByOrderId(id_order)**

    - **Parametres**

        - id_order: `integer` ID of the prestashop order
    
    - **Return Values**
    
      `object` return PaypalOrder object by id of the prestashop order




