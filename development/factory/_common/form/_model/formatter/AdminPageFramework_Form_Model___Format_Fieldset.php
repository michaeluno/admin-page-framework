<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to format form individual field-set definition arrays.
 *
 * @package     AdminPageFramework/Common/Form/Model/Format
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Form_Model___Format_Fieldset extends AdminPageFramework_Form_Model___Format_FormField_Base {

    /**
     * Represents the structure of the form field array.
     *
     * @since       2.0.0
     * @since       3.6.0       Moved from `AdminPageFramework_FormDefinition`.
     * @var         array       Represents the array structure of form field.
     * @static
     * @internal
     */
    static public $aStructure = array(

        // Required Keys
        'field_id'                  => null,    // (string)
        'section_id'                => null,    // (string)

        // Optional Keys
        'type'                      => null,    // (string) (3.8.0+ Became okay to omit.)
        'section_title'             => null,    // This will be assigned automatically in the formatting method.
        'page_slug'                 => null,    // This will be assigned automatically in the formatting method.
        'tab_slug'                  => null,    // This will be assigned automatically in the formatting method.
        'option_key'                => null,    // This will be assigned automatically in the formatting method.
        'class_name'                => null,    // Stores the instantiated class name. Used by the export field type. Also a third party custom field type uses it.
        'capability'                => null,
        'title'                     => null,
        'tip'                       => null,
        'description'               => null,
        'error_message'             => null,    // error message for the field
        'before_label'              => null,
        'after_label'               => null,
        'if'                        => true,
        'order'                     => null,    // do not set the default number here for this key.
        'default'                   => null,
        'value'                     => null,
        'help'                      => null,    // 2.1.0
        'help_aside'                => null,    // 2.1.0
        'repeatable'                => null,    // 2.1.3
        'sortable'                  => null,    // 2.1.3
        'show_title_column'         => true,    // 3.0.0
        'hidden'                    => null,    // 3.0.0

        'placement'                 => 'normal',    // 3.8.0 (string) accepts either 'section_title', 'field_title', or 'normal'

        // @todo    Examine why an array is not set but null here for the attributes argument.
        'attributes'                => null,    // 3.0.0 - the array represents the attributes of input tag
        'class'                     => array(   // 3.3.1
            'fieldrow'  =>  array(),
            'fieldset'  =>  array(),
            'fields'    =>  array(),
            'field'     =>  array(),
        ),

        'save'                      => true,    // 3.6.0
        'content'                   => null,    // 3.6.1 - (string) An overriding field-set output.

        'show_debug_info'           => null,    // 3.8.8+  (boolean) whether to show debug information such as field definition tool-tips. This value is inherited from the section definition argument of the same name. Not setting a value here as it is determined with another calculated value.

        // Internal Keys
        '_fields_type'              => null,    // @deprecated  3.7.0, 3.0.0 - an internal key that indicates the fields type such as page, meta box for pages, meta box for posts, or taxonomy.
        '_structure_type'           => null,    // 3.7.0
        '_caller_object'            => null,    // 3.4.0 (object) stores the object of the caller class. The object is referenced when creating nested fields.

        '_section_path'             => '',      // 3.7.0 (string) Stores the section path that indicates the structural address of the nested section. e.g. my_section|nested_one
        '_section_path_array'       => '',      // 3.7.0 (array) An array version of the above section path.
        '_nested_depth'             => 0,       // 3.4.0 (integer) stores the level of the nesting depth. This is mostly used for debugging by checking if the field is a nested field or not.
        '_subsection_index'         => null,    // 3.7.0 Passed to the `field_definition_{...}` filter hook callbacks.
        '_section_repeatable'       => false,   // @deprecated
        '_is_section_repeatable'    => false,   // 3.8.0 (boolean) Whether the belonging section is repeatable or not.

        '_field_path'               => '',      // 3.7.0 (string) Stores the field path that indicates the structural location of the field. This is relative to the belonging section.
        '_field_path_array'         => array(), // 3.7.0 (array) An array version of the above field path.
        '_parent_field_path'        => '',      // 3.8.0 (string)
        '_parent_field_path_array'  => array(), // 3.8.0 (array)

        '_is_title_embedded'        => false,   // 3.8.0 (boolean) whether the field title is in the fieldset element, not in the table th element. This becomes `true` for `section_title` fields and fields with the `placement` argument with the value of `section_title` or `field_title`.

    );

    /**
     * Stores the passed unformatted fieldset definition array.
     */
    public $aFieldset = array();

    /**
     * Stores the fields type.
     * @remark      This is not the field type but 'fields' type.
     */
    public $sStructureType = '';

    /**
     * The capability.
     */
    public $sCapability = 'manage_options';

    /**
     * Stores the count of fields.
     */
    public $iCountOfElements = 0;

    /**
     * Stores the section index.
     */
    public $iSubSectionIndex = null;

    /**
     * Stores a flag that indicates whether the section is repeatable or not.
     */
    public $bIsSectionRepeatable = false;

    /**
     * Stores the caller object.
     */
    public $oCallerObject;


    /**
     * Sets up properties.
     */
    public function __construct( /* $aFieldset, $sStructureType, $sCapability, $iCountOfElements, $iSubSectionIndex, $bIsSectionRepeatable, $oCallerObject */ ) {

        $_aParameters = func_get_args() + array(
            $this->aFieldset,
            $this->sStructureType,
            $this->sCapability,
            $this->iCountOfElements,
            $this->iSubSectionIndex,
            $this->bIsSectionRepeatable,
            $this->oCallerObject
        );
        $this->aFieldset            = $_aParameters[ 0 ];
        $this->sStructureType       = $_aParameters[ 1 ];
        $this->sCapability          = $_aParameters[ 2 ];
        $this->iCountOfElements     = $_aParameters[ 3 ];
        // @todo    The section index value is still not accurate in the timing that only sanitize and condition sections and fieldset definition arrays.
        $this->iSubSectionIndex     = $_aParameters[ 4 ];
        $this->bIsSectionRepeatable = $_aParameters[ 5 ];
        $this->oCallerObject        = $_aParameters[ 6 ];

    }

    /**
     *
     * @return      array       The formatted definition array.
     */
    public function get() {

        // Fill missing argument keys - this method overrides 'null' values.
        $_aFieldset = $this->uniteArrays(
            array(
                '_fields_type'           => $this->sStructureType, // @deprecated 3.7.0 backward-compatibility
                '_structure_type'        => $this->sStructureType,
                '_caller_object'         => $this->oCallerObject,  // 3.4.1+ Stores the caller form object.
                '_subsection_index'      => $this->iSubSectionIndex,  // 3.7.0+
            )
            + $this->aFieldset,
            array(
                'capability'             => $this->sCapability,
                'section_id'             => '_default',
                '_section_repeatable'    => $this->bIsSectionRepeatable,   // @deprecated  3.8.0   This was not used.
                '_is_section_repeatable' => $this->bIsSectionRepeatable,
            )
            + self::$aStructure
        );

        $_aFieldset[ 'field_id' ]            = $this->getIDSanitized( $_aFieldset[ 'field_id' ] );
        $_aFieldset[ 'section_id' ]          = $this->getIDSanitized( $_aFieldset[ 'section_id' ] );
        $_aFieldset[ '_section_path' ]       = $this->getFormElementPath( $_aFieldset[ 'section_id' ] );
        $_aFieldset[ '_section_path_array' ] = explode( '|', $_aFieldset[ '_section_path' ] );
        $_aFieldset[ '_field_path' ]         = $this->_getFieldPath( $_aFieldset );
        $_aFieldset[ '_field_path_array' ]   = explode( '|', $_aFieldset[ '_field_path' ] );
        $_aFieldset[ 'order' ]               = $this->getAOrB(
            is_numeric( $_aFieldset[ 'order' ] ),
            $_aFieldset[ 'order' ],
            $this->iCountOfElements + 10
        );

        $_aFieldset[ 'class' ] = $this->getAsArray( $_aFieldset[ 'class' ] );

        // 3.8.0+ Support nested fields and inline_mized field type.
        if ( $this->hasFieldDefinitionsInContent( $_aFieldset ) ) {
            $_aFieldset[ 'content' ] = $this->_getChildFieldsetsFormatted( $_aFieldset[ 'content' ], $_aFieldset );
        }

        // 3.8.0+ Set the internal field type slug as the user can omit the field type slug.
        if ( $this->hasNestedFields( $_aFieldset ) ) {
            $_aFieldset[ 'type' ] = '_nested';
        }

        // 3.8.0+
        $_aFieldset[ '_is_title_embedded' ] = $this->_isTitleEmbedded( $_aFieldset );

        // 3.8.8+
        $_aFieldset[ 'show_debug_info' ] = $this->_getShowDebugInfo( $_aFieldset );

        return $_aFieldset;

    }
        /**
         * Determines the value of the `show_debug_info` argument.
         *
         * Assumes the `_section_path` is already set.
         *
         * @remark      This should be inherited from the section if not set explicitly in the field definition argument.
         * @since       3.8.8
         * @return      boolean
         */
        private function _getShowDebugInfo( $aFieldset ) {

            // If the user sets a value. use it.
            if ( isset( $aFieldset[ 'show_debug_info' ] ) ) {
                return $aFieldset[ 'show_debug_info' ];
            }

            // Retrieve from the section definition which this field belong to.
            return $this->getElement(
                $this->oCallerObject->aSectionsets,
                array( $aFieldset[ '_section_path' ], 'show_debug_info' ),
                true
            );

        }

        /**
         * Checks whether the field title is supposed to be embedded in the field-set element.
         *
         * @since       3.8.0
         * @return      boolean
         * @internal
         */
        private function _isTitleEmbedded( array $aFieldset ) {

            if ( 'section_title' === $aFieldset[ 'type' ] ) {
                return true;
            }

            // Can be either `section_title` or `field_title`.
            if ( 'normal' !== $aFieldset[ 'placement' ] ) {
                return true;
            }

            return false;

        }

        /**
         * Calculates a field path.
         * @since       3.8.0
         * @return      string
         */
        private function _getFieldPath( array $aFieldset ) {
            return $this->getTrailingPipeCharacterAppended( $aFieldset[ '_parent_field_path' ] )
                . $this->getFormElementPath( $aFieldset[ 'field_id' ] );
        }

        /**
         * Formats the child fieldsets definition arrays.
         *
         * @since       3.8.0
         * @return      array
         */
        private function _getChildFieldsetsFormatted( array $aNestedFieldsets, array $aParentFieldset ) {

            $_aInheritingFieldsetValues = array(
                'section_id'                => $aParentFieldset[ 'section_id' ],
                'section_title'             => $aParentFieldset[ 'section_title' ],
'placement'                 => $aParentFieldset[ 'placement' ],
                'page_slug'                 => $aParentFieldset[ 'page_slug' ],
                'tab_slug'                  => $aParentFieldset[ 'tab_slug' ],
                'option_key'                => $aParentFieldset[ 'option_key' ],
                'class_name'                => $aParentFieldset[ 'class_name' ],
                'capability'                => $aParentFieldset[ 'capability' ],
                '_structure_type'           => $aParentFieldset[ '_structure_type' ],
                '_caller_object'            => $aParentFieldset[ '_caller_object' ],
                '_section_path'             => $aParentFieldset[ '_section_path' ],
                '_section_path_array'       => $aParentFieldset[ '_section_path_array' ],
                '_subsection_index'         => $aParentFieldset[ '_subsection_index' ],

            );

            foreach( $aNestedFieldsets as $_isIndex => &$_aNestedFieldset ) {

                // The inline-mixed type has a string element.
                if ( is_scalar( $_aNestedFieldset ) ) {
                    $_aNestedFieldset = array(
                        'field_id'              => $aParentFieldset[ 'field_id' ] . '_' . uniqid(),
                        'content'               => $_aNestedFieldset,
                    );
                }

                $_aNestedFieldset[ '_parent_field_path' ]       = $aParentFieldset[ '_field_path' ];
                $_aNestedFieldset[ '_parent_field_path_array' ] = explode( '|', $aParentFieldset[ '_parent_field_path' ] );
                $_aNestedFieldset[ '_nested_depth' ]            = $aParentFieldset[ '_nested_depth' ] + 1;
                $_oFieldsetFormatter = new AdminPageFramework_Form_Model___Format_Fieldset(
                    $_aNestedFieldset + $_aInheritingFieldsetValues, // merge with the parent definition to inherit its values
                    $this->sStructureType,
                    $this->sCapability,
                    $this->iCountOfElements,
                    $this->iSubSectionIndex,
                    $this->bIsSectionRepeatable,
                    $this->oCallerObject
                );
                $_aNestedFieldset = $_oFieldsetFormatter->get();

            }

            return $aNestedFieldsets;

        }

}
