# Admin Page Framework Coding Standard

## Variable Naming

Admin Page Framework employs [PHP Alternative Hungarian Notation](http://en.wikibooks.org/wiki/PHP_Programming/Alternative_Hungarian_Notation) for variable naming used in the source code to help better code readability.

### Scope Based Prefix

These should be prefixed before any other prefix character.

- `g+` - global variables.

```php
global $gasGlobalArray;
$gasGlobalArray = $oObject->doMethod();
```

- `_` - private/protected class property variables. But if it is clear that they are accessed by the end-users (the user types the variable name to access it), the prefix should not be added.
	
```php
class MyClass {

	public $sPublicProperty			= 'This is a public property string value';
	
	protected $_sProtectedProperty	= 'This is a protected property string value';
	
	private	  $_sPrivateProperty	= 'This is a private property string value';
		
}
```

- '_' - local variables.

```php
function doMyfunc( $sPrameter ) {
	
	$_sLocalVariable = $sParameter;

}
```

### Data Type Based Prefix

When the used types are mixed place them in alphabetical order.

- `a+` - array (often combined with the data type used inside the array)
- `b+` - boolean
- `c+` - character
- `d+` - date object -- as in what's returned from a date() or gmdate()
- `f+` - float -- a floating point number, e.g. an integer with a fractional part
- `h+` - handle, as in db handle, file handle, connection handle, curl handle, socket handle, etc.
- `hf` - handle to function, as in setRetrievalStrategy(callable $hfStrategy)
- `i+` - integer -- an integer
- `n+` - numeric (unknown if it's float, integer, etc. Use infrequently)
- `o+` - object
- `rs+` - db recordset (set of rows)
- `rw+` - db row
- `s+` - string
- `v+` - variant -- used very infrequently to mean any kind of possible variable type
- `x+` - to let other programmers know that this is a variable intended to be used by reference rather than value

```php
$sMyString	= 'Hello World';
$iCount		= 43;
$aMyArray	= array();
$asValue	= $bIsString ? 'My String' : array( 'My Array' );
```	

## Array Key Naming

Use lower case characters with underscores. 

```php
array(
	'first_key'		=>	'some value',
	'second_key'	=>	'another value',
);
```

When it's internal and certain that the user will not need to modify the value, use the above alternative Hungarian notation to imply that the elements are for internal use.

```php
private $_aLibraryInfo = array(
	'sName'		=> ...,
	'sVersion'	=> ...,
);
```

Or add a prefix	of an underscore.

```php
private $_aLibraryInfo = array(
	'_name'		=> ...,
	'_version'	=> ...,
);
```

## Class Naming

Use nouns for class names. 

Use a single underscore (`_`) for hierarchical relationships.

```php
    
    class MyClass_Base {}
    
    class MyClass extends MyClass_Base {}

```

This way it is easy to see `MyClass_Base`, `MyClass_View`, `MyClass_Model`, `MyClass_Controller` are all associated with `MyClass`.
```php
    
    abstract class MyClass_Base {}
    abstract class MyClass_View extends MyClass_Base {}
    abstract class MyClass_Model extends MyClass_View {}
    abstract class MyClass_Controller extends MyClass_Model {}    
    final class MyClass extends MyClass_Controller {}

```

For delegation classes use either double underscores (`__`) or triple underscores (`___`). 

Use double underscores (`__`) for classes that undertake the task and have to receive the delegating class object.

```php

    class MyClass {
   
        public function do( $bValue ) {
        
            if ( $bValue ) {
                $_oConditionX = new MyClass__ConditionX( $this );
                return $_oConditionX->get();
            }
        
        }
        
        public function doTaskA( $sVar ) {}
        public function doTaskB( $sVar ) {}
   
    }

    // Use double underscores for classes that cannot do anything without the caller object.
    class MyClass__ConditionX {
    
        public function __construct( $oCaller ) {
            
            $oCaller->doTaskA( ... );
            $oCaller->doTaskB( ... );
        
        }
        
        public function get() {}
    
    }

```

Use triple underscores (`___`) for sub classes that only used by the caller class but does not use the caller class object and complete the task by itself.

```php

    class MyClass {
   
        public function do() {
        
            if ( $bValue ) {
                $_oConditionX = new MyClass___ConditionX;
                return $_oConditionX->get();
            }
        
        }
        
    }

    // Use triple underscores for sub-classes that does not require the caller object.
    class MyClass___ConditionX {
            
        public function get() {
            return 'something';
        }
    
    }

```


## Function and Method Naming

Add the underscore prefix (`_`) for _internal_ functions or class methods regardless of the scope. *Internal* here means that the end-users (developers) will not need to use them therefore they do not have to pay attention to them.

```php
_fomrmatData();
```

Always start with a verb. 

```php
run();
doTask();
```
	
Use the camel-back notation.

```php
doMyStuff();
```
	
Not, 

```php	
do_my_stuff();
```	
For callback functions, prepend `replyTo` to help understand it's a callback. 

```php
replyToDoMyStuff();
```
	
Usually the framework callbacks are internal, so prepend an underscore to it.

```php
_replyToHandleCallbacks();
```
