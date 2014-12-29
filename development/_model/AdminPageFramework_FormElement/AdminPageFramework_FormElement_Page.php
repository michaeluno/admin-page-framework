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
 * @package AdminPageFramework
 * @subpackage Property
 * @since 3.0.0
 * @internal
 */
class AdminPageFramework_FormElement_Page extends AdminPageFramework_FormElement {
    
    /**
     * Stores the default the page slug.
     * 
     * @since 3.0.0
     */
    protected $sDefaultPageSlug;
    
    /**
     * Checks if the given page slug is added to a section.
     * 
     * @since 3.0.0
     */
    public function isPageAdded( $sPageSlug ) {

        foreach( $this->aSections as $_sSectionID => $_aSection ) {
            if ( isset( $_aSection['page_slug'] ) && $sPageSlug == $_aSection['page_slug'] ) {
                return true;    
            }
        }
        return false;
        
    }
    
    /**
     * Returns the registered field that belongs to the given page by slug.
     * 
     * @since 3.0.0
     */
    public function getFieldsByPageSlug( $sPageSlug, $sTabSlug='' ) {
        
        return $this->castArrayContents( $this->getSectionsByPageSlug( $sPageSlug, $sTabSlug ), $this->aFields );
        
    }
    
    /**
     * Returns the registered sections that belong to the given page by slug.
     * @since 3.0.0.
     */
    public function getSectionsByPageSlug( $sPageSlug, $sTabSlug='' ) {
        
        $_aSections = array();
        foreach( $this->aSections as $_sSecitonID => $_aSection ) {
            
            if ( $sTabSlug && $_aSection['tab_slug'] != $sTabSlug ) { continue; }
            
            if ( $_aSection['page_slug'] != $sPageSlug ) { continue; }
            
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
     * @since 2.0.0
     * @return string|null
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
     * @since 3.0.0
     */
    public function setDefaultPageSlug( $sDefaultPageSlug ) {
        $this->sDefaultPageSlug = $sDefaultPageSlug;
    }
    
    /**
     * Sets the option key.
     * 
     * Used by the field formatting method.
     * 
     * @since 3.0.0
     */
    public function setOptionKey( $sOptionKey ) {
        $this->sOptionKey = $sOptionKey;
    }
    
    /**
     * Sets the caller class name.
     * 
     * Used by the field formatting method.
     * 
     * @since 3.0.0
     */
    public function setCallerClassName( $sClassName ) {
        $this->sClassName = $sClassName;     
    }
    
    /**
     * Sets the current page slug.
     * 
     * Used by the conditioning method for sections.
     * 
     * @since 3.0.0
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
     * @since 3.0.0
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
     * @since       3.4.1       Added the $oCallerObject parameter.
     */
    protected function formatField( $aField, $sFieldsType, $sCapability, $iCountOfElements, $iSectionIndex, $bIsSectionRepeatable, $oCallerObject ) {
        
        $_aField = parent::formatField( $aField, $sFieldsType, $sCapability, $iCountOfElements, $iSectionIndex, $bIsSectionRepeatable, $oCallerObject );
        
        if ( ! $_aField ) { return; }
        $_aField['option_key']      = $this->sOptionKey;
        $_aField['class_name']      = $this->sClassName;
        $_aField['page_slug']       = isset( $this->aSections[ $_aField['section_id'] ]['page_slug'] ) ? $this->aSections[ $_aField['section_id'] ]['page_slug'] : null;
        $_aField['tab_slug']        = isset( $this->aSections[ $_aField['section_id'] ]['tab_slug'] ) ? $this->aSections[ $_aField['section_id'] ]['tab_slug'] : null;
        $_aField['section_title']   = isset( $this->aSections[ $_aField['section_id'] ]['title'] ) ? $this->aSections[ $_aField['section_id'] ]['title'] : null; // used for the contextual help pane.
        return $_aField;
        
    }
    
    /**
     * Applies the conditions to the given section.
     * 
     * Before calling this method, $sCurrentPageSlug and $sCurrentTabSlug properties must be set.
     * 
     * @since 3.0.0
     */
    protected function getConditionedSection( array $aSection ) {

        // Check the conditions
        if ( ! current_user_can( $aSection['capability'] ) ) return;
        if ( ! $aSection['if'] ) return;    
        if ( ! $aSection['page_slug'] ) return;    
        if ( 'options.php' != $this->getPageNow() && $this->sCurrentPageSlug != $aSection['page_slug'] ) return;    
        if ( ! $this->_isSectionOfCurrentTab( $aSection, $this->sCurrentPageSlug, $this->sCurrentTabSlug ) ) return;
        return $aSection;
        
    }
        /**
         * Checks if the given section belongs to the currently loading tab.
         * 
         * @since 2.0.0
         * @since 3.0.0 Moved from the setting class.
         * @return boolean Returns true if the section belongs to the current tab page. Otherwise, false.
         * @deprecated
         */     
        private function _isSectionOfCurrentTab( $aSection, $sCurrentPageSlug, $sCurrentTabSlug ) {
            
            // Make sure if it's in the loading page.
            if ( $aSection['page_slug'] != $sCurrentPageSlug  ) { return false; }

            // If the tab slug is not specified, it means that the user wants the section to be visible in the page regardless of tabs.
            if ( ! isset( $aSection['tab_slug'] ) ) { return true; }
                                        
            // If the checking tab slug and the current loading tab slug is the same, it should be registered.
            if ( $aSection['tab_slug'] == $sCurrentTabSlug )  { return true; }
            
            // Otherwise, false.
            return false;
            
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
     * This is a strict version of the getPageOptions() method and this does not respect the injected elements by the page meta box class.
     * 
     * @since 3.0.0
     */
    public function getPageOnlyOptions( $aOptions, $sPageSlug ) {

        $_aStoredOptionsOfThePage = array();
        foreach( $this->aFields as $_sSectionID => $_aFields ) {
            
            // Check the section
            if ( isset( $this->aSections[ $_sSectionID ]['page_slug'] ) && $this->aSections[ $_sSectionID ]['page_slug'] != $sPageSlug ) continue;

            // At this point, the element belongs the given page slug as the section is of the given page slug's.
            foreach( $_aFields as $_sFieldID => $_aField ) {
            
                if ( ! isset( $_aField['page_slug'] ) || $_aField['page_slug'] != $sPageSlug ) { continue; }
                
                // If it's a sub-section array,
                if ( is_numeric( $_sFieldID ) && is_int( $_sFieldID + 0 ) ) {
                    if ( array_key_exists( $_sSectionID, $aOptions ) ) {
                        $_aStoredOptionsOfThePage[ $_sSectionID ] = $aOptions[ $_sSectionID ];
                    }
                    continue;
                }    
                
                // If a section is set,
                if ( isset( $_aField['section_id'] ) && $_aField['section_id'] != '_default' ) {
                    if ( array_key_exists( $_aField['section_id'], $aOptions ) )
                        $_aStoredOptionsOfThePage[ $_aField['section_id'] ] = $aOptions[ $_aField['section_id'] ];
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
     * @since 2.0.0
     * @since 3.0.0 Moved from the settings class.
     * @return array     the array storing the options excluding the key of the given page slug. 
     */ 
    public function getOtherPageOptions( $aOptions, $sPageSlug ) {

        $_aStoredOptionsNotOfThePage = array();
        foreach( $this->aFields as $_sSectionID => $_aFields ) {
            
            // Check the section
            if ( 
                isset( $this->aSections[ $_sSectionID ]['page_slug'] ) 
                && $this->aSections[ $_sSectionID ]['page_slug'] == $sPageSlug 
            ) { 
                continue; 
            }
        
            // At this point, the parsing element does not belong to the given page slug as the section does not ( as it is checked above ).
            foreach( $_aFields as $_sFieldID => $_aField ) {
                
                if ( ! isset( $_aField['page_slug'] ) ) { 
                    continue; 
                }
                if ( $_aField['page_slug'] == $sPageSlug ) { 
                    continue; 
                }
                // it's a sub-section array. 
                if ( is_numeric( $_sFieldID ) && is_int( $_sFieldID + 0 ) ) { 
                    continue; 
                } 
                
                // If a section is set,
                if ( isset( $_aField['section_id'] ) && $_aField['section_id'] != '_default' ) {
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

     * @param       array       $aOptions      the options array. Note that the options array structure are very similar to the aFields array. However, it does not have the '_default' section key.
     * @param       string      $sPageSlug     the page slug to check
     * @param       string      $sTabSlug      the tab slug to check
     * @return      array       the stored options excluding the currently specified tab's sections and their fields.
     *      If not found, an empty array will be returned.
     */ 
    public function getOtherTabOptions( $aOptions, $sPageSlug, $sTabSlug ) {

        $_aStoredOptionsNotOfTheTab = array();
        foreach( $this->aFields as $_sSectionID => $_aSubSectionsOrFields ) {
            
            // Check the section
            if (     // if the section is of the given page and the given tab, skip
                isset( $this->aSections[ $_sSectionID ]['page_slug'] ) && $this->aSections[ $_sSectionID ]['page_slug'] == $sPageSlug 
                && isset( $this->aSections[ $_sSectionID ]['tab_slug'] ) && $this->aSections[ $_sSectionID ]['tab_slug'] == $sTabSlug
            ) { continue; }
            
            // At this point, the passed element belongs to the other tabs since the section of the given tab is skipped.
            foreach ( $_aSubSectionsOrFields as $_isSubSectionIndexOrFieldID => $_aSubSectionOrField  ) {
                
                // If it's a sub section
                if ( is_numeric( $_isSubSectionIndexOrFieldID ) && is_int( $_isSubSectionIndexOrFieldID + 0 ) ) { // means it's a sub-section
                    
                    // Store the entire section 
                    if ( array_key_exists( $_sSectionID, $aOptions ) ) {
                        $_aStoredOptionsNotOfTheTab[ $_sSectionID ] = $aOptions[ $_sSectionID ];
                    }
                    continue;
                    
                }
                
                // Otherwise,
                $_aField = $_aSubSectionOrField;
                
                // If a section is set,
                if ( isset( $_aField['section_id'] ) && $_aField['section_id'] != '_default' ) {
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
     * This is more strict version of the getTabOptions() method which does not respect injected elements such as the one of page meta box fields.
     * 
     * @since 3.0.0
     */
    public function getTabOnlyOptions( $aOptions, $sPageSlug, $sTabSlug='' ) {
        
        $_aStoredOptionsOfTheTab = array();
        if ( ! $sTabSlug ) { return $_aStoredOptionsOfTheTab; }
        foreach( $this->aFields as $_sSectionID => $_aSubSectionsOrFields ) {
        
            // Check the section
            if ( isset( $this->aSections[ $_sSectionID ]['page_slug'] ) && $this->aSections[ $_sSectionID ]['page_slug'] != $sPageSlug ) { continue; }
            if ( isset( $this->aSections[ $_sSectionID ]['tab_slug'] ) && $this->aSections[ $_sSectionID ]['tab_slug'] != $sTabSlug ) { continue; }
            
            // At this point, the element is of the given page and the tab.     
            foreach( $_aSubSectionsOrFields as $_sFieldID => $_aField ) {
                                
                // if it's a sub-section array.
                if ( is_numeric( $_sFieldID ) && is_int( $_sFieldID + 0 ) ) { 
                    if ( array_key_exists( $_sSectionID, $aOptions ) ) {
                        $_aStoredOptionsOfTheTab[ $_sSectionID ] = $aOptions[ $_sSectionID ];
                    }
                    continue;
                }    
                
                // if a section is set,
                if ( isset( $_aField['section_id'] ) && '_default' != $_aField['section_id'] ) {
                    if ( array_key_exists( $_aField['section_id'], $aOptions ) ) {
                        $_aStoredOptionsOfTheTab[ $_aField['section_id'] ] = $aOptions[ $_aField['section_id'] ];
                    }
                    continue;
                }
                
                // It does not have a section so set the field id as its key.
                if ( array_key_exists( $_aField['field_id'], $aOptions ) ) {
                    $_aStoredOptionsOfTheTab[ $_aField['field_id'] ] = $aOptions[ $_aField['field_id'] ];
                }
                    
            }
        
        }     
        return $_aStoredOptionsOfTheTab; 
        
    }

}