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
    error_reporting(E_ALL|E_STRICT);
    require_once ('PHP/Beautifier.php');
    require_once ('PHP/Beautifier/Batch.php');
    try {
        $oBeaut = new PHP_Beautifier();
        $oBatch = new PHP_Beautifier_Batch($oBeaut);
        $oBatch->addFilter('ArrayNested');
        $oBatch->addFilter('ListClassFunction');
        $oBatch->addFilter('Pear',array('add_header'=>'php'));
        $oBatch->setInputFile('example_*.php');
        $oBatch->process();
        if (php_sapi_name()=='cli') {
            $oBatch->show();
        } else {
            echo '<pre>'.$oBatch->show().'</pre>';
        }
    }
    catch(Exception $oExp) {
        echo ($oExp);
    }
?>