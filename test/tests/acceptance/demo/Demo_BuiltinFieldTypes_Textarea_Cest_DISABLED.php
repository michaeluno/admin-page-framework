<?php
use \AcceptanceTester;

/**
 * Bootstrap does not work for including abstract classes.
 * @see     https://github.com/Codeception/Codeception/issues/862
 */
require_once( dirname( dirname( __FILE__ ) ) . '/_common/Demo_AdminPage_Base.php' );

class Demo_BuiltinFieldTypes_Textarea_Cest extends \Demo_AdminPage_Base {
    
    /**
     * @group   demo
     * @group   form
     * @group   admin
     * @group   loader
     */
    public function checkBuiltinFieldTypesPage( \AcceptanceTester $I ) {

        $I->wantTo( 'Check the built-in field types page of the demo of the loader plugin.' );
        
        // Click on the 'Built-in Field Types' menu link.
        // <a href="edit.php?post_type=apf_posts&amp;page=apf_builtin_field_types">Built-in Field Types</a>
        $I->click( '//li/a[contains(@href, "page=apf_builtin_field_types")]' );
        $this->_checkCommonElements( $I );
        
        // Check some field values.
        $I->seeInField(
            ['id' => 'text_fields_text__0'], 
            '123456'
        );
        
        // Click on the default active in-page tab. It should load the same page but the url should have the 'tab' query key.
        // <a class="nav-tab nav-tab-active" href="http://.../wp-admin/edit.php?post_type=apf_posts&amp;page=apf_builtin_field_types&amp;tab=textfields" data-tab-slug="textfields">Text</a>
        $I->click( '//a[@data-tab-slug="textfields"]' );

        // Check some field values.
        $I->seeInField(
            ['id' => 'text_fields_text__0'], 
            '123456'
        ); 
        
        // @todo fill the form and confirm values are stored
        
        
    } 
        
}