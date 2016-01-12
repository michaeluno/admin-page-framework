<?php
/**
 * Manually include the bootstrap script as Codeception bootstrap runs after loading this file.
 * @see https://github.com/Codeception/Codeception/issues/862
 */
include_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/_bootstrap.php' );

class AdminPageFramework_Property_FuncitonalTest extends AdminPageFramework_Property_Base {}

/**
 * @group   factory
 * @group   utility
 * @group   core
 */
class AdminPageFramework_Property_Base_Test extends \WP_UnitTestCase {
    
    private $_oProp;
  
    public function setUp() {
        
        parent::setUp();
        
        $this->_oProp = AdminPageFramework_ClassTester::getInstance(
            'AdminPageFramework_Property_FuncitonalTest',
            array(  // these parameters are just passed not to fail the object instantiation
                $this, 
                __FILE__, 
                get_class( $this ), 
                'manage_options', 
                'admin-page-framework', 
                'admin_page'
            )
        );        
        
    }

    public function tearDown() {
        parent::tearDown();
    }
        
    public function test__getCallerType() {
        
        $_sCallerType      = AdminPageFramework_ClassTester::call(
            $this->_oProp,
            '_getCallerType',
            array(
                'abc/efg/hij/klm/opq/rstu',        // 1st parameter
            )
        );
        $this->assertEquals( 'unknown', $_sCallerType );
        $_sCallerType      = AdminPageFramework_ClassTester::call(
            $this->_oProp,
            '_getCallerType',
            array(
                'abc\efg\hij\klm\opq\rstu',        // 1st parameter
            )
        );
        $this->assertEquals( 'unknown', $_sCallerType );
        
        $_sCallerType      = AdminPageFramework_ClassTester::call(
            $this->_oProp,
            '_getCallerType',
            array(
                'abc/efg/plugins/klm/opq/rstu',        // 1st parameter
            )
        );
        $this->assertEquals( 'plugin', $_sCallerType );
        $_sCallerType      = AdminPageFramework_ClassTester::call(
            $this->_oProp,
            '_getCallerType',
            array(
                'abc\efg\plugins\klm\opq\rstu',        // 1st parameter
            )
        );
        $this->assertEquals( 'plugin', $_sCallerType );
        
        $_sCallerType      = AdminPageFramework_ClassTester::call(
            $this->_oProp,
            '_getCallerType',
            array(
                'abc/efg/hij/themes/opq/rstu',        // 1st parameter
            )
        );
        $this->assertEquals( 'theme', $_sCallerType );
        $_sCallerType      = AdminPageFramework_ClassTester::call(
            $this->_oProp,
            '_getCallerType',
            array(
                'abc\efg\hij\themes\opq\rstu',        // 1st parameter
            )
        );
        $this->assertEquals( 'theme', $_sCallerType );
        
    }   
    
    /**
     * array(
        'sPath'         => ...,
        'sType'         => ...,
        'sName'         => ...,     
        'sURI'          => ...,
        'sVersion'      => ...,
        'sThemeURI'     => ...,
        'sScriptURI'    => ...,
        'sAuthorURI'    => ...,
        'sAuthor'       => ...,
        'sDescription'  => ...,
       )
     */
    public function test_getCallerInfo() {
        
        $_aCallerData      = AdminPageFramework_ClassTester::call(
            $this->_oProp,
            'getCallerInfo',
            array(
                AdminPageFrameworkLoader_Registry::$sFilePath,        // 1st parameter
            )
        );

        $this->assertEquals( AdminPageFrameworkLoader_Registry::$sFilePath, $_aCallerData[ 'sPath' ] );
        $this->assertEquals( 'plugin', $_aCallerData[ 'sType' ] );
        $this->assertEquals( AdminPageFrameworkLoader_Registry::NAME, $_aCallerData[ 'sName' ] );
        $this->assertEquals( AdminPageFrameworkLoader_Registry::URI, $_aCallerData[ 'sURI' ] );
        $this->assertEquals( AdminPageFrameworkLoader_Registry::VERSION, $_aCallerData[ 'sVersion' ] );
        $this->assertEquals( AdminPageFrameworkLoader_Registry::AUTHOR_URI, $_aCallerData[ 'sAuthorURI' ] );
        $this->assertEquals( AdminPageFrameworkLoader_Registry::DESCRIPTION, $_aCallerData[ 'sDescription' ] );
        
    }
    
    /**
            'sName'         => AdminPageFramework_Registry::NAME,
            'sURI'          => AdminPageFramework_Registry::URI,
            'sScriptName'   => AdminPageFramework_Registry::NAME,
            'sLibraryName'  => AdminPageFramework_Registry::NAME,
            'sLibraryURI'   => AdminPageFramework_Registry::URI,
            'sVersion'      => AdminPageFramework_Registry::getVersion(),
            'sDescription'  => AdminPageFramework_Registry::DESCRIPTION,
            'sAuthor'       => AdminPageFramework_Registry::AUTHOR,
            'sAuthorURI'    => AdminPageFramework_Registry::AUTHOR_URI,
            'sTextDomain'   => AdminPageFramework_Registry::TEXT_DOMAIN,
            'sDomainPath'   => AdminPageFramework_Registry::TEXT_DOMAIN_PATH,
     */
    public function test__getLibraryData() {
        
        $_aLibraryData = AdminPageFramework_ClassTester::call(
            $this->_oProp,      // class object instance
            '_getLibraryData',  // method name
            array() // parameters
        );

        $this->assertEquals( AdminPageFramework_Registry::NAME, $_aLibraryData[ 'sName' ] );
        $this->assertEquals( AdminPageFramework_Registry::URI, $_aLibraryData[ 'sURI' ] );
        $this->assertEquals( AdminPageFramework_Registry::getVersion(), $_aLibraryData[ 'sVersion' ] );
        $this->assertEquals( AdminPageFramework_Registry::AUTHOR_URI, $_aLibraryData[ 'sAuthorURI' ] );
        $this->assertEquals( AdminPageFramework_Registry::DESCRIPTION, $_aLibraryData[ 'sDescription' ] );
        
    }
    

}
