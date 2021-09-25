<?php 
/**
	Admin Page Framework v3.9.0b10 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
abstract class AdminPageFramework_FieldType_Base extends AdminPageFramework_Form_Utility {
    public $_sFieldSetType = '';
    public $aFieldTypeSlugs = array('default');
    protected $aDefaultKeys = array();
    protected static $_aDefaultKeys = array('value' => null, 'default' => null, 'repeatable' => false, 'sortable' => false, 'label' => '', 'delimiter' => '', 'before_input' => '', 'after_input' => '', 'before_label' => null, 'after_label' => null, 'before_field' => null, 'after_field' => null, 'label_min_width' => '', 'before_fieldset' => null, 'after_fieldset' => null, 'field_id' => null, 'page_slug' => null, 'section_id' => null, 'before_fields' => null, 'after_fields' => null, 'attributes' => array('disabled' => null, 'class' => '', 'fieldrow' => array(), 'fieldset' => array(), 'fields' => array(), 'field' => array(),),);
    protected $oMsg;
    public function __construct($asClassName = 'admin_page_framework', $asFieldTypeSlug = null, $oMsg = null, $bAutoRegister = true) {
        $this->aFieldTypeSlugs = empty($asFieldTypeSlug) ? $this->aFieldTypeSlugs : ( array )$asFieldTypeSlug;
        $this->oMsg = $oMsg ? $oMsg : AdminPageFramework_Message::getInstance();
        if ($bAutoRegister) {
            foreach (( array )$asClassName as $_sClassName) {
                add_filter('field_types_' . $_sClassName, array($this, '_replyToRegisterInputFieldType'));
            }
        }
        $this->construct();
    }
    protected function construct() {
    }
    protected function isTinyMCESupported() {
        return version_compare($GLOBALS['wp_version'], '3.3', '>=') && function_exists('wp_editor');
    }
    protected function getElementByLabel($asElement, $asKey, $asLabel) {
        if (is_scalar($asElement)) {
            return $asElement;
        }
        return is_array($asLabel) ? $this->getElement($asElement, $this->getAsArray($asKey, true), '') : $asElement;
    }
    protected function getFieldOutput(array $aFieldset) {
        if (!is_object($aFieldset['_caller_object'])) {
            return '';
        }
        $aFieldset['_nested_depth']++;
        $aFieldset['_parent_field_object'] = $aFieldset['_field_object'];
        $_oCallerForm = $aFieldset['_caller_object'];
        $_oFieldset = new AdminPageFramework_Form_View___Fieldset($aFieldset, $_oCallerForm->aSavedData, $_oCallerForm->getFieldErrors(), $_oCallerForm->aFieldTypeDefinitions, $_oCallerForm->oMsg, $_oCallerForm->aCallbacks);
        return $_oFieldset->get();
    }
    protected function geFieldOutput(array $aFieldset) {
        return $this->getFieldOutput($aFieldset);
    }
    public function _replyToRegisterInputFieldType($aFieldDefinitions) {
        foreach ($this->aFieldTypeSlugs as $sFieldTypeSlug) {
            $aFieldDefinitions[$sFieldTypeSlug] = $this->getDefinitionArray($sFieldTypeSlug);
        }
        return $aFieldDefinitions;
    }
    public function getDefinitionArray($sFieldTypeSlug = '') {
        $_aDefaultKeys = $this->aDefaultKeys + self::$_aDefaultKeys;
        $_aDefaultKeys['attributes'] = isset($this->aDefaultKeys['attributes']) && is_array($this->aDefaultKeys['attributes']) ? $this->aDefaultKeys['attributes'] + self::$_aDefaultKeys['attributes'] : self::$_aDefaultKeys['attributes'];
        return array('sFieldTypeSlug' => $sFieldTypeSlug, 'aFieldTypeSlugs' => $this->aFieldTypeSlugs, 'hfRenderField' => array($this, "_replyToGetField"), 'hfGetScripts' => array($this, "_replyToGetScripts"), 'hfGetStyles' => array($this, "_replyToGetStyles"), 'hfGetIEStyles' => array($this, "_replyToGetInputIEStyles"), 'hfFieldLoader' => array($this, "_replyToFieldLoader"), 'hfFieldSetTypeSetter' => array($this, "_replyToFieldTypeSetter"), 'hfDoOnRegistration' => array($this, "_replyToDoOnFieldRegistration"), 'aEnqueueScripts' => $this->_replyToGetEnqueuingScripts(), 'aEnqueueStyles' => $this->_replyToGetEnqueuingStyles(), 'aDefaultKeys' => $_aDefaultKeys,);
    }
    public function _replyToGetField($aField) {
        return '';
    }
    public function _replyToGetScripts() {
        return '';
    }
    public function _replyToGetInputIEStyles() {
        return '';
    }
    public function _replyToGetStyles() {
        return '';
    }
    public function _replyToFieldLoader() {
    }
    public function _replyToFieldTypeSetter($sFieldSetType = '') {
        $this->_sFieldSetType = $sFieldSetType;
    }
    public function _replyToDoOnFieldRegistration($aField) {
    }
    protected function _replyToGetEnqueuingScripts() {
        return array();
    }
    protected function _replyToGetEnqueuingStyles() {
        return array();
    }
    protected function enqueueMediaUploader() {
        add_filter('media_upload_tabs', array($this, '_replyToRemovingMediaLibraryTab'));
        wp_enqueue_script('jquery');
        wp_enqueue_script('thickbox');
        wp_enqueue_style('thickbox');
        if (function_exists('wp_enqueue_media')) {
            add_action(is_admin() ? 'admin_footer' : 'wp_footer', array($this, 'replyToEnqueueScriptsForMediaUpload'), 1);
        } else {
            wp_enqueue_script('media-upload');
        }
        if (in_array($this->getPageNow(), array('media-upload.php', 'async-upload.php',))) {
            add_filter('gettext', array($this, '_replyToReplaceThickBoxText'), 1, 2);
        }
    }
    public function replyToEnqueueScriptsForMediaUpload() {
        wp_enqueue_media();
        wp_enqueue_script('admin-page-framework-script-form-media-uploader');
    }
    public function _replyToReplaceThickBoxText($sTranslated, $sText) {
        if (!in_array($this->getPageNow(), array('media-upload.php', 'async-upload.php'))) {
            return $sTranslated;
        }
        if ($sText !== 'Insert into Post') {
            return $sTranslated;
        }
        if ($this->getQueryValueInURLByKey(wp_get_referer(), 'referrer') !== 'admin_page_framework') {
            return $sTranslated;
        }
        if (isset($_GET['button_label'])) {
            return $this->getHTTPQueryGET('button_label', '');
        }
        return $this->oMsg->get('use_this_image');
    }
    public function _replyToRemovingMediaLibraryTab($aTabs) {
        if (!isset($_REQUEST['enable_external_source'])) {
            return $aTabs;
        }
        if (!( boolean )$_REQUEST['enable_external_source']) {
            unset($aTabs['type_url']);
        }
        return $aTabs;
    }
    protected function getLabelContainerAttributes($aField, $asClassAttributes, array $aAttributes = array()) {
        $aAttributes['class'] = $this->getClassAttribute($asClassAttributes, $this->getElement($aAttributes, 'class'));
        $aAttributes['style'] = $this->getStyleAttribute(array('min-width' => $aField['label_min_width'] || '0' === ( string )$aField['label_min_width'] ? $this->getLengthSanitized($aField['label_min_width']) : null,), $this->getElement($aAttributes, 'style'));
        return $this->getAttributes($aAttributes);
    }
    }
    