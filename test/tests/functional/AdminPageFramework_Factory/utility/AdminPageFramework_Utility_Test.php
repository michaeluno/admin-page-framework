<?php
/**
 * Manually include the bootstrap script as Codeception bootstrap runs after loading this file.
 * @see https://github.com/Codeception/Codeception/issues/862
 */
include_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/_bootstrap.php' );

/**
 * @group   factory
 * @group   utility
 * @group   core
 */
class AdminPageFramework_Utility_Test extends \APF_UnitTestCase {
    
    use \Codeception\Specify;
    
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
    
    public function test_callBack() {
               
        $this->specify(
            'When a callback is not callable, the first parameter value is returned.', 
            function() {
                $this->assertEquals( 
                    "first_value", 
                    $this->oUtil->callBack( array(), array( "first_value", "second_value" ) )
                );
            }
        );
        
        $this->specify( 
            'When a callback is not callable, the first parameter value is returned.', 
            function() {
                $this->assertEquals( 
                    'returned_value_from_callback', 
                    $this->oUtil->callBack( array( $this, '_replyToTest' ), array( 'first_value', 'second_value' ) )
                );
            }
        );
        
    }
        public function _replyToTest( $sParam1, $sParam2 ) {
            return 'returned_value_from_callback';
        }
    
   
    public function test_hasBeenCalled() {
        
        $this->specify(
            'Checking the default behavior.', 
            function() {
                $this->assertEquals( 
                    false, 
                    $this->oUtil->hasBeenCalled( 'foo' )
                );
                $this->assertEquals( 
                    true, 
                    $this->oUtil->hasBeenCalled( 'foo' )
                );
            }
        );    
    
    }
    
    public function test_getOutputBuffer() {
        
        $this->specify(
            'Checking the default behavior.', 
            function() {
                $this->assertEquals( 
                    'Printing: Testing', 
                    $this->oUtil->getOutputBuffer( array( $this, '_replyToPrintSomething' ), array( 'Testing' ) )
                );
            }
        );                    
        
    }
        public function _replyToPrintSomething( $sParameter ) {
            echo 'Printing: ' . $sParameter;
        }
    
    public function test_getObjectInfo() {
        
        $this->specify(
            'Checking the default behavior.', 
            function() {
                $this->assertEquals( 
                    '(object) AdminPageFramework_ArrayHandler: 2 properties.', 
                    $this->oUtil->getObjectInfo( new AdminPageFramework_ArrayHandler )
                );
            }
        );           
        
    }
    
    public function test_getAOrB() {
        
        $this->specify(
            'Checking the default behavior.', 
            function() {
                $this->assertEquals( 
                    'A', 
                    $this->oUtil->getAOrB( true, 'A', 'B' )
                );
                $this->assertEquals( 
                    'B', 
                    $this->oUtil->getAOrB( false, 'A', 'B' )
                );                
            }
        );                   
        
    }
    
    
}
