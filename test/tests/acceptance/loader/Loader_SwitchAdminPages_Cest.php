<?php

/**
 * Bootstrap does not work for including abstract classes.
 * @see     https://github.com/Codeception/Codeception/issues/862
 */
require_once( dirname( dirname( __FILE__ ) ) . '/_common/Loader_AdminPage_Base.php' );

class Loader_SwitchAdminPages_Cest extends \Loader_AdminPage_Base {

    /**
     *
     * @group   loader
     * @group   admin
     * @group   switch
     */
    public function switchAdminPages( \AcceptanceTester $I )  {

        $I->wantTo( 'Switch admin pages of the loader plugin.' );
        $I->lookForwardTo( 'see the admin pages is enabled when clicking on Enable link and disabled when clicking on the Disable link.' );

        $I->amOnPage( '/wp-admin/plugins.php' );

        // See the top-level menu item
        // <div class="wp-menu-name">Admin Page Framework</div>
        $I->see(
            'Admin Page Framework', // text
            '//div[@class="wp-menu-name"]'
        );

        // Click on the 'Disable Admin Page' link.
        // <a href="/wp41/wp-admin/plugins.php?enable_apfl_admin_pages=0">Disable Admin Pages</a>
        $I->click( '//a[contains(@href, "wp-admin/plugins.php?enable_apfl_admin_pages=0")]' );
        $I->dontsee(
            'Admin Page Framework', // text
            '//div[@class="wp-menu-name"]'
        );

        // Click on the 'Enable Admin Page' link.
        $I->click( '//a[contains(@href, "wp-admin/plugins.php?enable_apfl_admin_pages=1")]' );
        $I->see(
            'Admin Page Framework', // text
            '//div[@class="wp-menu-name"]'
        );

    }

}
