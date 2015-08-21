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
     * @since       3.5.9       Changed the timing of the hooks (do_{...} and content_{...} ) to allow the user to decide 
     * whether the title should be visible or not depending on the content.
     * @return      void
     */
	public function widget( $aArguments, $aFormData ) {
           
        echo $aArguments[ 'before_widget' ];
        
        $this->oCaller->oUtil->addAndDoActions( 
            $this->oCaller, 
            'do_' . $this->oCaller->oProp->sClassName, 
            $this->oCaller
        );
       
        $_sContent = $this->oCaller->oUtil->addAndApplyFilters(
            $this->oCaller, 
            "content_{$this->oCaller->oProp->sClassName}", 
            $this->oCaller->content( '', $aArguments, $aFormData ),
            $aArguments,
            $aFormData
        );    
        
        // 3.5.9+ Moved this after the content_{...} filter hook so that the user can decide whether the title shoudl be visible or not.
        echo $this->_getTitle( $aArguments, $aFormData );

        echo $_sContent;
        
		echo $aArguments[ 'after_widget' ];
		
	}
        /**
         * Returns the widget title.
         * 
         * @since       3.5.7
         * @remark      The user needs to add a field with the id, `title` to display a title.
         * @remark      In order to disable the title, add a field with the id  `show_title` and if the value yields `false`, 
         * the title will not be displayed.
         * @return      string      The widget title
         */
        private function _getTitle( array $aArguments, array $aFormData ) {
                
            if ( ! $this->oCaller->oProp->bShowWidgetTitle ) {
                return '';
            }
            
            $_sTitle = apply_filters(
                'widget_title',
                $this->oCaller->oUtil->getElement(
                    $aFormData,
                    'title',
                    ''
                ),
                $aFormData,
                $this->id_base 
            );
            if ( ! $_sTitle ) {
                return '';
            }
           return $aArguments['before_title'] 
                . $_sTitle 
            . $aArguments['after_title'];           
            
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
            call_user_func_array( 
                array( $this->oCaller, 'validate' ),    // triggers __call()
                array( $aSubmittedFormData, $aSavedFormData, $this->oCaller )
            ), // 3.5.3+                        
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
            'hfName'        => array( $this, '_replyToGetFieldName' ),  // defined in the WP_Widget class.  
            'hfInputName'   => array( $this, '_replyToGetFieldInputName' ),
            // 'hfName'        => array( $this, 'get_field_name' ),  // defined in the WP_Widget class.  
            // 'hfClass'       => array( $this, '_replyToAddClassSelector' ),
            // 'hfNameFlat'    => array( $this, '_replyToGetFlatFieldName' ), // @deprecated 3.6.0+ the same as the framework factory method.
        );              
      
        // Render the form. 
        $this->oCaller->_printWidgetForm();
       
        /** 
         * Initialize the form object that stores registered sections and fields
         * because this class gets called multiple times to render the form including added widgets and the initial widget that gets listed on the lsft hand side of the page.
         * @since       3.5.2
         */
        $this->oCaller->oForm = new AdminPageFramework_FormDefinition( 
            $this->oCaller->oProp->sFieldsType, 
            $this->oCaller->oProp->sCapability, 
            $this->oCaller
        );   
       
	}
    
        /**
         * 
         * @remark      This one is tricky as the core widget factory method enclose this value in []. So when the framework field has a section, it must NOT end with ].
         * @since       3.5.7       Moved from `AdminPageFramework_FormField`.
         * @since       3.6.0       Changed the name from `_replyToGetInputName`.
         * @return      string
         */
        public function _replyToGetFieldName( /* $sNameAttribute, array $aField */ ) {
            
            $_aParams      = func_get_args() + array( null, null, null );
            $aField        = $_aParams[ 1 ];
            
            $_sSectionIndex = isset( $aField['section_id'], $aField['_section_index'] ) 
                ? "[{$aField['_section_index']}]" 
                : "";             
            $_sID           = $this->oCaller->isSectionSet( $aField )
                ? $aField['section_id'] . "]" . $_sSectionIndex . "[" . $aField[ 'field_id' ]
                : "{$aField['field_id']}";
            return $this->get_field_name( $_sID );
        
        }    
        
        /**
         * 
         * @since       3.6.0
         * @return      string
         */
        public function _replyToGetFieldInputName( /* $sNameAttribute, array $aField, $sIndex */ ) {
            
            $_aParams      = func_get_args() + array( null, null, null );
            $aField        = $_aParams[ 1 ];
            $sIndex        = $_aParams[ 2 ];
            
            $_sIndex       = isset( $sIndex ) 
                ? "[" . $sIndex . "]"
                : '';
            $_sSectionIndex = isset( $aField['section_id'], $aField['_section_index'] ) 
                ? "[{$aField['_section_index']}]" 
                : "";             
            $_sID           = $this->oCaller->isSectionSet( $aField )
                ? $aField['section_id'] . "]" . $_sSectionIndex . "[" . $aField[ 'field_id' ]
                : $aField[ 'field_id' ];
            return $this->get_field_name( $_sID ) . $_sIndex;
            
        }
        
        /**
         * Returns the flat input name.
         * 
         * A flat input name is a 'name' attribute value whose dimensional elements are delimited by the pile character.
         * 
         * Instead of [] enclosing array elements, it uses the pipe(|) to represent the multi dimensional array key.
         * This is used to create a reference to the submit field name to determine which button is pressed.
         * 
         * @since       3.5.7       Moved from `AdminPageFramework_FormField`.
         * @since       3.6.0       Changed the name from `_replyToGetFlatInputName()`.
         * @return      string
         * @deprecated
         */
        // protected function _replyToGetFlatFieldName( /* $sFlatInputName, array $aField */ ) {
            // $_aParams       = func_get_args() + array( null, null );            
            // $aField         = $_aParams[ 1 ];

            
            // $_sSectionIndex = isset( $aField['section_id'], $aField['_section_index'] )
                // ? "|{$aField['_section_index']}" 
                // : '';                        
            // $sFlatInputName = $this->oCaller->isSectionSet( $aField )
                // ? "{$aField['section_id']}{$_sSectionIndex}|{$aField['field_id']}"
                // : "{$aField['field_id']}";
            // return $sFlatInputName;
        // }
    
    /**
     * Modifies the class Selector.
     * 
     * @since   3.2.0
     * @remark  currently not used
     * @deprecated
     */
    // public function _replyToAddClassSelector( $sClassSelectors ) {
        
        // $sClassSelectors .= ' widefat';
        // return trim( $sClassSelectors );
        
    // }
    
}