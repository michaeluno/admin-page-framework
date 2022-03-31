<?php
/*
 * Admin Page Framework v3.9.1b04 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_FieldType_media extends AdminPageFramework_FieldType_image {
    public $aFieldTypeSlugs = array( 'media', );
    protected $aDefaultKeys = array( 'attributes_to_store' => array(), 'show_preview' => true, 'allow_external_source' => true, 'attributes' => array( 'input' => array( 'size' => 40, 'maxlength' => 400, ), 'button' => array( ), 'remove_button' => array( ), 'preview' => array( ), ), );
    protected function getEnqueuingScripts()
    {
        return array( array( 'handle_id' => 'admin-page-framework-field-type-media', 'src' => dirname(__FILE__) . '/js/media.bundle.js', 'in_footer' => true, 'dependencies' => array( 'jquery', 'admin-page-framework-script-form-main' ), 'translation_var' => 'AdminPageFrameworkMediaFieldType', 'translation' => array( 'fieldTypeSlugs' => $this->aFieldTypeSlugs, 'referer' => 'admin_page_framework', 'hasMediaUploader' => function_exists('wp_enqueue_media'), 'label' => array( 'uploadFile' => $this->oMsg->get('upload_file'), 'useThisFile' => $this->oMsg->get('use_this_file'), 'insertFromURL' => $this->oMsg->get('insert_from_url'), ), ), ), );
    }
    protected function _getUploaderButtonHTML($sInputID, array $aButtonAttributes, $bRepeatable, $bExternalSource)
    {
        $_bIsLabelSet = isset($aButtonAttributes[ 'data-label' ]) && $aButtonAttributes[ 'data-label' ];
        $_aAttributes = $this->___getFormattedUploadButtonAttributes_Media($sInputID, $aButtonAttributes, $_bIsLabelSet, $bExternalSource, $bRepeatable);
        return "<a " . $this->getAttributes($_aAttributes) . ">" . $this->getAOrB($_bIsLabelSet, $_aAttributes[ 'data-label' ], $this->getAOrB(strrpos($_aAttributes[ 'class' ], 'dashicons'), '', $this->oMsg->get('select_file'))) ."</a>";
    }
    private function ___getFormattedUploadButtonAttributes_Media($sInputID, array $aButtonAttributes, $_bIsLabelSet, $bExternalSource, $bRepeatable)
    {
        $_aAttributes = array( 'id' => "select_media_{$sInputID}", 'href' => '#', 'data-input_id' => $sInputID, 'data-repeatable' => ( string ) ( boolean ) $bRepeatable, 'data-uploader_type' => ( string ) function_exists('wp_enqueue_media'), 'data-enable_external_source' => ( string ) ( boolean ) $bExternalSource, ) + $aButtonAttributes + array( 'title' => $_bIsLabelSet ? $aButtonAttributes['data-label'] : $this->oMsg->get('select_file'), 'data-label' => null, );
        $_aAttributes[ 'class' ] = $this->getClassAttribute('select_media button button-small ', $this->getAOrB(trim($aButtonAttributes[ 'class' ]), $aButtonAttributes[ 'class' ], $this->getAOrB(! $_bIsLabelSet && version_compare($GLOBALS['wp_version'], '3.8', '>='), 'dashicons dashicons-portfolio', '')));
        return $_aAttributes;
    }
    protected function _getPreviewContainer($aField, $sImageURL, $aPreviewAtrributes)
    {
        return '';
    }
}
