<?php
use \AcceptanceTester;

/**
 * Bootstrap does not work for including abstract classes.
 * @see     https://github.com/Codeception/Codeception/issues/862
 */
require_once( dirname( dirname( __FILE__ ) ) . '/_bootstrap.php' );

class Demo_CustomFieldTypes_ACE_Cest extends \Demo_AdminPage_Base {
    
    /**
     * @group   demo
     * @group   form
     * @group   custom_field_types
     * @group   ace
     * @group   admin
     * @group   loader
     */
    public function checkTab( \AcceptanceTester $I ) {

        $I->wantTo( 'Check the ACE tab of the custom field type of the demo of the loader plugin.' );
        
        // Click on the 'Custom Field Types' menu link.
        // <a href="edit.php?post_type=apf_posts&amp;page=custom_field_type">Custom Field Type</a>
        $I->click( '//li/a[contains(@href, "page=custom_field_type")]' );
                
        // This is the default tab so check an element first.
        $this->_checkCommonElements( $I );                
        $I->seeElement( '//textarea[contains(@name, "ace")]' );                        
                        
        // Click on the 'ACE' tab. 
        $I->click( '//a[@data-tab-slug="ace"]' );

        $this->_checkCommonElements( $I );
        
        // Check some field elements.
        $I->seeElement( '//textarea[contains(@name, "ace")]' );
   
        // @todo fill the form and confirm that values are stored
        
    } 
        
}
