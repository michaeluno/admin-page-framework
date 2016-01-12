<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to build forms of the `admin_page` structure type.
 * 
 * The suffix of `_admin_page` represents the structure type of the form.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.7.0      
 * @extends     AdminPageFramework_Form
 * @internal
 */
class AdminPageFramework_Form_admin_page extends AdminPageFramework_Form {
            
    /**
     * Retrieves the stored options of the given page slug.
     * 
     * The other pages' option data will not be contained in the returning array.
     * This is used to pass the old option array to the validation callback method.
     * 
     * @since       2.0.0
     * @since       3.0.0       Moved from the settings class.
     * @since       3.7.0      Moved from `AdminPageFramework_FormDefinition_Page`.
     * @remark      Consider the possibility that page meta box's values are included in the `$aOptions` array. 
     * So rather than storing the page-matching elements, drop the unmatched elements
     * so that the externally injected options will be respected.
     * @return      array     the stored options of the given page slug. If not found, an empty array will be returned.
     */ 
    public function getPageOptions( $aOptions, $sPageSlug ) {
        $_aOtherPageOptions = $this->getOtherPageOptions( $aOptions, $sPageSlug );
        return $this->invertCastArrayContents( $aOptions, $_aOtherPageOptions );   
    }
    
    /**
     * Retrieves the saved options of the given page slug.  
     * 
     * This is a stricter version of the `getPageOptions()` method.
     * This method does not respect the injected elements by the page meta box class.
     * 
     * @since       3.0.0
     * @since       3.7.0      Moved from `AdminPageFramework_FormDefinition_Page`.
     * @return      array
     */
    public function getPageOnlyOptions( $aOptions, $sPageSlug ) {

        $_aStoredOptionsOfThePage = array();
        foreach( $this->aFieldsets as $_sSectionID => $_aSubSectionsOrFields ) {
            
            // Check the section
            if ( ! $this->_isThisSectionSetToThisPage( $_sSectionID, $sPageSlug ) ) {
                continue;
            }

            // At this point, the element belongs the given page slug as the section is of the given page slug's.
            $this->_setPageOnlyOptions( 
                $_aStoredOptionsOfThePage,  // by reference - gets updated in the method.
                $aOptions, 
                $_aSubSectionsOrFields, 
                $sPageSlug,
                $_sSectionID
            );
 
        }
        return $_aStoredOptionsOfThePage; 
        
    }
        /**
         * Updates the first parameter array holding page only options.
         * 
         * @since       3.5.3
         * @since       3.7.0      Moved from `AdminPageFramework_FormDefinition_Page`.
         * @return      void
         * @internal
         */
        private function _setPageOnlyOptions( array &$_aStoredOptionsOfThePage, array $aOptions, array $_aSubSectionsOrFields, $sPageSlug, $_sSectionID ) {
            foreach( $_aSubSectionsOrFields as $_sFieldID => $_aFieldset ) {
                
                // If it's a sub-section array,
                if ( $this->isNumericInteger( $_sFieldID ) ) {
                    
                    $this->_setOptionValue( 
                        $_aStoredOptionsOfThePage,
                        $_sSectionID, 
                        $aOptions
                    );
                                        
                    // @deprecated
                    // if ( array_key_exists( $_sSectionID, $aOptions ) ) {
                        // $_aStoredOptionsOfThePage[ $_sSectionID ] = $aOptions[ $_sSectionID ];
                    // }
                    continue;
                }    
                
                // At this point, a section is set.
                $_aFieldset = $_aFieldset + array(
                    'section_id' => null,
                    'field_id'   => null,
                    'page_slug'  => null,
                );
                // @todo Examine whether this check can be removed 
                // as the section that hods this field is already checked above outside this loop.
                if ( $sPageSlug !== $_aFieldset[ 'page_slug' ] ) { 
                    continue; 
                }        
                
                if ( '_default' !== $_aFieldset[ 'section_id' ] ) {
                    
                    $this->_setOptionValue( 
                        $_aStoredOptionsOfThePage,
                        $_aFieldset[ 'section_id' ], 
                        $aOptions
                    );

                    // @deprecated
                    // if ( array_key_exists( $_aFieldset[ 'section_id' ], $aOptions ) ) {
                        // $_aStoredOptionsOfThePage[ $_aFieldset[ 'section_id' ] ] = $aOptions[ $_aFieldset[ 'section_id' ] ];
                    // }
                    continue;
                }
                
                // It does not have a section so set the field id as its key.
                $this->_setOptionValue( 
                    $_aStoredOptionsOfThePage,
                    $_aFieldset[ 'field_id' ], 
                    $aOptions
                );           
                // @deprecated
                // if ( array_key_exists( $_aFieldset[ 'field_id' ], $aOptions ) ) {
                    // $_aStoredOptionsOfThePage[ $_aFieldset[ 'field_id' ] ] = $aOptions[ $_aFieldset[ 'field_id' ] ];
                // }
                    
            }            
        }

