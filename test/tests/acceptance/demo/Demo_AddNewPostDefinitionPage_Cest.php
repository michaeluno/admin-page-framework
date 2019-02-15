<?php


/**
 * Bootstrap does not work for including abstract classes.
 * @see     https://github.com/Codeception/Codeception/issues/862
 */
require_once( dirname( dirname( __FILE__ ) ) . '/_common/Demo_AdminPage_Base.php' );

class Demo_AddNewPostDefinitionPage_Cest extends \Demo_AdminPage_Base {

    /**
     * @group   demo
     * @group   post_type
     * @group   admin
     * @group   loader
     */
    public function checkAddNewPostDefinitionPage( \AcceptanceTester $I ) {

        $I->wantTo( 'Check the Add New post definition page of the demo of the loader plugin.' );

        $I->click( '//a[@href="post-new.php?post_type=apf_posts"]' );
        $this->_checkFooter( $I );

        // Publish button
        // <input type="submit" name="publish" id="publish" class="button button-primary button-large" value="Publish" accesskey="p">
        $I->see(
            '', // text omitted
            '//input[@id="publish"]'
        );

    }

}
