---
name: MethodPPP
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
      
- **_getProductsList($currency)**

    - **Parametres**
      
      currency: `string` ISO of the currency
    
    - **Return Values**
    
      `void` adds the information about the products of the order to the transaction

- **checkCredentials($mode = null)**

    - **Parametres**
          
      mode: `boolean` the mode of the environment. True is Sandbox, False is Live
    
    - **Return Values**
    
      `void` check if the credentials are right and update the value 'PAYPAL_PLUS_EXPERIENCE' in DB
      
- **formatPrice($price)**

    - **Parametres**
          
      price: `float` price
    
    - **Return Values**
    
      `string` returns the converted price

- **getLinkToTransaction($id_transaction, $sandbox)**

    - **Parametres**
          
      id_transaction: `string` id of the payment transaction
          
      sandbox: `boolean` the mode of the environment. True is Sandbox, False is Live
    
    - **Return Values**
    
      `string` returns the the link to the page of the transaction on the site of PayPal     
      
- **getTplVars()**
    
    - **Return Values**
    
      `array` returns array of the template variables      
      
- **isConfigured($mode = null)**

    - **Parametres**
          
      mode: `boolean` the mode of the environment. True is Sandbox, False is Live
    
    - **Return Values**
    
      `boolean` checks if the connection is configured      
      
- **logOut($sandbox = null)**

    - **Parametres**
          
      sandbox: `boolean` the mode of the environment. True is Sandbox, False is Live
    
    - **Return Values**
    
      `void` does unconnecting with PayPal by deleting the saved credentials       
      
- **getInstructionInfo($id_payment)**

    - **Parametres**
          
      id_payment: `string` id of the payment
    
    - **Return Values**
    
      `object|boolean` returns the \PayPal\Api\PaymentInstruction object or False    
            
- **renderExpressCheckoutShortCut(&$context, $type, $page_source)**

    - **Parametres**
    
      context: `object` the Context object
      
      type: `string` PPP
          
      page_source: `string` product or cart
    
    - **Return Values**
    
      `string` returns the html code      
           
      
      
      
      
      
      
      


 
