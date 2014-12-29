<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to compose form elements
 * 
 * @package     AdminPageFramework
 * @subpackage  Property
 * @since       3.0.0
 * @internal
 */
class AdminPageFramework_FormElement extends AdminPageFramework_FormElement_Utility {
    
    /**
     * Represents the structure of the form section array.
     * 
     * @since       2.0.0
     * @remark      Not for the user.
     * @var         array       Holds array structure of form section.
     * @static
     * @internal
     */     
    static public $_aStructure_Section = array(    
        'section_id'        => '_default', // 3.0.0+
        '_fields_type'      => null, // 3.0.0+ - same as the one of the field definition array. Used to insert debug info at the bottom of sections.        
        'page_slug'         => null,
        'tab_slug'          => null,
        'section_tab_slug'  => null, // 3.0.0+
        'title'             => null,
        'description'       => null,
        'capability'        => null,
        'if'                => true,    
        'order'             => null, // do not set the default number here because incremented numbers will be added when registering the sections.
        'help'              => null,
        'help_aside'        => null,
        'repeatable'        => null, // 3.0.0+
        'attributes'        => array(   // 3.3.1+
            'class'         => null,    // set null to avoid undefined index warnings.
            'style'         => null,    // set null to avoid undefined index warnings.
            'tab'           => array(),
        ),
        'class'             => array(    // 3.3.1+
            'tab'           => array(),
        ),
        'hidden'            => false,    // 3.3.1+
        'collapsible'       => false,    // 3.4.0+ (boolean|array) For the array structure see the $_aStructure_CollapsibleArguments property.
        '_is_first_index'   => false,    // 3.4.0+ (boolean) indicates whether it is the first item of the sub-sections (for repeatable sections).
        '_is_last_index'    => false,    // 3.4.0+ (boolean) indicates whether it is the last item of the sub-sections (for repeatable sections).
    );    
    
    /**
     * Represents the structure of the 'collapsible' argument.
     * @since       3.4.0
     */
    static public $_aStructure_CollapsibleArguments = array(
        'title'                     => null,    // (string)  the section title will be assigned by default in the section formatting method.
        'is_collapsed'              => true,    // (boolean) whether it is already collapsed or expanded
        'toggle_all_button'         => null,    // (boolean|string|array) the position of where to display the toggle-all button that toggles the folding state of all collapsible sections. Accepts the following values. 'top-right', 'top-left', 'bottom-right', 'bottom-left'. If true is passed, the default 'top-right' will be used. To not to display, do not set any or pass `false` or `null`.
        'collapse_others_on_expand' => true,    // (boolean) whether the other collapsible sections should be folded when the section is unfolded.
        'container'                 => 'sections'   // (string) the container element that collapsible styling gets applied to. Either 'sections' or 'section' is accepted.
    );
    
    /**
     * Represents the structure of the form field array.
     * 
     * @since 2.0.0
     * @remark Not for the user.
     * @var array Holds array structure of form field.
     * @static
     * @internal
     */ 
    static public $_aStructure_Field = array(
        'field_id'          => null,    // (required)
        'type'              => null,    // (required)
        'section_id'        => null,    // (optional)
        'section_title'     => null,    // This will be assigned automatically in the formatting method.
        'page_slug'         => null,    // This will be assigned automatically in the formatting method.
        'tab_slug'          => null,    // This will be assigned automatically in the formatting method.
        'option_key'        => null,    // This will be assigned automatically in the formatting method.
        'class_name'        => null,    // used by the export field type
        'capability'        => null,        
        'title'             => null,    
        'tip'               => null,    
        'description'       => null,    
        'error_message'     => null,    // error message for the field
        'before_label'      => null,    
        'after_label'       => null,    
        'if'                => true,    
        'order'             => null,    // do not set the default number here for this key.     
        'default'           => null,
        'value'             => null,
        'help'              => null,    // 2.1.0+
        'help_aside'        => null,    // 2.1.0+
        'repeatable'        => null,    // 2.1.3+
        'sortable'          => null,    // 2.1.3+
        'show_title_column' => true,    // 3.0.0+
        'hidden'            => null,    // 3.0.0+
        '_fields_type'      => null,    // 3.0.0+ - an internal key that indicates the fields type such as page, meta box for pages, meta box for posts, or taxonomy.
        '_section_index'    => null,    // 3.0.0+ - internally set to indicate the section index for repeatable sections.
    // @todo    Examine why an array is not set but null here for the attributes argument.
        'attributes'        => null,    // 3.0.0+ - the array represents the attributes of input tag
        'class'             => array(   // 3.3.1+
            'fieldrow'  =>  array(),
            'fieldset'  =>  array(),
            'fields'    =>  array(),
            'field'     =>  array(),
        ), 
        '_caller_object'    => null,    // 3.4.0+ - stores the object of the caller class. The object is referenced when creating nested fields.
        '_nested_depth'     => 0,       // 3.4.0+ - stores the level of the nesting depth. This is mostly used for debugging by checking if the field is a nested field or not.
    );    
    
