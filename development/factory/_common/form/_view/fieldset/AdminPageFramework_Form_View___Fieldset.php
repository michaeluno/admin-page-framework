<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods for rendering form input field-sets.
 *
 * @since       2.0.0
 * @since       2.0.1       Added the <em>size</em> type.
 * @since       2.1.5       Separated the methods that defines field types to different classes.
 * @since       3.6.0       Changed the name from `AdminPageFramework_FormField`.
 * @extends     AdminPageFramework_FormField_Base
 * @package     AdminPageFramework
 * @subpackage  Common/Form/View/Field
 * @internal
 */
class AdminPageFramework_Form_View___Fieldset extends AdminPageFramework_Form_View___Fieldset_Base {
          
    /**
     * Returns the field-set HTML output.
     * 
     * @since       3.6.0
     * @return      string
     */
    public function get() {

        $_aOutputs      = array(); 

        // 1. Prepend the field error message. 
        $_oFieldError   = new AdminPageFramework_Form_View___Fieldset___FieldError(
            $this->aErrors, 
            $this->aFieldset[ '_section_path_array' ], 
            $this->aFieldset[ '_field_path_array' ],
            $this->aFieldset[ 'error_message' ]
        );
        $_aOutputs[]     = $_oFieldError->get();

        // 2. Construct fields array for sub-fields.
        $_oFieldsFormatter = new AdminPageFramework_Form_Model___Format_Fields(
            $this->aFieldset,
            $this->aOptions
        );
        $_aFields = $_oFieldsFormatter->get();
            
        // 3. Get the field and its sub-fields output.
        $_aOutputs[] = $this->_getFieldsOutput( 
            $this->aFieldset,
            $_aFields, 
            $this->aCallbacks 
        );

        // 4. Return the entire output.
        return $this->_getFinalOutput( 
            $this->aFieldset, 
            $_aOutputs, 
            count( $_aFields )
        );
     
    }
    
        /**
         * Returns the output of the given fieldset (main field and its sub-fields) array.
         * 
         * @since   3.1.0
         * @since   3.2.0   Added the `$aCallbacks` parameter.
         * @since   3.8.0   Added the `$aFieldset` parameter
         * @return  string
         */ 
        private function _getFieldsOutput( array $aFieldset, array $aFields, array $aCallbacks=array() ) {

            // 3.8.0+ Check the `content` argument and if it is an array holding field definitions, the user wants them to be nested.
            // @deprecated  3.8.0
            // if ( $this->hasFieldDefinitionsInContent( $aFieldset ) ) {   // @deprecated
/*             if ( $this->hasNestedFields( $aFieldset ) ) {
                return $this->_getChildFieldsets( 
                    $aFieldset, // parent field-set holding the nested field-sets.
                    $aFields
                );
            }  */
            // At this point, the field does not have nested/inline-mixed field items.
            
            $_aOutput = array();
            foreach( $aFields as $_isIndex => $_aField ) {
                $_aOutput[] = $this->_getEachFieldOutput( 
                    $_aField, 
                    $_isIndex, 
                    $aCallbacks,
                    $this->isLastElement( $aFields, $_isIndex )
                );
            }     
            return implode( PHP_EOL, array_filter( $_aOutput ) );
            
        }
        
            /**
             * Returns nested field outputs.
             * 
             * Iterate the fields array which can contain multiple sub-fields for repeatable and sortable items.
             * 
             * @since       3.8.0
             * @param       array       $aParentFieldset        The parent field-set definition that holds the nested field-sets.
             * @param       array       $aFields                An array holding the main and sub fields for dynamic (repeatable/sortable) fields.
             * @return      string
             */
            private function _getChildFieldsets( array $aParentFieldset, array $aFields ) {

                // $_aMethodNames = array(
                    // 0 => '_getNestedFieldsetsBySubFieldIndex',
                    // 1 => '_getInlineMixedFieldsetsBySubFieldIndex',  // @deprecated 3.8.0
                // );
                // $_sMethodName      = $_aMethodNames[ ( integer ) ( 'inline_mixed' === $aParentFieldset[ 'type' ] ) ];
                
                $_sNestedFieldsets = '';
                foreach( $aFields as $_isIndex => $_aField ) {
                    
                    $_sNestedFieldsets .= $this->_getNestedFieldsetsBySubFieldIndex( 
                        $_isIndex, 
                        $_aField,
                        $aParentFieldset,
                        $this->isLastElement( $aFields, $_isIndex ),
                        $this->hasSubFields( $aFields, $_aField )   // has sub-fields
                    );                    
                }
                return $_sNestedFieldsets;
                                                            
            }
                
