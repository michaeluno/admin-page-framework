<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Interact with the database for the forms.
 *
 * @abstract
 * @since           3.3.1
 * @since           3.6.3       Chagned the name from `AdminPageFramework_Form_Model`.
 * @extends         AdminPageFramework_Router
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 */
abstract class AdminPageFramework_Model_Form extends AdminPageFramework_Router {
    
    /**
     * Stores the settings field errors. 
     * 
     * @remark      Do not set a default value here since it is checked whether it is null or not later.
     * @since       2.0.0
     * @since       3.6.3       Changed the visibility scope to public as a delegation class needs to access this property.
     * @var         array       Stores field errors.
     * @internal
     */ 
    public $aFieldErrors; 
    
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
    public function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {
        
        parent::__construct( $sOptionKey, $sCallerPath, $sCapability, $sTextDomain );

        if ( $this->oProp->bIsAdminAjax ) {
            return;
        }
        if ( ! $this->oProp->bIsAdmin ) {
            return;
        }
        
        new AdminPageFramework_Model_FormRegistration( $this );
        new AdminPageFramework_Model_FormSubmission( $this );
                        
        // Checking the GET and POST methods.
        if ( isset( $_REQUEST['apf_remote_request_test'] ) && '_testing' === $_REQUEST['apf_remote_request_test'] ) {
            exit( 'OK' );
        }
        
    }

    /**
     * Registers a field.
     * 
     * @remark      Overrides the parent method.
     * @since       3.5.0
     * @internal
     */
    protected function _registerField( array $aField ) {
        
        // Load head tag elements for fields.
        AdminPageFramework_FieldTypeRegistration::_setFieldResources( $aField, $this->oProp, $this->oResource ); 

        // For the contextual help pane,
        if ( $aField['help'] ) {
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
                          
        // Call the field type callback method to let it know the field type is registered.
        if ( 
            isset( $this->oProp->aFieldTypeDefinitions[ $aField['type'] ][ 'hfDoOnRegistration' ] ) 
            && is_callable( $this->oProp->aFieldTypeDefinitions[ $aField['type'] ][ 'hfDoOnRegistration' ] )
        ) {
            call_user_func_array( $this->oProp->aFieldTypeDefinitions[ $aField['type'] ][ 'hfDoOnRegistration' ], array( $aField ) );
        }            
        
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
        
        $_bHasConfirmation  = isset( $_GET[ 'confirmation' ] );
        $_bHasFieldErrors   = isset( $_GET[ 'field_errors' ] ) && $_GET[ 'field_errors' ];
        $_aLastInput        = $_bHasConfirmation || $_bHasFieldErrors
            ? $this->oProp->aLastInput
            : array();

        return $_aLastInput + $this->oProp->aOptions;
    }
         
}