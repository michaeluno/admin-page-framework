<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Deals with validating submitted options.
 * 
 * Provides validation methods which mainly deals with saved options 
 * including applying filters and merging options by tab, page, class etc.
 * 
 * @abstract
 * @since           3.5.3  
 * @extends         AdminPageFramework_Setting_ExPort
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 */
abstract class AdminPageFramework_Form_Model_Validation_Opiton extends AdminPageFramework_Form_Model_Export {
  
    /**
     * Applies validation filters to the submitted input data.
     * 
     * @since       2.0.0
     * @since       2.1.5       Added the `$sPressedFieldID` and `$sPressedInputID` parameters.
     * @since       3.0.0       Removed the `$sPressedFieldID` and `$sPressedInputID` parameters.
     * @since       3.5.0       Removed the $sTabSlug and $sPageSlug parameters as they are contained in $aSubmitInformation.
     * @since       3.5.3       Moved from `AdminPageFramework_Form_Model_Validation`.
     * @param       array       $aInput             The submitted form data merged with the default option values.
     * @param       array       $aInputRaw          The submitted form data.
     * @param       array       $aStoredData        The options data stored in the database.
     * @param       array       $aSubmitInformation Extra information of form submission such as pressed submit field ID.
     * @param       array       $aStatus            
     * @return      array       The filtered input array.
     */
    protected function _getFilteredOptions( $aInput, $aInputRaw, $aStoredData, $aSubmitInformation, array &$aStatus ) {

        $_aData = array(
            'sPageSlug'         => $aSubmitInformation['page_slug'],
            'sTabSlug'          => $aSubmitInformation['tab_slug'],            
            'aInput'            => $this->oUtil->getAsArray( $aInput ),
            'aStoredData'       => $aStoredData,
            'aStoredTabData'    => array(), // stores options of the belonging in-page tab.
            'aStoredDataWODynamicElements'  => $this->oUtil->addAndApplyFilter( 
                $this, 
                "validation_saved_options_without_dynamic_elements_{$this->oProp->sClassName}", 
                $this->oForm->dropRepeatableElements( $aStoredData ),
                $this
            ),               
            'aStoredTabDataWODynamicElements' => array(),
            'aEmbeddedDataWODynamicElements'  => array(),   // stores page meta box field options. This will be updated inside the validation methods.
            'aSubmitInformation'    => $aSubmitInformation, // 3.5.0+
        );
        
        // For each submitted element, tabs, and pages.
        $_aData = $this->_validateEachField( $_aData, $aInputRaw );
        $_aData = $this->_validateTabFields( $_aData );
        $_aData = $this->_validatePageFields( $_aData );
        
        // For the class             
        $_aInput = $this->_getValidatedData(
            "validation_{$this->oProp->sClassName}", 
            call_user_func_array( 
                array( $this, 'validate' ), // triggers __call()
                array( $_aData['aInput'], $_aData['aStoredData'], $this, $_aData['aSubmitInformation'] )
            ),    // 3.5.3+
            $_aData['aStoredData'],
            $_aData['aSubmitInformation'] // 3.5.0+
        );
        // Make sure it is an array as the value is modified through filters.
        $_aInput = $this->oUtil->getAsArray( $_aInput );

        $_aInput = $this->_getInputByUnset( $_aInput );
        
        // If everything fine, return the filtered input data. 
        $_bHasFieldErrors = $this->hasFieldError();
        if ( ! $_bHasFieldErrors ) {
            return $_aInput;
        }
        
        // Otherwise, set the last input data and throw an exception.
        $this->_setSettingNoticeAfterValidation( empty( $_aInput ) );
        $this->_setLastInput( $aInputRaw );
        $aStatus = $aStatus + array( 'field_errors' => $_bHasFieldErrors );  // 3.4.1+
        
        // Go to the catch clause.
        $_oException = new Exception( 'aReturn' );  // the property name to return from the catch clasue.
        $_oException->aReturn = $_aInput;
        throw $_oException;

    }    
        
        /**
         * Removes elements whose 'save' argument is false.
         * @return      array
         * @since       3.6.0
         */
        private function _getInputByUnset( array $aInput ) {
            
            $_sUnsetKey = '__unset_' . $this->oProp->sFieldsType;
            if ( ! isset( $_POST[ $_sUnsetKey ] ) ) {
                return $aInput;
            }
            
            $_aUnsetElements = array_unique( $_POST[ $_sUnsetKey ] );
            foreach( $_aUnsetElements as $_sFlatInputName ) {
                $_aDimensionalKeys = explode( '|', $_sFlatInputName );
                
                // The first element is the option key; the section or field dimensional keys follow.
                unset( $_aDimensionalKeys[ 0 ] );
                
                $this->oUtil->unsetDimensionalArrayElement( 
                    $aInput, 
                    $_aDimensionalKeys
                );
            }
            return $aInput;
            
        }        
        
