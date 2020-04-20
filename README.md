# PHP Value Storage

Is a simple storage to hold values and value providers.

### Install
```bin
$ composer require tasoft/value-storage
```

### Usage
The value storage can be used like a simple array.  
In addition, it accepts values with a specific characteristic: They are resolvable on demand.
```php
<?php
use TASoft\Util\ValueStorage;

$vp = new ValueStorage(["value1" => 23]);
$vp->value2 = 44;
$vp["value3"] = 'Hello World';

// The values are fetchable by properties or array access:
echo $vp->value3; // 'Hello World'
echo $vp["value1"]; // 23
```
This behaviour is not yet special.  
But take a look at this:
```php
<?php
use TASoft\Util\ValueStorage;

$vp = new ValueStorage(["test" => $func1 = function() { return 23; }]);
// or
$vp["other-test"] = $func2 = function() { return "Hello World"; };

// You can assign a callback using the constructor, a property name or by array access.

// Fetching the value provider is done by then get() method
echo $func1 === $vp->get('test'); // TRUE
// or
echo $func1 === $vp->test; // TRUE

// And fetching the real value is done by getValue or array access
echo $vp->getValue('test'); // 23
// or
echo $vp["test"]; // 23 !
```
In the same way like callbacks objects implementing ```TASoft\Util\Value\ValueInterface``` are working.

Passing an object implementing ```TASoft\Util\Value\ValueInterfaces``` injects the returned values from that object.
You can assign such an object by any property or key or simply the addValue() method.
