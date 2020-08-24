---
name: MethodMB
category: Classes
---

The MethodMB extends the abstract class AbstractMethodPayPal.

## Definition

This class contains an information about a state of the method 
(configured or non, client id, secret id etc)


## Methods


- **assignJSvarsPaypalMB**
 
   Assigns the variables like `ajaxPatch`, `EMPTY_TAX_ID`, `INVALID_PAYER_TAX_ID` etc.  
   
   -**Return values**
   
    `void`

- **checkCredentials()**  
    
    This method check the setted credentials
    
    - **Return Values**
    
      `void` 
      
      
- **confirmCapture($paypalOrder)**  
    Implements an abstract method
      
      
- **getAdvancedFormInputs()**      
    
    - **Return Values**  
    `array` returns the advanced from fields
      
      
- **getCancelUrl()**  
    Implements an abstract method      
      
- **getClientId()**  
    Implements an abstract method
    
- **getIdProfileExperience()**

    - **Return value**  
    `string` 
    
- **getIntent()**  
    Implements an abstract method
  
- **getIpnUrl()**

    - **Return value**  
    `string`
              
- **getOrderStatus()**
    
    - **Return Values**  
    `int` returns id order state. It is used in AbstractMethosPaypal::validation() method. 
   
- **getPayerId()**

    - **Return value**  
    `string` 
    
- **getPayerTaxInfo()**

    - **Return value**  
    `array` return array that consists `tax_id` and `tax_id_type`
     
- **getPaymentId()**
    
    - **Return Value**  
    `string` returns id payment. It is used in AbstractMethosPaypal::validation() method.
    
- **getPaypalPartnerId()**  
    Implements an abstract method
    
- **getRememberedCards()**

    - **Return value**  
    `string` 
    
- **getReturnUrl()**  
    Implements an abstract method
  
- **getSecret()**  
    Implements an abstract method
    
- **getShortCut()**  
    
    - **Return Value**  
    `bool` returns true if the smart button is used
    
- **getTaxIdType()**
    
    - **Return Value**  
    `string`
        
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
      
- **renderExpressCheckoutShortCut($context, $type, $page_source)**

    - **Parametres**
          
      context: `Context` object  
      type: `string`  
      page_source: `string` like product / order
    
    - **Return Values**
    
      `string` HTML code
      
- **setConfig($params)**

    - **Parametres**
          
      params: `array` must consist the clientId and secret
    
    - **Return Values**
    
      `void`    
           
      
      
      
      
      
      
      


 
