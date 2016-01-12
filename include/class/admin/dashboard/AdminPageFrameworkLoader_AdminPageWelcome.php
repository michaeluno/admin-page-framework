<?php
/**
 * One of the abstract class of the plugin admin page class.
 * 
 * @package      Admin Page Framework Loader
 * @copyright    Copyright (c) 2014, Michael Uno
 * @author       Michael Uno
 * @authorurl    http://michaeluno.jp
 * @since        3.5.0
 */

/**
 * Constructs the Welcome admin page
 * 
 * @filter      apply       admin_page_framework_loader_filter_admin_welcome_redirect_url        Applies to the redirecting welcome url. Use this filter to disable the redirection upon plugin installation.
 * 
 * @since       3.5.0
 */
class AdminPageFrameworkLoader_AdminPageWelcome extends AdminPageFramework {
    
    /**
     * User constructor.
     * 
     * @since       3.5.0
     */
    public function start() {
        
        if ( ! is_admin() ) {
            return;
        }
        
        add_action( 'init', array( $this, '_replyToHandleRedirects' ) );
        
    }
        /**
         * Handles page redirects.
         * 
         * This is called to prevent the plugin from performing the redirect when the plugin is not activated or intervene the activation process.
         * If this is called in the start() method above, it will redirect the user to the page during the activation process 
         * and the user gets a page that is not created because the plugin is not activated.
         * 
         * @callback    action      init
         * @since       3.5.0
         * @sicne       3.5.3       Change the hook from `admin_init` as the resetting option results in an error 'You do not have permission to access this page.'
         */
        public function _replyToHandleRedirects() {

            // When newly installed, the 'welcomed' value is not set.
            $_oOption = AdminPageFrameworkLoader_Option::getInstance();
            if ( ! $_oOption->get( 'welcomed' ) ) {                
                $this->_setInitialOptions( $_oOption, AdminPageFrameworkLoader_Registry::VERSION );
                $this->_goToWelcomePage(); // will exit
            }
            if ( $_oOption->hasUpgraded() ) {
                $this->_setInitialOptions( $_oOption, $_oOption->get( 'version_saved' ) );
                $this->_goToWelcomePage(); // will exit
            }            
            
        }
            /**
             * 
             * @return void
             */
            private function _setInitialOptions( $oOption, $sVersionUpgradedFrom ) {
                
                $oOption->set( 'welcomed', true );
                $oOption->set( 'version_upgraded_from', $sVersionUpgradedFrom );
                $oOption->set( 'version_saved', AdminPageFrameworkLoader_Registry::VERSION );
                $oOption->save();                
                
            }
            
        private function _goToWelcomePage() {    
        
            $_sWelcomePageURL = apply_filters(
                AdminPageFrameworkLoader_Registry::HOOK_SLUG . '_filter_admin_welcome_redirect_url',
                add_query_arg( 
                    array( 'page' => AdminPageFrameworkLoader_Registry::$aAdminPages[ 'about' ] ),
                    admin_url( 'index.php' )   // Dashboard
                )                
            );
            
            $this->oUtil->goToLocalURL( $_sWelcomePageURL );
            
        }
    
