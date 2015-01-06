<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides the space to store the shared properties for meta boxes.
 * 
 * This class stores various types of values. This is used to encapsulate properties so that it helps to avoid naming conflicts.
 * 
 * @since 3.0.0
 * @package AdminPageFramework
 * @subpackage Property
 * @extends AdminPageFramework_Property_Base
 * @internal
 */
class AdminPageFramework_Property_MetaBox_Page extends AdminPageFramework_Property_MetaBox {

    /**
     * Defines the property type.
     * @remark      Setting the property type helps to check whether some components are loaded such as scripts that can be reused per a class type basis.
     * @since       3.0.0
     * @internal
     */
    public $_sPropertyType = 'page_meta_box';
    
    /**
     * The condition array for page slugs associated with the meta box.
     * 
     * This is used in the meta box class for pages.
     * 
     * @since 3.0.0
     */
    public $aPageSlugs = array();
    
    /**
     * Stores the admin page object currently browsed.
     * @since 3.0.0
     */
    public $oAdminPage;
    
    public $aHelpTabs = array();
    
    function __construct( $oCaller, $sClassName, $sCapability='manage_options', $sTextDomain='admin-page-framework', $sFieldsType='page_meta_box' ) {     
        
        add_action( 'admin_menu', array( $this, '_replyToSetUpProperties' ), 100 ); // this must be done after the menu class finishes building the menu with the _replyToBuildMenu() method.
        if ( is_network_admin() ) { 
            add_action( 'network_admin_menu', array( $this, '_replyToSetUpProperties' ), 100 );    
        }     
        
        parent::__construct( $oCaller, $sClassName, $sCapability, $sTextDomain, $sFieldsType );

        /* Store the 'meta box for pages' class objects in the global storage. These will be referred by the admin page class to determine if there are added meta boxes so that the screen option does not have to be set. */
        $GLOBALS['aAdminPageFramework']['aMetaBoxForPagesClasses'] = isset( $GLOBALS['aAdminPageFramework']['aMetaBoxForPagesClasses'] ) && is_array( $GLOBALS['aAdminPageFramework']['aMetaBoxForPagesClasses'] )
            ? $GLOBALS['aAdminPageFramework']['aMetaBoxForPagesClasses']
            : array();
        $GLOBALS['aAdminPageFramework']['aMetaBoxForPagesClasses'][ $sClassName ] = $oCaller; // The meta box class for pages needs to access the object.
        
    }     
    
    /**
     * Determines the current page and sets the appropriate properties.
     * @since       3.0.0
     * @internal
     */
    public function _replyToSetUpProperties() {
        
        if ( ! isset( $_GET['page'] ) ) { return; }
                
        $this->oAdminPage = $this->_getOwnerObjectOfPage( $_GET['page'] );
        if ( ! $this->oAdminPage ) { return; }
        
        $this->aHelpTabs = $this->oAdminPage->oProp->aHelpTabs; // the $this->oHelpPane object access it.
        
        $this->oAdminPage->oProp->bEnableForm = true; // enable the form tag
        
        $this->aOptions = $this->oAdminPage->oProp->aOptions;
        
    }
        
    /**
     * Retrieves the screen ID (hook suffix) of the given page slug.
     * @since       3.0.0
     * @internal
     */
    public function _getScreenIDOfPage( $sPageSlug ) {

        return ( $_oAdminPage = $this->_getOwnerObjectOfPage( $sPageSlug ) )
            ? $_oAdminPage->oProp->aPages[ $sPageSlug ]['_page_hook'] . ( is_network_admin() ? '-network' : '' )
            : '';
        
    }    
    
    /**
     * Checks if the given page slug is one of the pages added by the framework.
     * 
     * @since       3.0.0
     * @return      boolean     Returns true if it is of framework's added page; otherwise, false.
     */
    public function isPageAdded( $sPageSlug='' ) {    
        
        return ( $_oAdminPage = $this->_getOwnerObjectOfPage( $sPageSlug ) )
            ? $_oAdminPage->oProp->isPageAdded( $sPageSlug )
            : false;

    }
    
    /**
     * Checks if the current loading page is in the given page tab.
     * 
     * @remark      If the user is in the default tab page, it's possible that the $_GET['tab'] key is not set.
     * @since       3.0.0
     * return       boolean
     */
    public function isCurrentTab( $sTabSlug ) {
        
        $_sCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : '';
        if ( ! $_sCurrentPageSlug ) { return false; }
        
        $_sCurrentTabSlug = isset( $_GET['tab'] ) 
            ? $_GET['tab']
            : $this->getDefaultInPageTab( $_sCurrentPageSlug );
            
        return ( $sTabSlug == $_sCurrentTabSlug );

    }
    
    /**
     * Retrieves the default in-page tab from the given tab slug.
     * 
     * @since       3.0.0
     * @remark      Used in the `__call()` method in the main class.
     * @return      string      The default in-page tab slug if found; otherwise, an empty string.
     */         
    public function getDefaultInPageTab( $sPageSlug ) {
    
        if ( ! $sPageSlug ) { return ''; }
        return ( $_oAdminPage = $this->_getOwnerObjectOfPage( $sPageSlug ) )
            ? $_oAdminPage->oProp->getDefaultInPageTab( $sPageSlug )
            : '';    

    }    
    
    /**
     * Returns the option key for the given page slug that is supposed to be one of the added page by the framework.
     * @since       3.0.0
     */
    public function getOptionKey( $sPageSlug ) {
        
        if ( ! $sPageSlug ) { return ''; }
        return ( $_oAdminPage = $this->_getOwnerObjectOfPage( $sPageSlug ) )
            ? $_oAdminPage->oProp->sOptionKey
            : '';     
        
    }
    /**
     * Returns the class object that owns the page of the given page slug.
     * 
     * The owner class object is not the caller object of this property class object. 
     * It is the page factory class of the framework that creates the page of the given page slug.
     * The property object of the owner class has some methods to determine whether the currently loading page 
     * has been added or not. So this class will use those methods by accessing the owner class object.
     * 
     * @since       3.0.0
     * @since       3.4.1       Changed the name from `_getOwneerClass`.
     * @internal
     */
    private function _getOwnerObjectOfPage( $sPageSlug ) {
        
        if ( ! isset( $GLOBALS['aAdminPageFramework']['aPageClasses'] ) ) { return null; }
        if ( ! is_array( $GLOBALS['aAdminPageFramework']['aPageClasses'] ) ) { return null; }
                 
        foreach( $GLOBALS['aAdminPageFramework']['aPageClasses'] as $__oClass ) {
            if ( $__oClass->oProp->isPageAdded( $sPageSlug ) ) {
                return $__oClass;
            }
        }
        return null;
        
    }
}