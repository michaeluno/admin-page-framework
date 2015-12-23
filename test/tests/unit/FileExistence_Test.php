<?php
/**
 * Manually include the bootstrap script as Codeception bootstrap runs after loading this file.
 * @see https://github.com/Codeception/Codeception/issues/862
 */
// include_once( dirname( __FILE__ ) . '/_bootstrap.php' );

/**
 * @group sample_test_plugin
 */

class FileExistence_Test extends \Codeception\TestCase\Test {

    /**
     * The utility object to test.
     */
    // protected $oUtil; 
   
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before() {}

    protected function _after() {}

    // tests
    /**
     * Checks files exist.
     */
    public function testFileExists() {
        
        $this->assertFileExists( $GLOBALS['_sProjectDirPath'] . '/admin-page-framework-loader.php' );
        $this->assertFileExists( $GLOBALS['_sProjectDirPath'] . '/development/admin-page-framework.php' );
        $this->assertFileExists( $GLOBALS['_sProjectDirPath'] . '/development/admin-page-framework-include-class-list.php' );
        $this->assertFileExists( $GLOBALS['_sProjectDirPath'] . '/development/LICENSE.txt' );
        $this->assertFileExists( $GLOBALS['_sProjectDirPath'] . '/library/apf/admin-page-framework.php' );
        $this->assertFileExists( $GLOBALS['_sProjectDirPath'] . '/library/apf/admin-page-framework-include-class-list.php' );
        
    }
 
}