    /**
     * Sets up admin pages.
     * 
     * @since       3.5.0
     * @callback    action      wp_loaded
     */
    public function setUp() {
  
        $_oOption = AdminPageFrameworkLoader_Option::getInstance();
        if ( ! $_oOption->get( 'enable_admin_pages' ) ) {
            return;
        }
        
        $this->sPageSlug  = AdminPageFrameworkLoader_Registry::$aAdminPages[ 'about' ];
        
        // Root page
        $this->setRootMenuPage( 
            'Dashboard'     // menu slug
        ); 

        // Sub-pages
        $this->addSubMenuItems( 
            array(
                'title'         => AdminPageFrameworkLoader_Registry::SHORTNAME,
                'page_slug'     => AdminPageFrameworkLoader_Registry::$aAdminPages[ 'about' ],    // page slug
                'show_in_menu'  => false,
                'style'         => array(
                    AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/about.css', 
                    AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/column.css', 
                    AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/javascript/flip/jquery.m.flip.css',
                    version_compare( $GLOBALS[ 'wp_version' ], '3.8', '<' )
                        ? ".about-wrap .introduction h2 {
                                padding: 1em;
                            }"
                            
                        : "",
                        ".admin-page-framework-section-tab h4 {
                            padding: 6px 16px 8px;
                            font-size: 1.2em;
                            font-weight: 400;
                        }",     
                ),
                'script'        => array(
                    AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/javascript/flip/jquery.m.flip.js',
                    "jQuery( document ).ready( function() {
                        jQuery( '.apf-badge-image' ).mflip();
                    } );",
                ),
            )
        );

        $this->setPluginSettingsLinkLabel( '' ); // pass an empty string to disable it.
        
        // Hook
        add_action( "load_" . $this->oProp->sClassName, array( $this, 'replyToLoadClassPages' ) );
        add_action( "load_" . AdminPageFrameworkLoader_Registry::$aAdminPages[ 'about' ], array( $this, 'replyToLoadPage' ) );

    }   
    
    /**
     * Set up page settings.
     */
    public function replyToLoadClassPages( $oFactory ) {

        $this->setPageHeadingTabsVisibility( false ); // disables the page heading tabs by passing false.
        $this->setInPageTabTag( 'h2' ); // sets the tag used for in-page tabs     
        $this->setPageTitleVisibility( false ); // disable the page title of a specific page.
    
    }
        
    /**
     * Triggered when the page loads.
     * 
     * Adds tabs.
     * @callback    action      load_{page slug}
     * @return      void
     */
    public function replyToLoadPage( $oFactory ) {

        $_sPageSlug = AdminPageFrameworkLoader_Registry::$aAdminPages[ 'about' ];
        new AdminPageFrameworkLoader_AdminPageWelcome_Welcome( 
            $this,              // factory object
            $_sPageSlug,        // page slug
            array(
                'tab_slug'      => 'welcome',
                // 'title'         => __( "What's New", 'admin-page-framework-loader' ),   // '
                'style'         => array(
                    AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/admin.css',
                    AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/code.css',
                ),
            )                
        );        

        $this->_setPreferences( $oFactory );
        
    }
        /**
         * Gets triggered when the page loads.
         */
        private function _setPreferences( $oFactory ) {

            $this->oProp->sWrapperClassAttribute = "wrap about-wrap";
            
            $oFactory->setInPageTabsVisibility( false );
            
            add_filter( "content_top_{$this->sPageSlug}", array( $this, 'replyToFilterContentTop' ) );
                       
        } 
                       
    /**
     * Filters the top part of the page content.
     * 
     * @callback    filter      content_top_{page slug}
     * @return      string
     */
    public function replyToFilterContentTop( $sContent ) {

        $_sVersion      = '- ' . AdminPageFrameworkLoader_Registry::VERSION;
        $_sPluginName   = AdminPageFrameworkLoader_Registry::SHORTNAME . ' ' . $_sVersion;
        
        $_sBadgeURL     = esc_url( AdminPageFrameworkLoader_Registry::getPluginURL( 'asset/image/icon-128x128.png' ) );
        
        $_aOutput   = array();
        $_aOutput[] = "<h1>" 
                . sprintf( __( 'Welcome to %1$s', 'admin-page-framework-loader' ), $_sPluginName )
            . "</h1>";
        $_aOutput[] = "<div class='about-text'>"
                . sprintf( __( 'Thank you for updating to the latest version! %1$s is ready to make your plugin or theme development faster, more organized and better!', 'admin-page-framework-loader' ), $_sPluginName )
            . "</div>";
        $_aOutput[] = ''
                . "<div class='apf-badge'>"
                    . "<div class='apf-badge-image m-flip'>"
                        . "<img src='{$_sBadgeURL}' />"
                    . "</div>"
                    . "<span class='label'>" . sprintf( __( 'Version %1$s', 'admin-page-framework-loader' ), $_sVersion ) . "</span>"
                . "</div>";
           
        return implode( PHP_EOL, $_aOutput )
            . $sContent;
    }
 
}
