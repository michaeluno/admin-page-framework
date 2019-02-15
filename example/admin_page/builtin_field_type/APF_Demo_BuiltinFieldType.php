<?php
/**
 * Admin Page Framework Loader
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds a page to the loader plugin.
 * 
 * @since       3.6.2
 * @package     AdminPageFramework/Example
 */
class APF_Demo_BuiltinFieldType {

    private $_sClassName = 'APF_Demo';

    private $_sPageSlug  = 'apf_builtin_field_types';

    /**
     * Adds a page item and sets up hooks.
     */
    public function __construct( $sClassName='' ) {
        
        $this->_sClassName = $sClassName ? $sClassName : $this->_sClassName;
        
        add_action(
            'set_up_' . $this->_sClassName,
            array( $this, 'replyToSetUp' )
        );
        
    }
    
    /**
     * @callback        action      set_up_{instantiated class name}
     */
    public function replyToSetUp( $oFactory ) {
        
        /**
         * ( required ) Add sub-menu items (pages or links) 
         */
        $oFactory->addSubMenuItems(
            /**     
             * Examples:
             * for sub-menu pages, e.g.
             *  'title'         => 'Your Page Title',
             *  'page_slug'     => 'your_page_slug', // avoid hyphen(dash), dots, and white spaces
             *  'screen_icon'   => 'edit', // for WordPress v3.7.x or below
             *  'capability'    => 'manage-options',
             *  'order'         => 10,
             *  
             * for sub-menu links, e.g.
             *  'title'         => 'Google',
             *  'href'          => 'http://www.google.com',
             *  
             */
            array(
                'title'         => __( 'Built-in Field Types', 'admin-page-framework-loader' ),
                'page_slug'     => $this->_sPageSlug,
                
                /**
                 * (optional) One of the screen type from the below can be used.
                 * 
                 * Screen Types (for WordPress v3.7.x or below) :
                 *  'edit', 'post', 'index', 'media', 'upload', 'link-manager', 'link', 'link-category', 
                 *  'edit-pages', 'page', 'edit-comments', 'themes', 'plugins', 'users', 'profile', 
                 *  'user-edit', 'tools', 'admin', 'options-general', 'ms-admin', 'generic',  
                 */
                'screen_icon'   => 'options-general',
                
                /**
                 * (optional) The order of the page.
                 * If you don't set this, an index will be assigned internally in the added order
                 */
                'order'         => 10, 
                
                /**
                 * (optional) Page resources.
                 * 
                 * For scripts, use the `script` argument.
                 * ```
                 * 'script' => $sDirPath . '/asset/js/my-script.js',
                 * ```
                 */
                'style'         => array(
                    AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/code.css',
                    AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/field_example.css',
                ),                
            )
        );        
           
        add_action( 'load_' . $this->_sPageSlug, array( $this, 'replyToLoadPage' ) );
        
    }
    
    /**
     * @return      void
     * @callback    action      load_{page slug}
     */
    public function replyToLoadPage( $oFactory ) {
        
        /**
         * (optional) Contextual help pane
         */
        $oFactory->addHelpTab( 
            array(
                'page_slug'                => $this->_sPageSlug, // ( required )
                'help_tab_id'              => 'admin_page_framework', // ( required )
                // 'page_tab_slug' => null, // ( optional )
                'help_tab_title'           => AdminPageFramework_Registry::NAME,
                'help_tab_content'         => __( 'This contextual help text can be set with the <code>addHelpTab()</code> method.', 'admin-page-framework' ),
                'help_tab_sidebar_content' => __( 'This is placed in the sidebar of the help pane.', 'admin-page-framework' ),
            )
        );        
        
        /**
         * (optional) Add in-page tabs - here tabs are defined in the below classes.
         */
        $_aTabClasses = array(
            'APF_Demo_BuiltinFieldTypes_Text',
            'APF_Demo_BuiltinFieldTypes_Selector',
            'APF_Demo_BuiltinFieldTypes_File',
            'APF_Demo_BuiltinFieldTypes_Checklist',
            'APF_Demo_BuiltinFieldTypes_MISC',
            'APF_Demo_BuiltinFieldTypes_System', 
        );
        foreach ( $_aTabClasses as $_sTabClassName ) {
            if ( ! class_exists( $_sTabClassName ) ) {
                continue;                
            }        
            new $_sTabClassName( $oFactory );
        }
        
    }

}
