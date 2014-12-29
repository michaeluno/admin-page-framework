<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
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
     * Returns the input tag name for the name attribute.
     * 
     * @since   2.0.0
     * @since   3.0.0       Dropped the page slug dimension. Deprecated the 'name' field key to override the name attribute since the new 'attribute' key supports the functionality.
     * @since   3.2.0       Added the $hfFilterCallback parameter.
     */
    private function _getInputName( $aField=null, $sKey='', $hfFilterCallback=null ) {
        
        $sKey           = ( string ) $sKey; // casting string is required as 0 value may have been interpreted as false.
        $aField         = isset( $aField ) ? $aField : $this->aField;
        $_sKey          = '0' !== $sKey && empty( $sKey ) ? '' : "[{$sKey}]";
        $_sSectionIndex = isset( $aField['section_id'], $aField['_section_index'] ) ? "[{$aField['_section_index']}]" : "";
        $_sResult       = '';
        $_sResultTail   = '';
        switch( $aField['_fields_type'] ) {
            default:
            case 'page':
                $sSectionDimension = isset( $aField['section_id'] ) && $aField['section_id'] && '_default' != $aField['section_id']
                    ? "[{$aField['section_id']}]"
                    : '';
                $_sResult = "{$aField['option_key']}{$sSectionDimension}{$_sSectionIndex}[{$aField['field_id']}]{$_sKey}";
                break;
                
            case 'page_meta_box':
            case 'post_meta_box':
                $_sResult = isset( $aField['section_id'] ) && $aField['section_id'] && '_default' != $aField['section_id']
                    ? "{$aField['section_id']}{$_sSectionIndex}[{$aField['field_id']}]{$_sKey}"
                    : "{$aField['field_id']}{$_sKey}";
                break;
                
            // taxonomy fields type does not support sections.
            case 'taxonomy': 
                $_sResult = "{$aField['field_id']}{$_sKey}";
                break;
                
            // [3.2.0+] 
            case 'widget':  
                $_sResult       = isset( $aField['section_id'] ) && $aField['section_id'] && '_default' != $aField['section_id']
                    ? "{$aField['section_id']}{$_sSectionIndex}[{$aField['field_id']}]"
                    : "{$aField['field_id']}";            
                $_sResultTail   = $_sKey;
                break;
                
        }
        return is_callable( $hfFilterCallback )
            ? call_user_func_array( $hfFilterCallback, array( $_sResult ) ) . $_sResultTail
            : $_sResult . $_sResultTail;
            
    }
        
    /**
     * Retrieves the field name attribute whose dimensional elements are delimited by the pile character.
     * 
     * Instead of [] enclosing array elements, it uses the pipe(|) to represent the multi dimensional array key.
     * This is used to create a reference to the submit field name to determine which button is pressed.
     * 
     * @remark  Used by the import and submit field types.
     * @since   2.0.0
     * @since   2.1.5       Made the parameter mandatory. Changed the scope to protected from private. Moved from AdminPageFramework_FormField.
     * @since   3.0.0       Moved from the submit field type class. Dropped the page slug dimension.
     * @since   3.2.0       Added the $hfFilterCallback parameter.
     */ 
    protected function _getFlatInputName( $aField, $sKey='', $hfFilterCallback=null ) {    
        
        $sKey           = ( string ) $sKey; // casting string is important as 0 value may have been interpreted as false.
        $_sKey          = '0' !== $sKey && empty( $sKey ) ? '' : "|{$sKey}";
        $_sSectionIndex = isset( $aField['section_id'], $aField['_section_index'] ) ? "|{$aField['_section_index']}" : "";
        $_sResult       = '';
        $_sResultTail   = '';
        switch( $aField['_fields_type'] ) {
            default:
            case 'page':
                $sSectionDimension = isset( $aField['section_id'] ) && $aField['section_id'] && '_default' != $aField['section_id']
                    ? "|{$aField['section_id']}"
                    : '';
                $_sResult = "{$aField['option_key']}{$sSectionDimension}{$_sSectionIndex}|{$aField['field_id']}{$_sKey}";
                break;
                
            case 'page_meta_box':
            case 'post_meta_box':
                $_sResult = isset( $aField['section_id'] ) && $aField['section_id'] && '_default' != $aField['section_id']
                    ? "{$aField['section_id']}{$_sSectionIndex}|{$aField['field_id']}{$_sKey}"
                    : "{$aField['field_id']}{$_sKey}";
                break;
                
            // taxonomy fields type does not support sections.
            case 'taxonomy': 
                $_sResult = "{$aField['field_id']}{$_sKey}";
                break;
            
            // 3.2.0+                
            case 'widget':  
                $_sResult       = isset( $aField['section_id'] ) && $aField['section_id'] && '_default' != $aField['section_id']
                    ? "{$aField['section_id']}{$_sSectionIndex}|{$aField['field_id']}"
                    : "{$aField['field_id']}";            
                $_sResultTail   = $_sKey;
                break;
                
        }    
        return is_callable( $hfFilterCallback )
            ? call_user_func_array( $hfFilterCallback, array( $_sResult ) ) . $_sResultTail
            : $_sResult . $_sResultTail;    
            
    }
        
    /**
     * Returns the input tag ID.
     * 
     * e.g. "{$aField['field_id']}__{$isIndex}";
     * 
     * @remark      The index keys are prefixed with double-underscores.
     * @since       2.0.0
     * @since       3.2.0       Added the $hfFilterCallback parameter.
     * @since       3.3.2       Made it static public because the `<for>` tag needs to refer to it and it is called from another class that renders the form table. Added a default value for the <var>$isIndex</var> parameter.
     */
    static public function _getInputID( $aField, $isIndex=0, $hfFilterCallback=null ) {
        
        $_sSectionIndex  = isset( $aField['_section_index'] ) ? '__' . $aField['_section_index'] : ''; // double underscore
        $_isFieldIndex   = '__' . $isIndex; // double underscore
        $_sResult        = isset( $aField['section_id'] ) && '_default' != $aField['section_id']
            ? $aField['section_id'] . $_sSectionIndex . '_' . $aField['field_id'] . $_isFieldIndex
            : $aField['field_id'] . $_isFieldIndex;
        return is_callable( $hfFilterCallback )
            ? call_user_func_array( $hfFilterCallback, array( $_sResult ) )
            : $_sResult;            
            
    }
    

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
        
        $_sSectionIndex = isset( $aField['_section_index'] ) ? '__' . $aField['_section_index'] : '';
        $_sResult       = isset( $aField['section_id'] ) && '_default' != $aField['section_id']
            ? $aField['section_id'] . $_sSectionIndex . '_' . $aField['field_id']
            : $aField['field_id'];
        return is_callable( $hfFilterCallback )
            ? call_user_func_array( $hfFilterCallback, array( $_sResult ) )
            : $_sResult;        
            
    }     
    
    /** 
     * Retrieves the input field HTML output.
     * @since 2.0.0
     * @since 2.1.6 Moved the repeater script outside the fieldset tag.
     */ 
    public function _getFieldOutput() {
        
        $aFieldsOutput = array(); 

        /* 1. Prepend the field error message. */
        $_sFieldError = $this->_getFieldError( $this->aErrors, $this->aField['section_id'], $this->aField['field_id'] );
        if ( $_sFieldError ) {
            $aFieldsOutput[] = $_sFieldError;
        }
                    
        /* 2. Set the tag ID used for the field container HTML tags. */
        $this->aField['tag_id'] = $this->_getInputTagBaseID( $this->aField, $this->aCallbacks['hfTagID'] );
            
        /* 3. Construct fields array for sub-fields */
        $aFields = $this->_constructFieldsArray( $this->aField, $this->aOptions );

        /* 4. Get the field and its sub-fields output. */
        $aFieldsOutput[] = $this->_getFieldsOutput( $aFields, $this->aCallbacks );
                    
        /* 5. Return the entire output */
        return $this->_getFinalOutput( $this->aField, $aFieldsOutput, count( $aFields ) );

    }
    
        /**
         * Returns the output of the given fieldset(main field and its sub-fields) array.
         * 
         * @since   3.1.0
         * @since   3.2.0   Added the $aCallbacks parameter.
         */ 
        private function _getFieldsOutput( array $aFields, array $aCallbacks=array() ) {

            $_aOutput   = array();
            foreach( $aFields as $__sKey => $__aField ) {

                /* Retrieve the field definition for this type - this process enables to have mixed field types in sub-fields 
                 * The $this->aFieldTypeDefinitions property stores default key-values of all the registered field types.
                 * */ 
                $_aFieldTypeDefinition = isset( $this->aFieldTypeDefinitions[ $__aField['type'] ] )
                    ? $this->aFieldTypeDefinitions[ $__aField['type'] ] 
                    : $this->aFieldTypeDefinitions['default'];
                    
                if ( ! is_callable( $_aFieldTypeDefinition['hfRenderField'] ) ) {
                    continue;
                }     

                /* Set some internal keys */ 
                $_bIsSubField                           = is_numeric( $__sKey ) && 0 < $__sKey;
                $__aField['_index']                     = $__sKey;
                $__aField['input_id']                   = $this->_getInputID( $__aField, $__sKey, $aCallbacks['hfID'] ); //  ({section id}_){field_id}_{index}
                $__aField['_input_name']                = $this->_getInputName( $__aField, $__aField['_is_multiple_fields'] ? $__sKey : '', $aCallbacks['hfName'] );    
                $__aField['_input_name_flat']           = $this->_getFlatInputName( $__aField, $__aField['_is_multiple_fields'] ? $__sKey : '', $aCallbacks['hfNameFlat'] ); // used for submit, export, import field types     
                $__aField['_field_container_id']        = "field-{$__aField['input_id']}"; // used in the attribute below plus it is also used in the sample custom field type.
// These models are for generating ids and names dynamically.
$__aField['_input_id_model']            = $this->_getInputID( $__aField, '-fi-',  $aCallbacks['hfID'] ); // 3.3.1+ referred by the repeatable field script
$__aField['_input_name_model']          = $this->_getInputName( $__aField, $__aField['_is_multiple_fields'] ? '-fi-': '', $aCallbacks['hfName'] );      // 3.3.1+ referred by the repeatable field script
$__aField['_fields_container_id_model'] = "field-{$__aField['_input_id_model']}"; // [3.3.1+] referred by the repeatable field script
                $__aField['_fields_container_id']       = "fields-{$this->aField['tag_id']}";
                $__aField['_fieldset_container_id']     = "fieldset-{$this->aField['tag_id']}";
                $__aField                               = $this->uniteArrays(
                    $__aField, // includes the user-set values.
                    array( // the automatically generated values.
                        'attributes' => array(
                            'id'                => $__aField['input_id'],
                            'name'              => $__aField['_input_name'],
                            'value'             => $__aField['value'],
                            'type'              => $__aField['type'], // text, password, etc.
                            'disabled'          => null,
                            'data-id_model'     => $__aField['_input_id_model'],    // 3.3.1+
                            'data-name_model'   => $__aField['_input_name_model'],  // 3.3.1+
                        )
                    ),
                    ( array ) $_aFieldTypeDefinition['aDefaultKeys'] // this allows sub-fields with different field types to set the default key-values for the sub-field.
                );
                
                $__aField['attributes']['class'] = 'widget' === $__aField['_fields_type'] && is_callable( $aCallbacks['hfClass'] )
                    ? call_user_func_array( $aCallbacks['hfClass'], array( $__aField['attributes']['class'] ) )
                    : $__aField['attributes']['class'];
                $__aField['attributes']['class'] = $this->generateClassAttribute(
                    $__aField['attributes']['class'],  
                    $this->dropElementsByType( $__aField['class'] )
                );

                /* Callback the registered function to output the field */     
                $_aFieldAttributes = array(
                    'id'            => $__aField['_field_container_id'],
                    'data-type'     => "{$__aField['type']}",   // this is referred by the repeatable field JavaScript script.
                    'data-id_model' => $__aField['_fields_container_id_model'], // 3.3.1+
                    'class'         => "admin-page-framework-field admin-page-framework-field-{$__aField['type']}" 
                        . ( $__aField['attributes']['disabled'] ? ' disabled' : null )
                        . ( $_bIsSubField ? ' admin-page-framework-subfield' : null ),
                );
                
                $_aOutput[] = $__aField['before_field']
                    . "<div " . $this->_getFieldContainerAttributes( $__aField, $_aFieldAttributes, 'field' ) . ">"
                        . call_user_func_array(
                            $_aFieldTypeDefinition['hfRenderField'],
                            array( $__aField )
                        )
                        . ( ( $sDelimiter = $__aField['delimiter'] )
                            ? "<div " . $this->generateAttributes( array(
                                    'class' => 'delimiter',
                                    'id'    => "delimiter-{$__aField['input_id']}",
                                    'style' => $this->isLastElement( $aFields, $__sKey ) ? "display:none;" : "",
                                ) ) . ">{$sDelimiter}</div>"
                            : ""
                        )
                    . "</div>"
                    . $__aField['after_field'];

            }     
            
            return implode( PHP_EOL, $_aOutput );
            
        }
    
        /**
         * Returns the final fields output.
         * 
         * @since 3.1.0
         */
        private function _getFinalOutput( array $aField, array $aFieldsOutput, $iFieldsCount ) {
                            
            // Construct attribute arrays.
            
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
                    . ( $aField['repeatable'] ? ' repeatable' : '' )
                    . ( $aField['sortable'] ? ' sortable' : '' ),
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
             * Returns the output of the extra elements for the fields such as description and JavaScri
             * 
             * The additional but necessary elements are placed outside of the fields tag. 
             */
            private function _getExtras( $aField, $iFieldsCount ) {
                
                $_aOutput = array();
                
                // Add the description
                if ( isset( $aField['description'] ) )  {
                    $_aOutput[] = $this->_getDescription( $aField['description'] );
                }
                    
                // Add the repeater & sortable scripts 
                $_aOutput[] = $this->_getFieldScripts( $aField, $iFieldsCount );
                
                return implode( PHP_EOL, $_aOutput );
                
            }
                /**
                 * Returns the HTML formatted description blocks by the given description definition.
                 * 
                 * @since   3.3.0
                 * @return  string      The description output.
                 */
                private function _getDescription( $asDescription ) {
                    
                    if ( empty( $asDescription ) ) { return ''; }
                    
                    $_aOutput = array();
                    foreach( $this->getAsArray( $asDescription ) as $_sDescription ) {
                        $_aOutput[] = "<p class='admin-page-framework-fields-description'>"
                                . "<span class='description'>{$_sDescription}</span>"
                            . "</p>";
                    }
                    return implode( PHP_EOL, $_aOutput );
                    
                }
                /**
                 * Returns the output of JavaScript scripts for the field (and its sub-fields).
                 * 
                 * @since 3.1.0
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
         * @since 3.1.0
         */
        private function _getFieldError( $aErrors, $sSectionID, $sFieldID ) {
            
            // If this field has a section and the error element is set
            if ( 
                isset( 
                    $aErrors[ $sSectionID ], 
                    $aErrors[ $sSectionID ][ $sFieldID ]
                )
                && is_array( $aErrors[ $sSectionID ] )
                && ! is_array( $aErrors[ $sSectionID ][ $sFieldID ] )
                
            ) {     
                return "<span class='field-error'>*&nbsp;{$this->aField['error_message']}" 
                        . $aErrors[ $sSectionID ][ $sFieldID ]
                    . "</span>";
            } 
            
            // if this field does not have a section and the error element is set,
            if ( isset( $aErrors[ $sFieldID ] ) && ! is_array( $aErrors[ $sFieldID ] ) ) {
                return "<span class='field-error'>*&nbsp;{$this->aField['error_message']}" 
                        . $aErrors[ $sFieldID ]
                    . "</span>";
            }     
            
        }    
    
        /**
         * Returns the array of fields 
         * 
         * @since 3.0.0
         */
        protected function _constructFieldsArray( &$aField, &$aOptions ) {

            /* Get the set value(s) */
            $vSavedValue    = $this->_getStoredInputFieldValue( $aField, $aOptions );
            
            /* Separate the first field and sub-fields */
            $aFirstField    = array();
            $aSubFields     = array();
            foreach( $aField as $nsIndex => $vFieldElement ) {
                if ( is_numeric( $nsIndex ) ) {
                    $aSubFields[] = $vFieldElement;
                } else {
                    $aFirstField[ $nsIndex ] = $vFieldElement;
                }
            }     
            
            /* Create the sub-fields of repeatable fields based on the saved values */
            if ( $aField['repeatable'] ) {
                foreach( ( array ) $vSavedValue as $iIndex => $vValue ) {
                    if ( 0 == $iIndex ) { continue; }
                    $aSubFields[ $iIndex - 1 ] = isset( $aSubFields[ $iIndex - 1 ] ) && is_array( $aSubFields[ $iIndex - 1 ] ) 
                        ? $aSubFields[ $iIndex - 1 ] 
                        : array();     
                }
            }
            
            /* Put the initial field and the sub-fields together in one array */
            foreach( $aSubFields as &$aSubField ) {
                
                /* Before merging recursively, evacuate the label element which should not be merged */
                $aLabel = isset( $aSubField['label'] ) 
                    ? $aSubField['label']
                    : ( isset( $aFirstField['label'] )
                         ? $aFirstField['label'] 
                         : null
                    );
                
                /* Do recursive array merging */
                $aSubField = $this->uniteArrays( $aSubField, $aFirstField ); // the 'attributes' array of some field types have more than one dimensions. // $aSubField = $aSubField + $aFirstField;
                
                /* Restore the label element */
                $aSubField['label'] = $aLabel;
                
            }
            $aFields = array_merge( array( $aFirstField ), $aSubFields );
                    
            /* Set the saved values */     
            if ( count( $aSubFields ) > 0 || $aField['repeatable'] || $aField['sortable'] ) { // means the elements are saved in an array.
                foreach( $aFields as $iIndex => &$aThisField ) {
                    $aThisField['_saved_value'] = isset( $vSavedValue[ $iIndex ] ) ? $vSavedValue[ $iIndex ] : null;
                    $aThisField['_is_multiple_fields'] = true;
                }
            } else {
                $aFields[ 0 ]['_saved_value'] = $vSavedValue;
                $aFields[ 0 ]['_is_multiple_fields'] = false;
            } 

            /* Determine the value */
            unset( $aThisField ); // PHP requires this for a previously used variable as reference.
            foreach( $aFields as &$aThisField ) {
                $aThisField['_is_value_set_by_user'] = isset( $aThisField['value'] );
                $aThisField['value'] = isset( $aThisField['value'] ) 
                    ? $aThisField['value'] 
                    : ( isset( $aThisField['_saved_value'] ) 
                        ? $aThisField['_saved_value']
                        : ( isset( $aThisField['default'] )
                            ? $aThisField['default']
                            : null
                        )
                    );     
            }

            return $aFields;
            
        }
        
            /**
             * Returns the stored field value.
             * 
             * It checks if a previously saved option value exists or not. Regular setting pages and page meta boxes will be applied here.
             * It's important to return null if not set as the returned value will be checked later on whether it is set or not. If an empty value is returned, they will think it's set.
             * 
             * @since       2.0.0
             * @since       3.0.0       Removed the check of the 'value' and 'default' keys. Made it use the '_fields_type' internal key.
             * @since       3.1.0       Changed the name to _getStoredInputFieldValue from _getInputFieldValue
             * @since       3.4.1       Removed the switch block as it was redundant.
             */
            private function _getStoredInputFieldValue( $aField, $aOptions ) {    

                // If a section is not set, check the first dimension element.
                if ( ! isset( $aField['section_id'] ) || '_default' == $aField['section_id'] ) {
                    return isset( $aOptions[ $aField['field_id'] ] )
                        ? $aOptions[ $aField['field_id'] ]
                        : null;     
                }
                    
                // At this point, the section dimension is set.
                
                // If it belongs to a sub section,
                if ( isset( $aField['_section_index'] ) ) {
                    return isset( $aOptions[ $aField['section_id'] ][ $aField['_section_index'] ][ $aField['field_id'] ] )
                        ? $aOptions[ $aField['section_id'] ][ $aField['_section_index'] ][ $aField['field_id'] ]
                        : null;     
                }
                
                // Otherwise, return the second dimension element.
                return isset( $aOptions[ $aField['section_id'] ][ $aField['field_id'] ] )
                    ? $aOptions[ $aField['section_id'] ][ $aField['field_id'] ]
                    : null;
                                                
            }     
}