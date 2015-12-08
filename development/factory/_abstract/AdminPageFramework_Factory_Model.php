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
     * @since       3.7.0
     * @internal
     */
    public function __construct( $oProp ) {
        
        parent::__construct( $oProp );
        
        add_filter(
            // 'field_types_admin_page_framework',
            'field_types_' . $oProp->sClassName,
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
     * @since       3.7.0
     * @return      void
     */
    public function _replyToFieldsetResourceRegistration( $aFieldset ) {
        
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
     * @callback    filter      field_types_{class name}
     * @since       3.7.0
     * @since       3.7.1       Changed the callback from `field_types_admin_page_framework`.
     */
    public function _replyToFilterFieldTypeDefinitions( $aFieldTypeDefinitions ) {
        
        // Not triggering `__call()` as the filter is fired manually in the form class.
        if ( method_exists( $this, 'field_types_' . $this->oProp->sClassName ) ) {
            return call_user_func_array(
                array( $this, 'field_types_' . $this->oProp->sClassName ),
                array( $aFieldTypeDefinitions )
            );
        }
        return $aFieldTypeDefinitions;
        
        // @deprecated 3.7.1
        // return $this->oUtil->addAndApplyFilters( 
            // $this,
            // "field_types_{$this->oProp->sClassName}",
            // $aFieldTypeDefinitions
        // );                     
    }
    
    /**
     * Modifies registered sectionsets definition array.
     * 
     * This lets third party scripts to set their own sections 
     * before the framework registered field resource (assets) files.
     * 
     * @remark      Called prior to field resource registrations.
     * @since       3.7.0
     * @return      array       The modified sectionsets definition array.
     */    
    public function _replyToModifySectionsets( $aSectionsets ) {    
        
        return $this->oUtil->addAndApplyFilter( 
            $this,  // caller factory object
            "sections_{$this->oProp->sClassName}", 
            $aSectionsets
        );
        
    }

    /**
     * Modifies registered fieldsets definition array.
     * 
     * This lets third party scripts to set their own sections 
     * before the framework registered field resource (assets) files.
     * 
     * @remark      Called prior to field resource registrations.
     * @since       3.7.0
     * @return      array       The modified fieldsets definition array.
     */
    public function _replyToModifyFieldsets( $aFieldsets, $aSectionsets ) {

        // Apply filters to added fieldsets
        foreach( $aFieldsets as $_sSectionPath => $_aFields ) {
            $_aSectionPath  = explode( '|', $_sSectionPath );
            $_sFilterSuffix = implode( '_', $_aSectionPath );
            $aFieldsets[ $_sSectionPath ] = $this->oUtil->addAndApplyFilter(
                $this,
                "fields_{$this->oProp->sClassName}_{$_sFilterSuffix}",
                $_aFields
            ); 
        }
        $aFieldsets =  $this->oUtil->addAndApplyFilter( 
            $this,
            "fields_{$this->oProp->sClassName}",
            $aFieldsets
        );         
        
        // If at lease one filed is added, set the flag to enable the form.
        // Do not set `false` when there is no field because page meta boxes may add form fields.
        if ( count( $aFieldsets ) ) {
            $this->oProp->bEnableForm = true;
        }
        
        return $aFieldsets;
        
    }
    
    /**
     * Applies filters to all the conditioned field definitions array.
     * @since       3.7.0
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
     * Applies filters to each conditioned (formatted) field definition array.
     * 
     * @since       3.0.2
     * @since       3.1.1       Made it reformat the fields after applying filters.
     * @since       3.7.0      Changed the name from `applyFiltersToFieldsets()`.
     * Moved from `AdminPageFramework_FormDefinition_Base`.
     * @return      array      
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
     * @since       3.7.0
     */
    public function _replyToHandleSubmittedFormData( $aSavedData, $aArguments, $aSectionsets, $aFieldsets ) {
        // Do validation and saving data 
    }
        
    /**
     * @since       3.7.0
     * @return      array
     */
    public function _replyToFormatFieldsetDefinition( $aFieldset, $aSectionsets ) {

        if ( empty( $aFieldset ) ) { 
            return $aFieldset; 
        }
        return $aFieldset;
    
    }
    
    /**
     * @since       3.7.0
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
     * @since       3.7.0
     * @return      boolean     Whether or not the form registration should be allowed in the current screen.
     */
    public function _replyToDetermineWhetherToProcessFormRegistration( $bAllowed ) {
        return $this->_isInThePage();
    }
    
    /**
     * Returns the inherited capability value from the page and in-page tab for form elements.
     * 
     * @since       3.7.0      Moved from `AdminPageFramework_FormDefinition_Page`.
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
     * @since       3.7.0
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
     * As of 3.7.0, the form object will store the saved options by itself. And the revealer field type shuold use the form object method.
     * 
     * @remark      When the confirmation URL query key is set, it will merger the saved options with the last form input array, used for contact forms.
     * @since       3.3.0
     * @since       3.3.1       Moved from `AdminPageFramework_Setting_Base`. Changed the visibility scope to `protected` as the caller method has moved to the view class.
     * @since       3.4.0       Changed the visibility scope to public.
     * @since       3.4.1       Changed the name from '_getSavedOptions()'.
     * @since       3.7.0      Moved from `AdminPageFramework_Model_Form`.
     * @remark      assumes the `aSavedData` property is already set. 
     * This is set when the form fields are registered.
     * @deprecated  3.7.0      Kept for backward compatibility. 
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
        return $this->oForm->getFieldErrors();
    }

    /**
     * Retrieves the settings error array set by the user in the validation callback.
     * 
     * @since       3.0.4    
     * @since       3.6.3       Changed the visibility scope to public as a delegation class needs to access this method.
     * @since       3.7.0      Changed back the visibility scope to protected as there is the `getFieldErrors()` public method.
     * @access      protected
     * @internal
     * @param       string      $sID        deprecated
     * @param       boolean     $bDelete    whether or not the transient should be deleted after retrieving it. 
     * @return      array
     * @deprecated  3.7.0      Use `getFieldErrors()` instead. Kept for backward compatibility.
     */
    protected function _getFieldErrors( /* $sID='deprecated', $bDelete=true */ ) {
        return $this->oForm->getFieldErrors();        
    }    

    /**
     * Saves user last input in the database as a transient.
     * 
     * To get the set input, call `$this->oProp->aLastInput`.
     * 
     * @since       3.4.1
     * @since       3.7.0      Changed the name from `_setLastInput()`.
     * @return      boolean     True if set; otherwise, false.
     * @internal
     */
    public function setLastInputs( array $aLastInputs ) {
        return $this->oForm->setLastInputs( $aLastInputs );
    }
        /**
         * An alias of `_setLastInputs()`.
         * @deprecated      3.7.0
         */
        public function _setLastInput( $aLastInputs )  {
            return $this->setLastInputs( $aLastInputs );
        }    

     
}