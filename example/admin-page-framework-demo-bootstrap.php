<?php
/**
 * Loads the demo components.
 * 
 * @package      Admin Page Framework Loader
 * @copyright    Copyright (c) 2014-2015, Michael Uno
 * @author       Michael Uno
 * @authorurl    http://michaeluno.jp
 */

/**
 * 
 * @since      DEVVER
 */
class AdminPageFrameworkLoader_Demo_Bootstrap {
    
    public function __construct() {

        $this->_registerClasses();
        
        $this->_loadCustomPostType();
        
        $this->_loadPostMetaBoxes();
                
        $this->_loadTermMeta();
        
        $this->_loadAdminPaeges();
        
        $this->_loadWidgets();
        
        $this->_loadUserMeta();

    }
    
        private function _loadCustomPostType() {
            
            // Custom post type
            include( AdminPageFrameworkLoader_Registry::$sDirPath . '/example/post_type/APF_PostType.php' );
                         
        }
        
        /**
         * Post meta boxes
         */
        private function _loadPostMetaBoxes() {
       
            include( AdminPageFrameworkLoader_Registry::$sDirPath . '/example/post_meta_box/APF_MetaBox_BuiltinFieldTypes.php' );
            
            // Section Tabs
            include( AdminPageFrameworkLoader_Registry::$sDirPath . '/example/post_meta_box/APF_MetaBox_TabbedSections.php' );

            // Repeatable Tabbed Sections
            include( AdminPageFrameworkLoader_Registry::$sDirPath . '/example/post_meta_box/APF_MetaBox_RepeatableTabbedSections.php' );

            // Collapsible Sections
            include( AdminPageFrameworkLoader_Registry::$sDirPath . '/example/post_meta_box/APF_MetaBox_CollapsibleSections.php' );
  
            // Repeatable Collapsible Sections
            include( AdminPageFrameworkLoader_Registry::$sDirPath . '/example/post_meta_box/APF_MetaBox_RepeatableCollapsibleSections.php' );

            // For debugging
            include( AdminPageFrameworkLoader_Registry::$sDirPath . '/example/post_meta_box/APF_PostMetaBox_TestAjaxField.php' );

        }
      
        /**
         * Taxonomy
         */     
        private function _loadTermMeta() {
            
            if ( version_compare( $GLOBALS[ 'wp_version' ], '4.4', '>=' ) ) {
                include( AdminPageFrameworkLoader_Registry::$sDirPath . '/example/term_meta/APF_TermMeta.php' );
                include( AdminPageFrameworkLoader_Registry::$sDirPath . '/example/term_meta/APF_TermMetaTestAjaxField.php' );
                return;
            }
            include( AdminPageFrameworkLoader_Registry::$sDirPath . '/example/taxonomy_field/APF_TaxonomyField.php' );
            include( AdminPageFrameworkLoader_Registry::$sDirPath . '/example/taxonomy_field/APF_TaxonomyFieldTestAjaxField.php' );

        }
      
        /**
         * Admin Pages
         */
        private function _loadAdminPaeges() {
        
            if ( ! is_admin() ) { 
                return; 
            }
                      
            // Basic usage - admin page and page meta boxes
            include( AdminPageFrameworkLoader_Registry::$sDirPath . '/example/basic_usage/APF_BasicUsage.php' );
               
            // Network admin pages
            include( AdminPageFrameworkLoader_Registry::$sDirPath . '/example/network_admin/APF_NetworkAdmin.php' );
           
            // Admin Pages
            include( AdminPageFrameworkLoader_Registry::$sDirPath . '/example/admin_page/APF_Demo.php' );

        }
      
        private function _loadUserMeta() {
            
            include( AdminPageFrameworkLoader_Registry::$sDirPath . '/example/user_meta/APF_MyUserMeta.php' );   
            include( AdminPageFrameworkLoader_Registry::$sDirPath . '/example/user_meta/APF_UserMetaTestAjaxField.php' );

        }
        
        private function _loadWidgets() {   
        
            include( AdminPageFrameworkLoader_Registry::$sDirPath . '/example/widget/APF_Widget.php' );
            include( AdminPageFrameworkLoader_Registry::$sDirPath . '/example/widget/APF_Widget_WithSection.php' );
            include( AdminPageFrameworkLoader_Registry::$sDirPath . '/example/widget/APF_WidgetTestAjaxField.php' );

        }      
      
        /**
         * Registers classes to be auto-loaded.
         * @return      void
         */
        private function _registerClasses() {

            $_aClassFiles = array();
            include( AdminPageFrameworkLoader_Registry::$sDirPath . '/example/class-file-list.php' );
            new AdminPageFramework_RegisterClasses( 
                array(),              // scanning directory paths
                array(),              // autoloader options
                $_aClassFiles         // pre-generated class list
            );            
            
        }
 
}
new AdminPageFrameworkLoader_Demo_Bootstrap;
