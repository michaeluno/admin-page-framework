<?php
use \AcceptanceTester;

/**
 * Bootstrap does not work for including abstract classes.
 * @see     https://github.com/Codeception/Codeception/issues/862
 */
require_once( dirname( dirname( __FILE__ ) ) . '/_bootstrap.php' );

class Demo_BuiltinFieldTypes_Verification_Cest extends \Demo_AdminPage_Base {
    
    /**
     * @group   demo
     * @group   form
     * @group   built-in_field_types
     * @group   verification
     * @group   admin
     * @group   loader
     */
    public function checkTab( \AcceptanceTester $I ) {

        $I->wantTo( 'Check the Verification tab of the built-in field type of the demo of the loader plugin.' );
        
        // Click on the 'Built-in Field Types' menu link.
        // <a href="edit.php?post_type=apf_posts&amp;page=apf_builtin_field_types">Built-in Field Types</a>
        $I->click( '//li/a[contains(@href, "page=apf_builtin_field_types")]' );
                        
        // Click on the 'Verification' tab. 
        $I->click( '//a[@data-tab-slug="verification"]' );

        $this->_checkCommonElements( $I );
        
        // Check some field elements..
        // <input id="verification_verify_text_field__0" name="APF_Demo[verification][verify_text_field]" type="text" data-id_model="verification_verify_text_field__-fi-" data-name_model="APF_Demo[verification][verify_text_field]" maxlength="400" class="">
        $I->seeElement( '//input[contains(@name, "verify_text_field")]' );
   
        // @todo fill the form and confirm that values are stored
        
    } 
        
}