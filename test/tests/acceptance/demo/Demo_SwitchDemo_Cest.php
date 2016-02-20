<?php
use \AcceptanceTester;

/**
 * Bootstrap does not work for including abstract classes.
 * @see     https://github.com/Codeception/Codeception/issues/862
 */
require_once( dirname( dirname( __FILE__ ) ) . '/_bootstrap.php' );

class Demo_SwitchDemo_Cest extends \Loader_AdminPage_Base {
 
    /**
     * 
     * @group   loader
     * @group   demo
     * @group   admin
     * @group   switch
     * @group   switch_demo
     */
    public function switchDemo( \AcceptanceTester $I )  {
        
        $I->wantTo( 'Switch demo of the loader plugin.' );
        $I->lookForwardTo( 'see the demo pages are enabled when clicking on Enable link and disabled when clicking on the Disable link.' );

        $I->amOnPage( '/wp-admin/plugins.php' );
        
        // See the top-level menu item
        // <div class="wp-menu-name">Admin Page Framework</div>
        $I->see(
            'Admin Page Framework', // text
            '//div[@class="wp-menu-name"]'
        );
        
        // The first sub-menu item of the Demo menu.
        // <a href="edit.php?post_type=apf_posts" class="wp-first-item">Sample Posts</a>
        $I->dontSee(
            '', // text
            '//a[@href="edit.php?post_type=apf_posts"]'
        );
        
        // Click on the 'Enable Demo' link.
        // <a href="/wp41/wp-admin/plugins.php?enable_apfl_demo_pages=1"><strong style="font-size: 1em;">Enable Demo</strong></a>
        $I->click( '//a[contains(@href, "wp-admin/plugins.php?enable_apfl_demo_pages=1")]' );

        // The first sub-menu item of the Demo menu.
        // <a href="edit.php?post_type=apf_posts" class="wp-first-item">Sample Posts</a>
        $I->see(
            '', // text
            '//a[@href="edit.php?post_type=apf_posts"]'
        );        
        
        // Click on the 'Disable Demo' link.
        $I->click( '//a[contains(@href, "wp-admin/plugins.php?enable_apfl_demo_pages=0")]' );
        $I->dontSee(
            '', // text
            '//a[@href="edit.php?post_type=apf_posts"]'
        );
        
        // Go to the Add-on page
        $I->amOnPage( '/wp-admin/admin.php?page=apfl_addons' );
        
        // Click on the Activate button of the Demo add-on
        // <a href="http://...wp-admin/admin.php?enable_apfl_demo_pages=1&amp;page=apfl_addons" target="" rel="nofollow" class="button button-secondary">Activate</a>
        $I->click( '//a[contains(@href, "wp-admin/admin.php?enable_apfl_demo_pages=1")]' );
        $I->see(
            '', // text
            '//a[@href="edit.php?post_type=apf_posts"]'
        ); 
        $I->click( '//a[contains(@href, "wp-admin/admin.php?enable_apfl_demo_pages=0")]' );
        $I->dontSee(
            '', // text
            '//a[@href="edit.php?post_type=apf_posts"]'
        ); 
        
    }

}
