---
name: Admin log
category: Admin controllers
---

## Definition

AdminPaypalProcessLoggerController from PPBTlib SDK. 

Associated object class name : ProcessLogger.
Associated table name : paypal_processlogger.

Use Helper list and Helper Options.

Helper list :

|Name|Description|
|------|------|
|id_paypal_log|ID|
|id_order|Redirect to Order|
|id_cart|Redirect to cart|
|id_transaction|Redirect to PP or BT transaction page|
|log|Show Message|
|status|Error or info|
|mode|Sandbox or Live|
|tools|Cards, paypal, google or apple pay|
|date_add|Date|

Configurations:

PAYPAL_REMOVE_LOG_DAYS - number of days, after which remove the logs (90 by default)
PAYPAL_REMOVE_LOG_AUTO - delete the logs (enabled by default)

