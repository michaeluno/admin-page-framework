<?php
abstract class AdminPageFramework_FormFieldset_Base extends AdminPageFramework_WPUtility {
    public $aField = array();
    public $aFieldTypeDefinitions = array();
    public $aOptions = array();
    public $aErrors = array();
    public $oMsg;
    public $aCallbacks = array();
    public function __construct(&$aField, $aOptions, $aErrors, &$aFieldTypeDefinitions, &$oMsg, array $aCallbacks = array()) {
        $this->aField = $this->uniteArrays($aField, $this->_getFieldTypeDefaultArguments($aField['type'], $aFieldTypeDefinitions));
        $this->aFieldTypeDefinitions = $aFieldTypeDefinitions;
        $this->aOptions = $aOptions;
        $this->aErrors = $this->getAsArray($aErrors);
        $this->oMsg = $oMsg;
        $this->aCallbacks = $aCallbacks + array('hfID' => null, 'hfTagID' => null, 'hfName' => null, 'hfNameFlat' => null, 'hfInputName' => null, 'hfInputNameFlat' => null, 'hfClass' => null,);
        $this->_loadScripts($this->aField['_fields_type']);
    }
    private function _getFieldTypeDefaultArguments($sFieldType, $aFieldTypeDefinitions) {
        $_aFieldTypeDefinition = $this->getElement($aFieldTypeDefinitions, $sFieldType, $aFieldTypeDefinitions['default']);
        $_aDefaultKeys = $this->getAsArray($_aFieldTypeDefinition['aDefaultKeys']);
        $_aDefaultKeys['attributes'] = array('fieldrow' => $_aDefaultKeys['attributes']['fieldrow'], 'fieldset' => $_aDefaultKeys['attributes']['fieldset'], 'fields' => $_aDefaultKeys['attributes']['fields'], 'field' => $_aDefaultKeys['attributes']['field'],);
        return $_aDefaultKeys;
    }
    static private $_bIsLoadedSScripts = false;
    static private $_bIsLoadedSScripts_Widget = false;
    private function _loadScripts($sFieldsType = '') {
        if ('widget' === $sFieldsType && !self::$_bIsLoadedSScripts_Widget) {
            new AdminPageFramework_Script_Widget;
            self::$_bIsLoadedSScripts_Widget = true;
        }
        if (self::$_bIsLoadedSScripts) {
            return;
        }
        self::$_bIsLoadedSScripts = true;
        new AdminPageFramework_Script_Utility;
        new AdminPageFramework_Script_OptionStorage;
        new AdminPageFramework_Script_AttributeUpdator;
        new AdminPageFramework_Script_RepeatableField($this->oMsg);
        new AdminPageFramework_Script_SortableField;
        new AdminPageFramework_Script_RegisterCallback;
    }
    protected function _getRepeaterFieldEnablerScript($sFieldsContainerID, $iFieldCount, $aSettings) {
        $_sAdd = $this->oMsg->get('add');
        $_sRemove = $this->oMsg->get('remove');
        $_sVisibility = $iFieldCount <= 1 ? " style='visibility: hidden;'" : "";
        $_sSettingsAttributes = $this->generateDataAttributes(( array )$aSettings);
        $_bDashiconSupported = false;
        $_sDashiconPlus = $_bDashiconSupported ? 'dashicons dashicons-plus' : '';
        $_sDashiconMinus = $_bDashiconSupported ? 'dashicons dashicons-minus' : '';
        $_sButtons = "<div class='admin-page-framework-repeatable-field-buttons' {$_sSettingsAttributes} >" . "<a class='repeatable-field-remove-button button-secondary repeatable-field-button button button-small {$_sDashiconMinus}' href='#' title='{$_sRemove}' {$_sVisibility} data-id='{$sFieldsContainerID}'>" . ($_bDashiconSupported ? '' : '-') . "</a>" . "<a class='repeatable-field-add-button button-secondary repeatable-field-button button button-small {$_sDashiconPlus}' href='#' title='{$_sAdd}' data-id='{$sFieldsContainerID}'>" . ($_bDashiconSupported ? '' : '+') . "</a>" . "</div>";
        $_aJSArray = json_encode($aSettings);
        $_sButtonsHTML = '"' . $_sButtons . '"';
        $_sScript = <<<JAVASCRIPTS
jQuery( document ).ready( function() {
    var _nodePositionIndicators = jQuery( '#{$sFieldsContainerID} .admin-page-framework-field .repeatable-field-buttons' );
    /* If the position of inserting the buttons is specified in the field type definition, replace the pointer element with the created output */
    if ( _nodePositionIndicators.length > 0 ) {
        _nodePositionIndicators.replaceWith( $_sButtonsHTML );
    } else { 
    /* Otherwise, insert the button element at the beginning of the field tag */
        // check the button container already exists for WordPress 3.5.1 or below
        if ( ! jQuery( '#{$sFieldsContainerID} .admin-page-framework-repeatable-field-buttons' ).length ) { 
            // Adds the buttons
            jQuery( '#{$sFieldsContainerID} .admin-page-framework-field' ).prepend( $_sButtonsHTML ); 
        }
    }     
    jQuery( '#{$sFieldsContainerID}' ).updateAdminPageFrameworkRepeatableFields( $_aJSArray ); // Update the fields     
});
JAVASCRIPTS;
        return "<script type='text/javascript'>" . $_sScript . "</script>";
    }
    protected function _getSortableFieldEnablerScript($sFieldsContainerID) {
        $_sScript = <<<JAVASCRIPTS
    jQuery( document ).ready( function() {
        jQuery( this ).enableAdminPageFrameworkSortableFields( '$sFieldsContainerID' );
    });
JAVASCRIPTS;
        return "<script type='text/javascript' class='admin-page-framework-sortable-field-enabler-script'>" . $_sScript . "</script>";
    }
}