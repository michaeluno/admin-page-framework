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
        
        if ( ! $this->_shouldLoadDemo() ) {
            return;
        }
        
        do_action( 'admin_page_framework_loader_action_before_loading_demo' );
        
        // Include example components.
        include( AdminPageFrameworkLoader_Registry::$sDirPath . '/example/admin-page-framework-demo-bootstrap.php' );
        
        do_action( 'admin_page_framework_loader_action_after_loading_demo' );
        
    }
        /**
         * @since       3.7.4
         * @return      boolean
         */
        private function _shouldLoadDemo() {

            if ( AdminPageFrameworkLoader_Utility::isSilentMode() ) {
                return false;
            }
        
            // Check if the demo is enabled.
            $_oOption = AdminPageFrameworkLoader_Option::getInstance();
            if ( ! $_oOption->get( 'enable_admin_pages' ) ) {
                return false;
            }            
            if ( ! $_oOption->get( 'enable_demo' ) ) {
                $this->_setPointerToolTips();
                return false;
            }           
            
            return true;
            
        }
 
    private function _setPointerToolTips() {
        
        new AdminPageFramework_PointerToolTip(
            array( 
                // screen ids
                'plugins', 
                
                // page slugs below
                'apfl_addons', 
            ),     
            'apf_demo_pointer_tool_box_activate_demo', // unique id for the pointer tool box
            array(    // pointer data
                'target'    => array(
                    '#activate-demo-action-link',
                    '#button-activate-demo', // multiple targets can be set with an array
                ), 
                'options'   => array(
                    'content' => sprintf( '<h3> %1$s </h3> <p> %2$s </p>',
                        AdminPageFrameworkLoader_Registry::NAME,
                        __( 'Check out the functionality of Admin Page Framework by enabling the demo.','admin-page-framework-loader' )
                    ),
                    'position'  => array( 'edge' => 'left', 'align' => 'middle' )
                )
            )
        );               
        
    }
 
}
