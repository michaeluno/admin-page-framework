<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2018, Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format form in-page tabs definition arrays.
 * 
 * @package     AdminPageFramework/Factory/AdminPage/Format
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Format_InPageTabs extends AdminPageFramework_Format_Base {
    
    /**
     * Represents the structure of the sub-field definition array.
     */
    static public $aStructure = array();
    
    public $aInPageTabs = array();
    
    public $sPageSlug = '';
    
    public $oFactory;
    
    
    /**
     * Sets up properties
     */
    public function __construct( /* $sPageSlug, $oFactory */ ) {
     
        $_aParameters = func_get_args() + array( 
            $this->aInPageTabs,
            $this->sPageSlug, 
            $this->oFactory, 
        );
        $this->aInPageTabs  = $_aParameters[ 0 ];
        $this->sPageSlug    = $_aParameters[ 1 ];
        $this->oFactory     = $_aParameters[ 2 ];
     
    }

    /**
     * 
     * @return      array       A sub-fields definition array.
     */
    public function get() {

         // Apply filters to modify the in-page tab array.
        $_aInPageTabs = $this->addAndApplyFilter(
            $this->oFactory,  // caller object
            "tabs_{$this->oFactory->oProp->sClassName}_{$this->sPageSlug}", // filter name
            $this->aInPageTabs     // filtering value
        );    

        // Added items may be missing necessary keys so format them
        foreach( ( array ) $_aInPageTabs as $_sTabSlug => $_aInPageTab ) {
            if ( ! is_array( $_aInPageTab ) ) {
                continue;
            }
            $_oFormatter = new AdminPageFramework_Format_InPageTab( 
                $_aInPageTab,
                $this->sPageSlug,
                $this->oFactory
            );
            $_aInPageTabs[ $_sTabSlug ] = $_oFormatter->get();
        }

        // Sort the in-page tab array.
        uasort( 
            $_aInPageTabs, 
            array( $this, 'sortArrayByKey' ) 
        );
        
        return $_aInPageTabs;
        
    }
    
}
