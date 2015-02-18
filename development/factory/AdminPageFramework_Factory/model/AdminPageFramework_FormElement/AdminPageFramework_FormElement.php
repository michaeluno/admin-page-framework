<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods that deal with field and section definition arrays.
 * 
 * @package     AdminPageFramework
 * @subpackage  Property
 * @since       3.0.0
 * @internal
 */
class AdminPageFramework_FormElement extends AdminPageFramework_FormElement_Base {
    
    /**
     * Represents the structure of the form section array.
     * 
     * @since       2.0.0
     * @remark      Not for the user.
     * @var         array       Represents the array structure of form section.
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
        'title'                     => null,        // (string)  the section title will be assigned by default in the section formatting method.
        'is_collapsed'              => true,        // (boolean) whether it is already collapsed or expanded
        'toggle_all_button'         => null,        // (boolean|string|array) the position of where to display the toggle-all button that toggles the folding state of all collapsible sections. Accepts the following values. 'top-right', 'top-left', 'bottom-right', 'bottom-left'. If true is passed, the default 'top-right' will be used. To not to display, do not set any or pass `false` or `null`.
        'collapse_others_on_expand' => true,        // (boolean) whether the other collapsible sections should be folded when the section is unfolded.
        'container'                 => 'sections'   // (string) the container element that collapsible styling gets applied to. Either 'sections' or 'section' is accepted.
    );
    
    /**
     * Represents the structure of the form field array.
     * 
     * @since       2.0.0
     * @remark      Not for the user.
     * @var         array       Represents the array structure of form field.
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
        'class_name'        => null,    // Stores the instantiated class name. Used by the export field type. Also a third party custom field type uses it.
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
     * @since       3.0.0
     * @since       3.5.3       Changed the scope to `public` from `protected` as the meta box and user meta classes access this value from outside.
     * @access      public
     */
    public $sFieldsType = '';
    
    /**
     * Stores the target page slug which will be applied when no page slug is specified.
     * 
     * @since       3.0.0
     */
    protected $_sTargetSectionID = '_default';    
    
    /**
     * Stores the default access level of the fields.
     * 
     * @remark      The scope is public to let the value being changed externally.
     * @since       3.0.0
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
     * @since       3.0.0
     */
    public function addSection( array $aSection ) {
        
        $aSection               = $aSection + self::$_aStructure_Section;
        $aSection['section_id'] = $this->sanitizeSlug( $aSection['section_id'] );
        
        $this->aSections[ $aSection['section_id'] ] = $aSection;    
        $this->aFields[ $aSection['section_id'] ]   = $this->getElement(
            $this->aFields,  // subject array
            $aSection['section_id'], // key
            array()      // default
        );                                
        
    }
    
    /**
     * Removes a section definition array from the property by the given section ID.
     * 
     * @since       3.0.0
     */
    public function removeSection( $sSectionID ) {
        
        if ( '_default' === $sSectionID ){ 
            return; 
        }
        unset( 
            $this->aSections[ $sSectionID ],
            $this->aFields[ $sSectionID ]
        );
        
    }
    
