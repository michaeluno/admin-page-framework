<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Property_NetworkAdmin' ) ) :
/**
 * Stores properties of a network admin object.
 * 
 * @since 3.1.0
 * @package AdminPageFramework
 * @subpackage Property
 * @extends AdminPageFramework_Property_Page
 * @internal
 */
class AdminPageFramework_Property_NetworkAdmin extends AdminPageFramework_Property_Page {
    
    /**
     * Defines the property type.
     * 
     * @since 3.1.0
     * @internal
     */
    public $_sPropertyType = 'network_admin_page';
    
    /**
     * Defines the fields type.
     * 
     * @since 3.1.0
     */
    public $sFieldsType = 'network_admin_page';
    
    /**
     * Returns the option array.
     * 
     * @since 3.1.0
     * @internal
     */
    protected function _getOptions() {
    
        return AdminPageFramework_WPUtility::addAndApplyFilter( // Parameters: $oCallerObject, $sFilter, $vInput, $vArgs...
            $GLOBALS['aAdminPageFramework']['aPageClasses'][ $this->sClassName ], // the caller object
            'options_' . $this->sClassName, // options_{instantiated class name}
            $this->sOptionKey ? get_site_option( $this->sOptionKey, array() ) : array()
        );
        
        // return $this->sOptionKey
            // ? get_site_option( $this->sOptionKey, array() )
            // : array();
            
    }
    
    /**
     * Utility methods
     */
    /**
     * Saves the options into the database.
     * 
     * @since 3.1.0
     */
    public function updateOption( $aOptions=null ) {
        update_site_option( $this->sOptionKey, $aOptions !== null ? $aOptions : $this->aOptions );
    }    
    
}
endif;