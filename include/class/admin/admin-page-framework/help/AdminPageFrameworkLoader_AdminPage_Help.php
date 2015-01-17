<?php
/**
 * Admin Page Framework Loader
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds the Contact page to the loader plugin.
 * 
 * @since       3.5.0       
 */
class AdminPageFrameworkLoader_AdminPage_Help {

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
            new AdminPageFrameworkLoader_AdminPage_Help_Information(
                $this->oFactory,    // factory object
                $this->sPageSlug,   // page slug
                'information'       // tab slug 
            );     
            new AdminPageFrameworkLoader_AdminPage_Help_Guide(
                $this->oFactory,    
                $this->sPageSlug,   
                'guide'              
            );                 
            new AdminPageFrameworkLoader_AdminPage_Help_Tip(
                $this->oFactory,    
                $this->sPageSlug,   
                'tips'              
            );               
            new AdminPageFrameworkLoader_AdminPage_Help_FAQ(
                $this->oFactory,
                $this->sPageSlug, 
                'faq'              
            );      
            new AdminPageFrameworkLoader_AdminPage_Help_Example(
                $this->oFactory,
                $this->sPageSlug, 
                'examples'              
            );
            new AdminPageFrameworkLoader_AdminPage_Help_Report(
                $this->oFactory,
                $this->sPageSlug,  
                'report'          
            );      
            new AdminPageFrameworkLoader_AdminPage_Help_About(
                $this->oFactory,
                $this->sPageSlug, 
                'about'            
            );     
            new AdminPageFrameworkLoader_AdminPage_Help_Debug(
                $this->oFactory,
                $this->sPageSlug,             
                'debug'
            );            
            
        }
        
    /**
     * Gets triggered when the page loads.
     * 
     * @remark      A callback of the "load_{page slug}" action hook.
     */
    public function replyToLoadPage( $oFactory ) {

        // add_filter( "content_top_{$this->sPageSlug}", array( $this, 'replyToFilterContentTop' ) );
        // add_action( "style_{$this->sPageSlug}", array( $this, 'replyToAddInlineCSS' ) );
        add_action( "do_after_{$this->sPageSlug}", array( $this, 'replyToDoAfterPage' ) );    
        
        $oFactory->enqueueStyle( 
            AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/help.css', 
            $this->sPageSlug
        );
        
    } 
    
    /**
     * Output custom page contents.
     */
    public function replyToDoAfterPage() {
        
        
        $_aReplacements   = array(
            '%PLUGIN_DIR_URL%'  => AdminPageFrameworkLoader_Registry::getPluginURL(),
            '%WP_ADMIN_URL%'    => admin_url(),
        );
        $_oWPReadmeParser = new AdminPageFramework_WPReadmeParser( 
            AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/text/about.txt',
            $_aReplacements
        );    
        echo "<h3>" . __( 'Tutorials', 'admin-page-framework-loader' ) . "</h3>"
            . $_oWPReadmeParser->getSection( 'Tutorials' );    
        
        
    }
        
}
