<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods that deal with field and section definition arrays specific to the ones that belong to generic pages created by the framework.
 * 
 * @package     AdminPageFramework
 * @subpackage  Property
 * @since       3.0.0
 * @internal
 */
class AdminPageFramework_FormElement_Page extends AdminPageFramework_FormElement {
    
    /**
     * Stores the default the page slug.
     * 
     * @since       3.0.0
     */
    protected $sDefaultPageSlug;
    
    /**
     * Stores the option key used for the options database table.
     * @since       3.0.0
     * @since       3.5.0       Declared as a default property.
     */
    protected $sOptionKey;
    
    /**
     * Stores the class name of the caller object.
     * @since       3.0.0
     * @since       3.5.0       Declared as a default property.
     */
    protected $sClassName;
    
    /**
     * Stores the currently loading page slug.
     * @since       3.0.0
     * @since       3.5.0       Declared as a default property.
     */
    protected $sCurrentPageSlug;

    /**
     * Stores the currently loading page slug.
     * @since       3.0.0
     * @since       3.5.0       Declared as a default property.
     */
    protected $sCurrentTabSlug;
    
    /**
     * Checks if the given page slug is added to a section.
     * 
     * @since       3.0.0
     */
    public function isPageAdded( $sPageSlug ) {

        foreach( $this->aSections as $_sSectionID => $_aSection ) {
            if ( 
                isset( $_aSection['page_slug'] ) 
                && $sPageSlug == $_aSection['page_slug'] 
            ) {
                return true;    
            }
        }
        return false;
        
    }
    
    /**
     * Returns the registered field that belongs to the given page by slug.
     * 
     * @since       3.0.0
     */
    public function getFieldsByPageSlug( $sPageSlug, $sTabSlug='' ) {
        
        return $this->castArrayContents( 
            $this->getSectionsByPageSlug( $sPageSlug, $sTabSlug ), 
            $this->aFields
        );
        
    }
    
    /**
     * Returns the registered sections that belong to the given page by slug.
     * @since       3.0.0.
     */
    public function getSectionsByPageSlug( $sPageSlug, $sTabSlug='' ) {
        
        $_aSections = array();
        foreach( $this->aSections as $_sSecitonID => $_aSection ) {
            
            if ( $sTabSlug && $_aSection['tab_slug'] != $sTabSlug ) { 
                continue; 
            }
            
            if ( $_aSection['page_slug'] != $sPageSlug ) { 
                continue; 
            }
            
            $_aSections[ $_sSecitonID ] = $_aSection;
                
        }
        
        uasort( $_aSections, array( $this, '_sortByOrder' ) ); 
        return $_aSections;
    }
    
    
    /**
     * Retrieves the page slug that the settings section belongs to.     
     * 
     * Used by fields type that require the page_slug key.
     * 
     * @since       2.0.0
     * @return      string|null
     * @internal
     */ 
    public function getPageSlugBySectionID( $sSectionID ) {
        return isset( $this->aSections[ $sSectionID ]['page_slug'] )
            ? $this->aSections[ $sSectionID ]['page_slug']
            : null;     
    }    
    
    /**
     * Sets the default page slug property.
     * 
     * @since       3.0.0
     */
    public function setDefaultPageSlug( $sDefaultPageSlug ) {
        $this->sDefaultPageSlug = $sDefaultPageSlug;
    }
    
    /**
     * Sets the option key.
     * 
     * Used by the field formatting method.
     * 
     * @since       3.0.0
     */
    public function setOptionKey( $sOptionKey ) {
        $this->sOptionKey = $sOptionKey;
    }
    
    /**
     * Sets the caller class name.
     * 
     * Used by the field formatting method.
     * 
     * @since       3.0.0
     */
    public function setCallerClassName( $sClassName ) {
        $this->sClassName = $sClassName;     
    }
    
