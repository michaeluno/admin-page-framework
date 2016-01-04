<?php
/**
 * Manually include the bootstrap script as Codeception bootstrap runs after loading this file.
 * @see https://github.com/Codeception/Codeception/issues/862
 */
include_once( dirname( dirname( __FILE__ ) ) . '/_bootstrap.php' );

/**
 * @group   factory
 * @group   utility
 * @group   loader
 */
class AdminPageFramework_Loader_Test extends \WP_UnitTestCase {
    
    /**
     * Sores the utility object.
     */
    public $oUtil;
    
    public function setUp() {
        
        parent::setUp();
        
    }

    public function tearDown() {
        parent::tearDown();
    }
    
    /**
     * @group   method_exists
     */
    public function test_AdminPageFrameworkLoader_AdminPage() {
        
        $_oAdminPage = new AdminPageFrameworkLoader_AdminPage;
        
        $this->assertEquals( 
            true, 
            method_exists( $_oAdminPage, 'setUp' )
        );
        $this->assertEquals( 
            true, 
            method_exists( $_oAdminPage, 'start' )
        );
        
    }   
    

}