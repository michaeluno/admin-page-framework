<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
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
 * @method          _renderPage( $_sPageSlug, $_sTabSlug )      
 * @method          _renderSectionDescription( $sMethodName )           defined in AdminPageFramework_Setting
 * @method          _renderSettingField( $_mFirstArg, $_sPageSlug )     defined in AdminPageFramework_Setting
 */
abstract class AdminPageFramework_Router extends AdminPageFramework_Factory {
    
    /**'
     * Sets up hooks and properties.
     * 
     * @since       3.3.0
     */
    public function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {

        $this->oProp = isset( $this->oProp ) 
            ? $this->oProp // for the AdminPageFramework_NetworkAdmin class
            : new AdminPageFramework_Property_admin_page( 
                $this, 
                $sCallerPath, 
                get_class( $this ), 
                $sOptionKey, 
                $sCapability, 
                $sTextDomain 
            );

        parent::__construct( $this->oProp );

        if ( $this->oProp->bIsAdminAjax ) {
            return;
        }     
        if ( ! $this->oProp->bIsAdmin ) {
            return;
        }
        
        add_action( 'wp_loaded', array( $this, '_replyToDetermineToLoad' ) );        
        
    }   
    
    /**
     * Instantiates a link object based on the type.
     * 
     * @since       3.7.10
     * @internal
     * @return      null|object
     */
    protected function _getLinkObject() {
        return new AdminPageFramework_Link_admin_page( $this->oProp, $this->oMsg );
    }    
    
    /**
     * Instantiates a link object based on the type.
     * 
     * @since       3.7.10
     * @internal
     * @return      null|object
     */    
    protected function _getPageLoadObject() {
        return new AdminPageFramework_PageLoadInfo_admin_page( $this->oProp, $this->oMsg );
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

        $_sPageSlug             = $this->oProp->getCurrentPageSlug();
        $_sTabSlug              = $this->oProp->getCurrentTabSlug( $_sPageSlug );
        $_mFirstArg             = $this->oUtil->getElement( $aArgs, 0 );
        $_aKnownMethodPrefixes  = array(
            'section_pre_',
            'field_pre_',
            'load_pre_',
        );        
        
        switch( $this->_getCallbackName( $sMethodName, $_sPageSlug, $_aKnownMethodPrefixes ) ) {
                
            // A callback of the call_page_{page slug} action hook
            // @deprecated  3.7.10
            // case $this->oProp->sClassHash . '_page_' . $_sPageSlug:
                // return $this->_renderPage( $_sPageSlug, $_sTabSlug );   // defined in AdminPageFramework_Page.
             
            // add_settings_section() callback 
            case 'section_pre_':
                return $this->_renderSectionDescription( $sMethodName );    // defined in AdminPageFramework_Setting
                
            // add_settings_field() callback - 
            case 'field_pre_':
                return $this->_renderSettingField( $_mFirstArg, $_sPageSlug );  // defined in AdminPageFramework_Setting
            
            // load-{page} callback            
            case 'load_pre_':
                return $this->_doPageLoadCall( $sMethodName, $_sPageSlug, $_sTabSlug, $_mFirstArg );
            
            default:
                return parent::__call( $sMethodName, $aArgs );
        }        
        
    }    
        /**
         * Attempts to find the factory class callback method for the given method name.
         * 
         * @since       3.5.3
         * @return      string      The found callback method name or the prefix of a known callback method name. An empty string if not found.
         */
        private function _getCallbackName( $sMethodName, $sPageSlug, array $aKnownMethodPrefixes=array() ) {
            
            // @deprecated      3.7.10
            // if ( in_array( 
                    // $sMethodName, 
                    // array( 
                        // $this->oProp->sClassHash . '_page_' . $sPageSlug
                    // ) 
                // ) 
            // ) {
                // return $sMethodName;
            // }
            
            foreach( $aKnownMethodPrefixes as $_sMethodPrefix ) {
                if ( $this->oUtil->hasPrefix( $_sMethodPrefix, $sMethodName ) ) {
                    return $_sMethodPrefix;
                }
            }
            return '';
            
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
            
            if ( ! $this->_isPageLoadCall( $sMethodName, $sPageSlug, $oScreen->id ) ) {
                return;
            }

            // [3.4.6+] Set the page and tab slugs to the default form section so that added form fields without a section will appear in different pages and tabs.
            $this->oForm->aSections[ '_default' ][ 'page_slug' ]  = $sPageSlug ? $sPageSlug : null;
            $this->oForm->aSections[ '_default' ][ 'tab_slug' ]   = $sTabSlug ? $sTabSlug : null;
        
            // Do actions, class ->  page -> in-page tab
            $this->oUtil->addAndDoActions( 
                $this, // the caller object
                array( 
                    "load_{$this->oProp->sClassName}",
                    "load_{$sPageSlug}",
                ),
                $this // the admin page object - this lets third-party scripts use the framework methods.
            );
            
            // The in-page tabs handling method `_replyToFinalizeInPageTabs()` is called in the above action hook.
            
            $this->oUtil->addAndDoActions( 
                $this, // the caller object
                array( "load_{$sPageSlug}_" . $this->oProp->getCurrentTabSlug( $sPageSlug ) ),
                $this // the admin page object - this lets third-party scripts use the framework methods.
            );         
            
            $this->oUtil->addAndDoActions( 
                $this, // the caller object
                array( 
                    "load_after_{$this->oProp->sClassName}",
                    "load_after_{$sPageSlug}", // 3.6.3+
                ),
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
            private function _isPageLoadCall( $sMethodName, $sPageSlug, $sScreenID ) {
                
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
     * Checks whether the class should be instantiated.
     * 
     * @since       3.1.0
     * @since       3.3.1       Moved from `AdminPageFramework_Base`.
     * @internal
     */
    protected function _isInstantiatable() {
        
        // Disable in admin-ajax.php
        if ( isset( $GLOBALS[ 'pagenow' ] ) && 'admin-ajax.php' === $GLOBALS[ 'pagenow' ] ) {
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
    public function _isInThePage() {
        
        // If the setUp method is not loaded yet,
        if ( ! did_action( 'set_up_' . $this->oProp->sClassName ) ) {
            return true;
        }    

        if ( ! isset( $_GET[ 'page' ] ) ) { 
            return false; 
        }        
        
        return $this->oProp->isPageAdded();
        
    }
    
    /**
     * Determines whether the class component classes should be instantiated or not.
     * 
     * @internal
     * @callback    action      current_screen
     * @return      void
     * @since       3.7.0
     */
    public function _replyToLoadComponents( /* $oScreen */ ) {

        if ( 'plugins.php' === $this->oProp->sPageNow ) {
            $this->oLink = $this->_replyTpSetAndGetInstance_oLink();
        }
        parent::_replyToLoadComponents();
        
    }    
    
}
