<?php
use \AcceptanceTester;

/**
 * Bootstrap does not work for including abstract classes.
 * @see     https://github.com/Codeception/Codeception/issues/862
 */
require_once( dirname( dirname( __FILE__ ) ) . '/_common/Demo_AdminPage_Base.php' );

class Demo_PostListingPage_Cest extends \Demo_AdminPage_Base {
    
    /**
     * @group   demo
     * @group   post_type
     * @group   admin
     * @group   loader
     */
    public function checkCustomPostListingPage( \AcceptanceTester $I ) {

        $I->wantTo( 'Check the custom post listing table page of the demo of the loader plugin.' );
              
        // Click the 'Sample Posts' menu link
        // <a href="edit.php?post_type=apf_posts" class="wp-first-item">Sample Posts</a>
        $I->click( '//a[@href="edit.php?post_type=apf_posts"]' );
        $this->_checkFooter( $I );
        
        // Check the 'Add New' button
        // <a href="http://localhost/wp41/wp-admin/post-new.php?post_type=apf_posts" class="add-new-h2">Add New</a>
        $I->see( 
            '', // text omitted
            '//a[contains(@href, "wp-admin/post-new.php?post_type=apf_posts")]' // xpath
        );                    
    
    } 

}
