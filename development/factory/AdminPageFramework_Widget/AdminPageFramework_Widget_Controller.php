<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Widget_Controller' ) ) :
/**
 * Provides methods of views for the widget factory class.
 * 
 * Those methods are public and provides means for users to set property values.
 * 
 * @abstract
 * @since       3.2.0
 * @package     AdminPageFramework
 * @subpackage  Widget
 */
abstract class AdminPageFramework_Widget_Controller extends AdminPageFramework_Widget_View {    

    /**
     * Sets up hooks and properties.
     * @since   3.2.0
     */
    function __construct( $oProp ) {
        
        parent::__construct( $oProp );
        
        if ( $this->_isInThePage() ) :
            
            // Other admin page framework factory classes uses wp_loaded hook but widget_init hook is called before that.
            // So we use widgets_init hook for this factory.
            if ( did_action( 'widgets_init' ) ) {  // For the activation hook.
                $this->setup_pre();
            } {
                add_action( 'widgets_init', array( $this, 'setup_pre' ) );     
            }
            
        endif;
        
    }

    /**
    * The method for all necessary set-ups.
    * 
    * @abstract
    * @since        3.2.0
    */
    public function setUp() {}    
      
    /**
     * The method for setting up form elements.
     * 
     * @since       3.2.0
     */
    public function load( $oAdminWidget ) {}
      
    /*
     * Head Tag Methods
     */
    /**
     * Enqueues styles by page slug and tab slug.
     * 
     * @since 3.2.0
     * @return array An array holding the handle IDs of queued items.
     */
    public function enqueueStyles( $aSRCs, $aCustomArgs=array() ) {     
        if ( method_exists( $this->oResource, '_enqueueStyles' ) ) {
            return $this->oResource->_enqueueStyles( $aSRCs, array( $this->oProp->sPostType ), $aCustomArgs );
        }
    }
    /**
     * Enqueues a style by page slug and tab slug.
     * 
     * @since       3.2.0
     * @see         http://codex.wordpress.org/Function_Reference/wp_enqueue_style
     * @param       string      The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
     * @param       array       (optional) The argument array for more advanced parameters.
     * @return      string      The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
     */    
    public function enqueueStyle( $sSRC, $aCustomArgs=array() ) {
        if ( method_exists( $this->oResource, '_enqueueStyle' ) ) {
            return $this->oResource->_enqueueStyle( $sSRC, array( $this->oProp->sPostType ), $aCustomArgs );     
        }
    }
    /**
     * Enqueues scripts by page slug and tab slug.
     * 
     * @since       3.2.0
     * @return      array An array holding the handle IDs of queued items.
     */
    public function enqueueScripts( $aSRCs, $aCustomArgs=array() ) {
        if ( method_exists( $this->oResource, '_enqueueScripts' ) ) {
            return $this->oResource->_enqueueScripts( $aSRCs, array( $this->oProp->sPostType ), $aCustomArgs );
        }
    }    
    /**
     * Enqueues a script by page slug and tab slug.
     *  
     * 
     * @since           3.2.0
     * @see             http://codex.wordpress.org/Function_Reference/wp_enqueue_script
     * @param           string The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
     * @param           array (optional) The argument array for more advanced parameters.
     * <h4>Custom Argument Array</h4>
     * <ul>
     *     <li><strong>handle_id</strong> - (optional, string) The handle ID of the script.</li>
     *     <li><strong>dependencies</strong> - (optional, array) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
     *     <li><strong>version</strong> - (optional, string) The stylesheet version number.</li>
     *     <li><strong>translation</strong> - (optional, array) The translation array. The handle ID will be used for the object name.</li>
     *     <li><strong>in_footer</strong> - (optional, boolean) Whether to enqueue the script before `</head>` or before `</body>` Default: `false`.</li>
     * </ul>
     * @return          string The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
     */
    public function enqueueScript( $sSRC, $aCustomArgs=array() ) {    
        if ( method_exists( $this->oResource, '_enqueueScript' ) ) {
            return $this->oResource->_enqueueScript( $sSRC, array( $this->oProp->sPostType ), $aCustomArgs );
        }
    }     
    
    /**
     * Sets the widget arguments.
     * 
     * This is only necessary if it is not set in the constructor.
     * 
     * @since       3.2.0
     * @return      void
     */ 
    protected function setArguments( array $aArguments=array() ) {
        $this->oProp->aWidgetArguments = $aArguments;  
    }  
    
}
endif;