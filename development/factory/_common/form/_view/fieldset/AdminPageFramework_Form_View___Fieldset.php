<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods for rendering form input field-sets.
 *
 * @since    2.0.0
 * @since    2.0.1       Added the <em>size</em> type.
 * @since    2.1.5       Separated the methods that defines field types to different classes.
 * @since    3.6.0       Changed the name from `AdminPageFramework_FormField`.
 * @package  AdminPageFramework/Common/Form/View/Field
 * @internal
 */
class AdminPageFramework_Form_View___Fieldset extends AdminPageFramework_Form_View___Fieldset_Base {

    /**
     * Returns the field-set HTML output.
     *
     * @since  3.6.0
     * @return string
     */
    public function get() {

        $_aOutputs      = array();

        // Prepend the field error message.
        $_oFieldError   = new AdminPageFramework_Form_View___Fieldset___FieldError(
            $this->aErrors,
            $this->aFieldset[ '_section_path_array' ],
            $this->aFieldset[ '_field_path_array' ],
            $this->aFieldset[ 'error_message' ]
        );
        $_aOutputs[]     = $_oFieldError->get();

        // Construct fields array for sub-fields.
        $_oFieldsFormatter = new AdminPageFramework_Form_Model___Format_Fields(
            $this->aFieldset,
            $this->aOptions
        );
        $_aFields = $_oFieldsFormatter->get();

        /// Get the field and its sub-fields output.
        $_aOutputs[] = $this->___getFieldsOutput(
            $this->aFieldset,
            $_aFields,
            $this->aCallbacks
        );

        // Return the entire output.
        return $this->___getFinalOutput( $this->aFieldset, $_aOutputs, count( $_aFields ) );

    }
        /**
         * Returns the output of the given fieldset (main field and its sub-fields) array.
         *
         * @since   3.1.0
         * @since   3.2.0  Added the `$aCallbacks` parameter.
         * @since   3.8.0  Added the `$aFieldset` parameter
         * @return  string
         */
        private function ___getFieldsOutput( $aFieldset, array $aFields, array $aCallbacks=array() ) {
            $_aOutput = array();
            foreach( $aFields as $_isIndex => $_aField ) {
                $_aOutput[] = $this->___getEachFieldOutput(
                    $_aField,
                    $_isIndex,
                    $aCallbacks,
                    $this->isLastElement( $aFields, $_isIndex )
                );
            }
            return implode( PHP_EOL, array_filter( $_aOutput ) );
        }

            /**
             * Returns the HTML output of the given field.
             * @since  3.5.3
             * @return string The HTML output of the given field.
             */
            private function ___getEachFieldOutput( $aField, $isIndex, array $aCallbacks, $bIsLastElement=false ) {

                // Field type definition - allows mixed field types in sub-fields
                $_aFieldTypeDefinition = $this->___getFieldTypeDefinition( $aField[ 'type' ] );
                if ( ! is_callable( $_aFieldTypeDefinition[ 'hfRenderField' ] ) ) {
                    return '';
                }

                // Set some internal keys
                $_oSubFieldFormatter = new AdminPageFramework_Form_Model___Format_EachField(
                    $aField,
                    $isIndex,
                    $aCallbacks,
                    $_aFieldTypeDefinition
                );
                $aField = $_oSubFieldFormatter->get();

                // Callback the registered function to output the field
                return $this->___getFieldOutput(
                    call_user_func_array(
                        $_aFieldTypeDefinition[ 'hfRenderField' ],
                        array( $aField )
                    ),
                    $aField,
                    $bIsLastElement
                );

            }

