<?php
use \AcceptanceTester;

/*
 * Include the bootstrap script of 'functional' test suites to load WodPress
 */
// include_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/functional/_bootstrap.php' );

class AdminPageFramework_Utility_URL_Cest {
    
    public function _before( \AcceptanceTester $I ){}
    public function _after( \AcceptanceTester $I ){}

    // tests
    /**
     * Temporarily disabled.
     * @group       utility
     * @group       url
     */
    protected function _getCurrentURL( \AcceptanceTester $I ){

        $I->wantTo( 'Test geCurrentURL method.' );
        $I->lookForwardTo( 'see the current url.' );
        
        // UserLoginPage::of( $I )->login( 'admin', 'admin' );

        $I->amOnPage( '/' );
        // $_oUtil = new AdminPageFramework_WPUtility;

        // $I->seeInCurrentUrl( $_SERVER['REQUEST_URI'] );

        // $I->seeCurrentUrlEquals( $_oUtil->getCurrentURL() );
        // $I->seeCurrentUrlEquals( 'aaa' );

    }

}
