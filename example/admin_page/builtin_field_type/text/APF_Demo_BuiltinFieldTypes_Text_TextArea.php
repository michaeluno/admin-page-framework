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
class APF_Demo_BuiltinFieldTypes_Text_TextArea {
    
    
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_builtin_field_types';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'textfields';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'textarea_fields';
        
    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {
    
        // Section
        $oFactory->addSettingSections(    
            $this->sPageSlug, // the target page slug                
            array(
                'section_id'    => $this->sSectionID,       // avoid hyphen(dash), dots, and white spaces
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Text Areas', 'admin-page-framework-loader' ),
                'description'   => __( 'Text input with multiple lines.', 'admin-page-framework-loader' ),
            )
        );   
             
        /*
         * Text area fields.
         */        
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section id
            array( // Text Area
                'field_id'      => 'textarea',
                'title'         => __( 'Text Area', 'admin-page-framework-loader' ),
                'description'   => __( 'Type a text string here.', 'admin-page-framework-loader' ),
                'type'          => 'textarea',
                'default'       => __( 'Hello world!', 'admin-page-framework-loader' ) 
                    . ' ' . __( 'This is set as the default string.', 'admin-page-framework-loader' ),
                'attributes'    => array(
                    'rows' => 6,
                    'cols' => 60,
                ),
            ),
            array( // Multiple Text Area
                'field_id'      => 'textarea_multiple_with_labels',
                'title'         => __( 'Multiple', 'admin-page-framework-loader' ),
                'type'          => 'textarea',
                'default'       => array(
                    'a' => __( 'This is an apple.', 'admin-page-framework-loader' ),
                    'b' => __( 'This is a banana.', 'admin-page-framework-loader' ),
                    'c' => __( 'This is a cherry.', 'admin-page-framework-loader' ),
                ),
                'label'         => array(
                    'a' => __( 'Apple', 'admin-page-framework-loader' ),
                    'b' => __( 'Banana', 'admin-page-framework-loader' ),
                    'c' => __( 'Cherry', 'admin-page-framework-loader' ),
                ),
            ),            
            array( // Repeatable Text Areas
                'field_id'      => 'textarea_repeatable',
                'title'         => __( 'Repeatable', 'admin-page-framework-loader' ),
                'type'          => 'textarea',
                'repeatable'    => array(
                    'max' => 20,
                    'min' => 2,
                ),
                'attributes'    => array(
                    'rows' => 3,
                    'cols' => 60,
                ),
            ),     
            array( // Sortable Text Areas
                'field_id'      => 'textarea_sortable',
                'title'         => __( 'Sortable', 'admin-page-framework-loader' ),
                'type'          => 'textarea',
                'sortable'      =>    true,
                'label'         => __( 'Sortable Item', 'admin-page-framework-loader' ),
                array(), // the second item
                array(), // the third item
            ),     
            array( // Rich Text Editors
                'field_id'      => 'rich_textarea',
                'title'         => __( 'Rich Text Area', 'admin-page-framework-loader' ),
                'type'          => 'textarea',
                'rich'          =>    true, // just pass non empty value to enable the rich editor.
                'attributes'    => array(
                    'field' => array(
                        'style' => 'width: 100%;' // since the rich editor does not accept the cols attribute, set the width by inline-style.
                    ),
                ),
                'description'   =>  __( 'The arguments can be passed to the <code>rich</code> argument.', 'admin-page-framework-loader' )
                    . sprintf( __( 'For more information see the <a href="%1$s">Codex page</a>.', 'admin-page-framework-loader' ), 'http://codex.wordpress.org/Function_Reference/wp_editor#Parameters' ),
                array(
                    // pass the setting array to customize the editor. For the setting argument, see http://codex.wordpress.org/Function_Reference/wp_editor.
                    'rich' => array( 
                        'media_buttons' => false, 
                        'tinymce'       => false
                    ),    
                ),
            ),     
            array( // Multiple text areas
                'field_id'      => 'textarea_multiple',
                'title'         => __( 'Multiple', 'admin-page-framework-loader' ),
                'description'   => __( 'These are multiple text areas.', 'admin-page-framework-loader' ),
                'type'          => 'textarea',
                'label'         => __( 'First', 'admin-page-framework-loader' ),
                'default'       => __( 'The first default text.', 'admin-page-framework-loader' ),
                'delimiter'     => '<br />',
                'attributes'    => array(
                    'rows' => 5,
                    'cols' => 60,
                ),
                array(
                    'label'         => __( 'Second', 'admin-page-framework-loader' ),
                    'default'       => __( 'The second default text. See the background color is different from the others. This is done with the <code>attributes</code> argument.', 'admin-page-framework-loader' ),
                    'attributes'    => array(
                        'rows'  => 3,
                        'cols'  => 40,
                        'style' => 'background-color: #F0F8FA;' // this changes the style of the textarea tag.
                    ),     
                ),
                array(
                    'label'         => __( 'Third', 'admin-page-framework-loader' ),
                    'default'       => __( 'The third default text.', 'admin-page-framework-loader' ),
                    'attributes'    => array(
                        'rows' => 2,
                        'cols' => 20,
                    ),     
                ),    
            ),
            array()
        );             
        
    }
    
  
}