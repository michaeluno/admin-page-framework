<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Widget_Model' ) ) :
/**
 * Provides methods of models for the widget factory class.
 * 
 * Those methods are internal and deal with internal properties.
 * 
 * @abstract
 * @since       3.2.0
 * @package     AdminPageFramework
 * @subpackage  Widget
 */
abstract class AdminPageFramework_Widget_Model extends AdminPageFramework_Widget_Router {    

    function __construct( $oProp ) {
        
        parent::__construct( $oProp );   

        if ( did_action( 'widgets_init' ) ) { 
            add_action( "set_up_{$this->oProp->sClassName}", array( $this, '_replyToRegisterWidget' ), 20 ); 
        } else {
            // set a lower priority to let the setUp method gets processed earlier.
            add_action( 'widgets_init', array( $this, '_replyToRegisterWidget' ), 20 ); 
        }
        
    }
    
    /**
     * The predefined validation method. 
     * 
     * This method should be overridden in an extended class.
     * 
     * @since   3.2.0
     */
    protected function validate( $aSubmit, $aStored, $oAdminWidget ) {
        return $aSubmit;
    }
    
    /**
     * Determines whether the currently loaded page is of the post type page.
     * 
     * @since 3.2.0
     * @todo: complete this method        
     */
    public function _isInThePage() {
        return true;
    }

    /**
     * Registers the widget.
     * 
     * @internal
     * @since       3.2.0
     */
    public function _replyToRegisterWidget() {
        
        global $wp_widget_factory;
        if ( ! is_object( $wp_widget_factory ) ) { return; }
        
        $wp_widget_factory->widgets[ $this->oProp->sClassName ] = new AdminPageFramework_Widget_Factory( 
            $this, 
            $this->oProp->sWidgetTitle, 
            is_array( $this->oProp->aWidgetArguments ) ? $this->oProp->aWidgetArguments : array() 
        );
        
    }

    /**
     * Registers form fields and sections.
     * 
     * @since       3.2.0
     * @internal
     * @remark      Called from the widget factory class.
     */
    public function _registerFormElements( $aOptions ) {
                    
        $this->_loadDefaultFieldTypeDefinitions();  // defined in the framework factory class.    
        
        // Set the internal options array. The framework refers this array when rendering the form.
        $this->oProp->aOptions = $aOptions;
     
        // Format the fields array.
        $this->oForm->format();
        $this->oForm->applyConditions(); // will set $this->oForm->aConditionedFields
  
        // Add the repeatable section elements to the fields definition array.
        $this->oForm->setDynamicElements( $this->oProp->aOptions ); // will update $this->oForm->aConditionedFields
  
        $this->_registerFields( $this->oForm->aConditionedFields ); // defined in the framework factory model class.
                
    }   
    
}
endif;