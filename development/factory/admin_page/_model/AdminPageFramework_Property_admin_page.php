<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides the space to store the shared properties.
 * 
 * This class stores various types of values. This is used to encapsulate properties so that it helps to avoid naming conflicts.
 * 
 * @since       2.0.0
 * @package     AdminPageFramework/Factory/AdminPage/Property
 * @extends     AdminPageFramework_Property_Base
 * @internal
 */
class AdminPageFramework_Property_admin_page extends AdminPageFramework_Property_Base {
    
    /**
     * Defines the property type.
     * @remark Setting the property type helps to check whether some components are loaded such as scripts that can be reused per a class type basis.
     * @since       3.0.0
     * @since       3.7.0      Changed the default value from `page`.
     * @internal
     */
    public $_sPropertyType = 'admin_page';
    
    /**
     * Defines the fields type.
     * 
     * @since       3.1.0
     * @since       3.7.0      Changed the default value from `page`. Renamed from `$sFieldsType`.
     */
    public $sStructureType = 'admin_page';
    
    /**
     * Stores framework's instantiated object name.
     * 
     * @since 2.0.0
     */ 
    public $sClassName;    
    
    /**
     * Stores the md5 hash string of framework's instantiated object name.
     * @since 2.1.1
     */
    public $sClassHash;
    
    /**
     * Stores the access level to the root page. 
     * 
     * When sub pages are added and the capability value is not provided, this will be applied.
     * 
     * @since 2.0.0
     */     
    public $sCapability = 'manage_options';    
    
    /**
     * Stores the tag for the page heading navigation bar.
     * @since 2.0.0
     */ 
    public $sPageHeadingTabTag = 'h2';

    /**
     * Stores the tag for the in-page tab navigation bar.
     * @since 2.0.0
     */ 
    public $sInPageTabTag = 'h3';
    
    /**
     * Stores the default page slug.
     * @since 2.0.0
     */     
    public $sDefaultPageSlug;
        
    // Container arrays.
    /**
     * A two-dimensional array storing registering sub-menu(page) item information with keys of the page slug.
     * @since 2.0.0
     */     
    public $aPages = array(); 

    /**
     * Stores the hidden page slugs.
     * @since 2.1.4
     */
    public $aHiddenPages = array();
    
    /**
     * Stores the registered sub menu pages.
     * 
     * Unlike the above $aPages that holds the pages to be added, this stores the added pages. This is referred when adding a help section.
     * 
     * @since 2.1.0
     */ 
    public $aRegisteredSubMenuPages = array();
    
    /**
     * Stores the root menu item information for one set root menu item.
     * @since 2.0.0
     */         
    public $aRootMenu = array(
        'sTitle'        => null, // menu label that appears on the menu list
        'sPageSlug'     => null, // menu slug that identifies the menu item
        'sIcon16x16'    => null, // the associated icon that appears beside the label on the list
        'iPosition'     => null, // determines the position of the menu
        'fCreateRoot'   => null, // indicates whether the framework should create the root menu or not.
    ); 
    
    /**
     * Stores in-page tabs.
     * @since 2.0.0
     */     
    public $aInPageTabs = array();     
    
    /**
     * Stores the default in-page tab.
     * @since 2.0.0
     */         
    public $aDefaultInPageTabs = array();     
        
    /**
     * Stores link text that is scheduled to be embedded in the plugin listing table's description column cell.
     * @since 2.0.0
     */             
    public $aPluginDescriptionLinks = array(); 

    /**
     * Stores link text that is scheduled to be embedded in the plugin listing table's title column cell.
     * @since 2.0.0
     */             
    public $aPluginTitleLinks = array();     
            
    /**
     * Stores the framework's options. 
     * 
     * Do not even declare the property here 
     * because the __get() magic method needs to be triggered 
     * when it accessed for the first time.
     */
    // public $aOptions; 

    /**
     * The instantiated class name will be assigned in the constructor if the first parameter is not set.
     * @since 2.0.0
     */                 
    public $sOptionKey = '';     

    /**
     * Stores contextual help tabs.
     * @since 2.1.0
     */     
    public $aHelpTabs = array();
    
    /**
     * Set one of the followings: application/x-www-form-urlencoded, multipart/form-data, text/plain
     * @since 2.0.0
     */                     
    public $sFormEncType = 'multipart/form-data';    
    
    /**
     * Stores the label for for the "Insert to Post" button in the media uploader box.
     * @since 2.0.0
     * @internal
     */     
    public $sThickBoxButtonUseThis = '';
    
