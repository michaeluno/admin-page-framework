<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to filter user form inputs.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.6.3
 * @internal
 */
class AdminPageFramework_Model__FormSubmission__Validator__Filter extends AdminPageFramework_Model__FormSubmission_Base {
    
    public $oFactory;
    public $aInputs = array();
    public $aRawInputs = array();
    public $aOptions = array();
    public $aSubmitInformation = array();
    
    private $_bHasFieldErrors = false;
    
    /**
     * Sets up properties.
     */
    public function __construct( /* $oFactory, array $aInputs, array $aRawInputs, array $aOptions */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->oFactory,
            $this->aInputs, 
            $this->aRawInputs, 
            $this->aOptions,
            $this->aSubmitInformation,
        );
        $this->oFactory             = $_aParameters[ 0 ];
        $this->aInputs              = $_aParameters[ 1 ];
        $this->aRawInputs           = $_aParameters[ 2 ];
        $this->aOptions             = $_aParameters[ 3 ];
        $this->aSubmitInformation   = $_aParameters[ 4 ];

    }
    
    /**
     * Returns an formatted definition array.
     * 
     * @return      array       The formatted definition array.
     */
    public function get() {
        return $this->_getFiltered(
            $this->aInputs, 
            $this->aRawInputs, 
            $this->aOptions,
            $this->aSubmitInformation
        );
    }
 
        /**
         * Applies validation filters to the submitted input data.
         * 
         * @since       2.0.0
         * @since       2.1.5       Added the `$sPressedFieldID` and `$sPressedInputID` parameters.
         * @since       3.0.0       Removed the `$sPressedFieldID` and `$sPressedInputID` parameters.
         * @since       3.5.0       Removed the $sTabSlug and $sPageSlug parameters as they are contained in $aSubmitInformation.
         * @since       3.5.3       Moved from `AdminPageFramework_Form_Model_Validation`.
         * @since       3.6.3       Moved from `AdminPageFramework_Form_Model_Validation_Opiton`. Changed the name from `_getFilteredOptions()`. Deprecated the status parameter.
         * @param       array       $aInputs             The submitted form data merged with the default option values.
         * @param       array       $aRawInputs          The submitted form data.
         * @param       array       $aStoredData        The options data stored in the database.
         * @param       array       $aSubmitInformation Extra information of form submission such as pressed submit field ID.
         * @return      array       The filtered input array.
         */
        private function _getFiltered( $aInputs, $aRawInputs, $aStoredData, $aSubmitInformation ) {

            $_aData = array(
                'sPageSlug'         => $aSubmitInformation[ 'page_slug' ],
                'sTabSlug'          => $aSubmitInformation[ 'tab_slug' ],
                'aInput'            => $this->getAsArray( $aInputs ),
                'aStoredData'       => $aStoredData,
                'aStoredTabData'    => array(), // stores options of the belonging in-page tab.
                'aStoredDataWODynamicElements'  => $this->addAndApplyFilter( 
                    $this->oFactory, 
                    "validation_saved_options_without_dynamic_elements_{$this->oFactory->oProp->sClassName}", 
                    $this->oFactory->oForm->dropRepeatableElements( $aStoredData ),
                    $this->oFactory
                ),               
                'aStoredTabDataWODynamicElements' => array(),
                'aEmbeddedDataWODynamicElements'  => array(),   // stores page meta box field options. This will be updated inside the validation methods.
                'aSubmitInformation'    => $aSubmitInformation, // 3.5.0+
            );
            
            // For each submitted element, tabs, and pages.
            $_aData = $this->_validateEachField( $_aData, $aRawInputs );
            $_aData = $this->_validateTabFields( $_aData );
            $_aData = $this->_validatePageFields( $_aData );
            
            // For the class             
            $_aInput = $this->_getValidatedData(
                "validation_{$this->oFactory->oProp->sClassName}", 
                call_user_func_array( 
                    array( $this->oFactory, 'validate' ), // triggers __call()
                    array( $_aData[ 'aInput' ], $_aData[ 'aStoredData' ], $this->oFactory, $_aData[ 'aSubmitInformation' ] )
                ),    // 3.5.3+
                $_aData[ 'aStoredData' ],
                $_aData[ 'aSubmitInformation' ] // 3.5.0+
            );
            // Make sure it is an array as the value is modified through filters.
            $_aInput = $this->getAsArray( $_aInput );

            $_aInput = $this->_getInputByUnset( $_aInput );
            
            // If everything fine, return the filtered input data. 
            $this->_bHasFieldErrors = $this->oFactory->hasFieldError();
            if ( ! $this->_bHasFieldErrors ) {
                return $_aInput;
            }
            
            // Otherwise, set the last input data and throw an exception.
            $this->_setSettingNoticeAfterValidation( empty( $_aInput ) );
            $this->oFactory->setLastInputs( $aRawInputs );
            
            add_filter(
                "options_update_status_{$this->oFactory->oProp->sClassName}", 
                array( $this, '_replyToSetStatus' )
            );            
                        
            // Go to the catch clause.
            $_oException = new Exception( 'aReturn' );  // the property name to return from the catch clause.
            $_oException->aReturn = $_aInput;
            throw $_oException;

        }    
            /**
             * @return      array
             * @since       3.6.3
             * @callback    filter      options_update_status_{class name}
             */
            public function _replyToSetStatus( $aStatus ) {
                return array( 
                    'field_errors' => $this->_bHasFieldErrors,
                ) + $aStatus;
            }
            
            /**
             * Removes elements whose 'save' argument is false.
             * @return      array
             * @since       3.6.0
             */
            private function _getInputByUnset( array $aInputs ) {
                
                $_sUnsetKey = '__unset_' . $this->oFactory->oProp->sStructureType;
                if ( ! isset( $_POST[ $_sUnsetKey ] ) ) {
                    return $aInputs;
                }
                
                $_aUnsetElements = array_unique( $_POST[ $_sUnsetKey ] );
                foreach( $_aUnsetElements as $_sFlatInputName ) {
                    $_aDimensionalKeys = explode( '|', $_sFlatInputName );
                    
                    // The first element is the option key; the section or field dimensional keys follow.
                    unset( $_aDimensionalKeys[ 0 ] );
                    
                    $this->unsetDimensionalArrayElement( 
                        $aInputs, 
                        $_aDimensionalKeys
                    );
                }
                return $aInputs;
                
            }        
            
            /**
             * Validates each field or section.
             * 
             * @since       3.0.2
             * @since       3.4.4       Stored all arguments in one argument of an array.
             */
            private function _validateEachField( array $aData, array $aInputsToParse ) {
                
                foreach( $aInputsToParse as $_sID => $_aSectionOrFields ) { // $_sID is either a section id or a field id
                    
                    // For each section
                    if ( $this->oFactory->oForm->isSection( $_sID ) ) {
                        
                        // If the parsing item does not belong to the current page, do not call the validation callback method.
                        if ( ! $this->_isValidSection( $_sID, $aData[ 'sPageSlug' ], $aData[ 'sTabSlug' ] ) ) {
                            continue;
                        }                             
                        
                        // Call the validation callback method.
                        foreach( $_aSectionOrFields as $_sFieldID => $_aFields ) { // For fields
                            $aData[ 'aInput' ][ $_sID ][ $_sFieldID ] = $this->_getValidatedData(
                                "validation_{$this->oFactory->oProp->sClassName}_{$_sID}_{$_sFieldID}", 
                                $aData[ 'aInput' ][ $_sID ][ $_sFieldID ], 
                                $this->getElement( $aData, array( 'aStoredData', $_sID, $_sFieldID ), null ),    // isset( $aData[ 'aStoredData' ][ $_sID ][ $_sFieldID ] ) ? $aData[ 'aStoredData' ][ $_sID ][ $_sFieldID ] : null,
                                $aData[ 'aSubmitInformation' ]    // 3.5.0+
                            );
                        }
                        
                        // For an entire section - consider each field has a different individual capability. In that case, the key itself will not be sent,
                        // which causes data loss when a lower capability user submits the form but it was stored by a higher capability user.
                        // So merge the submitted array with the old stored array only for the first level.
                        $_aSectionInput = is_array( $aData[ 'aInput' ][ $_sID ] ) 
                            ? $aData[ 'aInput' ][ $_sID ] 
                            : array();
                        $_aSectionInput = $_aSectionInput 
                            + ( 
                                isset( $aData[ 'aStoredDataWODynamicElements' ][ $_sID ] ) && is_array( $aData[ 'aStoredDataWODynamicElements' ][ $_sID ] ) 
                                    ? $aData[ 'aStoredDataWODynamicElements' ][ $_sID ] 
                                    : array() 
                            );
                        
                        $aData[ 'aInput' ][ $_sID ] = $this->_getValidatedData(
                            "validation_{$this->oFactory->oProp->sClassName}_{$_sID}", 
                            $_aSectionInput,
                            $this->getElement( $aData, array( 'aStoredData', $_sID ), null ),    // isset( $aData[ 'aStoredData' ][ $_sID ] ) ? $aData[ 'aStoredData' ][ $_sID ] : null,
                            $aData[ 'aSubmitInformation' ]
                        );     
                        
                        continue;
                        
                    }
                                        
                    // Check if the parsing item (the default section) belongs to the current page; if not, do not call the validation callback method.
                    if ( ! $this->_isValidSection( '_default', $aData[ 'sPageSlug' ], $aData[ 'sTabSlug' ] ) ) {
                        continue;
                    }  
                    
                    // For a field
                    $aData[ 'aInput' ][ $_sID ] = $this->_getValidatedData(
                        "validation_{$this->oFactory->oProp->sClassName}_{$_sID}",
                        $aData[ 'aInput' ][ $_sID ],
                        $this->getElement( $aData, array( 'aStoredData', $_sID ), null ),    // isset( $aData[ 'aStoredData' ][ $_sID ] ) ? $aData[ 'aStoredData' ][ $_sID ] : null,
                        $aData[ 'aSubmitInformation' ]
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
                        && isset( $this->oFactory->oForm->aSections[ $sSectionID ][ 'page_slug' ] ) 
                        && $sPageSlug !== $this->oFactory->oForm->aSections[ $sSectionID ][ 'page_slug' ] 
                    ) {
                        return false;
                    }
                    if ( 
                        $sTabSlug 
                        && isset( $this->oFactory->oForm->aSections[ $sSectionID ][ 'tab_slug' ] ) 
                        && $sTabSlug !== $this->oFactory->oForm->aSections[ $sSectionID ][ 'tab_slug' ]
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

                if ( ! $aData[ 'sTabSlug' ] || ! $aData[ 'sPageSlug' ] ) { 
                    return $aData; 
                }

                $aData[ 'aStoredTabData' ]        = $this->oFactory->oForm->getTabOptions( 
                    $aData[ 'aStoredData' ], 
                    $aData[ 'sPageSlug' ], 
                    $aData[ 'sTabSlug' ] 
                ); // respects page meta box fields
                $aData[ 'aStoredTabData' ]        = $this->addAndApplyFilter(
                    $this->oFactory, 
                    "validation_saved_options_{$aData[ 'sPageSlug' ]}_{$aData[ 'sTabSlug' ]}", 
                    $aData[ 'aStoredTabData' ], 
                    $this->oFactory 
                );
                $_aOtherTabOptions  = $this->oFactory->oForm->getOtherTabOptions( 
                    $aData[ 'aStoredData' ], 
                    $aData[ 'sPageSlug' ], 
                    $aData[ 'sTabSlug' ] 
                );

                // This options data contain embedded options.
                $aData[ 'aStoredTabDataWODynamicElements' ] = $this->oFactory->oForm->getTabOptions( 
                    $aData[ 'aStoredDataWODynamicElements' ], 
                    $aData[ 'sPageSlug' ], 
                    $aData[ 'sTabSlug' ] 
                );
                $aData[ 'aStoredTabDataWODynamicElements' ] = $this->addAndApplyFilter( 
                    $this->oFactory, 
                    "validation_saved_options_without_dynamic_elements_{$aData[ 'sPageSlug' ]}_{$aData[ 'sTabSlug' ]}", 
                    $aData[ 'aStoredTabDataWODynamicElements' ], 
                    $this->oFactory 
                );
                // Update the aStoredDataWODynamicElements element as it will be used in page validation method. Removed elements for in-page tabs should take effect.
                $aData[ 'aStoredDataWODynamicElements' ] = $aData[ 'aStoredTabDataWODynamicElements' ] + $aData[ 'aStoredDataWODynamicElements' ];
                
                // Consider each field has a different individual capability. In that case, the key itself will not be sent,
                // which causes data loss when a lower capability user submits the form but it was stored by a higher capability user.
                // So merge the submitted array with the old stored array only for the first level.     
                $_aTabOnlyOptionsWODynamicElements = $this->oFactory->oForm->getTabOnlyOptions( $aData[ 'aStoredTabDataWODynamicElements' ], $aData[ 'sPageSlug' ], $aData[ 'sTabSlug' ] ); // excludes embedded elements such as page-meta-box fields
                $aData[ 'aInput' ] = $aData[ 'aInput' ] + $_aTabOnlyOptionsWODynamicElements;

                // Validate the input data.
                $aData[ 'aInput' ] = $this->_getValidatedData(
                    "validation_{$aData[ 'sPageSlug' ]}_{$aData[ 'sTabSlug' ]}",
                    $aData[ 'aInput' ],
                    $aData[ 'aStoredTabData' ],
                    $aData[ 'aSubmitInformation' ]    // 3.5.0+
                );

                // Get embedded options. This is for page meta boxes.
                $aData[ 'aEmbeddedDataWODynamicElements' ] = $this->_getEmbeddedOptions( 
                    $aData[ 'aInput' ], 
                    $aData[ 'aStoredTabDataWODynamicElements' ],
                    $_aTabOnlyOptionsWODynamicElements
                );     
      
                $aData[ 'aInput' ] = $aData[ 'aInput' ] + $_aOtherTabOptions;
                return $aData;
                
            }     
                
            /**
             * Validates field options which belong to the given page.
             * 
             * @since       3.0.2
             */
            private function _validatePageFields( array $aData ) {
           
                if ( ! $aData[ 'sPageSlug' ] ) { 
                    return $aData[ 'aInput' ]; 
                }

                // Prepare the saved page option array.
                $_aPageOptions      = $this->oFactory->oForm->getPageOptions( $aData[ 'aStoredData' ], $aData[ 'sPageSlug' ] ); // this method respects injected elements into the page ( page meta box fields )     
                $_aPageOptions      = $this->addAndApplyFilter( $this->oFactory, "validation_saved_options_{$aData[ 'sPageSlug' ]}", $_aPageOptions, $this->oFactory );
                $_aOtherPageOptions = $this->invertCastArrayContents( $this->oFactory->oForm->getOtherPageOptions( $aData[ 'aStoredData' ], $aData[ 'sPageSlug' ] ), $_aPageOptions );
                
                $_aPageOptionsWODynamicElements = $this->addAndApplyFilter( 
                    $this->oFactory, 
                    "validation_saved_options_without_dynamic_elements_{$aData[ 'sPageSlug' ]}", 
                    $this->oFactory->oForm->getPageOptions( $aData[ 'aStoredDataWODynamicElements' ], $aData[ 'sPageSlug' ] ),     // united with the in-page tab specific data in order to override the page-specific dynamic elements.
                    $this->oFactory 
                );                

                // Consider each field has a different individual capability. In that case, the key itself will not be sent,
                // which causes data loss when a lower capability user submits the form but it was stored by a higher capability user.
                // So merge the submitted array with the old stored array only for the first level.     
                $_aPageOnlyOptionsWODynamicElements = $this->oFactory->oForm->getPageOnlyOptions( $_aPageOptionsWODynamicElements, $aData[ 'sPageSlug' ] ); // excludes embedded elements like page meta box fields
                $aData[ 'aInput' ] = $aData[ 'aInput' ] + $_aPageOnlyOptionsWODynamicElements;
                
                // Validate the input data.
                $aData[ 'aInput' ] = $this->_getValidatedData(
                    "validation_{$aData[ 'sPageSlug' ]}", 
                    $aData[ 'aInput' ],                   // new values
                    $_aPageOptions,                     // stored page options
                    $aData[ 'aSubmitInformation' ]        // submit information 3.5.0+
                );

                // If it's in a tab-page, drop the elements which belong to the tab so that arrayed-options will not be merged such as multiple select options.
                $_aPageOptions = $aData[ 'sTabSlug' ] && ! empty( $aData[ 'aStoredTabData' ] ) 
                    ? $this->invertCastArrayContents( $_aPageOptions, $aData[ 'aStoredTabData' ] ) 
                    : ( ! $aData[ 'sTabSlug' ] // if the tab is not specified, do not merge the input array with the page options as the input array already includes the page options. This is for dynamic elements(repeatable sections).
                        ? array()
                        : $_aPageOptions
                    );    
                
                // Get embedded options. This is for page meta boxes. Merging with the array defined earlier because in-page tabs also update this value.
                $_aEmbeddedOptionsWODynamicElements = $aData[ 'aEmbeddedDataWODynamicElements' ] 
                    + $this->_getEmbeddedOptions( 
                        $aData[ 'aInput' ], 
                        $_aPageOptionsWODynamicElements,
                        $_aPageOnlyOptionsWODynamicElements 
                    );                 
                                
                $aData[ 'aInput' ] = $aData[ 'aInput' ] + $this->uniteArrays( 
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
                private function _getEmbeddedOptions( array $aInputs, array $aOptions, array $aPageSpecificOptions ) {
                
                    $_aEmbeddedData = $this->invertCastArrayContents(
                        $aOptions,
                        $aPageSpecificOptions
                    );  
                    return $this->invertCastArrayContents(
                        $_aEmbeddedData,
                        $aInputs
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
                 * @param       array       $aInputs         The submitted form data.
                 * @param       array       $aStoredData    The stored option.
                 * @param       array       $aSubmitInfo    [3.5.0+] The form submit information such as the field ID of the pressed submit field.
                 */
                private function _getValidatedData( $sFilterName, $aInputs, $aStoredData, $aSubmitInfo=array() ) {
                    return $this->addAndApplyFilter( 
                        $this->oFactory,          // caller
                        $sFilterName,   // hook name
                        $aInputs,        // 1st argument
                        $aStoredData,   // 2nd argument
                        $this->oFactory,          // 3rd argument
                        $aSubmitInfo    // 4th argument 3.5.0+
                    );                    
                }  
     
}
