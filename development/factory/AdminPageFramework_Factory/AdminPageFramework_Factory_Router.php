<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Factory_Router' ) ) :
/**
 * Provides the routing functionality to the Admin Page Framework factory object based on the fields type.
 * 
 * This class mainly deals with routing function calls and instantiation of objects based on the type.
 * 
 * @abstract
 * @since 3.0.4
 * @subpackage Factory
 * @internal
 */
abstract class AdminPageFramework_Factory_Router {
    
    /**
     * Stores the property object.
      * @internal
     * @since 2.0.0
     * @acess public The AdminPageFramework_Page_MetaBox class accesses it.
     */     
    public $oProp;    
    
    /**
     * * The object that provides the debug methods. 
     * @internal
     * @remark Do not even declare the variable to allow to trigger the getter method.
     * @access public
     * @since 2.0.0
     * @since 3.1.0 Changed the scope to public from protected.
     */     
    // public $oDebug;
    /**
     * Provides the utility methods. 
     * @internal
     * @remark Do not even declare the variable to allow to trigger the getter method.
     * @since 2.0.0
     * @since 3.1.0 Changed the scope to public from protected.
     */         
    // public $oUtil;
    /**
     * Provides the methods for text messages of the framework. 
     * @since 2.0.0
     * @access public
     * @internal
     * @remark Do not even declare the variable to allow to trigger the getter method.
     * @since 3.1.0 Changed the scope to public from protected.
     */         
    // public $oMsg;    
    
    /**
     * @internal
     * @since 3.0.0
     * @remark Do not even declare the variable to allow to trigger the getter method.
     */     
     // protected $oForm = null;    
    
    /**
     * Inserts page load information into the footer area of the page. 
     * 
     * @remark Do not even declare the variable to allow to trigger the getter method.
     */
    // protected $oPageLoadInfo;
    
    /**
     * Provides the methods to insert head tag elements.
     * 
     * @remark Do not even declare the variable to allow to trigger the getter method.
     */
    // protected $oHeadTag;
    
    /**
     * Provides methods to manipulate contextual help pane.
     * 
     * @remark Do not even declare the variable to allow to trigger the getter method.
     */
    // protected $oHelpPane;
    
    /**
     * Provides the methods for creating HTML link elements. 
     * 
     * @remark Do not even declare the variable to allow to trigger the getter method.
     */    
    // protected $oLink;
    
    /**
     * Sets up built-in objects.
     */
    function __construct( $oProp ) {

        $this->oProp = $oProp;
    
        if ( $this->oProp->bIsAdmin && ! $this->oProp->bIsAdminAjax ) {
            add_action( 'current_screen', array( $this, '_replyToLoadComponents' ) ); // set a higher priority
        }
        
        // Call the start method - defined in the controller class.
        $this->start();    
        
    }    
        
        /**
         * Determines whether the class component classes should be instantiated or not.
         */
        public function _replyToLoadComponents( $oScreen ) {

            if ( 'plugins.php' === $this->oProp->sPageNow ) {
                $this->oLink = isset( $this->oLink ) // the user may have already accessed it
                    ? $this->oLink
                    : $this->_getLinkInstancce( $this->oProp, $this->oMsg );
            }
    
            if ( ! $this->_isInThePage() ) { return; }

            $this->oHeadTag         = isset( $this->oHeadTag ) // the user may have already accessed it
                ? $this->oHeadTag
                : $this->_getHeadTagInstance( $this->oProp );
            $this->oLink = isset( $this->oLink ) // the user may have already accessed it
                ? $this->oLink
                : $this->_getLinkInstancce( $this->oProp, $this->oMsg );     
            $this->oPageLoadInfo = isset( $this->oPageLoadInfo ) // the user may have already accessed it
                ? $this->oPageLoadInfo
                : $this->_getPageLoadInfoInstance( $this->oProp, $this->oMsg );
            
        }
    
    /**
     * Determines whether the class object is instantiatable in the current page.
     * 
     * This method should be redefined in the extended class.
     * 
     * @since 3.1.0
     * @internal
     */ 
    protected function _isInstantiatable() { return true; }
    
    /**
     * Determines whether the instantiated object and its producing elements belong to the loading page.
     * 
     * This method should be redefined in the extended class.
     * 
     * @since       3.0.3
     * @since       3.2.0   Changed the scope to public from protected as the head tag object will access it.
     * @internal
     */
    public function _isInThePage() { return true; }
    
    /*
     * Route
     */
    /**
     * Instantiate a form object based on the type.
     * 
     * @since 3.1.0
     */
    protected function _getFormInstance( $oProp ) {
        
        switch ( $oProp->sFieldsType ) {
            case 'page':
            case 'network_admin_page':
                if ( $oProp->bIsAdminAjax ) {
                    return null;
                }
                return new AdminPageFramework_FormElement_Page( $oProp->sFieldsType, $oProp->sCapability );
            case 'post_meta_box':
            case 'page_meta_box':
            case 'post_type':
                if ( $oProp->bIsAdminAjax ) {
                    return null;
                }     
                return new AdminPageFramework_FormElement( $oProp->sFieldsType, $oProp->sCapability );
            case 'taxonomy':
            case 'widget':      // 3.2.0+
                return new AdminPageFramework_FormElement( $oProp->sFieldsType, $oProp->sCapability );
            
        }     
        
    }
    