    // Flags    
    /**
     * Decides whether the setting form tag is rendered or not.    
     * 
     * This will be enabled when a settings section and a field is added.
     * @since 2.0.0
     */                         
    public $bEnableForm = false;     
    
    /**
     * Indicates whether the page title should be displayed.
     * @since 2.0.0
     */                         
    public $bShowPageTitle = true;    
    
    /**
     * Indicates whether the page heading tabs should be displayed.
     * @since 2.0.0
     * @remark Used by the setPageHeadingTabsVisibility() method.
     */     
    public $bShowPageHeadingTabs = true;

    /**
     * Indicates whether the in-page tabs should be displayed.
     * 
     * This sets globally among the script using the framework. 
     * 
     * @since 2.1.2
     * @remark Used by the setInPageTabsVisibility() method.
     */
    public $bShowInPageTabs = true;

    /**
     * Stores the set administration notices.
     * 
     * The index number will be incremented as a script is enqueued regardless a previously added enqueue item has been removed or not.
     * This is because this index number will be used for the style handle ID which is automatically generated.
     * @since 2.1.2
     */
    public $aAdminNotices = array();
    
    /**
     * Stores the disallowed query keys in the links generated by the main class of the framework.
     * 
     * @remark Currently this does not take effect on the meta box and post type classes of the framework.
     * @since 2.1.2
     */
    public $aDisallowedQueryKeys = array( 
        'settings-updated', 
        'confirmation',     // 3.3.0+
        'field_errors'      // 3.4.1+
    );
        
        
    /** 
     * Stores the target page redirected to when the user submit the form of the framework.
     * 
     * @since 3.1.0
     */
    public $sTargetFormPage = '';
          
    /**
     * Indicates whether the form data should be automatically saved in the options table.
     * @since 3.1.0
     */ 
    public $_bDisableSavingOptions = false;
     
    /**
     * Stores added page hooks.
     * 
     * @since 3.1.2
     */ 
    public $aPageHooks = array();
     
    /**
     * Stores the wrapper element class attribute.
     * 
     * This allows users to set own class selector such as "wrap about-wrap" for About pages.
     * 
     * @since       3.5.0
     */ 
    public $sWrapperClassAttribute = 'wrap';
    
    /**
     * Stores the type of the form data.
     * 
     * @remark      Currently only accepts 'options_table' or 'transient'
     * @since       3.5.9
     */ 
    public $sOptionType = 'options_table';
     
    /**
     * Stores the cache lifetime of the transient used for the form options when the user passes an integer to the option key parameter.
     * @since       3.5.9
     */
    public $iOptionTransientDuration  = 0; 
     
