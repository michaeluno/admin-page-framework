<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Deals with redirecting function calls and instantiating classes.
 *
 * @abstract
 * @since           3.0.0     
 * @since           3.3.1       Changed from `AdminPageFramework_Base`.
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 */
abstract class AdminPageFramework_Router extends AdminPageFramework_Factory {
    
    /**
     * Stores the prefixes of the filters used by this framework.
     * 
     * This must not use the private scope as the extended class accesses it, such as 'start_' and must use the public since another class uses this externally.
     * 
     * @since       2.0.0
     * @since       2.1.5       Made it public from protected since the HeadTag class accesses it.
     * @since       3.0.0       Moved from `AdminPageFramework_Page`. Changed the scope to protected as the head tag class no longer access this property.
     * @since       3.3.1       Moved from `AdminPageFramework_Base`. Deprecated
     * @var         array
     * @static
     * @access      protected
     * @internal
     * @deprecated  3.3.1
     */ 
    protected static $_aHookPrefixes = array(    
        'start_'                        => 'start_',
        'set_up_'                       => 'set_up_', // 3.1.3+
        'load_'                         => 'load_',     
        'load_after_'                   => 'load_after_', // 3.1.3+
        'do_before_'                    => 'do_before_',
        'do_after_'                     => 'do_after_',
        'do_form_'                      => 'do_form_',
        'do_'                           => 'do_',
        'submit_'                       => 'submit_', // 3.0.0+
        'content_top_'                  => 'content_top_',         // 3.2.1+
        'content_bottom_'               => 'content_bottom_',     // 3.0.0+
        'content_'                      => 'content_',
        'validation_'                   => 'validation_',
        'validation_saved_options_'     => 'validation_saved_options_', // [3.0.0+]
        'export_name'                   => 'export_name',
        'export_format'                 => 'export_format',
        'export_'                       => 'export_',
        'import_name'                   => 'import_name',
        'import_format'                 => 'import_format',
        'import_'                       => 'import_',
        'style_common_ie_'              => 'style_common_ie_',
        'style_common_'                 => 'style_common_',
        'style_ie_'                     => 'style_ie_',
        'style_'                        => 'style_',
        'script_'                       => 'script_',
        
        'field_'                        => 'field_',
        'section_head_'                 => 'section_head_', // 3.0.0+ Changed from 'section_'
        'fields_'                       => 'fields_',
        'sections_'                     => 'sections_',
        'pages_'                        => 'pages_',
        'tabs_'                         => 'tabs_',
        
        'field_types_'                  => 'field_types_',
        'field_definition_'             => 'field_definition_', // 3.0.2+
        'options_'                      => 'options_', // 3.1.0+
    );    
    
    /**'
     * Sets up hooks and properties.
     * 
     * @since       3.3.0
     */
    function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {
                
        // Objects
        $this->oProp = isset( $this->oProp ) 
            ? $this->oProp // for the AdminPageFramework_NetworkAdmin class
            : new AdminPageFramework_Property_Page( $this, $sCallerPath, get_class( $this ), $sOptionKey, $sCapability, $sTextDomain );

        parent::__construct( $this->oProp );

        if ( $this->oProp->bIsAdminAjax ) {
            return;
        }     
        if ( $this->oProp->bIsAdmin ) {
            add_action( 'wp_loaded', array( $this, 'setup_pre' ) );     
        }
        
    }
    
    /**
     * The magic method which redirects callback-function calls with the pre-defined prefixes for hooks to the appropriate methods. 
     * 
     * @access      public
     * @remark      the users do not need to call or extend this method unless they know what they are doing.
     * @param       string      the called method name. 
     * @param       array       the argument array. The first element holds the parameters passed to the called method.
     * @return      mixed       depends on the called method. If the method name matches one of the hook prefixes, the redirected methods return value will be returned. Otherwise, none.
     * @since       2.0.0
     * @since       3.3.1       Moved from `AdminPageFramework_Base`.
     * @internal
     */
    public function __call( $sMethodName, $aArgs=null ) {     

        // The currently loading in-page tab slug. Be careful that not all cases $sMethodName have the page slug.
        $sPageSlug  = isset( $_GET['page'] ) ? $_GET['page'] : null;    
        $sTabSlug   = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->oProp->getDefaultInPageTab( $sPageSlug );    

        if ( 'setup_pre' === $sMethodName ) {
            $this->_setUp();
            $this->oUtil->addAndDoAction( $this, "set_up_{$this->oProp->sClassName}", $this );
            $this->oProp->_bSetupLoaded = true;
            return;
        }
        
        // If it is a pre callback method, call the redirecting method.     
        if ( substr( $sMethodName, 0, strlen( 'section_pre_' ) ) == 'section_pre_' ) return $this->_renderSectionDescription( $sMethodName );  // add_settings_section() callback - defined in AdminPageFramework_Setting
        if ( substr( $sMethodName, 0, strlen( 'field_pre_' ) ) == 'field_pre_' ) return $this->_renderSettingField( $aArgs[ 0 ], $sPageSlug );  // add_settings_field() callback - defined in AdminPageFramework_Setting
        if ( substr( $sMethodName, 0, strlen( 'load_pre_' ) ) == 'load_pre_' ) {
            
            return substr( $sMethodName, strlen( 'load_pre_' ) ) === $sPageSlug
                ? $this->_doPageLoadCall( $sPageSlug, $sTabSlug, $aArgs[ 0 ] )  // load-{page} callback
                : null;

        }

        // The callback of the call_page_{page slug} action hook
        if ( $sMethodName == $this->oProp->sClassHash . '_page_' . $sPageSlug ) {
            return $this->_renderPage( $sPageSlug, $sTabSlug ); // the method is defined in the AdminPageFramework_Page class.
        }

        if ( has_filter( $sMethodName ) ) {
            return isset( $aArgs[ 0 ] ) ? $aArgs[ 0 ] : null;
        }
                        
        trigger_error( 'Admin Page Framework: ' . ' : ' . sprintf( __( 'The method is not defined: %1$s', $this->oProp->sTextDomain ), $sMethodName ), E_USER_WARNING );
        
    }    
 
