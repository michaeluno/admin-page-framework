<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
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
     * The parsed index. 
     * 
     * This should represents the order of the registered sub-menu item as it is used to calculate the sub-menu position. 
     * @since       3.7.4
     */
    public $iParsedIndex = 1;
    
    /**
     * Sets up properties
     */
    public function __construct( /* $aSubMenuItem, $oFactory, $iParsedIndex */ ) {
     
        $_aParameters = func_get_args() + array(
            $this->aSubMenuItem,
            $this->oFactory,
            $this->iParsedIndex,
        );
        $this->aSubMenuItem  = $_aParameters[ 0 ];
        $this->oFactory      = $_aParameters[ 1 ];
        $this->iParsedIndex  = $_aParameters[ 2 ];
     
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
                $this->oFactory,
                $this->iParsedIndex
            );

            return $_oFormatter->get();
        }
            
        if ( isset( $_aSubMenuItem[ 'href' ] ) ) {
            $_oFormatter = new AdminPageFramework_Format_SubMenuLink(
                $_aSubMenuItem,
                $this->oFactory,
                $this->iParsedIndex
            );

            return $_oFormatter->get();
        }
            
        return array();
        
    }
    
}
