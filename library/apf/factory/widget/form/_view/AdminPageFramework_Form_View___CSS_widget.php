<?php
/*
 * Admin Page Framework v3.9.0b19 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Form_View___CSS_widget extends AdminPageFramework_Form_View___CSS_Base {
    protected function _get()
    {
        return $this->_getWidgetRules();
    }
    private function _getWidgetRules()
    {
        return <<<CSSRULES
.widget .admin-page-framework-section .form-table>tbody>tr>td,.widget .admin-page-framework-section .form-table>tbody>tr>th{display:inline-block;width:100%;padding:0;float:right;clear:right}.widget .admin-page-framework-field,.widget .admin-page-framework-input-label-container{width:100%}.widget .sortable .admin-page-framework-field{padding:4% 4.4% 3.2% 4.4%;width:91.2%}.widget .admin-page-framework-field input{margin-bottom:.1em;margin-top:.1em}.widget .admin-page-framework-field input[type=text],.widget .admin-page-framework-field textarea{width:100%}@media screen and (max-width:782px){.widget .admin-page-framework-fields{width:99.2%}.widget .admin-page-framework-field input[type='checkbox'],.widget .admin-page-framework-field input[type='radio']{margin-top:0}}
CSSRULES;
    }
    protected function _getVersionSpecific()
    {
        $_sCSSRules = '';
        if (version_compare($GLOBALS[ 'wp_version' ], '3.8', '<')) {
            $_sCSSRules .= <<<CSSRULES
.widget .admin-page-framework-section table.mceLayout{table-layout:fixed}
CSSRULES;
        }
        if (version_compare($GLOBALS[ 'wp_version' ], '3.8', '>=')) {
            $_sCSSRules .= <<<CSSRULES
.widget .admin-page-framework-section .form-table th{font-size:13px;font-weight:400;margin-bottom:.2em}.widget .admin-page-framework-section .form-table{margin-top:1em}
CSSRULES;
        }
        return $_sCSSRules;
    }
}
