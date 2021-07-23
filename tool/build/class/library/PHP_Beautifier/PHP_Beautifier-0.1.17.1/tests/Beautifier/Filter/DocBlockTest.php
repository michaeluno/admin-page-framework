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

require_once(dirname(__FILE__).'/../../Helpers.php');

class DocBlockTest extends PHPUnit_Framework_TestCase
{
    function setUp() 
    {
        error_reporting (E_ALL & ~(E_DEPRECATED | E_STRICT));
        $this->oBeaut = new PHP_Beautifier();
    }
    /**
     * Almost identical to original. The space after o before comment
     * is arbitrary, so I can't predict when I have to put a new line
     *
     */
    function testDocBlockSample() 
    {
        $this->markTestSkipped(
            "PHP_DocBlockGenerator has changed its alignment algorithm.\n" .
            "See http://pear.php.net/bugs/bug.php?id=16193"
        );
        $sSample = dirname(__FILE__) . '/docblock_sample_file.phps';
        $sContent = file_get_contents($sSample);
        $this->oBeaut->setInputFile($sSample);
        $this->oBeaut->addFilter("DocBlock");
        $this->oBeaut->process();
        $this->assertEquals($sContent, $this->oBeaut->get());
    }
}

?>
