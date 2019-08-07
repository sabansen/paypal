---
name: MethodEC
category: Classes
---

The MethodEC extends the abstract class AbstractMethodPayPal.

## Definition

This class contains functions for creating transactions for paying for an order, 
refunding money for already created transactions and other functions 
related to the payment method "Express checkout"


## Methods


- **_getCredentialsInfo([mode_order = null])**

    - **Parametres**
      
      mode_order: `bool` True is mode **Sandbox**, False is mode **Live**. If mode_order is **null**
      current mode configured will be used
    
    - **Return Values**
    
      `array` returns array of the parameters for creating of the connection with PayPal
      
- **_getDiscountsList(currency)**

    - **Parametres**
      
      currency: `string` ISO of the currency
    
    - **Return Values**
    
      `void` adds the information related to the used discounts to the transaction  

- **__getGiftWrapping(currency)**

    - **Parametres**
      
      currency: `string` ISO of the currency
    
    - **Return Values**
    
      `void` adds the information related to the gift wrapping to the transaction      

- **_getPaymentDetails()**
    
    - **Return Values**
    
      `void` adds the information about payment to the transaction. 
      Calls the methods `_getDiscountsList()`, `__getGiftWrapping()`, `_getProductsList()`, `_getPaymentValues()`

- **_getPaymentValues($currency)**

    - **Parametres**
      
      currency: `string` ISO of the currency
    
    - **Return Values**
    
      `void` adds the information about payment to the transaction








 