    /**
     * Retrieves the stored options excluding the key of the given page slug.
     * 
     * This is used to merge the submitted form input data with the previously stored option data except the given page.
     * 
     * @since       2.0.0
     * @since       3.0.0     Moved from the settings class.
     * @since       3.7.0      Moved from `AdminPageFramework_FormDefinition_Page`.
     * @return      array     An array storing the options excluding the key of the given page slug.
     */ 
    public function getOtherPageOptions( $aOptions, $sPageSlug ) {

        $_aStoredOptionsNotOfThePage = array();
        foreach( $this->aFieldsets as $_sSectionID => $_aSubSectionsOrFields ) {
            
            // Check the section
            if ( $this->_isThisSectionSetToThisPage( $_sSectionID, $sPageSlug ) ) {
                continue;
            }
        
            // At this point, the parsing element does not belong to the given page slug as the section does not ( as it is checked above ).
            $this->_setOtherPageOptions( 
                $_aStoredOptionsNotOfThePage, 
                $aOptions, 
                $_aSubSectionsOrFields, 
                $sPageSlug 
            );
        
        }  

        return $_aStoredOptionsNotOfThePage;
        
    }
        /**
         * Updates the first parameter array holding the other page options.
         * 
         * @since       3.5.3
         * @since       3.7.0      Moved from `AdminPageFramework_FormDefinition_Page`.
         * @return      void
         * @internal
         */
        private function _setOtherPageOptions( array &$_aStoredOptionsNotOfThePage, array $aOptions, array $_aSubSectionsOrFields, $sPageSlug ) {
            foreach( $_aSubSectionsOrFields as $_sFieldID => $_aFieldset ) {

                // It's a sub-section array. 
                if ( $this->isNumericInteger( $_sFieldID ) ) {
                    continue; 
                } 
                
                // @todo Examine whether this check can be removed 
                // as the section that holds this field is already checked above outside the loop.
                // if ( $sPageSlug === $_aFieldset[ 'page_slug' ] ) { 
                    // continue; 
                // }
             
                // If a section is set,
                if ( '_default' !== $_aFieldset[ 'section_id' ] ) {
                    
                    $this->_setOptionValue( 
                        $_aStoredOptionsNotOfThePage,
                        $_aFieldset[ 'section_id' ], 
                        $aOptions
                    );                             
                    
                    // @deprecated
                    // if ( array_key_exists( $_aFieldset[ 'section_id' ], $aOptions ) ) {
                        // $_aStoredOptionsNotOfThePage[ $_aFieldset[ 'section_id' ] ] = $aOptions[ $_aFieldset[ 'section_id' ] ];
                    // } 
                    continue;
                }
                
                // It does not have a section                
                $this->_setOptionValue( 
                    $_aStoredOptionsNotOfThePage,
                    $_aFieldset[ 'field_id' ], 
                    $aOptions
                );                           
                // @deprecated
                // if ( array_key_exists( $_aFieldset[ 'field_id' ], $aOptions ) ) {
                    // $_aStoredOptionsNotOfThePage[ $_aFieldset[ 'field_id' ] ] = $aOptions[ $_aFieldset[ 'field_id' ] ];
                // }
                    
            }            
        }
    /**
     * Returns the options excluding the currently specified tab's sections and their fields.
     * 
     * This is used to merge the submitted form data with the previously stored option data of the form elements 
     * that belong to the in-page tab of the given page.
     * 
     * @remark      Note that this method will return the other pages' option elements as well.
     * 
     * @since       2.0.0
     * @since       3.0.0       The second parameter was changed to a tab slug. Moved from the settings class.
     * @since       3.7.0      Moved from `AdminPageFramework_FormDefinition_Page`.
     * @param       array       $aOptions      the options array. Note that the options array structure are very similar to the aFieldsets array. 
     * However, it does not have the `_default` section key.
     * @param       string      $sPageSlug     the page slug to check
     * @param       string      $sTabSlug      the tab slug to check
     * @return      array       the stored options excluding the currently specified tab's sections and their fields.
     * If not found, an empty array will be returned.
     */ 
    public function getOtherTabOptions( $aOptions, $sPageSlug, $sTabSlug ) {

        $_aStoredOptionsNotOfTheTab = array();
        foreach( $this->aFieldsets as $_sSectionPath => $_aSubSectionsOrFields ) {
                        
            // If the section is of the given page and the given tab, skip.
            if ( $this->_isThisSectionSetToThisTab( $_sSectionPath, $sPageSlug, $sTabSlug ) ) {
                continue;
            }
            
            // At this point, the passed element belongs to the other tabs since the section of the given tab is skipped.
            $this->_setOtherTabOptions( 
                $_aStoredOptionsNotOfTheTab,
                $aOptions, 
                $_aSubSectionsOrFields, 
                $_sSectionPath
            );
 
        }
                
        return $_aStoredOptionsNotOfTheTab;
        
    }
        /**
         * Updates the first parameter array holding the other tab options.
         * 
         * @since       3.5.3
         * @since       3.7.0      Moved from `AdminPageFramework_FormDefinition_Page`.
         * @return      void
         * @internal
         */
        private function _setOtherTabOptions( array &$_aStoredOptionsNotOfTheTab, array $aOptions, array $_aSubSectionsOrFields, $sSectionPath ) {
            
            // At this point, the passed element belongs to the other tabs since the section of the given tab is skipped.
            foreach ( $_aSubSectionsOrFields as $_isSubSectionIndexOrFieldID => $_aSubSectionOrField  ) {
                
                // If it's a sub section
                if ( $this->isNumericInteger( $_isSubSectionIndexOrFieldID ) ) {
                            
                    // Store the entire section 
                    $this->_setOptionValue( 
                        $_aStoredOptionsNotOfTheTab,
                        $sSectionPath, 
                        $aOptions
                    );               
                    // @deprecated
                    // if ( array_key_exists( $sSectionPath, $aOptions ) ) {
                        // $_aStoredOptionsNotOfTheTab[ $sSectionPath ] = $aOptions[ $sSectionPath ];
                    // }
                    continue;
                  
                }
                
                // Otherwise,
                $_aFieldset = $_aSubSectionOrField;
                
                // If a section is set,
                if ( $_aFieldset[ 'section_id' ] !== '_default' ) {
                    $this->_setOptionValue( 
                        $_aStoredOptionsNotOfTheTab,
                        $_aFieldset[ 'section_id' ], 
                        $aOptions
                    );
                    // @deprecated
                    // if ( array_key_exists( $_aFieldset[ 'section_id' ], $aOptions ) ) {
                        // $_aStoredOptionsNotOfTheTab[ $_aFieldset[ 'section_id' ] ] = $aOptions[ $_aFieldset[ 'section_id' ] ];
                    // }
                    continue;
                }
                // So it's a field
                $this->_setOptionValue( 
                    $_aStoredOptionsNotOfTheTab,
                    $_aFieldset[ 'field_id' ], 
                    $aOptions
                );                                           
                // if ( array_key_exists( $_aFieldset[ 'field_id' ], $aOptions ) ) {
                    // $_aStoredOptionsNotOfTheTab[ $_aFieldset[ 'field_id' ] ] = $aOptions[ $_aFieldset[ 'field_id' ] ];
                // }

            }            
            
        }
    /**
     * Retrieves the stored options of the given tab slug.
     * 
     * @remark      Consider the possibility that the values of page meta boxes are included in the `$aOptions` array. 
     * So rather than storing the page-tab-matching elements, drop the unmatched elements
     * so that the externally injected options will be respected.
     * @since       3.0.0
     * @since       3.7.0      Moved from `AdminPageFramework_FormDefinition_Page`.
     * @return      array
     */
    public function getTabOptions( $aOptions, $sPageSlug, $sTabSlug='' ) {     
                
        $_aOtherTabOptions = $this->getOtherTabOptions( $aOptions, $sPageSlug, $sTabSlug );
        $_aTabOptions      = $this->invertCastArrayContents( $aOptions, $_aOtherTabOptions );       
        return $_aTabOptions;
        
    }
    
