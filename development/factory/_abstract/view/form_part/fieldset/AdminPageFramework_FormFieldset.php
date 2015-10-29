<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods for rendering form input fields.
 *
 * @since       2.0.0
 * @since       2.0.1       Added the <em>size</em> type.
 * @since       2.1.5       Separated the methods that defines field types to different classes.
 * @since       3.6.0       Changed the name from `AdminPageFramework_FormField`.
 * @extends     AdminPageFramework_FormField_Base
 * @package     AdminPageFramework
 * @subpackage  Form
 * @internal
 */
class AdminPageFramework_FormFieldset extends AdminPageFramework_FormFieldset_Base {
          
    /**
     * Returns the fieldset HTML output.
     * 
     * @since       3.6.0
     * @return      string
     */
    public function get() {
     
        $_aOutput = array(); 

        // 1. Prepend the field error message. 
        $_sFieldError   = $this->_getFieldError( 
            $this->aErrors, 
            $this->aField[ 'section_id' ], 
            $this->aField[ 'field_id' ] 
        );
        if ( '' !== $_sFieldError ) {
            $_aOutput[] = $_sFieldError;
        }
                        
        // 2. Construct fields array for sub-fields.
        $_oFieldsFormatter = new AdminPageFramework_Format_Fields(
            $this->aField, 
            $this->aOptions
        );
        $_aFields = $_oFieldsFormatter->get();
        
        // 3. Get the field and its sub-fields output.
        $_aOutput[] = $this->_getFieldsOutput( 
            $_aFields, 
            $this->aCallbacks 
        );

        // 4. Return the entire output.
        return $this->_getFinalOutput( 
            $this->aField, 
            $_aOutput, 
            count( $_aFields )
        );
     
    }
        /** 
         * Retrieves the input field HTML output.
         * 
         * @since       2.0.0
         * @since       2.1.6       Moved the repeater script outside the fieldset tag.
         * @return      string
         * @deprecated  3.6.0       Use the `get()` method.
         */ 
        public function _getFieldOutput() {
            return $this->get();
        }
    
        /**
         * Returns the output of the given fieldset (main field and its sub-fields) array.
         * 
         * @since   3.1.0
         * @since   3.2.0   Added the $aCallbacks parameter.
         */ 
        private function _getFieldsOutput( array $aFields, array $aCallbacks=array() ) {

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
             * Returns the HTML output of the given field.
             * @internal
             * @since       3.5.3
             * @return      string      the HTML output of the given field.
             */
            private function _getEachFieldOutput( array $aField, $isIndex, array $aCallbacks, $bIsLastElement=false ) {
                
                // Field type definition - allows mixed field types in sub-fields 
                $_aFieldTypeDefinition = $this->_getFieldTypeDefinition( $aField['type'] );
                if ( ! is_callable( $_aFieldTypeDefinition['hfRenderField'] ) ) {
                    return '';
                }     

                // Set some internal keys                 
                $_oSubFieldFormatter = new AdminPageFramework_Format_EachField(
                    $aField, 
                    $isIndex, 
                    $aCallbacks, 
                    $_aFieldTypeDefinition
                );
                $aField = $_oSubFieldFormatter->get();
                
                // Callback the registered function to output the field 
                $_oFieldAttribute = new AdminPageFramework_Attribute_Field( $aField );
                return $aField[ 'before_field' ]
                    . "<div " . $_oFieldAttribute->get() . ">"
                        . call_user_func_array(
                            $_aFieldTypeDefinition[ 'hfRenderField' ],
                            array( $aField )
                        )
                        . $this->_getUnsetFlagFieldInputTag( $aField )
                        . $this->_getDelimiter( $aField, $bIsLastElement )
                    . "</div>"
                    . $aField[ 'after_field' ]
                    ;
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
                        $this->aFieldTypeDefinitions['default']
                    );
                }  

