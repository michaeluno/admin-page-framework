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
// | Authors: Original Author <author@example.com>                        |
// |          Your Name <you@example.com>                                 |
// +----------------------------------------------------------------------+
//
// \$Id:

    error_reporting(E_ALL|E_STRICT); /* this scripts show itself beautified */
    require_once ('PHP/Beautifier.php');
    require_once ('PHP/Beautifier/Batch.php');
    try {
        $oBeaut = new PHP_Beautifier();
        $oBeaut->setIndentNumber(4); /* default */
        $oBeaut->setIndentChar(' '); /* default */
        $oBeaut->setNewLine("\n"); /* default */
        $oBeaut->addFilter('ArrayNested');
        $oBeaut->addFilter('ListClassFunction');
        $oBeaut->addFilter('Pear', array(
            'add_header'=>'php'
        ));
        $oBeaut->setInputFile(__FILE__);
        $oBeaut->process();
        if (php_sapi_name() == 'cli') {
            $oBeaut->show();
        } else {
            echo '<pre>'.$oBeaut->show() .'</pre>';
        }
    }
    catch(Exception $oExp) {
        echo ($oExp);
    }
?>