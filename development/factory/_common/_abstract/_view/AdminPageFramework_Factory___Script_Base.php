<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides an abstract base to create an automatic script insertion class.
 * 
 * @since       3.3.0
 * @since       3.5.3      Extends `AdminPageFramework_WPUtility`.
 * @since       3.7.0      Renamed from `AdminPageFramework_Script_Base`.
 * @package     AdminPageFramework
 * @subpackage  Common/Factory/JavaScript
 * @internal
 * @extends     AdminPageFramework_FrameworkUtility
 */
abstract class AdminPageFramework_Factory___Script_Base extends AdminPageFramework_FrameworkUtility {
    
    public $oMsg;
    
    /**
     * Sets up hooks and properties.
     * 
     * It will enqueue the scrip in the footer.
     * 
     * @since       3.3.0
     */
    public function __construct( $oMsg=null ) {
                
        if ( $this->hasBeenCalled( get_class( $this ) ) ) {
            return;
        }
        
        $this->oMsg = $oMsg ? $oMsg : AdminPageFramework_Message::getInstance();
        
        // add_action( 'customize_controls_print_footer_scripts', array( $this, '_replyToPrintScript' ) );
        $this->registerAction(
            'customize_controls_print_footer_scripts', 
            array( $this, '_replyToPrintScript' )
        );
        
        $this->registerAction(
            'admin_print_footer_scripts', 
            array( $this, '_replyToPrintScript' )       
        );

        $this->registerAction(
            'wp_print_footer_scripts', 
            array( $this, '_replyToPrintScript' )       
        );
        
        $this->construct();
        
        add_action( 'wp_enqueue_scripts', array( $this, 'load' ) );
        
    }
    
    /**
     * The user constructor.
     * 
     * Enqueue dependencies with this method.
     *
     * @remark      This should be overridden in extended classes.
     * @since       3.3.0
     * @since       3.7.0      Changed the visibility scope from protected.
     * @return      void
     */
    public function construct() {}
    
    /**
     * @callback    wp_enqueue_script
     * @since       3.7.0
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
