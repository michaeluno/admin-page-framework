<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides routing functionality to the Admin Page Framework factory object based on the fields type.
 * 
 * This class mainly deals with routing function calls and instantiation of objects based on the type.
 * 
 * @abstract
 * @since       3.0.4
 * @package     AdminPageFramework
 * @subpackage  Factory
 * @internal
 * @method      void    start()   User constructor. Defined in `AdminPageFramework_Factory_Controller`.
 * @method      void    _setUp()    
 */
abstract class AdminPageFramework_Factory_Router {
    
    /**
     * Stores the property object.
     * 
     * @since       2.0.0
     * @access      public      The AdminPageFramework_Page_MetaBox class accesses it.
     */     
    public $oProp;    
    
    /**
     * The object that provides the debug methods. 
     * 
     * @internal
     * @access      public
     * @since       2.0.0
     * @since       3.1.0   Changed the scope to public from protected.
     */     
    public $oDebug;
    /**
     * Provides the utility methods. 
     * 
     * @internal
     * @since       2.0.0
     * @since       3.1.0     Changed the scope to public from protected.
     */         
    public $oUtil;
    /**
     * Provides the methods for text messages of the framework. 
     * 
     * @since       2.0.0
     * @since       3.1.0     Changed the scope to public from protected.
     * @access      public
     * @internal
     */         
    public $oMsg;
    
    /**
     * The form object that provides methods to handle form sections and fields.
     * @internal
     * @since       3.0.0
     * @since       3.5.2       Changed the scope to public from protected as the widget class needs to initialize this object.
     */     
    public $oForm;
    
    /**
     * Inserts page load information into the footer area of the page. 
     * 
     */
    protected $oPageLoadInfo;
    
    /**
     * Provides the methods to insert head tag elements.
     * 
     * @since   3.3.0   Changed the name from $oHeadTag as it has become to deal with footer elements.
     */
    protected $oResource;
    
    /**
     * Provides the methods to insert head tag elements.
     * @deprecated
     */
    protected $oHeadTag;
    
    /**
     * Provides methods to manipulate contextual help pane.
     */
    protected $oHelpPane;
    
    /**
     * Provides the methods for creating HTML link elements. 
     * 
     */    
    protected $oLink;
    
    /**
     * Stores sub-class names.
     * 
     * Used in the __get() method to check whether a method with the name of the property should be called or not.
     * 
     * @since       3.5.3
     */
    protected $_aSubClassNames = array(
        'oDebug', 
        'oUtil',
        'oMsg',
        'oForm',
        'oPageLoadInfo',
        'oResource',
        'oHelpPane',
        'oLink',
    );
    
    /**
     * Sets up built-in objects.
     */
    public function __construct( $oProp ) {

        // Let them overload.
        unset( 
            $this->oDebug, 
            $this->oUtil, 
            $this->oMsg, 
            $this->oForm, 
            $this->oPageLoadInfo,
            $this->oResource,
            $this->oHelpPane,
            $this->oLink
        );
        
        // Property object
        $this->oProp = $oProp;
    
        if ( $this->oProp->bIsAdmin && ! $this->oProp->bIsAdminAjax ) {
            add_action( 'current_screen', array( $this, '_replyToLoadComponents' ) );
        }
        
        // Call the start method - defined in the controller class.
        $this->start();    
        
    }    
        
        /**
         * Determines whether the class component classes should be instantiated or not.
         * 
         * @internal
         * @callback    action      current_screen
         * @return      void
         */
        public function _replyToLoadComponents( /* $oScreen */ ) {

            if ( 'plugins.php' === $this->oProp->sPageNow ) {
                // triggers __get() if not set.
                $this->oLink = $this->oLink;
            }
    
            if ( ! $this->_isInThePage() ) { 
                return; 
            }
            
            // Do not load widget resources in the head tag because widgets can be loaded in any page unless it is in customize.php.
            if ( in_array( $this->oProp->_sPropertyType, array( 'widget' ) ) && 'customize.php' !== $this->oProp->sPageNow ) {
                return;
            }
            
            $this->_setSubClasses();
            
        }
            /**
             * Sets sub-classes.
             * 
             * This method forces the overload method __get() to be triggered if those sub-class objects
             * are not set.
             * 
             * @since       3.5.3
             * @internal
             * @return      void
             */
            private function _setSubClasses() {
                $this->oResource        = $this->oResource;
                $this->oHeadTag         = $this->oResource; // backward compatibility                
                $this->oLink            = $this->oLink;
                $this->oPageLoadInfo    = $this->oPageLoadInfo;
            }

