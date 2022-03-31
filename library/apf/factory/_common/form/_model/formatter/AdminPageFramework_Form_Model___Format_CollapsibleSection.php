<?php
/*
 * Admin Page Framework v3.9.1b04 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Form_Model___Format_CollapsibleSection extends AdminPageFramework_FrameworkUtility {
    public static $aStructure = array( 'title' => null, 'is_collapsed' => true, 'toggle_all_button' => null, 'collapse_others_on_expand' => true, 'container' => 'sections', 'type' => 'box', );
    public $abCollapsible = false;
    public $sTitle = '';
    public $aSection = array();
    public function __construct()
    {
        $_aParameters = func_get_args() + array( $this->abCollapsible, $this->sTitle, $this->aSection, );
        $this->abCollapsible = $_aParameters[ 0 ];
        $this->sTitle = $_aParameters[ 1 ];
        $this->aSection = $_aParameters[ 2 ];
    }
    public function get()
    {
        if (empty($this->abCollapsible)) {
            return $this->abCollapsible;
        }
        return $this->_getArguments($this->abCollapsible, $this->sTitle, $this->aSection);
    }
    private function _getArguments($abCollapsible, $sTitle, array $aSection)
    {
        $_aCollapsible = $this->getAsArray($this->abCollapsible) + array( 'title' => $sTitle, ) + self::$aStructure;
        $_aCollapsible[ 'toggle_all_button' ] = implode(',', $this->getAsArray($_aCollapsible[ 'toggle_all_button' ]));
        if (! empty($aSection)) {
            $_aCollapsible[ 'toggle_all_button' ] = $this->_getToggleAllButtonArgument($_aCollapsible[ 'toggle_all_button' ], $aSection);
        }
        $_aCollapsible[ 'toggle_all_button' ] = $this->getAOrB('' === $_aCollapsible[ 'toggle_all_button' ], false, $_aCollapsible[ 'toggle_all_button' ]);
        return $_aCollapsible;
    }
    private function _getToggleAllButtonArgument($sToggleAll, array $aSection)
    {
        if (! $aSection[ 'repeatable' ]) {
            return $sToggleAll;
        }
        if ($aSection[ '_is_first_index' ] && $aSection[ '_is_last_index' ]) {
            return $sToggleAll;
        }
        if (! $aSection[ '_is_first_index' ] && ! $aSection[ '_is_last_index' ]) {
            return 0;
        }
        $_aToggleAll = $this->getAOrB(true === $sToggleAll || 1 === $sToggleAll, array( 'top-right', 'bottom-right' ), explode(',', $sToggleAll));
        $_aToggleAll = $this->getAOrB($aSection[ '_is_first_index' ], $this->dropElementByValue($_aToggleAll, array( 1, true, 0, false, 'bottom-right', 'bottom-left' )), $_aToggleAll);
        $_aToggleAll = $this->getAOrB($aSection[ '_is_last_index' ], $this->dropElementByValue($_aToggleAll, array( 1, true, 0, false, 'top-right', 'top-left' )), $_aToggleAll);
        $_aToggleAll = $this->getAOrB(empty($_aToggleAll), array( 0 ), $_aToggleAll);
        return implode(',', $_aToggleAll);
    }
}
