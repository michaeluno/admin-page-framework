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
 * @extends     AdminPageFramework_FormField_Base
 * @package     AdminPageFramework
 * @subpackage  Form
 * @internal
 */
class AdminPageFramework_FormField extends AdminPageFramework_FormField_Base {
      
    /**
     * Returns the field input base ID used for field container elements.
     * 
     * The returning value does not represent the exact ID of the field input tag. 
     * This is because each input tag has an index for sub-fields.
     * 
     * @remark  This is called from the fields table class to insert the row id.
     * @since   2.0.0
     * @since   3.2.0       Added the $hfFilterCallback parameter.
     * @since   3.3.2       Changed the name from `_getInputTagID()`.
     */
    static public function _getInputTagBaseID( $aField, $hfFilterCallback=null )  {

        $_sSectionIndex = isset( $aField['_section_index'] )
            ? '__' . $aField['_section_index'] 
            : '';
        $_sInputTagID   = isset( $aField['section_id'] ) && '_default' !== $aField['section_id']
            ? $aField['section_id'] . $_sSectionIndex . '_' . $aField['field_id']
            : $aField['field_id'];
        return ! is_callable( $hfFilterCallback )
            ? $_sInputTagID
            : call_user_func_array( 
                $hfFilterCallback, 
                array( 
                    $_sInputTagID 
                )
            );
            
    }     
    
    /** 
     * Retrieves the input field HTML output.
     * 
     * @since       2.0.0
     * @since       2.1.6       Moved the repeater script outside the fieldset tag.
     * @return      string
     */ 
    public function _getFieldOutput() {
        
        $_aFieldsOutput = array(); 

        // 1. Prepend the field error message. 
        $_sFieldError = $this->_getFieldError( 
            $this->aErrors, 
            $this->aField[ 'section_id' ], 
            $this->aField[ 'field_id' ] 
        );
        if ( '' !== $_sFieldError ) {
            $_aFieldsOutput[] = $_sFieldError;
        }
                    
        // 2. Set the tag ID used for the field container HTML tags. 
        $this->aField[ 'tag_id' ] = $this->_getInputTagBaseID( 
            $this->aField, 
            $this->aCallbacks[ 'hfTagID' ]
        );
            
        // 3. Construct fields array for sub-fields.
        $_oFieldsFormatter = new AdminPageFramework_Format_Fields(
            $this->aField, 
            $this->aOptions
        );
        $_aFields = $_oFieldsFormatter->get();
        
        // 4. Get the field and its sub-fields output.
        $_aFieldsOutput[] = $this->_getFieldsOutput( 
            $_aFields, 
            $this->aCallbacks 
        );

        // 5. Return the entire output.
        return $this->_getFinalOutput( 
            $this->aField, 
            $_aFieldsOutput, 
            count( $_aFields )
        );

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
                $_aFieldAttributes = $this->_getFieldAttributes( $aField );
                            
                return $aField[ 'before_field' ]
                    . "<div " . $this->_getFieldContainerAttributes( $aField, $_aFieldAttributes, 'field' ) . ">"
                        . call_user_func_array(
                            $_aFieldTypeDefinition['hfRenderField'],
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
                    return $this->generateHTMLTag( 
                        'input',
                        array(
                            'type'  => 'hidden',
                            'name'  => "__unset[{$aField[ 'input_id' ]}]",
                            'value' => $aField[ '_input_name_flat' ],
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
                 * Returns the field container attribute array.
                 * 
                 * @remark      Formatting each sub-field should be performed prior to callign this method.
                 * @param       array       $aField     The (sub-)field definition array. This should have been formatted already.
                 * @return      array       The generated field container attribute array.
                 * @internal   
                 * @since       3.5.3
                 */
                private function _getFieldAttributes( array $aField ) {            
                    return array(
                        'id'            => $aField['_field_container_id'],
                        'data-type'     => "{$aField['type']}",   // this is referred by the repeatable field JavaScript script.
                        'data-id_model' => $aField['_fields_container_id_model'], // 3.3.1+
                        'class'         => "admin-page-framework-field admin-page-framework-field-{$aField['type']}"
                            . $this->getAOrB(
                                $aField['attributes']['disabled'],
                                ' disabled',
                                ''
                            )
                            . $this->getAOrB(
                                $aField['_is_sub_field'],
                                ' admin-page-framework-subfield',
                                ''
                            ) 
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
                        ? "<div " . $this->generateAttributes( 
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
        private function _getFinalOutput( array $aField, array $aFieldsOutput, $iFieldsCount ) {
                            
            //// Construct attribute arrays.
            
            // the 'fieldset' container attributes
            $_aFieldsSetAttributes = array(
                'id'            => 'fieldset-' . $aField['tag_id'],
                'class'         => 'admin-page-framework-fieldset',
                'data-field_id' => $aField['tag_id'], // <-- don't remember what this was for...
            );
            
            // the 'fields' container attributes
            $_aFieldsContainerAttributes = array(
                'id'            => 'fields-' . $aField['tag_id'],
                'class'         => 'admin-page-framework-fields'
                    . $this->getAOrB( $aField['repeatable'], ' repeatable', '' )
                    . $this->getAOrB( $aField['sortable'], ' sortable', '' ),
                'data-type'     => $aField['type'], // this is referred by the sortable field JavaScript script.
            );           
            
            return $aField['before_fieldset']
                . "<fieldset " . $this->_getFieldContainerAttributes( $aField, $_aFieldsSetAttributes, 'fieldset' ) . ">"
                    . "<div " . $this->_getFieldContainerAttributes( $aField, $_aFieldsContainerAttributes, 'fields' ) . ">"
                        . $aField['before_fields']
                            . implode( PHP_EOL, $aFieldsOutput )
                        . $aField['after_fields']
                    . "</div>"
                    . $this->_getExtras( $aField, $iFieldsCount )
                . "</fieldset>"
                . $aField['after_fieldset'];
                        
        }
            
            /**
             * Returns the output of the extra elements for the fields such as description and JavaScript.
             * 
             * The additional but necessary elements are placed outside of the fields tag. 
             */
            private function _getExtras( $aField, $iFieldsCount ) {
                
                $_aOutput = array();
                
                // Add the description
                if ( isset( $aField['description'] ) )  {
                    $_aOutput[] = $this->_getDescriptions( 
                        $aField['description'],
                        'admin-page-framework-fields-description'
                    );
                }
                    
                // Insert dimensional keys of repeatable and sortable fields.
                $_aOutput[] = $this->_getDynamicElementFlagFieldInputTag( $aField );
                    
                // Add the repeatable and sortable scripts 
                $_aOutput[] = $this->_getFieldScripts( $aField, $iFieldsCount );
                
                return implode( PHP_EOL, $_aOutput );
                
            }
                /**
                 * Embeds an internal hidden input for the 'sortable' and 'repeatable' arguments.
                 * @since       3.6.0
                 * @return      string
                 */
                private function _getDynamicElementFlagFieldInputTag( array $aField ) {

                    if ( ! $aField[ 'sortable' ] && ! $aField[ 'repeatable' ] ) {
                        return '';
                    }

                    return $this->generateHTMLTag( 
                        'input',
                        array(
                            'type'  => 'hidden',
                            'name'  => "__dynamic_elements[" . $aField[ '_field_name_flat' ] . "]",
                            'value' => $aField[ '_field_name_flat' ],
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
                    
                    $_aOutput = array();
                    
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