<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides base methods that deal with generating values.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.0
 * @internal
 */
abstract class AdminPageFramework_Generate_Base extends AdminPageFramework_WPUtility {
    
    /**
     * Sets up properties.
     */
    // public function __construct() {
        
    // }
    
    /**
     * 
     * @return      string       The generated string value.
     */
    public function get() {
        return '';
    }
           
}
