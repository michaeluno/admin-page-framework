<?php
/*
 * Admin Page Framework v3.9.1b01 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_Model_Form extends AdminPageFramework_Router {
    public $aFieldErrors;
    protected $_sTargetPageSlug = null;
    protected $_sTargetTabSlug = null;
    protected $_sTargetSectionTabSlug = null;
    public function __construct($sOptionKey=null, $sCallerPath=null, $sCapability='manage_options', $sTextDomain='admin-page-framework')
    {
        parent::__construct($sOptionKey, $sCallerPath, $sCapability, $sTextDomain);
        if (! $this->oProp->bIsAdmin) {
            return;
        }
        if (isset($_REQUEST[ 'apf_remote_request_test' ]) && '_testing' === $_REQUEST[ 'apf_remote_request_test' ]) {
            exit('OK');
        }
    }
    public function _replyToHandleSubmittedFormData($aSavedData, $aArguments, $aSectionsets, $aFieldsets)
    {
        new AdminPageFramework_Model__FormSubmission($this, $aSavedData, $aArguments, $aSectionsets, $aFieldsets);
    }
    public function _replyToFieldsetResourceRegistration($aFieldset)
    {
        $aFieldset = $aFieldset + array( 'help' => null, 'title' => null, 'help_aside' => null, 'page_slug' => null, 'tab_slug' => null, 'section_title' => null, 'section_id' => null, );
        if (! $aFieldset[ 'help' ]) {
            return;
        }
        $_sRootSectionID = $this->oUtil->getElement($this->oUtil->getAsArray($aFieldset[ 'section_id' ]), 0);
        $this->addHelpTab(array( 'page_slug' => $aFieldset[ 'page_slug' ], 'page_tab_slug' => $aFieldset[ 'tab_slug' ], 'help_tab_title' => $aFieldset[ 'section_title' ], 'help_tab_id' => $_sRootSectionID, 'help_tab_content' => "<span class='contextual-help-tab-title'>" . $aFieldset[ 'title' ] . "</span> - " . PHP_EOL . $aFieldset[ 'help' ], 'help_tab_sidebar_content' => $aFieldset[ 'help_aside' ] ? $aFieldset[ 'help_aside' ] : "", ));
    }
    public function _replyToModifySectionsets($aSectionsets)
    {
        $this->_registerHelpPaneItemsOfFormSections($aSectionsets);
        return parent::_replyToModifySectionsets($aSectionsets);
    }
    public function _registerHelpPaneItemsOfFormSections($aSectionsets)
    {
        foreach ($aSectionsets as $_aSectionset) {
            $_aSectionset = $_aSectionset + array( 'help' => null, 'page_slug' => null, 'tab_slug' => null, 'title' => null, 'section_id' => null, 'help' => null, 'help_aside' => null, );
            if (empty($_aSectionset[ 'help' ])) {
                continue;
            }
            $this->addHelpTab(array( 'page_slug' => $_aSectionset[ 'page_slug' ], 'page_tab_slug' => $_aSectionset[ 'tab_slug' ], 'help_tab_title' => $_aSectionset[ 'title' ], 'help_tab_id' => $_aSectionset[ 'section_id' ], 'help_tab_content' => $_aSectionset[ 'help' ], 'help_tab_sidebar_content' => $this->oUtil->getElement($_aSectionset, 'help_aside', ''), ));
        }
    }
    public function _replyToDetermineSectionsetVisibility($bVisible, $aSectionset)
    {
        if (! current_user_can($aSectionset[ 'capability' ])) {
            return false;
        }
        if (! $aSectionset[ 'if' ]) {
            return false;
        }
        if (! $this->_isSectionOfCurrentPage($aSectionset)) {
            return false;
        }
        return $bVisible;
    }
    private function _isSectionOfCurrentPage(array $aSectionset)
    {
        $_sCurrentPageSlug = ( string ) $this->oProp->getCurrentPageSlug();
        if ($aSectionset[ 'page_slug' ] !== $_sCurrentPageSlug) {
            return false;
        }
        if (! $aSectionset[ 'tab_slug' ]) {
            return true;
        }
        return ($aSectionset[ 'tab_slug' ] === $this->oProp->getCurrentTabSlug($_sCurrentPageSlug));
    }
    public function _replyToDetermineFieldsetVisibility($bVisible, $aFieldset)
    {
        $_sCurrentPageSlug = $this->oProp->getCurrentPageSlug();
        if ($aFieldset[ 'page_slug' ] !== $_sCurrentPageSlug) {
            return false;
        }
        return parent::_replyToDetermineFieldsetVisibility($bVisible, $aFieldset);
    }
    public function _replyToFormatFieldsetDefinition($aFieldset, $aSectionsets)
    {
        if (empty($aFieldset)) {
            return $aFieldset;
        }
        $_aSectionPath = $this->oUtil->getAsArray($aFieldset[ 'section_id' ]);
        $_sSectionPath = implode('|', $_aSectionPath);
        $aFieldset[ 'option_key' ] = $this->oProp->sOptionKey;
        $aFieldset[ 'class_name' ] = $this->oProp->sClassName;
        $aFieldset[ 'page_slug' ] = $this->oUtil->getElement($aSectionsets, array( $_sSectionPath, 'page_slug' ), $this->oProp->getCurrentPageSlugIfAdded());
        $aFieldset[ 'tab_slug' ] = $this->oUtil->getElement($aSectionsets, array( $_sSectionPath, 'tab_slug' ), $this->oProp->getCurrentInPageTabSlugIfAdded());
        $_aSectionset = $this->oUtil->getElementAsArray($aSectionsets, $_sSectionPath);
        $aFieldset[ 'section_title' ] = $this->oUtil->getElement($_aSectionset, 'title');
        $aFieldset[ 'capability' ] = $aFieldset[ 'capability' ] ? $aFieldset[ 'capability' ] : $this->_replyToGetCapabilityForForm($this->oUtil->getElement($_aSectionset, 'capability'), $_aSectionset[ 'page_slug' ], $_aSectionset[ 'tab_slug' ]);
        return parent::_replyToFormatFieldsetDefinition($aFieldset, $aSectionsets);
    }
    public function _replyToFormatSectionsetDefinition($aSectionset)
    {
        if (empty($aSectionset)) {
            return $aSectionset;
        }
        $aSectionset = $aSectionset + array( 'page_slug' => null, 'tab_slug' => null, 'capability' => null, );
        $aSectionset[ 'page_slug' ] = $this->_getSectionPageSlug($aSectionset);
        $aSectionset[ 'tab_slug' ] = $this->_getSectionTabSlug($aSectionset);
        $aSectionset[ 'capability' ] = $this->_getSectionCapability($aSectionset);
        return parent::_replyToFormatSectionsetDefinition($aSectionset);
    }
    private function _getSectionCapability($aSectionset)
    {
        if ($aSectionset[ 'capability' ]) {
            return $aSectionset[ 'capability' ];
        }
        if (0 < $aSectionset[ '_nested_depth' ]) {
            $_aSectionPath = $aSectionset[ '_section_path_array' ];
            array_pop($_aSectionPath);
            $_sParentCapability = $this->oUtil->getElement($this->oForm->aSectionsets, array_merge($_aSectionPath, array( 'capability' )));
            if ($_sParentCapability) {
                return $_sParentCapability;
            }
        }
        return $this->_replyToGetCapabilityForForm($aSectionset[ 'capability' ], $aSectionset[ 'page_slug' ], $aSectionset[ 'tab_slug' ]);
    }
    private function _getSectionPageSlug($aSectionset)
    {
        if ($aSectionset[ 'page_slug' ]) {
            return $aSectionset[ 'page_slug' ];
        }
        if (0 < $aSectionset[ '_nested_depth' ]) {
            $_aSectionPath = $aSectionset[ '_section_path_array' ];
            $_sRootSectionID = $this->oUtil->getFirstElement($_aSectionPath);
            $_sRootSectionPageSlug = $this->oUtil->getElement($this->oForm->aSectionsets, array( $_sRootSectionID, 'page_slug' ));
            if ($_sRootSectionPageSlug) {
                return $_sRootSectionPageSlug;
            }
        }
        return $this->oProp->getCurrentPageSlugIfAdded();
    }
    private function _getSectionTabSlug($aSectionset)
    {
        if ($aSectionset[ 'tab_slug' ]) {
            return $aSectionset[ 'tab_slug' ];
        }
        return $this->oProp->getCurrentInPageTabSlugIfAdded();
    }
    public function _replyToDetermineWhetherToProcessFormRegistration($bAllowed)
    {
        if ($this->oProp->bIsAdminAjax) {
            return true;
        }
        $_sPageSlug = $this->oProp->getCurrentPageSlug();
        return $this->oProp->isPageAdded($_sPageSlug);
    }
    public function _replyToGetCapabilityForForm($sCapability)
    {
        $_aParameters = func_get_args() + array( '', '', '' );
        $_sPageSlug = $this->oUtil->getAOrB($_aParameters[ 1 ], $_aParameters[ 1 ], $this->oProp->getCurrentPageSlug());
        $_sTabSlug = $this->oUtil->getAOrB($_aParameters[ 2 ], $_aParameters[ 2 ], $this->oProp->getCurrentTabSlug($_sPageSlug));
        $_sTabCapability = $this->_getInPageTabCapability($_sTabSlug, $_sPageSlug);
        $_sPageCapability = $this->_getPageCapability($_sPageSlug);
        $_aCapabilities = array_values(array_filter(array( $_sTabCapability, $_sPageCapability ))) + array( $this->oProp->sCapability );
        return $_aCapabilities[ 0 ];
    }
}
