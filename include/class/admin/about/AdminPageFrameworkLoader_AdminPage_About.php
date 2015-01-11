<?php
/**
 * Admin Page Framework Loader
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds the About page to the loader plugin.
 * 
 * @since       3.5.0       
 */
class AdminPageFrameworkLoader_AdminPage_About {

    public function __construct( $oFactory, $sPageSlug, $sTitle ) {
    
        $this->oFactory     = $oFactory;
        $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = $sPageSlug;
        $this->sPageTitle   = $sTitle;
    
        $this->_addPage();
                
        add_action( "load_{$this->sPageSlug}", array( $this, 'replyToLoadPage' ) );
        
    }
        
        /**
         * Adds an admin page.
         */
        private function _addPage() {
            
            $this->oFactory->addSubMenuItems( 
                array(
                    'title'         => $this->sPageTitle,
                    'page_slug'     => $this->sPageSlug,    // page slug
                )
            );

            // Tabs
            new AdminPageFrameworkLoader_AdminPage_About_Welcome( 
                $this->oFactory,        // factory object
                $this->sPageSlug,       // page slug
                'welcome'                  // tab slug
            );        
            new AdminPageFrameworkLoader_AdminPage_About_Guide(
                $this->oFactory,        // factory object
                $this->sPageSlug,       // page slug
                'guide'                 // tab slug            
            );         
            new AdminPageFrameworkLoader_AdminPage_About_ChangeLog(
                $this->oFactory,        // factory object
                $this->sPageSlug,       // page slug
                'change_log'            // tab slug                        
            );
            new AdminPageFrameworkLoader_AdminPage_About_Credit(
                $this->oFactory,        // factory object
                $this->sPageSlug,       // page slug
                'credit'                // tab slug            
            );     
            
        }
        
    /**
     * Gets triggered when the page loads.
     * 
     * @remark      A callback of the "load_{page slug}" action hook.
     */
    public function replyToLoadPage( $oFactory ) {

        $oFactory->oProp->sWrapperClassAttribute = "wrap about-wrap";
    
        add_filter( "content_top_{$this->sPageSlug}", array( $this, 'replyToFilterContentTop' ) );
        add_action( "style_{$this->sPageSlug}", array( $this, 'replyToAddInlineCSS' ) );
    
        $oFactory->enqueueStyle( 
            AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/about.css', 
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
