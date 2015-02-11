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
     * Handles undefined function calls.
     * 
     * This method redirects callback-function calls with the pre-defined prefixes for hooks to the appropriate methods. 
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

        $_sPageSlug = isset( $_GET['page'] )
            ? $_GET['page'] 
            : null;    
        $_sTabSlug  = isset( $_GET['tab'] )
            ? $_GET['tab'] 
            : $this->oProp->getDefaultInPageTab( $_sPageSlug );
        $_mFirstArg = isset( $aArgs[ 0 ] ) ? $aArgs[ 0 ] : null;
        
        switch( $sMethodName ) {
            
            case 'setup_pre':
                $this->_doSetUp();
                return;
            
            // A callback of the call_page_{page slug} action hook
            case $this->oProp->sClassHash . '_page_' . $_sPageSlug:
                return $this->_renderPage( $_sPageSlug, $_sTabSlug );   // defined in AdminPageFramework_Page.
                
        }
               
        // If it is a pre callback method, call the redirecting method.     
        if ( $this->oUtil->hasPrefix( 'section_pre_', $sMethodName ) ) {
            // add_settings_section() callback - defined in AdminPageFramework_Setting
            return $this->_renderSectionDescription( $sMethodName );  
        } 
        else if ( $this->oUtil->hasPrefix( 'field_pre_', $sMethodName ) ) {
            // add_settings_field() callback - defined in AdminPageFramework_Setting
            return $this->_renderSettingField( $_mFirstArg, $_sPageSlug );  
        }
        else if ( $this->oUtil->hasPrefix( 'load_pre_', $sMethodName ) ) {
            // load-{page} callback
            return $this->_doPageLoadCall( $sMethodName, $_sPageSlug, $_sTabSlug, $_mFirstArg );
        }

        return parent::__call( $sMethodName, $aArgs );
        
    }    
        /**
         * Calls the setUp() method.
         * @since       3.5.3
         * @internal
         * @return      void
         * @todo        introduce "set_up_pre_{ class name }" action hook.
         */
        private function _doSetUp() {
            
            $this->_setUp();
            
            // This action hook must be called AFTER the _setUp() method as there are callback methods that hook into this hook and assumes required configurations have been made.
            $this->oUtil->addAndDoAction( 
                $this, 
                "set_up_{$this->oProp->sClassName}", 
                $this 
            );
            
            $this->oProp->_bSetupLoaded = true;            
            
        }
        
        /**
         * Redirects the callback of the load-{page} action hook to the framework's callback.
         * 
         * @since       2.1.0
         * @since       3.3.1       Moved from `AdminPageFramework_Base`.
         * @since       3.5.3       Added the $sMethodName parameter.
         * 
         * @access      protected
         * @internal
         * @remark      This method will be triggered before the header gets sent.
         * @return      void
         * @internal
         */ 
        protected function _doPageLoadCall( $sMethodName, $sPageSlug, $sTabSlug, $oScreen ) {
            
            if ( ! $this->isPageLoadCall( $sMethodName, $sPageSlug, $oScreen->id ) ) {
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
                array( "load_{$sPageSlug}_" . $this->oProp->getCurrentTab( $sPageSlug ) ),
                $this // the admin page object - this lets third-party scripts use the framework methods.
            );         
            
            $this->oUtil->addAndDoActions( 
                $this, // the caller object
                array( "load_after_{$this->oProp->sClassName}" ),
                $this // the admin page object - this lets third-party scripts use the framework methods.
            );
            
        }
            /**
             * Determines whether the function call is of a page load.
             * @since       3.5.3
             * @internal
             * @return      boolean     True if it is a page load call; othwrwise, false.
             * @param       string      $sMethodName        The undefined method name that is passed to the __call() overload method.
             * @param       string      $sPageSlug          The currently loading page slug.
             * @param       string      $sScreenID          The screen ID that the WordPress screen object gives.
             */
            private function isPageLoadCall( $sMethodName, $sPageSlug, $sScreenID ) {
                
                if ( substr( $sMethodName, strlen( 'load_pre_' ) ) !== $sPageSlug ) {
                    return false;
                }
                if ( ! isset( $this->oProp->aPageHooks[ $sPageSlug ] ) ) {
                    return false;
                }
                if ( $sScreenID !== $this->oProp->aPageHooks[ $sPageSlug ] ) {
                    return false;
                }
                return true;
                
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