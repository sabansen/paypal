---
name: Admin menu
category: Admin controllers
---

## Definition

Coming in v.4.5

Second level menu "PayPal" with several third-level menus.

## Structure

Second level menu is AdminPaypalConfiguration controller, which replace content of getContent function.

Third-level menus :
* Configurations (equal to second level)
* Logs (AdminPaypalProcessLoggerController)
* Stats (AdminPaypalStatsController)

### Stats

This tab redirect to external url of PayPal or Braintree stats

### Logs

ModuleAdminController from PPBTlib SDK. 

Associated object class name : PaypalLog.
Associated table name : paypal_log.

### Configurations

Parent tab - AdminPaypalParentConfiguration:
* AdminPaypalConfiguration (position 0)
* AdminPaypalAdvancedConfiguration (position 1) (v 5.0)

AdminPaypalConfiguration redirect to module configuration page. 

GetContent function will be replaced by the system of tabs in v 5.0: 
- General Configuration 
- Advanced Options (statuses, crons, widgets)
- Logs
- Help
