<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods for models.
 * 
 * @abstract
 * @since       3.0.4
 * @package     AdminPageFramework
 * @subpackage  Factory
 * @transient   apf_field_erros_{user id}   stores the user-set fields error array.
 * @transient   apf_notices_{user id}       stores the user-set admin notification messages.
 * @internal
 */
abstract class AdminPageFramework_Factory_Model extends AdminPageFramework_Factory_Router {
    
    /**
     * Sets up hooks and properties.
     * 
     * @since       DEVVER
     * @internal
     */
    public function __construct( $oProp ) {
        
        parent::__construct( $oProp );
        
        add_filter(
            'field_types_admin_page_framework',
            array( $this, '_replyToFilterFieldTypeDefinitions' )
        );
        
    }       
    
    /**
     * Calls the setUp() method. 
     * 
     * @since       3.1.0
     * @todo        Deprecate this method. This method was intended to be used in a user defined abstract class 
     * but it requires to call the setUp() method in the overridden method so it's not that useful.
     * @internal
     */
    protected function _setUp() { 
        $this->setUp();
    }    
    
    /**
     * Called upon fieldset resource registration.
     * 
     * A contextual help pane item associated with this fieldset will be added.
     * 
     * @since       DEVVER
     * @return      void
     */
    public function _replyToFieldsetReourceRegistration( $aFieldset ) {
        
        $aFieldset = $aFieldset + array(
            'help'       => null,
            'title'      => null,
            'help_aside' => null,
        );
        if ( ! $aFieldset[ 'help' ] ) {
            return;
        }
        $this->oHelpPane->_addHelpTextForFormFields( 
            $aFieldset[ 'title' ], 
            $aFieldset[ 'help' ], 
            $aFieldset[ 'help_aside' ] 
        );
                   
    }    
    
    /**
     * Filters field type definitions array.
     * @callback    filter      field_types_admin_page_framework
     * @since       DEVVER
     */
    public function _replyToFilterFieldTypeDefinitions( $aFieldTypeDefinitions ) {
        return $this->oUtil->addAndApplyFilters( 
            $this,
            "field_types_{$this->oProp->sClassName}",
            $aFieldTypeDefinitions
        );                     
    }
    
    /**
     * Modifies registered sectionsets definition array.
     * @since       DEVVER
     * @return      array       The modified sectionsets definition array.
     */    
    public function _replyToModifySectionsets( $aSectionsets ) {        
        // Apply filters to added sectionsets.
        return $this->oUtil->addAndApplyFilter( 
            $this,  // caller factory object
            "sections_{$this->oProp->sClassName}", 
            $aSectionsets
        );
    }

    /**
     * Modifies registered fieldsets definition array.
     * @since       DEVVER
     * @return      array       The modified fieldsets definition array.
     */
    public function _replyToModifyFieldsets( $aFieldsets, $aSectionsets ) {

// @todo Think of a new way to retrieve the $_sSectionID for nested sections and fields
// $aSectionsets can be used

        // Apply filters to added fieldsets
        foreach( $aFieldsets as $_sSectionID => $_aFields ) {
            $aFieldsets[ $_sSectionID ] = $this->oUtil->addAndApplyFilter(
                $this,
                "fields_{$this->oProp->sClassName}_{$_sSectionID}",
                $_aFields
            ); 
        }
        $aFieldsets =  $this->oUtil->addAndApplyFilter( 
            $this,
            "fields_{$this->oProp->sClassName}",
            $aFieldsets
        );         
        
        // If at lease one filed is added, set the flag to enable the form.
        // Be careful not to set `false` when there is no field because page meta boxes may add form fields.
        if ( count( $aFieldsets ) ) {
            $this->oProp->bEnableForm = true;
        }
        
        
        return $aFieldsets;
        
    }
    
    /**
     * Applies filters to all the conditioned field definitions array.
     * @since       DEVVER
     * @return      array   
     */
    public function _replyToModifyFieldsetsDefinitions( $aFieldsets /*, $aSectionsets */ ) {
        return $this->oUtil->addAndApplyFilter(
            $this,
            "field_definition_{$this->oProp->sClassName}",
            $aFieldsets
        );    
    }
    