    /**
     * Stores field definition arrays.
     * @since 3.0.0
     */
    public $aFields = array();
    
    /**
     * Stores section definition arrays.
     * 
     * @since 3.0.0
     */
    public $aSections = array(
        '_default' => array(),
    );
    
    /**
     * Stores the conditioned fields definition array.
     * 
     * @since 3.0.0
     */
    public $aConditionedFields = array();
    
    /**
     * Stores the conditioned sections definition array.
     * 
     * @since 3.0.0
     */
    public $aConditionedSections = array();
    
    /**
     * Stores the fields type. 
     * 
     * @since 3.0.0
     */
    protected $sFieldsType = '';
    
    /**
     * Stores the target page slug which will be applied when no page slug is specified.
     * 
     * @since 3.0.0
     */
    protected $_sTargetSectionID = '_default';    
    
    /**
     * Stores the default access level of the fields.
     * 
     * @remark The scope is public to allow change the value externally.
     * @since 3.0.0
     */
    public $sCapability = 'manage_option';
    
    /**
     * Stores the default capability.
     * 
     * @since       3.0.0
     * @since       3.4.0       Added the $oCaller parameter.
     * 
     * @param       string      $sFieldsType
     * @param       string      $sCapability    
     * @param       object      $oCaller            The caller object. Each formatted field will have the caller object. 
     * This give power to each field to create nested fields.
     */
    public function __construct( $sFieldsType, $sCapability, $oCaller=null ) {
        
        $this->sFieldsType  = $sFieldsType;
        $this->sCapability  = $sCapability;
        $this->oCaller      = $oCaller;
        
    }
    
    /**
     * Adds the given section definition array to the form property.
     * 
     * @since 3.0.0
     */
    public function addSection( array $aSection ) {
        
        $aSection               = $aSection + self::$_aStructure_Section;
        $aSection['section_id'] = $this->sanitizeSlug( $aSection['section_id'] );
        
        $this->aSections[ $aSection['section_id'] ] = $aSection;    
        $this->aFields[ $aSection['section_id'] ]   = isset( $this->aFields[ $aSection['section_id'] ] ) 
            ? $this->aFields[ $aSection['section_id'] ] 
            : array();

    }
    
    /**
     * Removes a section definition array from the property by the given section ID.
     * 
     * @since 3.0.0
     */
    public function removeSection( $sSectionID ) {
        
        if ( '_default' === $sSectionID ){  return; }
        
        unset( $this->aSections[ $sSectionID ] );
        unset( $this->aFields[ $sSectionID ] );
        
    }
    
