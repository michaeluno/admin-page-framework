<?php
/*
 * Admin Page Framework v3.9.0b15 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_FieldType_contact extends AdminPageFramework_FieldType_submit
{
    public $aFieldTypeSlugs = array( 'contact', );
    private $___sAction = 'admin-page-framework_contact_field_type_email';
    protected function construct()
    {
        $this->aDefaultKeys = array( 'email' => array( 'to' => null, 'subject' => null, 'message' => null, 'headers' => null, 'attachments' => null, 'is_html' => true, 'from' => null, 'name' => null, 'data' => array(), ), 'system_message' => array( 'success' => $this->oMsg->get('email_sent'), 'failure' => $this->oMsg->get('email_could_not_send'), 'error' => $this->oMsg->get('nonce_verification_failed'), ), ) + $this->aDefaultKeys;
        add_action("wp_ajax_{$this->___sAction}", '__return_empty_string');
    }
    protected function getField($aField)
    {
        return "<div class='result-placeholder'>" . "<span class='dashicons'></span>" . "</div>" . parent::getField($aField);
    }
    protected function getEnqueuingScripts()
    {
        return array( array( 'handle_id' => 'admin-page-framework-field-type-contact', 'src' => dirname(__FILE__) . '/js/contact.bundle.js', 'in_footer' => true, 'dependencies' => array( 'jquery', 'admin-page-framework-script-form-main' ), 'translation_var' => 'AdminPageFrameworkContactFieldType', 'translation' => array( 'nonce' => wp_create_nonce(get_class($this)), 'action' => $this->___sAction, 'messages' => array( 'requiredField' => $this->oMsg->get('please_fill_out_this_field'), ), ), ), );
    }
    protected function _getInputAttributes(array $aField)
    {
        return parent::_getInputAttributes($aField) + $this->getDataAttributeArray($this->___getEmailArguments($aField));
    }
    private function ___getEmailArguments(array $aField)
    {
        if (empty($aField[ 'email' ])) {
            return array();
        }
        return array( 'email' => true, 'input_flat' => $aField[ '_input_name_flat' ], 'section_id' => $aField[ 'section_id' ], );
    }
    protected function _getHiddenInput_Email(array $aField)
    {
        $_sTransientKey = 'apf_em_' . md5($aField[ '_input_name_flat' ] . get_current_user_id());
        $this->setTransient($_sTransientKey, array( 'nonce' => $this->getNonceCreated('apf_email_nonce_' . md5(( string ) site_url()), 86400), 'system_message' => $this->getElementAsArray($aField, 'system_message'), ) + $this->getAsArray($aField[ 'email' ]));
        return ! $this->_checkConfirmationDisplayed($aField, $aField[ '_input_name_flat' ], 'email') ? $this->getHTMLTag('input', array( 'type' => 'hidden', 'name' => "__submit[{$aField[ 'input_id' ]}][confirming_sending_email]", 'value' => '1', )) : $this->getHTMLTag('input', array( 'type' => 'hidden', 'name' => "__submit[{$aField[ 'input_id' ]}][confirmed_sending_email]", 'value' => '1', ));
    }
    protected function doOnFieldRegistration($aFieldset)
    {
        if (isset($_REQUEST[ 'action' ]) && $this->___sAction === $_REQUEST[ 'action' ]) {
            $this->___doAjaxResponse();
        }
    }
    private function ___doAjaxResponse()
    {
        if (false === wp_verify_nonce($this->getElement($_REQUEST, 'nonce'), get_class($this))) {
            wp_send_json(array( 'result' => false, 'message' => $this->oMsg->get('nonce_verification_failed') ));
        }
        $_aRequest = $this->getHTTPRequestSanitized($_REQUEST);
        $_sInputFlat = $this->getElement($_aRequest, 'input_flat');
        $_sSubmitSectionID = $this->getElement($_aRequest, 'section_id');
        $_aInputPath = explode('|', $_sInputFlat);
        $_sRootDimension = reset($_aInputPath);
        $_aForm = $this->___getFormDataParsed($this->getElementAsArray($_aRequest, array( 'form' )));
        $_aInputs = $this->getElementAsArray($_aForm, array( $_sRootDimension ));
        $_sTransientKey = 'apf_em_' . md5($_sInputFlat . get_current_user_id());
        $_aEmailOptions = $this->getTransient($_sTransientKey);
        $_aEmailOptions = $this->getAsArray($_aEmailOptions) + array( 'nonce' => '', 'to' => '', 'subject' => '', 'message' => '', 'headers' => '', 'attachments' => '', 'is_html' => false, 'from' => '', 'name' => '', );
        $_aMessages = $this->getElementAsArray($_aEmailOptions, array( 'system_message' ));
        if (false === wp_verify_nonce($_aEmailOptions[ 'nonce' ], 'apf_email_nonce_' . md5(( string ) site_url()))) {
            wp_send_json(array( 'result' => false, 'message' => $this->getElement($_aMessages, 'error', $this->oMsg->get('nonce_verification_failed')) ));
        }
        $_oEmail = new AdminPageFramework_FormEmail($_aEmailOptions, $_aInputs, $_sSubmitSectionID);
        $_bResult = $_oEmail->send();
        wp_send_json(array( 'result' => $_bResult, 'message' => $_bResult ? $this->getElement($_aMessages, 'success', $this->oMsg->get('email_sent')) : $this->getElement($_aMessages, 'failure', $this->oMsg->get('email_could_not_send')), ));
    }
    private function ___getFormDataParsed(array $aForm)
    {
        $_aForm = array();
        $aForm = array_reverse($aForm);
        foreach ($aForm as $_iIndex => $_aNameValue) {
            parse_str($_aNameValue[ 'name' ] . '=' . $_aNameValue[ 'value' ], $_a);
            $_aForm = $this->uniteArrays($_aForm, $_a);
        }
        return array_reverse($_aForm);
    }
}