    /**
     * Embeds an internal hidden input for the 'save' argument.
     * @since  3.6.0
     * @return string
     */
    private function ___getUnsetFlagFieldInputTag( $aField ) {
        if ( false !== $aField[ 'save' ] ) {
            return '';
        }
        return $this->getHTMLTag(
            'input',
            array(
                'type'  => 'hidden',
                'name'  => '__unset_' . $aField[ '_fields_type' ] . '[' . $aField[ '_input_name_flat' ] . ']',
                'value' => $aField[ '_input_name_flat' ],
                'class' => 'unset-element-names element-address',
            )
        );
    }
                /**
                 * Retrieves a field output.
                 *
                 * @since  3.8.0
                 * @return string
                 */
                private function ___getFieldOutput( $sContent, $aField, $bIsLastElement ) {
                    $_oFieldAttribute = new AdminPageFramework_Form_View___Attribute_Field( $aField );
                    return $aField[ 'before_field' ]
                        . "<div " . $_oFieldAttribute->get() . ">"
                            . $sContent
                            . $this->___getUnsetFlagFieldInputTag( $aField )
                            . $this->___getDelimiter( $aField, $bIsLastElement )
                        . "</div>"
                        . $aField[ 'after_field' ];
                }
                /**
                 * Returns the registered field type definition array of the given field type slug.
                 *
                 * @remark The `$this->aFieldTypeDefinitions` property stores default key-values of all the registered field types.
                 * @since  3.5.3
                 * @return array  The field type definition array.
                 */
                private function ___getFieldTypeDefinition( $sFieldTypeSlug ) {
                    return $this->getElement(
                        $this->aFieldTypeDefinitions,
                        $sFieldTypeSlug,
                        $this->aFieldTypeDefinitions[ 'default' ]
                    );
                }

                /**
                 * Returns the HTML output of delimiter
                 * @since  3.5.3
                 * @return string The HTML output of delimiter.
                 */
                private function ___getDelimiter( $aField, $bIsLastElement ) {
                    return $aField[ 'delimiter' ]
                        ? "<div " . $this->getAttributes(
                                array(
                                    'class' => 'delimiter',
                                    'id'    => "delimiter-{$aField[ 'input_id' ]}",
                                    'style' => $this->getAOrB(
                                        $bIsLastElement,
                                        "display:none;",
                                        ""
                                    ),
                                )
                            ) . ">"
                                . $aField[ 'delimiter' ]
                            . "</div>"
                        : '';
                }

        /**
         * Returns the final fields output.
         *
         * @since  3.1.0
         * @return string
         */
        private function ___getFinalOutput( $aFieldset, array $aFieldsOutput, $iFieldsCount ) {
            $_oFieldsetAttributes = new AdminPageFramework_Form_View___Attribute_Fieldset( $aFieldset );
            return $aFieldset[ 'before_fieldset' ]
                . "<fieldset " . $_oFieldsetAttributes->get() . ">"
                    . $this->___getEmbeddedFieldTitle( $aFieldset )
                    . $this->___getChildFieldTitle( $aFieldset )
                    . $this->___getFieldsetContent( $aFieldset, $aFieldsOutput, $iFieldsCount )
                    . $this->___getExtras( $aFieldset, $iFieldsCount )
                . "</fieldset>"
                . $aFieldset[ 'after_fieldset' ];
        }
            /**
             * @remark For `section_title` fields and fields with the `placement` argument of the value of `section_title` or `field_title`.
             * @return string
             * @since  3.8.0
             */
            private function ___getEmbeddedFieldTitle( $aFieldset ) {
                if ( ! $aFieldset[ '_is_title_embedded' ] ) {
                    return '';
                }
                $_oFieldTitle = new AdminPageFramework_Form_View___FieldTitle(
                    $aFieldset,
                    '',
                    $this->aOptions,
                    $this->aErrors,
                    $this->aFieldTypeDefinitions,
                    $this->aCallbacks,
                    $this->oMsg
                );
                return $_oFieldTitle->get();
            }

