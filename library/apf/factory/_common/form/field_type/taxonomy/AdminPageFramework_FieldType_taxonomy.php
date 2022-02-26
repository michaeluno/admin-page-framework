<?php
/*
 * Admin Page Framework v3.9.0b17 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_FieldType_taxonomy extends AdminPageFramework_FieldType_checkbox {
    public $aFieldTypeSlugs = array( 'taxonomy', );
    protected $aDefaultKeys = array( 'taxonomy_slugs' => 'category', 'height' => '250px', 'width' => null, 'max_width' => '100%', 'show_post_count' => true, 'attributes' => array(), 'select_all_button' => true, 'select_none_button' => true, 'label_no_term_found' => null, 'label_list_title' => '', 'query' => array( 'child_of' => 0, 'parent' => '', 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false, 'hierarchical' => true, 'number' => '', 'pad_counts' => false, 'exclude' => array(), 'exclude_tree' => array(), 'include' => array(), 'fields' => 'all', 'slug' => '', 'get' => '', 'name__like' => '', 'description__like' => '', 'offset' => '', 'search' => '', 'cache_domain' => 'core', ), 'queries' => array(), 'save_unchecked' => true, );
    protected function getEnqueuingScripts()
    {
        return array( array( 'handle_id' => 'admin-page-framework-field-type-taxonomy', 'src' => dirname(__FILE__) . '/js/taxonomy.bundle.js', 'in_footer' => true, 'dependencies' => array( 'jquery', 'admin-page-framework-script-form-main' ), 'translation_var' => 'AdminPageFrameworkFieldTypeTaxonomy', 'translation' => array( 'fieldTypeSlugs' => $this->aFieldTypeSlugs, 'label' => array(), ), ), );
    }
    protected function getField($aField)
    {
        $aField[ 'label_no_term_found' ] = $this->getElement($aField, 'label_no_term_found', $this->oMsg->get('no_term_found'));
        $_aTabs = array();
        $_aCheckboxes = array();
        foreach ($this->getAsArray($aField[ 'taxonomy_slugs' ]) as $_isKey => $_sTaxonomySlug) {
            $_aAssociatedDataAttributes = $this->___getDataAttributesOfAssociatedPostTypes($_sTaxonomySlug, $this->___getPostTypesByTaxonomySlug($_sTaxonomySlug));
            $_aTabs[] = $this->___getTaxonomyTab($aField, $_isKey, $_sTaxonomySlug, $_aAssociatedDataAttributes);
            $_aCheckboxes[] = $this->___getTaxonomyCheckboxes($aField, $_isKey, $_sTaxonomySlug, $_aAssociatedDataAttributes);
        }
        return "<div id='tabbox-{$aField['field_id']}' class='tab-box-container categorydiv' style='max-width:{$aField['max_width']};'>" . "<ul class='tab-box-tabs category-tabs'>" . implode(PHP_EOL, $_aTabs) . "</ul>" . "<div class='tab-box-contents-container'>" . "<div class='tab-box-contents' style='height: {$aField['height']};'>" . implode(PHP_EOL, $_aCheckboxes) . "</div>" . "</div>" . "</div>" ;
    }
    private function ___getPostTypesByTaxonomySlug($sTaxonomySlug)
    {
        $_oTaxonomy = get_taxonomy($sTaxonomySlug);
        return $_oTaxonomy->object_type;
    }
    private function ___getDataAttributesOfAssociatedPostTypes($sTaxonomySlusg, $aPostTypes)
    {
        return array( 'data-associated-with' => $sTaxonomySlusg, 'data-associated-post-types' => implode(',', $aPostTypes) . ',', );
    }
    private function ___getTaxonomyCheckboxes(array $aField, $sKey, $sTaxonomySlug, $aAttributes)
    {
        $_aTabBoxContainerArguments = array( 'id' => "tab_{$aField['input_id']}_{$sKey}", 'class' => 'tab-box-content', 'style' => $this->getInlineCSS(array( 'height' => $this->getAOrB($aField[ 'height' ], $this->getLengthSanitized($aField[ 'height' ]), null), 'width' => $this->getAOrB($aField[ 'width' ], $this->getLengthSanitized($aField[ 'width' ]), null), )), ) + $aAttributes;
        return "<div " . $this->getAttributes($_aTabBoxContainerArguments) . ">" . $this->getElement($aField, array( 'before_label', $sKey )) . "<div " . $this->getAttributes($this->_getCheckboxContainerAttributes($aField)) . ">" . "</div>" . "<ul class='list:category taxonomychecklist form-no-clear'>" . $this->___getTaxonomyChecklist($aField, $sKey, $sTaxonomySlug) . "</ul>" . "<!--[if IE]><b>.</b><![endif]-->" . $this->getElement($aField, array( 'after_label', $sKey )) . "</div><!-- tab-box-content -->";
    }
    private function ___getTaxonomyChecklist($aField, $sKey, $sTaxonomySlug)
    {
        return wp_list_categories(array( 'walker' => new AdminPageFramework_WalkerTaxonomyChecklist, 'taxonomy' => $sTaxonomySlug, '_name_prefix' => is_array($aField[ 'taxonomy_slugs' ]) ? "{$aField[ '_input_name' ]}[{$sTaxonomySlug}]" : $aField[ '_input_name' ], '_input_id_prefix' => $aField[ 'input_id' ], '_attributes' => $this->getElementAsArray($aField, array( 'attributes', $sKey )) + $aField[ 'attributes' ], '_selected_items' => $this->___getSelectedKeyArray($aField['value'], $sTaxonomySlug), 'echo' => false, 'show_post_count' => $aField[ 'show_post_count' ], 'show_option_none' => $aField[ 'label_no_term_found' ], 'title_li' => $aField[ 'label_list_title' ], '_save_unchecked' => $aField[ 'save_unchecked' ], ) + $this->getAsArray($this->getElement($aField, array( 'queries', $sTaxonomySlug ), array()), true) + $aField[ 'query' ]);
    }
    private function ___getSelectedKeyArray($vValue, $sTaxonomySlug)
    {
        $_aSelected = $this->getElementAsArray($this->getAsArray($vValue), array( $sTaxonomySlug ));
        return array_keys($_aSelected, true);
    }
    private function ___getTaxonomyTab($aField, $sKey, $sTaxonomySlug, $aAttributes)
    {
        $_aLiAttributes = array( 'class' => 'tab-box-tab', ) + $aAttributes;
        return "<li " . $this->getAttributes($_aLiAttributes) . ">" . "<a href='#tab_{$aField['input_id']}_{$sKey}'>" . "<span class='tab-box-tab-text'>" . $this->___getLabelFromTaxonomySlug($sTaxonomySlug) . "</span>" ."</a>" ."</li>";
    }
    private function ___getLabelFromTaxonomySlug($sTaxonomySlug)
    {
        $_oTaxonomy = get_taxonomy($sTaxonomySlug);
        return isset($_oTaxonomy->label) ? $_oTaxonomy->label : '';
    }
}