    /**
     * Applies filters to each conditioned field definition array.
     * 
     * @since       3.0.2
     * @since       3.1.1       Made it reformat the fields after applying filters.
     * @since       DEVVER      Changed the name from `applyFiltersToFieldsets()`.
     * Moved from `AdminPageFramework_FormDefinition_Base`.
     */
    public function _replyToModifyFieldsetDefinition( $aFieldset /*, $aSectionsets */ ) {
        
        $_sFieldPart    = '_' . implode( '_', $aFieldset[ '_field_path_array' ] );
        $_sSectionPart  = implode( '_', $aFieldset[ '_section_path_array' ] );
        $_sSectionPart  = $this->oUtil->getAOrB(
            '_default' === $_sSectionPart,
            '',
            '_' . $_sSectionPart
        );
        return $this->oUtil->addAndApplyFilter(
            $this,
            "field_definition_{$this->oProp->sClassName}{$_sSectionPart}{$_sFieldPart}",
            $aFieldset,
            $aFieldset[ '_subsection_index' ]
        ); 

    }    
               
    
    /**
     * Gets called after the form element registration is done.
     * 
     * @since       DEVVER
     */
    public function _replyToHandleSubmittedFormData( $aSavedData, $aArguments, $aSectionsets, $aFieldsets ) {
        // Do validation and saving data 
    }
        
    /**
     * @since       DEVVER
     * @return      array
     */
    public function _replyToFormatFieldsetDefinition( $aFieldset, $aSectionsets ) {

        if ( empty( $aFieldset ) ) { 
            return $aFieldset; 
        }
        return $aFieldset;
    
    }
    
    /**
     * @since       DEVVER
     * @return      array
     */
    public function _replyToFormatSectionsetDefinition( $aSectionset ) {
        
        if ( empty( $aSectionset ) ) {
            return $aSectionset;
        }
        
        $aSectionset = $aSectionset
            + array( 
                '_fields_type'      => $this->oProp->_sPropertyType, // backward compatibility
                '_structure_type'   => $this->oProp->_sPropertyType,
            );

        return $aSectionset;
        
    }
    
    /**
     * @since       DEVVER
     * @return      boolean     Whether or not the form registration should be allowed in the current screen.
     */
    public function _replyToDetermineWhetherToProcessFormRegistration( $bAllowed ) {
        return $this->_isInThePage();
    }
    
    /**
     * Returns the inherited capability value from the page and in-page tab for form elements.
     * 
     * @since       DEVVER      Moved from `AdminPageFramework_FormDefinition_Page`.
     * @return      string
     */    
    public function _replyToGetCapabilityForForm( $sCapability ) {
        return $this->oProp->sCapability;         
    }    

    /**
     * Called when the form object tries to set the form data from the database.
     * 
     * @callback    form        `saved_data`    
     * @remark      The `oOptions` property will be automatically set with the overload method.
     * @return      array       The saved form data.
     * @since       DEVVER
     */
    public function _replyToGetSavedFormData() {
        return $this->oUtil->addAndApplyFilter(
            $this, // the caller factory object
            'options_' . $this->oProp->sClassName,
            $this->oProp->aOptions      // subject value to be filtered
        );         
    }
    
    /**
     * Returns the saved options array.
     * 
     * The scope public it is accessed from the outside. This is mainly for field callback methods to create inner nested or different type of fields
     * as instantiating a field object requires this value.
     * 
     * This method is used from inside field classes especially for the 'revealer' custom field type that needs to create a field object
     * while processing the revealer field output. For that, the saved option array needs to be passed and accessing the property object was somewhat indirect 
     * so there needs to be a direct method to retrieve the options. 
     * 
     * As of DEVVER, the form object will store the saved options by itself. And the revealer field type shuold use the form object method.
     * 
     * @remark      When the confirmation URL query key is set, it will merger the saved options with the last form input array, used for contact forms.
     * @since       3.3.0
     * @since       3.3.1       Moved from `AdminPageFramework_Setting_Base`. Changed the visibility scope to `protected` as the caller method has moved to the view class.
     * @since       3.4.0       Changed the visibility scope to public.
     * @since       3.4.1       Changed the name from '_getSavedOptions()'.
     * @since       DEVVER      Moved from `AdminPageFramework_Model_Form`.
     * @remark      assumes the `aSavedData` property is already set. 
     * This is set when the form fields are registered.
     * @deprecated  DEVVER      Kept for backward compatibility. 
     */
    public function getSavedOptions() {
        return $this->oForm->aSavedData;
    }

    /**
     * Returns the settings error array set by the user in the validation callback.
     * 
     * The scope is public because it is accessed from outside ofo the class. This is mainly for field callback methods to create inner nested or different type of fields
     * as instantiating a field object requires this value.
     * 
     * @since       3.4.0
     */
    public function getFieldErrors() {
        return $this->_getFieldErrors();
    }
    
    
    /**
     * Retrieves the settings error array set by the user in the validation callback.
     * 
     * @since       3.0.4    
     * @since       3.6.3       Changed the visibility scope to public as a delegation class needs to access this method.
     * @access      public
     * @internal
     * @param       string      $sID        deprecated
     * @param       boolean     $bDelete    whether or not the transient should be deleted after retrieving it. 
     * @return      array
     * @deprecated  DEVVER      Kept for backward compatibility
     */
    public function _getFieldErrors( $sID='deprecated', $bDelete=true ) {
        return $this->oForm->getFieldErrors();        
    }    
        
