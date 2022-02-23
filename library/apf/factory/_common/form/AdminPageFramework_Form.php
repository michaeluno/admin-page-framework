<?php
/*
 * Admin Page Framework v3.9.0b15 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Form extends AdminPageFramework_Form_Controller
{
    public $sStructureType = '';
    public $aFieldTypeDefinitions = array();
    public $aSectionsets = array( '_default' => array( 'section_id' => '_default', ), );
    public $aFieldsets = array();
    public $aSavedData = array();
    public $sCapability = '';
    public $aCallbacks = array( 'capability' => null, 'is_in_the_page' => null, 'is_fieldset_registration_allowed' => null, 'load_fieldset_resource' => null, 'saved_data' => null, 'fieldset_output' => null, 'section_head_output' => null, 'sectionset_before_output' => null, 'fieldset_before_output' => null, 'is_sectionset_visible' => null, 'is_fieldset_visible' => null, 'sectionsets_before_registration' => null, 'fieldsets_before_registration' => null, 'fieldset_after_formatting' => null, 'fieldsets_before_formatting' => null, 'handle_form_data' => null, 'show_debug_info' => null, 'field_errors' => null, 'get_form_object' => null, 'hfID' => null, 'hfTagID' => null, 'hfName' => null, 'hfNameFlat' => null, 'hfInputName' => null, 'hfInputNameFlat' => null, 'hfClass' => null, 'hfSectionName' => null, );
    public $oMsg;
    public $aArguments = array( 'caller_id' => '', 'structure_type' => 'admin_page', 'action_hook_form_registration' => 'current_screen', 'register_if_action_already_done' => true, 'autoload_min_resource' => true, );
    public $aSubClasses = array( 'submit_notice' => 'AdminPageFramework_Form___SubmitNotice', 'field_error' => 'AdminPageFramework_Form___FieldError', 'last_input' => 'AdminPageFramework_Form_Model___LastInput', 'message' => 'AdminPageFramework_Message', );
    public function __construct()
    {
        $_aParameters = func_get_args() + array( $this->aArguments, $this->aCallbacks, $this->oMsg, );
        $this->aArguments = $this->___getArgumentsFormatted($_aParameters[ 0 ]);
        $this->aCallbacks = $this->getAsArray($_aParameters[ 1 ]) + array( 'get_form_object' => array( $this, 'replyToGetSelf' ), ) + $this->aCallbacks;
        $this->oMsg = $_aParameters[ 2 ] ? $_aParameters[ 2 ] : new $this->aSubClasses[ 'message' ]();
        $this->___setSubClassObjects();
        parent::__construct();
        $this->construct();
    }
    private function ___getArgumentsFormatted($aArguments)
    {
        $aArguments = $this->getAsArray($aArguments) + $this->aArguments;
        $aArguments[ 'caller_id' ] = $aArguments[ 'caller_id' ] ? $aArguments[ 'caller_id' ] : get_class($this);
        if ($this->sStructureType) {
            $aArguments[ 'structure_type' ] = $this->sStructureType;
        }
        return $aArguments;
    }
    private function ___setSubClassObjects()
    {
        if (class_exists($this->aSubClasses[ 'submit_notice' ])) {
            $this->oSubmitNotice = new $this->aSubClasses[ 'submit_notice' ]();
        }
        if (class_exists($this->aSubClasses[ 'field_error' ])) {
            $this->oFieldError = new $this->aSubClasses[ 'field_error' ]($this->aArguments[ 'caller_id' ]);
        }
        if (class_exists($this->aSubClasses[ 'last_input' ])) {
            $this->oLastInputs = new $this->aSubClasses[ 'last_input' ]($this->aArguments[ 'caller_id' ]);
        }
    }
    public function construct()
    {
    }
}
