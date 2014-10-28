<?php 
$I = new AdminPageFramework_AcceptanceTester( $scenario );
$I->wantTo( 'Login to WordPress.' );
$I->amOnPage( '/wp-login.php' );
$I->fillField( 'Username', 'admin' );
$I->fillField( 'Password','admin' );
$I->click( 'Log In' );
$I->see( 'Dashboard' );

$I->wantTo( 'Check the existence of the custom post type post listing page of the demo plugin.' );
$I->amOnPage( '/wp-admin/edit.php?post_type=apf_posts' );
$I->see( 'Admin Page Framework', 'h2' );
$I->see( 'Add New', 'a.add-new-h2' );

$I->wantTo( 'Check the existence of the custom taxonomy of the demo plugin.' );
$I->amOnPage( '/wp-admin/edit-tags.php?taxonomy=apf_sample_taxonomy&post_type=apf_posts' );
$I->see( 'Add New Genre', 'h3' );

$I->wantTo( 'Check the existence of the Built-in Field Types page.' );
$I->amOnPage( '/wp-admin/edit.php?post_type=apf_posts&page=apf_builtin_field_types' );
$I->see( 'Built-in Field Types', 'h2' );

$I->wantTo( 'Check the existence of the Sample page.' );
$I->amOnPage( '/wp-admin/edit.php?post_type=apf_posts&page=apf_sample_page' );
$I->see( 'Sample Page', 'h2' );

$I->wantTo( 'Check the existence of the Custom Field Types page.' );
$I->amOnPage( '/wp-admin/edit.php?post_type=apf_posts&page=apf_custom_field_types' );
$I->see( 'Custom Field Types', 'h2' );

$I->wantTo( 'Check the existence of the Manage Options page.' );
$I->amOnPage( '/wp-admin/edit.php?post_type=apf_posts&page=apf_manage_options' );
$I->see( 'Manage Options', 'h2' );

$I->wantTo( 'Check the existence of the Read Me(About) page.' );
$I->amOnPage( '/wp-admin/edit.php?post_type=apf_posts&page=apf_read_me' );
$I->see( 'Admin Page Framework - Demo', 'h1' );

$I->wantTo( 'Check the existence of the Documentation page.' );
$I->click( 'Documentation', 'li > a' );
$I->see( '- The framework for all WordPress developers.' );

$I->wantTo( 'Check the existence of the Contact page.' );
$I->amOnPage( '/wp-admin/edit.php?post_type=apf_posts&page=apf_contact' );
$I->see( 'Contact', 'h3' );
