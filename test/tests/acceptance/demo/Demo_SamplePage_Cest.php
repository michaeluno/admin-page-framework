<?php


/**
 * Bootstrap does not work for including abstract classes.
 * @see     https://github.com/Codeception/Codeception/issues/862
 */
require_once( dirname( dirname( __FILE__ ) ) . '/_bootstrap.php' );

class Demo_SamplePage_Cest extends \Demo_AdminPage_Base {

    /**
     * @group   demo
     * @group   sample_page
     * @group   admin
     * @group   loader
     */
    public function checkPage( \AcceptanceTester $I ) {

        $I->wantTo( 'Check the sample page of the demo of the loader plugin.' );

        // Click on the 'Sample Page' menu link.
        // <a href="edit.php?post_type=apf_posts&amp;page=apf_sample_page">Sample Page</a>
        $I->click( '//li/a[contains(@href, "page=apf_sample_page")]' );

        $this->_checkCommonElements( $I );

        // Click on the 'Go to hidden page' link.
        // <a href="http://.../wp-admin/edit.php?post_type=apf_posts&amp;page=apf_hidden_page">Go to Hidden Page</a>
        $I->click( '//a[contains(@href, "page=apf_hidden_page")]' );

        $this->_checkCommonElements( $I );

        // Check the 'Go back' link
        // <a href="http://../wp-admin/edit.php?post_type=apf_posts&amp;page=apf_sample_page">Go Back</a>
        $I->seeElement( '//a[contains(@href, "page=apf_sample_page")]' );

        // @todo fill the form and confirm that values are stored

    }

}
