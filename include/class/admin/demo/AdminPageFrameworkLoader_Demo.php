<?php
/**
 * Loads the demo components.
 * 
 * @package      Admin Page Framework Loader
 * @copyright    Copyright (c) 2014, Michael Uno
 * @author       Michael Uno
 * @authorurl    http://michaeluno.jp
 * @since        3.5.0
 */

 /**
  * 
  * 
  * @action     do      admin_page_framework_loader_action_before_loading_demo
  * @action     do      admin_page_framework_loader_action_after_loading_demo
  */
class AdminPageFrameworkLoader_Demo {
    
    public function __construct() {

        // Check if the demo is enabled.
        $_oOption = AdminPageFrameworkLoader_Option::getInstance();
        if ( ! $_oOption->get( 'enable_demo' ) ) {
            return;
        }
        
        // Otherwise, load it.
        $this->_registerClasses();
        
        // Backward compatibility.
        define( 'APFDEMO_FILE', AdminPageFrameworkLoader_Registry::$sFilePath );
        define( 'APFDEMO_DIRNAME', AdminPageFrameworkLoader_Registry::$sDirPath );
        
        do_action( 'admin_page_framework_loader_action_before_loading_demo' );
        
        // Include example components.
        $this->_includePostTypes();
        $this->_includeBasicExamples();
        $this->_includeAdminPages();
        $this->_includeNetworkAdminPages();
        $this->_includeWidgets();
     
        do_action( 'admin_page_framework_loader_action_after_loading_demo' );
        
    }

        private function _includeBasicExamples() {
                        
            if ( ! is_admin() ) { return; }
            
            new APF_BasicUsage(
                null,                       // the option key - when null is passed the class name in this case 'APF_BasicUsage' will be used
                APFDEMO_FILE,               // the caller script path.
                'manage_options',           // the default capability
                'admin-page-framework-demo' // the text domain    
            );

            new APF_MetaBox_For_Pages_Normal(
                null,                                           // meta box id - passing null will make it auto generate
                __( 'Sample Meta Box for Admin Pages Inserted in Normal Area', 'admin-page-framework-demo' ), // title
                'apf_first_page',                               // page slugs
                'normal',                                       // context
                'default'                                       // priority
            );
            new APF_MetaBox_For_Pages_Advanced(    
                null,                                           // meta box id - passing null will make it auto generate
                __( 'Sample Meta Box for Admin Pages Inserted in Advanced Area', 'admin-page-framework-demo' ), // title
                'apf_first_page',                               // page slugs
                'advanced',                                     // context
                'default'                                       // priority
            );    
            new APF_MetaBox_For_Pages_Side(    
                null,                                           // meta box id - passing null will make it auto generate
                __( 'Sample Meta Box for Admin Pages Inserted in Advanced Area', 'admin-page-framework-demo' ), // title
                array( 'apf_first_page', 'apf_second_page' ),   // page slugs - setting multiple slugs is possible
                'side',                                         // context
                'default'                                       // priority
            );       
            
        }
        
        private function _includeAdminPages() {
            
            if ( ! is_admin() ) { return; }
            
            // Add pages and forms in the custom post type root page
            new APF_Demo( 
                null,                       // the option key - when null is passed the class name in this case 'APF_Demo' will be used
                APFDEMO_FILE,               // the caller script path.
                'manage_options',           // the default capability
                'admin-page-framework-demo' // the text domain
            );
                        
            // Add the Manage Options page.
            new APF_Demo_ManageOptions( 
                'APF_Demo',                 // passing the option key used by the main pages.
                APFDEMO_FILE,               // the caller script path.
                'manage_options',           // the default capability
                'admin-page-framework-demo' // the text domain        
            );
            
            // Add a hidden page. This class does not extend the framework factory class.
            new APF_Demo_HiddenPage;
            
            // Add the contact page
            new APF_Demo_Contact(
                '',                         // passing an empty string will disable the form data to be saved.
                APFDEMO_FILE,               // the caller script path.
                'read',                     // the default capability
                'admin-page-framework-demo' // the text domain        
            );
            
        }
        
        private function _includeNetworkAdminPages() {
            if ( ! is_network_admin() ) {
                return;
            }
            new APF_NetworkAdmin(
                null,                       // passing the option key used by the main pages.
                APFDEMO_FILE,               // the caller script path.
                'manage_options',           // the default capability
                'admin-page-framework-demo' // the text domain        
            ); 
            new APF_NetworkAdmin_ManageOptions( 
                'APF_NetworkAdmin', 
                APFDEMO_FILE,               // the caller script path.
                'manage_options',           // the default capability
                'admin-page-framework-demo' // the text domain                    
            );

        }        
        
        private function _includeWidgets() {
            
            new APF_Widget( 
                __( 'Admin Page Framework', 'admin-page-framework-demo' ) // the widget title
            );
            
            
        }
        private function _includePostTypes() {
                
            new APF_PostType( 
                'apf_posts',                // the post type slug
                null,                       // the argument array. Here null is passed because it is defined inside the class.
                APFDEMO_FILE,               // the caller script path.
                'admin-page-framework-demo' // the text domain.
            );   

            $this->_includePostMetaBoxes();
            $this->_includeTaxonomies();
            
        }
            private function _includePostMetaBoxes() {
                
                new APF_MetaBox_BuiltinFieldTypes(
                    null,  // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
                    __( 'Demo Meta Box with Built-in Field Types', 'admin-page-framework-demo' ), // title
                    array( 'apf_posts' ),                            // post type slugs: post, page, etc.
                    'normal',                                        // context (what kind of metabox this is)
                    'high'                                           // priority
                );
                new APF_MetaBox_TabbedSections(
                    null,  // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
                    __( 'Section Tabs', 'admin-page-framework-demo' ), // title
                    array( 'apf_posts' ),                               // post type slugs: post, page, etc.
                    'normal',                                           // context (what kind of metabox this is)
                    'default'                                           // priority
                );    
                new APF_MetaBox_RepeatableTabbedSections(
                    null,  // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
                    __( 'Repeatable Section Tabs', 'admin-page-framework-demo' ), // title
                    array( 'apf_posts' ),                               // post type slugs: post, page, etc.
                    'normal',                                           // context (what kind of metabox this is)
                    'default'                                           // priority
                );
                new APF_MetaBox_CollapsibleSections(
                    null,   // meta box id
                    __( 'Collapsible Sections', 'admin-page-framework-demo' ),
                    array( 'apf_posts' ),                             
                    'normal',
                    'low'
                );
                new APF_MetaBox_RepeatableCollapsibleSections(
                    null,   // meta box id
                    __( 'Repeatable Collapsible Sections', 'admin-page-framework-demo' ),
                    array( 'apf_posts' ),                             
                    'normal',
                    'low'
                );
                
            }
            private function _includeTaxonomies() {
                new APF_TaxonomyField( 'apf_sample_taxonomy' ); // taxonomy slug
            }
 
 
        /**
         * Registers classes to be auto-loaded.
         */
        private function _registerClasses() {

            $_aClassFiles = array();
            include( AdminPageFrameworkLoader_Registry::$sDirPath . '/include/admin-page-framework-demo-include-class-file-list.php' );            
            new AdminPageFramework_RegisterClasses( 
                array(),              // scanning directory paths
                array(),              // autoloader options
                $_aClassFiles         // pre-generated class list
            );            
            
        }
 
}