    /*
     * Adds the given field definition array to the form property.
     * 
     * @since 3.0.0
     * @return array|string|null If the passed field is set, it returns the set field array. If the target section id is set, the set section id is returned. Otherwise null.
     */    
    public function addField( $asField ) {
        
        if ( ! is_array( $asField ) ) {
            $this->_sTargetSectionID = is_string( $asField ) ? $asField : $this->_sTargetSectionID;
            return $this->_sTargetSectionID;
        }
        $aField = $asField;
        $this->_sTargetSectionID = isset( $aField['section_id'] ) ? $aField['section_id'] : $this->_sTargetSectionID;
        
        $aField = $this->uniteArrays( 
            array( '_fields_type' => $this->sFieldsType ),
            $aField, 
            array( 'section_id' => $this->_sTargetSectionID ),
            self::$_aStructure_Field
        );
        if ( ! isset( $aField['field_id'], $aField['type'] ) ) { return null; } // Check the required keys as these keys are necessary.
            
        // Sanitize the IDs since they are used as a callback method name.
        $aField['field_id']     = $this->sanitizeSlug( $aField['field_id'] );
        $aField['section_id']   = $this->sanitizeSlug( $aField['section_id'] );     
        
        $this->aFields[ $aField['section_id'] ][ $aField['field_id'] ] = $aField;
        return $aField;
        
    }    
        
    /**
     * Removes a field definition array from the property array by the given field ID.
     * 
     *  The structure of the aFields property array looks like this:
     *  <code>    array( 
     *          'my_sec_a' => array(
     *              'my_field_a' => array( ... ),
     *              'my_field_b' => array( ... ),
     *              'my_field_c' => array( ... ),
     *          ),
     *          'my_sec_b' => array(
     *              'my_field_a' => array( ... ),
     *              'my_field_b' => array( ... ),
     *              1 => array(
     *                  'my_field_a' => array( ... ),
     *                  'my_field_b' => array( ... ),
     *              )
     *              2 => array(
     *                  'my_field_a' => array( ... ),
     *                  'my_field_b' => array( ... ),
     *              )     
     *          )
     *      )</code>
     * 
     * @since 3.0.0
     */     
    public function removeField( $sFieldID ) {
               
        foreach( $this->aFields as $_sSectionID => $_aSubSectionsOrFields ) {

            if ( array_key_exists( $sFieldID, $_aSubSectionsOrFields ) ) {
                unset( $this->aFields[ $_sSectionID ][ $sFieldID ] );
            }
            
            // Check sub-sections.
            foreach ( $_aSubSectionsOrFields as $_sIndexOrFieldID => $_aSubSectionOrFields ) {
                
                if ( is_numeric( $_sIndexOrFieldID ) && is_int( $_sIndexOrFieldID + 0 ) ) { // means it's a sub-section
                    
                    if ( array_key_exists( $sFieldID, $_aSubSectionOrFields ) ) {
                        unset( $this->aFields[ $_sSectionID ][ $_sIndexOrFieldID ] );
                    }
                    continue;
                    
                }
                
            }
        }
        
    }
    
    /**
     * Formats the section and field definition arrays.
     * 
     * @since 3.0.0
     */
    public function format() {
        
        $this->aSections    = $this->formatSections( $this->aSections, $this->sFieldsType, $this->sCapability );
        $this->aFields      = $this->formatFields( $this->aFields, $this->sFieldsType, $this->sCapability );
        
    }
    
    /**
     * Formats the stored sections definition array.
     * 
     * @since 3.0.0
     * @since 3.1.1 Added a parameter. Changed to return the formatted sections array.
     * @return array    the formatted sections array.
     */
    public function formatSections( array $aSections, $sFieldsType, $sCapability ) {

        $_aNewSectionArray = array();
        foreach( $aSections as $_sSectionID => $_aSection ) {

            if ( ! is_array( $_aSection ) ) { continue; }

            $_aSection = $this->formatSection( $_aSection, $sFieldsType, $sCapability, count( $_aNewSectionArray ) );
            if ( ! $_aSection ) { continue; }
            
            $_aNewSectionArray[ $_sSectionID ] = $_aSection;
            
        }
        uasort( $_aNewSectionArray, array( $this, '_sortByOrder' ) ); 
        return $_aNewSectionArray;
        
    }
    