        /**
         * Validates each field or section.
         * 
         * @since       3.0.2
         * @since       3.4.4       Stored all arguments in one argument of an array.
         */
        private function _validateEachField( array $aData, array $aInputToParse ) {
            
            foreach( $aInputToParse as $_sID => $_aSectionOrFields ) { // $_sID is either a section id or a field id
                
                // For each section
                if ( $this->oForm->isSection( $_sID ) ) {
                    
                    // If the parsing item does not belong to the current page, do not call the validation callback method.
                    if ( ! $this->_isValidSection( $_sID, $aData['sPageSlug'], $aData['sTabSlug'] ) ) {
                        continue;
                    }                             
                    
                    // Call the validation callback method.
                    foreach( $_aSectionOrFields as $_sFieldID => $_aFields ) { // For fields
                        $aData['aInput'][ $_sID ][ $_sFieldID ] = $this->_getValidatedData(
                            "validation_{$this->oProp->sClassName}_{$_sID}_{$_sFieldID}", 
                            $aData['aInput'][ $_sID ][ $_sFieldID ], 
                            $this->oUtil->getElement( $aData, array( 'aStoredData', $_sID, $_sFieldID ), null ),    // isset( $aData['aStoredData'][ $_sID ][ $_sFieldID ] ) ? $aData['aStoredData'][ $_sID ][ $_sFieldID ] : null,
                            $aData['aSubmitInformation']    // 3.5.0+
                        );
                    }
                    
                    // For an entire section - consider each field has a different individual capability. In that case, the key itself will not be sent,
                    // which causes data loss when a lower capability user submits the form but it was stored by a higher capability user.
                    // So merge the submitted array with the old stored array only for the first level.
                    $_aSectionInput = is_array( $aData['aInput'][ $_sID ] ) 
                        ? $aData['aInput'][ $_sID ] 
                        : array();
                    $_aSectionInput = $_aSectionInput 
                        + ( 
                            isset( $aData['aStoredDataWODynamicElements'][ $_sID ] ) && is_array( $aData['aStoredDataWODynamicElements'][ $_sID ] ) 
                                ? $aData['aStoredDataWODynamicElements'][ $_sID ] 
                                : array() 
                        );
                    
                    $aData['aInput'][ $_sID ] = $this->_getValidatedData(
                        "validation_{$this->oProp->sClassName}_{$_sID}", 
                        $_aSectionInput,
                        $this->oUtil->getElement( $aData, array( 'aStoredData', $_sID ), null ),    // isset( $aData['aStoredData'][ $_sID ] ) ? $aData['aStoredData'][ $_sID ] : null,
                        $aData['aSubmitInformation']
                    );     
                    
                    continue;
                    
                }
                                    
                // Check if the parsing item (the default section) belongs to the current page; if not, do not call the validation callback method.
                if ( ! $this->_isValidSection( '_default', $aData['sPageSlug'], $aData['sTabSlug'] ) ) {
                    continue;
                }  
                
                // For a field
                $aData['aInput'][ $_sID ] = $this->_getValidatedData(
                    "validation_{$this->oProp->sClassName}_{$_sID}",
                    $aData['aInput'][ $_sID ],
                    $this->oUtil->getElement( $aData, array( 'aStoredData', $_sID ), null ),    // isset( $aData['aStoredData'][ $_sID ] ) ? $aData['aStoredData'][ $_sID ] : null,
                    $aData['aSubmitInformation']
                );
                
            }
            
            return $aData;
            
        }   

            /**
             * Checks whether the given section belongs to the passed page and tab.
             * 
             * @since       3.4.4
             */
            private function _isValidSection( $sSectionID, $sPageSlug, $sTabSlug ) {
                
                if ( 
                    $sPageSlug
                    && isset( $this->oForm->aSections[ $sSectionID ][ 'page_slug' ] ) 
                    && $sPageSlug !== $this->oForm->aSections[ $sSectionID ][ 'page_slug' ] 
                ) {
                    return false;
                }
                if ( 
                    $sTabSlug 
                    && isset( $this->oForm->aSections[ $sSectionID ][ 'tab_slug' ] ) 
                    && $sTabSlug !== $this->oForm->aSections[ $sSectionID ][ 'tab_slug' ]
                ) {
                    return false;
                }     
                return true;
                
            }
        
