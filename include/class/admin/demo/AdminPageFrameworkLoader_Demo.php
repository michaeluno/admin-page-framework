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
        
        // Backward compatibility.
        define( 'APFDEMO_FILE', AdminPageFrameworkLoader_Registry::$sFilePath );
        define( 'APFDEMO_DIRNAME', AdminPageFrameworkLoader_Registry::$sDirPath );
        
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
            if ( ! $_oOption->get( 'enable_demo' ) ) {
                return false;
            }           
            
            return true;
            
        }
 
}