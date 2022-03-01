<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * A text area field that lets the user set text values with multiple lines.
 *
 * This class defines the `textarea` field type. With the `rich` argument, the TinyMCE editor is available.
 *
 * <h3>Example</h3>
 * <code>
 *  array(
 *      'field_id'      => 'textarea',
 *      'title'         => 'Text Area',
 *      'type'          => 'textarea',
 *      'default'       => 'This is a default string value.',
 *      'attributes'    => array(
 *          'rows' => 6,
 *          'cols' => 60,
 *      ),
 *  ),
 * </code>
 * <code>
 *  array(
 *      'field_id'      => 'rich_textarea',
 *      'title'         => 'Rich Text Area',
 *      'type'          => 'textarea',
 *      'rich'          => true,
 *      'attributes'    => array(
 *          'field' => array(
 *              'style' => 'width: 100%;' // since the rich editor does not accept the cols attribute, set the width by inline-style.
 *          ),
 *      ),
 *  ),
 * </code>
 * <code>
 *  array(
 *      'field_id'      => 'rich_textarea',
 *      'title'         => 'Rich Text Area',
 *      'type'          => 'textarea',
 *      // pass the setting array to customize the editor. For the setting argument, see http://codex.wordpress.org/Function_Reference/wp_editor.
 *      'rich' => array(
 *          'media_buttons' => false,
 *          'tinymce'       => false
 *      ),
 *      'attributes'    => array(
 *          'field' => array(
 *              'style' => 'width: 100%;' // since the rich editor does not accept the cols attribute, set the width by inline-style.
 *          ),
 *      ),
 *  ),
 * </code>
 *
 * <h2>Field Definition Arguments</h2>
 * <h3>Field Type Specific Arguments</h3>
 * <ul>
 *     <li>**rich** - [2.1.2+] (optional, boolean|array) to make it a rich text editor, set it `true`. It accepts a setting array of the `_WP_Editors` class defined in the core with the following arguments.
 *     <blockquote>
 *      <ul>
 *         <li>wpautop (boolean) (optional) Whether to use wpautop for adding in paragraphs. Note that the paragraphs are added automatically when wpautop is false. Default: true </li>
 *         <li>media_buttons (boolean) (optional) Whether to display media insert/upload buttons. Default: true </li>
 *         <li>textarea_name (string) (optional) The name assigned to the generated textarea and passed parameter when the form is submitted. (may include [] to pass data as array). Default: $editor_id </li>
 *         <li>textarea_rows (integer) (optional) The number of rows to display for the textarea. Default: get_option('default_post_edit_rows', 10) </li>
 *         <li>tabindex (integer) (optional) The tabindex value used for the form field. Default: None </li>
 *         <li>editor_css (string) (optional) Additional CSS styling applied for both visual and HTML editors buttons, needs to include <style> tags, can use "scoped". Default: None </li>
 *         <li>editor_class (string) (optional) Any extra CSS Classes to append to the Editor textarea. Default: Empty string </li>
 *         <li>editor_height (integer) (optional) The height to set the editor in pixels. If set, will be used instead of textarea_rows. (since WordPress 3.5). Default: None </li>
 *         <li>teeny (boolean) (optional) Whether to output the minimal editor configuration used in PressThis. Default: false </li>
 *         <li>dfw (boolean) (optional) Whether to replace the default fullscreen editor with DFW (needs specific DOM elements and CSS). Default: false </li>
 *         <li>tinymce (array) (optional) Load TinyMCE, can be used to pass settings directly to TinyMCE using an array. Default: true </li>
 *         <li>quicktags (array) (optional) Load Quicktags, can be used to pass settings directly to Quicktags using an array. Set to false to remove your editor's Visual and Text tabs. Default: true</li>
 *         <li>drag_drop_upload (boolean) (optional) Enable Drag & Drop Upload Support (since WordPress 3.9). Default: false </li>
 *      </ul>
 *     </blockquote>
 *     (source, [wp_editor](http://codex.wordpress.org/Function_Reference/wp_editor))
 *     </li>
 * </ul>
 *
 * <h3>Common Field Definition Arguments</h3>
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 *
 * @image           http://admin-page-framework.michaeluno.jp/image/common/form/field_type/textarea.png
 * @package         AdminPageFramework/Common/Form/FieldType
 * @since           2.1.5
 * @since           3.3.1       Changed to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
 */
class AdminPageFramework_FieldType_textarea extends AdminPageFramework_FieldType {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'textarea' );

    /**
     * Defines the default key-values of this field type.
     *
     * @remark $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'rich'          => false,
        'attributes'    => array(
            'autofocus'     => null,
            'cols'          => 60,
            'disabled'      => null,
            'formNew'       => null,
            'maxlength'     => null,
            'placeholder'   => null,
            'readonly'      => null,
            'required'      => null,
            'rows'          => 4,
            'wrap'          => null,
        ),
    );

    /**
     * @return array
     * @since  3.9.0
     */
    protected function getEnqueuingScripts() {
        return array(
            array(
                'handle_id'         => 'admin-page-framework-field-type-textarea',
                'src'               => dirname( __FILE__ ) . '/js/textarea.bundle.js',
                'in_footer'         => true,
                'dependencies'      => array( 'jquery', 'admin-page-framework-script-form-main' ),
                'translation_var'   => 'AdminPageFrameworkFieldTypeTextArea',
                'translation'       => array(
                    'fieldTypeSlugs' => $this->aFieldTypeSlugs,
                    'label'          => array(),
                ),
            ),
        );
    }

    /**
     * Returns the output of the 'textarea' input field.
     *
     * @since       2.1.5
     * @since       3.0.0       Removed redundant elements including parameters.
     * @since       3.3.1       Changed from `_replyToGetField()`.
     * @internal
     * @return      string
     */
    protected function getField( $aField ) {

        $_aOutput = array();
        foreach( ( array ) $aField[ 'label' ] as $_sKey => $_sLabel ) {
            $_aOutput[] = $this->_getFieldOutputByLabel(
                $_sKey,
                $_sLabel,
                $aField
            );
        }

        // the repeatable field buttons will be replaced with this element.
        $_aOutput[] = "<div class='repeatable-field-buttons'></div>";
        return implode( '', $_aOutput );

    }
        /**
         *
         * @internal
         * @since       3.5.8
         * @return      string
         */
        private function _getFieldOutputByLabel( $sKey, $sLabel, $aField ) {

            $_bIsArray          = is_array( $aField[ 'label' ] );
            $_sClassSelector    = $_bIsArray
                ? 'admin-page-framework-field-textarea-multiple-labels'
                : '';
            $_sLabel                = $this->getElementByLabel( $aField[ 'label' ], $sKey, $aField[ 'label' ] );
            $aField[ 'value' ]      = $this->getElementByLabel( $aField[ 'value' ], $sKey, $aField[ 'label' ] );
            $aField[ 'rich' ]       = $this->getElementByLabel( $aField[ 'rich' ], $sKey, $aField[ 'label' ] );
            $aField[ 'attributes' ] = $_bIsArray
                ? array(
                        'name'  => $aField[ 'attributes' ][ 'name' ] . "[{$sKey}]",
                        'id'    => $aField[ 'attributes' ][ 'id' ] . "_{$sKey}",
                        'value' => $aField[ 'value' ],
                    )
                    + $aField[ 'attributes' ]
                : $aField[ 'attributes' ];
            $_aOutput           = array(
                $this->getElementByLabel( $aField['before_label'], $sKey, $aField[ 'label' ] ),
                "<div class='admin-page-framework-input-label-container {$_sClassSelector}'>",
                    "<label for='" . $aField[ 'attributes' ][ 'id' ] . "'>",
                        $this->getElementByLabel( $aField['before_input'], $sKey, $aField[ 'label' ] ),
                        $_sLabel
                            ? "<span " . $this->getLabelContainerAttributes( $aField, 'admin-page-framework-input-label-string' ) . ">"
                                    . $_sLabel
                                . "</span>"
                            : '',
                        $this->_getEditor( $aField ),
                        $this->getElementByLabel( $aField['after_input'], $sKey, $aField[ 'label' ] ),
                    "</label>",
                "</div>",
                $this->getElementByLabel( $aField['after_label'], $sKey, $aField[ 'label' ] ),
            );
            return implode( '', $_aOutput );

        }

        /**
         * Returns the output of the editor.
         *
         * @since       3.0.7
         * @internal
         * @return      string
         */
        private function _getEditor( $aField ) {

            unset( $aField[ 'attributes' ][ 'value' ] );

            // For no TinyMCE
            if ( empty( $aField[ 'rich' ] ) || ! $this->isTinyMCESupported() ) {
                return "<textarea " . $this->getAttributes( $aField[ 'attributes' ] ) . " >" // this method is defined in the base class
                            . esc_textarea( $aField[ 'value' ] )
                        . "</textarea>";
            }

            // Rich editor
            ob_start();
            wp_editor(
                $aField[ 'value' ],
                $aField[ 'attributes' ][ 'id' ],
                $this->uniteArrays(
                    ( array ) $aField[ 'rich' ],
                    array(
                        'wpautop'           => true, // use wpautop?
                        'media_buttons'     => true, // show insert/upload button(s)
                        'textarea_name'     => $aField[ 'attributes' ][ 'name' ],
                        'textarea_rows'     => $aField[ 'attributes' ][ 'rows' ],
                        'tabindex'          => '',
                        'tabfocus_elements' => ':prev,:next', // the previous and next element ID to move the focus to when pressing the Tab key in TinyMCE
                        'editor_css'        => '', // intended for extra styles for both visual and Text editors buttons, needs to include the <style> tags, can use "scoped".
                        'editor_class'      => $aField[ 'attributes' ][ 'class' ], // add extra class(es) to the editor textarea
                        'teeny'             => false, // output the minimal editor config used in Press This
                        'dfw'               => false, // replace the default fullscreen with DFW (needs specific DOM elements and css)
                        'tinymce'           => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
                        'quicktags'         => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
                    )
                )
            );
            $_sContent = ob_get_contents();
            ob_end_clean();

            return $_sContent
                . "<input type='hidden' class='admin-page-framework-textarea-data-input' data-tinymce-textarea='" . esc_attr( $aField[ 'attributes' ][ 'id' ] ) . "' />"; // needed for JavaScript initialization

        }

}