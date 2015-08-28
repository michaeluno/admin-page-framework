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
     * Stores the default field definitions. 
     * 
     * Once they are set, it no longer needs to be done.
     * 
     * @since       3.1.3
     * @internal    
     */
    static private $_aFieldTypeDefinitions = array();
    
    /**
     * Loads the default field type definition.
     * 
     * @since       2.1.5
     * @since       3.5.0       Changed the scope to protected as it is internal. 
     * Changed the name from `_loadDefaultFieldTypeDefinitions()` as it applies filters so custom field types also get registered here.
     * @internal
     */
    protected function _loadFieldTypeDefinitions() {
        
        if ( empty( self::$_aFieldTypeDefinitions ) ) {
            
            // This class adds filters for the field type definitions so that framework's default field types will be added.
            self::$_aFieldTypeDefinitions = AdminPageFramework_FieldTypeRegistration::register( 
                array(), 
                $this->oProp->sClassName, 
                $this->oMsg 
            );     
            
        } 
                
        $this->oProp->aFieldTypeDefinitions = $this->oUtil->addAndApplyFilters( 
            $this,
            array( 
                'field_types_admin_page_framework',         // 3.5.0+ Allows sitewide field type registration.
                "field_types_{$this->oProp->sClassName}",
            ),
            self::$_aFieldTypeDefinitions
        );     
        
    }    

    /**
     * Registers the given fields.
     * 
     * @remark      `$oHelpPane` and `$oHeadTab` need to be set in the extended class.
     * @remark      This method should be called after the `_loadFieldTypeDefinitions()` emthod.
     * @since       3.0.0
     * @internal
     * @return      void
     */
    protected function _registerFields( array $aFields ) {

        foreach( $aFields as $_sSecitonID => $_aFields ) {
            
            $_bIsSubSectionLoaded = false;
            foreach( $_aFields as $_iSubSectionIndexOrFieldID => $_aSubSectionOrField )  {
                
                // if it's a sub-section
                if ( $this->oUtil->isNumericInteger( $_iSubSectionIndexOrFieldID ) ) {

                    // no need to repeat the same set of fields
                    if ( $_bIsSubSectionLoaded ) { 
                        continue;
                    }
                    $_bIsSubSectionLoaded = true;
                    foreach( $_aSubSectionOrField as $_aField ) {
                        $this->_registerField( $_aField );     
                    }
                    continue;
                }
                    
                $_aField = $_aSubSectionOrField;
                $this->_registerField( $_aField );
            
            }
        }
        
    }
        /**
         * Registers a field.
         * 
         * @since       3.0.4
         * @since       3.5.0       Changed the scope to protected as the admin page factory class overrides it.
         * @internal
         * @return      void
         */
        protected function _registerField( array $aField ) {
            
            // Set relevant scripts and styles for the field.
            AdminPageFramework_FieldTypeRegistration::_setFieldResources( $aField, $this->oProp, $this->oResource );

            // For the contextual help pane,
            if ( $aField['help'] ) {
                $this->oHelpPane->_addHelpTextForFormFields( $aField['title'], $aField['help'], $aField['help_aside'] );
            }
            
            // Call the field type callback method to let it know the field type is registered.
            if ( 
                isset( $this->oProp->aFieldTypeDefinitions[ $aField['type'] ][ 'hfDoOnRegistration' ] ) 
                && is_callable( $this->oProp->aFieldTypeDefinitions[ $aField['type'] ][ 'hfDoOnRegistration' ] )
            ) {
                call_user_func_array( 
                    $this->oProp->aFieldTypeDefinitions[ $aField['type'] ][ 'hfDoOnRegistration' ],
                    array( $aField )
                );
            }
            
        }    
    
    /**
     * Returns the saved options array.
     * 
     * The scope public it is accessed from the outside. This is mainly for field callback methods to create inner nested or different type of fields
     * as instantiating a field object requires this value.
     * 
     * @since       3.4.0
     */
    public function getSavedOptions() {
        
        // @todo: implement the last input data that is available for the page factory using $aLastInput for their factory fields.
        return $this->oProp->aOptions;
        
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
     * @access      private
     * @internal
     * @param       string      $sID        deprecated
     * @param       boolean     $bDelete    whether or not the transient should be deleted after retrieving it. 
     */
    protected function _getFieldErrors( $sID='deprecated', $bDelete=true ) {
        
        static $_aFieldErrors;
        
        // Find the transient.
        $_sTransientKey = "apf_field_erros_" . get_current_user_id();
        $_sID           = md5( $this->oProp->sClassName );

        $_aFieldErrors = isset( $_aFieldErrors ) 
            ? $_aFieldErrors 
            : $this->oUtil->getTransient( $_sTransientKey );
        if ( $bDelete ) {
            add_action( 'shutdown', array( $this, '_replyToDeleteFieldErrors' ) );
        }
        return $this->oUtil->getElementAsArray(
            $_aFieldErrors,
            $_sID,
            array()
        );
        
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

        if ( 
            isset( $GLOBALS['aAdminPageFramework'][ 'aFieldErrors' ] ) 
            && $GLOBALS['aAdminPageFramework'][ 'aFieldErrors' ] ) 
        {
            return true;
        }
        return $this->oUtil->getTransient( 
            "apf_field_erros_" . get_current_user_id() 
        );

    }

        /**
         * Deletes the field errors.
         * 
         * This should be called with the shutdown hook.
         * 
         * @since       3.0.4
         * @internal
         */
        public function _replyToDeleteFieldErrors() {
            $this->oUtil->deleteTransient( "apf_field_erros_" . get_current_user_id() );
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
     * @return      boolean     True if set; otherwise, false.
     * @internal
     */
    public function _setLastInput( array $aLastInput ) {
        return $this->oUtil->setTransient( 
            'apf_tfd' . md5( 'temporary_form_data_' . $this->oProp->sClassName . get_current_user_id() ),
            $aLastInput, 
            60*60 
        );
    }
    
    
    /**
     * Sorts dynamic elements.
     * @since       3.6.0
     * @return      array       The sorted input array.
     */
    protected function _getSortedInputs( array $aInput ) {
        
        $_sFieldAddressKey = '__dynamic_elements_' . $this->oProp->sFieldsType;
        
        if ( ! isset( $_POST[ $_sFieldAddressKey ] ) ) {
            return $aInput;
        }
        
        $_oInputSorter = new AdminPageFramework_Sort_Input( 
            $aInput, 
            $_POST[ $_sFieldAddressKey ] 
        );
        return $_oInputSorter->get();
        
    }         
    
}