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

/**
 * Adds the Contact page to the demo plugin.
 * 
 * @since   3.4.2
 */
class APF_Demo_Contact_Tab_Report {

    public function __construct( $oFactory, $sPageSlug='', $sTabSlug='' ) {
    
        $this->oFactory     = $oFactory;
        // $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = $sPageSlug ? $sPageSlug : $this->sPageSlug;
        $this->sTabSlug     = $sTabSlug ? $sTabSlug : $this->sTabSlug;
        $this->sSectionID   = $this->sTabSlug;
        $this->_addTab();
    
    }
    
    private function _addTab() {
        
        $this->oFactory->addInPageTabs(    
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Report', 'admin-page-framework-demo' ),
            )
        );  
        
        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToAddFormElements' ) );
        
    }
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToAddFormElements( $oAdminPage ) {
        
        /*
         * ( optional ) Create a form - To create a form in Admin Page Framework, you need two kinds of components: sections and fields.
         * A section groups fields and fields belong to a section. So a section needs to be created prior to fields.
         * Use the addSettingSections() method to create sections and use the addSettingFields() method to create fields.
         */
        // Section
        $oAdminPage->addSettingSections(    
            $this->sPageSlug, // the target page slug                
            array(
                'section_id'    => $this->sSectionID,       // avoid hyphen(dash), dots, and white spaces
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Report Issues', 'admin-page-framework-demo' ),
                'description'   => __( 'If you find a bug, you can report it from here.', 'admin-page-framework-demo' ),
            )            
        );        

        $_oCurrentUser = wp_get_current_user();
        
        $oAdminPage->addSettingFields(
            'report',
            array( 
                'field_id'          => 'name',
                'title'             => __( 'Your Name', 'admin-page-framework-demo' ),
                'type'              => 'text',
                'default'           => $_oCurrentUser->user_firstname || $_oCurrentUser->user_firstname 
                    ? $_oCurrentUser->user_lastname . ' ' .  $_oCurrentUser->user_lastname 
                    : '',
                'attributes'        => array(
                    'required'      => 'required',
                    'placeholder'   => __( 'Type your name.', 'admin-page-framewrok-demo' ),
                ),
            ),    
            array( 
                'field_id'          => 'from',
                'title'             => __( 'Your Email Address', 'admin-page-framework-demo' ),
                'type'              => 'text',
                'default'           => $_oCurrentUser->user_email,
                'attributes'        => array(
                    'required'      => 'required',
                    'placeholder'   =>  __( 'Type your email that the developer replies backt to.', 'admin-page-framework-demo' )
                ),
            ),                
            array( 
                'field_id'          => 'expected_result',
                'title'             => __( 'Expected Behavior', 'admin-page-framework-demo' ),
                'type'              => 'textarea',
                'description'       => __( 'Tell how the framework should work.', 'admin-page-framework-demo' ),
                'attributes'        => array(
                    'required'  => 'required',
                ),
            ),  
            array( 
                'field_id'          => 'actual_result',
                'title'             => __( 'Actual Behavior', 'admin-page-framework-demo' ),
                'type'              => 'textarea',
                'description'      => __( 'Describe the behavior of the framework.', 'admin-page-framework-demo' ),
                'attributes'        => array(
                    'required'  => 'required',
                ),                
            ),    
            array(
                'field_id'          => 'attachments',
                'title'             => __( 'Screenshots', 'admin-page-framework-demo' ),
                'type'              => 'image',
                'repeatable'        => true,
                'attributes'        => array(
                    'size'  => 40,
                    'preview' => array(
                        'style' => 'max-width: 200px;'
                    ),
                ),                                
            ),
            array(
                'field_id'      => 'system_information',
                'type'          => 'system',     
                'title'         => __( 'System Information', 'admin-page-framework-demo' ),
                'data'          => array(
                    __( 'Custom Data', 'admin-page-framework-demo' )    => __( 'This is custom data inserted with the data argument.', 'admin-page-framework-demo' ),
                    __( 'Current Time', 'admin-page-framework' )        => '', // Removes the Current Time Section.
                ),
                'attributes'    => array(
                    'rows'          =>  10,
                ),
            ),
            array(
                'field_id'      => 'saved_options',
                'type'          => 'system',     
                'title'         => __( 'Saved Options', 'admin-page-framework-demo' ),
                'data'          => array(
                    // Removes the default data by passing an empty value below.
                    'WordPress'             => '',
                    'Admin Page Framework'  => '',
                    'Server'                => '',
                    'PHP'                   => '',
                    'PHP Error Log'         => '',
                    'MySQL'                 => '',
                    'MySQL Error Log'       => '',                    
                    'Browser'               => '',                    
                ) 
                + get_option( 'APF_Demo', array() ), // the stored options of the main demo class
                'attributes'    => array(
                    'rows'          =>  10,
                ),
                'hidden'        => true,
            ),     
            array(
                'field_id'          => 'allow_sending_system_information',
                'title'             => __( 'Confirmation', 'admin-page-framework-demo' ) 
                    . ' (' . __( 'required', 'admin-page-framework-demo' ) . ')',
                'type'              => 'checkbox',
                'label'             => __( 'I understand that the system information including a PHP version and WordPress version etc. will be sent along with the messages to help developer trouble-shoot the problem.', 'admin-page-framework-demo' ),
                'attributes'        => array(
                    'required'  => 'required',
                ),  
            ),
            array( 
                'field_id'          => 'send',
                'type'              => 'submit',
                'label_min_width'   => 0,
                'value'             => isset( $_GET['confirmation'] ) && 'email' === $_GET['confirmation']
                    ? __( 'Send', 'adimn-page-framework-demo' )
                    : __( 'Preview', 'adimn-page-framework-demo' ),
                'attributes'        => array(
                    'field' => array(
                        'style' => 'float:right; clear:none; display: inline;',
                    ),
                    'class' => isset( $_GET['confirmation'] ) && 'email' === $_GET['confirmation']
                        ? null
                        : 'button-secondary',
                ),     
                'email'             => array(
                    // Each argument can accept a string or an array representing the dimensional array key.
                    // For example, if there is a field for the email title, and its section id is 'my_section'  and  the field id is 'my_field', pass an array, array( 'my_section', 'my_field' )
                    'to'            => 'admin-page-framework@michaeluno.jp',
                    'subject'       => 'Reporting Issue',
                    'message'       => array( 'report' ), // the section name enclosed in an array. If it is a field, set it to the second element like array( 'seciton id', 'field id' ).
                    'headers'       => '',
                    'attachments'   => '', // the file path(s)
                    'name'          => '', // The email sender name. If the 'name' argument is empty, the field named 'name' in this section will be applied
                    'from'          => '', // The sender email address. If the 'from' argument is empty, the field named 'from' in this section will be applied.
                    // 'is_html'       => true,
                ),
            ),     
            array()
        );        
        
        // validation + page slug + tab slug
        add_action( 'validation_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToValidateForm' ), 10, 3 );
        
    }
    
    /**
     * Validates the submitted data.
     */
    public function replyToValidateForm( $aInput, $aOldInput, $oFactory ) {
        
       // Local variables
        $_bIsValid = true;
        $_aErrors  = array();
      
        if ( ! $aInput[ $this->sSectionID ]['allow_sending_system_information'] ) {
            $_bIsValid = false;
            $_aErrors[ $this->sSectionID ]['allow_sending_system_information'] = __( 'We need necessary information to help you.', 'fetch-tweets' );
        }
        
        if ( ! $_bIsValid ) {
        
            $oFactory->setFieldErrors( $_aErrors );     
            $oFactory->setSettingNotice( __( 'Please help us to help you.', 'fetch-tweets' ) );        
            return $aOldInput;
            
        }     
     
        // Otherwise, process the data.
        return $aInput;        

    }
}
