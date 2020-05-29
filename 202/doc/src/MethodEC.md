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
      
- **_getProductsList($currency)**

    - **Parametres**
      
      currency: `string` ISO of the currency
    
    - **Return Values**
    
      `void` adds the information about the products of the order to the transaction

- **_getShippingAddress()**
    
    - **Return Values**
    
      `object` returns the PayPal\EBLBaseComponents\AddressType object

- **checkCredentials($mode = null)**

    - **Parametres**
          
      mode: `boolean` the mode of the environment. True is Sandbox, False is Live
    
    - **Return Values**
    
      `void` check if the credentials are right and update the value 'PAYPAL_CONNECTION_EC_CONFIGURED'
      in DB

- **confirmCapture($paypal_order)**

    - **Parametres**
          
      paypal_order: `object` the PaypalOrder object
    
    - **Return Values**
    
      `array` returns the result of the transaction confirmation
      
- **formatPrice($price)**

    - **Parametres**
          
      price: `float` price
    
    - **Return Values**
    
      `string` returns the converted price      
      
- **getInfo()**
    
    - **Return Values**
    
      `object` returns the \PayPal\PayPalAPI\GetExpressCheckoutDetailsResponseType object

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
    
      `void` does disconnect with PayPal by deleting the saved credentials      
      
- **redirectToAPI($method)**

    - **Parametres**
          
      method: `string` EC, PPP
    
    - **Return Values**
    
      `string` returns the link      
      
- **useMobile()**
    
    - **Return Values**
    
      `boolean` should use the mobile view or desktop      
      
    
            
      
      
      
      
      
      
      
      
      
      


 
