<?php
/**
 * Manually include the bootstrap script as Codeception bootstrap runs after loading this file.
 * @see https://github.com/Codeception/Codeception/issues/862
 */
include_once( dirname( dirname( __FILE__ ) ) . '/_bootstrap.php' );

/**
 * @group   boot
 * @group   core
 */
class AdminPageFramework_Test extends \WP_UnitTestCase {
    
    /**
     * Sores the utility object.
     */
    public $oUtil;
    
    public function setUp() {
     
        codecept_debug( 'Test Dir: ' . $GLOBALS[ '_sTestsDirPath' ] );  
        codecept_debug( 'WordPress Debug Mode: ' . ( defined( 'WP_DEBUG' ) && WP_DEBUG ) );  
     
        parent::setUp();
        
    }

    public function tearDown() {
        parent::tearDown();
    }
    

    /**
     * @group   class_exists
     */
    public function test_AdminPageFramework_class_exists() {
        
        codecept_debug( AdminPageFramework_Registry::NAME . ' ' . AdminPageFramework_Registry::getVersion() );
        codecept_debug( AdminPageFramework_Registry::$sFilePath );
          
        $this->assertTrue( count( AdminPageFramework_Registry::$aClassFiles ) > 0 );
        
        $_iTotalCount = count( AdminPageFramework_Registry::$aClassFiles );
        codecept_debug( 'total classes: ' . $_iTotalCount );
        
        // Elapsed time to loop through files.
        $_iMicrotime1 = microtime( true );
        $_iCount = 0;
        $_iCountDidNotExist = 0;
        foreach( AdminPageFramework_Registry::$aClassFiles as $_sClassName => $_sClassPath ) {
            // $this->assertTrue( class_exists( $_sClassName ) );            
            if ( class_exists( $_sClassName, false ) ) {
                $_iCount++;
                // codecept_debug( $_iCount . '. Class ALready Exists: ' . $_sClassName );
            } else {
                $_iCountDidNotExist++;
            }        
        }
        $_iMicrotime2 = microtime( true );
        codecept_debug( 'Classes Already Existed: ' . $_iCount  );
        codecept_debug( 'Classes Do Not Exist: ' . $_iCountDidNotExist . ' (' . ( $_iTotalCount - $_iCount ) . ')' );
        codecept_debug( 'the elapsed time to check all the ' . $_iTotalCount . ' classes exist.' . ( $_iMicrotime2 - $_iMicrotime1 ) );

        // Elapsed time to check file existence
        $_iMicrotime1 = microtime( true );
        foreach( AdminPageFramework_Registry::$aClassFiles as $_sClassName => $_sClassPath ) {
            $this->assertTrue( file_exists( $_sClassPath ) );
        }
        $_iMicrotime2 = microtime( true );
        codecept_debug( 'the elapsed time to check existence of all ' . $_iTotalCount . ' files: ' . ( $_iMicrotime2 - $_iMicrotime1 ) );
        
        // Elapsed time to include files. This will trigger spl autoloader and parent base classes will be automatically included.
        $_iMicrotime1 = microtime( true );
        $_iCount = 0;
        $_iAlreadyIncluded = 0;
        foreach( AdminPageFramework_Registry::$aClassFiles as $_sClassName => $_sClassPath ) {
            if ( ! class_exists( $_sClassName, false ) ) {
                $_iCount++;
                // codecept_debug( $_iCount . '. ' . $_sClassName );
                include( $_sClassPath );
            } else {
                $_iAlreadyIncluded++;
                // codecept_debug( 'already included: ' . $_sClassName );
            }
        }
        $_iMicrotime2 = microtime( true );
        codecept_debug( 'Already Included: ' . $_iAlreadyIncluded );
        codecept_debug( 'Has not Been Included: ' . $_iCount );
        codecept_debug( 'the elapsed time to include un-included ' . $_iCount . ' files: ' . ( $_iMicrotime2 - $_iMicrotime1 ) );
        
    }   
    
}
