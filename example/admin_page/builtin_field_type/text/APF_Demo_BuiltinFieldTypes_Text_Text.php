<?php
/**
 * Admin Page Framework - Demo
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds a section in a tab.
 * 
 * @package     AdminPageFramework
 * @subpackage  Example
 */
class APF_Demo_BuiltinFieldTypes_Text_Text {
    
    
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
    public $sSectionID  = 'text_fields';
        
    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {
    
        /*
         * ( optional ) Create a form - To create a form in Admin Page Framework, you need two kinds of components: sections and fields.
         * A section groups fields and fields belong to a section. So a section needs to be created prior to fields.
         * Use the addSettingSections() method to create sections and use the addSettingFields() method to create fields.
         */
        // Section
        $oFactory->addSettingSections(
            $this->sPageSlug, // the target page slug                
            array(
                'section_id'    => $this->sSectionID,       // avoid hyphen(dash), dots, and white spaces
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Text Fields', 'admin-page-framework-loader' ),
                'tip'           => __( 'With the <code>tip</code> argument in a section definition array, a tool tip can be inserted.', 'admin-page-framework-loader' ),
                'description'   => __( 'These are text type fields.', 'admin-page-framework-loader' ), // ( optional )
                'order'         => 10, // ( optional ) - if you don't set this, an index will be assigned internally in the added order
            )
        );
        
        /*
         * Text input fields - text, password, number, textarea, rich text editor
         */
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section id
            array( // Single text field
                'field_id'          => 'text',
                // 'section_id'     => 'text_fields', // can be omitted as it is set previously
                'title'             => __( 'Text', 'admin-page-framework-loader' ),
                'description'       => array(
                    __( 'Type something here. This text is inserted with the <code>description</code> argument in the field definition array.', 'admin-page-framework-loader' ),
                    __( 'The argument accepts as an array and each element will be treated as one paragraph.', 'admin-page-framework-loader' ),
                ),
                'tip'               => __( 'With the <code>tip</code> argument in a field definition array, a tool tip can be inserted.', 'admin-page-framework-loader' ),
                'help'              => __( 'This is a text field and typed text will be saved.', 'admin-page-framework-loader' )
                    . ' ' . __( 'This text is inserted with the <code>help</code> argument in the field definition array.', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'order'             => 1, // ( optional )
                'default'           => 123456,
                'attributes'        => array(
                    'size' => 40,
                ),
            ),
            array( // Password Field
                'field_id'          => 'password',
                'title'             => __( 'Password', 'admin-page-framework-loader' ),
                'tip'               => __( 'This input will be masked.', 'admin-page-framework-loader' ),
                'type'              => 'password',
                'help'              => __( 'This is a password type field; the user\'s entered input will be masked.', 'admin-page-framework-loader' ), //'
                'attributes'        => array(
                    'size' => 20,
                ),
                'description'       => __( 'The entered characters will be masked.', 'admin-page-framework-loader' ),
            ),
            array( // Read-only
                'field_id'          => 'read_only_text',
                'title'             => __( 'Read Only', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'attributes'        => array(
                    'size'          => 20,
                    'readonly'      => 'readonly',
                    // 'disabled' => 'disabled', // disabled can be specified like so
                ),
                'value'             => __( 'This is a read-only value.', 'admin-page-framework-loader' ),
                'description'       => __( 'The attribute can be set with the <code>attributes</code> argument.', 'admin-page-framework-loader' ),
            ),
            array( // Number Field
                'field_id'          => 'number',
                'title'             => __( 'Number', 'admin-page-framework-loader' ),
                'type'              => 'number',
            ),
            array( // sub fields
                'field_id'          => 'text_multiple',
                'title'             => __( 'Multiple', 'admin-page-framework-loader' ),
                'help'              => __( 'Multiple text fields can be set by passing an array to the <code>label</code> argument.', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'default'           => __( 'Hello world!', 'admin-page-framework-loader' ),
                'label'             => __( 'First', 'admin-page-framework-loader' ) . ': ',
                'attributes'        => array(
                    'size' => 20,
                ),
                'capability'        => 'manage_options',
                'delimiter'         => '<br />',
                array(
                    'default'       => 'Foo bar',
                    'label'         => __( 'Second', 'admin-page-framework-loader' ) . ': ',
                    'attributes'    => array(
                        'size' => 40,
                    ),
                ),
                array(
                    'default'       => __( 'Yes, we can', 'admin-page-framework-loader' ),
                    'label'         => __( 'Third', 'admin-page-framework-loader' ) . ': ',
                    'attributes'    => array(
                        'size' => 60,
                    ),
                ),
                'description'       => __( 'These are multiple text fields. To include multiple input fields associated with one field ID, use the numeric keys in the field definition array.', 'admin-page-framework-loader' ),
            ),
            array( // Multiple text fields by labels
                'field_id'          => 'text_multiple_with_label',
                'title'             => __( 'Multiple with Labels', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'default'           => array(
                    'first'  => 'First Item',
                    'second' => 'Second Item',
                    'third'  => 'Third Item',
                ),
                'label'             => array(
                    'first'  => __( 'First', 'admin-page-framework-loader' ),
                    'second' => __( 'Second', 'admin-page-framework-loader' ),
                    'third'  => __( 'Third', 'admin-page-framework-loader' ),
                ),
                'description'       => __( 'These uses the <code>label</code> argument to crate multiple elements.', 'admin-page-framework-loader' ),
            ),
            array( // Repeatable text fields
                'field_id'          => 'text_repeatable',
                'title'             => __( 'Repeatable', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'default'           => 'a',
                'capability'        => 'manage_options',
                'repeatable'        => array(
                    'max' => 10,
                    'min' => 3,
                ),
                'tip'               => __( 'With the <code>repeatable</code> argument, you can let your users add and remove field items dynamically.', 'admin-page-framework-loader' ),
                'description'       => array(
                    __( 'Press + / - to add / remove the fields. To enable the repeatable fields functionality, set the <code>repeatable</code> argument to <code>true</code>.', 'admin-page-framework-loader' ),
                    __( 'To set maximum and minimum numbers of fields, set the <code>max</code> and <code>min</code> arguments in the <code>repeatable</code> argument array in the field definition array.' ),
                ),
            ),
            array( // Sortable text fields
                'field_id'          => 'text_sortable',
                'title'             => __( 'Sortable', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'default'           => 'a',
                'label'             => __( 'Sortable Item', 'admin-page-framework-loader' ),
                'sortable'          =>    true,
                'description'       => __( 'Drag and drop the fields to change the order.', 'admin-page-framework-loader' ),
                array(
                    'default'       => 'b',
                ),
                array(
                    'default'       => 'c',
                ),
                array(
                    'label'         => __( 'Disabled Item', 'admin-page-framework-loader' ),
                    'default'       => 'd',
                    'attributes'    => array(
                        'disabled' => 'disabled',
                    ),
                ),
            ),
            array( // Sortable + Repeatable text fields
                'field_id'      => 'text_repeatable_and_sortable',
                'title'         => __( 'Repeatable & Sortable', 'admin-page-framework-loader' ),
                'type'          => 'text',
                'repeatable'    => true,
                'sortable'      => true,
            )
        );
        
    }
    
  
}