    /**
     * Instantiate a head tag object based on the type.
     * 
     * @since 3.0.4
     * @internal
     */
    protected function _getHeadTagInstance( $oProp ) {
        
        switch ( $oProp->sFieldsType ) {
            case 'page':
            case 'network_admin_page':
                return new AdminPageFramework_HeadTag_Page( $oProp );
            case 'post_meta_box':
                return new AdminPageFramework_HeadTag_MetaBox( $oProp );
            case 'page_meta_box':
                return new AdminPageFramework_HeadTag_MetaBox_Page( $oProp );     
            case 'post_type':
                return new AdminPageFramework_HeadTag_PostType( $oProp );
            case 'taxonomy':
                return new AdminPageFramework_HeadTag_TaxonomyField( $oProp );
            case 'widget':  // 3.2.0+
                return new AdminPageFramework_HeadTag_Widget( $oProp );
    
        }

    }
    
    /**
     * Instantiates a help pane object based on the type.
     * 
     * @since 3.0.4
     * @internal
     */
    protected function _getHelpPaneInstance( $oProp ) {

        switch ( $oProp->sFieldsType ) {
            case 'page':
            case 'network_admin_page':
                return new AdminPageFramework_HelpPane_Page( $oProp );
            case 'post_meta_box':
                return new AdminPageFramework_HelpPane_MetaBox( $oProp );
            case 'page_meta_box':
                return new AdminPageFramework_HelpPane_MetaBox_Page( $oProp );
            case 'post_type':
                return null; // no help pane class for the post type factory class.
            case 'taxonomy':
                return new AdminPageFramework_HelpPane_TaxonomyField( $oProp );
            case 'widget':  // 3.2.0+
                return new AdminPageFramework_HelpPane_Widget( $oProp );                
        }     
    }
    
    /**
     * Instantiates a link object based on the type.
     * 
     * @since 3.0.4
     * @internal
     */
    protected function _getLinkInstancce( $oProp, $oMsg ) {
        
        switch ( $oProp->sFieldsType ) {
            case 'page':
            case 'network_admin_page':
                return new AdminPageFramework_Link_Page( $oProp, $oMsg );
            case 'post_meta_box':
                return null;
            case 'page_meta_box':
                return null;
            case 'post_type':
                return new AdminPageFramework_Link_PostType( $oProp, $oMsg );
            case 'taxonomy':
            case 'widget':  // 3.2.0+
                return null;
        }     
        
    }
    
    /**
     * Instantiates a page load object based on the type.
     * 
     * @since 3.0.4
     * @internal
     */
    protected function _getPageLoadInfoInstance( $oProp, $oMsg ) {
        
        switch ( $oProp->sFieldsType ) {
            case 'page':
                return AdminPageFramework_PageLoadInfo_Page::instantiate( $oProp, $oMsg );
            case 'network_admin_page':
                return AdminPageFramework_PageLoadInfo_NetworkAdminPage::instantiate( $oProp, $oMsg );
            case 'post_meta_box':
                return null;
            case 'page_meta_box':
                return null;
            case 'post_type':
                return AdminPageFramework_PageLoadInfo_PostType::instantiate( $oProp, $oMsg );
            case 'taxonomy':
            case 'widget':  // 3.2.0+
                return null;
        }     
        
    }
    
    /**
     * Responds to a request of an undefined property.
     * 
     * This is meant to instantiate classes only when necessary, rather than instantiating them all at once.
     */
    function __get( $sPropertyName ) {
            
        switch( $sPropertyName ) {
            case 'oUtil':     
                $this->oUtil = new AdminPageFramework_WPUtility;
                return $this->oUtil;
            case 'oDebug':     
                $this->oDebug = new AdminPageFramework_Debug;
                return $this->oDebug;
            case 'oMsg':     
                $this->oMsg = AdminPageFramework_Message::getInstance( $this->oProp->sTextDomain );
                return $this->oMsg;
            case 'oForm':
                $this->oForm = $this->_getFormInstance( $this->oProp );
                return $this->oForm;
            case 'oHeadTag':
                $this->oHeadTag = $this->_getHeadTagInstance( $this->oProp );
                return $this->oHeadTag;
            case 'oHelpPane':
                $this->oHelpPange = $this->_getHelpPaneInstance( $this->oProp );
                return $this->oHelpPange;
            case 'oLink':
                $this->oLink = $this->_getLinkInstancce( $this->oProp, $this->oMsg );
                return $this->oLink;
            case 'oPageLoadInfo':
                $this->oPageLoadInfo = $this->_getPageLoadInfoInstance( $this->oProp, $this->oMsg );
                return $this->oPageLoadInfo;
        }     
        
    }
    
    /**
     * Redirects dynamic function calls to the pre-defined internal method.
     * 
     */
    function __call( $sMethodName, $aArgs=null ) {    
         
        if ( has_filter( $sMethodName ) ) {
            return isset( $aArgs[ 0 ] ) ? $aArgs[ 0 ] : null;
        }
        
        trigger_error( 'Admin Page Framework: ' . ' : ' . sprintf( __( 'The method is not defined: %1$s', $this->oProp->sTextDomain ), $sMethodName ), E_USER_ERROR );
        
    }     
        
}
endif;