                /**
                 * Returns nested field outputs by sub-field index.
                 * 
                 * @since       3.8.0
                 * @return      string
                 */
                private function _getNestedFieldsetsBySubFieldIndex( $iIndex, array $aField, array $aParentFieldset, $bIsLastElement=false, $bHasSubFields=false ) {

                    // Treat the nested field-set as an individual field. The output of `<fieldset>` tag will be enclosed in the `<div class="admin-page-framework-field">` tag.
                    $aParentFieldset[ '_is_multiple_fields' ] = $bHasSubFields;
                    $aParentFieldset[ 'type' ] = '_nested'; // set an internal type which is not defined with a field type class.
                    $_oSubFieldFormatter = new AdminPageFramework_Form_Model___Format_EachField(
                        $aParentFieldset, 
                        $iIndex,  // zero-based sub-field index
                        $this->aCallbacks,
                        $this->_getFieldTypeDefinition( $aField[ 'type' ] )
                    );
                    $_aParentFieldset   = $_oSubFieldFormatter->get();
                    
                    $_sNestedFieldsetsOutput = '';
                    foreach( $_aParentFieldset[ 'content' ] as $_aNestedFieldset ) {      

                        // Now re-format it so that the field path will be re-generated with the sub-field index.
                        $_aNestedFieldset = $this->getFieldsetReformattedBySubFieldIndex( 
                            $_aNestedFieldset, 
                            ( integer ) $iIndex,
                            $bHasSubFields,
                            $_aParentFieldset
                        );
                 
                        // Generate the output.
                        $_oFieldset = new AdminPageFramework_Form_View___Fieldset(
                            $_aNestedFieldset,  
                            $this->aOptions,
                            array(),    // @todo Generate field errors. $this->aErrors, 
                            $this->aFieldTypeDefinitions, 
                            $this->oMsg,
                            $this->aCallbacks // field output element callables.
                        );
                        $_sNestedFieldsetsOutput .= $_oFieldset->get(); // field output
                        
                    }
                    return $this->_getFieldOutput( 
                        $_sNestedFieldsetsOutput, // content
                        $_aParentFieldset,        // field definition array
                        $bIsLastElement           // whether the sub-field is the last element 
                    );
                
                }   
               
            /**
             * Returns the HTML output of the given field.
             * @internal
             * @since       3.5.3
             * @return      string      the HTML output of the given field.
             */
            private function _getEachFieldOutput( array $aField, $isIndex, array $aCallbacks, $bIsLastElement=false ) {
                
                // Field type definition - allows mixed field types in sub-fields 
                $_aFieldTypeDefinition = $this->_getFieldTypeDefinition( $aField[ 'type' ] );
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
                return $this->_getFieldOutput( 
                    call_user_func_array(
                        $_aFieldTypeDefinition[ 'hfRenderField' ],
                        array( $aField )
                    ), 
                    $aField, 
                    $bIsLastElement 
                );

            }

                /** 
                 * Retrieves a field output.
                 * 
                 * @since       3.8.0
                 * @return      string
                 */ 
                private function _getFieldOutput( $sContent, array $aField, $bIsLastElement ) {
                    $_oFieldAttribute = new AdminPageFramework_Form_View___Attribute_Field( $aField );
                    return $aField[ 'before_field' ]
                        . "<div " . $_oFieldAttribute->get() . ">"
                            . $sContent
                            . $this->_getUnsetFlagFieldInputTag( $aField )
                            . $this->_getDelimiter( $aField, $bIsLastElement )
                        . "</div>"
                        . $aField[ 'after_field' ];
                }            
            