        /**
         * Returns the formatted section array.
         * 
         * @since       3.0.0
         * @remark      The scope is protected because the extended page class overrides this method.
         */
        protected function formatSection( array $aSection, $sFieldsType, $sCapability, $iCountOfElements ) {

            $aSection = $this->uniteArrays(
                $aSection,
                array( 
                    '_fields_type'  => $sFieldsType,
                    'capability'    => $sCapability,
                ),
                self::$_aStructure_Section
            );
                
            $aSection['order'] = is_numeric( $aSection['order'] ) ? $aSection['order'] : $iCountOfElements + 10;
            
            // 3.4.0
            if ( empty( $aSection['collapsible'] ) ) {
                $aSection['collapsible'] = $aSection['collapsible'];
            } else {
                $aSection['collapsible'] = $this->getAsArray( $aSection['collapsible'] ) + array(
                    'title' => $aSection['title'],
                ) +  self::$_aStructure_CollapsibleArguments;
                $aSection['collapsible']['toggle_all_button'] = implode( ',', $this->getAsArray( $aSection['collapsible']['toggle_all_button'] ) );
            }
            
            return $aSection;
            
        }
        
        
    /**
     * Formats the stored fields definition array.
     * 
     * @since 3.0.0
     * @since 3.1.1 Added a parameter. Changed to return the formatted sections array.
     */
    public function formatFields( array $aFields, $sFieldsType, $sCapability ) {

        $_aNewFields = array();
        foreach ( $aFields as $_sSectionID => $_aSubSectionsOrFields ) {
            
            if ( ! isset( $this->aSections[ $_sSectionID ] ) ) { continue; }

            $_aNewFields[ $_sSectionID ] = isset( $_aNewFields[ $_sSectionID ] ) ? $_aNewFields[ $_sSectionID ] : array();
            
            // If there are sub-section items.
            $_abSectionRepeatable = $this->aSections[ $_sSectionID ]['repeatable']; // a setting array or boolean or true/false
            if ( count( $this->getIntegerElements( $_aSubSectionsOrFields ) ) || $_abSectionRepeatable ) { // if sub-section exists or repeatable
                
                foreach( $this->numerizeElements( $_aSubSectionsOrFields ) as $_iSectionIndex => $_aFields ) {
                                            
                    foreach( $_aFields as $_aField ) {
                        
                        $_iCountElement = isset( $_aNewFields[ $_sSectionID ][ $_iSectionIndex ] ) ? count( $_aNewFields[ $_sSectionID ][ $_iSectionIndex ] ) : 0 ;
                        $_aField = $this->formatField( $_aField, $sFieldsType, $sCapability, $_iCountElement, $_iSectionIndex, $_abSectionRepeatable, $this->oCaller );
                        if ( $_aField ) {
                            $_aNewFields[ $_sSectionID ][ $_iSectionIndex ][ $_aField['field_id'] ] = $_aField;     
                        }
                        
                    }
                    uasort( $_aNewFields[ $_sSectionID ][ $_iSectionIndex ], array( $this, '_sortByOrder' ) );                 
                    
                }
                continue;
                
            }
            
            // Otherwise, these are normal sectioned fields.
            $_aSectionedFields = $_aSubSectionsOrFields;
            foreach( $_aSectionedFields as $_sFieldID => $_aField ) {
                
                // Insert the formatted field definition array.
                $_iCountElement = isset( $_aNewFields[ $_sSectionID ] ) ? count( $_aNewFields[ $_sSectionID ] ) : 0; // the count is needed to set each order value.
                $_aField = $this->formatField( $_aField, $sFieldsType, $sCapability, $_iCountElement, null, $_abSectionRepeatable, $this->oCaller );
                if ( $_aField ) {
                    $_aNewFields[ $_sSectionID ][ $_aField['field_id'] ] = $_aField;
                }
                
            }
            uasort( $_aNewFields[ $_sSectionID ], array( $this, '_sortByOrder' ) ); 
                
        }
        
        // Sort by the order of the sections.
        if ( ! empty( $this->aSections ) && ! empty( $_aNewFields ) ) : // as taxonomy fields don't have sections
            $_aSortedFields = array();
            foreach( $this->aSections as $sSectionID => $aSeciton ) { // will be parsed in the order of the $aSections array. Therefore, the sections must be formatted before this method.
                if ( isset( $_aNewFields[ $sSectionID ] ) ) {
                    $_aSortedFields[ $sSectionID ] = $_aNewFields[ $sSectionID ];
                }
            }
            $_aNewFields = $_aSortedFields;
        endif;
        
        return $_aNewFields;
        
    }
        /**
         * Returns the formatted field array.
         * 
         * @since       3.0.0
         */
        protected function formatField( $aField, $sFieldsType, $sCapability, $iCountOfElements, $iSectionIndex, $bIsSectionRepeatable, $oCallerObject ) {
            
            if ( ! isset( $aField['field_id'], $aField['type'] ) ) { return; }
            
            $_aField = $this->uniteArrays(
                array( 
                    '_fields_type'          => $sFieldsType,
                    '_caller_object'        => $oCallerObject,  // 3.4.1+ Stores the caller object. 
                )
                + $aField,
                array( 
                    'capability'            => $sCapability,
                    'section_id'            => '_default',
                    '_section_index'        => $iSectionIndex,
                    '_section_repeatable'   => $bIsSectionRepeatable,
                )
                + self::$_aStructure_Field
            );
            $_aField['field_id']    = $this->sanitizeSlug( $_aField['field_id'] );
            $_aField['section_id']  = $this->sanitizeSlug( $_aField['section_id'] );     
            $_aField['tip']         = esc_attr( strip_tags( 
                isset( $_aField['tip'] ) 
                    ? $_aField['tip'] 
                    : ( 
                        is_array( $_aField['description'] ) 
                            ? implode( '&#10;', $_aField['description'] ) 
                            : $_aField['description'] 
                    ) 
            ) );
            $_aField['order']       = is_numeric( $_aField['order'] ) ? $_aField['order'] : $iCountOfElements + 10;
                        
            return $_aField;
            
        }
        
