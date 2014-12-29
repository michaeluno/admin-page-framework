<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

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
     * 
     * @since       3.2.0
     * @internal    
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
    * <h4>Example</h4>
    * <code>
    *   public function setUp() {
    *       $this->setArguments( 
    *           array(
    *               'description'   =>  __( 'This is a sample widget with built-in field types created by Admin Page Framework.', 'admin-page-framework-demo' ),
    *           ) 
    *       );
    *   }  
    * </code>
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
     * {@inheritdoc}
     * 
     * {@inheritdoc}
     * 
     * @since       3.2.0
     * @internal    Temporarily marked internal
     */
    public function enqueueStyles( $aSRCs, $aCustomArgs=array() ) {     
        if ( method_exists( $this->oResource, '_enqueueStyles' ) ) {
            return $this->oResource->_enqueueStyles( $aSRCs, array( $this->oProp->sPostType ), $aCustomArgs );
        }
    }
    /**
     * {@inheritdoc}
     * 
     * {@inheritdoc}
     * 
     * @since       3.2.0
     * @internal    Temporarily marked internal
     */    
    public function enqueueStyle( $sSRC, $aCustomArgs=array() ) {
        if ( method_exists( $this->oResource, '_enqueueStyle' ) ) {
            return $this->oResource->_enqueueStyle( $sSRC, array( $this->oProp->sPostType ), $aCustomArgs );     
        }
    }
    /**
     * {@inheritdoc}
     * 
     * {@inheritdoc}
     * 
     * @since       3.2.0
     * @internal    Temporarily marked internal
     */
    public function enqueueScripts( $aSRCs, $aCustomArgs=array() ) {
        if ( method_exists( $this->oResource, '_enqueueScripts' ) ) {
            return $this->oResource->_enqueueScripts( $aSRCs, array( $this->oProp->sPostType ), $aCustomArgs );
        }
    }    
    /**
     * {@inheritdoc}
     * 
     * {@inheritdoc}
     * 
     * @since           3.2.0
     * @internal    Temporarily marked internal
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