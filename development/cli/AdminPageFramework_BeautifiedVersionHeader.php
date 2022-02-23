<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * If accessed from a console, include the registry class to laod 'AdminPageFramework_Registry_Base'.
 */
if ( php_sapi_name() === 'cli' ) {
    $_sFrameworkFilePath = dirname( dirname( __FILE__ ) ) . '/admin-page-framework.php';
    if ( file_exists( $_sFrameworkFilePath ) ) {
        include_once( $_sFrameworkFilePath );
    }
}

/**
 * Provides header information of the framework for the minifed version.
 *
 * The minifier script will include this file ( but it does not include WordPress ) to use the reflection class to generate the header comment section.
 *
 * @since       3.5.4
 * @package     AdminPageFramework/Property
 * @internal
 */
final class AdminPageFramework_BeautifiedVersionHeader extends AdminPageFramework_Registry_Base {

    const NAME          = 'Admin Page Framework';
    const DESCRIPTION   = 'Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>';

}