    /**
     * Returns the fields-definition array that the conditions have been applied.
     * 
     * @since 3.0.0
     */
    public function applyConditions( $aFields=null, $aSections=null ) {
        
        return $this->getConditionedFields( $aFields, $this->getConditionedSections( $aSections ) );
        
    }
    
    /**
     * Returns a sections-array by applying the conditions.
     * 
     * It will internally sets the $aConditionedSections array property.
     * 
     * @since 3.0.0
     */
    public function getConditionedSections( $aSections=null ) {
        
        $aSections      = is_null( $aSections ) ? $this->aSections : $aSections;
        $_aNewSections  = array();

        foreach( $aSections as $_sSectionID => $_aSection ) {
            $_aSection = $this->getConditionedSection( $_aSection );
            if ( $_aSection ) {
                $_aNewSections[ $_sSectionID ] = $_aSection;
            }
        }        
        $this->aConditionedSections = $_aNewSections;
        return $_aNewSections;
        
    }
        /**
         * Returns the conditioned section definition array.
         * 
         * This method is meant to be overridden in the extended class to have more customized conditions.
         * 
         * @since 3.0.0
         */
        protected function getConditionedSection( array $aSection ) {
            
            // Check capability. If the access level is not sufficient, skip.
            if ( ! current_user_can( $aSection['capability'] ) ) { 
                return; 
            }
            if ( ! $aSection['if'] ) { 
                return; 
            }
            
            return $aSection;
            
        }
    
