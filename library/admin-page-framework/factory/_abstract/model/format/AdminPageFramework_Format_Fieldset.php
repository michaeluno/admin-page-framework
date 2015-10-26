<?php
class AdminPageFramework_Format_Fieldset extends AdminPageFramework_Format_FormField_Base {
    static public $aStructure = array('field_id' => null, 'type' => null, 'section_id' => null, 'section_title' => null, 'page_slug' => null, 'tab_slug' => null, 'option_key' => null, 'class_name' => null, 'capability' => null, 'title' => null, 'tip' => null, 'description' => null, 'error_message' => null, 'before_label' => null, 'after_label' => null, 'if' => true, 'order' => null, 'default' => null, 'value' => null, 'help' => null, 'help_aside' => null, 'repeatable' => null, 'sortable' => null, 'show_title_column' => true, 'hidden' => null, 'attributes' => null, 'class' => array('fieldrow' => array(), 'fieldset' => array(), 'fields' => array(), 'field' => array(),), 'save' => true, 'content' => null, '_fields_type' => null, '_caller_object' => null, '_nested_depth' => 0, '_parent_field_name_flat' => '',);
    public $aFieldset = array();
    public $sFieldsType = '';
    public $sCapability = 'manage_options';
    public $iCountOfElements = 0;
    public $iSectionIndex = null;
    public $bIsSectionRepeatable = false;
    public $oCallerObject;
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aFieldset, $this->sFieldsType, $this->sCapability, $this->iCountOfElements, $this->iSectionIndex, $this->bIsSectionRepeatable, $this->oCallerObject);
        $this->aFieldset = $_aParameters[0];
        $this->sFieldsType = $_aParameters[1];
        $this->sCapability = $_aParameters[2];
        $this->iCountOfElements = $_aParameters[3];
        $this->iSectionIndex = $_aParameters[4];
        $this->bIsSectionRepeatable = $_aParameters[5];
        $this->oCallerObject = $_aParameters[6];
    }
    public function get() {
        $_aFieldset = $this->uniteArrays(array('_fields_type' => $this->sFieldsType, '_caller_object' => $this->oCallerObject,) + $this->aFieldset, array('capability' => $this->sCapability, 'section_id' => '_default', '_section_repeatable' => $this->bIsSectionRepeatable,) + self::$aStructure);
        $_aFieldset['field_id'] = $this->sanitizeSlug($_aFieldset['field_id']);
        $_aFieldset['section_id'] = $this->sanitizeSlug($_aFieldset['section_id']);
        $_aFieldset['tip'] = esc_attr(strip_tags($this->getElement($_aFieldset, 'tip', is_array($_aFieldset['description']) ? implode('&#10;', $_aFieldset['description']) : $_aFieldset['description'])));
        $_aFieldset['order'] = $this->getAOrB(is_numeric($_aFieldset['order']), $_aFieldset['order'], $this->iCountOfElements + 10);
        $_aFieldset['class'] = $this->getAsArray($_aFieldset['class']);
        return $_aFieldset;
    }
}