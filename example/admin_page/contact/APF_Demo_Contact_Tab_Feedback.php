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
class APF_Demo_Contact_Tab_Feedback {

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
                'title'         => __( 'Feedback', 'admin-page-framework-demo' ),
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
                'title'         => __( 'Feedback', 'admin-page-framework-demo' ),
                'description'   => __( 'Tell the developer how you are using the framework.', 'admin-page-framework-demo' ), 
            )            
        );        

        $_oCurrentUser = wp_get_current_user();
        
        $oAdminPage->addSettingFields(
            $this->sSectionID, // the target section id
            array( 
                'field_id'          => 'name',
                'title'             => __( 'Your Name', 'admin-page-framework-demo' ),
                'type'              => 'text',
                'default'           => $_oCurrentUser->user_firstname || $_oCurrentUser->user_firstname 
                    ? $_oCurrentUser->user_lastname . ' ' .  $_oCurrentUser->user_lastname 
                    : '',
                'attributes'        => array(
                    'required' => 'required',
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
                    'placeholder'   =>  __( 'Type your email here.', 'admin-page-framework-demo' ),
                ),
            ),     
            array( 
                'field_id'          => 'use_for_commercial_products',
                'title'             => __( 'I use the framework for', 'admin-page-framework-demo' ),
                'type'              => 'radio',
                'default'           => 1,
                'label'             => array(
                    1       => __( 'Commercial Products', 'admin-page-framework-demo' ),
                    0       => __( 'Non-commercial Products', 'admin-page-framework-demo' ),
                ),
            ),              
            array( 
                'field_id'          => 'use_for',
                'title'             => __( 'I use the framework for', 'admin-page-framework-demo' ),
                'type'              => 'radio',
                'default'           => 'others',
                'label'             => array(
                    'plugins'   => __( 'Plugins', 'admin-page-framework-demo' ),
                    'themes'    => __( 'Themes', 'admin-page-framework-demo' ),
                    'others'    => __( 'Others', 'admin-page-framework-demo' ),
                ),
            ),                    
            array( 
                'field_id'          => 'subject',
                'title'             => __( 'Subject', 'admin-page-framework-demo' ),
                'type'              => 'text',
                'attributes'        => array(
                    'size' => 40,
                    'placeholder'   =>  __( 'Type the title here.', 'admin-page-framework-demo' ),
                ),
            ),    
            array( 
                'field_id'          => 'body',
                'title'             => __( 'Message', 'admin-page-framework-demo' ),
                'type'              => 'textarea',
                'rich'              => true,
                'attributes'        => array(
                    'placeholder'   =>  __( 'Type the message here.', 'admin-page-framework-demo' ),
                ),
            ),            
            array( 
                'field_id'          => 'ip',
                'type'              => 'hidden',
                'value'             => $_SERVER["REMOTE_ADDR"],
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
                    'to'          => 'admin-page-framework@michaeluno.jp',
                    'subject'     => array( $this->sSectionID, 'subject' ),
                    'message'     => array( $this->sSectionID ), // the section name enclosed in an array. If it is a field, set it to the second element like array( 'seciton id', 'field id' ).
                    'headers'     => '',
                    'attachments' => '',    // the file path
                    'is_html'     => true,  // boolean  Whether the mail should be sent as an html text
                    'from'        => array( $this->sSectionID, 'from' ),
                    'name'        => array( $this->sSectionID, 'name' ),
                ),                
            ),     
            array()    
        );        
        
    }
        
    
}
