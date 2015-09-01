<?php
/**
 * Admin Page Framework - Demo
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds a section in a tab.
 * 
 * @package     AdminPageFramework
 * @subpackage  Example
 */
class APF_Demo_BuiltinFieldTypes_MISC_Submit {
    
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
    public $sSectionID  = 'submit_buttons';        
        
    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {
    
        // Section
        $oFactory->addSettingSections(    
            $this->sPageSlug, // the target page slug                
            array(
                'tab_slug'          => $this->sTabSlug,
                'section_id'        => $this->sSectionID,
                'title'             => __( 'Submit Buttons', 'admin-page-framework-loader' ),
                'description'       => __( 'These are custom submit buttons.', 'admin-page-framework-loader' ),
            )
        );   

        // Fields
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID        
            array( // Default Submit Button
                'field_id'          => 'submit_button_field',
                'title'             => __( 'Submit Button', 'admin-page-framework-loader' ),
                'type'              => 'submit',
                'description'       => __( 'This is the default submit button.', 'admin-page-framework-loader' ),
            ),     
            array( // Submit button as a link
                'field_id'          => 'submit_button_link',
                'type'              => 'submit',
                'title'             => __( 'Link Button', 'admin-page-framework-loader' ),
                'description'       => __( 'These buttons serve as a hyper link. Set the url to the <code>href</code> argument to enable this option.', 'admin-page-framework-loader' ),
                'label'             => __( 'Google', 'admin-page-framework-loader' ),
                'href'              => 'http://www.google.com',
                'attributes'        => array(
                    'class'     => 'button button-secondary',     
                    'title'     => __( 'Go to Google!', 'admin-page-framework-loader' ),
                    'style'     => 'background-color: #C1DCFA;',
                    'field'     => array(
                        'style' => 'display: inline; clear: none;',
                    ),
                ),
                array(
                    'label'         => __( 'Yahoo', 'admin-page-framework-loader' ),
                    'href'          => 'http://www.yahoo.com',
                    'attributes'    => array(
                        'class' => 'button button-secondary',     
                        'title' => __( 'Go to Yahoo!', 'admin-page-framework-loader' ),
                        'style' => 'background-color: #C8AEFF;',
                    ),
                ),
                array(
                    'label'         => __( 'Bing', 'admin-page-framework-loader' ),
                    'href'          => 'http://www.bing.com',
                    'attributes'    => array(
                        'class' => 'button button-secondary',     
                        'title' => __( 'Go to Bing!', 'admin-page-framework-loader' ),
                        'style' => 'background-color: #FFE5AE;',
                    ),     
                ),     
            ),     
            array( 
                'field_id'      => 'submit_button_download',
                'title'         => __( 'Download Button', 'admin-page-framework-loader' ),
                'type'          => 'submit',
                'label'         => __( 'Admin Page Framework', 'admin-page-framework-loader' ),
                'description'   => __( 'Download the latest version of the Admin Page Framework Demo plugin.', 'admin-page-framework-loader' ),
                'href'          => 'http://downloads.wordpress.org/plugin/admin-page-framework.latest-stable.zip',
            ),            
            array( // Submit button as a redirect
                'field_id'      => 'submit_button_redirect',
                'title'         => __( 'Redirect Button', 'admin-page-framework-loader' ),
                'type'          => 'submit',
                'description'   => sprintf( __( 'Unlike the above link buttons, this button saves the options and then redirects to: <code>%1$s</code>', 'admin-page-framework-loader' ), admin_url() )
                    . ' ' . __( 'To enable this functionality, set the url to the <code>redirect_url</code> argument in the field definition array.', 'admin-page-framework-loader' ),
                'label'         => __( 'Dashboard', 'admin-page-framework-loader' ),
                'redirect_url'  => admin_url(),
                'attributes'    => array(
                    'class' => 'button button-secondary',
                ),
            ),
            array( // Reset Submit button
                'field_id'      => 'submit_button_reset',
                'title'         => __( 'Reset Button', 'admin-page-framework-loader' ),
                'type'          => 'submit',
                'label'         => __( 'Reset', 'admin-page-framework-loader' ),
                'reset'         => true,
                'attributes'    => array(
                    'class' => 'button button-secondary',
                ),
                'description'   => __( 'If you press this button, a confirmation message will appear and then if you press it again, it resets the option.', 'admin-page-framework-loader' ),
            ),
            array( // Reset Section
                'field_id'      => 'submit_button_reset_section',
                'title'         => __( 'Reset Section', 'admin-page-framework-loader' ),
                'type'          => 'submit',
                'label'         => __( 'Reset Section', 'admin-page-framework-loader' ),
                'reset'         => 'color_picker',    // the section ID to reset 
                'attributes'    => array(
                    'class' => 'button button-secondary',
                ),
                'description'   => array( 
                    __( 'To reset values of a section, set the section ID to the <code>reset</code> argument.', 'admin-page-framework-loader' ),
                    __( 'As an example, this reset button rests the Color section above.', 'admin-page-framework-loader' ),
                 ),
            ),
            array( // Reset Field
                'field_id'      => 'submit_button_reset_field',
                'title'         => __( 'Reset Field', 'admin-page-framework-loader' ),
                'type'          => 'submit',
                'label'         => __( 'Reset Field', 'admin-page-framework-loader' ),
                'reset'         => array( 
                    'color_picker',         // section ID   
                    'color_picker_field'    // field ID to reset
                ),
                'attributes'    => array(
                    'class' => 'button button-secondary',
                ),
                'description'   => array( 
                    __( 'To reset a value of a particular field, set an array with the the section ID in the first element and field ID in the second element to the <code>reset</code> argument.', 'admin-page-framework-loader' ),
                    __( 'If a field does not have a section, just set the field ID.', 'admin-page-framework-loader' ),
                    __( 'As an example, this reset button rests the first item of the Color section above.', 'admin-page-framework-loader' ),
                ),
            ),            
            array( // with an image
                'field_id'          => 'image_submit_button',
                'title'             => __( 'Image Submit Button', 'admin-page-framework-loader' ),
                'type'              => 'submit',
                'href'              => 'http://en.michaeluno.jp/donate',
                'attributes'        =>  array(
                   'src'    => APFDEMO_DIRNAME . '/asset/image/donation.gif',
                   'alt'    => __( 'Submit', 'admin-page-framework-loader' ),
                   'class'  => '',
                ),
                'description'   => __( 'For a custom image submit button, set the image url in the <code>src</code> attribute with the <code>attributes</code> argument.', 'admin-page-framework-loader' )
                    . ' ' . __( 'This button will take you to the donation page for the developer of this framework. If you like to donate, please do so to help the development!', 'admin-page-framework-loader' ),
            )
        );              
      
    }

}