<?php
use \AcceptanceTester;

/**
 * Bootstrap does not work for including abstract classes.
 * @see     https://github.com/Codeception/Codeception/issues/862
 */
require_once( dirname( dirname( __FILE__ ) ) . '/_bootstrap.php' );

class Demo_BuiltinFieldTypes_MISC_Cest extends \Demo_AdminPage_Base {
    
    /**
     * @group   demo
     * @group   form
     * @group   built-in_field_types
     * @group   misc
     * @group   admin
     * @group   loader
     */
    public function checkTab( \AcceptanceTester $I ) {

        $I->wantTo( 'Check the MISC tab of the built-in field type of the demo of the loader plugin.' );
        
        // Click on the 'Built-in Field Types' menu link.
        // <a href="edit.php?post_type=apf_posts&amp;page=apf_builtin_field_types">Built-in Field Types</a>
        $I->click( '//li/a[contains(@href, "page=apf_builtin_field_types")]' );
                        
        // Click on the 'MISC' tab. 
        $I->click( '//a[@data-tab-slug="misc"]' );

        $this->_checkCommonElements( $I );
        
        // Check some field elements..
        // <input type="text" class="input_color" id="color_picker_color_picker_field__0" name="APF_Demo[color_picker][color_picker_field]" value="transparent" data-id_model="color_picker_color_picker_field__-fi-" data-name_model="APF_Demo[color_picker][color_picker_field]" size="10" maxlength="400">
        $I->seeElement( '//input[contains(@name, "color_picker_field")]' );
   
        // @todo fill the form and confirm that values are stored
        
        
    } 
        
}