    /**
     * Returns a fields-array by applying the conditions.
     * 
     * This will internally stores the aConditionedFields array into the property.
     * 
     * @since 3.0.0
     */
    public function getConditionedFields( $aFields=null, $aSections=null ) {
        
        $aFields    = is_null( $aFields ) ? $this->aFields : $aFields;
        $aSections  = is_null( $aSections ) ? $this->aSections : $aSections;

        // Drop keys of fields-array which do not exist in the sections-array. For this reasons, the sections-array should be conditioned first before applying this method.
        $aFields    = ( array ) $this->castArrayContents( $aSections, $aFields );

        $_aNewFields = array();
        foreach( $aFields as $_sSectionID => $_aSubSectionOrFields ) {
            
            if ( ! is_array( $_aSubSectionOrFields ) ) { continue; }
            if ( ! array_key_exists( $_sSectionID, $aSections ) ) { continue; }
            
            foreach( $_aSubSectionOrFields as $_sIndexOrFieldID => $_aSubSectionOrField ) {
                
                // If it is a sub-section array.
                if ( is_numeric( $_sIndexOrFieldID ) && is_int( $_sIndexOrFieldID + 0 ) ) {
                    
                    $_sSubSectionIndex = $_sIndexOrFieldID;
                    $_aFields = $_aSubSectionOrField;
                    foreach( $_aFields as $_aField ) {
                        $_aField = $this->getConditionedField( $_aField );
                        if ( $_aField ) {
                            $_aNewFields[ $_sSectionID ][ $_sSubSectionIndex ][ $_aField['field_id'] ] = $_aField;     
                        }
                    }
                    continue;
                    
                }
                
                // Otherwise, insert the formatted field definition array.
                $_aField = $_aSubSectionOrField;
                $_aField = $this->getConditionedField( $_aField );
                if ( $_aField ) {
                    $_aNewFields[ $_sSectionID ][ $_aField['field_id'] ] = $_aField;
                }
                
            }
            
        }
                
        $this->aConditionedFields = $_aNewFields;
        return $_aNewFields;
        
    }     
        /**
         * Returns the field definition array by applying conditions. 
         * 
         * This method is intended to be extended to let the extended class customize the conditions.
         * 
         * @since 3.0.0
         */
        protected function getConditionedField( $aField ) {
            
            // Check capability. If the access level is not sufficient, skip.
            if ( ! current_user_can( $aField['capability'] ) ) { return null; }
            if ( ! $aField['if'] ) { return null; }
            return $aField;
            
        }
    
    
    /**
     * Adds dynamic elements such as repeatable sections from the given options array.
     * 
     * This method checks the structure of the given array and adds section elements to the $aConditionedFields property arrays.
     * 
     * @remark This should be called after conditioning the form definition arrays.
     * @since 3.0.0
     */
    public function setDynamicElements( $aOptions ) {
        
        $aOptions = $this->castArrayContents( $this->aConditionedSections, $aOptions );
        
        foreach( $aOptions as $_sSectionID => $_aSubSectionOrFields ) {
            
            if ( ! is_array( $_aSubSectionOrFields ) ) { continue; }
            
            $_aSubSection = array();
            foreach( $_aSubSectionOrFields as $_isIndexOrFieldID => $_aSubSectionOrFieldOptions ) {
            
                // If it is not a sub-section array, skip
                if ( ! ( is_numeric( $_isIndexOrFieldID ) && is_int( $_isIndexOrFieldID + 0 ) ) ) { continue; }
                
                $_iIndex = $_isIndexOrFieldID;
                
                // Insert the fields definition array into a temporary sub section array.
                $_aSubSection[ $_iIndex ] = isset( $this->aConditionedFields[ $_sSectionID ][ $_iIndex ] ) // already numerized ?
                    ? $this->aConditionedFields[ $_sSectionID ][ $_iIndex ]
                    : $this->getNonIntegerElements( $this->aConditionedFields[ $_sSectionID ] );
                $_aSubSection[ $_iIndex ] = ! empty( $_aSubSection[ $_iIndex ] )     // if empty, merge with the previous element.
                    ? $_aSubSection[ $_iIndex ]
                    : ( isset( $_aSubSection[ $_iPrevIndex ] )
                         ? $_aSubSection[ $_iPrevIndex ]
                         : array()
                    );
                
                // Update the internal section index key
                foreach( $_aSubSection[ $_iIndex ] as &$_aField ) {
                    $_aField['_section_index'] = $_iIndex;
                }
                unset( $_aField ); // to be safe in PHP
                
                $_iPrevIndex = $_iIndex;
                
            }

            if ( ! empty( $_aSubSection ) ) {
                // At this point, the associative keys will be gone but the element only consists of numeric keys.
                $this->aConditionedFields[ $_sSectionID ] = $_aSubSection;    
            }
            
        }

    }
        
}