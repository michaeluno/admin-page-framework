<?php
/**
 * Admin Page Framework - Loader
 * 
 * Loads Admin Page Framework.
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno
 * 
 */
 
 
class AdminPageFrameworkLoader_AdminPageMetaBox_ExternalLinks extends AdminPageFramework_PageMetaBox {
        
    /*
     * ( optional ) Use the setUp() method to define settings of this meta box.
     */
    public function setUp() {}
    
    /**
     * The content filter callback method.
     * 
     * Alternatively use the `content_{instantiated class name}` method instead.
     */
    public function content( $sContent ) {
        
        $_aReplacements   = array(
            '%PLUGIN_DIR_URL%'  => AdminPageFrameworkLoader_Registry::getPluginURL(),
            '%WP_ADMIN_URL%'    => admin_url(),
        );
        $_oWPReadmeParser = new AdminPageFramework_WPReadmeParser( 
            AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/text/about.txt',
            $_aReplacements
        );
        return ''
            . "<h4>" 
                . "<span class='header-icon dashicons dashicons-book'></span>"
                . __( 'Documentation', 'admin-page-framework-loader' ) 
            . "</h4>"
            . "<a href='http://admin-page-framework.michaeluno.jp/en/v3/package-AdminPageFramework.html' target='_blank'>"
                . __( 'Manual', 'admin-page-framework-loader' ) 
            . "</a>"
            . "<h4>" 
                . "<span class='header-icon dashicons dashicons-book'></span>"
                . __( 'Tutorials', 'admin-page-framework-loader' ) 
            . "</h4>"
            . $_oWPReadmeParser->getSection( 'Tutorials' ) 
            . $sContent;
        
    }
 
}
