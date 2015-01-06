<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides an abstract base to create an automatic script insertion class.
 * 
 * @since       3.3.0
 * @package     AdminPageFramework
 * @subpackage  JavaScript
 * @internal
 */
class AdminPageFramework_Script_Base {

    /**
     * Stores the enqueued script class names.
     * 
     * @since       3.3.0
     */
    static public $_aEnqueued = array();
    
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
        
        $this->oMsg = $oMsg;
        
        add_action( 'customize_controls_print_footer_scripts', array( $this, '_replyToPrintScript' ) );
        add_action( 'admin_footer', array( $this, '_replyToPrintScript' ) );        
        
        $this->construct();
        
    }
    
    /**
     * The user constructor.
     * 
     * Enqueue dependencies which this method.
     *
     * @remark      This should be overridden in extended classes.
     * @since       3.3.0
     */
    protected function construct() {}
    
    /**
     * Prints the script.
     * 
     * @since       3.3.0
     */
    public function _replyToPrintScript() {
        $_sScript = $this->getScript( $this->oMsg );
        if ( ! $_sScript ) {
            return;
        }
        echo "<script type='text/javascript' class='" . strtolower( get_class( $this ) ) . "'>"
                . $_sScript
            . "</script>";
    }
    
    /**
     * Returns the JavaScript script.
     * 
     * @remark      Extended classes just override this method and return the script.
     * @since       3.3.0
     * @param       $oMsg           object      The message object.
     */
    static public function getScript() {
        $_aParams   = func_get_args() + array( null );
        $_oMsg      = $_aParams[ 0 ];                 
        return "";  
    }

}