    /**
     * Checks whether a validation error is set.
     * 
     * @since       3.0.3
     * @internal
     * @return      mixed           If the error is not set, returns false; otherwise, the stored error array.
     * @todo        Examine which class uses this method. It looks like this can be deprecated as there is the `hasFieldError()` method.
     */
    protected function _isValidationErrors() {
        
        $_aFieldErrors = $this->oUtil->getElement(
            $GLOBALS,
            array( 'aAdminPageFramework', 'aFieldErrors' )
        );
        
        // If the transient is not set, false will be given.
        return ! empty( $_aFieldErrors )
            ? $_aFieldErrors
            : $this->oUtil->getTransient( "apf_field_erros_" . get_current_user_id() );

    }
        
    /**
     * Saves the field error array into the transient.
     * 
     * @since       3.0.4
     * @internal
     * @return      void
     */ 
    public function _replyToSaveFieldErrors() {
        
        if ( ! isset( $GLOBALS[ 'aAdminPageFramework' ][ 'aFieldErrors' ] ) ) { 
            return; 
        }

        $this->oUtil->setTransient( 
            "apf_field_erros_" . get_current_user_id(),  
            $GLOBALS['aAdminPageFramework']['aFieldErrors'], 
            300     // store it for 5 minutes ( 60 seconds * 5 )
        );    
        
    }
    
    /**
     * Saves the notification array set via the setSettingNotice() method.
     * 
     * @remark      This method will be triggered with the 'shutdown' hook.
     * @since       3.0.4 
     * @internal
     * @return      void
     */
    public function _replyToSaveNotices() {
        
        if ( ! isset( $GLOBALS['aAdminPageFramework']['aNotices'] ) ) { 
            return; 
        }
        if ( empty( $GLOBALS['aAdminPageFramework']['aNotices'] ) ) { 
            return; 
        }
                
        $this->oUtil->setTransient( 
            'apf_notices_' . get_current_user_id(), 
            $GLOBALS['aAdminPageFramework']['aNotices'] 
        );
        
    }
    
    /**
     * The validation callback method.
     * 
     * The user may just override this method instead of defining a `validation_{...}` callback method.
     * 
     * @since       3.4.1
     * @remark      Declare this method in each factory class as the form of parameters will be different which triggers PHP strict standard warnings.
     */
    // public function validate( $aInput, $aOldInput, $oFactory ) {
        // return $aInput;
    // }
    
    /**
     * Saves user last input in the database as a transient.
     * 
     * To get the set input, call `$this->oProp->aLastInput`.
     * 
     * @since       3.4.1
     * @since       DEVVER      Changed the name from `_setLastInput()`.
     * @return      boolean     True if set; otherwise, false.
     * @internal
     */
    public function _setLastInputs( array $aLastInputs ) {
        return $this->oUtil->setTransient( 
            'apf_tfd' . md5( 'temporary_form_data_' . $this->oProp->sClassName . get_current_user_id() ),
            $aLastInputs, 
            60*60 
        );
    }
        /**
         * An alias of `_setLastInputs()`.
         * @deprecated      DEVVER
         */
        public function _setLastInput( $aLastInputs )  {
            return $this->_setLastInputs( $aLastInputs );
        }
     
    /**
     * The public version of `_getSortedInputs()`.
     * 
     * A delegation class needs to access the `_getSortedInputs()` method but it is protected, so uses this instead.
     * 
     * @since       3.6.3
     * @deprecated  DEVVER
     */
    // public function getSortedInputs( array $aInput ) {
        // return $this->_getSortedInputs( $aInput );
    // }     
    /**
     * Sorts dynamic elements.
     * @since       3.6.0
     * @return      array       The sorted input array.
     * @deprecated  DEVVER
     */
    // protected function _getSortedInputs( array $aInput ) {
        
        // $_aDynamicFieldAddressKeys = array_unique(
            // array_merge(
                // $this->oUtil->getElementAsArray( 
                    // $_POST,
                    // '__repeatable_elements_' . $this->oProp->sStructureType,
                    // array()
                // ),
                // $this->oUtil->getElementAsArray( 
                    // $_POST,
                    // '__sortable_elements_' . $this->oProp->sStructureType,
                    // array()
                // )
            // )
        // );

        // if ( empty( $_aDynamicFieldAddressKeys ) ) {
            // return $aInput;
        // }

        // $_oInputSorter = new AdminPageFramework_Form_Model___Modifier_SortInput( 
            // $aInput, 
            // $_aDynamicFieldAddressKeys
        // );
        // return $_oInputSorter->get();
        
    // }   

}