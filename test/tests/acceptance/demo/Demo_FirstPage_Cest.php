<?php
use \AcceptanceTester;

/**
 * Bootstrap does not work for including abstract classes.
 * @see     https://github.com/Codeception/Codeception/issues/862
 */
require_once dirname( dirname( __FILE__ ) ) . '/_bootstrap.php';

class Demo_FirstPage_Cest extends \Demo_AdminPage_Base {
    
    /**
     * @group   demo
     * @group   form
     * @group   first_page
     * @group   admin
     * @group   loader
     */
    public function checkTab( \AcceptanceTester $I ) {

        $I->wantTo( 'Check "First Page" of the demo of the loader plugin.' );
        
        // Click on the 'First Page' menu link.
        // <a href="admin.php?page=apf_first_page" class="wp-has-submenu wp-not-current-submenu menu-top toplevel_page_APF_BasicUsage menu-top-last" aria-haspopup="true"><div class="wp-menu-arrow"><div></div></div><div class="wp-menu-image dashicons-before dashicons-format-audio"><br></div><div class="wp-menu-name">Demo</div></a>
        $I->click( '//li/a[contains(@href, "page=apf_first_page")]' );
                
        $this->_checkCommonElements( $I );
        
        // Check some field elements.

        // @todo fill the form and confirm that values are stored

    }

}