        /**
         * Validates field options which belong to the given in-page tab.
         * 
         * @since       3.0.2
         */
        private function _validateTabFields( array $aData ) {
            
            if ( ! $aData['sTabSlug'] || ! $aData['sPageSlug'] ) { 
                return $aData; 
            }
                
            $aData['aStoredTabData']        = $this->oForm->getTabOptions( $aData['aStoredData'], $aData['sPageSlug'], $aData['sTabSlug'] ); // respects page meta box fields
            $aData['aStoredTabData']        = $this->oUtil->addAndApplyFilter( $this, "validation_saved_options_{$aData['sPageSlug']}_{$aData['sTabSlug']}", $aData['aStoredTabData'], $this );
            $_aOtherTabOptions  = $this->oForm->getOtherTabOptions( $aData['aStoredData'], $aData['sPageSlug'], $aData['sTabSlug'] );

            // This options data contain embedded options.
            $aData['aStoredTabDataWODynamicElements'] = $this->oForm->getTabOptions( $aData['aStoredDataWODynamicElements'], $aData['sPageSlug'], $aData['sTabSlug'] );
            $aData['aStoredTabDataWODynamicElements'] = $this->oUtil->addAndApplyFilter( 
                $this, 
                "validation_saved_options_without_dynamic_elements_{$aData['sPageSlug']}_{$aData['sTabSlug']}", 
                $aData['aStoredTabDataWODynamicElements'], 
                $this 
            );
            // Update the aStoredDataWODynamicElements element as it will be used in page validation method. Removed elements for in-page tabs should take effect.
            $aData['aStoredDataWODynamicElements'] = $aData['aStoredTabDataWODynamicElements'] + $aData['aStoredDataWODynamicElements'];
            
            // Consider each field has a different individual capability. In that case, the key itself will not be sent,
            // which causes data loss when a lower capability user submits the form but it was stored by a higher capability user.
            // So merge the submitted array with the old stored array only for the first level.     
            $_aTabOnlyOptionsWODynamicElements = $this->oForm->getTabOnlyOptions( $aData['aStoredTabDataWODynamicElements'], $aData['sPageSlug'], $aData['sTabSlug'] ); // excludes embedded elements such as page-meta-box fields
            $aData['aInput'] = $aData['aInput'] + $_aTabOnlyOptionsWODynamicElements;

            // Validate the input data.
            $aData['aInput'] = $this->_getValidatedData(
                "validation_{$aData['sPageSlug']}_{$aData['sTabSlug']}",
                $aData['aInput'],
                $aData['aStoredTabData'],
                $aData['aSubmitInformation']    // 3.5.0+
            );

            // Get embedded options. This is for page meta boxes.
            $aData['aEmbeddedDataWODynamicElements'] = $this->_getEmbeddedOptions( 
                $aData['aInput'], 
                $aData['aStoredTabDataWODynamicElements'],
                $_aTabOnlyOptionsWODynamicElements
            );     
  
            $aData['aInput'] = $aData['aInput'] + $_aOtherTabOptions;
            return $aData;
            
        }     
            
