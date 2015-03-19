<?php
use \AcceptanceTester;

/**
 * Bootstrap does not work for including abstract classes.
 * @see     https://github.com/Codeception/Codeception/issues/862
 */
require_once( dirname( dirname( __FILE__ ) ) . '/_common/Loader_AdminPage_Base.php' );

class Loader_AdminPages_Cest extends \Loader_AdminPage_Base {
    
    /**
     * 
     * @group       loader
     * @group       welcome
     * @group       admin
     */
    public function checkWelcomePage( \AcceptanceTester $I ) {
        
        // apf-badge
        $I->wantTo( 'Check the Welcome page of the loader plugin.' );
        $I->lookForwardTo( 'see elements specific to the Welcome admin page of the loader plugin.' );
        
        $I->amOnPage( '/wp-admin/index.php?page=apfl_about' );
        $this->_checkBadge( $I );
        $this->_checkCommonElements( $I );

        
        $I->click( '//a[@data-tab-slug="welcome"]' );
        $this->_checkBadge( $I );
        $this->_checkCommonElements( $I );
        
        // <a class="nav-tab nav-tab-active" href="http://localhost/test_wp/test-admin-page-framework/wp-admin/index.php?page=apfl_about&amp;tab=guide" data-tab-slug="guide">Getting Started</a>
        $I->click( '//a[@data-tab-slug="guide"]' );
        $this->_checkBadge( $I );
        $this->_checkCommonElements( $I );
        
        // <a class="nav-tab nav-tab-active" href="http://localhost/test_wp/test-admin-page-framework/wp-admin/index.php?page=apfl_about&amp;tab=change_log" data-tab-slug="change_log">Change Log</a>
        $I->click( '//a[@data-tab-slug="change_log"]' );
        $this->_checkBadge( $I );
        $this->_checkCommonElements( $I );
        
        // <a class="nav-tab nav-tab-active" href="http://localhost/test_wp/test-admin-page-framework/wp-admin/index.php?page=apfl_about&amp;tab=credit" data-tab-slug="credit">Credit</a>
        $I->click( '//a[@data-tab-slug="credit"]' );
        $this->_checkBadge( $I );
        $this->_checkCommonElements( $I );
        
    }
        protected function _checkBadge( \AcceptanceTester $I ) {
            
            // Check the badge (framework large icon)
            // <div class="apf-badge"><span class="label">Version - 3.5.7b01</span></div>            
            // Somehow this does not work: $I->see( '//div[@class="apf-badge"]' );            
            $I->see( 
                '', // text 
                'div.apf-badge' // css
            );            
            
        }    
    
    
    /**
     * 
     * @group   admin
     * @group   loader
     */
    public function checkGeneratorTab( \AcceptanceTester $I ) {

        $I->wantTo( 'Check the admin pages of the loader plugin.' );
        $I->lookForwardTo( 'see certain text values of the loader plugin admin pages.' );
                
                
        // Generator  - default without and with the tab query key.
        $I->amOnPage( '/wp-admin/admin.php?page=apfl_tools' );
        $this->_checkCommonElements( $I );
        $I->click( 
            '', // the Generator tab 
            'h2.in-page-tab > a.nav-tab' 
        );
        $I->see( 
            '', // the Generator tab
            'h2.in-page-tab > a.nav-tab' 
        );
    }
    
    /**
     * @group   admin
     * @group   loader
     */
    public function checkAddOnsPage( \AcceptanceTester $I ) {

        $I->wantTo( 'Check the Add-ons page of the loader plugin.' );
        
        // <li><a href="admin.php?page=apfl_addons">Add Ons</a></li>
        $I->click( '//a[@href="admin.php?page=apfl_addons"]' );
        $this->_checkCommonElements( $I );
        $I->see( 
            'Demo', // the Demo add-on title. This label is imported from RSS feed so is not applied translation.
            'h4.apfl_feed_item_title' 
        );             
     
    }
    
    /**
     * @group   help
     * @group   admin
     * @group   loader
     */
    public function checkHelpPage( \AcceptanceTester $I ) {

        $I->wantTo( 'Check the Help page of the loader plugin.' );
        
        // Click the Help menu link
        // <li><a href="admin.php?page=apfl_contact">Help</a></li>
        $I->click( '//a[@href="admin.php?page=apfl_contact"]' );
        $this->_checkCommonElements( $I );
        
        // Click the 'Support' tab
        // <a class="nav-tab nav-tab-active" href="http://.../wp-admin/admin.php?page=apfl_contact&amp;tab=information">Support</a>
        $I->click( '//a[@data-tab-slug="information"]' );
        $this->_checkCommonElements( $I );
        
        // See the Donation image button
        $I->see(
            '',
            'div.donate-button'
        );
        
        // See the 'Getting Started' tab
        $I->see(
            '', // text
            '//a[@data-tab-slug="guide"]'   // xpath
        );                
        // Click the 'Tips' tab
        $I->click( '//a[@data-tab-slug="tips"]' );
        $this->_checkCommonElements( $I );

        // Click the 'FAQ' tab
        $I->click( '//a[@data-tab-slug="faq"]' );
        $this->_checkCommonElements( $I );

        // Click the 'Examples' tab
        $I->click( '//a[@data-tab-slug="examples"]' );
        $this->_checkCommonElements( $I );

        // Click the 'Report' tab
        $I->click( '//a[@data-tab-slug="report"]' );
        $this->_checkCommonElements( $I );

        // See the 'About' tab
        $I->see( 
            '',
            '//a[@data-tab-slug="about"]' 
        );

        // Click the 'Debug' tab
        $I->click( '//a[@data-tab-slug="debug"]' );
        $this->_checkCommonElements( $I );        
        
    }    

}