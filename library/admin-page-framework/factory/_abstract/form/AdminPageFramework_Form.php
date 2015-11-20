<?php
class AdminPageFramework_Form extends AdminPageFramework_Form_Controller {
    public $sStructureType = '';
    public $aFieldTypeDefinitions = array();
    public $aSectionsets = array('_default' => array('section_id' => '_default',),);
    public $aFieldsets = array();
    public $aSavedData = array();
    public $sCapability = '';
    public $aCallbacks = array('capability' => null, 'is_in_the_page' => null, 'is_fieldset_registration_allowed' => null, 'load_fieldset_resource' => null, 'saved_data' => null, 'fieldset_output' => null, 'section_head_output' => null, 'sectionset_before_output' => null, 'fieldset_before_output' => null, 'is_sectionset_visible' => null, 'is_fieldset_visible' => null, 'secitonsets_before_registration' => null, 'fieldsets_before_registration' => null, 'fieldset_after_formatting' => null, 'fieldsets_after_formatting' => null, 'handle_form_data' => null,);
    public $oMsg;
    public $aArguments = array('caller_id' => '', 'structure_type' => 'admin_page', 'action_hook_form_registration' => 'current_screen',);
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aArguments, $this->aCallbacks, $this->oMsg,);
        $this->aArguments = $this->_getFormattedArguments($_aParameters[0]);
        $this->aCallbacks = $this->getAsArray($_aParameters[1]) + $this->aCallbacks;
        $this->oMsg = $_aParameters[2];
        parent::__construct();
        $this->construct();
    }
    public function construct() {
    }
    private function _getFormattedArguments($aArguments) {
        $aArguments = $this->getAsArray($aArguments) + $this->aArguments;
        $aArguments['caller_id'] = $aArguments['caller_id'] ? $aArguments['caller_id'] : get_class($this);
        if ($this->sStructureType) {
            $aArguments['structure_type'] = $this->sStructureType;
        }
        return $aArguments;
    }
}