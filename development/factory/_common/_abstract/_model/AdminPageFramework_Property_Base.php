<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * The base class for Property classes.
 * 
 * Stores necessary data to render pages and its elements such as forms, including callback functions, resource (asset) localtions (paths and urls), inline script and stylesheet etc.
 * 
 * Provides the common methods  and properties for the property classes that are used by the main class, the meta box class, and the post type class.
 * @since       2.1.0
 * @package     AdminPageFramework
 * @subpackage  Property
 * @internal
 * @extends     AdminPageFramework_FrameworkUtility
 */ 
abstract class AdminPageFramework_Property_Base extends AdminPageFramework_FrameworkUtility {

    /**
     * Represents the structure of the script info array.
     * @internal
     * @since       2.0.0
     * @since       3.0.0     Moved from the link class.
     */ 
    private static $_aStructure_CallerInfo = array(
        'sPath'         => null,
        'sType'         => null,
        'sName'         => null,     
        'sURI'          => null,
        'sVersion'      => null,
        'sThemeURI'     => null,
        'sScriptURI'    => null,
        'sAuthorURI'    => null,
        'sAuthor'       => null,
        'sDescription'  => null,
    );    
    
    /**
     * Stores the library information.
     * 
     * @remark Do not assign anything here because it is checked whether it is set.
     * @since 3.0.0
     */
    static public $_aLibraryData;

    /**
     * Defines the property type.
     * 
     * @since       3.3.1   This was defined in each extended classes.
     * @internal
     */
    public $_sPropertyType = '';    
    
    /**
     * Stores the main (caller) object.
     * 
     * @since 2.1.5
     */
    protected $oCaller;    
    
    /**
     * Stores the caller script file path.
     * 
     * @since 3.0.0
     */
    public $sCallerPath;
    
    /**
     * Stores the caller script data
     * 
     * @remark Do not even declare the variable name so that it triggers the getter method.
     * @since Unknown
     */
    // public $aScriptInfo;
    
    /**
     * Stores the extended class name that instantiated the property object.
     * 
     * @since     
     */
    public $sClassName;
    
    /**
     * The MD5 hash string of the extended class name.
     * @since     
     */
    public $sClassHash;
    
    /**
     * Stores the script to be embedded in the head tag.
     * 
     * @remark This should be an empty string by default since the related methods uses the append operator.
     * @since 2.0.0
     * @since 2.1.5 Moved from each extended property class.
     * @internal
     */             
    public $sScript = '';    

    /**
     * Stores the CSS rules to be embedded in the head tag.
     * 
     * @remark This should be an empty string by default since the related methods uses the append operator.
     * @since 2.0.0
     * @since 2.1.5 Moved from each extended property class.
     * @internal
     */         
    public $sStyle = '';
    
    /**
     * Stores the CSS rules for IE to be embedded in the head tag.
     * 
     * @remark This should be an empty string by default since the related methods uses the append operator.
     * @since 2.0.0 to 2.1.4
     * @internal
     */ 
    public $sStyleIE = '';    

    /**
     * Stores the field type definitions.
     * 
     * @since 2.1.5
     * @internal
     */
    public $aFieldTypeDefinitions = array();
    
    /**
     * The default JavaScript script loaded in the head tag of the created admin pages.
     * 
     * @since 3.0.0
     * @internal
     */
    public static $_sDefaultScript = "";
    
    /**
     * The default CSS rules loaded in the head tag of the created admin pages.
     * 
     * @since       2.0.0
     * @var         string
     * @static
     * @remark      It is accessed from the main class and meta box class.
     * @access      public    
     * @deprecated  3.2.0
     * @internal    
     */
    public static $_sDefaultStyle ="";    
        
    /**
     * The default CSS rules for IE loaded in the head tag of the created admin pages.
     * @since       2.1.1
     * @since       2.1.5       Moved the contents to the taxonomy field definition so it become an empty string.
     * @deprecated  3.2.0
     * @internal
     */
    public static $_sDefaultStyleIE = '';
        
    /**
     * Stores enqueuing script URLs and their criteria by md5 hash of the source url.
     * @since       2.1.2
     * @since       2.1.5       Moved to the base class.
     */
    public $aEnqueuingScripts = array();
    /**    
     * Stores enqueuing style URLs and their criteria by md5 hash of the source url.
     * @since       2.1.2
     * @since       2.1.5       Moved to the base class.
     */    
    public $aEnqueuingStyles = array();


