<?php
use \AcceptanceTester;

require_once( dirname( __FILE__ ) . '/Loader_AdminPage_Base.php' );

class Demo_AdminPage_Base extends \Loader_AdminPage_Base {
    
    public function _before( \AcceptanceTester $I ){
        
        parent::_before( $I );
        $this->_enableDemo( $I );
        
    }   
    
        protected function _enableDemo( $I ) {
            
            // Go to the plugin listing page.
            $I->amOnPage( '/wp-admin/plugins.php' );

            // Click on the 'Enable Demo' link.
            // <a href="/wp41/wp-admin/plugins.php?enable_apfl_demo_pages=1"><strong style="font-size: 1em;">Enable Demo</strong></a>
            $I->click( '//a[contains(@href, "wp-admin/plugins.php?enable_apfl_demo_pages=1")]' );

        }

}