    /**
     * Sets the current page slug.
     * 
     * Used by the conditioning method for sections.
     * 
     * @since       3.0.0
     */
    public function setCurrentPageSlug( $sCurrentPageSlug ) {
        $this->sCurrentPageSlug = $sCurrentPageSlug;
    }
    
    /**
     * Sets the current page slug.
     * 
     * Used by the conditioning method for sections.
     * 
     * @since 3.0.0
     */
    public function setCurrentTabSlug( $sCurrentTabSlug ) {
        $this->sCurrentTabSlug = $sCurrentTabSlug;
    }    
        
    /*
     * Extending the methods in the base class
     */
        
    /**
     * Returns the formatted section array.
     * 
     * @since       3.0.0
     */
    protected function formatSection( array $aSection, $sFieldsType, $sCapability, $iCountOfElements ) {
        
        $aSection = $aSection
        + array( 
            '_fields_type'  => $sFieldsType,
            'capability'    => $sCapability,
            'page_slug'     => $this->sDefaultPageSlug,
        );
        return parent::formatSection( $aSection, $sFieldsType, $sCapability, $iCountOfElements );
                
    }

    /**
     * Returns the formatted field array.
     * 
     * Before calling this method, $sOptionKey and $sClassName properties must be set.
     * 
     * @since       3.0.0
     * @since       3.4.1           Added the $oCallerObject parameter.
     * @return      array|void      An array of formatted field definition array. If required keys are not set, nothing will be returned. 
     */
    protected function formatField( $aField, $sFieldsType, $sCapability, $iCountOfElements, $iSectionIndex, $bIsSectionRepeatable, $oCallerObject ) {
        
        $_aField = parent::formatField( $aField, $sFieldsType, $sCapability, $iCountOfElements, $iSectionIndex, $bIsSectionRepeatable, $oCallerObject );
        
        if ( ! $_aField ) { 
            return; 
        }
        $_aField['option_key']      = $this->sOptionKey;
        $_aField['class_name']      = $this->sClassName;
        $_aField['page_slug']       = $this->getElement( $this->aSections, array( $_aField['section_id'], 'page_slug' ), null );
        $_aField['tab_slug']        = $this->getElement( $this->aSections, array( $_aField['section_id'], 'tab_slug' ), null );
        
        // used for the contextual help pane.
        $_aField['section_title']   = $this->getElement( $this->aSections, array( $_aField['section_id'], 'title' ), null );
        return $_aField;
        
    }
    
    /**
     * Applies the conditions to the given section.
     * 
     * Before calling this method, $sCurrentPageSlug and $sCurrentTabSlug properties must be set.
     * 
     * @remark      Assumes the given section definition array is already formatted.
     * @since       3.0.0
     * @since       3.5.3       The return type became only array from array|null as the return value will be passed to field conditioning method and it expects an array to be passed.
     * @return      array       The filtered sections array.
     */
    protected function getConditionedSection( array $aSection ) {

        if ( ! current_user_can( $aSection['capability'] ) ) {
            return array();
        }
        if ( ! $aSection['if'] ) { 
            return array();
        }
        if ( ! $aSection['page_slug'] ) { 
            return array();
        }
        if ( 'options.php' != $this->getPageNow() && $this->sCurrentPageSlug != $aSection['page_slug'] ) { 
            return array();
        }
        if ( ! $this->_isSectionOfCurrentTab( $aSection, $this->sCurrentPageSlug, $this->sCurrentTabSlug ) ) { 
            return array();
        }
        return $aSection;
        
    }
        /**
         * Checks if the given section belongs to the currently loading tab.
         * 
         * @since       2.0.0
         * @since       3.0.0       Moved from the setting class.
         * @remark      Assumes the given section definition array is already formatted.
         * @return      boolean     Returns true if the section belongs to the current tab page. Otherwise, false.
         */     
        private function _isSectionOfCurrentTab( array $aSection, $sCurrentPageSlug, $sCurrentTabSlug ) {
            
            // Make sure if it's in the loading page.
            if ( $aSection['page_slug'] != $sCurrentPageSlug  ) { 
                return false; 
            }

            // If the tab slug is not specified, it means that the user wants the section to be visible in the page regardless of tabs.
            // @deprecated 3.5.3+ As the passed section definition array is formatted and the key exists.
            // if ( ! isset( $aSection['tab_slug'] ) ) { 
                // return true; 
            // }
                                        
            // If the checking tab slug and the current loading tab slug is the same, it should be registered.
            return ( $aSection['tab_slug'] == $sCurrentTabSlug );
            
        }    
        
