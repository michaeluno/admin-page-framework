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

class APF_Demo_BuiltinFieldTypes_Text {

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
    public $sTabSlug    = 'textfields';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'text_fields';
    
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
        
        /*
         * ( optional ) Add in-page tabs - In Admin Page Framework, there are two kinds of tabs: page-heading tabs and in-page tabs.
         * Page-heading tabs show the titles of sub-page items which belong to the set root page. 
         * In-page tabs show tabs that you define to be embedded within an individual page.
         */        
        $oAdminPage->addInPageTabs(    
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => 'textfields',
                'title'         => __( 'Text', 'admin-page-framework-demo' ),
                'order'         => 1, // ( optional ) - if you don't set this, an index will be assigned internally in the added order
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
                'title'         => __( 'Text Fields', 'admin-page-framework-demo' ),
                'description'   => __( 'These are text type fields.', 'admin-page-framework-demo' ), // ( optional )
                'order'         => 10, // ( optional ) - if you don't set this, an index will be assigned internally in the added order
            )
        );        

        /*
         * Text input fields - text, password, number, textarea, rich text editor
         */        
        $oAdminPage->addSettingFields(
            $this->sSectionID, // the target section id
            array( // Single text field
                'field_id'          => 'text',
                // 'section_id'     => 'text_fields', // can be omitted as it is set previously
                'title'             => __( 'Text', 'admin-page-framework-demo' ),
                'description'       => array(
                    __( 'Type something here. This text is inserted with the <code>description</code> argument in the field definition array.', 'admin-page-framework-demo' ),
                    __( 'The argument accepts as an array and each element will be treated as one paragraph.', 'admin-page-framework-demo' ),
                ),
                'help'              => __( 'This is a text field and typed text will be saved.', 'admin-page-framework-demo' )
                    . ' ' . __( 'This text is inserted with the <code>help</code> argument in the field definition array.', 'admin-page-framework-demo' ),
                'type'              => 'text',
                'order'             => 1, // ( optional )
                'default'           => 123456,
                'attributes'        => array(
                    'size' => 40,
                ),
            ),    
            array( // Password Field
                'field_id'          => 'password',
                'title'             => __( 'Password', 'admin-page-framework-demo' ),
                'tip'               => __( 'This input will be masked.', 'admin-page-framework-demo' ),
                'type'              => 'password',
                'help'              => __( 'This is a password type field; the user\'s entered input will be masked.', 'admin-page-framework-demo' ), //'
                'attributes'        => array(
                    'size' => 20,
                ),
                'description'       => __( 'The entered characters will be masked.', 'admin-page-framework-demo' ),
            ),     
            array( // Read-only
                'field_id'          => 'read_only_text',
                'title'             => __( 'Read Only', 'admin-page-framework-demo' ),
                'type'              => 'text',
                'attributes'        => array(
                    'size'          => 20,
                    'readonly'      => 'readonly',
                    // 'disabled' => 'disabled', // disabled can be specified like so
                ),
                'value'             => __( 'This is a read-only value.', 'admin-page-framework-demo' ),
                'description'       => __( 'The attribute can be set with the <code>attributes</code> argument.', 'admin-page-framework-demo' ),
            ),     
            array( // Number Field
                'field_id'          => 'number',
                'title'             => __( 'Number', 'admin-page-framework-demo' ),
                'type'              => 'number',
            ),         
            array( // Multiple text fields
                'field_id'          => 'text_multiple',
                'title'             => __( 'Multiple', 'admin-page-framework-demo' ),
                'help'              => __( 'Multiple text fields can be set by passing an array to the <code>label</code> argument.', 'admin-page-framework-demo' ),
                'type'              => 'text',
                'default'           => __( 'Hello world!', 'admin-page-framework-demo' ),
                'label'             => __( 'First', 'admin-page-framework-demo' ) . ': ',
                'attributes'        => array(
                    'size' => 20,     
                ),
                'capability'        => 'manage_options',     
                'delimiter'         => '<br />',
                array(
                    'default'       => 'Foo bar',
                    'label'         => __( 'Second', 'admin-page-framework-demo' ) . ': ',
                    'attributes'    => array(
                        'size' => 40,
                    )
                ),
                array(
                    'default'       => __( 'Yes, we can', 'admin-page-framework-demo' ),
                    'label'         => __( 'Third', 'admin-page-framework-demo' ) . ': ',
                    'attributes'    => array(
                        'size' => 60,
                    )
                ),     
                'description'       => __( 'These are multiple text fields. To include multiple input fields associated with one field ID, use the numeric keys in the field definition array.', 'admin-page-framework-demo' ),
            ),     
            array( // Repeatable text fields
                'field_id'          => 'text_repeatable',
                'title'             => __( 'Repeatable', 'admin-page-framework-demo' ),
                'type'              => 'text',
                'default'           => 'a',
                'capability'        => 'manage_options',
                'repeatable'        => array(
                    'max' => 10,
                    'min' => 3,
                ),
                'description'       => array( 
                    __( 'Press + / - to add / remove the fields. To enable the repeatable fields functionality, set the <code>repeatable</code> argument to <code>true</code>.', 'admin-page-framework-demo' ),
                    __( 'To set maximum and minimum numbers of fields, set the <code>max</code> and <code>min</code> arguments in the <code>repeatable</code> argument array in the field definition array.' ),
                ),
            ),     
            array( // Sortable text fields
                'field_id'          => 'text_sortable',
                'title'             => __( 'Sortable', 'admin-page-framework-demo' ),
                'type'              => 'text',
                'default'           => 'a',
                'label'             => __( 'Sortable Item', 'admin-page-framework-demo' ),
                'sortable'          =>    true,
                'description'       => __( 'Drag and drop the fields to change the order.', 'admin-page-framework-demo' ),
                array(
                    'default'       => 'b',
                ),
                array(
                    'default'       => 'c',
                ),     
                array(
                    'label'         => __( 'Disabled Item', 'admin-page-framework-demo' ),
                    'default'       => 'd',
                    'attributes'    => array(
                        'disabled' => 'disabled',
                    ),
                ),     
                'delimiter'     => '<br />',
            ),    
            array( // Sortable + Repeatable text fields
                'field_id'      => 'text_repeatable_and_sortable',
                'title'         => __( 'Repeatable & Sortable', 'admin-page-framework-demo' ),
                'type'          => 'text',
                'repeatable'    => true,
                'sortable'      => true,
            ),     
            array( // Text Area
                'field_id'      => 'textarea',
                'title'         => __( 'Text Area', 'admin-page-framework-demo' ),
                'description'   => __( 'Type a text string here.', 'admin-page-framework-demo' ),
                'type'          => 'textarea',
                'default'       => __( 'Hello world!', 'admin-page-framework-demo' ) 
                    . ' ' . __( 'This is set as the default string.', 'admin-page-framework-demo' ),
                'attributes'    => array(
                    'rows' => 6,
                    'cols' => 60,
                ),
            ),
            array( // Repeatable Text Areas
                'field_id'      => 'textarea_repeatable',
                'title'         => __( 'Repeatable', 'admin-page-framework-demo' ),
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
                'title'         => __( 'Sortable', 'admin-page-framework-demo' ),
                'type'          => 'textarea',
                'sortable'      =>    true,
                'label'         => __( 'Sortable Item', 'admin-page-framework-demo' ),
                array(), // the second item
                array(), // the third item
            ),     
            array( // Rich Text Editors
                'field_id'      => 'rich_textarea',
                'title'         => __( 'Rich Text Area', 'admin-page-framework-demo' ),
                'type'          => 'textarea',
                'rich'          =>    true, // just pass non empty value to enable the rich editor.
                'attributes'    => array(
                    'field' => array(
                        'style' => 'width: 100%;' // since the rich editor does not accept the cols attribute, set the width by inline-style.
                    ),
                ),
                'description'   =>  __( 'The arguments can be passed to the <code>rich</code> argument.', 'admin-page-framework-demo' )
                    . sprintf( __( 'For more information see the <a href="%1$s">Codex page</a>.', 'admin-page-framework-demo' ), 'http://codex.wordpress.org/Function_Reference/wp_editor#Parameters' ),
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
                'title'         => __( 'Multiple', 'admin-page-framework-demo' ),
                'description'   => __( 'These are multiple text areas.', 'admin-page-framework-demo' ),
                'type'          => 'textarea',
                'label'         => __( 'First', 'admin-page-framework-demo' ),
                'default'       => __( 'The first default text.', 'admin-page-framework-demo' ),
                'delimiter'     => '<br />',
                'attributes'    => array(
                    'rows' => 5,
                    'cols' => 60,
                ),
                array(
                    'label'         => __( 'Second', 'admin-page-framework-demo' ),
                    'default'       => __( 'The second default text. See the background color is different from the others. This is done with the <code>attributes</code> argument.', 'admin-page-framework-demo' ),
                    'attributes'    => array(
                        'rows'  => 3,
                        'cols'  => 40,
                        'style' => 'background-color: #F0F8FA;' // this changes the style of the textarea tag.
                    ),     
                ),
                array(
                    'label'         => __( 'Third', 'admin-page-framework-demo' ),
                    'default'       => __( 'The third default text.', 'admin-page-framework-demo' ),
                    'attributes'    => array(
                        'rows' => 2,
                        'cols' => 20,
                    ),     
                ),    
            ),
            array( // Repeatable TinyMCE Text Editor [3.1.6+]
                'field_id'      => 'repeatable_rich_textarea',
                'title'         => __( 'Repeatable', 'admin-page-framework-demo' ),
                'type'          => 'textarea',
                'rich'          => true,
                'repeatable'    => true,
                'description'   => __( 'As of v3.1.6, repeatable TinyMCE editor fields are supported.', 'admin-page-framework-demo' ),
            )
        );             
        
    }
    
}