    /*
     * Adds the given field definition array to the form property.
     * 
     * @since       3.0.0
     * @param       array|string            $asField        A field definition array.
     * @return      array|string|null       If the passed field is set, it returns the set field array. If the target section id is set, the set section id is returned. Otherwise null.
     */    
    public function addField( $asField ) {
        
        if ( ! is_array( $asField ) ) {
            $this->_sTargetSectionID = $this->getAOrB(
                is_string( $asField ),
                $asField,
                $this->_sTargetSectionID
            );
            return $this->_sTargetSectionID;
        }
        $_aField = $asField;
        $this->_sTargetSectionID = $this->getElement(
            $_aField,  // subject array
            'section_id', // key
            $this->_sTargetSectionID // default
        );                               
            
        $_aField = $this->uniteArrays( 
            array( '_fields_type' => $this->sFieldsType )
            + $_aField, 
            array( 'section_id' => $this->_sTargetSectionID )
            + self::$_aStructure_Field
        );
        
        // Required Keys
        if ( ! isset( $_aField['field_id'], $_aField['type'] ) ) { 
            return null; 
        } 
            
        // Sanitize the IDs since they are used as a callback method name.
        $_aField['field_id']     = $this->sanitizeSlug( $_aField['field_id'] );
        $_aField['section_id']   = $this->sanitizeSlug( $_aField['section_id'] );     
        
        $this->aFields[ $_aField['section_id'] ][ $_aField['field_id'] ] = $_aField;
        return $_aField;
        
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
                
                // if it's a sub-section
                if ( $this->isNumericInteger( $_sIndexOrFieldID ) ) {
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
     * @since       3.0.0
     * @since       3.1.1    Added a parameter. Changed to return the formatted sections array.
     * @return      array    the formatted sections array.
     */
    public function formatSections( array $aSections, $sFieldsType, $sCapability ) {

        $_aNewSectionArray = array();
        foreach( $aSections as $_sSectionID => $_aSection ) {

            if ( ! is_array( $_aSection ) ) { 
                continue; 
            }

            $_aSection = $this->formatSection( $_aSection, $sFieldsType, $sCapability, count( $_aNewSectionArray ) );
            if ( empty( $_aSection ) ) { 
                continue; 
            }
            
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
         * @return      array       The formatted section definition array.
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
                
            $aSection['order'] = $this->getAOrB(
                is_numeric( $aSection['order'] ),
                $aSection['order'],
                $iCountOfElements + 10
            );
            
            // 3.4.0+
            if ( empty( $aSection['collapsible'] ) ) {
                $aSection['collapsible'] = $aSection['collapsible'];
            } else {
                $aSection['collapsible'] = $this->getAsArray( $aSection['collapsible'] ) + array(
                    'title' => $aSection['title'],
                ) +  self::$_aStructure_CollapsibleArguments;
                $aSection['collapsible']['toggle_all_button'] = implode( ',', $this->getAsArray( $aSection['collapsible']['toggle_all_button'] ) );
            }
            
            // 3.5.2+ Accept a string value set to the 'class' element.
            $aSection['class'] = is_array( $aSection['class'] ) 
                ? $aSection['class']
                : $this->getAsArray( $aSection['class'] );
            
            return $aSection;
            
        }
        
        
    /**
     * Formats the stored fields definition array.
     * 
     * @since       3.0.0
     * @since       3.1.1       Added a parameter. Changed to return the formatted sections array.
     */
    public function formatFields( array $aFields, $sFieldsType, $sCapability ) {

        $_aNewFields = array();
        foreach( $aFields as $_sSectionID => $_aSubSectionsOrFields ) {
            
            if ( ! isset( $this->aSections[ $_sSectionID ] ) ) { 
                continue; 
            }

            $_aNewFields[ $_sSectionID ] = $this->getElementAsArray( $_aNewFields, $_sSectionID, array() );
            
            // If there are sub-section items,
            $_abSectionRepeatable = $this->aSections[ $_sSectionID ]['repeatable']; // a setting array or boolean or true/false
            
            // If sub-section exists or repeatable,
            if ( count( $this->getIntegerKeyElements( $_aSubSectionsOrFields ) ) || $_abSectionRepeatable ) { 
                                 
                foreach( $this->numerizeElements( $_aSubSectionsOrFields ) as $_iSectionIndex => $_aFields ) {
                                  
                    foreach( $_aFields as $_aField ) {
                        $_iCountElement = count( $this->getElementAsArray( $_aNewFields, array( $_sSectionID, $_iSectionIndex ), array() ) );
                        $_aField        = $this->formatField( $_aField, $sFieldsType, $sCapability, $_iCountElement, $_iSectionIndex, $_abSectionRepeatable, $this->oCaller );
                        if ( ! empty( $_aField ) ) {
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
                
                // Insert the formatted field definition array. The fields count is needed to set each order value.
                $_iCountElement = count( $this->getElementAsArray( $_aNewFields, $_sSectionID, array() ) ); 
                $_aField        = $this->formatField( $_aField, $sFieldsType, $sCapability, $_iCountElement, null, $_abSectionRepeatable, $this->oCaller );
                if ( ! empty( $_aField ) ) {
                    $_aNewFields[ $_sSectionID ][ $_aField['field_id'] ] = $_aField;
                }
                
            }
            uasort( $_aNewFields[ $_sSectionID ], array( $this, '_sortByOrder' ) ); 

        }
        
        // Sort by the order of the sections.
        $this->_sortFieldsBySectionsOrder( $_aNewFields, $this->aSections );
        return $_aNewFields;
        
    }
          
        /**
         * Sorts fields by section order.
         * 
         * Assumes the sections are formatted already.
         * 
         * @since       3.5.3
         * @return      void
         * @internal
         */
        private function _sortFieldsBySectionsOrder( array &$aFields, array $aSections ) {

            // Check if they are not empty as taxonomy fields don't have sections
            if ( empty( $aSections ) || empty( $aFields ) ) {
                return;
            }
            
            $_aSortedFields = array();
            foreach( $aSections as $_sSectionID => $_aSeciton ) { 
                if ( isset( $aFields[ $_sSectionID ] ) ) {
                    $_aSortedFields[ $_sSectionID ] = $aFields[ $_sSectionID ];
                }
            }
            $aFields = $_aSortedFields;
        
        }
        
        /**
         * Returns the formatted field array.
         * 
         * @since       3.0.0
         * @return      array|void       An array of formatted field definition array. If required keys are not set, nothing will be returned. 
         */
        protected function formatField( $aField, $sFieldsType, $sCapability, $iCountOfElements, $iSectionIndex, $bIsSectionRepeatable, $oCallerObject ) {
            
            if ( ! isset( $aField['field_id'], $aField['type'] ) ) { 
                return; 
            }
            
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
                $this->getElement(
                    $_aField,  // subject array
                    'tip', // key
                    is_array( $_aField['description'] )     // default
                        ? implode( '&#10;', $_aField['description'] ) 
                        : $_aField['description'] 
                )
            ) );
            $_aField['order']       = $this->getAOrB(
                is_numeric( $_aField['order'] ),
                $_aField['order'],
                $iCountOfElements + 10
            );
                        
            return $_aField;
            
        }
        
    /**
     * Returns the fields-definition array that the conditions have been applied.
     * 
     * @since       3.0.0
     * @since       3.5.3       Removed the parameters.
     */
    public function applyConditions() {
        return $this->getConditionedFields( 
            $this->getAsArray( $this->aFields ), 
            $this->getConditionedSections( $this->getAsArray( $this->aSections ) )
        );
    }
    
    /**
     * Returns a sections-array by applying the conditions.
     * 
     * @remark      Updates the `$aConditionedSections` array property.
     * @since       3.0.0
     * @since       3.5.3       Added a type hint and changed the default value to array from null.
     * @return      array       The conditioned sections array.
     */
    public function getConditionedSections( array $aSections=array() ) {
        
        $_aNewSections  = array();
        foreach( $aSections as $_sSectionID => $_aSection ) {
            $_aSection = $this->getConditionedSection( $_aSection );
            if ( ! empty( $_aSection ) ) {
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
         * @since       3.0.0
         * @since       3.5.3       Changed the return type to only array from array|null.
         * @return      array       The filtered section array.
         */
        protected function getConditionedSection( array $aSection ) {
            
            // Check capability. If the access level is not sufficient, skip.
            if ( ! current_user_can( $aSection['capability'] ) ) { 
                return array();
            }
            if ( ! $aSection['if'] ) { 
                return array(); 
            }
            
            return $aSection;
            
        }
    
    /**
     * Returns a fields-array by applying the conditions.
     * 
     * This will internally stores the aConditionedFields array into the property.
     * 
     * @remark      Assumes sections are conditioned already.
     * @since       3.0.0
     * @since       3.5.3       Added type hints to the parameters and removed default values.
     */
    public function getConditionedFields( array $aFields, array $aSections ) {

        // Drop keys of fields-array which do not exist in the sections-array. 
        // For this reasons, the sections-array should be conditioned first before applying this method.
        $aFields    = $this->castArrayContents( $aSections, $aFields );

        $_aNewFields = array();
        foreach( $aFields as $_sSectionID => $_aSubSectionOrFields ) {
            
            // This type check is important as the parsing field array is content-cast, which can set null value to elements.
            if ( ! is_array( $_aSubSectionOrFields ) ) { 
                continue; 
            }
            
            // @deprecated      3.5.3+      This check is redundant a s the parsing fields array is content-cast and the key that resulted from the model array always exists
            // if ( ! array_key_exists( $_sSectionID, $aSections ) ) { continue; }
            
            $this->_setConditionedFields( 
                $_aNewFields,   // by reference - gets updated in the method.
                $_aSubSectionOrFields, 
                $_sSectionID
            );
      
        }
                
        $this->aConditionedFields = $_aNewFields;
        return $_aNewFields;
        
    }     
        /**
         * Updates the given array of conditioned fields.
         * 
         * @since       3.5.3
         * @internal
         * @return      void
         */
        private function _setConditionedFields( array &$_aNewFields, $_aSubSectionOrFields, $_sSectionID ) {
            
            foreach( $_aSubSectionOrFields as $_sIndexOrFieldID => $_aSubSectionOrField ) {
                
                // If it is a sub-section array.
                if ( $this->isNumericInteger( $_sIndexOrFieldID ) ) {
                    $_sSubSectionIndex  = $_sIndexOrFieldID;
                    $_aFields           = $_aSubSectionOrField;
                    foreach( $_aFields as $_aField ) {
                        $_aField = $this->getConditionedField( $_aField );
                        if ( ! empty( $_aField ) ) {
                            $_aNewFields[ $_sSectionID ][ $_sSubSectionIndex ][ $_aField['field_id'] ] = $_aField;
                        }
                    }
                    continue;
                    
                }
                
                // Otherwise, insert the formatted field definition array.
                $_aField = $_aSubSectionOrField;
                $_aField = $this->getConditionedField( $_aField );
                if ( ! empty( $_aField ) ) {
                    $_aNewFields[ $_sSectionID ][ $_aField['field_id'] ] = $_aField;
                }
                
            }            
            
        }
        /**
         * Returns the field definition array by applying conditions. 
         * 
         * This method is intended to be extended to let the extended class customize the conditions.
         * 
         * @since       3.0.0
         * @since       3.5.3       Added a type hint to the parameter. Changed the return type to only array from null|array.
         * @return      array       The filtered field definition array.
         */
        protected function getConditionedField( array $aField ) {
            
            // Check capability. If the access level is not sufficient, skip.
            if ( ! current_user_can( $aField['capability'] ) ) { 
                return array();
            }
            if ( ! $aField['if'] ) { 
                return array(); 
            }
            return $aField;
            
        }
    
    
    /**
     * Updates the `aConditionedFields` property by adding dynamic elements from the given options array.
     * 
     * Dynamic elements are repeatable sections and sortable/repeatable fields. 
     * This method checks the structure of the given array 
     * and adds section elements to the `$aConditionedFields` property arrays.
     * 
     * @remark      Assumes sections and fields have already conditioned.
     * @since       3.0.0
     * @return      void
     * @todo        Display a warning when sections and fields are not conditioned.
     */
    public function setDynamicElements( $aOptions ) {
        
        $aOptions = $this->castArrayContents( $this->aConditionedSections, $aOptions );
        foreach( $aOptions as $_sSectionID => $_aSubSectionOrFields ) {
            
            $_aSubSection = $this->_getSubSectionFromOptions(   
                $_sSectionID,
                // Content-cast array elements (done with castArrayContents()) can be null so make sure to have it an array
                $this->getAsArray( 
                    $_aSubSectionOrFields   // a sub-section or fields extracted from the saved options array
                )  
            );

            if ( empty( $_aSubSection ) ) {
                continue;
            }
            
            // At this point, the associative keys will be gone but the element only consists of numeric keys.
            $this->aConditionedFields[ $_sSectionID ] = $_aSubSection;
            
        }

    }
        /**
         * Extracts sub-section from the given options array element.
         * 
         * The options array is the one stored in and retrieved from the database.
         * 
         * @internal
         * @since       3.5.3
         * @param       string      $_sSectionID                    The expected section ID.
         * @param       array       $_aSubSectionOrFields           sub-sections or fields extracted from the saved options array
         * @return      array       sub-sections array.
         */
        private function _getSubSectionFromOptions( $_sSectionID, array $_aSubSectionOrFields ) {
            
            $_aSubSection = array();
            $_iPrevIndex  = null;
            foreach( $_aSubSectionOrFields as $_isIndexOrFieldID => $_aSubSectionOrFieldOptions ) {
            
                // If it is not a sub-section array, skip.
                if ( ! $this->isNumericInteger( $_isIndexOrFieldID ) ) { 
                    continue; 
                }
                
                $_iIndex = $_isIndexOrFieldID;
                
                $_aSubSection[ $_iIndex ] = $this->_getSubSectionItemsFromOptions(
                    $_aSubSection, 
                    $_sSectionID, 
                    $_iIndex, 
                    $_iPrevIndex 
                );
   
                // Update the internal section index key
                foreach( $_aSubSection[ $_iIndex ] as &$_aField ) {
                    $_aField['_section_index'] = $_iIndex;
                }
                unset( $_aField ); // to be safe in PHP
                
                $_iPrevIndex = $_iIndex;
                
            }
            return $_aSubSection;
            
        }
            /**
             * Returns items belonging to the given sub-section from the options array.
             * 
             * @internal
             * @since       3.5.3
             * @param       array           $_aSubSection       the subsection array
             * @param       string          $_sSectionID        the section id
             * @param       integer         $_iIndex            the sub-section index
             * @param       integer|null    $_iPrevIndex
             * @return      array
             */
            private function _getSubSectionItemsFromOptions( array $_aSubSection, $_sSectionID, $_iIndex, $_iPrevIndex ) {
                
                $_aFields = isset( $this->aConditionedFields[ $_sSectionID ][ $_iIndex ] )
                    ? $this->aConditionedFields[ $_sSectionID ][ $_iIndex ]
                    : $this->getNonIntegerKeyElements( $this->aConditionedFields[ $_sSectionID ] );
                    
                // if empty, merge with the previous element.
                return ! empty( $_aFields )
                    ? $_aFields
                    : $this->getElementAsArray(
                        $_aSubSection,
                        $_iPrevIndex,
                        array()
                    );                     
                
            }
        
}