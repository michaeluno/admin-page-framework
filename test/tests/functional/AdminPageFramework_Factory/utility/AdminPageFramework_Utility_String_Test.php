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
class AdminPageFramework_Utility_String_Test extends \APF_UnitTestCase {
    
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
    
    public function test_getLengthSanitized() {
               
        $this->assertEquals( '80px', $this->oUtil->getLengthSanitized( 80 ) );
        $this->assertEquals( '80em', $this->oUtil->getLengthSanitized( 80 , 'em' ) );
        $this->assertEquals( '0%', $this->oUtil->getLengthSanitized( 0 , '%' ) );
        $this->assertEquals( '0px', $this->oUtil->getLengthSanitized( 0 ) );
        $this->assertEquals( '0px', $this->oUtil->getLengthSanitized( '0' ) );
        $this->assertEquals( '0px', $this->oUtil->getLengthSanitized( null ) );
        $this->assertEquals( '0px', $this->oUtil->getLengthSanitized( '' ) );
        $this->assertEquals( '0px', $this->oUtil->getLengthSanitized( false ) );      
        
    }   
    
    public function test_sanitizeSlug() {
        
        $this->assertEquals( 'foo_bar', $this->oUtil->sanitizeSlug( 'foo-bar' ) );
        $this->assertEquals( '_Foo_Bar', $this->oUtil->sanitizeSlug( '\Foo\Bar' ) );
        $this->assertEquals( null, $this->oUtil->sanitizeSlug( null ) );
        
    }
    
    public function test_sanitizeString() {
        
        $this->assertEquals( 'foo-bar', $this->oUtil->sanitizeString( 'foo-bar' ) );
        $this->assertEquals( '_Foo_Bar', $this->oUtil->sanitizeString( '\Foo\Bar' ) );
        $this->assertEquals( null, $this->oUtil->sanitizeString( null ) );
        
    }    
    
    /**
     *```
     * fixNumber( $nToFix, $nDefault, $nMin='', $nMax='' )
     *```
     * 
     */
    public function test_fixNumber() {
        
        $this->specify( 
            "Checking the default behavior.", 
            function() {
                $this->assertEquals( 10, $this->oUtil->fixNumber( 10, 5 ) );
            }
        );
        
        $this->specify( 
            "Checking the `max` parameter.", 
            function() {
                $this->assertEquals( 8, $this->oUtil->fixNumber( 10, 5, 4, 8 ) );
            }
        );

        $this->specify( 
            "Checking the `min` parameter.", 
            function() {
                $this->assertEquals( 4, $this->oUtil->fixNumber( 2, 5, 4, 8 ) );
            }
        );

        $this->specify( 
            "Checking with null.", 
            function() {
                $this->assertEquals( 5, $this->oUtil->fixNumber( null, 5, 4, 8 ) );
            }
        );        

        $this->specify( 
            "Checking with a string.", 
            function() {
                $this->assertEquals( 5, $this->oUtil->fixNumber( 'string value', 5, 4, 8 ) );
                $this->assertEquals( 8, $this->oUtil->fixNumber( '15', 5, 4, 8 ) );
                $this->assertEquals( '6', $this->oUtil->fixNumber( '6', 5, 4, 8 ) );
            }
        );        
                        
    }    
    
    public function test_getCSSMinified() {
        
        $this->specify( 
            "Checking the default behavior.", 
            function() {
                $_sCSS = <<<CSS
    .foo-bar {
        white-space: pre;
    }
CSS;
                $this->assertEquals( 
                    '.foo-bar {white-space: pre;}',
                    $this->oUtil->getCSSMinified( $_sCSS ) 
                );
            }
        );           
        
    }
    
    public function test_getStringLength() {
        
        $this->specify( 
            "Checking the default behavior.", 
            function() {
                $this->assertEquals( 12, $this->oUtil->getStringLength( 'hello world!' ) );
            }
        );

        $this->specify( 
            "Checking a unicode string length.", 
            function() {
                $this->assertEquals( 6, $this->oUtil->getStringLength( 'ワードプレス' ) );
                $this->assertEquals( 22, $this->oUtil->getStringLength( 'ワードプレス means WordPress' ) );
            }
        );
        
    }
    