            /**
             * @remark Used by inline-mixed fields and nested fields.
             * @return string
             * @since  3.8.0
             */
            private function ___getChildFieldTitle( $aFieldset ) {
                if ( ! $aFieldset[ '_nested_depth' ] ) {
                    return '';
                }
                if ( $aFieldset[ '_is_title_embedded' ] ) {
                    return '';
                }
                $_oFieldTitle = new AdminPageFramework_Form_View___FieldTitle(
                    $aFieldset,
                    array( 'admin-page-framework-child-field-title' ),
                    $this->aOptions,
                    $this->aErrors,
                    $this->aFieldTypeDefinitions,
                    $this->aCallbacks,
                    $this->oMsg
                );
                return $_oFieldTitle->get();
            }

            /**
             * @since  3.6.1
             * @return string
             */
            private function ___getFieldsetContent( $aFieldset, $aFieldsOutput, $iFieldsCount ) {
                if ( is_scalar( $aFieldset[ 'content' ] ) ) {
                    return $aFieldset[ 'content' ];
                }
                $_oFieldsAttributes     = new AdminPageFramework_Form_View___Attribute_Fields(
                    $aFieldset,
                    array(),    // attribute array
                    $iFieldsCount
                );
                return "<div " . $_oFieldsAttributes->get() . ">"
                        . $aFieldset[ 'before_fields' ]
                            . implode( PHP_EOL, $aFieldsOutput )
                        . $aFieldset[ 'after_fields' ]
                    . "</div>";
            }

            /**
             * Returns the output of the extra elements for the fields such as description and JavaScript.
             *
             * The additional but necessary elements are placed outside of the `fields` tag.
             * @return string
             */
            private function ___getExtras( $aField, $iFieldsCount ) {

                $_aOutput = array();

                // Descriptions
                $_oFieldDescription = new AdminPageFramework_Form_View___Description(
                    $aField[ 'description' ],
                    'admin-page-framework-fields-description'   // class selector
                );
                $_aOutput[] = $_oFieldDescription->get();

                // Dimensional keys of repeatable and sortable fields
                $_aOutput[] = $this->___getDynamicElementFlagFieldInputTag( $aField );

                $_aOutput[] = $this->_getRepeatableFieldButtons( 'fields-' . $aField[ 'tag_id' ], $iFieldsCount, $aField[ 'repeatable' ] );

                return implode( PHP_EOL, array_filter( $_aOutput ) );

            }
                /**
                 * Embeds an internal hidden input for the 'sortable' and 'repeatable' arguments.
                 * @since  3.6.0
                 * @return string
                 */
                private function ___getDynamicElementFlagFieldInputTag( $aFieldset ) {
                    if ( ! empty( $aFieldset[ 'repeatable' ] ) ) {
                        return $this->___getRepeatableFieldFlagTag( $aFieldset );
                    }
                    if ( ! empty( $aFieldset[ 'sortable' ] ) ) {
                        return $this->___getSortableFieldFlagTag( $aFieldset );
                    }
                    return '';
                }
                    /**
                     * @since  3.6.2
                     * @return string
                     */
                    private function ___getRepeatableFieldFlagTag( $aFieldset ) {
                        return $this->getHTMLTag(
                            'input',
                            array(
                                'type'                      => 'hidden',
                                'name'                      => '__repeatable_elements_' . $aFieldset[ '_structure_type' ]
                                    . '[' . $aFieldset[ '_field_address' ] . ']',
                                'class'                     => 'element-address',
                                'value'                     => $aFieldset[ '_field_address' ],
                                'data-field_address_model'  => $aFieldset[ '_field_address_model' ],
                            )
                        );
                    }
                    /**
                     * @since  3.6.2
                     * @return string
                     */
                    private function ___getSortableFieldFlagTag( $aFieldset ) {
                        return $this->getHTMLTag(
                            'input',
                            array(
                                'type'                      => 'hidden',
                                'name'                      => '__sortable_elements_' . $aFieldset[ '_structure_type' ]
                                    . '[' . $aFieldset[ '_field_address' ] . ']',
                                'class'                     => 'element-address',
                                'value'                     => $aFieldset[ '_field_address' ],
                                'data-field_address_model'  => $aFieldset[ '_field_address_model' ],
                            )
                        );
                    }

}