<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * The factory class that creates network admin pages.
 *
 * @abstract
 * @since       3.1.0
 * @remark      This class stems from several abstract classes.
 * @extends     AdminPageFramework
 * @package     AdminPageFramework
 * @subpackage  NetworkAdmin
 */
abstract class AdminPageFramework_NetworkAdmin extends AdminPageFramework {
       
    /**
     * Defines the class object structure type.
     * 
     * @since       3.7.12      
     * @internal
     */
    protected $_sStructureType = 'network_admin_page';
       
    /**
     * Used to refer the built-in root menu slugs.
     * 
     * @since       3.1.0
     * @remark      Not for the user.
     * @var         array Holds the built-in root menu slugs.
     * @internal
     */
    protected $_aBuiltInRootMenuSlugs = array(
        // All keys must be lower case to support case insensitive look-ups.
        'dashboard'     => 'index.php',
        'sites'         => 'sites.php',         // not work
        'themes'        => 'themes.php',        // not work
        'plugins'       => 'plugins.php',
        'users'         => 'users.php',
        'settings'      => 'settings.php',
        'updates'       => 'update-core.php',   // does not work
    );
        
    /**
     * Registers necessary callbacks ans sets up internal components including properties.
     * 
     * <h4>Example</h4>
     * <code>if ( is_admin() )
     *     new MyAdminPageClass( 'my_custom_option_key', __FILE__ );</code>
     * 
     * @access      public
     * @since       3.1.0
     * @see         http://codex.wordpress.org/Roles_and_Capabilities
     * @see         http://codex.wordpress.org/I18n_for_WordPress_Developers#Text_Domains
     * @param       string      $sOptionKey         (optional) specifies the option key name to store in the options table. If this is not set, the instantiated class name will be used.
     * @param       string      $sCallerPath        (optional) used to retrieve the plugin/theme details to auto-insert the information into the page footer.
     * @param       string      $sCapability        (optional) sets the overall access level to the admin pages created by the framework. The used capabilities are listed <a href="http://codex.wordpress.org/Roles_and_Capabilities">here</a>. The capability can be set per page, tab, setting section, setting field. Default: `manage_options.`
     * @param       string      $sTextDomain        (optional) the <a href="http://codex.wordpress.org/I18n_for_WordPress_Developers#Text_Domains" target="_blank">text domain</a> used for the framework's system messages. Default: `admin-page-framework`.
     * @return      void        returns nothing.
     */
    public function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability='manage_network', $sTextDomain='admin-page-framework' ){
            
        if ( ! $this->_isInstantiatable() ) {
            return;
        }
                    
        $sCallerPath = $sCallerPath
            ? $sCallerPath
            : AdminPageFramework_Utility::getCallerScriptPath( __FILE__ );     // this is important to attempt to find the caller script path here when separating the library into multiple files.

        // $_sProprtyClassName = $this->aSubClassNames[ 'oProp' ] 
            // ? $this->aSubClassNames[ 'oProp' ] 
            // : 'AdminPageFramework_Property_' . $this->_sStructureType;

        // $this->oProp = new $_sProprtyClassName( 
            // $this, 
            // $sCallerPath, 
            // get_class( $this ), 
            // $sOptionKey, 
            // $sCapability,
            // $sTextDomain,
            // $this->_sStructureType
        // );

        parent::__construct( $sOptionKey, $sCallerPath, $sCapability, $sTextDomain );
        
        new AdminPageFramework_Model_Menu__RegisterMenu( $this, 'network_admin_menu' );
        
    }
    
    /**
     * Instantiates a link object based on the type.
     * 
     * @since       3.7.10
     * @internal
     * @return      null|object
     */
    protected function _getLinkObject() {
        $_sClassName = $this->aSubClassNames[ 'oLink' ];

        return new $_sClassName( $this->oProp, $this->oMsg );
    }
    
    /**
     * Instantiates a link object based on the type.
     * 
     * @since       3.7.10
     * @internal
     * @return      null|object
     */
    protected function _getPageLoadObject() {
        $_sClassName = $this->aSubClassNames[ 'oPageLoadInfo' ];

        return new $_sClassName( $this->oProp, $this->oMsg );
    }

    /**
     * Checks whether the class should be instantiated.
     * 
     * @since       3.1.0
     * @internal
     */
    protected function _isInstantiatable() {
        
        if ( isset( $GLOBALS['pagenow'] ) && 'admin-ajax.php' === $GLOBALS['pagenow'] ) {
            return false;
        }
        
        // Nothing to do in the non-network admin area.
        if ( is_network_admin() ) {
            return true;
        }
        
        return false;
        
    }
    
    
    /**
     * Retrieves the saved option value from the given option key and the dimensional array key representation.
     * 
     * <h4>Example</h4>
     * <code>
     * $aData       = AdminPageFramework::getOption( 'APF' );
     * $aSection    = AdminPageFramework::getOption( 'APF', 'my_section' );
     * $sText       = AdminPageFramework::getOption( 'APF', array( 'my_section', 'my_text_field' ), 'foo' );
     * $sColor      = AdminPageFramework::getOption( 'APF', 'my_color_field', '#FFF' );
     * </code>
     * 
     * @since       3.1.0
     * @param       string      $sOptionKey     the option key of the options table.
     * @param       string      $asKey          the representation of dimensional array keys. If the returning option structure is like the following,
     * <code>
     * array(
     *     'a' => array(
     *         'b' => array(
     *             'c' => 'ccc',
     *         ),
     *     ),
     * )
     * </code>
     * then the value 'ccc' can be retrieved with the key representation array of 
     * <code>
     * array( 'a', 'b', 'c' )
     * </code>
     * @param       mixed       $vDefault     the default value that will be returned if nothing is stored.
     * @return      mixed       If the field ID is not specified.
     */
    static public function getOption( $sOptionKey, $asKey=null , $vDefault=null ) {
        return AdminPageFramework_WPUtility::getSiteOption( $sOptionKey, $asKey, $vDefault );
    }
    
}
