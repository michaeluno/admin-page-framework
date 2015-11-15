<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
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
    
    /**
     * Stores the action hook name that gets triggered when the form registration is performed.
     * 'admin_page' and 'network_admin_page' will use a custom hook for it.
     * @since       DEVVER
     * @access      pulbic      Called externally.
     */
    public $_sFormRegistrationHook = 'admin_enqueue_scripts';
    
    function __construct( $oCaller, $sClassName, $sCapability='manage_options', $sTextDomain='admin-page-framework', $sStructureType='page_meta_box' ) {     
        
        // this must be done after the menu class finishes building the menu with the _replyToBuildMenu() method.
        add_action( 'admin_menu', array( $this, '_replyToSetUpProperties' ), 100 ); 
        if ( is_network_admin() ) { 
            add_action( 'network_admin_menu', array( $this, '_replyToSetUpProperties' ), 100 ); 
        }     
        
        parent::__construct( 
            $oCaller, 
            $sClassName, 
            $sCapability, 
            $sTextDomain,
            $sStructureType
        );

        // DEVVER+ 
        // @deprecated      Moved to the declaration area.
        // $this->_sFormRegistrationHook = 'admin_enqueue_scripts';

        // Store the 'meta box for pages' class objects in the global storage. 
        // These will be referred by the admin page class to determine if there are added meta boxes so that the screen option does not have to be set. 
        $GLOBALS[ 'aAdminPageFramework' ][ 'aMetaBoxForPagesClasses' ] = $this->getElementAsArray(
            $GLOBALS,
            array( 'aAdminPageFramework', 'aMetaBoxForPagesClasses' )
        );
        // The meta box class for pages needs to access the object.
        $GLOBALS[ 'aAdminPageFramework' ][ 'aMetaBoxForPagesClasses' ][ $sClassName ] = $oCaller; 
        
    }     
    
    /**
     * Determines the current page and sets the appropriate properties.
     * @since       3.0.0
     * @internal
     */
    public function _replyToSetUpProperties() {
        
        if ( ! isset( $_GET[ 'page' ] ) ) { 
            return; 
        }
                
        $this->oAdminPage = $this->_getOwnerObjectOfPage( $_GET[ 'page' ] );
        if ( ! $this->oAdminPage ) { 
            return; 
        }
        
        $this->aHelpTabs = $this->oAdminPage->oProp->aHelpTabs; // the $this->oHelpPane object access it.
        
        $this->oAdminPage->oProp->bEnableForm = true; // enable the form tag
        
        // $this->aOptions = $this->oAdminPage->oProp->aOptions;
        
    }
    
    /**
     * Retrusn the saved form options.
     * 
     * This is supposed to be called within the `__get()` overload magic method.
     * 
     * @since       DEVVER
     * @return      array
     */
    protected function _getOptions() {
        return $this->oAdminPage->oProp->aOptions;
    }
        
    /**
     * Retrieves the screen ID (hook suffix) of the given page slug.
     * @since       3.0.0
     * @internal
     */
    public function _getScreenIDOfPage( $sPageSlug ) {
        return ( $_oAdminPage = $this->_getOwnerObjectOfPage( $sPageSlug ) )
            ? $_oAdminPage->oProp->aPages[ $sPageSlug ][ '_page_hook' ] . ( is_network_admin() ? '-network' : '' )
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
     * @remark      If the user is in the default tab page, it's possible that the $_GET[ 'tab' ] key is not set.
     * @since       3.0.0
     * return       boolean
     */
    public function isCurrentTab( $sTabSlug ) {
        
        $_sCurrentPageSlug = $this->getElement( $_GET, 'page' );
        if ( ! $_sCurrentPageSlug ) { 
            return false; 
        }
        $_sCurrentTabSlug = $this->getElement( 
            $_GET, 
            'tab',
            $this->getDefaultInPageTab( $_sCurrentPageSlug )
        );            
        return ( $sTabSlug === $_sCurrentTabSlug );

    }
    /**
     * Retrieves the currently loading page slug.
     * 
     * @since       3.5.3
     * @return      string      The found page slug. An empty string if not found.
     * @remark      Do not return `null` when not found as some framework methods check the retuened value with `isset()` and if null is given, `isset()` yields `false` while it does `true` for an emtpy string (''). 
    */     
    public function getCurrentPageSlug() {
        return isset( $_GET[ 'page' ] ) 
            ? $_GET[ 'page' ] 
            : '';
    }
    
    /**
     * Returns the currently loading in-page tab slug.
     * @since       3.5.0
     * @since       3.5.3       Changed the name from `getCurrentTab()` to be more specific.
     */
    public function getCurrentTabSlug( $sPageSlug ) {
        $_oAdminPage = $this->_getOwnerObjectOfPage( $sPageSlug );
        return $_oAdminPage->oProp->getCurrentTabSlug( $sPageSlug );
    }
        /**
         * An alias of `getCurrentTabSlug()`.
         * @depreacated     3.5.3       Use `getCurrentTabSlug()`.
         */
        public function getCurretTab( $sPageSlug ) {
            return $this->getCurrentTabSlug( $sPageSlug );
        }
    
    /**
     * Retrieves the default in-page tab from the given tab slug.
     * 
     * @since       3.0.0
     * @remark      Used in the `__call()` method in the main class.
     * @return      string      The default in-page tab slug if found; otherwise, an empty string.
     */         
    public function getDefaultInPageTab( $sPageSlug ) {
    
        if ( ! $sPageSlug ) { 
            return ''; 
        }
        return ( $_oAdminPage = $this->_getOwnerObjectOfPage( $sPageSlug ) )
            ? $_oAdminPage->oProp->getDefaultInPageTab( $sPageSlug )
            : '';    

    }    
    
    /**
     * Returns the option key for the given page slug that is supposed to be one of the added page by the framework.
     * @since       3.0.0
     */
    public function getOptionKey( $sPageSlug ) {
        
        if ( ! $sPageSlug ) { 
            return ''; 
        }
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
        
        $_aPageClasses = $this->getElementAsArray(
            $GLOBALS,
            array( 'aAdminPageFramework', 'aPageClasses' )
        );                 
        foreach( $_aPageClasses as $_oAdminPage ) {
            if ( $_oAdminPage->oProp->isPageAdded( $sPageSlug ) ) {
                return $_oAdminPage;
            }
        }
        return null;
        
    }
    
}