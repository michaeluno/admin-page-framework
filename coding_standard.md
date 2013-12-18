# Admin Page Framework Coding Standard #

## Variable Naming ##

Admin Page Framework employs [PHP Alternative Hungarian Notation](http://en.wikibooks.org/wiki/PHP_Programming/Alternative_Hungarian_Notation) for the variable naming used in the source code to help better code readability.

### Scope Based Prefix ###

These should be prefixed before any other prefix character.

- `g+` - global variable.
- `_` - a private/protected class variable.

	global $gasGlobalArray;
	$gasGlobalArray = $oObject->doMethod();

### Data Type Based Prefix ###

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

	$sMyString = 'Hello World';
	$iCount = 43;
	$aMyArray = array();
	$asValue = $bIsString ? 'My String' : array( 'My Array' );
	

## Array Key Naming ##

Use lower case characters with underscores where the user may interact with.

	array(
		'first_key' => 'some value',
		'second_key' => 'another value',
	);

When it's internal and it's certain that the user will not need to modify the value, use the above alternative Hungarian notation to imply that the elements are for internal use.

	private $aLibraryInfo = array(
		'sName' => ...,
		'sVersion' => ...,
	);

## Function and Method Naming ##

Start from always a verb.

Use the camel-back notation.

	doMyStuff()
	
For callback functions, prepend `replyTo` to help understand it's a callback.

	replyToDoMyStuff()
	
