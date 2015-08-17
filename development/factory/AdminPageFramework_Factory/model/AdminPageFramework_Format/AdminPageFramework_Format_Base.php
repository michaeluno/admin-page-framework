<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods that deal with field and section definition arrays.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.0
 * @internal
 */
abstract class AdminPageFramework_Format_Base extends AdminPageFramework_WPUtility {
    
    /**
     * Represents the structure and its default values of the definition array.
     */
    static public $aStructure = array();
    
    /**
     * Sets up properties.
     */
    // public function __construct() {
        
    // }
    
    /**
     * 
     * @return      array       The formatted definition array.
     */
    public function get() {
        return array();
    }
           
}
