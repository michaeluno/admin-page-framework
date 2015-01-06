<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * The base class for Property classes.
 * 
 * Provides the common methods  and properties for the property classes that are used by the main class, the meta box class, and the post type class.
 * @since 2.1.0
 * @package AdminPageFramework
 * @subpackage Property
 * @internal
 */ 
abstract class AdminPageFramework_Property_Base {

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
     * @since       2.1.5 Moved the contents to the taxonomy field definition so it become an empty string.
     * @deprecated  3.2.0
     * @internal
     */
    public static $_sDefaultStyleIE = '';
        

    /**
     * Stores enqueuing script URLs and their criteria by md5 hash of the source url.
     * @since 2.1.2
     * @since 2.1.5 Moved to the base class.
     */
    public $aEnqueuingScripts = array();
    /**    
     * Stores enqueuing style URLs and their criteria by md5 hash of the source url.
     * @since 2.1.2
     * @since 2.1.5 Moved to the base class.
     */    
    public $aEnqueuingStyles = array();


    /**
     * Stores enqueued script/style argument array by its url.
     * @since   3.3.0
     */
    public $aResourceAttributes = array();

    
    /**
     * Stores the index of enqueued scripts.
     * 
     * @since 2.1.2
     * @since 2.1.5 Moved to the base class.
     */
    public $iEnqueuedScriptIndex = 0;
    /**
     * Stores the index of enqueued styles.
     * 
     * The index number will be incremented as a script is enqueued regardless a previously added enqueue item has been removed or not.
     * This is because this index number will be used for the script handle ID which is automatically generated.
     * 
     * @since 2.1.2
     * @since 2.1.5 Moved to the base class.
     */    
    public $iEnqueuedStyleIndex = 0;     
    
    /**
     * Stores is_admin() value to be reused. 
     * 
     * @since 3.0.0
     */
    public $bIsAdmin;
    
    /**
     * Stores the flag that indicates whether the library is minified or not.
     * @since 3.0.0
     * @deprecated 3.1.3 Use AdminPageFramework_Registry::$bIsMinifiedVersion
     */
    public $bIsMinifiedVersion;
        
    /**
     * Stores the capability for displayable elements.
     * 
     * @since 2.0.0
     */     
    public $sCapability;
    
    /**
     * Defines the fields type.
     * 
     * Can be either 'page', 'network_admin_page', 'post_meta_box', 'page_meta_box', 'post_type', 'taxonomy'
     * 
     * @since 3.0.4
     * @internal
     */
    public $sFieldsType;     
        
    /**
     * Stores the text domain.
     * 
     * @since 3.0.4
     * @internal
     */ 
    public $sTextDomain;
    
    /**
     * Stores the current page's base name.
     * 
     * @since 3.0.5
     * @internal
     */
    public $sPageNow;
    
    /**
     * Indicates whether the setUp() method is loaded.
     *  
     * @since 3.1.0
     * @internal
     */
    public $_bSetupLoaded;
    
    /**
     * Indicates whether the current page is in admin-ajax.php
     * 
     * @since 3.1.3
     * @internal
     */
    public $bIsAdminAjax;
        
    /**
     * Stores callable for the form field outputs such as the id and name attribute values.
     * 
     * @internal
     * @since       3.2.0
     */
    public $aFieldCallbacks  = array(
        'hfID'          => null,    // the input id attribute
        'hfTagID'       => null,    // the fields & fieldset & field row container id attribute
        'hfName'        => null,    // the input name attribute
        'hfNameFlat'    => null,    // the flat input name attribute
        'hfClass'       => null,    // the class attribute
    );
                
    /**
     * Sets up necessary property values.
     */
    public function __construct( $oCaller, $sCallerPath, $sClassName, $sCapability, $sTextDomain, $sFieldsType ) {
        
        $this->oCaller          = $oCaller;
        $this->sCallerPath      = $sCallerPath ? $sCallerPath : null;
        $this->sClassName       = $sClassName;     
        $this->sClassHash       = md5( $sClassName );    
        $this->sCapability      = empty( $sCapability ) ? 'manage_options' : $sCapability ;
        $this->sTextDomain      = empty( $sTextDomain ) ? 'admin-page-framework' : $sTextDomain;
        $this->sFieldsType      = $sFieldsType;
        $GLOBALS['aAdminPageFramework'] = isset( $GLOBALS['aAdminPageFramework'] ) && is_array( $GLOBALS['aAdminPageFramework'] ) 
            ? $GLOBALS['aAdminPageFramework']
            : array( 'aFieldFlags' => array() );
        $this->sPageNow         = AdminPageFramework_WPUtility::getPageNow();
        $this->bIsAdmin         = is_admin();
        $this->bIsAdminAjax     = in_array( $this->sPageNow, array( 'admin-ajax.php' ) );
        
    }
        
    /**
     * Returns the caller object.
     * 
     * This is used from other sub classes that need to retrieve the caller object.
     * 
     * @since 2.1.5
     * @access public    
     * @return object The caller class object.
     * @internal
     */     
    public function _getCallerObject() {
        return $this->oCaller;
    }
    