    /**
     * Retrieves the stored options of the given tab slug. 
     * 
     * This is stricter version of the `getTabOptions()` method.
     * This method does not respect injected elements such as page meta box fields.
     * 
     * @since       3.0.0
     * @since       3.7.0      Moved from `AdminPageFramework_FormDefinition_Page`.
     * @return      array
     */
    public function getTabOnlyOptions( array $aOptions, $sPageSlug, $sTabSlug='' ) {
        
        $_aStoredOptionsOfTheTab = array();
        if ( ! $sTabSlug ) { 
            return $_aStoredOptionsOfTheTab; 
        }
        
        foreach( $this->aFieldsets as $_sSectionID => $_aSubSectionsOrFields ) {
             
            // Check the section
            if ( ! $this->_isThisSectionSetToThisTab( $_sSectionID, $sPageSlug, $sTabSlug ) ) {
                continue;
            }
            
            // At this point, the parsing element is of the given page and the tab.
            $this->_setTabOnlyOptions( 
                $_aStoredOptionsOfTheTab, // by reference, gets updated in the method
                $aOptions,
                $_aSubSectionsOrFields, 
                $_sSectionID 
            );
        
        }     
        return $_aStoredOptionsOfTheTab; 
        
    }
        /**
         * Updates the first parameter array holding tab only options.
         * 
         * @since       3.5.3
         * @since       3.7.0      Moved from `AdminPageFramework_FormDefinition_Page`.
         * @return      void
         * @internal
         */
        private function _setTabOnlyOptions( array &$_aStoredOptionsOfTheTab, array $aOptions, array $_aSubSectionsOrFields, $_sSectionID ) {
            
            foreach( $_aSubSectionsOrFields as $_sFieldID => $_aFieldset ) {
                                
                // if it's a sub-section array.
                if ( $this->isNumericInteger( $_sFieldID ) ) {
                    $this->_setOptionValue( 
                        $_aStoredOptionsOfTheTab,
                        $_sSectionID, 
                        $aOptions
                    );
                    // @deprecated
                    // if ( array_key_exists( $_sSectionID, $aOptions ) ) {
                        // $_aStoredOptionsOfTheTab[ $_sSectionID ] = $aOptions[ $_sSectionID ];
                    // }
                    continue;
                }    
                
                // if a section is set,
                if ( '_default' !== $_aFieldset[ 'section_id' ] ) {
                    
                    $this->_setOptionValue(
                        $_aStoredOptionsOfTheTab, // by reference
                        $_aFieldset[ 'section_id' ],
                        $aOptions
                    );
                    // @deprecated
                    // if ( array_key_exists( $_aFieldset[ 'section_id' ], $aOptions ) ) {
                        // $_aStoredOptionsOfTheTab[ $_aFieldset[ 'section_id' ] ] = $aOptions[ $_aFieldset[ 'section_id' ] ];
                    // }
                    continue;
                }
                
                // It does not have a section so set the field id as its key.
                $this->_setOptionValue( 
                    $_aStoredOptionsOfTheTab,
                    $_aFieldset[ 'field_id' ], 
                    $aOptions
                );                
                // @deprecated      3.7.0
                // if ( array_key_exists( $_aFieldset[ 'field_id' ], $aOptions ) ) {
                    // $_aStoredOptionsOfTheTab[ $_aFieldset[ 'field_id' ] ] = $aOptions[ $_aFieldset[ 'field_id' ] ];
                    // continue;
                // }

            }            
                      
        }
        
        
    /**
     * Checks if the given section added to the given page.
     * 
     * @since       3.5.3
     * @since       3.7.0      Moved from `AdminPageFramework_FormDefinition_Page`.
     * @return      boolean
     */
    private function _isThisSectionSetToThisPage( $sSectionPath, $sPageSlug ) {
        
        if ( ! isset( $this->aSectionsets[ $sSectionPath ][ 'page_slug' ] ) ) {
            return false;
        }
        return ( 
            $sPageSlug === $this->aSectionsets[ $sSectionPath ][ 'page_slug' ]
        );
    }
    
