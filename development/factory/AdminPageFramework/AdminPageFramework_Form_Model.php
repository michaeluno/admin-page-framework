<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Interact with the database for the forms.
 *
 * @abstract
 * @since           3.3.1
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 */
abstract class AdminPageFramework_Form_Model extends AdminPageFramework_Form_Model_Validation {
    
    /**
     * Stores the settings field errors. 
     * 
     * @since       2.0.0
     * @var         array       Stores field errors.
     * @internal
     */ 
    protected $aFieldErrors; // Do not set a value here since it is checked to see it's null.
    
    /**
     * Defines the fields type.
     * @since       3.0.0
     * @internal
     */
    static protected $_sFieldsType = 'page';
    
    /**
     * Stores the target page slug which will be applied when no page slug is specified for the `addSettingSection()` method.
     * 
     * @since       3.0.0
     */
    protected $_sTargetPageSlug = null;
    
    /**
     * Stores the target tab slug which will be applied when no tab slug is specified for the `addSettingSection()` method.
     * 
     * @since       3.0.0
     */    
    protected $_sTargetTabSlug = null;

    /**
     * Stores the target section tab slug which will be applied when no section tab slug is specified for the `addSettingSection()` method.
     * 
     * @since 3.0.0
     */    
    protected $_sTargetSectionTabSlug = null;
    
    /**
     * Registers necessary hooks and sets up properties.
     * 
     * @internal
     * @since       3.3.0
     * @since       3.3.1       Moved from `AdminPageFramework_Setting_Base`.
     */
    function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {
        
        parent::__construct( $sOptionKey, $sCallerPath, $sCapability, $sTextDomain );

        if ( $this->oProp->bIsAdminAjax ) {
            return;
        }
        
        if ( ! $this->oProp->bIsAdmin ) {
            return;
        }
        
        add_action( "load_after_{$this->oProp->sClassName}", array( $this, '_replyToRegisterSettings' ), 20 );
        add_action( "load_after_{$this->oProp->sClassName}", array( $this, '_replyToCheckRedirects' ), 21 ); // should be loaded after registering the settings.
        
        // Form emails.
        if ( isset( $_GET['apf_action'], $_GET['transient'] ) && 'email' === $_GET['apf_action'] ) {
            
            // Set the server not to abort even the client browser terminates.
            ignore_user_abort( true );
            
            // wp_mail() will be loaded by the time 'plugins_loaded' is loaded.
            $this->oUtil->registerAction( 'plugins_loaded', array( $this, '_replyToSendFormEmail' ) );

        }
        
        // Get and post method checking.
        if ( isset( $_REQUEST['apf_remote_request_test'] ) && '_testing' === $_REQUEST['apf_remote_request_test'] ) {
            exit( 'OK' );
        }
        
    }
        
        /**
         * Indicates whether the email method is triggered or not.
         * 
         * Since multiple factory instances can load the constructor, it is possible that the method is called multiple times.
         * 
         * @since       3.4.2
         */
        static public $_bDoneEmail = false;
        
        /**
         * Sends a form email.
         * 
         * This should be called only in the background.
         * 
         * @since       3.4.2
         */
        public function _replyToSendFormEmail() {
            
            if ( self::$_bDoneEmail ) {
                return;
            }
            self::$_bDoneEmail = true;

            $_sTransient = isset( $_GET['transient'] ) ? $_GET['transient'] : '';
            if ( ! $_sTransient ) {
                return;
            }
            $_aFormEmail = $this->oUtil->getTransient( $_sTransient );
            $this->oUtil->deleteTransient( $_sTransient );
            if ( ! is_array( $_aFormEmail ) ) {
                return;
            }

            $_oEmail = new AdminPageFramework_FormEmail( 
                $_aFormEmail['email_options'], 
                $_aFormEmail['input'], 
                $_aFormEmail['section_id'] 
            );
            $_bSent = $_oEmail->send();

            exit;
            
        }
        
        
    /**
     * Check if a redirect transient is set and if so it redirects to the set page.
     * 
     * @remark A callback method for the admin_init hook.
     * @since       3.0.0
     * @since       3.3.1       Moved from `AdminPageFramework_Setting_Base`.
     * @internal
     */
    public function _replyToCheckRedirects() {

        // Check if it's one of the plugin's added page. If not, do nothing.
        if ( ! $this->_isInThePage() ) {
            return;
        }

        // If the settings have not updated the options, do nothing.
        if ( ! ( isset( $_GET['settings-updated'] ) && ! empty( $_GET['settings-updated'] ) ) ) {
            return;
        }
        
        // [3.3.0+] If the confirmation key does not hold the 'redirect' string value, do not process.
        if ( ! isset( $_GET['confirmation'] ) || 'redirect' !== $_GET['confirmation'] ) {
            return;
        }
        
        // The redirect transient key.
        $_sTransient = 'apf_rurl' . md5( trim( "redirect_{$this->oProp->sClassName}_{$_GET['page']}" ) );
        
        // Check the settings error transient.
        $_aError = $this->_getFieldErrors( $_GET['page'], false );
        if ( ! empty( $_aError ) ) {
            $this->oUtil->deleteTransient( $_sTransient ); // we don't need it any more.
            return;
        }
        
        // Okay, it seems the submitted data have been updated successfully.
        $_sURL = $this->oUtil->getTransient( $_sTransient );
        if ( false === $_sURL ) {
            return;
        }
        
        // The redirect URL seems to be set.
        $this->oUtil->deleteTransient( $_sTransient ); // we don't need it any more.
                    
        // Go to the page.
        exit( wp_redirect( $_sURL ) );
        
    }
    
