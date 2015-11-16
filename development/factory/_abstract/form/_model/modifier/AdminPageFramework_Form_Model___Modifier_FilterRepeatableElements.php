<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods that deal with dropping repeatable form elements from an input array.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.2
 * @internal
 */
class AdminPageFramework_Form_Model___Modifier_FilterRepeatableElements extends AdminPageFramework_Form_Model___Modifier_Base {
    
    public $aSubject         = array();
    public $aDimensionalKeys = array();
    
    /**
     * Sets up properties.
     */
    public function __construct( /* $aSubject, $aDimensionalKeys */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aSubject, 
            $this->aDimensionalKeys, 
        );
        $this->aSubject         = $_aParameters[ 0 ];
        $this->aDimensionalKeys = array_unique( $_aParameters[ 1 ] );
        
    }
    
    /**
     * Returns a filtered array that repeatable sections and fields are removed.
     * 
     * @return      array       The formatted definition array.
     */
    public function get() {
        foreach( $this->aDimensionalKeys as $_sFlatFieldAddress ) {
            $this->unsetDimensionalArrayElement( 
                $this->aSubject, 
                explode( '|', $_sFlatFieldAddress )
           );
        }
        return $this->aSubject;
    }       

}