    /**
     * Checks if a form section is set for the given section ID, page slug, and tab slug.
     * 
     * @internal
     * @since       3.5.3
     * @since       3.7.0      Moved from `AdminPageFramework_FormDefinition_Page`.
     * @return      boolean
     */
    private function _isThisSectionSetToThisTab( $sSectionPath, $sPageSlug, $sTabSlug ) {
        
        if ( ! $this->_isThisSectionSetToThisPage( $sSectionPath, $sPageSlug ) ) {
            return false;
        }
        if ( ! isset( $this->aSectionsets[ $sSectionPath ][ 'tab_slug' ] ) ) {
            return false;
        }
        return (
            $sTabSlug === $this->aSectionsets[ $sSectionPath ][ 'tab_slug' ]
        );
        
    }

    
    /**
     * Sets a value of a section of the given option array to the subject array.
     * @since       3.7.0
     * @return      void
     */
    private function _setOptionValue( &$aSubject, $asDimensionalPath, $aOptions ) {
        $_aDimensionalPath = $this->getAsArray( $asDimensionalPath );
        $_mValue     = $this->getElement(
            $aOptions,
            $_aDimensionalPath,    // as of 3.7.0, it can be an array or string
            null
        );
        if ( isset( $_mValue ) ) {
            $this->setMultiDimensionalArray( 
                $aSubject,
                $_aDimensionalPath,
                $_mValue
            );
        }                                                       
    }    
    
}
