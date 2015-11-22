<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides an abstract base to create an automatic script insertion class.
 * 
 * @since       3.3.0
 * @since       3.5.3       Extends `AdminPageFramework_WPUtility`.
 * @since       DEVVER      Ranamed from `AdminPageFramework_Script_Base`.
 * @package     AdminPageFramework
 * @subpackage  JavaScript
 * @internal
 * @extends     AdminPageFramework_WPUtility
 */
abstract class AdminPageFramework_Factory___Script_Base extends AdminPageFramework_WPUtility {

    /**
     * Stores the enqueued script class names.
     * 
     * @since       3.3.0
     */
    static public $_aEnqueued = array();
    
    public $oMsg;
    
    /**
     * Sets up hooks and properties.
     * 
     * It will enqueue the scrip in the footer.
     * 
     * @since       3.3.0
     */
    public function __construct( $oMsg=null ) {
        
        $_sClassName = get_class( $this );
        if ( in_array( $_sClassName, self::$_aEnqueued ) ) {
            return;
        }
        self::$_aEnqueued[ $_sClassName ] = $_sClassName;
        $this->oMsg = $oMsg ? $oMsg : AdminPageFramework_Message::getInstance();
        
        // add_action( 'customize_controls_print_footer_scripts', array( $this, '_replyToPrintScript' ) );
        $this->registerAction(
            'customize_controls_print_footer_scripts', 
            array( $this, '_replyToPrintScript' )
        );
        
        $this->registerAction(
            'admin_footer', 
            array( $this, '_replyToPrintScript' )       
        );        
        
        $this->construct();
        
        $this->registerAction( 'wp_enqueue_scripts', array( $this, 'load' ) );
        
    }
    
    /**
     * The user constructor.
     * 
     * Enqueue dependencies with this method.
     *
     * @remark      This should be overridden in extended classes.
     * @since       3.3.0
     * @since       DEVVER      Changed the visibility scope from protected.
     * @return      void
     */
    public  function construct() {}
    
    /**
     * @callback    wp_enqueue_script
     * @since       DEVVER
     */
    public function load() {}
    
    /**
     * Prints the script.
     * 
     * @since       3.3.0
     * @internal
     * @return      string      The generated HTML script tag.
     * @callback    action      admin_footer
     * @callback    action      customize_controls_print_footer_scripts
     */
    public function _replyToPrintScript() {
        $_sScript = $this->getScript( $this->oMsg );
        if ( ! $_sScript ) {
            return;
        }
        echo "<script type='text/javascript' class='" . strtolower( get_class( $this ) ) . "'>"
                . '/* <![CDATA[ */'
                . $_sScript
                . '/* ]]> */'
            . "</script>";
    }
    
    /**
     * Returns an inline JavaScript script.
     * 
     * @remark      Extended classes just override this method and return the script.
     * @since       3.3.0
     * @param       $oMsg       object      The message object.
     * @return      string      The inline JavaScript script.
     */
    static public function getScript( /* $oMsg */ ) {
        $_aParams   = func_get_args() + array( null );
        $_oMsg      = $_aParams[ 0 ];                 
        return "";  
    }

}