    /**
     * Retrieves the stored options of the given page slug.
     * 
     * The other pages' option data will not be contained in the returning array.
     * This is used to pass the old option array to the validation callback method.
     * 
     * @since 2.0.0
     * @since 3.0.0 Moved from the settings class.
     * @remark Consider the possibility that page meta box's values are included in the $aOptions array. So rather than storing the page-matching elements, drop the unmatched elements
     * so that the externally injected options will be respected.
     * @return array     the stored options of the given page slug. If not found, an empty array will be returned.
     */ 
    public function getPageOptions( $aOptions, $sPageSlug ) {

        $_aOtherPageOptions = $this->getOtherPageOptions( $aOptions, $sPageSlug );
        return $this->invertCastArrayContents( $aOptions, $_aOtherPageOptions );   
        
    }
    /**
     * Retrieves the saved options of the given page slug.  
     * 
     * This is a stricter version of the getPageOptions() method.
     * This method does not respect the injected elements by the page meta box class.
     * 
     * @since       3.0.0
     * @return      array
     */
    public function getPageOnlyOptions( $aOptions, $sPageSlug ) {

        $_aStoredOptionsOfThePage = array();
        foreach( $this->aFields as $_sSectionID => $_aFields ) {
            
            // Check the section
            if ( ! $this->_isThisSectionSetToThisPage( $_sSectionID, $sPageSlug ) ) {
                continue;
            }

            // At this point, the element belongs the given page slug as the section is of the given page slug's.
            foreach( $_aFields as $_sFieldID => $_aField ) {
                
                // If it's a sub-section array,
                if ( $this->isNumericInteger( $_sFieldID ) ) {
                    if ( array_key_exists( $_sSectionID, $aOptions ) ) {
                        $_aStoredOptionsOfThePage[ $_sSectionID ] = $aOptions[ $_sSectionID ];
                    }
                    continue;
                }    
                
                // At this point, a section is set.
                
                // @todo Examine whether this check can be removed 
                // as the section that hods this field is already checked above outside the loop.                
                if ( $sPageSlug !== $_aField['page_slug'] ) { 
                    continue; 
                }        
                
                if ( '_default' !== $_aField['section_id'] ) {
                    if ( array_key_exists( $_aField['section_id'], $aOptions ) ) {
                        $_aStoredOptionsOfThePage[ $_aField['section_id'] ] = $aOptions[ $_aField['section_id'] ];
                    }
                    continue;
                }
                
                // It does not have a section so set the field id as its key.
                if ( array_key_exists( $_aField['field_id'], $aOptions ) ) {
                    $_aStoredOptionsOfThePage[ $_aField['field_id'] ] = $aOptions[ $_aField['field_id'] ];
                }
                    
            }
        
        }
        return $_aStoredOptionsOfThePage; 
        
    }