    /**
     * Stores enqueued script/style argument array by its url.
     * @since       3.3.0
     */
    public $aResourceAttributes = array();

    
    /**
     * Stores the index of enqueued scripts.
     * 
     * @since       2.1.2
     * @since       2.1.5       Moved to the base class.
     */
    public $iEnqueuedScriptIndex = 0;
    /**
     * Stores the index of enqueued styles.
     * 
     * The index number will be incremented as a script is enqueued regardless a previously added enqueue item has been removed or not.
     * This is because this index number will be used for the script handle ID which is automatically generated.
     * 
     * @since       2.1.2
     * @since       2.1.5     Moved to the base class.
     */    
    public $iEnqueuedStyleIndex = 0;     
    
    /**
     * Stores is_admin() value to be reused. 
     * 
     * @since       3.0.0
     */
    public $bIsAdmin;
    
    /**
     * Stores the flag that indicates whether the library is minified or not.
     * @since       3.0.0
     * @deprecated  3.1.3   Use AdminPageFramework_Registry::$bIsMinifiedVersion
     */
    public $bIsMinifiedVersion;
        
    /**
     * Stores the capability for displayable elements.
     * 
     * @since       2.0.0
     */     
    public $sCapability;
    
    /**
     * Defines the fields type.
     * 
     * Can be either 'admin_page', 'network_admin_page', 'post_meta_box', 'page_meta_box', 'post_type', 'taxonomy_field'
     * 
     * @since       3.0.4
     * @internal
     */
    public $sStructureType;
        
    /**
     * Stores the text domain.
     * 
     * @since       3.0.4
     * @internal
     */ 
    public $sTextDomain;
    
    /**
     * Stores the current page's base name.
     * 
     * @since       3.0.5
     * @internal
     */
    public $sPageNow;
    
    /**
     * Indicates whether the setUp() method is loaded.
     *  
     * @since       3.1.0
     * @internal
     * @deprecated  3.7.0      To check if the `setUp()` is called, perform did_action( 'set_up_' . {instantiated class name} )
     */
    public $_bSetupLoaded;
    
    /**
     * Indicates whether the current page is in admin-ajax.php
     * 
     * @since       3.1.3
     * @internal
     */
    public $bIsAdminAjax;
        
    /**
     * Stores the label of the settings link embedded to the plugin listing table cell of the plugin title.
     * 
     * @since       3.1.0
     * @since       3.5.5       Moved from `AdminPageFramework_Property_Page` as the post type class also access it.
     * @remark      The default value should be `null` as the user may explicitly set an empty value.
     * The `null` value will be replaced with the system default text 'Settings' while an empty string '' will remove the link.
     */     
    public $sLabelPluginSettingsLink = null;
    
    /**
     * Stores the information to insert into the page footer.
     * 
     * The initially assigned text strings, `__SCRIPT_CREDIT__` and `__FRAMEWORK_CREDIT__` are reserved for the default values which will be replaced when the footer is being rendered.
     * 
     * @since       2.0.0
     * @since       3.5.5       Moved from `AdminpageFramework_Property_Page` as this is used by the post type link class and admin page link class.
     */             
    public $aFooterInfo = array(
        'sLeft'     => '__SCRIPT_CREDIT__',
        'sRight'    => '__FRAMEWORK_CREDIT__',
    );    

    /**
     * The utility object.
     * @since       3.5.3
     * @deprecated  3.7.0       Not declaring it here to trigger the `__get()` overload method.
     */
    /* public $oUtil; */
              
    /**
     * Stores the action hook name that gets triggered when the form registration is performed.
     * 'admin_page' and 'network_admin_page' will use a custom hook for it.
     * @since       3.7.0
     * @access      public
     */
    public $_sFormRegistrationHook = 'current_screen';
              
    /**
     * Stores arguments for the form object.
     * @since       3.7.0
     */
    public $aFormArguments = array(
        'caller_id'                         => '',
        'structure_type'                    => '',
        'action_hook_form_registration'     => '',
    );
    
    /**
     * Stores callbacks for the form object.
     * @since       3.7.0
     */
    public $aFormCallbacks = array(
        'hfID'              => null,    // the input id attribute
        'hfTagID'           => null,    // the field container id attribute
        'hfName'            => null,    // the field name attribute
        'hfNameFlat'        => null,    // the flat field name attribute
        // @todo Document the differences between `hfName` and `hfInputName`
        'hfInputName'       => null,    // 3.6.0+   the field input name attribute
        'hfInputNameFlat'   => null,    // 3.6.0+   the flat field input name 
        'hfClass'           => null,    // the class attribute       
    );
            
