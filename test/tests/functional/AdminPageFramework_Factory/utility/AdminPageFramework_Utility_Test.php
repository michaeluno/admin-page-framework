<?php
/**
 * Manually include the bootstrap script as Codeception bootstrap runs after loading this file.
 * @see https://github.com/Codeception/Codeception/issues/862
 */
include_once dirname( dirname( dirname( __FILE__ ) ) ) . '/_bootstrap.php';

/**
 * @group   factory
 * @group   utility
 * @group   core
 */
class AdminPageFramework_Utility_Test extends \WP_UnitTestCase {
    
    /**
     * Sores the utility object.
     */
    public $oUtil;
    
    public function setUp() {
        
        parent::setUp();
        
        $this->oUtil = new AdminPageFramework_WPUtility;
        
    }

    public function tearDown() {
        parent::tearDown();
    }
    
    public function test_sanitizeLength() {
        
        $this->assertEquals( '80px', $this->oUtil->sanitizeLength( 80 ) );
        $this->assertEquals( '80em', $this->oUtil->sanitizeLength( 80 , 'em' ) );
        $this->assertEquals( '0%', $this->oUtil->sanitizeLength( 0 , '%' ) );
        
    }
    

}
