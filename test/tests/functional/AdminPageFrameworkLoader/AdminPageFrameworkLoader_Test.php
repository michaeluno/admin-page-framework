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
        
        $_nStart = microtime( true );
        $_oAdminPage = new AdminPageFrameworkLoader_AdminPage(
            AdminPageFrameworkLoader_Registry::$aOptionKeys[ 'main' ],    // the option key
            AdminPageFrameworkLoader_Registry::$sFilePath   // caller script path        
        );
        $_nElapsed = microtime( true ) - $_nStart;
        codecept_debug( 'Elapsed seconds for instantiating ' . $_oAdminPage->oProp->sClassName . ': ' . $_nElapsed );  
        
        $this->assertEquals( 
            true, 
            method_exists( $_oAdminPage, 'setUp' )
        );
        $this->assertEquals( 
            true, 
            method_exists( $_oAdminPage, 'start' )
        );
        
    }   
    
    /**
     * @group       class
     */
    public function test_AdminPageFrameworkLoader_AdminPageWelcome() {
        
        $_nStart = microtime( true );
        $_oAdminPage = new AdminPageFrameworkLoader_AdminPageWelcome(
            '', // disable saving form data.
            AdminPageFrameworkLoader_Registry::$sFilePath   // caller script path        
        );
        $_nElapsed = microtime( true ) - $_nStart;
        codecept_debug( 'Elapsed seconds for instantiating ' . $_oAdminPage->oProp->sClassName . ': ' . $_nElapsed );  
        
        $this->assertEquals( 
            true, 
            method_exists( $_oAdminPage, 'setUp' )
        );
        $this->assertEquals( 
            true, 
            method_exists( $_oAdminPage, 'start' )
        );
        
        // Instantiation a Tab class which does not extend the framework class.
        $_nStart = microtime( true );
        $_oTab   = new AdminPageFrameworkLoader_AdminPageWelcome_Welcome( 
            $_oAdminPage,              // factory object
            AdminPageFrameworkLoader_Registry::$aAdminPages[ 'about' ],        // page slug
            array(
                'tab_slug'      => 'welcome',
                'style'         => array(
                    AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/admin.css',
                    AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/css/code.css',
                ),
            )                
        );     
        $_nElapsed = microtime( true ) - $_nStart;
        codecept_debug( 'Elapsed seconds for instantiating ' . get_class( $_oTab ) . ': ' . $_nElapsed );  
        
    }

    /**
     * @group   class
     */
    public function test_AdminPageFrameworkLoader_AdminPageMetaBox_Notification() {
        
        $_nStart = microtime( true );
        $_o = new AdminPageFrameworkLoader_AdminPageMetaBox_Notification(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Notification', 'admin-page-framework-loader' ), // title
            array( // page slugs
                AdminPageFrameworkLoader_Registry::$aAdminPages[ 'tool' ],
                AdminPageFrameworkLoader_Registry::$aAdminPages[ 'addon' ],
                AdminPageFrameworkLoader_Registry::$aAdminPages[ 'help' ],
            ),
            'side',                                       // context
            'default'                                     // priority
        );   
        $_nElapsed = microtime( true ) - $_nStart;
        codecept_debug( 'Elapsed seconds for instantiating ' . get_class( $_o ) . ': ' . $_nElapsed );
        
    }        
    
    /**
     * @group   class
     */
    public function test_AdminPageFrameworkLoader_AdminPageMetaBox_ExternalLinks() {
        
        $_nStart = microtime( true );
        $_o = new AdminPageFrameworkLoader_AdminPageMetaBox_ExternalLinks(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Resources', 'admin-page-framework-loader' ), // title
            array( // page slugs
                AdminPageFrameworkLoader_Registry::$aAdminPages[ 'help' ],
            ),
            'side',                                       // context
            'default'   
        );   
        $_nElapsed = microtime( true ) - $_nStart;
        codecept_debug( 'Elapsed seconds for instantiating ' . get_class( $_o ) . ': ' . $_nElapsed );
        
    }      

    /**
     * @group   class
     */
    public function test_AdminPageFrameworkLoader_NetworkAdmin() {
        
        $_nStart = microtime( true );
        $_o = new AdminPageFrameworkLoader_NetworkAdmin(
            AdminPageFrameworkLoader_Registry::$aOptionKeys[ 'main' ],    // the option key
            AdminPageFrameworkLoader_Registry::$sFilePath   // caller script path            
        );
        $_nElapsed = microtime( true ) - $_nStart;
        codecept_debug( 'Elapsed seconds for instantiating ' . get_class( $_o ) . ': ' . $_nElapsed );
        
    }        
    
    /**
     * @group   class
     */
    public function test_AdminPageFrameworkLoader_Demo() {
        
        $_nStart = microtime( true );
        $_o = new AdminPageFrameworkLoader_Demo;
        $_nElapsed = microtime( true ) - $_nStart;
        codecept_debug( 'Elapsed seconds for instantiating ' . get_class( $_o ) . ': ' . $_nElapsed );
        
    }    
    
    /**
     * @group   class
     */
    public function test_AdminPageFrameworkLoader_Event() {
        
        $_nStart = microtime( true );
        $_o = new AdminPageFrameworkLoader_Event;
        $_nElapsed = microtime( true ) - $_nStart;
        codecept_debug( 'Elapsed seconds for instantiating ' . get_class( $_o ) . ': ' . $_nElapsed );
        
    }
    
    /**
     * @group   class
     */
    public function test_AdminPageFramework_PointerToolTip() {
        
        $_nStart = microtime( true );
        $_o = new AdminPageFramework_PointerToolTip(
            array( 
                // screen ids
                'plugins', 
                
                // page slugs below
                'apfl_addons', 
            ),     
            'apf_demo_pointer_tool_box_activate_demo', // unique id for the pointer tool box
            array(    // pointer data
                'target'    => array(
                    '#activate-demo-action-link',
                    '#button-activate-demo', // multiple targets can be set with an array
                ), 
                'options'   => array(
                    'content' => sprintf( '<h3> %1$s </h3> <p> %2$s </p>',
                        AdminPageFrameworkLoader_Registry::NAME,
                        __( 'Check out the functionality of Admin Page Framework by enabling the demo.','admin-page-framework-loader' )
                    ),
                    'position'  => array( 'edge' => 'left', 'align' => 'middle' )
                )
            )
        );
        $_nElapsed = microtime( true ) - $_nStart;
        codecept_debug( 'Elapsed seconds for instantiating ' . get_class( $_o ) . ': ' . $_nElapsed );
        
    }    
    
    /**
     * @group   include
     */
    public function test_Demo() {
        
        $_nStart = microtime( true );
        include( AdminPageFrameworkLoader_Registry::$sDirPath . '/example/admin-page-framework-demo-bootstrap.php' );
        $_nElapsed = microtime( true ) - $_nStart;
        codecept_debug( 'Elapsed seconds for including demo components: ' . $_nElapsed );
        
    }        
    
}
