<?php
/**
 * 
 * ### Usage
 *         
 * `
 * UserLoginPage::of( $I )->login( 'user name', 'my password' );
 * `
 */
class UserLoginPage {
    
    /**
     * @var AcceptanceTester
     */
    protected $AcceptanceTester;

    public function __construct( AcceptanceTester $I ) {
        $this->AcceptanceTester = $I;
    }

    static public function of( AcceptanceTester $I ) {
        return new static( $I );
    }

    public function logIn( $sName, $sPassword ) {
        
        $I = $this->AcceptanceTester;

        $I->amOnPage( LoginPage::$sURL );
        $I->fillField( LoginPage::$sUserNameField, $sName );
        $I->fillField( LoginPage::$sPasswordField, $sPassword );
        $I->click( LoginPage::$sLoginButton );

        return $this;
        
    }

}