    /**
     * Determines whether the class object is instantiatable in the current page.
     * 
     * This method should be redefined in the extended class.
     * 
     * @since       3.1.0
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
    
    /**
     * Instantiate a form object based on the type.
     * 
     * @since       3.1.0
     * @internal
     * @return      object|null
     */
    protected function _getFormInstance( $oProp ) {
        
        switch ( $oProp->sFieldsType ) {
            case 'page':
            case 'network_admin_page':
                if ( $oProp->bIsAdminAjax ) {
                    return null;
                }
                return new AdminPageFramework_FormElement_Page( $oProp->sFieldsType, $oProp->sCapability, $this );
            case 'post_meta_box':
            case 'page_meta_box':
            case 'post_type':
                if ( $oProp->bIsAdminAjax ) {
                    return null;
                }     
                return new AdminPageFramework_FormElement( $oProp->sFieldsType, $oProp->sCapability, $this );
            case 'taxonomy':
            case 'widget':      // 3.2.0+
            case 'user_meta':   // 3.5.0+
                return new AdminPageFramework_FormElement( $oProp->sFieldsType, $oProp->sCapability, $this );
            
        }     
        
    }
    
    /**
     * Instantiate a resource handler object based on the type.
     * 
     * @since       3.0.4
     * @internal
     */
    protected function _getResourceInstance( $oProp ) {
        
        switch ( $oProp->sFieldsType ) {
            case 'page':
            case 'network_admin_page':
                return new AdminPageFramework_Resource_Page( $oProp );
            case 'post_meta_box':
                return new AdminPageFramework_Resource_MetaBox( $oProp );
            case 'page_meta_box':
                return new AdminPageFramework_Resource_MetaBox_Page( $oProp );     
            case 'post_type':
                return new AdminPageFramework_Resource_PostType( $oProp );
            case 'taxonomy':
                return new AdminPageFramework_Resource_TaxonomyField( $oProp );
            case 'widget':  // 3.2.0+
                return new AdminPageFramework_Resource_Widget( $oProp );
            case 'user_meta':    // 3.5.0+
                return new AdminPageFramework_Resource_UserMeta( $oProp );
        }

    }
    
    /**
     * Instantiates a help pane object based on the type.
     * 
     * @since       3.0.4
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
            case 'user_meta':    // 3.5.0+
                return new AdminPageFramework_HelpPane_UserMeta( $oProp );                
        }     
    }
    
    /**
     * Instantiates a link object based on the type.
     * 
     * @since       3.0.4
     * @internal
     */
    protected function _getLinkInstancce( $oProp, $oMsg ) {

        switch ( $oProp->sFieldsType ) {
            case 'page':
                return new AdminPageFramework_Link_Page( $oProp, $oMsg );
            case 'network_admin_page':            
                return new AdminPageFramework_Link_NetworkAdmin( $oProp, $oMsg );
            case 'post_meta_box':
                return null;
            case 'page_meta_box':
                return null;
            case 'post_type':
                return new AdminPageFramework_Link_PostType( $oProp, $oMsg );
            case 'taxonomy':
            case 'widget':  // 3.2.0+
            case 'user_meta':   // 3.5.0+
            default:
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
            case 'user_meta':   // 3.5.0+
            default:
                return null;
        }     
        
    }
    
