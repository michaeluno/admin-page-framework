<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * A back-end factory class that redirects callback methods to the main widget class.
 * 
 * @abstract
 * @since       3.2.0
 * @package     AdminPageFramework
 * @subpackage  Widget
 * @internal
 */
class AdminPageFramework_Widget_Factory extends WP_Widget {
    
    /**
     * Sets up internal properties.
     * 
     * @since       3.2.0
     * @return      void
     */
	public function __construct( $oCaller, $sWidgetTitle, array $aArguments=array() ) {
		
        $aArguments = $aArguments 
            + array( 
                'classname'     => 'admin_page_framework_widget',
                'description'   => '',  
            );
		parent::__construct( 
            $oCaller->oProp->sClassName,  // base id 
            $sWidgetTitle,      // widget title
            $aArguments         // widget arguments
        );
        $this->oCaller = $oCaller;
        
	}
    
    /**
     * Displays the widget contents in the front end.
     * 
     * @since       3.2.0
     * @return      void
     */
	public function widget( $aArguments, $aFormData ) {

        echo $aArguments['before_widget'];
        
		$_sTitle = apply_filters( 'widget_title', isset( $aFormData['title'] ) ? $aFormData['title'] : '', $aFormData, $this->id_base );
        if ( $_sTitle ) {
			echo $aArguments['before_title'] . $_sTitle . $aArguments['after_title'];
		}

        // Do action.
        $this->oCaller->oUtil->addAndDoActions( $this->oCaller, 'do_' . $this->oCaller->oProp->sClassName, $this->oCaller );
        
        // Filter the contents.
        echo $this->oCaller->oUtil->addAndApplyFilters(
            $this->oCaller, 
            "content_{$this->oCaller->oProp->sClassName}", 
            $this->oCaller->content( '', $aArguments, $aFormData ),
            $aArguments,
            $aFormData
        );    
 
		echo $aArguments['after_widget'];
		
	}
    
    /**
     * Validates the submitted form data.
     * 
     * @since       3.2.0
     * @return      mixed       The validated form data. The type should be an array but it is dealt by the framework user it will be unknown.
     */
	public function update( $aSubmittedFormData, $aSavedFormData ) {
                
        return $this->oCaller->oUtil->addAndApplyFilters(
            $this->oCaller, 
            "validation_{$this->oCaller->oProp->sClassName}", 
            $this->oCaller->validate( $aSubmittedFormData, $aSavedFormData, $this->oCaller ),
            $aSavedFormData,
            $this->oCaller
        );

        
	}
    
    /**
     * Constructs the widget form.
     * 
     * @return      void
     */
	public function form( $aFormData ) {

        // Trigger the load() method and load_{...} actions. The user sets up the form.
        $this->oCaller->load( $this->oCaller );
        $this->oCaller->oUtil->addAndDoActions( 
            $this->oCaller, 
            'load_' . $this->oCaller->oProp->sClassName, 
            $this->oCaller 
        );
	        
        // Register the form elements.
        $this->oCaller->_registerFormElements( $aFormData );
      
        // Set up callbacks for field element outputs such as for name and it attributes.
        $this->oCaller->oProp->aFieldCallbacks = array( 
            'hfID'          => array( $this, 'get_field_id' ),    // defined in the WP_Widget class.  
            'hfTagID'       => array( $this, 'get_field_id' ),    // defined in the WP_Widget class.  
            'hfName'        => array( $this, 'get_field_name' ),  // defined in the WP_Widget class.  
            // 'hfClass'       => array( $this, '_replyToAddClassSelector' ),
            // 'hfNameFlat'    => array( $this, '' ),
        );              
      
        // Render the form. 
        $this->oCaller->_printWidgetForm();
       
        /** 
         * Initialize the form object that stores registered sections and fields
         * because this class gets called multiple times to render the form including added widgets and the initial widget that gets listed on the lsft hand side of the page.
         * @since       3.5.2
         */
        $this->oCaller->oForm = new AdminPageFramework_FormElement( 
            $this->oCaller->oProp->sFieldsType, 
            $this->oCaller->oProp->sCapability, 
            $this->oCaller
        );   
       
	}
    
    /**
     * Modifies the class Selector.
     * 
     * @since   3.2.0
     */
    public function _replyToAddClassSelector( $sClassSelectors ) {
        
        $sClassSelectors .= ' widefat';
        return trim( $sClassSelectors );
        
    }
    
}