        /**
         * Redirects the callback of the load-{page} action hook to the framework's callback.
         * 
         * @since       2.1.0
         * @since       3.3.1       Moved from `AdminPageFramework_Base`.
         * @access      protected
         * @internal
         * @remark      This method will be triggered before the header gets sent.
         * @return      void
         * @internal
         */ 
        protected function _doPageLoadCall( $sPageSlug, $sTabSlug, $oScreen ) {
            
            if ( ! isset( $this->oProp->aPageHooks[ $sPageSlug ] ) || $oScreen->id !== $this->oProp->aPageHooks[ $sPageSlug ] ) {
                return;
            }
        
            // [3.4.6+] Set the page and tab slugs to the default form section so that added form fields without a section will appear in different pages and tabs.
            $this->oForm->aSections[ '_default' ]['page_slug']  = $sPageSlug ? $sPageSlug : null;
            $this->oForm->aSections[ '_default' ]['tab_slug']   = $sTabSlug ? $sTabSlug : null;
        
            // Do actions, class ->  page -> in-page tab
            $this->oUtil->addAndDoActions( 
                $this, // the caller object
                array( 
                    "load_{$this->oProp->sClassName}",
                    "load_{$sPageSlug}",
                ),
                $this // the admin page object - this lets third-party scripts use the framework methods.
            );
            
            // It is possible that an in-page tab is added during the above hooks and the current page is the default tab without the tab GET query key in the url. 
            $this->_finalizeInPageTabs();
            $this->oUtil->addAndDoActions( 
                $this, // the caller object
                array( "load_{$sPageSlug}_" . $this->oProp->getCurrentTab() ),
                $this // the admin page object - this lets third-party scripts use the framework methods.
            );         
            
            $this->oUtil->addAndDoActions( 
                $this, // the caller object
                array( "load_after_{$this->oProp->sClassName}" ),
                $this // the admin page object - this lets third-party scripts use the framework methods.
            );
            
        }
        
    /* Shared methods */
    /**
     * Calculates the subtraction of two values with the array key of `order`.
     * 
     * This is used to sort arrays.
     * 
     * @since       2.0.0
     * @since       3.0.0       Moved from the property class.
     * @since       3.3.1       Moved from `AdminPageFramework_Base`.
     * @remark      a callback method for `uasort()`.
     * @return      integer
     * @internal
     */ 
    public function _sortByOrder( $a, $b ) {
        return isset( $a['order'], $b['order'] )
            ? $a['order'] - $b['order']
            : 1;
    }    

    
    /**
     * Checks whether the class should be instantiated.
     * 
     * @since       3.1.0
     * @since       3.3.1       Moved from `AdminPageFramework_Base`.
     * @internal
     */
    protected function _isInstantiatable() {
        
        // Disable in admin-ajax.php
        if ( isset( $GLOBALS['pagenow'] ) && 'admin-ajax.php' === $GLOBALS['pagenow'] ) {
            return false;
        }
        
        // Nothing to do in the network admin area.
        return ! is_network_admin();
        
    }
    
    /**
     * Checks whether the currently loading page is of the given pages. 
     * 
     * @since       3.0.2
     * @since       3.2.0       Changed the scope to public from protected as the head tag object will access it.
     * @since       3.3.1       Moved from `AdminPageFramework_Base`.
     * @internal
     */
    public function _isInThePage( $aPageSlugs=array() ) {

        // Maybe called too early
        if ( ! isset( $this->oProp ) ) {
            return true;
        }
        
        // If the setUp method is not loaded yet,
        if ( ! $this->oProp->_bSetupLoaded ) {
            return true;
        }    

        if ( ! isset( $_GET['page'] ) ) { return false; }
                
        $_oScreen = get_current_screen();
        if ( is_object( $_oScreen ) ) {
            return in_array( $_oScreen->id, $this->oProp->aPageHooks );
        }
                
        if ( empty( $aPageSlugs ) ) {
            return $this->oProp->isPageAdded();
        }
                
        return in_array( $_GET['page'], $aPageSlugs );
        
    }    
    
}