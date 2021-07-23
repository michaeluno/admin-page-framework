<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 5                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Claudio Bustos <cdx@users.sourceforge.net>                  |
// |          Jens Bierkandt <schtorch@users.sourceforge.net>             |
// +----------------------------------------------------------------------+
//
// $Id:
// Single line comment
// Single line comment 2
/* Multiline-Comment
1.-
2.-
*/
$sText = <<<SCRIPT
<?php
\$a=(\$b>1)?'0':'2';
\$b="asa{\$a}";
?>
SCRIPT;
//php_beautifier->seatBeautify(true);
echo 'hi'; /*one comment*/ /*after another*/ /*after another*/
/* */
/**
* Doc comment
*/
echo 'a'; // comment after standar code
echo 'b';/** comment after standar code */
$a=array('a','b',/*comment inside*/ 'c' // comment end
);
if ($a=5) // bug 1597   
{
// bug 1597
}
?>