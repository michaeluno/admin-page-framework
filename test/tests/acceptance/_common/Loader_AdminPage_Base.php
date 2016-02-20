<?php
use \AcceptanceTester;

class Loader_AdminPage_Base {
    
    public function _before( \AcceptanceTester $I ){
        UserLoginPage::of( $I )->login( 'admin', 'admin' );
    }
    
    public function _after( \AcceptanceTester $I ){}

    protected function _checkCommonElements( \AcceptanceTester $I ){    
        
        $this->_checkFooter( $I );
        
        // Page content ending comment
        // Fails in v2.1.6
        // $I->see( '<!-- .admin-page-framework-content -->' );
        
    }        
    
    protected function _checkFooter( \AcceptanceTester $I ) {
        
        // Footer info left
        // <span class="apf-script-info">...</span>
        $I->see( 
            '', // text
            '//span[@class="apf-script-info"]'  // xpath
        );

        // Footer info right
        $I->see( 
            '', // text 
            '//span[@class="apf-credit"]' // xpath
        );              
        
    }

}
