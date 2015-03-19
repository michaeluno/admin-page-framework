<?php
use \AcceptanceTester;

/**
 * Bootstrap does not work for including abstract classes.
 * @see     https://github.com/Codeception/Codeception/issues/862
 */
require_once( dirname( dirname( __FILE__ ) ) . '/_bootstrap.php' );

class Demo_SecondPage_Cest extends \Demo_AdminPage_Base {
    
    /**
     * @group   demo
     * @group   form
     * @group   second_page
     * @group   admin
     * @group   loader
     */
    public function checkTab( \AcceptanceTester $I ) {

        $I->wantTo( 'Check "Second Page" of the demo of the loader plugin.' );
        
        // Click on the 'Second Page' menu link.
        // <a href="admin.php?page=apf_second_page" class="wp-has-submenu wp-not-current-submenu menu-top toplevel_page_APF_BasicUsage menu-top-last" aria-haspopup="true"><div class="wp-menu-arrow"><div></div></div><div class="wp-menu-image dashicons-before dashicons-format-audio"><br></div><div class="wp-menu-name">Demo</div></a>
        $I->click( '//li/a[contains(@href, "page=apf_second_page")]' );
                
        $this->_checkCommonElements( $I );
        
        // Check some field elements.
        $I->seeElement( '//pre[contains(@class, "dump-array")]' );
        
        // @todo fill the form and confirm that values are stored

    }

}