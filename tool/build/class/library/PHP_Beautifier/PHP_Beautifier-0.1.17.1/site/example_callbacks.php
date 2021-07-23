<?php
$a=array('1',array('1.1','1.2','1.3'));
// php_beautifier->addFilter('ArrayNested');
$a=array('1',array('1.1','1.2','1.3'));
// php_beautifier->setIndentNumber(2);
echo 'Indent: 2 spaces';
// php_beautifier->setIndentNumber(10);
echo 'Indent: 10 spaces';
// php_beautifier->setBeautify(false);
echo "The following lines won't be beautified";
$a=array('1',array('1.1','1.2','1.3'));
if ($b) {$c;} else {$d;}
// php_beautifier->setBeautify(true);
// php_beautifier->setIndentNumber(4);
echo 'Beautify, again';
// now, turn out the Filter
// ArrayNested->off()
$a=array('1',array('1.1','1.2','1.3'));
// ArrayNested->on()
$a=array('1',array('1.1','1.2','1.3'));
?>
