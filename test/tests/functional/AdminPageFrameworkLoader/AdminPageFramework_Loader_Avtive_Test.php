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
class AdminPageFramework_Loader_Active_Test extends \WP_UnitTestCase {
    
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
     * Check if the plugin is activated.
     */
    public function test_is_active() {
        
        $this->assertTrue(
            is_plugin_active( 'admin-page-framework/admin-page-framework-loader.php' )
        );
        
    }

}
