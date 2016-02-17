<?php
use \AcceptanceTester;

/**
 * Bootstrap does not work for including abstract classes.
 * @see     https://github.com/Codeception/Codeception/issues/862
 */
require_once dirname( dirname( __FILE__ ) ) . '/_bootstrap.php';

class Demo_BuiltinFieldTypes_Files_Cest extends \Demo_AdminPage_Base {
    
    /**
     * @group   demo
     * @group   form
     * @group   files
     * @group   built-in_field_types
     * @group   admin
     * @group   loader
     */
    public function checkTab( \AcceptanceTester $I ) {

        $I->wantTo( 'Check the Files tab of the built-in field type of the demo of the loader plugin.' );
        
        // Click on the 'Built-in Field Types' menu link.
        // <a href="edit.php?post_type=apf_posts&amp;page=apf_builtin_field_types">Built-in Field Types</a>
        $I->click( '//li/a[contains(@href, "page=apf_builtin_field_types")]' );
                        
        // Click on the 'Files' tab. 
        $I->click( '//a[@data-tab-slug="files"]' );

        $this->_checkCommonElements( $I );
        
        // Check some field elements.
        // <div id="image_preview_container_image_select_image_select_field__0" class="image_preview " style="max-width:300px;" data-id_model="image_select_image_select_field__-fi-" data-name_model="APF_Demo[image_select][image_select_field][-fi-]"><img src="http://localhost/wp41/wp-content/plugins/admin-page-framework/asset/image/demo/wordpress-logo-2x.png" id="image_preview_image_select_image_select_field__0"></div>
        $I->seeElement( '//div[contains(@class, "image_preview")]' );
   
        // @todo fill the form and confirm that values are stored

        
    }
        
}