    public function test_getNumberOfReadableSize() {
                
        $this->assertEquals( 20971520, $this->oUtil->getNumberOfReadableSize( '20m' ) );
        $this->assertEquals( 331776, $this->oUtil->getNumberOfReadableSize( '324k' ) );
        
        $this->assertEquals( '20M', $this->oUtil->getNumberOfReadableSize( '20MB' ) );
        
    }
    
    public function test_getReadableBytes() {

        $this->assertEquals( '20MB', $this->oUtil->getReadableBytes( 20971520 ) );
        $this->assertEquals( '20MB', $this->oUtil->getReadableBytes( '20971520' ) );
        $this->assertEquals( '324KB', $this->oUtil->getReadableBytes( '331776' ) );    
        
    }
    
    public function test_getPrefixRemoved() {
        
        $this->assertEquals( 'Testing', $this->oUtil->getPrefixRemoved( 'AdminPageFramework_Testing', 'AdminPageFramework_' ) );
        $this->assertEquals( 'AdminPageFramework_Testing', $this->oUtil->getPrefixRemoved( 'AdminPageFramework_Testing', 'adminfageframework_' ) );
        $this->assertEquals( 'AdminPageFramework_Test', $this->oUtil->getPrefixRemoved( 'AdminPageFramework_Test', 'APF' ) );
        
        // Somehow `\` causes a test to fail so substitute with `|`.
        $this->assertEquals( 'AdminPageFramework_Test', $this->oUtil->getPrefixRemoved( 'Foo|AdminPageFramework_Test', 'Foo|' ) );
        $this->assertEquals( 'Foo|AdminPageFramework_Test', $this->oUtil->getPrefixRemoved( 'Foo|AdminPageFramework_Test', '|' ) );
        $this->assertEquals( 'Foo|AdminPageFramework_Test', $this->oUtil->getPrefixRemoved( 'Foo|AdminPageFramework_Test', 'Test' ) );
        $this->assertEquals( 'Foo|AdminPageFramework_Test', $this->oUtil->getPrefixRemoved( 'Foo|AdminPageFramework_Test', '.' ) );
        
    }
 
    public function test_getSuffixRemoved() {
        
        $this->assertEquals( 'AdminPageFramework', $this->oUtil->getSuffixRemoved( 'AdminPageFramework_Testing', '_Testing' ) );
        $this->assertEquals( 'AdminPageFramework_Testing', $this->oUtil->getSuffixRemoved( 'AdminPageFramework_Testing', '_testing' ) );
        $this->assertEquals( 'AdminPageFramework_Test', $this->oUtil->getSuffixRemoved( 'AdminPageFramework_Test', 'Tst' ) );        
        
    }

    public function test_hasPrefix() {
        
        $this->assertEquals( true, $this->oUtil->hasPrefix( 'AdminPageFramework_', 'AdminPageFramework_Testing' ) );
        $this->assertEquals( false, $this->oUtil->hasPrefix( 'APF_', 'AdminPageFramework_Testing' ) );
        $this->assertEquals( false, $this->oUtil->hasPrefix( '_Testing', 'AdminPageFramework_Testing' ) );
        $this->assertEquals( true, $this->oUtil->hasPrefix( '', 'AdminPageFramework_Testing' ) );
        $this->assertEquals( true, $this->oUtil->hasPrefix( null, 'AdminPageFramework_Testing' ) );
        $this->assertEquals( true, $this->oUtil->hasPrefix( false, 'AdminPageFramework_Testing' ) );
        
    }

    public function test_hasSuffix() {
        
        $this->assertEquals( true, $this->oUtil->hasSuffix( '_Testing', 'AdminPageFramework_Testing' ) );
        $this->assertEquals( false, $this->oUtil->hasSuffix( '_Test', 'AdminPageFramework_Testing' ) );
        $this->assertEquals( false, $this->oUtil->hasSuffix( 'AdminPageFramework_', 'AdminPageFramework_Testing' ) );
        $this->assertEquals( true, $this->oUtil->hasSuffix( '', 'AdminPageFramework_Testing' ) );
        $this->assertEquals( true, $this->oUtil->hasSuffix( null, 'AdminPageFramework_Testing' ) );
        $this->assertEquals( true, $this->oUtil->hasSuffix( false, 'AdminPageFramework_Testing' ) );
        
    }
    
}
