<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods for the user to interact with the class object.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       DEVVER
 */
class AdminPageFramework_Form_Controller extends AdminPageFramework_Form_View {
    
    /**
     * Adds the given section definition array to the form property.
     * 
     * @since       3.0.0
     * @since       DEVVER       Moved from `AminPageFramework_FormDefinition`.
     * @return      void
     */
    public function addSection( array $aSectionset ) {
        
        // $aSectionset                 = $aSectionset + AdminPageFramework_Form_Model___FormatSectionset::$aStructure;
        // Pre-format
        $aSectionset                 = $aSectionset + array(
            'section_id'    => null,
        );
        $aSectionset[ 'section_id' ] = $this->sanitizeSlug( $aSectionset[ 'section_id' ] );
        
        $this->aSectionsets[ $aSectionset[ 'section_id' ] ] = $aSectionset;    
        $this->aFieldsets[ $aSectionset[ 'section_id' ] ]   = $this->getElement(
            $this->aFieldsets,  // subject array
            $aSectionset[ 'section_id' ], // key
            array()      // default
        );                                
        
    }
    
    /**
     * Removes a section definition array from the property by the given section ID.
     * 
     * @since       3.0.0
     * @since       DEVVER       Moved from `AminPageFramework_FormDefinition`.
     */
    public function removeSection( $sSectionID ) {
        
        if ( '_default' === $sSectionID ){ 
            return; 
        }
        unset( 
            $this->aSectionsets[ $sSectionID ],
            $this->aFieldsets[ $sSectionID ]
        );
        
    }
    
    /**
     * Returns the added resource items.
     * @since       DEVVER
     * @return      array
     */
    public function getResources( $sKey ) {
        return $this->getElement( self::$_aResources, $sKey );
    }
    /**
     * Sets the resouce items.
     * @return      void
     */
    public function setResources( $sKey, $mValue ) {
        return self::$_aResources[ $sKey ] = $mValue;
    }
    /**
     * @since       DEVVER
     * @return      void
     */
    public function addResource() {
        
    }
    
    /**
     * Stores the target page slug which will be applied when no page slug is specified.
     * 
     * @since       3.0.0
     * @since       DEVVER       Moved from `AminPageFramework_FormDefinition`.
     */
    protected $_sTargetSectionID = '_default';    
    
    /*
     * Adds the given field definition array to the form property.
     * 
     * @since       3.0.0
     * @since       DEVVER       Moved from `AminPageFramework_FormDefinition`.
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

        // Pre-format
        $_aField = array( 
                '_fields_type'    => $this->aArguments[ 'structure_type' ], // @todo deprecate this item.
                '_structure_type' => $this->aArguments[ 'structure_type' ],
            )
            + $_aField
            + array( 
                'section_id' => $this->_sTargetSectionID 
            )
            // + self::$_aStructure_Field // @deprecated 3.6.0 as the field will be formatted later anyway.
            ;
        
        // Required Keys
        if ( ! isset( $_aField[ 'field_id' ], $_aField[ 'type' ] ) ) { 
            return null; 
        } 
            
        // Sanitize the IDs since they are used as a callback method name.
        $_aField[ 'field_id' ]     = $this->sanitizeSlug( $_aField[ 'field_id' ] );
        $_aField[ 'section_id' ]   = $this->sanitizeSlug( $_aField[ 'section_id' ] );     
        
        $this->aFieldsets[ $_aField[ 'section_id' ] ][ $_aField[ 'field_id' ] ] = $_aField;

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
     * @since       3.0.0
     * @since       DEVVER       Moved from `AminPageFramework_FormDefinition`.
     */     
    public function removeField( $sFieldID ) {
               
        foreach( $this->aFieldsets as $_sSectionID => $_aSubSectionsOrFields ) {

            if ( array_key_exists( $sFieldID, $_aSubSectionsOrFields ) ) {
                unset( $this->aFieldsets[ $_sSectionID ][ $sFieldID ] );
            }
            
            // Check sub-sections.
            foreach ( $_aSubSectionsOrFields as $_sIndexOrFieldID => $_aSubSectionOrFields ) {
                
                // if it's a sub-section
                if ( $this->isNumericInteger( $_sIndexOrFieldID ) ) {
                    if ( array_key_exists( $sFieldID, $_aSubSectionOrFields ) ) {
                        unset( $this->aFieldsets[ $_sSectionID ][ $_sIndexOrFieldID ] );
                    }
                    continue;
                }
                
            }
        }
        
    }
        
}