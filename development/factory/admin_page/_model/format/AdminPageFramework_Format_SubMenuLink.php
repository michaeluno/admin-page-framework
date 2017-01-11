<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format form a definition array.
 * 
 * @package     AdminPageFramework
 * @subpackage  Factory/AdminPage/Format
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Format_SubMenuLink extends AdminPageFramework_Format_SubMenuPage {
  
    /**    
     * Represents the structure of the sub-menu link array.
     * 
     * @since       2.0.0
     * @since       2.1.4       Changed to be static since it is used from multiple classes.
     * @since       3.0.0       Moved from the link class.
     * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
     * @since       3.6.0       Moved from `AdminPageFramework_Menu_Model`.
     * @remark      The scope is public because this is accessed from an extended class.
     * @internal
     */ 
    static public $aStructure = array(     
        'type'                  => 'link',    
        'title'                 => null, // required
        'href'                  => null, // required
        'capability'            => null, // optional
        'order'                 => null, // optional
        'show_page_heading_tab' => true,
        'show_in_menu'          => true,
    );
          
  
    /**
     * Stores a definition.
     */
    public $aSubMenuLink = array();
    
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
    public function __construct( /* $aSubMenuLink, $oFactory, $iParsedIndex */ ) {
     
        $_aParameters = func_get_args() + array( 
            $this->aSubMenuLink, 
            $this->oFactory,
            $this->iParsedIndex
        );
        $this->aSubMenuLink  = $_aParameters[ 0 ];
        $this->oFactory      = $_aParameters[ 1 ];
        $this->iParsedIndex  = $_aParameters[ 2 ];
     
    }

    /**
     * 
     * @return      array       The formatted subject array.
     */
    public function get() {
        return $this->_getFormattedSubMenuLinkArray( $this->aSubMenuLink );
    }
     
        /**
         * Formats the given sub-menu link array.
         * 
         * @since       3.0.0
         * @since       3.3.1       Changed the scope to `protected` from `private` as the method is called from a different class.
         * @since       3.1.1       Moved from `AdminPageFramework_Menu`.
         * @since       3.6.0       Moved from `AdminPageFramework_Menu_Model`.
         * @internal
         * @remark      Assumes the passed sub-menu link argument array is formatted to have required keys.
         * @return      array
         */
        protected function _getFormattedSubMenuLinkArray( array $aSubMenuLink ) {
            
            // If the set URL is not valid, return.
            if ( ! filter_var( $aSubMenuLink[ 'href' ], FILTER_VALIDATE_URL ) ) { 
                return array(); 
            }
            
            return array(  
                    'capability'    => $this->getElement( 
                        $aSubMenuLink, 
                        'capability', 
                        $this->oFactory->oProp->sCapability
                    ),
                    'order'         => isset( $aSubMenuLink[ 'order' ] ) && is_numeric( $aSubMenuLink[ 'order' ] )
                        ? $aSubMenuLink[ 'order' ] 
                        : $this->iParsedIndex * 10,
                )
                + $aSubMenuLink 
                + self::$aStructure;
            
        }      
    
}
