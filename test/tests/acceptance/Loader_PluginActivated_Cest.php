<?php


class Loader_PluginActivated_Cest {

    public function _before( \AcceptanceTester $I ){}
    public function _after( \AcceptanceTester $I ){}

    // tests
    public function checkPluginIsActivated( \AcceptanceTester $I ){

        $I->wantTo( 'Check the existence of the loader plugin and it is activated.' );
        $I->lookForwardTo( 'see plugin is already activated in the plugin listing table.' );

        UserLoginPage::of( $I )->login( 'admin', 'admin' );

        $I->amOnPage( '/wp-admin/plugins.php' );

        $I->see( 'Admin Page Framework - Loader', 'tr.active' );

    }

}
