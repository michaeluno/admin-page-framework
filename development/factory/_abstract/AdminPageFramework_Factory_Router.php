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
            if ( did_action( 'current_screen' ) ) {
                $this->_replyToLoadComponents();
            } else {                
                add_action( 'current_screen', array( $this, '_replyToLoadComponents' ) );
            }
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
             * Sets sub-class objects.
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
    protected function _isInstantiatable() { 
        return true; 
    }
    
    /**
     * Determines whether the instantiated object and its producing elements belong to the loading page.
     * 
     * This method should be redefined in the extended class.
     * 
     * @since       3.0.3
     * @since       3.2.0   Changed the scope to public from protected as the head tag object will access it.
     * @internal
     */
    public function _isInThePage() { 
        return true; 
    }
         
    /**
     * Instantiate a form object based on the type.
     * 
     * @since       3.1.0
     * @internal
     * @return      object|null
     * @deprecated  3.7.0
     */
    protected function _getFormInstance( $oProp ) {

        $_sFormClass = "AdminPageFramework_Form_{$oProp->_sPropertyType}";
        return new $_sFormClass(
            $oProp->aFormArguments, // Options - for the values that do not need to change through out the script execution. 
            $oProp->aFormCallbacks, // Callbacks - for the values which change dynamically depending on conditions such as the loaded page url.
            $this->oMsg
        );    
        
    }
    
    /**
     * Stores class names by fields type for help pane objects.
     * @since       3.5.3
     */    
    protected $_aResourceClassNameMap = array(
        'admin_page'            => 'AdminPageFramework_Resource_Page',
        'network_admin_page'    => 'AdminPageFramework_Resource_Page',
        'post_meta_box'         => 'AdminPageFramework_Resource_MetaBox',
        'page_meta_box'         => 'AdminPageFramework_Resource_MetaBox_Page',
        'post_type'             => 'AdminPageFramework_Resource_PostType',
        'taxonomy_field'        => 'AdminPageFramework_Resource_TaxonomyField',
        'widget'                => 'AdminPageFramework_Resource_Widget',
        'user_meta'             => 'AdminPageFramework_Resource_UserMeta',
    );        
    /**
     * Instantiate a resource handler object based on the type.
     * 
     * @since       3.0.4
     * @internal
     */
    protected function _getResourceInstance( $oProp ) {
        return $this->_getInstanceByMap( $this->_aResourceClassNameMap, $oProp->sStructureType, $oProp );
    }
    
    /**
     * Stores class names by fields type for help pane objects.
     * @since       3.5.3
     */    
    protected $_aHelpPaneClassNameMap = array(
        'admin_page'            => 'AdminPageFramework_HelpPane_Page',
        'network_admin_page'    => 'AdminPageFramework_HelpPane_Page',
        'post_meta_box'         => 'AdminPageFramework_HelpPane_MetaBox',
        'page_meta_box'         => 'AdminPageFramework_HelpPane_MetaBox_Page',
        'post_type'             => null,    // no help pane class for the post type factory class.
        'taxonomy_field'        => 'AdminPageFramework_HelpPane_TaxonomyField',
        'widget'                => 'AdminPageFramework_HelpPane_Widget',
        'user_meta'             => 'AdminPageFramework_HelpPane_UserMeta',
    );    
    /**
     * Instantiates a help pane object based on the type.
     * 
     * @since       3.0.4
     * @internal
     */
    protected function _getHelpPaneInstance( $oProp ) {
        return $this->_getInstanceByMap( $this->_aHelpPaneClassNameMap, $oProp->sStructureType, $oProp );
    }
    
    /**
     * Stores class names by fields type for link objects.
     * @since       3.5.3
     */
    protected $_aLinkClassNameMap = array(
        'admin_page'            => 'AdminPageFramework_Link_Page',
        'network_admin_page'    => 'AdminPageFramework_Link_NetworkAdmin',
        'post_meta_box'         => null,
        'page_meta_box'         => null,
        'post_type'             => 'AdminPageFramework_Link_PostType', 
        'taxonomy_field'        => null,
        'widget'                => null,
        'user_meta'             => null,
    );    
    /**
     * Instantiates a link object based on the type.
     * 
     * @since       3.0.4
     * @internal
     */
    protected function _getLinkInstancce( $oProp, $oMsg ) {
        return $this->_getInstanceByMap( $this->_aLinkClassNameMap, $oProp->sStructureType, $oProp, $oMsg );
    }
    
    /**
     * Stores class names by fields type for page load objects.
     * @since       3.5.3
     */
    protected $_aPageLoadClassNameMap = array(
        'admin_page'            => 'AdminPageFramework_PageLoadInfo_Page',
        'network_admin_page'    => 'AdminPageFramework_PageLoadInfo_NetworkAdminPage',
        'post_meta_box'         => null,
        'page_meta_box'         => null,
        'post_type'             => 'AdminPageFramework_PageLoadInfo_PostType', 
        'taxonomy_field'        => null,
        'widget'                => null,
        'user_meta'             => null,
    );
    /**
     * Instantiates a page load object based on the type.
     * 
     * @since 3.0.4
     * @internal
     */
    protected function _getPageLoadInfoInstance( $oProp, $oMsg ) {
        
        if ( ! isset( $this->_aPageLoadClassNameMap[ $oProp->sStructureType ] ) ) {
            return null;
        }
        $_sClassName = $this->_aPageLoadClassNameMap[ $oProp->sStructureType ];
        return call_user_func_array( array( $_sClassName, 'instantiate' ), array( $oProp, $oMsg ) );

    }
    
    /**
     * Returns a class object instance by the given map array and the key, plus one or two arguments.
     * 
     * @remark      There is a limitation that only can accept up to 3 parameters at the moment. 
     * @internal
     * @since       3.5.3
     * @return      null|object
     */
    private function _getInstanceByMap( /* array $aClassNameMap, $sKey, $mParam1, $mParam2, $mParam3 */ ) {
        
        $_aParams       = func_get_args();
        $_aClassNameMap = array_shift( $_aParams );
        $_sKey          = array_shift( $_aParams );
        
        if ( ! isset( $_aClassNameMap[ $_sKey ] ) ) {
            return null;
        }
        
        $_iParamCount = count( $_aParams );
        
        // passing more than 3 arguments is not supported at the moment.
        if ( $_iParamCount > 3 ) {
            return null;
        }
        
        // Insert the class name at the beginning of the parameter array.
        array_unshift( $_aParams, $_aClassNameMap[ $_sKey ] );    
        
        // Instantiate the class and return the instance.
        return call_user_func_array( 
            array( $this, "_replyToGetClassInstanceByArgumentOf{$_iParamCount}" ), 
            $_aParams
        );
    
    }
        /**#@+
         * @internal
         * @return      object
         */      
        /**
         * Instantiate a class with zero parameter.
         * @since       3.5.3
         */
        private function _replyToGetClassInstanceByArgumentOf0( $sClassName ) {
            return new $sClassName;
        }    
        /**
         * Instantiate a class with one parameter.
         * @since       3.5.3
         */        
        private function _replyToGetClassInstanceByArgumentOf1( $sClassName, $mArg ) {
            return new $sClassName( $mArg );
        }
        /**
         * Instantiate a class with two parameters.
         * @since       3.5.3
         */             
        private function _replyToGetClassInstanceByArgumentOf2( $sClassName, $mArg1, $mArg2 ) {
            return new $sClassName( $mArg1, $mArg2 );
        }      
        /**
         * Instantiate a class with two parameters.
         * @since       3.5.3
         */             
        private function _replyToGetClassInstanceByArgumentOf3( $sClassName, $mArg1, $mArg2, $mArg3 ) {
            return new $sClassName( $mArg1, $mArg2, $mArg3 );
        }              
        /**#@-*/        
    
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
        /**#@+
         * @internal
         * @return      object
         * @callback    function    call_user_func
         */          
        /**
         * Sets and returns the `oUtil` property.
         * @since       3.5.3
         */
        public function _replyTpSetAndGetInstance_oUtil() {
            $this->oUtil = new AdminPageFramework_FrameworkUtility;
            return $this->oUtil;
        }
        /**
         * Sets and returns the `oDebug` property.
         * @since       3.5.3
         */        
        public function _replyTpSetAndGetInstance_oDebug() {
            $this->oDebug = new AdminPageFramework_Debug;
            return $this->oDebug;
        }
        /**
         * Sets and returns the `oMsg` property.
         * @since       3.5.3
         */              
        public function _replyTpSetAndGetInstance_oMsg() {
            $this->oMsg = AdminPageFramework_Message::getInstance( $this->oProp->sTextDomain );
            return $this->oMsg;
        }
        /**
         * Sets and returns the `oForm` property.
         * @since       3.5.3
         */              
        public function _replyTpSetAndGetInstance_oForm() {
            $this->oForm = $this->_getFormInstance( $this->oProp );           
            return $this->oForm;
        }
        /**
         * Sets and returns the `oResouce` property.
         * @since       3.5.3
         */            
        public function _replyTpSetAndGetInstance_oResource() {
            $this->oResource = $this->_getResourceInstance( $this->oProp );
            return $this->oResource;
        }
        /**
         * Sets and returns the `oHelpPane` property.
         * @since       3.5.3
         */
        public function _replyTpSetAndGetInstance_oHelpPane() {
            $this->oHelpPane = $this->_getHelpPaneInstance( $this->oProp );
            return $this->oHelpPane;
        }
        /**
         * Sets and returns the `oLink` property.
         * @since       3.5.3
         */
        public function _replyTpSetAndGetInstance_oLink() {
            $this->oLink = $this->_getLinkInstancce( $this->oProp, $this->oMsg );
            return $this->oLink;
        }
        /**
         * Sets and returns the `oPageLoadInfo` property.
         * @since       3.5.3
         */        
        public function _replyTpSetAndGetInstance_oPageLoadInfo() {
            $this->oPageLoadInfo = $this->_getPageLoadInfoInstance( $this->oProp, $this->oMsg );
            return $this->oPageLoadInfo;
        }
        /**#@-*/
        
    /**
     * Redirects dynamic function calls to the pre-defined internal method.
     * 
     * @internal
     */
    public function __call( $sMethodName, $aArguments=null ) {    
         
        $_mFirstArg = $this->oUtil->getElement( $aArguments, 0 );
        
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
                return;
        }
        
        // If it is called with the framework auto-callback,
        if ( has_filter( $sMethodName ) ) {
            return $this->_getAutoCallback( $sMethodName, $aArguments );
        }
                
        $this->_triggerUndefinedMethodWarning( $sMethodName );
        
    }     
        /**
         * Returns the first parameter value if the method name does not contain a backslash.
         * If it contains a backslash, the user uses a name-spaced class name. In that case,
         * the backslashes need to be converted to underscores to support valid PHP method names.
         * 
         * @since       3.7.0
         */
        private function _getAutoCallback( $sMethodName, $aArguments ) {
            
            // Check if the method name contains a backslash.
            if ( false === strpos( $sMethodName, "\\" ) ) {
                return $this->oUtil->getElement( $aArguments, 0 );  // the first element - the filter value
            }
                
            // if the method name contains a backslash, the user may be using a name space. 
            // In that case, convert the backslash to underscore and call the method.
            $_sAutoCallbackClassName = str_replace( '\\', '_', $this->oProp->sClassName );
            return method_exists( $this, $_sAutoCallbackClassName )
                ? call_user_func_array(
                    array( $this, $_sAutoCallbackClassName ),
                    $aArguments
                )
                : $this->oUtil->getElement( $aArguments, 0 );   // the first argument
            
        }
        
        /**
         * @since   3.7.0
         * @return  void
         */
        private function _triggerUndefinedMethodWarning( $sMethodName ) {
            trigger_error(
                AdminPageFramework_Registry::NAME . ': ' 
                    . sprintf( 
                        __( 'The method is not defined: %1$s', $this->oProp->sTextDomain ),
                        $sMethodName 
                    ), 
                E_USER_WARNING 
            );            
        }
            
        
    
    /**
     * Prevents the output from getting too long when the object is dumped.
     *
     * Field definition arrays contain the factory object reference and when the debug log method tries to dump it, the output gets too long.
     * So shorten it here.
     * 
     * @remark      Called when the object is called as a string.
     * @since       3.4.4
     */   
    public function __toString() {
        return $this->oUtil->getObjectInfo( $this );        
    }
 
    /**
     * Deprecated methods.
     */
    /**
     * @remark          This was not functional since 3.1.3
     * @deprecated      3.5.5
     */
    public function setFooterInfoRight() {}
    /**
     * @remark          This was not functional since 3.1.3
     * @deprecated      3.5.5
     */    
    public function setFooterInfoLeft() {}
 
}