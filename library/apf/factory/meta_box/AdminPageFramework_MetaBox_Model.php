<?php
/*
 * Admin Page Framework v3.9.0 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_MetaBox_Model extends AdminPageFramework_MetaBox_Router {
    public function __construct($sMetaBoxID, $sTitle, $asPostTypeOrScreenID=array( 'post' ), $sContext='normal', $sPriority='default', $sCapability='edit_posts', $sTextDomain='admin-page-framework')
    {
        add_action('set_up_' . $this->oProp->sClassName, array( $this, '_replyToSetUpHooks' ));
        add_action('set_up_' . $this->oProp->sClassName, array( $this, '_replyToSetUpValidationHooks' ));
        parent::__construct($sMetaBoxID, $sTitle, $asPostTypeOrScreenID, $sContext, $sPriority, $sCapability, $sTextDomain);
    }
    public function _replyToSetUpHooks($oFactory)
    {
        $this->oUtil->registerAction('add_meta_boxes', array( $this, '_replyToRegisterMetaBoxes' ));
    }
    public function _replyToSetUpValidationHooks($oScreen)
    {
        if ('attachment' === $oScreen->post_type && in_array('attachment', $this->oProp->aPostTypes)) {
            add_filter('wp_insert_attachment_data', array( $this, '_replyToFilterSavingData' ), 10, 2);
        } else {
            add_filter('wp_insert_post_data', array( $this, '_replyToFilterSavingData' ), 10, 2);
        }
    }
    public function _replyToRegisterMetaBoxes()
    {
        foreach ($this->oProp->aPostTypes as $_sPostType) {
            add_meta_box($this->oProp->sMetaBoxID, $this->oProp->sTitle, array( $this, '_replyToPrintMetaBoxContents' ), $_sPostType, $this->oProp->sContext, $this->oProp->sPriority, null);
        }
    }
    public function _replyToGetSavedFormData()
    {
        $_oMetaData = new AdminPageFramework_MetaBox_Model___PostMeta($this->___getPostID(), $this->oForm->aFieldsets);
        $this->oProp->aOptions = $_oMetaData->get();
        return parent::_replyToGetSavedFormData();
    }
    private function ___getPostID()
    {
        if (isset($GLOBALS[ 'post' ]->ID)) {
            return $GLOBALS[ 'post' ]->ID;
        }
        if (isset($_GET[ 'post' ])) {
            return absint($_GET[ 'post' ]);
        }
        if (isset($_POST[ 'post_ID' ])) {
            return absint($_POST[ 'post_ID' ]);
        }
        return 0;
    }
    public function _replyToFilterSavingData($aPostData, $aUnmodified)
    {
        if (! $this->_shouldProceedValidation($aUnmodified)) {
            return $aPostData;
        }
        $_aInputs = $this->oForm->getSubmittedData($this->oForm->getHTTPRequestSanitized($_POST, false), true, false);
        $_aInputsRaw = $_aInputs;
        $_iPostID = $aUnmodified[ 'ID' ];
        $_aSavedMeta = $this->oUtil->getSavedPostMetaArray($_iPostID, array_keys($_aInputs));
        $_aInputs = $this->oUtil->addAndApplyFilters($this, "validation_{$this->oProp->sClassName}", call_user_func_array(array( $this, 'validate' ), array( $_aInputs, $_aSavedMeta, $this )), $_aSavedMeta, $this);
        if ($this->hasFieldError()) {
            $this->setLastInputs($_aInputsRaw);
            $aPostData[ 'post_status' ] = 'pending';
            add_filter('redirect_post_location', array( $this, '_replyToModifyRedirectPostLocation' ));
        }
        $this->oForm->updateMetaDataByType($_iPostID, $_aInputs, $this->oForm->dropRepeatableElements($_aSavedMeta), $this->oForm->sStructureType);
        return $aPostData;
    }
    public function _replyToModifyRedirectPostLocation($sLocation)
    {
        remove_filter('redirect_post_location', array( $this, __FUNCTION__ ));
        return add_query_arg(array( 'message' => 'apf_field_error', 'field_errors' => true ), $sLocation);
    }
    private function _shouldProceedValidation(array $aUnmodified)
    {
        if ('auto-draft' === $aUnmodified[ 'post_status' ]) {
            return false;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return false;
        }
        if (! isset($_POST[ $this->oProp->sMetaBoxID ])) {
            return false;
        }
        if (! wp_verify_nonce($_POST[ $this->oProp->sMetaBoxID ], $this->oProp->sMetaBoxID)) {
            return false;
        }
        if (! in_array($aUnmodified[ 'post_type' ], $this->oProp->aPostTypes, true)) {
            return false;
        }
        return current_user_can($this->oProp->sCapability, $aUnmodified[ 'ID' ]);
    }
}
