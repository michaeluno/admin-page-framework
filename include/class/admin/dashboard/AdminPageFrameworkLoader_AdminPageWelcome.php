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
        
        add_action( 'admin_menu', array( $this, '_replyToHandleRedirects' ) );
        
    }
        /**
         * Handles page redirects.
         * 
         * This is called to prevent the plugin from performing the redirect when the plugin is not activated or intervene the activation process.
         * If this is called in the start() method above, it will redirect the user to the page during the activation process 
         * and the user gets a page that is not created because the plugin is not activated.
         * 
         * @callback    action      admin_menu
         * @since       3.5.0
         * @sicne       3.5.3       Change the hook from `admin_init` as the resetting option results in an error 'You do not have permission to access this page.'
         */
        public function _replyToHandleRedirects() {
                
            $_oOption = AdminPageFrameworkLoader_Option::getInstance();
                    
            // When newly installed, the 'welcomed' value is not set.
            if ( ! $_oOption->get( 'welcomed' ) ) {
                
                $_oOption->set( 'welcomed', true );
                $_oOption->set( 'version_upgraded_from', AdminPageFrameworkLoader_Registry::Version );
                $_oOption->set( 'version_saved', AdminPageFrameworkLoader_Registry::Version );
                $_oOption->save();
                $this->_gotToWelcomePage();
                
            }
            if ( $_oOption->hasUpgraded() ) {

                $_oOption->set( 'welcomed', true );
                $_oOption->set( 'version_upgraded_from', $_oOption->get( 'version_saved' ) );
                $_oOption->set( 'version_saved', AdminPageFrameworkLoader_Registry::Version );
                $_oOption->save();
                $this->_gotToWelcomePage();
                
            }            
            
        }
        private function _gotToWelcomePage() {        
            $_sWelcomePageURL = apply_filters( 
                'admin_page_framework_loader_filter_admin_welcome_redirect_url',
                    add_query_arg( 
                    array( 'page' => AdminPageFrameworkLoader_Registry::$aAdminPages['about'] ),
                    admin_url( 'index.php' )   // Dashboard
                )                
            );
            exit( wp_safe_redirect( $_sWelcomePageURL ) );
        }
    
    /**
     * Sets up admin pages.
     * 
     * @since       3.5.0
     */
    public function setUp() {
        
        $this->sPageSlug  = AdminPageFrameworkLoader_Registry::$aAdminPages['about'];
        
        // Root page
        $this->setRootMenuPage( 
            'Dashboard'     // menu slug
        ); 
        
        // Sub-pages
        $this->addSubMenuItems( 
            array(
                'title'         => AdminPageFrameworkLoader_Registry::ShortName,
                'page_slug'     => AdminPageFrameworkLoader_Registry::$aAdminPages['about'],    // page slug
                'show_in_menu'  => false,
            )
        );

        // Page Settings
        $this->setPageHeadingTabsVisibility( false ); // disables the page heading tabs by passing false.
        $this->setInPageTabTag( 'h2' ); // sets the tag used for in-page tabs     
        $this->setPageTitleVisibility( false ); // disable the page title of a specific page.
        $this->setPluginSettingsLinkLabel( '' ); // pass an empty string to disable it.
           
        // Styles
        $this->enqueueStyle( AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/code.css' );
        $this->enqueueStyle( AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/admin.css' );
              
        // Hook
        add_action( "load_" . AdminPageFrameworkLoader_Registry::$aAdminPages['about'], array( $this, 'replyToLoadPage' ) );
        
    }   
        
        /**
         * Triggered when the page loads.
         * 
         * Adds tabs.
         */
        public function replyToLoadPage( $oFactory ) {
            
            $_sPageSlug = AdminPageFrameworkLoader_Registry::$aAdminPages['about'];
            new AdminPageFrameworkLoader_AdminPageWelcome_Welcome( 
                $this,              // factory object
                $_sPageSlug,        // page slug
                array(
                    'tab_slug'      => 'welcome',
                    'title'         => __( "What's New", 'admin-page-framework-loader' ),   // '
                )                
            );        
            new AdminPageFrameworkLoader_AdminPageWelcome_Guide(
                $this,        
                $_sPageSlug,                       
                array(
                    'tab_slug'      => 'guide',
                    'title'         => __( 'Getting Started', 'admin-page-framework-loader' ),
                )                
            );         
            new AdminPageFrameworkLoader_AdminPageWelcome_ChangeLog(
                $this,          
                $_sPageSlug,   
                array(
                    'tab_slug'      => 'change_log',
                    'title'         => __( 'Change Log', 'admin-page-framework-loader' ),
                )
            );
            new AdminPageFrameworkLoader_AdminPageWelcome_Credit(
                $this,        
                $_sPageSlug,  
                array(
                    'tab_slug'      => 'credit',
                    'title'         => __( 'Credit', 'admin-page-framework-loader' ),
                )                
            );                 
            
            $this->_setPreferences();
            
        }
            /**
             * Gets triggered when the page loads.
             * 
             * @remark      A callback of the "load_{page slug}" action hook.
             */
            private function _setPreferences() {

                $this->oProp->sWrapperClassAttribute = "wrap about-wrap";
            
                add_filter( "content_top_{$this->sPageSlug}", array( $this, 'replyToFilterContentTop' ) );
                add_action( "style_{$this->sPageSlug}", array( $this, 'replyToAddInlineCSS' ) );
            
                $this->enqueueStyle( 
                    AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/about.css', 
                    $this->sPageSlug 
                );
                $this->enqueueStyle( 
                    AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/column.css', 
                    $this->sPageSlug 
                );                
                
            } 
    
    /**
     * Modifies the inline CSS rules of the page.
     * 
     * @remark      A callback method of the "style_{page slug}" filter hook.
     */
    public function replyToAddInlineCSS( $sCSSRules ) {
        
        $_sBadgeURL     = esc_url( AdminPageFrameworkLoader_Registry::getPluginURL( 'asset/image/icon-128x128.png' ) );
        
        $_sWP38OrBelow  = version_compare( $GLOBALS['wp_version'], '3.8', '<' )
            ? ".about-wrap .introduction h2 {
                    padding: 1em;
                }"
            : "";
        
        return $sCSSRules
            . ".apf-badge {
                    background: url('{$_sBadgeURL}') no-repeat;
                }            
            " . $_sWP38OrBelow;
        
    }
        
        
    /**
     * Filters the top part of the page content.
     * 
     * @remark      A callback of the "content_top_{page slug}" filter hook.
     */
    public function replyToFilterContentTop( $sContent ) {

        $_sVersion      = '- ' . AdminPageFrameworkLoader_Registry::Version;
        $_sPluginName   = AdminPageFrameworkLoader_Registry::ShortName . ' ' . $_sVersion;
        
        $_aOutput   = array();
        $_aOutput[] = "<h1>" 
                . sprintf( __( 'Welcome to %1$s', 'admin-page-framework-loader' ), $_sPluginName )
            . "</h1>";
        $_aOutput[] = "<div class='about-text'>"
                . sprintf( __( 'Thank you for updating to the latest version! %1$s is ready to make your plugin or theme development faster, more organized and better!', 'admin-page-framework-loader' ), $_sPluginName )
            . "</div>";
        $_aOutput[] = "<div class='apf-badge'>"
                . "<span class='label'>" . sprintf( __( 'Version %1$s', 'admin-page-framework-loader' ), $_sVersion ) . "</span>"
            . "</div>";		
            
        return implode( PHP_EOL, $_aOutput )
            . $sContent;
    }
 
}