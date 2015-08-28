<?php
use \AcceptanceTester;

/**
 * Bootstrap does not work for including abstract classes.
 * @see     https://github.com/Codeception/Codeception/issues/862
 */
require_once( dirname( dirname( __FILE__ ) ) . '/_bootstrap.php' );

class Demo_Contact_Feedback_Cest extends \Demo_AdminPage_Base {
    
    /**
     * @group   demo
     * @group   form
     * @group   contact
     * @group   feedback
     * @group   admin
     * @group   loader
     */
    public function checkTab( \AcceptanceTester $I ) {

        $I->wantTo( 'Check the "Feedback" tab of the "Contact" page of the demo of the loader plugin.' );
        
        // Click on the 'Contact' menu link.
        // <a href="edit.php?post_type=apf_posts&amp;page=apf_contact">Contact</a>
        $I->click( '//li/a[contains(@href, "page=apf_contact")]' );
        
        // This is the default tab so check an element first.
        $this->_checkCommonElements( $I );
        $I->seeElement( '//input[contains(@name, "feedback")]' );
        
        // Click on the 'Feedback' tab. 
        $I->click( '//a[@data-tab-slug="feedback"]' );

        $this->_checkCommonElements( $I );
        
        // Check some field elements.
        
        $I->seeElement( '//input[contains(@name, "feedback")]' );

        // @todo fill the form and confirm that values are stored

    }

}