<?php
/*
 * Admin Page Framework v3.9.0b18 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Form_Model___SetFieldResources extends AdminPageFramework_Form_Base {
    public $aArguments = array();
    public $aFieldsets = array();
    public $aResources = array( 'internal_styles' => array(), 'internal_styles_ie' => array(), 'internal_scripts' => array(), 'src_styles' => array(), 'src_scripts' => array(), 'register' => array( 'styles' => array(), 'scripts' => array(), ), );
    public $aFieldTypeDefinitions = array();
    public $aCallbacks = array( 'is_fieldset_registration_allowed' => null, );
    public $oMsg;
    public function __construct()
    {
        $_aParameters = func_get_args() + array( $this->aArguments, $this->aFieldsets, $this->aResources, $this->aFieldTypeDefinitions, $this->aCallbacks, $this->oMsg, );
        $this->aArguments = $_aParameters[ 0 ];
        $this->aFieldsets = $_aParameters[ 1 ];
        $this->aResources = $_aParameters[ 2 ];
        $this->aFieldTypeDefinitions = $_aParameters[ 3 ];
        $this->aCallbacks = $_aParameters[ 4 ] + $this->aCallbacks;
        $this->oMsg = $_aParameters[ 5 ];
    }
    public function get()
    {
        $this->___setCommons();
        $this->___set($this->aFieldsets);
        return $this->aResources;
    }
    private function ___setCommons()
    {
        if ($this->hasBeenCalled(__METHOD__)) {
            return;
        }
        $this->___setCommonFormJavaScriptScripts();
        $this->___setCommonFormExternalStylesheets();
    }
    private function ___setCommonFormJavaScriptScripts()
    {
        $_aData = array( 'wpVersion' => $GLOBALS[ 'wp_version' ], 'messages' => array( 'cannotAddMore' => $this->oMsg->get('allowed_maximum_number_of_fields'), 'cannotRemoveMore' => $this->oMsg->get('allowed_minimum_number_of_fields'), 'toggleAll' => $this->oMsg->get('toggle_all'), 'toggleAllCollapsibleSections' => $this->oMsg->get('toggle_all_collapsible_sections'), 'cannotAddMoreSections' => $this->oMsg->get('allowed_maximum_number_of_sections'), 'cannotRemoveMoreSections' => $this->oMsg->get('allowed_minimum_number_of_sections'), 'loading' => $this->oMsg->get('loading'), ), 'debugMode' => $this->isDebugMode(), 'ajaxURL' => admin_url('admin-ajax.php'), 'spinnerURL' => admin_url('images/loading.gif'), );
        $this->aResources[ 'src_scripts' ][] = array( 'handle_id' => 'admin-page-framework-script-form-main', 'src' => AdminPageFramework_Registry::$sDirPath . '/factory/_common/form/asset/js/form.bundle.js', 'dependencies' => array( 'jquery', 'wp-pointer', 'jquery-ui-sortable' ), 'in_footer' => true, 'version' => AdminPageFramework_Registry::VERSION, 'translation' => $_aData, 'translation_var' => 'AdminPageFrameworkScriptFormMain', );
        $this->aResources[ 'register' ][ 'scripts' ][] = array( 'handle_id' => 'admin-page-framework-script-form-collapsible-sections', 'src' => AdminPageFramework_Registry::$sDirPath . '/factory/_common/form/asset/js/form-collapsible-sections.js', 'dependencies' => array( 'jquery', 'jquery-ui-accordion', 'admin-page-framework-script-form-main' ), 'in_footer' => true, 'version' => AdminPageFramework_Registry::VERSION, );
        if (function_exists('wp_enqueue_media')) {
            $this->aResources[ 'register' ][ 'scripts' ][] = array( 'handle_id' => 'admin-page-framework-script-form-media-uploader', 'src' => AdminPageFramework_Registry::$sDirPath . '/factory/_common/form/asset/js/form-media-uploader.js', 'dependencies' => array( 'jquery', 'admin-page-framework-script-form-main' ), 'in_footer' => false, 'version' => AdminPageFramework_Registry::VERSION, 'translation_var' => 'AdminPageFrameworkScriptFormMediaUploader', 'translation' => array( 'messages' => array( 'returnToLibrary' => $this->oMsg->get('return_to_library'), 'select' => $this->oMsg->get('select'), 'insert' => $this->oMsg->get('insert'), ), ), );
        }
    }
    private function ___setCommonFormExternalStylesheets()
    {
        $this->aResources[ 'src_styles' ][] = array( 'handle_id' => 'admin-page-framework-form', 'src' => AdminPageFramework_Registry::$sDirPath . '/factory/_common/form/asset/css/form/form.css', );
        $this->aResources[ 'src_styles' ][] = array( 'handle_id' => 'admin-page-framework-form-ie', 'src' => AdminPageFramework_Registry::$sDirPath . '/factory/_common/form/asset/css/form_ie/form_ie.css', 'conditional' => 'IE', );
        $this->aResources[ 'src_styles' ][] = array( 'handle_id' => 'wp-pointer', );
        if (version_compare($GLOBALS[ 'wp_version' ], '5.3', '>=')) {
            $this->aResources[ 'src_styles' ][] = array( 'handle_id' => 'admin-page-framework-form-5_3-or-above', 'src' => AdminPageFramework_Registry::$sDirPath . '/factory/_common/form/asset/css/form_5_3_or_above/form_5_3_or_above.css', );
        }
        if (version_compare($GLOBALS[ 'wp_version' ], '4.7', '>=')) {
            $this->aResources[ 'src_styles' ][] = array( 'handle_id' => 'admin-page-framework-form-4_7-or-above', 'src' => AdminPageFramework_Registry::$sDirPath . '/factory/_common/form/asset/css/form_4_7_or_above/form_4_7_or_above.css', );
        }
        if (version_compare($GLOBALS[ 'wp_version' ], '3.8', '<')) {
            $this->aResources[ 'src_styles' ][] = array( 'handle_id' => 'admin-page-framework-form-4_8-below', 'src' => AdminPageFramework_Registry::$sDirPath . '/factory/_common/form/asset/css/form_3_8_below/form_3_8_below.css', );
        }
        if (version_compare($GLOBALS[ 'wp_version' ], '3.8', '>=')) {
            $this->aResources[ 'src_styles' ][] = array( 'handle_id' => 'admin-page-framework-form-3_8-or-above', 'src' => AdminPageFramework_Registry::$sDirPath . '/factory/_common/form/asset/css/form_3_8_or_above/form_3_8_or_above.css', );
        }
    }
    private function ___set($aAllFieldsets)
    {
        foreach ($aAllFieldsets as $_aFieldsets) {
            $this->___setFieldResourcesBySection($_aFieldsets);
        }
    }
    private function ___setFieldResourcesBySection($_aFieldsets)
    {
        $_bIsSubSectionLoaded = false;
        foreach ($_aFieldsets as $_iSubSectionIndexOrFieldID => $_aSubSectionOrField) {
            if ($this->isNumericInteger($_iSubSectionIndexOrFieldID)) {
                if ($_bIsSubSectionLoaded) {
                    continue;
                }
                $_bIsSubSectionLoaded = true;
                foreach ($_aSubSectionOrField as $_aField) {
                    $this->___setFieldResources($_aField);
                }
                continue;
            }
            $_aField = $_aSubSectionOrField;
            $this->___setFieldResources($_aField);
        }
    }
    private function ___setFieldResources($aFieldset)
    {
        if (! $this->___isFieldsetAllowed($aFieldset)) {
            return;
        }
        $this->___setResourcesOfNestedFields($aFieldset);
        if ($this->hasNestedFields($aFieldset)) {
            $aFieldset[ 'type' ] = '_nested';
        }
        $_sFieldtype = $this->getElement($aFieldset, 'type');
        $_aFieldTypeDefinition = $this->getElementAsArray($this->aFieldTypeDefinitions, $_sFieldtype);
        $this->___setFieldResourcesByFieldTypeDefinition($aFieldset, $_sFieldtype, $_aFieldTypeDefinition);
    }
    private function ___isFieldsetAllowed($aFieldset)
    {
        return $this->callBack($this->aCallbacks[ 'is_fieldset_registration_allowed' ], array( true, $aFieldset, ));
    }
    private function ___setResourcesOfNestedFields($aFieldset)
    {
        if (! $this->hasFieldDefinitionsInContent($aFieldset)) {
            return;
        }
        foreach ($aFieldset[ 'content' ] as $_asNestedFieldset) {
            if (is_scalar($_asNestedFieldset)) {
                continue;
            }
            $this->___setFieldResources($_asNestedFieldset);
        }
    }
    private function ___setFieldResourcesByFieldTypeDefinition($aFieldset, $_sFieldtype, $_aFieldTypeDefinition)
    {
        if (empty($_aFieldTypeDefinition)) {
            return;
        }
        $this->callback($_aFieldTypeDefinition[ 'hfDoOnRegistration' ], array( $aFieldset ));
        $this->callBack($this->aCallbacks[ 'load_fieldset_resource' ], array( $aFieldset, ));
        if ($this->hasBeenCalled('registered_' . $_sFieldtype . '_' . $this->aArguments[ 'structure_type' ])) {
            return;
        }
        new AdminPageFramework_Form_Model___FieldTypeRegistration($_aFieldTypeDefinition, $this->aArguments[ 'structure_type' ]);
        $_oFieldTypeResources = new AdminPageFramework_Form_Model___FieldTypeResource($_aFieldTypeDefinition, $this->aResources);
        $this->aResources = $_oFieldTypeResources->get();
    }
}