    /**
     * Indicates the caller script type.
     * 
     * This can be either 'unknown', 'plugin', or 'theme'.
     * @since       3.7.6
     */
    public $sScriptType = 'unknown';
            
    /**
     * Indicates the action hook to display setting notices.
     * 
     * @since       3.7.9
     */
    public $sSettingNoticeActionHook = 'admin_notices';
         
    /**
     * Sets up necessary property values.
     * 
     * @remark      This class gets instantiated in every factory class so the constructor should be lightest as possible.
     */
    public function __construct( $oCaller, $sCallerPath, $sClassName, $sCapability, $sTextDomain, $sStructureType ) {
                
        $this->oCaller          = $oCaller;
        $this->sCallerPath      = $sCallerPath;
        $this->sClassName       = $sClassName; // sanitize name space path delimiter.
        $this->sCapability      = empty( $sCapability )
            ? 'manage_options'
            : $sCapability;
        $this->sTextDomain      = empty( $sTextDomain )
            ? 'admin-page-framework'
            : $sTextDomain;
        $this->sStructureType   = $sStructureType;
        $this->sPageNow         = $this->getPageNow();
        $this->bIsAdmin         = is_admin();
        $this->bIsAdminAjax     = in_array( $this->sPageNow, array( 'admin-ajax.php' ) );
           
        // Overloading property items - these will be set on demand
        unset(
            $this->sScriptType,
            $this->sClassHash
        );           
           
        $this->_setGlobals();
       
    }
        /**
         * Sets up global variables.
         * @since       3.7.9
         */
        private function _setGlobals() {
            
            if ( ! isset( $GLOBALS[ 'aAdminPageFramework' ] ) ) {
                $GLOBALS[ 'aAdminPageFramework' ] = array( 
                    'aFieldFlags' => array() 
                );
            }
            
        }
        
    /**
     * Sets up properties related to the form object.
     * @remark      Should be called right before the form object gets created.
     * @since       3.7.9
     */
    public function setFormProperties() {
        $this->aFormArguments = $this->getFormArguments();
        $this->aFormCallbacks = $this->getFormCallbacks();
    }
    /**
     * @remark      The Widget factory class access this method.
     * @return      array
     * @since       3.7.9
     */
    public function getFormArguments() {
        return array(
            'caller_id'                         => $this->sClassName,
            'structure_type'                    => $this->_sPropertyType,  // @todo change this to admin_page
            'action_hook_form_registration'     => $this->_sFormRegistrationHook,
        ) + $this->aFormArguments;
    }
    /**
     * @remark      The widget factory class accesses this method.
     * @return      array
     * @since       3.7.9
     */
    public function getFormCallbacks() {
        return array(
            'is_in_the_page'                    => array( $this->oCaller, '_replyToDetermineWhetherToProcessFormRegistration' ),
            'load_fieldset_resource'            => array( $this->oCaller, '_replyToFieldsetResourceRegistration' ),
            
'is_fieldset_registration_allowed'  => null,

            'capability'                        => array( $this->oCaller, '_replyToGetCapabilityForForm' ),
            'saved_data'                        => array( $this->oCaller, '_replyToGetSavedFormData' ),
            
            // Outputs
            'section_head_output'               => array( $this->oCaller, '_replyToGetSectionHeaderOutput' ),
            'fieldset_output'                   => array( $this->oCaller, '_replyToGetFieldOutput' ),
             
            // 
            'sectionset_before_output'          => array( $this->oCaller, '_replyToFormatSectionsetDefinition' ),
            'fieldset_before_output'            => array( $this->oCaller, '_replyToFormatFieldsetDefinition' ),
            'fieldset_after_formatting'         => array( $this->oCaller, '_replyToModifyFieldsetDefinition' ),
            'fieldsets_after_formatting'        => array( $this->oCaller, '_replyToModifyFieldsetsDefinitions' ),
            
            'is_sectionset_visible'             => array( $this->oCaller, '_replyToDetermineSectionsetVisibility' ),
            'is_fieldset_visible'               => array( $this->oCaller, '_replyToDetermineFieldsetVisibility' ),
            
            'secitonsets_before_registration'   => array( $this->oCaller, '_replyToModifySectionsets' ),
            'fieldsets_before_registration'     => array( $this->oCaller, '_replyToModifyFieldsets' ),
            
            'handle_form_data'                  => array( $this->oCaller, '_replyToHandleSubmittedFormData' ),        
            
            // legacy callbacks
            'hfID'                              => array( $this->oCaller, '_replyToGetInputID' ), // the input id attribute
            'hfTagID'                           => array( $this->oCaller, '_replyToGetInputTagIDAttribute' ), // the fields & fieldset & field row container id attribute
            'hfName'                            => array( $this->oCaller, '_replyToGetFieldNameAttribute' ), // the input name attribute
            'hfNameFlat'                        => array( $this->oCaller, '_replyToGetFlatFieldName' ), // the flat input name attribute
            'hfInputName'                       => array( $this->oCaller, '_replyToGetInputNameAttribute' ),    // 3.6.0+   the field input name attribute
            'hfInputNameFlat'                   => array( $this->oCaller, '_replyToGetFlatInputName' ),    // 3.6.0+   the flat field input name                 
            'hfClass'                           => array( $this->oCaller, '_replyToGetInputClassAttribute' ), // the class attribute
            'hfSectionName'                     => array( $this->oCaller, '_replyToGetSectionName' ), // 3.6.0+            
        ) + $this->aFormCallbacks;        
    }
    
