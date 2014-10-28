<?php 
$I = new AdminPageFramework_AcceptanceTester( $scenario );
$I->wantTo( 'Check the existence of the demo plgin.' );
$I->amOnPage( '/wp-login.php' );
$I->fillField( 'Username', 'admin' );
$I->fillField( 'Password','admin' );
$I->click( 'Log In' );
$I->see( 'Dashboard' );
$I->click( 'Plugins');
$I->see( 'Admin Page Framework - Demo' );