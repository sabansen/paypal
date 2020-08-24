---
name: PaypalApiManagerInterface
category: API
---

## Definition

For more flexibility and extensibility, the logic for creating orders, 
refunds, and cancellation is placed in separate command classes.  
Each command must implement the `RequestInteface` interface which contains only one 
execute method.  
The api manager, which must implement `PaypalApiManagerInterface`, is 
responsible for defining the required class.

- **getAccessTokenRequest()**

    Returns `RequestInteface` object that allow take an access token.
    In fact it is used for checking of the credentials.


- **getAuthorizationVoidRequest(`PaypalOrder` $paypalOrder)**

    Returns `RequestInteface` object that allow to cancel an authorized 
    transaction.


- **getCaptureAuthorizeRequest(`PaypalOrder` $paypalOrder)**

    Returns `RequestInteface` object that allow to capture fonds from an authorized 
    transaction.


- **getOrderAuthorizeRequest(`string` $idPayment)**

    Returns `RequestInteface` object that allow to create an authorized 
    transaction.


- **getOrderCaptureRequest(`string` $idPayment)**

    Returns `RequestInteface` object that allow to capture fonds from a 
    transaction.


- **getOrderGetRequest(`string` $idPayment)**

    Returns `RequestInteface` object that allow to get an information about a 
    transaction.


- **getOrderPartialRefundRequest(`PaypalOrder` $paypalOrder, `float` $amount)**

    Returns `RequestInteface` object that allow to do partial refund.


- **getOrderPatchRequest(`string` $idPayment)**

    Returns `RequestInteface` object that allow to update a transaction.


- **getOrderRefundRequest(`PaypalOrder` $paypalOrder)**

    Returns `RequestInteface` object that allow to do refund of whole order.


- **getOrderRequest(`string` $idPayment)**

    Returns `RequestInteface` object that allow to create a transaction.