    /**
     * Returns the caller object.
     * 
     * This is used from other sub classes that need to retrieve the caller object.
     * 
     * @since       2.1.5
     * @access      public    
     * @return      object The caller class object.
     * @internal
     */     
    public function _getCallerObject() {
        return $this->oCaller;
    }
    
    /**
     * Sets the library information property array.
     * @internal
     * @since       3.0.0
     */
    static public function _setLibraryData() {

        self::$_aLibraryData = array(
            'sName'         => AdminPageFramework_Registry::NAME,
            'sURI'          => AdminPageFramework_Registry::URI,
            'sScriptName'   => AdminPageFramework_Registry::NAME,
            'sLibraryName'  => AdminPageFramework_Registry::NAME,
            'sLibraryURI'   => AdminPageFramework_Registry::URI,
            'sPluginName'   => '',
            'sPluginURI'    => '',
            'sThemeName'    => '',
            'sThemeURI'     => '',
            'sVersion'      => AdminPageFramework_Registry::getVersion(),
            'sDescription'  => AdminPageFramework_Registry::DESCRIPTION,
            'sAuthor'       => AdminPageFramework_Registry::AUTHOR,
            'sAuthorURI'    => AdminPageFramework_Registry::AUTHOR_URI,
            'sTextDomain'   => AdminPageFramework_Registry::TEXT_DOMAIN,
            'sDomainPath'   => AdminPageFramework_Registry::TEXT_DOMAIN_PATH,
            'sNetwork'      => '',
            '_sitewide'     => '',
        );
        return self::$_aLibraryData;
        
    }
    /**
     * Returns the set library data array.
     * 
     * @internal
     * @since       3.0.0
     */
    static public function _getLibraryData() {
        return isset( self::$_aLibraryData ) 
            ? self::$_aLibraryData 
            : self::_setLibraryData();     
    }
    
