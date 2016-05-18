<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
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
class AdminPageFramework_Form_Model___Format_EachField extends AdminPageFramework_Form_Model___Format_FormField_Base {
    
    /**
     * Represents the structure of the sub-field definition array.
     */
    static public $aStructure = array(
        '_is_sub_field'                 => false,   // @todo change this key name as all the parsed field is technically a sub-field.
        '_index'                        => 0,       // indicates the field index
        '_is_multiple_fields'           => false,
        '_saved_value'                  => null,
        '_is_value_set_by_user'         => false,
        
        '_field_container_id'           => '',
        '_input_id_model'               => '',
        '_input_name_model'             => '',
        
        '_input_name_flat'              => '',
        
        '_fields_container_id'          => '',
        '_fieldset_container_id'        => '',
        
        '_field_object'                 => null,        // 3.6.0+
        '_parent_field_object'          => null,        // 3.6.0+ Stores the parent field object to be accessed from the nested fields to generate id and name attribute models.
    );
    
    /**
     * 
     */
    public $aField                  = array();
    
    public $isIndex                 = 0;
    
    public $aCallbacks              = array();
    
    public $aFieldTypeDefinition    = array(
        'aDefaultKeys'  => array(
            'class' => null,
        ),
    );
    
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
        $this->aFieldTypeDefinition = $this->getAsArray( $_aParameters[ 3 ] );
    
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
        
        $_aField[ '_is_sub_field' ]            = is_numeric( $this->isIndex ) && 0 < $this->isIndex;      // 3.5.3+
        $_aField[ '_index' ]                   = $this->isIndex;

        // 'input_id' - something like ({section id}_){field_id}__{index} e.g. my_section_id_my_field_id__0
        $_oInputTagIDGenerator = new AdminPageFramework_Form_View___Generate_FieldInputID( 
            $_aField,  
            $this->isIndex,
            $this->aCallbacks[ 'hfID' ]
        );
        $_aField[ 'input_id' ] = $_oInputTagIDGenerator->get();

        $_oFieldInputNameGenerator = new AdminPageFramework_Form_View___Generate_FieldInputName(
            $_aField, 
            $this->getAOrB(
                $_aField[ '_is_multiple_fields' ],
                $this->isIndex,
                ''
            ),
            $this->aCallbacks[ 'hfInputName' ]       
        );
        $_aField[ '_input_name' ] = $_oFieldInputNameGenerator->get();
        
        // '_input_name_flat' - used for submit, export, import field types
        $_oFieldFlatInputName = new AdminPageFramework_Form_View___Generate_FlatFieldInputName(
            $_aField,
            $this->getAOrB(
                $_aField[ '_is_multiple_fields' ],
                $this->isIndex,
                ''
            ),
            $this->aCallbacks[ 'hfInputNameFlat' ]        
        );
        $_aField[ '_input_name_flat' ] = $_oFieldFlatInputName->get();
                            
        // used in the attribute below plus it is also used in the sample custom field type.
        $_aField[ '_field_container_id' ]      = "field-{$_aField[ 'input_id' ]}";            
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
        
        $_aField[ 'attributes' ][ 'class' ] = 'widget' === $_aField[ '_structure_type' ] && is_callable( $this->aCallbacks[ 'hfClass' ] )
            ? call_user_func_array( $this->aCallbacks[ 'hfClass' ], array( $_aField[ 'attributes' ][ 'class' ] ) )
            : $_aField[ 'attributes' ][ 'class' ];
        $_aField[ 'attributes' ][ 'class' ] = $this->getClassAttribute(
            $_aField[ 'attributes' ][ 'class' ],  
            $this->dropElementsByType( $_aField[ 'class' ] )
        );
        
        // 3.6.0+
        $_aField[ '_field_object' ] = new AdminPageFramework_ArrayHandler( $_aField );
        return $_aField;
        
    }


    /**
     * Generates a name attribute model for dynamic fields such as repeatable and sortable fields.
     * 
     * The repeatable script will check this name to generate incremented name.
     * @since       3.6.0
     * @return      string
     */    
    // protected function _getInputNameModel( $aField, $isIndex=0, $hfFilterCallback=null ) {
    // }    
    
}