                /**
                 * Returns the HTML output of delimiter
                 * @internal
                 * @since       3.5.3
                 * @return      string      the HTML output of delimiter
                 */
                private function _getDelimiter( array $aField, $bIsLastElement ) {
                    return $aField['delimiter']
                        ? "<div " . $this->getAttributes( 
                                array(
                                    'class' => 'delimiter',
                                    'id'    => "delimiter-{$aField['input_id']}",
                                    'style' => $this->getAOrB(
                                        $bIsLastElement,
                                        "display:none;",
                                        ""
                                    ),
                                ) 
                            ) . ">"
                                . $aField['delimiter']
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
                            
            $_oFieldsetAttributes   = new AdminPageFramework_Attribute_Fieldset( $aFieldset );
            return $aFieldset[ 'before_fieldset' ]
                . "<fieldset " . $_oFieldsetAttributes->get() . ">"
                    . $this->_getFieldsetContent( $aFieldset, $aFieldsOutput, $iFieldsCount )
                    . $this->_getExtras( $aFieldset, $iFieldsCount )
                . "</fieldset>"
                . $aFieldset[ 'after_fieldset' ];
                        
        }
            /**
             * @since       3.6.1
             * @return      string
             */
            private function _getFieldsetContent( $aFieldset, $aFieldsOutput, $iFieldsCount ) {

                if ( is_scalar( $aFieldset[ 'content' ] ) ) {
                    return $aFieldset[ 'content' ];
                }
            
                $_oFieldsAttributes     = new AdminPageFramework_Attribute_Fields( 
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
             * The additional but necessary elements are placed outside of the fields tag. 
             */
            private function _getExtras( $aField, $iFieldsCount ) {
                
                $_aOutput = array();
                
                // Descriptions
                $_oFieldDescription = new AdminPageFramework_FormPart_Description(
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

                    // return $this->getHTMLTag( 
                        // 'input',
                        // array(
                            // 'type'                      => 'hidden',
                            // 'name'                      => '__dynamic_elements_' . $aFieldset[ '_fields_type' ] . '[' . $aFieldset[ '_field_address' ] . ']',
                            // 'class'                     => 'dynamic-element-names element-address',
                            // 'value'                     => $aFieldset[ '_field_address' ],
                            // 'data-field_address_model'  => $aFieldset[ '_field_address_model' ],
                        // )
                    // );
                    
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
                                'name'                      => '__repeatable_elements_' . $aFieldset[ '_fields_type' ] 
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
                                'name'                      => '__sortable_elements_' . $aFieldset[ '_fields_type' ] 
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
                    $_aOutput[] = $aField['repeatable']
                        ? $this->_getRepeaterFieldEnablerScript( 'fields-' . $aField['tag_id'], $iFieldsCount, $aField['repeatable'] )
                        : '';

                    // Add the sortable script - if the number of fields is only one, no need to sort the field. 
                    // Repeatable fields can make the number increase so here it checkes the repeatability.
                    $_aOutput[] = $aField['sortable'] && ( $iFieldsCount > 1 || $aField['repeatable'] )
                        ? $this->_getSortableFieldEnablerScript( 'fields-' . $aField['tag_id'] )
                        : '';     
                    
                    return implode( PHP_EOL, $_aOutput );
                    
                }
        
        /**
         * Returns the set field error message to the section or field.
         * 
         * @since       3.1.0
         * @return      string     The error string message. An empty value if not found.
         */
        private function _getFieldError( $aErrors, $sSectionID, $sFieldID ) {
            
            // If this field has a section and the error element is set
            if ( $this->_hasFieldErrorsOfSection( $aErrors, $sSectionID, $sFieldID ) ) {   
                return "<span class='field-error'>*&nbsp;{$this->aField['error_message']}"         
                        . $aErrors[ $sSectionID ][ $sFieldID ]
                    . "</span>";
            }             
            
            // if this field does not have a section and the error element is set,
            if ( $this->_hasFieldError( $aErrors, $sFieldID ) ) {
                return "<span class='field-error'>*&nbsp;{$this->aField['error_message']}"                           
                        . $aErrors[ $sFieldID ]
                    . "</span>";
            }  
            return '';
            
        }    
            /**
             * Checks whether the given field has a section and an error element is set or not.
             * 
             * @internal
             * @since       3.5.3
             * @return      boolean
             */
            private function _hasFieldErrorsOfSection( $aErrors, $sSectionID, $sFieldID ) {
                return ( 
                    isset( 
                        $aErrors[ $sSectionID ], 
                        $aErrors[ $sSectionID ][ $sFieldID ]
                    )
                    && is_array( $aErrors[ $sSectionID ] )
                    && ! is_array( $aErrors[ $sSectionID ][ $sFieldID ] )
                );
            }
            /**
             * Checks whether the given field has a field error.
             * @internal
             * @since       3.5.3
             * @return      boolean
             */
            private function _hasFieldError( $aErrors, $sFieldID ) {
                return ( 
                    isset( $aErrors[ $sFieldID ] ) 
                    && ! is_array( $aErrors[ $sFieldID ] )
                );
            }
            
}