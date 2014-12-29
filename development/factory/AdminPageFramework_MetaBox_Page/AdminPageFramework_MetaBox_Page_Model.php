<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides model methods for creating meta boxes in pages added by the framework.
 * 
 * @abstract
 * @since           3.0.4
 * @package         AdminPageFramework
 * @subpackage      PageMetaBox
 * @internal
 */
abstract class AdminPageFramework_MetaBox_Page_Model extends AdminPageFramework_MetaBox_Page_Router {

    /**
     * Defines the fields type.
     * @since       3.0.0
     * @internal
     */
    static protected $_sFieldsType = 'page_meta_box';

    /**
     * Sets up properties and hooks.
     * 
     * @since       3.0.4
     */
    function __construct( $sMetaBoxID, $sTitle, $asPageSlugs=array(), $sContext='normal', $sPriority='default', $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {     
                
        /* The property object needs to be done first */
        $this->oProp             = new AdminPageFramework_Property_MetaBox_Page( $this, get_class( $this ), $sCapability, $sTextDomain, self::$_sFieldsType );     
        $this->oProp->aPageSlugs = is_string( $asPageSlugs ) ? array( $asPageSlugs ) : $asPageSlugs; // must be set before the isInThePage() method is used.
        
        parent::__construct( $sMetaBoxID, $sTitle, $asPageSlugs, $sContext, $sPriority, $sCapability, $sTextDomain );
            
    }
        
    /**
     * Sets up validation hooks.
     * 
     * @since       3.3.0
     */
    protected function _setUpValidationHooks( $oScreen ) {

        // Validation hooks
        foreach( $this->oProp->aPageSlugs as $_sIndexOrPageSlug => $_asTabArrayOrPageSlug ) {
            
            if ( is_string( $_asTabArrayOrPageSlug ) ) {     
                $_sPageSlug = $_asTabArrayOrPageSlug;
                // add_filter( "validation_saved_options_{$_sPageSlug}", array( $this, '_replyToFilterPageOptions' ) ); // 3.4.1 deprecated 
                add_filter( "validation_saved_options_without_dynamic_elements_{$_sPageSlug}", array( $this, '_replyToFilterPageOptionsWODynamicElements' ), 10, 2 );  // 3.4.1+
                add_filter( "validation_{$_sPageSlug}", array( $this, '_replyToValidateOptions' ), 10, 3 );
                add_filter( "options_update_status_{$_sPageSlug}", array( $this, '_replyToModifyOptionsUpdateStatus' ) );
                continue;
            }
            
            // At this point, the array key is the page slug. It means the user specified the tab(s).
            $_sPageSlug = $_sIndexOrPageSlug;
            $_aTabs     = $_asTabArrayOrPageSlug;
            foreach( $_aTabs as $_sTabSlug ) {
                add_filter( "validation_{$_sPageSlug}_{$_sTabSlug}", array( $this, '_replyToValidateOptions' ), 10, 3 );
                // deprecated 3.4.1+
                // add_filter( "validation_saved_options_{$_sPageSlug}_{$_sTabSlug}", array( $this, '_replyToFilterPageOptions' ) );
                add_filter( "validation_saved_options_without_dynamic_elements_{$_sPageSlug}_{$_sTabSlug}", array( $this, '_replyToFilterPageOptionsWODynamicElements' ), 10, 2 ); // 3.4.1+
                add_filter( "options_update_status_{$_sPageSlug}_{$_sTabSlug}", array( $this, '_replyToModifyOptionsUpdateStatus' ) );
            }
            
        }
    
    }        

    /**
     * Returns the field output.
     * 
     * @since       3.0.0
     * @internal
     */
    protected function getFieldOutput( $aField ) {
        
        /* Since meta box fields don't have the `option_key` key which is required to compose the name attribute in the regular pages. */
        $_sOptionKey            = $this->_getOptionKey();
        $aField['option_key']   = $_sOptionKey ? $_sOptionKey : null;
        $aField['page_slug']    = isset( $_GET['page'] ) ? $_GET['page'] : ''; // set an empty string to make it yield true for isset() so that saved options will be checked.

        return parent::getFieldOutput( $aField );
        
    }

        /**
         * Returns the currently loading page's option key if the page has the admin page object.
         * @since       3.0.0
         * @internal
         */
        private function _getOptionkey() {
            return isset( $_GET['page'] ) 
                ? $this->oProp->getOptionKey( $_GET['page'] )
                : null;
        }
                    
    /**
     * Adds the defined meta box.
     * 
     * @internal
     * @since       3.0.0
     * @remark      uses `add_meta_box()`.
     * @remark      Before this method is called, the pages and in-page tabs need to be registered already.
     * @remark      A callback for the `add_meta_boxes` hook.
     * @return      void
     */ 
    public function _replyToAddMetaBox( $sPageHook='' ) {

        foreach( $this->oProp->aPageSlugs as $sKey => $asPage ) {

            if ( is_string( $asPage ) )  {
                $this->_addMetaBox( $asPage );
                continue;
            }
            if ( ! is_array( $asPage ) ) { continue; }
            
            $_sPageSlug = $sKey;
            foreach( $asPage as $_sTabSlug ) {
                
                if ( ! $this->oProp->isCurrentTab( $_sTabSlug ) ) { continue; }
                
                $this->_addMetaBox( $_sPageSlug );
                
            }
            
        }
                
    }    
        /**
         * Adds meta box with the given page slug.
         * 
         * @since       3.0.0
         * @internal
         */
        private function _addMetaBox( $sPageSlug ) {

            add_meta_box( 
                $this->oProp->sMetaBoxID,                       // id
                $this->oProp->sTitle,                           // title
                array( $this, '_replyToPrintMetaBoxContents' ), // callback
                $this->oProp->_getScreenIDOfPage( $sPageSlug ), // screen ID
                $this->oProp->sContext,                         // context
                $this->oProp->sPriority,                        // priority
                null                                            // argument (deprecated)
            );     
            
        }

    /**
     * Filters the page option array.
     * 
     * This is triggered from the system validation method of the main Admin Page Framework factory class with the `validation_saved_options_{page slug}` filter hook.
     * 
     * @since       3.0.0
     * @param       array       $aPageOptions
     * @deprecated  3.4.1
     */
    public function _replyToFilterPageOptions( $aPageOptions ) {
        return $aPageOptions;
        // return $this->oForm->dropRepeatableElements( $aPageOptions );        // deprecated 3.4.1
    }
    /**
     * Filters the array of the options without dynamic elements.
     * 
     * @since       3.4.1       Deprecated `_replyToFilterPageOptions()`.
     */
    public function _replyToFilterPageOptionsWODynamicElements( $aOptionsWODynamicElements, $oFactory ) {
        return $this->oForm->dropRepeatableElements( $aOptionsWODynamicElements );
    }
    
    /**
     * Validates the submitted option values.
     * 
     * This method is triggered with the `validation_{page slug}` or `validation_{page slug}_{tab slug}` method of the main Admin Page Framework factory class.
     * 
     * @internal
     * @sicne       3.0.0
     * @param       array       $aNewPageOptions        The array holing the field values of the page sent from the framework page class (the main class).
     * @param       array       $aOldPageOptions        The array holing the saved options of the page. Note that this will be empty if non of generic page fields are created.
     */
    public function _replyToValidateOptions( $aNewPageOptions, $aOldPageOptions ) {

        // The field values of this class will not be included in the parameter array. So get them.
        $_aFieldsModel          = $this->oForm->getFieldsModel();
        $_aNewMetaBoxInput      = $this->oUtil->castArrayContents( $_aFieldsModel, $_POST );
        $_aOldMetaBoxInput      = $this->oUtil->castArrayContents( $_aFieldsModel, $aOldPageOptions );
        
        // 3.4.3+ deprecated
        // $_aOtherOldMetaBoxInput = $this->oUtil->invertCastArrayContents( $aOldPageOptions, $_aFieldsModel ); 

        // Apply filters - third party scripts will have access to the input.
        $_aNewMetaBoxInput      = stripslashes_deep( $_aNewMetaBoxInput ); // fixes magic quotes
        $_aNewMetaBoxInputRaw   = $_aNewMetaBoxInput; // copy one for a validation error.
        $_aNewMetaBoxInput      = $this->validate( $_aNewMetaBoxInput, $_aOldMetaBoxInput, $this );           
        $_aNewMetaBoxInput      = $this->oUtil->addAndApplyFilters( 
            $this, 
            "validation_{$this->oProp->sClassName}", 
            $_aNewMetaBoxInput, 
            $_aOldMetaBoxInput, 
            $this 
        );
    
        // If there are validation errors. set the last input.
        if ( $this->hasFieldError() ) {
            $this->_setLastInput( $_aNewMetaBoxInputRaw );           
        }    
    
        // Now merge the input values with the passed page options, and plus the old data to cover different in-page tab field options.
        return $this->oUtil->uniteArrays( 
            $_aNewMetaBoxInput, 
            $aNewPageOptions
            // $_aOtherOldMetaBoxInput  // 3.4.3+ deprecated 
        );       
                        
    }
    
    /**
     * Modifies the options update status array.
     * 
     * This is to insert the 'field_errors' key into the options update status array when there is a field error.
     * 
     * @since       3.4.1
     */
    public function _replyToModifyOptionsUpdateStatus( $aStatus ) {
        
        if ( ! $this->hasFieldError() ) {
            return $aStatus;
        }
        return array( 
                'field_errors' => true 
            ) 
            + $this->oUtil->getAsArray( $aStatus );
        
    }
    
    /**
     * Registers form fields and sections.
     * 
     * @since       3.0.0
     * @since       3.3.0       Changed the name from `_replyToRegisterFormElements()`. Changed the scope to `protected`.
     * @internal
     */
    public function _registerFormElements( $oScreen ) {
                
        // Schedule to add head tag elements and help pane contents.     
        if ( ! $this->_isInThePage() ) { return; }
        
        $this->_loadDefaultFieldTypeDefinitions();
        
        // Format the fields array.
        $this->oForm->format();
        $this->oForm->applyConditions(); // will create the conditioned elements.
        $this->oForm->applyFiltersToFields( $this, $this->oProp->sClassName );
        
        // Finalize the options array as it still holds values that are not of this class form fields.
        $this->_setOptionArray( $_GET['page'], $this->oForm->aConditionedFields );
        
        // Add the repeatable section elements to the fields definition array.
        $this->oForm->setDynamicElements( $this->oProp->aOptions ); // will update $this->oForm->aConditionedFields
                        
        $this->_registerFields( $this->oForm->aConditionedFields );

    }     
    
    /**
     * Sets the aOptions property array in the property object. 
     * 
     * The `$this->oProp->aOptions` property should be already set in the `_replyToSetUpProperties()` method of the `AdminPageFramework_Property_Metabox_Page`.
     * But the array is not finalized and it stores all the page's options. So the field values that are not of this class should be removed.
     * 
     * @since       3.4.1    
     * @remark      Overrides the parent method defined in the meta box class.
     * @internal    
     */
    protected function _setOptionArray( $sPageSlug, $aFields ) {
        
        // Remove elements that are not registered in this class. Here `array_key_exists()` is used instead of `isset()` to check the element existence
        // as `isset()` returns false when a null value is set.
        // @todo Examine why there are values of null assigned as the field value (confirmed in 3.4.0.) It could be that because dynamic elements were removed.
        $_aOptions = array();
        foreach( $aFields as $_sSectionID => $_aFields ) {
            if ( '_default' == $_sSectionID  ) {
                foreach( $_aFields as $_aField ) {
                    if ( array_key_exists( $_aField['field_id'], $this->oProp->aOptions ) ) {
                        $_aOptions[ $_aField['field_id'] ] = $this->oProp->aOptions[ $_aField['field_id'] ];
                    }
                }
            }
            if ( array_key_exists( $_sSectionID, $this->oProp->aOptions ) ) {
                $_aOptions = $this->oProp->aOptions[ $_sSectionID ];
            }
        }        

        // Apply the filters to let third party scripts to set own options array.
        $this->oProp->aOptions = $this->oUtil->addAndApplyFilter( // Parameters: $oCallerObject, $sFilter, $vInput, $vArgs...
            $this, // the caller object
            'options_' . $this->oProp->sClassName, // options_{instantiated class name}
            $_aOptions
        );
        
        $_aLastInput = isset( $_GET['field_errors'] ) && $_GET['field_errors'] ? $this->oProp->aLastInput : array();
        $this->oProp->aOptions = empty( $this->oProp->aOptions ) ? array() : $this->oUtil->getAsArray( $this->oProp->aOptions );
        $this->oProp->aOptions = $_aLastInput + $this->oProp->aOptions;

    }
            
}