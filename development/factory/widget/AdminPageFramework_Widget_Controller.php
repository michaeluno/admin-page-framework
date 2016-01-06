<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
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
        
        // Other admin page framework factory classes uses wp_loaded hook but widget_init hook is called before that.
        // So we use widgets_init hook for this factory.
        $this->oUtil->registerAction( 'widgets_init', array( $this, 'setup_pre' ) );
        
    }

    /**
    * The method for necessary set-ups.
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
     * <h4>Example</h4>
     * <code>
     *  public function load( $oAdminWidget ) {
     *      
     *      $this->addSettingFields(
     *          array(
     *              'field_id'      => 'title',
     *              'type'          => 'text',
     *              'title'         => __( 'Title', 'admin-page-framework-demo' ),
     *              'default'       => 'Hi there!',
     *          ),
     *          array(
     *              'field_id'      => 'repeatable_text',
     *              'type'          => 'text',
     *              'title'         => __( 'Text Repeatable', 'admin-page-framework-demo' ),
     *              'repeatable'    => true,
     *              'sortable'      => true,
     *          ),
     *          array(
     *              'field_id'      => 'textarea',
     *              'type'          => 'textarea',
     *              'title'         => __( 'Text Area', 'admin-page-framework-demo' ),
     *          ),
     *          array(
     *              'field_id'      => 'checkbox',
     *              'type'          => 'checkbox',
     *              'title'         => __( 'Check Box', 'admin-page-framework-demo' ),
     *              'label'         => __( 'This is a check box in a widget form.', 'admin-page-framework-demo' ),
     *          ),     
     *          array(
     *              'field_id'      => 'radio',
     *              'type'          => 'radio',
     *              'title'         => __( 'Radio Buttons', 'admin-page-framework-demo' ),
     *              'label'         => array(
     *                  'one'   =>  __( 'One', 'admin-page-framework-demo' ),
     *                  'two'   =>  __( 'Two', 'admin-page-framework-demo' ),
     *                  'three' =>  __( 'Three', 'admin-page-framework-demo' ),
     *              ),
     *              'default'       => 'two',
     *          ),      
     *          array(
     *              'field_id'      => 'select',
     *              'type'          => 'select',
     *              'title'         => __( 'Dropdown', 'admin-page-framework-demo' ),
     *              'label'         => array(
     *                  'i'     =>  __( 'I', 'admin-page-framework-demo' ),
     *                  'ii'    =>  __( 'II', 'admin-page-framework-demo' ),
     *                  'iii'   =>  __( 'III', 'admin-page-framework-demo' ),
     *              ),
     *          ),                
     *          array(
     *              'field_id'      => 'image',
     *              'type'          => 'image',
     *              'title'         => __( 'Image', 'admin-page-framework-demo' ),
     *          ),
     *          array(
     *              'field_id'      => 'media',
     *              'type'          => 'media',
     *              'title'         => __( 'Media', 'admin-page-framework-demo' ),
     *          ),            
     *          array(
     *              'field_id'      => 'color',
     *              'type'          => 'color',
     *              'title'         => __( 'Color', 'admin-page-framework-demo' ),
     *          ),
     *          array()
     *      );        
     *      
     *  }
     * </code>
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