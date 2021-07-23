<?
// this open tag is replaced by standard <?php
// control structures
if (($a) || ($b)) {
action1();
} elseif (($c) && ($d)) {
action2(2);
} else {
defaultaction();
}
// switch structure
switch ($condition) {
case 1:
action1();
break;
case 2:
action2();
break;
default:
defaultaction();
break;
}

for($x=0;$x<50;$x++) {
    if($x==25) { break;}
}
// function call
$var = foo($bar, $baz, $quux);
// function definition
function fooFunction($arg1, $arg2 = '') 
{
if (condition) {
statement;
}
return $val;
}
function connect(&$dsn, $persistent = false) 
{
if (is_array($dsn)) {
$dsninfo = &$dsn;
} else {
$dsninfo = DB::parseDSN($dsn);
}
if (!$dsninfo || !$dsninfo['phptype']) {
return $this->raiseError();
}
return true;
}
// In the next line, '#' will be transformed in //
echo '# is not aprecciated!';  # not allowed comment
?>