                /**
                 * Embeds an internal hidden input for the 'save' argument.
                 * @since       3.6.0
                 * @return      string
                 */
                private function _getUnsetFlagFieldInputTag( array $aField ) {
                    
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
                 * Returns the registered field type definition array of the given field type slug.
                 * 
                 * @remark      The $this->aFieldTypeDefinitions property stores default key-values of all the registered field types.
                 * @internal
                 * @since       3.5.3
                 * @return      array   The field type definition array.
                 */
                private function _getFieldTypeDefinition( $sFieldTypeSlug ) {
                    return $this->getElement(
                        $this->aFieldTypeDefinitions,
                        $sFieldTypeSlug,
                        $this->aFieldTypeDefinitions[ 'default' ]
                    );
                }  

                /**
                 * Returns the HTML output of delimiter
                 * @internal
                 * @since       3.5.3
                 * @return      string      the HTML output of delimiter
                 */
                private function _getDelimiter( array $aField, $bIsLastElement ) {
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
         * @since       3.1.0
         * @return      string
         */
        private function _getFinalOutput( array $aFieldset, array $aFieldsOutput, $iFieldsCount ) {
                            
            $_oFieldsetAttributes   = new AdminPageFramework_Form_View___Attribute_Fieldset( $aFieldset );
            return $aFieldset[ 'before_fieldset' ]
                . "<fieldset " . $_oFieldsetAttributes->get() . ">"
                    . $this->_getChildFieldTitle( $aFieldset )                
                    . $this->_getFieldsetContent( $aFieldset, $aFieldsOutput, $iFieldsCount )
                    . $this->_getExtras( $aFieldset, $iFieldsCount )
                . "</fieldset>"
                . $aFieldset[ 'after_fieldset' ];
                        
        }
            /**
             * 
             * @remark      Used by inline-mixed fields and nested fields.
             * @return      string
             * @since       3.8.0
             */
            private function _getChildFieldTitle( array $aFieldset ) {

                if ( ! $aFieldset[ '_nested_depth' ] ) {
                    return '';
                }
            
                if ( ! $aFieldset[ 'show_title_column' ] ) {
                    return '';
                }
                
                if ( ! $aFieldset[ 'title' ] ) {
                    return '';
                }
// @todo set the for attribute value.                
                return "<label class='admin-page-framework-child-field-title' for=''>"
                        . $aFieldset[ 'title' ]
                    . "</label>";
                
            }
        
            /**
             * @since       3.6.1
             * @return      string
             */
            private function _getFieldsetContent( $aFieldset, $aFieldsOutput, $iFieldsCount ) {
                              
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
             * @return      string
             */
            private function _getExtras( $aField, $iFieldsCount ) {
                
                $_aOutput = array();
                
                // Descriptions
                $_oFieldDescription = new AdminPageFramework_Form_View___Description(
                    $aField[ 'description' ],
                    'admin-page-framework-fields-description'   // class selector
                );
                $_aOutput[] = $_oFieldDescription->get();
                    
                // Dimensional keys of repeatable and sortable fields
                $_aOutput[] = $this->_getDynamicElementFlagFieldInputTag( $aField );
                    
                // Repeatable and sortable scripts 
                $_aOutput[] = $this->_getFieldScripts( $aField, $iFieldsCount );
                
                return implode( PHP_EOL, array_filter( $_aOutput ) );
                
            }
                /**
                 * Embeds an internal hidden input for the 'sortable' and 'repeatable' arguments.
                 * @since       3.6.0
                 * @return      string
                 */
                private function _getDynamicElementFlagFieldInputTag( array $aFieldset ) {
                    
                    if ( $aFieldset[ 'repeatable' ] ) {
                        return $this->_getRepeatableFieldFlagTag( $aFieldset );
                    }
                    if ( $aFieldset[ 'sortable' ] ) {
                        return $this->_getSortableFieldFlagTag( $aFieldset );
                    }
                    return '';
                    
                }
                    /**
                     * @since       3.6.2
                     * @return      string
                     */
                    private function _getRepeatableFieldFlagTag( array $aFieldset ) {
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
                     * @since       3.6.2
                     * @return      string
                     */
                    private function _getSortableFieldFlagTag( array $aFieldset ) {
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
                    
                /**
                 * Returns the output of JavaScript scripts for the field (and its sub-fields).
                 * 
                 * @since       3.1.0
                 * @return      string
                 */
                private function _getFieldScripts( $aField, $iFieldsCount ) {
                    
                    $_aOutput   = array();
                    
                    // Add the repeater script 
                    $_aOutput[] = $aField[ 'repeatable' ]
                        ? $this->_getRepeaterFieldEnablerScript( 'fields-' . $aField['tag_id'], $iFieldsCount, $aField['repeatable'] )
                        : '';

                    // Add the sortable script - if the number of fields is only one, no need to sort the field. 
                    // Repeatable fields can make the number increase so here it checks the repeatability.
                    $_aOutput[] = $aField['sortable'] && ( $iFieldsCount > 1 || $aField['repeatable'] )
                        ? $this->_getSortableFieldEnablerScript( 'fields-' . $aField['tag_id'] )
                        : '';     
                    
                    return implode( PHP_EOL, $_aOutput );
                    
                }
            
}