        /**
         * Validates field options which belong to the given page.
         * 
         * @since       3.0.2
         */
        private function _validatePageFields( array $aData ) {
       
            if ( ! $aData['sPageSlug'] ) { 
                return $aData['aInput']; 
            }

            // Prepare the saved page option array.
            $_aPageOptions      = $this->oForm->getPageOptions( $aData['aStoredData'], $aData['sPageSlug'] ); // this method respects injected elements into the page ( page meta box fields )     
            $_aPageOptions      = $this->oUtil->addAndApplyFilter( $this, "validation_saved_options_{$aData['sPageSlug']}", $_aPageOptions, $this );
            $_aOtherPageOptions = $this->oUtil->invertCastArrayContents( $this->oForm->getOtherPageOptions( $aData['aStoredData'], $aData['sPageSlug'] ), $_aPageOptions );
            
            $_aPageOptionsWODynamicElements = $this->oUtil->addAndApplyFilter( 
                $this, 
                "validation_saved_options_without_dynamic_elements_{$aData['sPageSlug']}", 
                $this->oForm->getPageOptions( $aData['aStoredDataWODynamicElements'], $aData['sPageSlug'] ),     // united with the in-page tab specific data in order to override the page-specific dynamic elements.
                $this 
            );                

            // Consider each field has a different individual capability. In that case, the key itself will not be sent,
            // which causes data loss when a lower capability user submits the form but it was stored by a higher capability user.
            // So merge the submitted array with the old stored array only for the first level.     
            $_aPageOnlyOptionsWODynamicElements = $this->oForm->getPageOnlyOptions( $_aPageOptionsWODynamicElements, $aData['sPageSlug'] ); // excludes embedded elements like page meta box fields
            $aData['aInput'] = $aData['aInput'] + $_aPageOnlyOptionsWODynamicElements;
            
            // Validate the input data.
            $aData['aInput'] = $this->_getValidatedData(
                "validation_{$aData['sPageSlug']}", 
                $aData['aInput'],                   // new values
                $_aPageOptions,                     // stored page options
                $aData['aSubmitInformation']        // submit information 3.5.0+
            );

            // If it's in a tab-page, drop the elements which belong to the tab so that arrayed-options will not be merged such as multiple select options.
            $_aPageOptions = $aData['sTabSlug'] && ! empty( $aData['aStoredTabData'] ) 
                ? $this->oUtil->invertCastArrayContents( $_aPageOptions, $aData['aStoredTabData'] ) 
                : ( ! $aData['sTabSlug'] // if the tab is not specified, do not merge the input array with the page options as the input array already includes the page options. This is for dynamic elements(repeatable sections).
                    ? array()
                    : $_aPageOptions
                );    
            
            // Get embedded options. This is for page meta boxes. Merging with the array defined earlier because in-page tabs also update this value.
            $_aEmbeddedOptionsWODynamicElements = $aData['aEmbeddedDataWODynamicElements'] 
                + $this->_getEmbeddedOptions( 
                    $aData['aInput'], 
                    $_aPageOptionsWODynamicElements,
                    $_aPageOnlyOptionsWODynamicElements 
                );                 
                            
            $aData['aInput'] = $aData['aInput'] + $this->oUtil->uniteArrays( 
                $_aPageOptions, // repeatable elements have been dropped
                $_aOtherPageOptions,    
                $_aEmbeddedOptionsWODynamicElements  // page meta box fields etc.
            );    
            
            return $aData;

        }     
        
            /**
             * Returns the embedded options.
             * 
             * Page meta boxes embeds additional option elements. This method extracts those data.
             * 
             * @since       3.4.4
             */
            private function _getEmbeddedOptions( array $aInput, array $aOptions, array $aPageSpecificOptions ) {
            
                $_aEmbeddedData = $this->oUtil->invertCastArrayContents(
                    $aOptions,
                    $aPageSpecificOptions
                );  
                return $this->oUtil->invertCastArrayContents(
                    $_aEmbeddedData,
                    $aInput
                );
                 
            }            

            /**
             * Returns the data applied validation filters.
             * 
             * This is just a shorter version of calling the addAndApplyFilter() method.
             * 
             * @since       3.4.4
             * @internal
             * @param       string      $sFilterName    The filter hook name.
             * @param       array       $aInput         The submitted form data.
             * @param       array       $aStoredData    The stored option.
             * @param       array       $aSubmitInfo    [3.5.0+] The form submit information such as the field ID of the pressed submit field.
             */
            private function _getValidatedData( $sFilterName, $aInput, $aStoredData, $aSubmitInfo=array() ) {
                return $this->oUtil->addAndApplyFilter( 
                    $this,          // caller
                    $sFilterName,   // hook name
                    $aInput,        // 1st argument
                    $aStoredData,   // 2nd argument
                    $this,          // 3rd argument
                    $aSubmitInfo    // 4th argument 3.5.0+
                );                    
            }  
            
    /**
     * Sets a setting notice after form validation.
     * 
     * @since       3.5.3
     * @internal
     * @return      void
     * @remark      Accessed from some of the parent/child classes.
     */
    protected function _setSettingNoticeAfterValidation( $bIsInputEmtpy ) {
     
        if ( $this->hasSettingNotice() ) {     
            return;
        }
        $this->setSettingNotice(  
            $this->oUtil->getAOrB( $bIsInputEmtpy, $this->oMsg->get( 'option_cleared' ), $this->oMsg->get( 'option_updated' ) ),
            $this->oUtil->getAOrB( $bIsInputEmtpy, 'error', 'updated' ),
            $this->oProp->sOptionKey, // the id
            false // do not override
        );
     
    }                
  
}