    /**
     * Retrieves the stored options excluding the key of the given page slug.
     * 
     * This is used to merge the submitted form input data with the previously stored option data except the given page.
     * 
     * @since       2.0.0
     * @since       3.0.0     Moved from the settings class.
     * @return      array     An array storing the options excluding the key of the given page slug.
     */ 
    public function getOtherPageOptions( $aOptions, $sPageSlug ) {

        $_aStoredOptionsNotOfThePage = array();
        foreach( $this->aFields as $_sSectionID => $_aFields ) {
            
            // Check the section
            if ( $this->_isThisSectionSetToThisPage( $_sSectionID, $sPageSlug ) ) {
                continue;
            }
        
            // At this point, the parsing element does not belong to the given page slug as the section does not ( as it is checked above ).
            foreach( $_aFields as $_sFieldID => $_aField ) {

                // It's a sub-section array. 
                if ( $this->isNumericInteger( $_sFieldID ) ) {
                    continue; 
                } 
                
                // @todo Examine whether this check can be removed 
                // as the section that hods this field is already checked above outside the loop.
                if ( $sPageSlug === $_aField['page_slug'] ) { 
                    continue; 
                }
             
                // If a section is set,
                if ( '_default' !== $_aField['section_id'] ) {
                    if ( array_key_exists( $_aField['section_id'], $aOptions ) ) {
                        $_aStoredOptionsNotOfThePage[ $_aField['section_id'] ] = $aOptions[ $_aField['section_id'] ];
                    } 
                    continue;
                }
                // It does not have a section
                if ( array_key_exists( $_aField['field_id'], $aOptions ) ) {
                    $_aStoredOptionsNotOfThePage[ $_aField['field_id'] ] = $aOptions[ $_aField['field_id'] ];
                }
                    
            }
        
        }  

        return $_aStoredOptionsNotOfThePage;
        
    }
    
    /**
     * Returns the options excluding the currently specified tab's sections and their fields.
     * 
     * This is used to merge the submitted form data with the previously stored option data of the form elements 
     * that belong to the in-page tab of the given page.
     * 
     * @remark Note that this method will return the other pages' option elements as well.
     * 
     * @since       2.0.0
     * @since       3.0.0       The second parameter was changed to a tab slug. Moved from the settings class.

     * @param       array       $aOptions      the options array. Note that the options array structure are very similar to the aFields array. 
     * However, it does not have the `_default` section key.
     * @param       string      $sPageSlug     the page slug to check
     * @param       string      $sTabSlug      the tab slug to check
     * @return      array       the stored options excluding the currently specified tab's sections and their fields.
     * If not found, an empty array will be returned.
     */ 
    public function getOtherTabOptions( $aOptions, $sPageSlug, $sTabSlug ) {

        $_aStoredOptionsNotOfTheTab = array();
        foreach( $this->aFields as $_sSectionID => $_aSubSectionsOrFields ) {
                        
            // If the section is of the given page and the given tab, skip.
            if ( $this->_isThisSectionSetToThisTab( $_sSectionID, $sPageSlug, $sTabSlug ) ) {
                continue;
            }
            
            // At this point, the passed element belongs to the other tabs since the section of the given tab is skipped.
            foreach ( $_aSubSectionsOrFields as $_isSubSectionIndexOrFieldID => $_aSubSectionOrField  ) {
                
                // If it's a sub section
                if ( $this->isNumericInteger( $_isSubSectionIndexOrFieldID ) ) {
                            
                    // Store the entire section 
                    if ( array_key_exists( $_sSectionID, $aOptions ) ) {
                        $_aStoredOptionsNotOfTheTab[ $_sSectionID ] = $aOptions[ $_sSectionID ];
                    }
                    continue;
                    
                }
                
                // Otherwise,
                $_aField = $_aSubSectionOrField;
                
                // If a section is set,
                if ( $_aField['section_id'] !== '_default' ) {
                    if ( array_key_exists( $_aField['section_id'], $aOptions ) ) {
                        $_aStoredOptionsNotOfTheTab[ $_aField['section_id'] ] = $aOptions[ $_aField['section_id'] ];
                    }
                    continue;
                }
                // So it's a field
                if ( array_key_exists( $_aField['field_id'], $aOptions ) ) {
                    $_aStoredOptionsNotOfTheTab[ $_aField['field_id'] ] = $aOptions[ $_aField['field_id'] ];
                }

            }
        }
                
        return $_aStoredOptionsNotOfTheTab;
        
    }
    
