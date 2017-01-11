<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
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
 * @subpackage  Common/Factory
 * @internal
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
     * @since       3.7.12      Renamed from `_aSubClassNames`.
     * @internal
     * @remark      `$oProp` is not listed as it is created prior to calling the constructor of this class.
     */
    protected $_aSubClassPrefixes = array(
        'oForm'             => 'AdminPageFramework_Form_',
        'oPageLoadInfo'     => 'AdminPageFramework_PageLoadInfo_',
        'oResource'         => 'AdminPageFramework_Resource_',
        'oHelpPane'         => 'AdminPageFramework_HelpPane_',
        'oLink'             => 'AdminPageFramework_Link_',
    );
    
    /**
     * Stores the sub-object class names.
     * @since       3.5.3
     * @since       3.7.12      Changed the scope to private.
     */
    private $_aSubClassNames = array(
        'oProp'             => null,
        'oDebug'            => 'AdminPageFramework_Debug',
        'oUtil'             => 'AdminPageFramework_FrameworkUtility',
        'oMsg'              => 'AdminPageFramework_Message',
        'oForm'             => null,
        'oPageLoadInfo'     => null,
        'oResource'         => null,
        'oHelpPane'         => null,
        'oLink'             => null,
    );
    
    /**
     * Stores user-set sub-object class names.
     * 
     * This is for the user to use own classes for sub-class objects.
     * @since       3.7.12
     */
    public $aSubClassNames = array();
    
    /**
     * Sets up built-in objects.
     */
    public function __construct( $oProp ) {

        // Set sub-class names.
        foreach( $this->_aSubClassPrefixes as $_sObjectVariableName => $_sPrefix ) {
            $this->aSubClassNames[ $_sObjectVariableName ] = $_sPrefix . $this->_sStructureType;
        }
        $this->aSubClassNames = $this->aSubClassNames + $this->_aSubClassNames;
    
        // Let them overload so that these sub-class objects will not be instantiated until they are required.
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
        
        // Required sub-class objects
        $this->oProp = $oProp;
            
        if ( $this->oProp->bIsAdmin && ! $this->oProp->bIsAdminAjax ) {
            if ( did_action( 'current_screen' ) ) {
                $this->_replyToLoadComponents();
            } else {                
                add_action( 'current_screen', array( $this, '_replyToLoadComponents' ) );
            }
        }
        
        // Call the user constructor.
        $this->start();     // defined in the controller class.
        $this->oUtil->addAndDoAction( $this, 'start_' . $this->oProp->sClassName, $this );
        
    }    
        
    /**
     * Determines whether the class component classes should be instantiated or not.
     * 
     * @internal
     * @callback    action      current_screen
     * @return      void
     */
    public function _replyToLoadComponents( /* $oScreen */ ) {

        if ( ! $this->_isInThePage() ) { 
            return; 
        }
                    
        if ( ! isset( $this->oResource ) ) {
            $this->oResource = $this->_replyTpSetAndGetInstance_oResource();
        }
        
        if ( ! isset(  $this->oLink ) ) {
            $this->oLink = $this->_replyTpSetAndGetInstance_oLink();         
        }
        
        if ( $this->oUtil->isDebugMode() ) {
            $this->oPageLoadInfo = $this->oPageLoadInfo;
        }
        
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
     * @remark      This method should be called AFTER current screen is determined such as after the `current_screen` action hook.
     * @since       3.0.3
     * @since       3.2.0   Changed the visibility scope to `public` from `protected` as the head tag object will access it.
     * @todo        Change the visibility scope to `protected` as the public version of the method `isInThePage()` has been introduced to make the design consitent.
     * @internal
     */
    public function _isInThePage() { 
        return true; 
    }
         
    /**
     * Determines whether the `setUp()` method should be called.
     * 
     * @since       3.7.10
     * @callback    
     * @internal    
     * @return      void
     */
    public function _replyToDetermineToLoad() {

        if ( ! $this->_isInThePage() ) { 
            return; 
        }

        // Calls `setUp()` and the user will set up the meta box.
        $this->_setUp();
        
        /**
         * This action hook must be called AFTER the _setUp() method 
         * as there are callback methods that hook into this hook 
         * and assumes required configurations have been made.
         */
        $this->oUtil->addAndDoAction( $this, "set_up_{$this->oProp->sClassName}", $this );
                          
    }          
         
         
    /**
     * Instantiate a form object based on the type.
     * 
     * @since       3.1.0
     * @internal
     * @return      object|null
     */
    protected function _getFormObject() {
    
        $this->oProp->setFormProperties();
        $_sFormClass = $this->aSubClassNames[ 'oForm' ];
        return new $_sFormClass(
            $this->oProp->aFormArguments, // Options - for the values that do not need to change through out the script execution. 
            $this->oProp->aFormCallbacks, // Callbacks - for the values which change dynamically depending on conditions such as the loaded page url.
            $this->oMsg
        );    
        
    }
     
    /**
     * Instantiates a link object based on the type.
     * 
     * @since       3.0.4
     * @since       3.7.10      Removed the parameters as those values will be set in the extended class.
     * @remark      Override this method in an extended class.
     * @internal
     * @return      null|object
     */
    protected function _getLinkObject() {
        return null;
    }
    
    /**
     * Instantiates a page load object based on the type.
     * 
     * @since       3.0.4
     * @since       3.7.10      Removed the parameters as those values will be set in the extended class.
     * @internal
     */
    protected function _getPageLoadObject() {
        return null;
    }
      
    /**
     * Responds to a request of an undefined property.
     * 
     * This is used to instantiate classes only when necessary, rather than instantiating them all at once.
     * 
     * @internal
     */
    public function __get( $sPropertyName ) {
            
        // Set and return the sub class object instance.
        if ( isset( $this->aSubClassNames[ $sPropertyName ] ) ) {
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
            $_sClassName = $this->aSubClassNames[ 'oUtil' ];
            $this->oUtil = new $_sClassName;
            return $this->oUtil;
        }
        /**
         * Sets and returns the `oDebug` property.
         * @since       3.5.3
         */        
        public function _replyTpSetAndGetInstance_oDebug() {
            $_sClassName = $this->aSubClassNames[ 'oDebug' ];
            $this->oDebug = new $_sClassName;
            return $this->oDebug;
        }
        /**
         * Sets and returns the `oMsg` property.
         * @since       3.5.3
         */              
        public function _replyTpSetAndGetInstance_oMsg() {
            $this->oMsg = call_user_func_array(
                array( $this->aSubClassNames[ 'oMsg' ], 'getInstance'),
                array( $this->oProp->sTextDomain )  // parameters
            );
            return $this->oMsg;
        }
        /**
         * Sets and returns the `oForm` property.
         * @since       3.5.3
         */              
        public function _replyTpSetAndGetInstance_oForm() {
            $this->oForm = $this->_getFormObject();           
            return $this->oForm;
        }
        /**
         * Sets and returns the `oResouce` property.
         * @since       3.5.3
         */            
        public function _replyTpSetAndGetInstance_oResource() {
            if ( isset( $this->oResource ) ) {
                return $this->oResource;
            }
            $_sClassName     = $this->aSubClassNames[ 'oResource' ];
            $this->oResource = new $_sClassName( $this->oProp );
            return $this->oResource;
        }
            /**
             * Kept for backward compatibility.
             * @since       3.7.10
             */
            public function _replyTpSetAndGetInstance_oHeadTag() {
                $this->oHead = $this->_replyTpSetAndGetInstance_oResource();
                return $this->oHead;
            }        
        /**
         * Sets and returns the `oHelpPane` property.
         * @since       3.5.3
         */
        public function _replyTpSetAndGetInstance_oHelpPane() {
            $_sClassName     = $this->aSubClassNames[ 'oHelpPane' ];
            $this->oHelpPane = new $_sClassName( $this->oProp );            
            return $this->oHelpPane;
        }
        /**
         * Sets and returns the `oLink` property.
         * @since       3.5.3
         */
        public function _replyTpSetAndGetInstance_oLink() {
            $this->oLink = $this->_getLinkObject();
            return $this->oLink;
        }
        /**
         * Sets and returns the `oPageLoadInfo` property.
         * @since       3.5.3
         */        
        public function _replyTpSetAndGetInstance_oPageLoadInfo() {
            $this->oPageLoadInfo = $this->_getPageLoadObject();
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
            
            // Check if the method name does not contain a backslash.
            if ( false === strpos( $sMethodName, "\\" ) ) {
                return $this->oUtil->getElement( $aArguments, 0 );  // the first element - the filter value
            }
                
            // If the method name contains a backslash, the user may be using a name space. 
            // In that case, convert the backslash to underscore and call the method.
            $_sAutoCallbackMethodName = str_replace( '\\', '_', $sMethodName );
            return method_exists( $this, $_sAutoCallbackMethodName )
                ? call_user_func_array(
                    array( $this, $_sAutoCallbackMethodName ),
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
     * @remark      This method can be called statically with `AdminPageFramework_FrameworkUtility::getCallerScriptPath()` and the self instance is not instantiated.
     * In that case somehow `__get()` does not get triggered so here not using `$oUtil` and accessing the static method directly.
     * @since       3.4.4
     */   
    public function __toString() {
        return AdminPageFramework_FrameworkUtility::getObjectInfo( $this );        
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
