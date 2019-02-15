<?php
// @deprecated
// namespace AcceptanceTester;

class MemberSteps extends \AcceptanceTester {

    public function login( $sName, $sPassword ) {
        $I = $this;
        $I->amOnPage( \LoginPage::$URL );
        $I->fillField( \LoginPage::$sUserNameField, $sName );
        $I->fillField( \LoginPage::$sPasswordField, $sPassword );
        $I->click( \LoginPage::$sLoginButton );
    }

}
