<?php
if ($x == 1)
{
    $x = "Any text with {$x} sign";
}
elseif ($x == 2)
{
    $x = "Any text with ${x} sign";
}
else
{
    $x = $object->{$property};
}
if ($x == 1) $x = 2;
else $x = 3;
while ($i++ < 4)
{
    $obj->{'set' . $prop}($i);
}
?>
