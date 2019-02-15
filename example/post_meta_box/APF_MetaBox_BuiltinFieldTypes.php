<?php
/**
 * Admin Page Framework - Demo
 *
 * Demonstrates the usage of Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 *
 */

class APF_MetaBox_BuiltinFieldTypes extends AdminPageFramework_MetaBox {

    /*
     * ( optional ) Use the setUp() method to define settings of this meta box.
     */
    public function setUp() {

        /*
         * ( optional ) Adds a contextual help pane at the top right of the page that the meta box resides.
         */
        $this->addHelpText(
            __( 'This text will appear in the contextual help pane.', 'admin-page-framework-loader' ),
            __( 'This description goes to the sidebar of the help pane.', 'admin-page-framework-loader' )
        );

        /*
         * ( optional ) Set form sections - if not set, the system default section will be applied so you don't worry about it.
         */
        $this->addSettingSections(
            array(
                'section_id'        => '_selectors',
                'title'             => __( 'Selectors', 'admin-page-framework-loader' ),
                'description'       => __( 'These are grouped in the <code>selectors</code> section.', 'admin-page-framework-loader' ),
            ),
            array(
                'section_id'        => '_misc',
                'title'             => __( 'MISC', 'admin-page-framework-loader' ),
                'description'       => __( 'These are grouped in the <code>misc</code> section.', 'admin-page-framework-loader' ),
            ),
            array(
                'section_id'        => '_unsaved',
                'title'             => __( 'Unsaved Fields', 'admin-page-framework-loader' ),
                // 'save'              => false,
            )
        );

        /*
         * ( optional ) Adds setting fields into the meta box.
         *
         * It is suggested to start with a prefix of underscore for field ids if the field does not have a section.
         * This is because without it, the field will be shown in the post custom field option in the post editing page.
         * If you set a section, the section id should have a prefix of an underscore.
         */
        $this->addSettingFields(
            array(
                'field_id'      => '_metabox_text_field',
                'type'          => 'text',
                'tip'           => __( 'This is a tool tip.', 'admin-page-framework-loader' ),
                'title'         => __( 'Text Input', 'admin-page-framework-loader' ),
                'description'   => __( 'Type more than two characters.', 'admin-page-framework-loader' ),
                'help'          => __( 'This is help text.', 'admin-page-framework-loader' ),
                'help_aside'    => __( 'This is additional help text which goes to the side bar of the help pane.', 'admin-page-framework-loader' ),
            ),
            array(
                'field_id'      => '_metabox_text_field_repeatable',
                'type'          => 'text',
                'title'         => __( 'Text Repeatable & Sortable', 'admin-page-framework-loader' ),
                'repeatable'    => true,
                'sortable'      => true,
            ),
            array(
                'field_id'      => '_metabox_textarea_field',
                'type'          => 'textarea',
                'title'         => __( 'Text Area', 'admin-page-framework-loader' ),
                'description'   => __( 'The description for the field.', 'admin-page-framework-loader' ),
                'help'          => __( 'This a <em>text area</em> input field, which is larger than the <em>text</em> input field.', 'admin-page-framework-loader' ),
                'default'       => __( 'This is a default text value.', 'admin-page-framework-loader' ),
                'attributes'    => array(
                    'cols' => 40,
                ),
            ),
            array( // Rich Text Editor
                'field_id'      => '_rich_textarea',
                'type'          => 'textarea',
                'title'         => __( 'Rich Text Editor', 'admin-page-framework-loader' ),
                'rich'          =>    true, // array( 'media_buttons' => false )  <-- a setting array can be passed. For the specification of the array, see http://codex.wordpress.org/Function_Reference/wp_editor
            )
        );

        $this->addSettingFields(
            '_selectors',    // section id
            array(
                'field_id'      => 'checkbox_field',
                'type'          => 'checkbox',
                'title'         => __( 'Checkbox Input', 'admin-page-framework-loader' ),
                'description'   => __( 'The description for the field.', 'admin-page-framework-loader' ),
                'label'         => __( 'This is a check box.', 'admin-page-framework-loader' ),
            ),
            array(
                'field_id'      => 'select_filed',
                'type'          => 'select',
                'title'         => __( 'Select Box', 'admin-page-framework-loader' ),
                'description'   => __( 'The description for the field.', 'admin-page-framework-loader' ),
                'default'       => 'one', // 0 means the first item
                'label'         => array(
                    'one'   => __( 'One', 'admin-page-framework-loader' ),
                    'two'   => __( 'Two', 'admin-page-framework-loader' ),
                    'three' => __( 'Three', 'admin-page-framework-loader' ),
                ),
            ),
            array (
                'field_id'      => 'radio_field',
                'type'          => 'radio',
                'title'         => __( 'Radio Group', 'admin-page-framework-loader' ),
                'description'   => __( 'The description for the field.', 'admin-page-framework-loader' ),
                'default'       => 'one',
                'label'         => array(
                    'one'   => __( 'This option is the first item of the radio button example field and lets the user choose one from many.', 'admin-page-framework-loader' ),
                    'two'   => __( 'This option is the second item of the radio button example field.', 'admin-page-framework-loader' ),
                    'three' => __( 'This option is the third item of the radio button example field.', 'admin-page-framework-loader' ),
                ),
            ),
            array (
                'field_id'      => 'checkbox_group_field',
                'type'          => 'checkbox',
                'title'         => __( 'Checkbox Group', 'admin-page-framework-loader' ),
                'description'   => __( 'The description for the field.', 'admin-page-framework-loader' ),
                'label'         => array(
                    'one'   => __( 'This option is the first item of the checkbox button example field.', 'admin-page-framework-loader' ),
                    'two'   => __( 'This option is the second item of the radio button example field.', 'admin-page-framework-loader' ),
                    'three' => __( 'This option is the third item of the radio button example field.', 'admin-page-framework-loader' ),
                ),
                'default'       => array(
                    'one'   => true,
                    'two'   => false,
                    'three' => false,
                ),
            )
        );

        $this->addSettingFields(
            '_misc', // section id
            array (
                'field_id'          => 'image_field',
                'type'              => 'image',
                'title'             => __( 'Image', 'admin-page-framework-loader' ),
                'description'       => __( 'The description for the field.', 'admin-page-framework-loader' ),
            ),
            array(
                'field_id'          => 'metabox_password',
                'type'              => 'password',
                'title'             => __( 'Password', 'admin-page-framework-loader' ),
            ),
            array (
                'field_id'          => 'color_field',
                'type'              => 'color',
                'title'             => __( 'Color', 'admin-page-framework-loader' ),
            ),
            array (
                'field_id'          => 'size_field',
                'type'              => 'size',
                'title'             => __( 'Size', 'admin-page-framework-loader' ),
                'default'           => array( 'size' => 5, 'unit' => '%' ),
            ),
            array (
                'field_id'          => 'sizes_field',
                'type'              => 'size',
                'title'             => __( 'Multiple Sizes', 'admin-page-framework-loader' ),
                'label'             => __( 'Weight', 'admin-page-framework-loader' ),
                'default'           => array( 'size' => 15, 'unit' => 'g' ),
                'units'             => array( 'mg'=>'mg', 'g'=>'g', 'kg'=>'kg' ),
                array(
                    'label'         => __( 'Length', 'admin-page-framework-loader' ),
                    'default'       => array( 'size' => 100, 'unit' => 'mm' ),
                    'units'         => array( 'cm'=>'cm', 'mm'=>'mm', 'm'=>'m' ),
                ),
                array(
                    'label'         => __( 'File Size', 'admin-page-framework-loader' ),
                    'default'       => array( 'size' => 30, 'unit' => 'mb' ),
                    'units'         => array( 'b'=>'b', 'kb'=>'kb', 'mb'=>'mb', 'gb' => 'gb', 'tb' => 'tb' ),
                ),
                'delimiter'         => '<br />',
            ),
            array (
                'field_id'          => 'taxonomy_checklist',
                'type'              => 'taxonomy',
                'title'             => __( 'Taxonomy Checklist', 'admin-page-framework-loader' ),
                'taxonomy_slugs'    => get_taxonomies( '', 'names' ),
            )
        );

        $this->addSettingFields(
            '_unsaved', // section id
            array(
                'field_id'          => 'unsaved',
                'title'             => __( 'Unsaved', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'save'              => false,
                'description'       => __( 'By passing <code>false</code> to the <code>save</code> argument, the form will not save the field value.', 'admin-page-framework-loader' ),
                'attributes'        => array(
                    'readonly'  => 'readonly',
                ),
                'value'             => date_i18n( 'j F Y g:i:s', time() ),
            ),
            array(
                'field_id'          => 'saved',
                'title'             => __( 'Saved', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'save'              => true,
                'description'       => __( 'On contrast to the above field, this field value gets saved.', 'admin-page-framework-loader' ),
                'attributes'        => array(
                    'readonly'  => 'readonly',
                ),
                'default'           => date_i18n( 'j F Y g:i:s', time() + 60*60*24 ),
            )
        );

    }

    /**
     * The content filter callback method.
     *
     * Alternatively use the `content_{instantiated class name}` method instead.
     */
    public function content( $sContent ) {

        $_sInsert = "<p>" . sprintf( __( 'This text is inserted with the <code>%1$s</code> method.', 'admin-page-framework-loader' ), __FUNCTION__ ) . "</p>";
        return $_sInsert . $sContent;

    }

    /**
     * The content filter callback method.
     *
     * @callback        filter      content_{instantiated class name}
     */
    public function content_APF_MetaBox_BuiltinFieldTypes( $sContent ) {

        return $sContent
            . "<p>"
                . sprintf( __( 'This text is inserted with the <code>%1$s</code> hook.', 'admin-page-framework-loader' ), __FUNCTION__ )
            . "</p>";

    }

    /**
     * One of the predefined validation callback methods,
     *
     * Alternatively, you may use `validataion_{instantiated class name}()` method,
     */
    public function validate( $aInput, $aOldInput, $oAdmin ) {

        $_bIsValid  = true;
        $_aErrors   = array();

        // You can check the passed values with the log() method of the oDebug object.
        // $this->oDebug->log( $aInput );

        // Validate the submitted data.
        if ( strlen( trim( $aInput[ '_metabox_text_field' ] ) ) < 3 ) {

            $_aErrors[ '_metabox_text_field' ] = __( 'The entered text is too short! Type more than 2 characters.', 'admin-page-framework-loader' ) . ': ' . $aInput[ '_metabox_text_field' ];
            $_bIsValid = false;

        }

        if ( ! $_bIsValid ) {

            $this->setFieldErrors( $_aErrors );
            $this->setSettingNotice( __( 'There was an error in your input in meta box form fields', 'admin-page-framework-loader' ) );
            return $aOldInput;

        }

        return $aInput;

    }

}

new APF_MetaBox_BuiltinFieldTypes(
    null,  // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
    __( 'Demo Meta Box with Built-in Field Types', 'admin-page-framework-loader' ), // title
    array( 'apf_posts' ),                            // post type slugs: post, page, etc.
    'normal',                                        // context (what kind of metabox this is)
    'high'                                           // priority
);
