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
require_once('Helpers.php');

class BeautifierInternalTest extends PHPUnit_Framework_TestCase {
    public $oBeaut;
    public $oFilter;
    function setUp() 
    {
        $this->oBeaut = new PHP_Beautifier();
        $this->oFilter = new Test_Filter($this->oBeaut);
        $this->oBeaut->addFilter($this->oFilter);
    }
    function setText($sText) 
    {
        $this->oBeaut->setInputString($sText);
        $this->oBeaut->process();
    }
    function testSetBeautify() 
    {
        $sText = <<<SCRIPT
<?php
echo "sdsds";
// php_beautifier->setBeautify(false);
echo "don't process this token";
// php_beautifier->on(true);

?>
SCRIPT;
        $this->setText($sText);
        // verify setBeautify
        $this->assertTrue(count($this->oFilter->aTokens) == 6);
    }
    function testModes() 
    {
        $sText = <<<SCRIPT
<?php
\$a=(\$b>1)?'0':'2';
\$b="asa{\$a}";
?>
SCRIPT;
        //php_beautifier->seatBeautify(true);
        $this->setText($sText);
        // ternary mode from ? to :
        $this->assertFalse($this->oFilter->aModes[7]['ternary_operator']);
        for ($x = 8;$x <= 10;$x++) {
            $this->assertTrue($this->oFilter->aModes[$x]['ternary_operator'], $x);
        }
        $this->assertFalse($this->oFilter->aModes[15]['double_quote']);
        // quote from " to } previous to "
        for ($x = 16;$x <= 20;$x++) {
            $this->assertTrue($this->oFilter->aModes[$x]['double_quote'], $x);
        }
    }
    
}
?>
