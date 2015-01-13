<?php
/**
 * Admin Page Framework - Demo
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed GPLv2
 * 
 */

class APF_Demo_BuiltinFieldTypes_Verification {
    
    /**
     * Stores the caller class name, set in the constructor.
     */   
    public $sClassName = 'APF_Demo';
    
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_builtin_field_types';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'verification';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = '';
    
    /**
     * Sets up hooks and properties.
     */
    public function __construct( $sClassName='', $sPageSlug='', $sTabSlug='' ) {
        
        $this->sClassName   = $sClassName ? $sClassName : $this->sClassName;
        $this->sPageSlug    = $sPageSlug ? $sPageSlug : $this->sPageSlug;
        $this->sTabSlug     = $sTabSlug ? $sTabSlug : $this->sTabSlug;
        
        // load_ + page slug
        add_action( 'load_' . $this->sPageSlug, array( $this, 'replyToAddTab' ) );
        
    }
    
    /**
     * Triggered when the page is loaded.
     */
    public function replyToAddTab( $oAdminPage ) {
        
        // Tab
        $oAdminPage->addInPageTabs(    
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'  => $this->sTabSlug,
                'title'     => __( 'Verification', 'admin-page-framework-demo' ),    
            )      
        );  
        
        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToAddFormElements' ) );
        
    }
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToAddFormElements( $oAdminPage ) {
        
        // Section
        $oAdminPage->addSettingSections(    
            $this->sPageSlug, // the target page slug                     
            array(
                'tab_slug'          => $this->sTabSlug,
                'section_id'        => 'verification',
                'title'             => __( 'Verify Submitted Data', 'admin-page-framework-demo' ),
                'description'       => __( 'Show error messages when the user submits improper option value.', 'admin-page-framework-demo' ),
            ),
            array(
                'tab_slug'          => $this->sTabSlug,
                'section_id'        => 'section_verification',
                'title'             => __( 'Section Verification', 'admin-page-framework-demo' ),
                'description'       => __( 'Show error messages of the entire section.', 'admin-page-framework-demo' ),
            )       
        );        
                
        /* 
         * Verification Example
         * */
        $oAdminPage->addSettingFields(     
            'verification', // the target section ID
            array(
                'field_id'      => 'verify_text_field',
                'title'         => __( 'Verify Text Input', 'admin-page-framework-demo' ),
                'type'          => 'text',
                'description'   => __( 'Try setting a non numeric value here.', 'admin-page-framework-demo' ),
            ),
            array(
                'field_id'      => 'other_text_field',
                'title'         => __( 'Other Field', 'admin-page-framework-demo' ),
                'type'          => 'text',
                'description'   => __( 'This field will not be validated.', 'admin-page-framework-demo' ),
            ),    
            array(
                'field_id'      => 'verify_text_field_submit', // this submit field ID can be used in a validation callback method
                'type'          => 'submit',     
                'label'         => __( 'Verify', 'admin-page-framework-demo' ),
            )
        );    
        $oAdminPage->addSettingFields(     
            'section_verification', // the target sectin ID
            array(
                'field_id'  => 'item_a',
                'title'     => __( 'Choose Item', 'admin-page-framework-demo' ),
                'type'      => 'select',
                'label'     => array(
                    0       => '--- ' . __( 'Select Item', 'admin-page-framework-demo' ) . ' ---',
                    'one'   => __( 'One', 'admin-page-framework-demo' ),
                    'two'   => __( 'Two', 'admin-page-framework-demo' ),
                    'three' => __( 'Three', 'admin-page-framework-demo' ),     
                ),
            ),
            array(
                'field_id'      => 'item_b', // this submit field ID can be used in a validation callback method
                'type'          => 'text',
                'description'   => __( 'Select one above or enter text here.', 'admin-page-framework-demo' ),
            )
        );         

        // validation_ + class name + _ + section id + field id
        add_filter( 'validation_' . $this->sClassName . '_verification_verify_text_field', array( $this, 'replyToValidateField' ), 10, 3 );
        
        // validation_ + class name + _ + section id
        add_filter( 'validation_' . $this->sClassName . '_section_verification', array( $this, 'replyToValidateSection' ), 10, 3  );
        
        
    }
    
    
    /*
     * Validation Callbacks
     * */
    /**
     * Validates the 'verify_text_field' field in the 'verification' section of the 'APF_Demo' class.
     */
    public function replyToValidateField( $sNewInput, $sOldInput, $oAdmin ) { // validation_{instantiated class name}_{section id}_{field id}
    
        /* 1. Set a flag. */
        $_bVerified = true;
        
        /* 2. Prepare an error array.
             We store values that have an error in an array and pass it to the setFieldErrors() method.
            It internally stores the error array in a temporary area of the database called transient.
            The used name of the transient is a md5 hash of 'instantiated class name' + '_' + 'page slug'. 
            The library class will search for this transient when it renders the form fields 
            and if it is found, it will display the error message set in the field array.     
        */
        $_aErrors = array();

        /* 3. Check if the submitted value meets your criteria. As an example, here a numeric value is expected. */
        if ( ! is_numeric( $sNewInput ) ) {
            
            // $variable[ 'sectioni_id' ]['field_id']
            $_aErrors['verification']['verify_text_field'] = __( 'The value must be numeric:', 'admin-page-framework-demo' ) . ' ' . $sNewInput;
            $_bVerified = false;
                    
        }
        
        /* 4. An invalid value is found. */
        if ( ! $_bVerified ) {
        
            /* 4-1. Set the error array for the input fields. */
            $oAdmin->setFieldErrors( $_aErrors );     
            $oAdmin->setSettingNotice( __( 'There was something wrong with your input.', 'admin-page-framework-demo' ) );

            return $sOldInput;
            
        }
                
        return $sNewInput;     
        
    }    
    
    /**
     * Validates the 'section_verification' section items.
     */
    public function replyToValidateSection( $aInput, $aOldInput, $oAdmin ) { // validation_{instantiated class name}_{section id}

        // Local variables
        $_bIsValid = true;
        $_aErrors  = array();
        
        if ( '0' === (string) $aInput['item_a'] && '' === trim( $aInput['item_b'] ) ) {
            $_bIsValid = false;
            $_aErrors[ 'section_verification' ] = __( 'At least one item must be set', 'admin-page-framework-demo' );
        }
        
        if ( ! $_bIsValid ) {
        
            $oAdmin->setFieldErrors( $_aErrors );     
            $oAdmin->setSettingNotice( __( 'There was something wrong with your input.', 'admin-page-framework-demo' ) );        
            return $aOldInput;
            
        }     
     
        // Otherwise, process the data.
        return $aInput;
        
    }    
    
}