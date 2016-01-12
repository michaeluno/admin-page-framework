<?php
/**
 * One of the abstract class of the plugin admin page class.
 * 
 * @package      Admin Page Framework Loader
 * @copyright    Copyright (c) 2014-2015, Michael Uno
 * @author       Michael Uno
 * @authorurl    http://michaeluno.jp
 * @since        3.5.0
 */

class AdminPageFrameworkLoader_NetworkAdmin extends AdminPageFramework_NetworkAdmin {
    
    /**
     * User constructor.
     * 
     * @since       3.5.0
     */
    public function start() {
  
        if ( ! $this->oProp->bIsAdmin ) {
            return;
        }
        if ( ! is_network_admin() ) {
            return;
        }
          
        // Enable / disable the demo pages
        if ( isset( $_GET['enable_apfl_demo_pages'] ) ) {
            
            // Update the options and reload the page
            $_oOption = AdminPageFrameworkLoader_Option::getInstance( AdminPageFrameworkLoader_Registry::$aOptionKeys['main'] );
            $_oOption->update( 'enable_demo', $_GET['enable_apfl_demo_pages'] );
            
            $this->setSettingNotice( 
                __( 'Enabled demo!', 'admin-page-framework-loader' ),
                'updated' 
            );
            $this->oUtil->goToLocalURL( 
                remove_query_arg( 'enable_apfl_demo_pages' ),
                array( 'AdminPageFrameworkLoader_Utility', 'replyToShowRedirectError' )
            );

        }
             
    }
  
    /**
     * Sets up admin pages.
     * 
     * @since       3.5.0
     */
    public function setUp() {
        
        // Action Links (plugin.php)
        $this->addLinkToPluginTitle(
            $this->_getDemoSwitcherLink( $this->oProp->aOptions )
        );
        $this->addLinkToPluginDescription(
            "<a href='https://wordpress.org/support/plugin/admin-page-framework' target='_blank'>" . __( 'Support', 'admin-page-framework-loader' ) . "</a>"
        );
        
    }
          
        /**
         * Returns the switch link of the demo pages.
         */
        private function _getDemoSwitcherLink( $mOptions=array() ) {
            
            $_bEnabled  = isset( $mOptions['enable_demo'] ) && $mOptions['enable_demo'];
            $_sLink    = esc_url( 
                add_query_arg( 
                    array( 
                        'enable_apfl_demo_pages' => $_bEnabled ? 0 : 1,
                    )
                )
            );        
            return $_bEnabled
                ? "<a href='{$_sLink}'>" . __( 'Disable Demo', 'admin-page-framework-loader' ) . "</a>"
                : "<a href='{$_sLink}'><strong style='font-size: 1em;'>" . __( 'Enable Demo', 'admin-page-framework-loader' ) . "</strong></a>";
            
        }            

}
