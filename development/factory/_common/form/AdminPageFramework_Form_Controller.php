<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods for the user to interact with the class object.
 * 
 * @package     AdminPageFramework/Common/Form/Controller
 * @since       3.7.0
 * @internal
 */
class AdminPageFramework_Form_Controller extends AdminPageFramework_Form_View {
   
    /**
     * Sets a given field errors.
     * @since       3.7.0
     * @return      void
     */
    public function setFieldErrors( $aErrors ) {
        $this->oFieldError->set( $aErrors );
    }
   
    /**
     * Checks whether a field error exists.
     * @return      boolean
     * @since       3.7.0
     */
    public function hasFieldError() {
        return $this->oFieldError->hasError();
    }
    
    /**
     * Checks if an error settings notice has been set.
     * 
     * This is used in the internal validation callback method to decide whether the system error or update notice should be added or not.
     * If this method yields true, the framework discards the system message and displays the user set notification message.
     * 
     * @since       3.7.0
     * @param       string      $sType If empty, the method will check if a message exists in all types. Otherwise, it checks the existence of a message of the specified type.
     * @return      boolean     True if a setting notice is set; otherwise, false.
     */
    public function hasSubmitNotice( $sType='' ) {
        return $this->oSubmitNotice->hasNotice( $sType );
    }
    
    /**
     * Sets the given message to be displayed in the next page load. 
     * 
     * This is used to inform users about the submitted input data, such as "Updated successfully." or "Problem occurred." etc. 
     * and normally used in validation callback methods.
     * 
     * <h4>Example</h4>
     * `
     * if ( ! $bVerified ) {
     *       $this->setFieldErrors( $aErrors );     
     *       $this->setSettingNotice( 'There was an error in your input.' );
     *       return $aOldPageOptions;
     * }
     * `
     * @since        3.7.0
     * @access       public
     * @param        string      $sMessage       the text message to be displayed.
     * @param        string      $sType          (optional) the type of the message, either "error" or "updated"  is used.
     * @param        array       $asAttributes   (optional) the tag attribute array applied to the message container HTML element. If a string is given, it is used as the ID attribute value.
     * @param        boolean     $bOverride      (optional) If true, only one message will be shown in the next page load. false: do not override when there is a message of the same id. true: override the previous one.
     * @return       void
     */
    public function setSubmitNotice( $sMessage, $sType='error', $asAttributes=array(), $bOverride=true ) {
        $this->oSubmitNotice->set(
            $sMessage, 
            $sType, 
            $asAttributes, 
            $bOverride
        );
    }
    
    /**
     * Adds the given section definition array to the form property.
     * 
     * @since       3.0.0
     * @since       3.7.0       Moved from `AminPageFramework_FormDefinition`.
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
     * @since       3.7.0       Moved from `AminPageFramework_FormDefinition`.
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
     * @since       3.7.0
     * @return      array
     */
    public function getResources( $sKey ) {
        return $this->getElement( self::$_aResources, $sKey );
    }
    
    /**
     * @since       3.8.5
     * @return      void
     */    
    public function unsetResources( $aKeys ) {
        $this->unsetDimensionalArrayElement( self::$_aResources, $aKeys );   
    }
    
    /**
     * Sets the resource items.
     * @return      void
     */
    public function setResources( $sKey, $mValue ) {
        return self::$_aResources[ $sKey ] = $mValue;
    }
    /**
     * @since       3.7.0
     * @return      void
     */
    public function addResource( $sKey, $sValue ) {
        self::$_aResources[ $sKey ][] = $sValue;
    }
    
    /**
     * Stores the target page slug which will be applied when no page slug is specified.
     * 
     * @since       3.0.0
     * @since       3.7.0      Accepts an array.
     * @since       3.7.0      Moved from `AminPageFramework_FormDefinition`.
     */
    protected $_asTargetSectionID = '_default';    
    
    /*
     * Adds the given field definition array to the form property.
     * 
     * @since       3.0.0
     * @since       3.7.0       Moved from `AminPageFramework_FormDefinition`.
     * @param       array|string            $asFieldset        A field definition array.
     * @return      array|string|null       If the passed field is set, it returns the set field array. If the target section id is set, the set section id is returned. Otherwise null.
     */    
    public function addField( $asFieldset ) {

        // If it is a target section, update the property and return.
        if ( ! $this->_isFieldsetDefinition( $asFieldset ) ) {
            $this->_asTargetSectionID = $this->_getTargetSectionID( $asFieldset );
            return $this->_asTargetSectionID;
        }

        $_aFieldset = $asFieldset;
        
        // Set the target section ID
        $this->_asTargetSectionID = $this->getElement(
            $_aFieldset,  // subject array
            'section_id', // key
            $this->_asTargetSectionID // default
        );                               

        // Required Keys - 3.8.0+ Now 'type' can be omitted.
        if ( ! isset( $_aFieldset[ 'field_id' ] ) ) { 
            return null; 
        }         
                
        // Update the fieldset property
        $this->_setFieldset( $_aFieldset );

        return $_aFieldset;
        
    }    
        /**
         * @return      void
         * @since       3.7.0
         */
        private function _setFieldset( array $aFieldset ) {
            
            // Pre-format
            $aFieldset = array( 
                    '_fields_type'    => $this->aArguments[ 'structure_type' ], // @todo deprecate this item.
                    '_structure_type' => $this->aArguments[ 'structure_type' ],
                )
                + $aFieldset
                + array( 
                    'section_id'      => $this->_asTargetSectionID,
                    'class_name'      => $this->aArguments[ 'caller_id' ], // for backward-compatibility
                )
                // + self::$_aStructure_Field // @deprecated 3.6.0 as the field will be formatted later anyway.
                ;         
        
            // Sanitize the IDs since they are used as a callback method name.
            $aFieldset[ 'field_id' ]     = $this->getIDSanitized( $aFieldset[ 'field_id' ] );
            $aFieldset[ 'section_id' ]   = $this->getIDSanitized( $aFieldset[ 'section_id' ] );
            
            // 3.7.0+ A section path (e.g. parent_section|nested_section|more_nested_section) will be stored in the key.
            // Also in the fieldsets dimension, a field path is stored in the key.
            $_aSectionPath    = $this->getAsArray( $aFieldset[ 'section_id' ] );
            $_sSectionPath    = implode( '|', $_aSectionPath );
            
            $_aFieldPath      = $this->getAsArray( $aFieldset[ 'field_id' ] );
            $_sFieldPath      = implode( '|', $_aFieldPath );
            
            $this->aFieldsets[ $_sSectionPath ][ $_sFieldPath ] = $aFieldset;
            
        }

        /**
         * Checks if the given item is a fieldset definition or not.
         * @since       3.7.0
         * @return      boolean
         */
        private function _isFieldsetDefinition( $asFieldset ) {
            
            if ( is_scalar( $asFieldset ) ) {
                return false;
            }
            // if ( ! is_array( $asFieldset ) ) {
                // return false;
            // }
            return $this->isAssociative( $asFieldset );
            
        }
        /**
         * @return      string
         */
        private function _getTargetSectionID( $asTargetSectionID ) {
            
            if ( is_scalar( $asTargetSectionID ) ) {
                return $asTargetSectionID;
            }
            return $asTargetSectionID;
            // return implode( '|', $asTargetSectionID );
            
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
     * @since       3.7.0       Moved from `AminPageFramework_FormDefinition`.
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
