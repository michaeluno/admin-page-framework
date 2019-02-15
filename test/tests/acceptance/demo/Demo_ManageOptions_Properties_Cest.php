<?php

/**
 * Bootstrap does not work for including abstract classes.
 * @see     https://github.com/Codeception/Codeception/issues/862
 */
require_once( dirname( dirname( __FILE__ ) ) . '/_bootstrap.php' );

class Demo_ManageOptions_Properties_Cest extends \Demo_AdminPage_Base {

    /**
     * @group   demo
     * @group   form
     * @group   manage_options
     * @group   properties
     * @group   admin
     * @group   loader
     */
    public function checkTab( \AcceptanceTester $I ) {

        $I->wantTo( 'Check the Properties tab of the "Manage Options" page of the demo of the loader plugin.' );

        // Click on the 'Manage Options' menu link.
        // <a href="edit.php?post_type=apf_posts&amp;page=apf_manage_options">Manage Options</a>
        $I->click( '//li/a[contains(@href, "page=apf_manage_options")]' );

        // Click on the 'Properties' tab.
        $I->click( '//a[@data-tab-slug="properties"]' );

        $this->_checkCommonElements( $I );

        // Check some field elements.

        $I->seeElement( '//pre[contains(@class, "dump-array")]' );

        // @todo fill the form and confirm that values are stored

    }

}
