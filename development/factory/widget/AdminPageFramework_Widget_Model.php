<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods of models for the widget factory class.
 * 
 * Those methods are internal and deal with internal properties.
 * 
 * @abstract
 * @since       3.2.0
 * @package     AdminPageFramework
 * @subpackage  Factory/Widget
 * @internal
 */
abstract class AdminPageFramework_Widget_Model extends AdminPageFramework_Widget_Router {    

    /**
     * Sets up hooks and properties.
     * 
     * @since       3.2.0
     * @internal
     */
    function __construct( $oProp ) {
        
        parent::__construct( $oProp );   

        $this->oUtil->registerAction(
            "set_up_{$this->oProp->sClassName}",
            array( $this, '_replyToRegisterWidget' )
        );
        
        if ( $this->oProp->bIsAdmin ) {
            add_filter( 
                'validation_' . $this->oProp->sClassName,
                array( $this, '_replyToSortInputs' ),
                1,  // set a high priority 
                3   // number of parameters
            );            
        }
        
    }
        /**
         * Sorts dynamic elements.
         * @internal
         * @since       3.6.0
         * @return      array
         * @callback    filter      validation_{factory class name}
         */
        public function _replyToSortInputs( $aSubmittedFormData, $aStoredFormData, $oFactory ) {            
// @todo examine whether stripslashes_deep() is necessary or not.
            return $this->oForm->getSortedInputs( $aSubmittedFormData ); 
        }
        
    /**
     * The predefined validation method. 
     * 
     * This method should be overridden in an extended class. Alternatively the user may use validation_{instantiated class name} method.
     * 
     * <h4>Example</h4>
     * <code>
     * public function validate( $aSubmit, $aStored, $oAdminWidget ) {
     *     
     *     // Uncomment the following line to check the submitted value.
     *     // AdminPageFramework_Debug::log( $aSubmit );
     *     
     *     return $aSubmit;
     *     
     * }    
     * </code>
     * @since       3.2.0
     * @since       3.4.1       Changed the scope to protected from public as this method is called outside from the class.
     * @remark      The user will extend this method and use it.
     * @remark      Do not even declare this method to avoid PHP strict standard warnings.
     */
    // public function validate( $aSubmit, $aStored, $oAdminWidget ) {
        // return $aSubmit;
    // }
    
    /**
     * Gets called after the form element registration is done.
     * 
     * @since       3.7.0
     */
    public function _replyToHandleSubmittedFormData( $aSavedData, $aArguments, $aSectionsets, $aFieldsets ) {
        
        // Instantiate the resource object so that common styles and scripts will be automatically inserted.
        // This method is called when fields are registered. Originally this method is used to validate form data
        // but the widget class utilizes WordPress built-in widget factory class and it has its own validation method.
        if ( empty( $aSectionsets ) || empty( $aFieldsets ) ) {
            return;
        }
        $this->oResource; // triggers `__get()`.
        
    }    
    
    /**
     * Registers the widget.
     * 
     * @internal
     * @since       3.2.0
     * @callback    action      set_up_{class name}
     * @return      void
     */
    public function _replyToRegisterWidget() {
        
        global $wp_widget_factory;
        if ( ! is_object( $wp_widget_factory ) ) { 
            return; 
        }

        $wp_widget_factory->widgets[ $this->oProp->sClassName ] = new AdminPageFramework_Widget_Factory( 
            $this, 
            $this->oProp->sWidgetTitle, 
            $this->oUtil->getAsArray( $this->oProp->aWidgetArguments )
        );
        
        // [3.5.9+] Store the widget object in the property.
        $this->oProp->oWidget = $wp_widget_factory->widgets[ $this->oProp->sClassName ];
        
    }
    
}
