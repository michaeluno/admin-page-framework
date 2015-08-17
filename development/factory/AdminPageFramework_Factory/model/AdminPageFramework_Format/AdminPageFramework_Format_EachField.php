<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format form sub-field definition arrays.
 * 
 * A sub-field is an each individual field and its definition contains some internal keys.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Format_EachField extends AdminPageFramework_Format_FormField_Base {
    
    /**
     * Represents the structure of the sub-field definition array.
     */
    static public $aStructure = array(
        '_is_sub_field'                 => false, // @todo change this key name as all the parsed field is technically a sub-field.
        '_index'                        => 0,
        '_is_multiple_fields'           => false,
        '_saved_value'                  => null,
        '_is_value_set_by_user'         => false,
        
        '_field_container_id'           => '',
        '_input_id_model'               => '',
        '_input_name_model'             => '',
        '_fields_container_id_model'    => '',
        '_fields_container_id'          => '',
        '_fieldset_container_id'        => '',
    );
    
    /**
     * 
     */
    public $aField                  = array();
    
    public $isIndex                 = 0;
    
    public $aCallbacks              = array();
    
    public $aFieldTypeDefinition    = array();
    
    /**
     * Sets up properties.
     */
    public function __construct( /* array $aField, $isIndex, array $aCallbacks, $aFieldTypeDefinition */ ) {

        $_aParameters = func_get_args() + array( 
            $this->aField, 
            $this->isIndex, 
            $this->aCallbacks, 
            $this->aFieldTypeDefinition,
        );
        $this->aField               = $_aParameters[ 0 ];
        $this->isIndex              = $_aParameters[ 1 ];
        $this->aCallbacks           = $_aParameters[ 2 ];
        $this->aFieldTypeDefinition = $_aParameters[ 3 ];
    
    }
    
    /**
     * Returns the formatted field definition array.
     * @internal
     * @since       3.5.3
     * @since       3.6.0       Moved from `AdminPageFramework_FormatField`.
     * @return      array       The formatted sub-field definition array.
     */
    public function get() {

        $_aField = $this->aField + self::$aStructure;
        
        $_bIsSubField                          = is_numeric( $this->isIndex ) && 0 < $this->isIndex;
        $_aField[ '_is_sub_field' ]            = $_bIsSubField;      // 3.5.3+
        $_aField[ '_index' ]                   = $this->isIndex;

        // 'input_id' - something like ({section id}_){field_id}_{index} e.g. my_section_id_my_field_id_0
        $_aField[ 'input_id' ]                 = $this->getInputID( 
            $_aField, 
            $this->isIndex, 
            $this->aCallbacks[ 'hfID' ]
        );

        $_aField[ '_input_name' ]              = $this->_getInputName(
            $_aField, 
            $this->getAOrB(
                $_aField[ '_is_multiple_fields' ],
                $this->isIndex,
                ''
            ),
            $this->aCallbacks[ 'hfName' ]
        );
        
        // '_input_name_flat' - used for submit, export, import field types
        $_aField[ '_input_name_flat' ]         = $this->_getFlatInputName(
            $_aField,
            $this->getAOrB(
                $_aField[ '_is_multiple_fields' ],
                $this->isIndex,
                ''
            ),
            $this->aCallbacks[ 'hfNameFlat' ]
        ); 
                            
        // used in the attribute below plus it is also used in the sample custom field type.
        $_aField[ '_field_container_id' ]      = "field-{$_aField[ 'input_id' ]}";

// @todo for issue #158 https://github.com/michaeluno/admin-page-framework/issues/158               
// These models are for generating ids and names dynamically.
// 3.3.1+ referred by the repeatable field script
$_aField[ '_input_id_model' ]            = $this->getInputID( 
    $_aField, 
    '-fi-',  
    $this->aCallbacks[ 'hfID' ] 
); 
// 3.3.1+ referred by the repeatable field script
$_aField[ '_input_name_model' ]          = $this->_getInputName( 
    $_aField, 
    $_aField[ '_is_multiple_fields' ] 
        ? '-fi-'
        : '',
    $this->aCallbacks[ 'hfName' ] 
);
        
// 3.3.1+ referred by the repeatable field script
$_aField['_fields_container_id_model'] = "field-{$_aField[ '_input_id_model' ]}"; 
            
        $_aField[ '_fields_container_id' ]     = "fields-{$this->aField[ 'tag_id' ]}";
        $_aField[ '_fieldset_container_id' ]   = "fieldset-{$this->aField[ 'tag_id' ]}";
        $_aField                               = $this->uniteArrays(
            $_aField, // includes the user-set values.
            array( // the automatically generated values.
                'attributes' => array(
                    'id'                => $_aField[ 'input_id' ],
                    'name'              => $_aField[ '_input_name' ],
                    'value'             => $_aField[ 'value' ],
                    'type'              => $_aField[ 'type' ], // text, password, etc.
                    'disabled'          => null,
                    'data-id_model'     => $_aField[ '_input_id_model' ],    // 3.3.1+
                    'data-name_model'   => $_aField[ '_input_name_model' ],  // 3.3.1+
                )
            ),
            // this allows sub-fields with different field types to set the default key-values for the sub-field.
            ( array ) $this->aFieldTypeDefinition[ 'aDefaultKeys' ]
        );
        
        $_aField[ 'attributes' ][ 'class' ] = 'widget' === $_aField[ '_fields_type' ] && is_callable( $this->aCallbacks[ 'hfClass' ] )
            ? call_user_func_array( $this->aCallbacks[ 'hfClass' ], array( $_aField[ 'attributes' ][ 'class' ] ) )
            : $_aField[ 'attributes' ][ 'class' ];
        $_aField[ 'attributes' ][ 'class' ] = $this->generateClassAttribute(
            $_aField[ 'attributes' ][ 'class' ],  
            $this->dropElementsByType( $_aField[ 'class' ] )
        );
        return $_aField;
        
    }
   
        /**
         * Returns the input tag name for the name attribute.
         * 
         * @since       2.0.0
         * @since       3.0.0       Dropped the page slug dimension. Deprecated the 'name' field key to override the name attribute since the new 'attribute' key supports the functionality.
         * @since       3.2.0       Added the $hfFilterCallback parameter.
         * @since       3.5.3       Added a type hint to the first parameter and dropped the default value to only accept an array.
         * @return      string
         */
        private function _getInputName( array $aField, $sKey='', $hfFilterCallback=null ) {

            $sKey           = ( string ) $sKey; // a 0 value may have been interpreted as false.
            $_sKey          = $this->getAOrB(
                '0' !== $sKey && empty( $sKey ),
                '',
                "[{$sKey}]"
            );
            $_sSectionIndex = isset( $aField['section_id'], $aField['_section_index'] ) 
                ? "[{$aField['_section_index']}]" 
                : "";
            $_sNameAttribute = $this->getAOrB(
                $this->_isSectionSet( $aField ),
                "{$aField['section_id']}{$_sSectionIndex}[{$aField['field_id']}]{$_sKey}",
                "{$aField['field_id']}{$_sKey}"
            );

            return ! is_callable( $hfFilterCallback )
                ? $_sNameAttribute
                : call_user_func_array( 
                    $hfFilterCallback, 
                    array( 
                        $_sNameAttribute, 
                        $aField, 
                        $sKey // the unformatted raw value. (not $_sKey)
                    ) 
                );

        }
    
        /**
         * Retrieves the field name attribute whose dimensional elements are delimited by the pile character.
         * 
         * Instead of [] enclosing array elements, it uses the pipe(|) to represent the multi dimensional array key.
         * This is used to create a reference to the submit field name to determine which button is pressed.
         * 
         * @remark      Used by the import and submit field types.
         * @since       2.0.0
         * @since       2.1.5       Made the parameter mandatory. Changed the scope to protected from private. Moved from AdminPageFramework_FormField.
         * @since       3.0.0       Moved from the submit field type class. Dropped the page slug dimension.
         * @since       3.2.0       Added the $hfFilterCallback parameter.
         * @since       3.6.0       Changed the scope to `private` from `protected` to help understand this method is only accessed internally.
         * @return      string
         */ 
        private function _getFlatInputName( array $aField, $sKey='', $hfFilterCallback=null ) {    
            
            $sKey           = ( string ) $sKey; // a 0 value may have been interpreted as false.
            $_sKey          = $this->getAOrB(
                '0' !== $sKey && empty( $sKey ),
                '',
                "|{$sKey}"
            );
            $_sSectionIndex = isset( $aField['section_id'], $aField['_section_index'] ) 
                ? "|{$aField['_section_index']}" 
                : '';

            $_sFlatName = $this->getAOrB(
                $this->_isSectionSet( $aField ),
                "{$aField['section_id']}{$_sSectionIndex}|{$aField['field_id']}{$_sKey}",
                "{$aField['field_id']}{$_sKey}"
            );

            return ! is_callable( $hfFilterCallback )
                ? $_sFlatName
                : call_user_func_array( 
                    $hfFilterCallback, 
                    array( 
                        $_sFlatName, 
                        $aField, 
                        $sKey   // the unformatted raw value (not $_sKey)
                    )
                );

        }
                   
    
    /**
     * Returns the input id attribute value.
     * 
     * e.g. "{$aField['field_id']}__{$isIndex}";
     * 
     * @remark      The index keys are prefixed with double-underscores.
     * @remark      `AdminPageFramework_FormTable_Row` will also access this method so this method is public.
     * @since       2.0.0
     * @since       3.2.0       Added the $hfFilterCallback parameter.
     * @since       3.3.2       Made it static public because the `<for>` tag needs to refer to it and it is called from another class that renders the form table. Added a default value for the <var>$isIndex</var> parameter.
     * @since       3.6.0       Moved from `AdminPageFramework_FormField`. Changed the scope to be not static.
     */
    public function getInputID( $aField, $isIndex=0, $hfFilterCallback=null ) {
        
        $_sSectionIndex   = isset( $aField['_section_index'] ) 
            ? '__' . $aField['_section_index'] 
            : ''; // double underscore
        $_isFieldIndex    = '__' . $isIndex; // double underscore
        $_sInputAttribute = isset( $aField['section_id'] ) && '_default' !== $aField['section_id']
            ? $aField['section_id'] . $_sSectionIndex . '_' . $aField['field_id'] . $_isFieldIndex
            : $aField['field_id'] . $_isFieldIndex;
        return ! is_callable( $hfFilterCallback )
            ? $_sInputAttribute
            : call_user_func_array( 
                $hfFilterCallback, 
                array( 
                    $_sInputAttribute
                )
            );
            
    }            
    
}