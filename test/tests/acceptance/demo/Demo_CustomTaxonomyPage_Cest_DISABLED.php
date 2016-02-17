<?php
use \AcceptanceTester;

/**
 * Bootstrap does not work for including abstract classes.
 * @see     https://github.com/Codeception/Codeception/issues/862
 */
require_once dirname( dirname( __FILE__ ) ) . '/_common/Demo_AdminPage_Base.php';

class Demo_CustomTaxonomyPage_Cest extends \Demo_AdminPage_Base {
    
    /**
     * @group   demo
     * @group   taxonomy
     * @group   admin
     * @group   loader
     */
    public function checkCustomTaxonomyPage( \AcceptanceTester $I ) {

        $I->wantTo( 'Check the custom taxonomy page of the demo of the loader plugin.' );

        // <a href="edit-tags.php?taxonomy=apf_sample_taxonomy&amp;post_type=apf_posts">Sample Genre</a>
        $I->click( '//a[contains(@href, "edit-tags.php?taxonomy=apf_sample_taxonomy")]' );
        $this->_checkFooter( $I );
        
        // Add New button
        // <input type="submit" name="submit" id="submit" class="button button-primary" value="Add New Genre">
        $I->see(
            '', // text omitted
            '//input[@id="submit"]'
        );
        
    }

}
