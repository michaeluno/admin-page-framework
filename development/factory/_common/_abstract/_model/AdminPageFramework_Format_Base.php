<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods that deal with field and section definition arrays.
 * 
 * @package     AdminPageFramework/Common/Factory/Format
 * @since       3.6.0
 * @internal
 * @extends     AdminPageFramework_FrameworkUtility
 */
abstract class AdminPageFramework_Format_Base extends AdminPageFramework_FrameworkUtility {
    
    /**
     * Represents the structure and its default values of the definition array.
     */
    static public $aStructure = array();
    
    /**
     * The subject array to be formatted.
     */
    public $aSubject = array();
    
    /**
     * Sets up properties.
     */
    public function __construct( /* $aSubject=array() */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aSubject, 
        );
        $this->aSubject  = $_aParameters[ 0 ];        
        
    }
    
    /**
     * 
     * @return      array       The formatted definition array.
     */
    public function get() {
        return $this->aSubject;
    }
           
}