    /**
     * Retrieves the stored options of the given tab slug.
     * 
     * @remark Consider the possibility that page meta box's values are included in the $aOptions array. So rather than storing the page-tab-matching elements, drop the unmatched elements
     * so that the externally injected options will be respected.
     * @since 3.0.0
     */
    public function getTabOptions( $aOptions, $sPageSlug, $sTabSlug='' ) {
        
        $_aOtherTabOptions = $this->getOtherTabOptions( $aOptions, $sPageSlug, $sTabSlug );
        return $this->invertCastArrayContents( $aOptions, $_aOtherTabOptions );     
    }
    
    /**
     * Retrieves the stored options of the given tab slug. 
     * 
     * This is stricter version of the `getTabOptions()` method.
     * This method does not respect injected elements such as page meta box fields.
     * 
     * @since       3.0.0
     * @return      array
     */
    public function getTabOnlyOptions( array $aOptions, $sPageSlug, $sTabSlug='' ) {
        
        $_aStoredOptionsOfTheTab = array();
        if ( ! $sTabSlug ) { 
            return $_aStoredOptionsOfTheTab; 
        }
        
        foreach( $this->aFields as $_sSectionID => $_aSubSectionsOrFields ) {
             
            // Check the section
            if ( ! $this->_isThisSectionSetToThisTab( $_sSectionID, $sPageSlug, $sTabSlug ) ) {
                continue;
            }
            
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
         * @return      void
         * @internal
         */
        private function _setTabOnlyOptions( array &$_aStoredOptionsOfTheTab, array $aOptions, array $_aSubSectionsOrFields, $_sSectionID ) {
            
            // At this point, the element is of the given page and the tab.     
            foreach( $_aSubSectionsOrFields as $_sFieldID => $_aField ) {
                                
                // if it's a sub-section array.
                if ( $this->isNumericInteger( $_sFieldID ) ) {
                    if ( array_key_exists( $_sSectionID, $aOptions ) ) {
                        $_aStoredOptionsOfTheTab[ $_sSectionID ] = $aOptions[ $_sSectionID ];
                    }
                    continue;
                }    
                
                // if a section is set,
                if ( '_default' !== $_aField['section_id'] ) {
                    if ( array_key_exists( $_aField['section_id'], $aOptions ) ) {
                        $_aStoredOptionsOfTheTab[ $_aField['section_id'] ] = $aOptions[ $_aField['section_id'] ];
                    }
                    continue;
                }
                
                // It does not have a section so set the field id as its key.
                if ( array_key_exists( $_aField['field_id'], $aOptions ) ) {
                    $_aStoredOptionsOfTheTab[ $_aField['field_id'] ] = $aOptions[ $_aField['field_id'] ];
                    continue;
                }

            }            
                      
        }
        
        
    /**
     * Checks if the given section added to the given page.
     * 
     * @since       3.5.3
     * @return      boolean
     */
    private function _isThisSectionSetToThisPage( $_sSectionID, $sPageSlug ) {
        
        if ( ! isset( $this->aSections[ $_sSectionID ]['page_slug'] ) ) {
            return false;
        }
        return ( 
            $sPageSlug === $this->aSections[ $_sSectionID ]['page_slug']
        );
    }
    
    /**
     * Checks if a form section is set for the given section ID, page slug, and tab slug.
     * 
     * @internal
     * @since       3.5.3
     * @return      boolean
     */
    private function _isThisSectionSetToThisTab( $_sSectionID, $sPageSlug, $sTabSlug ) {
        
        if ( ! $this->_isThisSectionSetToThisPage( $_sSectionID, $sPageSlug ) ) {
            return false;
        }
        if ( ! isset( $this->aSections[ $_sSectionID ]['tab_slug'] ) ) {
            return false;
        }
        return (
            $sTabSlug === $this->aSections[ $_sSectionID ]['tab_slug']
        );
        
    }

}