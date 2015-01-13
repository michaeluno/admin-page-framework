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

class APF_Demo_BuiltinFieldTypes_MISC {
 
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
    public $sTabSlug    = 'misc';
    
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
                'title'     => __( 'MISC', 'admin-page-framework-demo' ),    
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
                'section_id'        => 'color_picker',
                'tab_slug'          => $this->sTabSlug,
                'title'             => __( 'Colors', 'admin-page-framework-demo' ),
            ),
            array(
                'section_id'        => 'hidden_field',
                'tab_slug'          => $this->sTabSlug,
                'title'             => __( 'Hidden Fields', 'admin-page-framework-demo' ),
                'description'       => __( 'These are hidden fields.', 'admin-page-framework-demo' ),
            ),     
            array(
                'section_id'        => 'raw_html',
                'tab_slug'          => $this->sTabSlug,
                'title'             => __( 'Custom HTML Output', 'admin-page-framework-demo' ),
                'description'       => __( 'You can insert custom HTML output along with the field output.', 'admin-page-framework-demo' ),
            ),               
            array(
                'section_id'        => 'submit_buttons',
                'tab_slug'          => $this->sTabSlug,
                'title'             => __( 'Submit Buttons', 'admin-page-framework-demo' ),
                'description'       => __( 'These are custom submit buttons.', 'admin-page-framework-demo' ),
            )          
        );        
    
        /*
         * MISC fields
         */
        $oAdminPage->addSettingFields(
            'color_picker', // the target section ID.
            array( // Color Picker
                'field_id' => 'color_picker_field',
                'title' => __( 'Color Picker', 'admin-page-framework-demo' ),
                'type' => 'color',
            ),     
            array( // Multiple Color Pickers
                'field_id' => 'multiple_color_picker_field',
                'title' => __( 'Multiple', 'admin-page-framework-demo' ),
                'type' => 'color',
                'label' => __( 'First', 'admin-page-framework-demo' ),
                'delimiter' => '<br />',
                array(
                    'label' => __( 'Second', 'admin-page-framework-demo' ),
                ),
                array(
                    'label' => __( 'Third', 'admin-page-framework-demo' ),
                ),     
            ),     
            array( // Repeatable Color Pickers
                'field_id' => 'color_picker_repeatable_field',
                'title' => __( 'Repeatable', 'admin-page-framework-demo' ),
                'type' => 'color',
                'repeatable' =>    true,
            ),
            array( // Repeatable Color Pickers
                'field_id'  => 'color_picker_sortable',
                'title'     => __( 'Sortable', 'admin-page-framework-demo' ),
                'type'      => 'color',
                'sortable'  => true,
                array(),    // the second item
                array(),    // the third item
                
            )            
        );
        $oAdminPage->addSettingFields(
            'hidden_field', // the target section ID.
            array( // Single Hidden Field
                'field_id'      => 'hidden_single',
                'title'         => __( 'Hidden Field', 'admin-page-framework-demo' ),
                'type'          => 'hidden',
                // 'hidden' =>    true // <-- the field row can be hidden with this option.
                'default'       => __( 'Test value', 'admin-page-framework-demo' ),
                'label'         => __( 'Test label', 'admin-page-framework-demo' ),
            ),
            array( // Single Hidden Field
                'field_id'      => 'hidden_repeatable',
                'title'         => __( 'Repeatable', 'admin-page-framework-demo' ),
                'type'          => 'hidden',
                'value'         => 'HIIDENVALUE',
                'label'         => __( 'Repeat Me', 'admin-page-framework-demo' ),
                'repeatable'    =>    true,
            ),     
            array( // Multiple Hidden Fields
                'field_id'      => 'hidden_miltiple',
                'title'         => __( 'Multiple', 'admin-page-framework-demo' ),
                'type'          => 'hidden',
                'label'         => __( 'First Item', 'admin-page-framework-demo' ),
                'default'       => 'a',
                array(
                    'label'     => __( 'Second Item', 'admin-page-framework-demo' ),
                    'default'   => 'b',
                ),
                array(
                    'label'     => __( 'Third Item', 'admin-page-framework-demo' ),
                    'default'   => 'c',
                ),
                'sortable'      => true,
            )
        );
        $oAdminPage->addSettingFields(
            'raw_html',
            array(
                'field_id'          => 'raw_html_example',
                'title'             => __( 'Raw HTML', 'admin-page-framework-demo' ),
                'type'              => 'my_custom_made_up_non_exisitng_field_type',
                'before_field'      => "<p>This is a custom output inserted with the <code>before_field</code> argument.</p>",
                'after_field'       => "<p>This is a custom output inserted with the <code>after_field</code> argument.</p>",
            )
        );
        $oAdminPage->addSettingFields(
            'submit_buttons', // the target section ID.
            array( // Default Submit Button
                'field_id'          => 'submit_button_field',
                'title'             => __( 'Submit Button', 'admin-page-framework-demo' ),
                'type'              => 'submit',
                'description'       => __( 'This is the default submit button.', 'admin-page-framework-demo' ),
            ),     
            array( // Submit button as a link
                'field_id'          => 'submit_button_link',
                'type'              => 'submit',
                'title'             => __( 'Link Button', 'admin-page-framework-demo' ),
                'description'       => __( 'These buttons serve as a hyper link. Set the url to the <code>href</code> argument to enable this option.', 'admin-page-framework-demo' ),
                'label'             => __( 'Google', 'admin-page-framework-demo' ),
                'href'              => 'http://www.google.com',
                'attributes'        => array(
                    'class'     => 'button button-secondary',     
                    'title'     => __( 'Go to Google!', 'admin-page-framework-demo' ),
                    'style'     => 'background-color: #C1DCFA;',
                    'field'     => array(
                        'style' => 'display: inline; clear: none;',
                    ),
                ),
                array(
                    'label'         => __( 'Yahoo', 'admin-page-framework-demo' ),
                    'href'          => 'http://www.yahoo.com',
                    'attributes'    => array(
                        'class' => 'button button-secondary',     
                        'title' => __( 'Go to Yahoo!', 'admin-page-framework-demo' ),
                        'style' => 'background-color: #C8AEFF;',
                    ),
                ),
                array(
                    'label'         => __( 'Bing', 'admin-page-framework-demo' ),
                    'href'          => 'http://www.bing.com',
                    'attributes'    => array(
                        'class' => 'button button-secondary',     
                        'title' => __( 'Go to Bing!', 'admin-page-framework-demo' ),
                        'style' => 'background-color: #FFE5AE;',
                    ),     
                ),     
            ),     
            array( 
                'field_id'      => 'submit_button_download',
                'title'         => __( 'Download Button', 'admin-page-framework-demo' ),
                'type'          => 'submit',
                'label'         => __( 'Admin Page Framework', 'admin-page-framework-demo' ),
                'description'   => __( 'Download the latest version of the Admin Page Framework Demo plugin.', 'admin-page-framework-demo' ),
                'href'          => 'http://downloads.wordpress.org/plugin/admin-page-framework.latest-stable.zip',
            ),            
            array( // Submit button as a redirect
                'field_id'      => 'submit_button_redirect',
                'title'         => __( 'Redirect Button', 'admin-page-framework-demo' ),
                'type'          => 'submit',
                'description'   => sprintf( __( 'Unlike the above link buttons, this button saves the options and then redirects to: <code>%1$s</code>', 'admin-page-framework-demo' ), admin_url() )
                    . ' ' . __( 'To enable this functionality, set the url to the <code>redirect_url</code> argument in the field definition array.', 'admin-page-framework-demo' ),
                'label'         => __( 'Dashboard', 'admin-page-framework-demo' ),
                'redirect_url'  => admin_url(),
                'attributes'    => array(
                    'class' => 'button button-secondary',
                ),
            ),
            array( // Reset Submit button
                'field_id'      => 'submit_button_reset',
                'title'         => __( 'Reset Button', 'admin-page-framework-demo' ),
                'type'          => 'submit',
                'label'         => __( 'Reset', 'admin-page-framework-demo' ),
                'reset'         => true,
                'attributes'    => array(
                    'class' => 'button button-secondary',
                ),
                'description'   => __( 'If you press this button, a confirmation message will appear and then if you press it again, it resets the option.', 'admin-page-framework-demo' ),
            ),
            array( // with an image
                'field_id'          => 'image_submit_button',
                'title'             => __( 'Image Submit Button', 'admin-page-framework-demo' ),
                'type'              => 'submit',
                'href'              => 'http://en.michaeluno.jp/donate',
                'attributes'        =>  array(
                   'src'    => APFDEMO_DIRNAME . '/asset/image/donation.gif',
                   'alt'    => __( 'Submit', 'admin-page-framework-demo' ),
                   'class'  => '',
                ),
                'description'   => __( 'For a custom image submit button, set the image url in the <code>src</code> attribute with the <code>attributes</code> argument.', 'admin-page-framework-demo' )
                    . ' ' . __( 'This button will take you to the donation page for the developer of this framework. If you like to donate, please do so to help the development!', 'admin-page-framework-demo' ),
            ),    
            array()
        );    

    }
    
}