    /**
     * Sets the library information property array.
     * @internal
     * @since 3.0.0
     */
    static public function _setLibraryData() {

        self::$_aLibraryData = array(
            'sName'         => AdminPageFramework_Registry::Name,
            'sURI'          => AdminPageFramework_Registry::URI,
            'sScriptName'   => AdminPageFramework_Registry::Name,
            'sLibraryName'  => AdminPageFramework_Registry::Name,
            'sLibraryURI'   => AdminPageFramework_Registry::URI,
            'sPluginName'   => '',
            'sPluginURI'    => '',
            'sThemeName'    => '',
            'sThemeURI'     => '',
            'sVersion'      => AdminPageFramework_Registry::getVersion(),
            'sDescription'  => AdminPageFramework_Registry::Description,
            'sAuthor'       => AdminPageFramework_Registry::Author,
            'sAuthorURI'    => AdminPageFramework_Registry::AuthorURI,
            'sTextDomain'   => AdminPageFramework_Registry::TextDomain,
            'sDomainPath'   => AdminPageFramework_Registry::TextDomainPath,
            'sNetwork'      => '',
            '_sitewide'     => '',
        );
        return self::$_aLibraryData;
        
    }
    /**
     * Returns the set library data array.
     * 
     * @internal
     * @since 3.0.0
     */
    static public function _getLibraryData() {
        return isset( self::$_aLibraryData ) ? self::$_aLibraryData : self::_setLibraryData();     
    }
    
    /*
     * Methods for getting script info.
     */      
    /**
     * Retrieves the caller script information whether it's a theme or plugin or something else.
     * 
     * @since 2.0.0
     * @since 3.0.0 Moved from the link class.
     * @remark The information can be used to embed into the footer etc.
     * @return array The information of the script.
     */  
    protected function getCallerInfo( $sCallerPath=null ) {
        
        $_aCallerInfo          = self::$_aStructure_CallerInfo;
        $_aCallerInfo['sPath'] = $sCallerPath;
        $_aCallerInfo['sType'] = $this->_getCallerType( $_aCallerInfo['sPath'] );

        if ( 'unknown' == $_aCallerInfo['sType'] ) {
            return $_aCallerInfo;
        }
        if ( 'plugin' == $_aCallerInfo['sType'] ) {
            return AdminPageFramework_WPUtility::getScriptData( $_aCallerInfo['sPath'], $_aCallerInfo['sType'] ) + $_aCallerInfo;
        }
        if ( 'theme' == $_aCallerInfo['sType'] ) {
            $_oTheme = wp_get_theme(); // stores the theme info object
            return array(
                'sName'         => $_oTheme->Name,
                'sVersion'      => $_oTheme->Version,
                'sThemeURI'     => $_oTheme->get( 'ThemeURI' ),
                'sURI'          => $_oTheme->get( 'ThemeURI' ),
                'sAuthorURI'    => $_oTheme->get( 'AuthorURI' ),
                'sAuthor'       => $_oTheme->get( 'Author' ),     
            ) + $_aCallerInfo;    
        }
        return array();
    }    
    
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
        
        if ( preg_match( '/[\/\\\\]themes[\/\\\\]/', $sScriptPath, $m ) ) {
            return 'theme';
        }
        if ( preg_match( '/[\/\\\\]plugins[\/\\\\]/', $sScriptPath, $m ) ) {
            return 'plugin';
        }
        return 'unknown';    
    
    }    
        
    
    /**
     * Checks if the current page is post editing page that belongs to the given post type(s).
     * 
     * @since 3.0.0
     * @param array|string The post type slug(s) to check. If this is empty, the method just checks the current page is a post definition page.
     * Otherwise, it will check if the page belongs to the given post type(s).
     * @return boolean
     * @deprecated
     */
    public function isPostDefinitionPage( $asPostTypes=array() ) {
        
        $_aPostTypes = ( array ) $asPostTypes;
        
        // If it's not the post definition page, 
        if ( ! in_array( $this->sPageNow, array( 'post.php', 'post-new.php', ) ) ) {
            return false;
        }
        
        // If the parameter is empty, 
        if ( empty( $_aPostTypes ) ) {
            return true;
        }
        
        // If the parameter the post type are set and it's in the given post types, 
        if ( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], $_aPostTypes ) ) {
            return true;
        }
        
        // Find the post type from the post ID.
        $this->_sCurrentPostType = isset( $this->_sCurrentPostType )
            ? $this->_sCurrentPostType
            : ( isset( $_GET['post'] )
                ? get_post_type( $_GET['post'] )
                : ''
            );
        
        // If the found post type is in the given post types,
        if ( isset( $_GET['post'], $_GET['action'] ) && in_array( $this->_sCurrentPostType, $_aPostTypes ) ) {
            return true;     
        }     
        
        // Otherwise,
        return false;
        
    }    
    
    /**
     * Retrieves the option array.
     * 
     * This method should be extended in the extended class.
     * 
     * @since       3.1.0
     * @internal
     */
    protected function _getOptions() { return array(); }

    /**
     * Returns the last user form input array.
     * 
     * @remark      This temporary data is not always set. This is only set when the form needs to show a confirmation message to the user such as for sending an email.
     * @since       3.3.0
     * @since       3.4.1       Moved from `AdminPageFramework_Property_Page`.
     * @internal
     * @return      array   The last input array.
     */
    protected function _getLastInput() {
        
        $_vValue = AdminPageFramework_WPUtility::getTransient( 'apf_tfd' . md5( 'temporary_form_data_' . $this->sClassName . get_current_user_id() ) );
        if ( is_array( $_vValue ) ) {
            return $_vValue;
        }
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
            $this->sCallerPath = $this->sCallerPath ? $this->sCallerPath : AdminPageFramework_Utility::getCallerScriptPath( __FILE__ );
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
        
        // 3.3.0+   Sets and returns the last user form input data as an array.
        // 3.4.1+   Moved from `AdminPageFramework_Property_Page` as meta box classes also access it.
        if ( 'aLastInput' === $sName ) {
            $this->aLastInput = $this->_getLastInput();
            return $this->aLastInput;
        }        
        
        // For regular undefined items, 
        // return 'undefined';
        
    }
    
}