    /*
     * Methods for getting script info.
     */      
    /**
     * Retrieves the caller script information whether it's a theme or plugin or something else.
     * 
     * @since       2.0.0
     * @since       3.0.0       Moved from the link class.
     * @since       3.7.9       Changed the default value to an empty string. Made it use a cache if set.
     * @remark      The information can be used to embed into the footer etc.
     * @return      array       The information of the script.
     */  
    protected function getCallerInfo( $sCallerPath='' ) {

        if ( isset( self::$_aScriptDataCaches[ $sCallerPath ] ) ) {
            return self::$_aScriptDataCaches[ $sCallerPath ];
        }
    
        $_aCallerInfo            = self::$_aStructure_CallerInfo;
        $_aCallerInfo[ 'sPath' ] = $sCallerPath;
        $_aCallerInfo[ 'sType' ] = $this->_getCallerType( $_aCallerInfo[ 'sPath' ] );

        if ( 'unknown' == $_aCallerInfo[ 'sType' ] ) {
            self::$_aScriptDataCaches[ $sCallerPath ] = $_aCallerInfo;
            return $_aCallerInfo;
        }
        if ( 'plugin' == $_aCallerInfo[ 'sType' ] ) {
            self::$_aScriptDataCaches[ $sCallerPath ] = $this->getScriptData( $_aCallerInfo[ 'sPath' ], $_aCallerInfo[ 'sType' ] ) + $_aCallerInfo;
            return self::$_aScriptDataCaches[ $sCallerPath ];
        }
        if ( 'theme' == $_aCallerInfo[ 'sType' ] ) {
            $_oTheme = wp_get_theme(); // stores the theme info object
            self::$_aScriptDataCaches[ $sCallerPath ] = array(
                'sName'         => $_oTheme->Name,
                'sVersion'      => $_oTheme->Version,
                'sThemeURI'     => $_oTheme->get( 'ThemeURI' ),
                'sURI'          => $_oTheme->get( 'ThemeURI' ),
                'sAuthorURI'    => $_oTheme->get( 'AuthorURI' ),
                'sAuthor'       => $_oTheme->get( 'Author' ),     
            ) + $_aCallerInfo;    
            return self::$_aScriptDataCaches[ $sCallerPath ];
        }
        self::$_aScriptDataCaches[ $sCallerPath ] = array();
        return self::$_aScriptDataCaches[ $sCallerPath ];
        
    }    
        /** 
         * @since       3.7.9
         */
        static private $_aScriptDataCaches = array();
    
    /**
     * Determines the script type.
     * 
     * It tries to find what kind of script this is, theme, plugin or something else from the given path.
     * 
     * @since       2.0.0
     * @since       3.0.0       Moved from the link class.
     * @since       3.1.5       Changed the scope to protected as the post type property class access it.
     * @return      string      Returns either 'theme', 'plugin', or 'unknown'
     */ 
    protected function _getCallerType( $sScriptPath ) {
                
        if ( isset( self::$_aCallerTypeCache[ $sScriptPath ] ) ) {
            return self::$_aCallerTypeCache[ $sScriptPath ];
        }
        
        if ( preg_match( '/[\/\\\\]themes[\/\\\\]/', $sScriptPath, $m ) ) {
            self::$_aCallerTypeCache[ $sScriptPath ] = 'theme';
            return 'theme';
        }
        if ( preg_match( '/[\/\\\\]plugins[\/\\\\]/', $sScriptPath, $m ) ) {
            self::$_aCallerTypeCache[ $sScriptPath ] = 'plugin';
            return 'plugin';
        }
        self::$_aCallerTypeCache[ $sScriptPath ] = 'unknown';
        return 'unknown';
    
    }    
        static private $_aCallerTypeCache = array();
            
    /**
     * Retrieves the option array.
     * 
     * This method should be extended in the extended class.
     * 
     * @remark      This method is triggered from the __get() overload magic method to set the $aOptions property.
     * @since       3.1.0
     * @internal
     */
    protected function _getOptions() {
        return array();
    }
    
    /*
     * Magic methods
     * */
    /**
     * 
     * @since 3.1.3
     */
    public function __get( $sName ) {
        
        if ( 'aScriptInfo' === $sName ) {
            $this->sCallerPath = $this->sCallerPath 
                ? $this->sCallerPath 
                : $this->getCallerScriptPath( __FILE__ );
            $this->aScriptInfo = $this->getCallerInfo( $this->sCallerPath );
            return $this->aScriptInfo;    
        }
        
        // 3.4.1+ Moved from `AdminPageFramework_Property_Page` as meta box classes also access it.
        // If $this->aOptions is called for the first time, retrieve the option data from the database and assign them to the property.
        // Once this is done, calling $this->aOptions will not trigger the __get() magic method any more.
        if ( 'aOptions' === $sName ) {
            $this->aOptions = $this->_getOptions();
            return $this->aOptions;    
        }     

        // 3.7.9 Moved from the constructor to make it lighter.
        if ( 'sClassHash' === $sName ) {
            $this->sClassHash       = md5( $this->sClassName );    
            return $this->sClassHash;
        }
        if ( 'sScriptType' === $sName ) {
            $this->sScriptType      = $this->_getCallerType( $this->sCallerPath );    // 3.7.6+        
            return $this->sScriptType;
        }
        if ( 'oUtil' === $sName ) {
            $this->oUtil = new AdminPageFramework_WPUtility;
            return $this->oUtil;
        }
        
    }
    
}