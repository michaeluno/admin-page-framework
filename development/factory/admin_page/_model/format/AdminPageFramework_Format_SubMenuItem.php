<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format form a definition array.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Format_SubMenuItem extends AdminPageFramework_Format_Base {
    
    /**
     * Represents the structure of the sub-field definition array.
  
     */
    static public $aStructure = array(
    );
    
    /**
     * Stores an in-page tab definition.
     */
    public $aSubMenuItem = array();
    
    public $oFactory;
    
    /**
     * Sets up properties
     */
    public function __construct( /* $aSubMenuItem, $oFactory */ ) {
     
        $_aParameters = func_get_args() + array( 
            $this->aSubMenuItem, 
            $this->oFactory, 
        );
        $this->aSubMenuItem  = $_aParameters[ 0 ];
        $this->oFactory      = $_aParameters[ 1 ];
     
    }

    /**
     * 
     * @return      array       The formatted subject array.
     */
    public function get() {

        $_aSubMenuItem = $this->getAsArray( $this->aSubMenuItem );
        
        if ( isset( $_aSubMenuItem[ 'page_slug' ] ) ) {
            $_oFormatter = new AdminPageFramework_Format_SubMenuPage(
                $_aSubMenuItem,
                $this->oFactory
            );
            return $_oFormatter->get();
        }
            
        if ( isset( $_aSubMenuItem[ 'href' ] ) ) {
            $_oFormatter = new AdminPageFramework_Format_SubMenuLink( 
                $_aSubMenuItem,
                $this->oFactory    
            );
            return $_oFormatter->get();            
        }
            
        return array();    
        
    }
    
}