    /**
     * Constructs the instance of AdminPageFramework_Property_admin_page class object.
     * 
     * @remark      Used by the setInPageTabsVisibility() method.
     * @since       2.0.0
     * @since       2.1.5   The $oCaller parameter was added.
     */ 
    public function __construct( $oCaller, $sCallerPath, $sClassName, $aisOptionKey, $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {

        // 3.7.0+ This must be set before the parent constructor. As the form arguments array uses this value.
        $this->_sFormRegistrationHook = 'load_after_' . $sClassName;

        parent::__construct( 
            $oCaller, 
            $sCallerPath, 
            $sClassName, 
            $sCapability, 
            $sTextDomain, 
            $this->sStructureType 
        );
        
        $this->sTargetFormPage = $_SERVER[ 'REQUEST_URI' ];
        
        $this->_setOptionsProperties( 
            $aisOptionKey, 
            $sClassName 
        );
                
        // Store the page class objects in the global storage. 
        // These will be referred by the meta box class to determine if the passed page slug's screen ID (hook suffix).
        $GLOBALS[ 'aAdminPageFramework' ][ 'aPageClasses' ] = $this->getElementAsArray(
            $GLOBALS,
            array( 'aAdminPageFramework', 'aPageClasses' )
        );
        // The meta box class for pages needs to access the object.
        $GLOBALS[ 'aAdminPageFramework' ][ 'aPageClasses' ][ $sClassName ] = $oCaller; 
                
        // The capability for the settings. `$this->sOptionKey` is the part that is set in the settings_fields() function.
        // This prevents the "Cheatin' huh?" message.
        add_filter( "option_page_capability_{$this->sOptionKey}", array( $this, '_replyToGetCapability' ) );
        
    }
        /**
         * Sets up the properties of options.
         * @since       3.5.9
         * @return      void
         */
        private function _setOptionsProperties( $aisOptionKey, $sClassName ) {            
                        
            $_aArguments = is_array( $aisOptionKey ) 
                ? $aisOptionKey
                : array();
            $_aArguments = $_aArguments + array(
                'type' => $this->_getOptionType( 
                    $aisOptionKey 
                ),
                'key' => $this->_getOptionKey( 
                    $aisOptionKey,
                    $sClassName
                ),
                'duration' => is_integer( $aisOptionKey )
                    ? $aisOptionKey
                    : 0
            );
            
            $this->sOptionKey               = $_aArguments[ 'key' ];
            $this->sOptionType              = $_aArguments[ 'type' ];
            $this->iOptionTransientDuration = $_aArguments[ 'duration' ];
            $this->_bDisableSavingOptions   = '' === $aisOptionKey;
        
        }    
            /**
             * Returns a key used for the options table.
             * @since       3.5.9
             * @return      string
             */
            private function _getOptionKey( $aisOptionKey, $sClassName ) {
                
                $_sType = gettype( $aisOptionKey );
                if ( in_array( $_sType, array( 'NULL', 'string' ) ) ) {
                    return $aisOptionKey
                        ? $aisOptionKey
                        : $sClassName;
                }
                // The user want to save options in a transient.
                if ( in_array( $_sType, array( 'integer' ) ) ) {
                    return 'apf_' . md5( site_url() . '_' . $sClassName . '_' . get_current_user_id() );
                }
                
                // Unknown type - maybe the user is trying to do something advanced.
                return $aisOptionKey;
                
            }
            /**
             * Returns a form data type from the given option key.
             * 
             * @since       3.5.9
             * @return      string      Currently only 'transient' or 'options_table' is supported.
             */
            private function _getOptionType( $aisOptionKey ) {
                return is_integer( $aisOptionKey )
                    ? 'transient'
                    : 'options_table';
            }
            
        /**
         * Checks whether the currently loading page is in one of the pages to which the framework can add pages.
         * 
         * @since 3.0.3
         * @internal
         */
        protected function _isAdminPage() {
            
            if ( ! is_admin() ) {
                return false;
            }
            return isset( $_GET[ 'page' ] );
            
        }    
    
    /**
     * Returns the option array.
     * 
     * @since       3.1.0
     * @since       3.3.0       Forced to return an array as it is possible that the options value get modified by third party scripts. 
     * @internal
     * @return      array       The options array.
     */
    protected function _getOptions() {
        return $this->_getOptionsByType( $this->sOptionType );
    }
        /**
         * Returns options data by a given type.
         * 
         * This is to support transient form data which disappears with the set timeout.
         * 
         * @return      array       The retrieved options array.
         */ 
        private function _getOptionsByType( $sOptionType ) {
            switch ( $sOptionType ) {
                default:
                case 'options_table':
                    return $this->sOptionKey 
                        ? $this->getAsArray( 
                            get_option( 
                                $this->sOptionKey, // option key
                                array() // default
                            ) 
                        )
                        : array();                       
                case 'transient':
                    return $this->getAsArray(
                        $this->getTransient( 
                            $this->sOptionKey,  // transient key
                            array() // default
                        )
                    );
            }
        }
             
    /**
     * Saves the options into the database.
     * 
     * @since       3.1.0
     * @since       3.1.1       Made it return a value.
     * @return      boolean     True if saved; otherwise, false.
     */
    public function updateOption( $aOptions=null ) {

        if ( $this->_bDisableSavingOptions ) {
            return false;
        }
        return $this->_updateOptionsByType( 
            null !== $aOptions
                ? $aOptions 
                : $this->aOptions,
            $this->sOptionType
        );
                
    }
        /**
         * 
         * @since       3.5.9
         * @return      boolean     True if saved; otherwise, false.
         */
        private function _updateOptionsByType( $aOptions, $sOptionType ) {
            switch ( $sOptionType ) {
                default:
                case 'options_table':
                    return update_option( 
                        $this->sOptionKey, 
                        $aOptions
                    );
                case 'transient':
                    return $this->setTransient( 
                        $this->sOptionKey,  // transient key
                        $aOptions,
                        $this->iOptionTransientDuration
                    );
            }            
            
        }
    
    
    /**
     * Checks if the given page slug is one of the pages added by the framework.
     * @since       2.0.0
     * @since       2.1.0       Set the default value to the parameter and if the parameter value is empty, it applies the current $_GET[ 'page' ] value.
     * @return      boolean     Returns true if it is of framework's added page; otherwise, false.
     */
    public function isPageAdded( $sPageSlug='' ) {    
        
        $sPageSlug = trim( $sPageSlug );
        $sPageSlug = $sPageSlug 
            ? $sPageSlug 
            : $this->getCurrentPageSlug();
        return isset( $this->aPages[ $sPageSlug ] );

    }
    
    /**
     * Retrieves the currently loading page slug.
     * 
     * @since       3.5.3
     * @return      string      The found page slug. An empty string if not found.
     * @remark      Do not return `null` when not found as some framework methods check the retuened value with `isset()` and if null is given, `isset()` yields `false` while it does `true` for an emtpy string ('').
     */
    public function getCurrentPageSlug() {
        return $this->getElement( 
            $_GET,  // subject array
            'page', // key
            ''      // default
        );            
    }

    /**
     * Retrieves the currently loading tab slug.
     * 
     * The tricky part is that even no tab is set in the $_GET array, it's possible that it could be the default tab of the loading page.
     * This method checks that.
     * 
     * @since       3.0.0
     * @since       3.5.0       Added the `$sCurrentPageSlug` parameter because the page-meta-box class determines the caller factory object by page slug.
     * @since       3.5.3       Changed the name from 'getCurrentTab()' to be more specific.
     * @return      string      The found tab slug. An empty string if not found.
     * @remark      Do not return `null` when not found as some framework methods check the returned value with `isset()` and if null is given, `isset()` yields `false` while it does `true` for an empty string ('').
     */    
    public function getCurrentTabSlug( $sCurrentPageSlug='' ) {
        
        // It is possible that the tab slug is not set if it is the default tab.
        $_sTabSlug = $this->getElement( $_GET, 'tab' );
        if ( $_sTabSlug ) { 
            return $_sTabSlug;
        }
        $sCurrentPageSlug = $sCurrentPageSlug
            ? $sCurrentPageSlug
            : $this->getCurrentPageSlug();
        return $sCurrentPageSlug
            ? $this->getDefaultInPageTab( $sCurrentPageSlug )
            : '';
            
    }    
        /**
         * An alias of getCurrentTabSlug();
         * 
         * @deprecated  3.5.3
         */
        public function getCurrentTab( $sCurrentPageSlug='' ) {
            return $this->getCurrentTabSlug( $sCurrentPageSlug );            
        }
    
    /**
     * Retrieves the default in-page tab from the given tab slug.
     * 
     * @since 2.0.0
     * @since 2.1.5 Made it public and moved from the AdminPageFramework_Page class since this method is used by the AdminPageFramework_HeadTab class as well.
     * @internal
     * @remark Used in the __call() method in the main class.
     * @return string The default in-page tab slug if found; otherwise, an empty string.
     */         
    public function getDefaultInPageTab( $sPageSlug ) {
    
        if ( ! $sPageSlug ) { 
            return ''; 
        }
        return $this->getElement( 
            $this->aDefaultInPageTabs,  // subject array
            $sPageSlug, // key
            ''    // default
        );
        
    }    
           
    /**
     * Returns the set capability.
     * @callback        option_page_capability_{$this->sOptionKey}
     */ 
    public function _replyToGetCapability() {
        return $this->sCapability;
    }
    
    
    /**
     * Returns the currently loaded page slug if it is one of the added ones with the class object.
     * 
     * @remark      Used for adding form elements.
     * @since       3.7.2
     * @return      string|null
     */
    public function getCurrentPageSlugIfAdded() {
        
        // Cache
        static $_nsCurrentPageSlugFromAddedOnes;
        if ( $this->hasBeenCalled( __METHOD__ ) ) {
            return $_nsCurrentPageSlugFromAddedOnes;
        }
        
        // Extract the slug from the page definition
        $_nsCurrentPageSlug              = $this->getElement( $_GET, 'page', null );
        $_nsCurrentPageSlugFromAddedOnes = $this->getElement( 
            $this->aPages,
            array( $_nsCurrentPageSlug, 'page_slug' )
        );
        return $_nsCurrentPageSlugFromAddedOnes;
        
    }    

    /**
     * Returns the currently loaded tab slug if it is one of the added ones with the class object.
     * @return      string|null
     * @sine        3.7.2
     */
    public function getCurrentInPageTabSlugIfAdded() {
        
        // Cache
        static $_nsCurrentTabSlugFromAddedOnes;
        if ( $this->hasBeenCalled( __METHOD__ ) ) {
            return $_nsCurrentTabSlugFromAddedOnes;
        }
        
        $_nsCurrentTabSlugFromAddedOnes = $this->getElement(
            $this->aInPageTabs,
            array( 
                $this->getCurrentPageSlugIfAdded(), 
                $this->getCurrentTabSlug(), 
                'tab_slug' 
            )
        );
        return $_nsCurrentTabSlugFromAddedOnes;
        
    }    
        
}