    /**
     * Registers the setting sections and fields.
     * 
     * This methods passes the stored section and field array contents to the `add_settings_section()` and `add_settings_fields()` functions.
     * Then perform `register_setting()`.
     * 
     * The filters will be applied to the section and field arrays; that means that third-party scripts can modify the arrays.
     * Also they get sorted before being registered based on the set order.
     * 
     * @since       2.0.0
     * @since       2.1.5       Added the ability to define custom field types.
     * @since       3.1.2       Changed the hook from the `admin_menu` to `current_screen` so that the user can add forms in `load_{...}` callback methods.
     * @since       3.1.3       Removed the Settings API related functions entirely.
     * @since       3.3.1       Moved from `AdminPageFramework_Setting_Base`.
     * @remark      This method is not intended to be used by the user.
     * @remark      The callback method for the `load_after_{instantiated class name}` hook.
     * @return      void
     * @internal
     */ 
    public function _replyToRegisterSettings() {

        if ( ! $this->_isInThePage() ) { 
            return;
        }

        /* 1. Apply filters to added sections and fields */
        $this->oForm->aSections = $this->oUtil->addAndApplyFilter( $this, "sections_{$this->oProp->sClassName}", $this->oForm->aSections );
        foreach( $this->oForm->aFields as $_sSectionID => &$_aFields ) {
            $_aFields = $this->oUtil->addAndApplyFilter( // Parameters: $oCallerObject, $aFilters, $vInput, $vArgs...
                $this,
                "fields_{$this->oProp->sClassName}_{$_sSectionID}",
                $_aFields
            ); 
            unset( $_aFields ); // to be safe in PHP especially the same variable name is used in the scope.
        }
        $this->oForm->aFields = $this->oUtil->addAndApplyFilter( // Parameters: $oCallerObject, $aFilters, $vInput, $vArgs...
            $this,
            "fields_{$this->oProp->sClassName}",
            $this->oForm->aFields
        );         
        
        /* 2. Format ( sanitize ) the section and field arrays and apply conditions to the sections and fields and drop unnecessary items. */
        // 2-1. Set required properties for formatting.
        $this->oForm->setDefaultPageSlug( $this->oProp->sDefaultPageSlug );    
        $this->oForm->setOptionKey( $this->oProp->sOptionKey );
        $this->oForm->setCallerClassName( $this->oProp->sClassName );
        
        // 2-2. Do format internally stored sections and fields definition arrays.
        $this->oForm->format();

        // 2-3. Now set required properties for conditioning.
        $this->oForm->setCurrentPageSlug( isset( $_GET['page'] ) && $_GET['page'] ? $_GET['page'] : '' );
        $this->oForm->setCurrentTabSlug( $this->oProp->getCurrentTab() );

        // 2-4. Do conditioning.
        $this->oForm->applyConditions();
        $this->oForm->applyFiltersToFields( $this, $this->oProp->sClassName ); // applies filters to the conditioned field definition arrays.
        $this->oForm->setDynamicElements( $this->oProp->aOptions ); // will update $this->oForm->aConditionedFields
        
        /* 3. Define field types. This class adds filters for the field type definitions so that framework's built-in field types will be added. */
        $this->oProp->aFieldTypeDefinitions = AdminPageFramework_FieldTypeRegistration::register( $this->oProp->aFieldTypeDefinitions, $this->oProp->sClassName, $this->oMsg );
        $this->oProp->aFieldTypeDefinitions = $this->oUtil->addAndApplyFilter( // Parameters: $oCallerObject, $sFilter, $vInput, $vArgs...
            $this,
            'field_types_' . $this->oProp->sClassName, // 'field_types_' . {extended class name}
            $this->oProp->aFieldTypeDefinitions
        );     

        /* 4. Set up the contextual help pane */ 
        foreach( $this->oForm->aConditionedSections as $_aSection ) {
                                    
            if ( empty( $_aSection['help'] ) ) {
                continue;
            }
            
            $this->addHelpTab( 
                array(
                    'page_slug'                 => $_aSection['page_slug'],
                    'page_tab_slug'             => $_aSection['tab_slug'],
                    'help_tab_title'            => $_aSection['title'],
                    'help_tab_id'               => $_aSection['section_id'],
                    'help_tab_content'          => $_aSection['help'],
                    'help_tab_sidebar_content'  => $_aSection['help_aside'] 
                        ? $_aSection['help_aside'] 
                        : "",
                )
            );
                
        }

        /* 5. Set head tag and help pane elements */
        foreach( $this->oForm->aConditionedFields as $_sSectionID => $_aSubSectionOrFields ) {
            
            foreach( $_aSubSectionOrFields as $_sSubSectionIndexOrFieldID => $_aSubSectionOrField ) {
                
                // If the iterating item is a sub-section array.
                if ( is_numeric( $_sSubSectionIndexOrFieldID ) && is_int( $_sSubSectionIndexOrFieldID + 0 ) ) {
                    
                    $_iSubSectionIndex  = $_sSubSectionIndexOrFieldID;
                    $_aSubSection       = $_aSubSectionOrField;
                    foreach( $_aSubSection as $__sFieldID => $__aField ) {     
                        AdminPageFramework_FieldTypeRegistration::_setFieldResources( $__aField, $this->oProp, $this->oResource ); // Set relevant scripts and styles for the input field.
                    }
                    continue;
                    
                }
                    
                /* 5-1. Add the given field. */
                $aField = $_aSubSectionOrField;

                /* 5-2. Set relevant scripts and styles for the input field. */
                AdminPageFramework_FieldTypeRegistration::_setFieldResources( $aField, $this->oProp, $this->oResource ); // Set relevant scripts and styles for the input field.
            
                /* 5-3. For the contextual help pane, */
                if ( ! empty( $aField['help'] ) ) {
                    $this->addHelpTab( 
                        array(
                            'page_slug'                 => $aField['page_slug'],
                            'page_tab_slug'             => $aField['tab_slug'],
                            'help_tab_title'            => $aField['section_title'],
                            'help_tab_id'               => $aField['section_id'],
                            'help_tab_content'          => "<span class='contextual-help-tab-title'>" 
                                    . $aField['title'] 
                                . "</span> - " . PHP_EOL
                                . $aField['help'],
                            'help_tab_sidebar_content'  => $aField['help_aside'] 
                                ? $aField['help_aside'] 
                                : "",
                        )
                    );
                }
                
            }
            
        }
        
        /* 6. Enable the form - Set the form enabling flag so that the <form></form> tag will be inserted in the page. */
        $this->oProp->bEnableForm = true;    
        
        /* 7. Handle submitted data. */
        $this->_handleSubmittedData();    
        
    }
        
    /**
     * Returns the saved options.
     * 
     * This method was introduced to be used from inside field classes especially for the 'revealer' custom field type that needs to create a field object
     * while processing the revealer field output. For that, the saved option array needs to be passed and accessing the property object was somewhat indirect 
     * so there needs to be a direct method to retrieve the options. 
     * 
     * @remark      When the confirmation URL query key is set, it will merger the saved options with the last form input array, used for contact forms.
     * @since   3.3.0
     * @since   3.3.1       Moved from `AdminPageFramework_Setting_Base`. Changed the scope to `protected` as the caller method has moved to the view class.
     * @since   3.4.1       Changed the name from '_getSavedOptions()'.
     */
    public function getSavedOptions() {
        
        $_bHasConfirmation  = isset( $_GET['confirmation'] );
        $_bHasFieldErrors   = isset( $_GET['field_errors'] ) && $_GET['field_errors'];
        $_aLastInput        = $_bHasConfirmation || $_bHasFieldErrors
            ? $this->oProp->aLastInput
            : array();

        return $_aLastInput + $this->oProp->aOptions;
    }
         
}