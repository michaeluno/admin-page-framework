<?php
use \AcceptanceTester;

/**
 * Bootstrap does not work for including abstract classes.
 * @see     https://github.com/Codeception/Codeception/issues/862
 */
require_once( dirname( dirname( __FILE__ ) ) . '/_bootstrap.php' );

class Demo_BuiltinFieldTypes_Checklist_Cest extends \Demo_AdminPage_Base {
    
    /**
     * @group   demo
     * @group   form
     * @group   built-in_field_types
     * @group   checklist
     * @group   admin
     * @group   loader
     */
    public function checkTab( \AcceptanceTester $I ) {

        $I->wantTo( 'Check the Checklist tab of the built-in field type of the demo of the loader plugin.' );
        
        // Click on the 'Built-in Field Types' menu link.
        // <a href="edit.php?post_type=apf_posts&amp;page=apf_builtin_field_types">Built-in Field Types</a>
        $I->click( '//li/a[contains(@href, "page=apf_builtin_field_types")]' );
                        
        // Click on the 'Checklist' tab. 
        $I->click( '//a[@data-tab-slug="checklist"]' );

        $this->_checkCommonElements( $I );
        
        // Check some field elements.
        // <input type="checkbox" id="checklists_post_type_checklist__0_post" value="1" name="APF_Demo[checklists][post_type_checklist][post]" data-id="checklists_post_type_checklist__0" data-id_model="checklists_post_type_checklist__-fi-" data-name_model="APF_Demo[checklists][post_type_checklist]" size="30" maxlength="400" class="" checked="checked">
        $I->seeElement( '//input[contains(@name, "post_type_checklist")]' );
   
        // @todo fill the form and confirm that values are stored
        
        
    } 
        
}