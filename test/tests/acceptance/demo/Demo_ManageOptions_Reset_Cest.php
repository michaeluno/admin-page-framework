<?php
use \AcceptanceTester;

/**
 * Bootstrap does not work for including abstract classes.
 * @see     https://github.com/Codeception/Codeception/issues/862
 */
require_once dirname( dirname( __FILE__ ) ) . '/_bootstrap.php';

class Demo_ManageOptions_Reset_Cest extends \Demo_AdminPage_Base {
    
    /**
     * @group   demo
     * @group   form
     * @group   manage_options
     * @group   reset
     * @group   admin
     * @group   loader
     */
    public function checkTab( \AcceptanceTester $I ) {

        $I->wantTo( 'Check the Reset tab of the "Manage Options" page of the demo of the loader plugin.' );
        
        // Click on the 'Manage Options' menu link.
        // <a href="edit.php?post_type=apf_posts&amp;page=apf_manage_options">Manage Options</a>
        $I->click( '//li/a[contains(@href, "page=apf_manage_options")]' );
                                        
        // Click on the 'Reset' tab. 
        $I->click( '//a[@data-tab-slug="reset"]' );

        $this->_checkCommonElements( $I );
        
        // Check some field elements.

        // <input type="submit" value="Delete Options" class="button-secondary" id="reset_submit_manage__0" name="APF_Demo[reset][submit_manage]" data-id_model="reset_submit_manage__-fi-" data-name_model="APF_Demo[reset][submit_manage]" title="Delete Options" alt="">
        $I->seeElement( '//input[contains(@name, "submit_skip_confirmation")]' );
   
        // @todo fill the form and confirm that values are stored

    }
        
}
