<?php 
/**
	Admin Page Framework v3.8.23 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2020, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
abstract class AdminPageFramework_Form_View___CSS_Base extends AdminPageFramework_FrameworkUtility {
    public $aAdded = array();
    public function add($sCSSRules) {
        $this->aAdded[] = $sCSSRules;
    }
    public function get() {
        $_sCSSRules = $this->_get() . PHP_EOL;
        $_sCSSRules.= $this->_getVersionSpecific();
        $_sCSSRules.= implode(PHP_EOL, $this->aAdded);
        return $_sCSSRules;
    }
    protected function _get() {
        return '';
    }
    protected function _getVersionSpecific() {
        return '';
    }
    }
    class AdminPageFramework_Form_View___CSS_CollapsibleSection extends AdminPageFramework_Form_View___CSS_Base {
        protected function _get() {
            return $this->___getCollapsibleSectionsRules();
        }
        private function ___getCollapsibleSectionsRules() {
            $_sCSSRules = ".admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box, .admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box{font-size:13px;background-color: #fff;padding: 1em 2.6em 1em 2em;border-top: 1px solid #eee;border-bottom: 1px solid #eee;}.admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box.collapsed.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box.collapsed {border-bottom: 1px solid #dfdfdf;margin-bottom: 1em; }.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box {margin-top: 0;}.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box.collapsed {margin-bottom: 0;}#poststuff .admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box.admin-page-framework-section-title > .section-title-outer-container > .section-title-container > .section-title,#poststuff .admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box.admin-page-framework-section-title > .section-title-outer-container > .section-title-container > .section-title{font-size: 1em;margin: 0 1em 0 0; }#poststuff .admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box.admin-page-framework-section-title > .section-title-outer-container > .section-title-container > fieldset {line-height: 0; }#poststuff .admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box.admin-page-framework-section-title > .section-title-outer-container > .section-title-container > fieldset .admin-page-framework-field {margin: 0;padding: 0;}.admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box.accordion-section-title:after,.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box.accordion-section-title:after {top: 0.88em;top: 34%;right: 15px;}.admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box.accordion-section-title:after,.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box.accordion-section-title:after {content: '\\f142';}.admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box.accordion-section-title.collapsed:after,.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box.accordion-section-title.collapsed:after {content: '\\f140';} .admin-page-framework-collapsible-sections-content.admin-page-framework-collapsible-content.accordion-section-content,.admin-page-framework-collapsible-section-content.admin-page-framework-collapsible-content.accordion-section-content,.admin-page-framework-collapsible-sections-content.admin-page-framework-collapsible-content-type-box, .admin-page-framework-collapsible-section-content.admin-page-framework-collapsible-content-type-box{border: 1px solid #dfdfdf;border-top: 0;background-color: #fff;}tbody.admin-page-framework-collapsible-content {display: table-caption; padding: 10px 20px 15px 20px;}tbody.admin-page-framework-collapsible-content.table-caption {display: table-caption; }.admin-page-framework-collapsible-toggle-all-button-container {margin-top: 1em;margin-bottom: 1em;width: 100%;display: table; }.admin-page-framework-collapsible-toggle-all-button.button {height: 36px;line-height: 34px;padding: 0 16px 6px;font-size: 20px;width: auto;}.flipped > .admin-page-framework-collapsible-toggle-all-button.button.dashicons {-moz-transform: scaleY(-1);-webkit-transform: scaleY(-1);transform: scaleY(-1);filter: flipv; }.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .admin-page-framework-repeatable-section-buttons {margin: 0; }.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .admin-page-framework-repeatable-section-buttons.section_title_field_sibling {margin-top: 0;}.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .repeatable-section-button {background: none; line-height: 1.8em; margin: 0;padding: 0;width: 2em;height: 2em;text-align: center;}.admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box .section-title-height-fixer, .admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .section-title-height-fixer {height: 100%;width: 0;display: inline-block;vertical-align: middle;}.admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box .section-title-outer-container, .admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .section-title-outer-container {width: 88%;display: inline-block;text-align: left;vertical-align: middle;}.admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box .admin-page-framework-repeatable-section-buttons-outer-container,.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .admin-page-framework-repeatable-section-buttons-outer-container {width: 10.88%;min-width: 60px; display: inline-block;text-align: right;vertical-align: middle;}@media only screen and ( max-width: 782px ) {.admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box .section-title-outer-container, .admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .section-title-outer-container {width: 87.8%;}}.accordion-section-content.admin-page-framework-collapsible-content-type-button {background-color: transparent;}.admin-page-framework-collapsible-button {color: #888;margin-right: 0.4em;font-size: 0.8em;}.admin-page-framework-collapsible-button-collapse {display: inline;} .collapsed .admin-page-framework-collapsible-button-collapse {display: none;}.admin-page-framework-collapsible-button-expand {display: none;}.collapsed .admin-page-framework-collapsible-button-expand {display: inline;}.admin-page-framework-collapsible-section-title .admin-page-framework-fields {display: inline;vertical-align: middle; line-height: 1em; }.admin-page-framework-collapsible-section-title .admin-page-framework-field {float: none;}.admin-page-framework-collapsible-section-title .admin-page-framework-fieldset {display: inline;margin-right: 1em;vertical-align: middle; }#poststuff .admin-page-framework-collapsible-title.admin-page-framework-collapsible-section-title .section-title-container.has-fields .section-title{width: auto;display: inline-block;margin: 0 1em 0 0.4em;vertical-align: middle;}";
            $_sCSSRules.= $this->___getForWP38OrBelow();
            $_sCSSRules.= $this->___getForWP53OrAbove();
            return $_sCSSRules;
        }
        private function ___getForWP53OrAbove() {
            if (version_compare($GLOBALS['wp_version'], '5.3', '<')) {
                return '';
            }
            return ".admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .repeatable-section-button {min-width: 2.4em;}.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .repeatable-section-button .dashicons {font-size: 1.2em;height: 100%;vertical-align: text-top;}.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .repeatable-section-button .dashicons:before {vertical-align: middle;}@media screen and (max-width: 782px) {.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .admin-page-framework-repeatable-section-buttons {white-space: nowrap;}.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .repeatable-section-button {font-size: 1.4em;}.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .repeatable-section-button .dashicons {height: unset;vertical-align: unset;}}";
        }
        private function ___getForWP38OrBelow() {
            if (version_compare($GLOBALS['wp_version'], '3.8', '>=')) {
                return '';
            }
            return ".admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box.accordion-section-title:after,.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box.accordion-section-title:after {content: '';top: 18px;}.admin-page-framework-collapsible-sections-title.admin-page-framework-collapsible-type-box.accordion-section-title.collapsed:after,.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box.accordion-section-title.collapsed:after {content: '';} .admin-page-framework-collapsible-toggle-all-button.button {font-size: 1em;}.admin-page-framework-collapsible-section-title.admin-page-framework-collapsible-type-box .admin-page-framework-repeatable-section-buttons {top: -8px;}";
        }
    }
    