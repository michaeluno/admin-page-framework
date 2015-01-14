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

class AdminPageFrameworkLoader_AdminPageWelcome extends AdminPageFramework {
        
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
                'title'         => AdminPageFrameworkLoader_Registry::Name,
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
                'welcome'           // tab slug
            );        
            new AdminPageFrameworkLoader_AdminPageWelcome_Guide(
                $this,        
                $_sPageSlug,       
                'guide'            
            );         
            new AdminPageFrameworkLoader_AdminPageWelcome_ChangeLog(
                $this,          
                $_sPageSlug,    
                'change_log'    
            );
            new AdminPageFrameworkLoader_AdminPageWelcome_Credit(
                $this,        
                $_sPageSlug,  
                'credit'      
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
        
        $_sBadgeURL = esc_url( AdminPageFrameworkLoader_Registry::getPluginURL( 'asset/image/icon-128x128.png' ) );
        return $sCSSRules
            . ".apf-badge {
                    background: url('{$_sBadgeURL}') no-repeat;
                }            
            ";
        
    }
        
        
    /**
     * Filters the top part of the page content.
     * 
     * @remark      A callback of the "content_top_{page slug}" filter hook.
     */
    public function replyToFilterContentTop( $sContent ) {

        $_sVersion      = '- ' . AdminPageFramework_Registry::Version;
        $_sPluginName   = AdminPageFramework_Registry::Name . ' ' . $_sVersion;
        
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