<?php
/*
 * Admin Page Framework v3.9.0 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_WalkerTaxonomyChecklist extends Walker_Category {
    public function start_el(&$sOutput, $oTerm, $iDepth=0, $aArgs=array(), $iCurrentObjectID=0)
    {
        $aArgs = $aArgs + array( '_name_prefix' => null, '_input_id_prefix' => null, '_attributes' => array(), '_selected_items' => array(), 'taxonomy' => null, 'disabled' => null, '_save_unchecked' => true, );
        $_iID = $oTerm->term_id;
        $_sTaxonomySlug = empty($aArgs[ 'taxonomy' ]) ? 'category' : $aArgs[ 'taxonomy' ];
        $_sID = "{$aArgs[ '_input_id_prefix' ]}_{$_sTaxonomySlug}_{$_iID}";
        $_sPostCount = $aArgs[ 'show_post_count' ] ? " <span class='font-lighter'>(" . $oTerm->count . ")</span>" : '';
        $_aInputAttributes = isset($_aInputAttributes[ $_iID ]) ? $_aInputAttributes[ $_iID ] + $aArgs[ '_attributes' ] : $aArgs[ '_attributes' ];
        $_aInputAttributes = array( 'id' => $_sID, 'value' => 1, 'type' => 'checkbox', 'name' => "{$aArgs[ '_name_prefix' ]}[{$_iID}]", 'checked' => in_array($_iID, ( array ) $aArgs[ '_selected_items' ]) ? 'checked' : null, ) + $_aInputAttributes + array( 'class' => null, );
        $_aInputAttributes['class'] .= ' apf_checkbox';
        $_aLiTagAttributes = array( 'id' => "list-{$_sID}", 'class' => 'category-list', 'title' => $oTerm->description, );
        $_sHiddenInputForUnchecked = $aArgs[ '_save_unchecked' ] ? "<input value='0' type='hidden' name='" . $_aInputAttributes[ 'name' ] . "' class='apf_checkbox' />" : '';
        $sOutput .= "\n" . "<li " . AdminPageFramework_WPUtility::getAttributes($_aLiTagAttributes) . ">" . "<label for='{$_sID}' class='taxonomy-checklist-label'>" . $_sHiddenInputForUnchecked . "<input " . AdminPageFramework_WPUtility::getAttributes($_aInputAttributes) . " />" . esc_html(apply_filters('the_category', $oTerm->name)) . $_sPostCount . "</label>";
    }
}
