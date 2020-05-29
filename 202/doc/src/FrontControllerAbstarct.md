---
name: Abstract
category: Front controllers
---

In version 4.5 the controller is replaced to PPBTlib.

## Definition
PaypalAbstarctModuleFrontController

Abstarct class that must be extended by other module front controllers.

##### jsonValues
Contain Ajax response. Must be an array.

##### redirectUrl
Contain redirect URL.

##### values
An array of POST and GET values. Can be manually defined during unit test
instead of environment variables.

##### errors
An array of error information : error_msg, error_code, msg_long.

### run
Overrider ModuleFrontController run function for make controllers more testable.
Redirect or send Ajax response only in run function.