    /**
     * Responds to a request of an undefined property.
     * 
     * This is used to instantiate classes only when necessary, rather than instantiating them all at once.
     * 
     * @internal
     */
    public function __get( $sPropertyName ) {
            
        switch( $sPropertyName ) {
            case 'oHeadTag':    // 3.3.0+ for backward compatibility
                $sPropertyName = 'oResource';
                break;
        }     

        // Set and return the sub class object instance.
        if ( in_array( $sPropertyName, $this->_aSubClassNames ) ) {            
            return call_user_func( 
                array( $this, "_replyTpSetAndGetInstance_{$sPropertyName}"  )
            );
        }
        
    }
        /**
         * Sets and returns the `oUtil` property.
         * @since       3.5.3
         * @internal
         * @return      object
         * @callback    function    call_user_func
         */
        public function _replyTpSetAndGetInstance_oUtil() {
            $this->oUtil = new AdminPageFramework_WPUtility;
            return $this->oUtil;
        }
        /**
         * Sets and returns the `oDebug` property.
         * @since       3.5.3
         * @internal
         * @return      object
         * @callback    function    call_user_func
         */        
        public function _replyTpSetAndGetInstance_oDebug() {
            $this->oDebug = new AdminPageFramework_Debug;
            return $this->oDebug;
        }
        /**
         * Sets and returns the `oMsg` property.
         * @since       3.5.3
         * @internal
         * @return      object
         * @callback    function    call_user_func
         */              
        public function _replyTpSetAndGetInstance_oMsg() {
            $this->oMsg = AdminPageFramework_Message::getInstance( $this->oProp->sTextDomain );
            return $this->oMsg;
        }
        /**
         * Sets and returns the `oForm` property.
         * @since       3.5.3
         * @internal
         * @return      object
         * @callback    function    call_user_func
         */              
        public function _replyTpSetAndGetInstance_oForm() {
            $this->oForm = $this->_getFormInstance( $this->oProp );
            return $this->oForm;
        }
        /**
         * Sets and returns the `oResouce` property.
         * @since       3.5.3
         * @internal
         * @return      object
         * @callback    function    call_user_func
         */            
        public function _replyTpSetAndGetInstance_oResource() {
            $this->oResource = $this->_getResourceInstance( $this->oProp );
            return $this->oResource;
        }
        /**
         * Sets and returns the `oHelpPane` property.
         * @since       3.5.3
         * @internal
         * @return      object
         * @callback    function    call_user_func
         */
        public function _replyTpSetAndGetInstance_oHelpPane() {
            $this->oHelpPane = $this->_getHelpPaneInstance( $this->oProp );
            return $this->oHelpPane;
        }
        /**
         * Sets and returns the `oLink` property.
         * @since       3.5.3
         * @internal
         * @return      object
         * @callback    function    call_user_func
         */
        public function _replyTpSetAndGetInstance_oLink() {
            $this->oLink = $this->_getLinkInstancce( $this->oProp, $this->oMsg );
            return $this->oLink;
        }
        /**
         * Sets and returns the `oPageLoadInfo` property.
         * @since       3.5.3
         * @internal
         * @return      object
         * @callback    function    call_user_func
         */        
        public function _replyTpSetAndGetInstance_oPageLoadInfo() {
            $this->oPageLoadInfo = $this->_getPageLoadInfoInstance( $this->oProp, $this->oMsg );
            return $this->oPageLoadInfo;
        }
        
    /**
     * Redirects dynamic function calls to the pre-defined internal method.
     * 
     * @internal
     * @todo        Introduce "set_up_pre_{ class name }" action hook.
     */
    public function __call( $sMethodName, $aArgs=null ) {    
         
        $_mFirstArg = $this->oUtil->getElement( $aArgs, 0 );
        
        switch ( $sMethodName ) {
            case 'validate':
            case 'content':
                return $_mFirstArg;
            case 'setup_pre':
                $this->_setUp();
                
                // This action hook must be called AFTER the _setUp() method as there are callback methods that hook into this hook and assumes required configurations have been made.
                $this->oUtil->addAndDoAction( 
                    $this, 
                    "set_up_{$this->oProp->sClassName}", 
                    $this 
                );
                $this->oProp->_bSetupLoaded = true;            
                return;
        }
        
        if ( has_filter( $sMethodName ) ) {
            return $_mFirstArg;
        }
                
        trigger_error( 
            'Admin Page Framework: ' . ' : ' . sprintf( 
                __( 'The method is not defined: %1$s', $this->oProp->sTextDomain ),
                $sMethodName 
            ), 
            E_USER_WARNING 
        );
        
    }     
    
    /**
     * Called when the object is called as a string.
     *
     * Field definition arrays contain the factory object reference and when the debug log method tries to dump it, the output gets too long.
     * So shorten it here.
     * 
     * @since       3.4.4
     */   
    public function __toString() {
        
        $_iCount     = count( get_object_vars( $this ) );
        $_sClassName = get_class( $this );
        return '(object) ' . $_sClassName . ': ' . $_iCount . ' properties.';
        
    }
 
}