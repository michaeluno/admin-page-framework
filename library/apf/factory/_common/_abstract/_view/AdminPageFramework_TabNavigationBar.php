<?php
/*
 * Admin Page Framework v3.9.1 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_TabNavigationBar extends AdminPageFramework_FrameworkUtility {
    public $sTabTag = 'h2';
    public $aTabs = array();
    public $aActiveSlugs;
    public $aAttributes = array( 'class' => 'nav-tab-wrapper', );
    public $aTab = array( 'slug' => null, 'title' => null, 'href' => null, 'disabled' => null, 'class' => null, 'attributes' => array(), );
    public $aCallbacks = array( 'format' => null, 'arguments' => array(), );
    public function __construct(array $aTabs, $asActiveTabSlugs, $sTabTag='h2', $aAttributes=array( 'class' => 'nav-tab-wrapper', ), $aCallbacks=array())
    {
        $this->aCallbacks = $aCallbacks + array( 'format' => null, 'arguments' => null, );
        $this->aTabs = $this->_getFormattedTabs($aTabs);
        $this->aActiveSlugs = $this->getAsArray($asActiveTabSlugs);
        $this->sTabTag = $sTabTag ? tag_escape($sTabTag) : $this->sTabTag;
        $this->aAttributes = $aAttributes;
    }
    private function _getFormattedTabs(array $aTabs)
    {
        foreach ($aTabs as $_isKey => &$_aTab) {
            $_aFormattedTab = $this->_getFormattedTab($_aTab, $aTabs);
            if (isset($_aFormattedTab[ 'slug' ])) {
                $_aTab = $_aFormattedTab;
            } else {
                unset($aTabs[ $_isKey ]);
            }
        }
        return $aTabs;
    }
    private function _getFormattedTab(array $aTab, array $aTabs)
    {
        $aTab = is_callable($this->aCallbacks[ 'format' ]) ? call_user_func_array($this->aCallbacks[ 'format' ], array( $aTab, $this->aTab, $aTabs, $this->aCallbacks[ 'arguments' ] )) : $aTab;
        if (isset($aTab[ 'attributes' ], $this->aTab[ 'attributes' ])) {
            $aTab[ 'attributes' ] = $aTab[ 'attributes' ] + $this->aTab[ 'attributes' ];
        }
        return $aTab + $this->aTab;
    }
    public function get()
    {
        return $this->_getTabs();
    }
    private function _getTabs()
    {
        $_aOutput = array();
        foreach ($this->aTabs as $_aTab) {
            $_sTab = $this->_getTab($_aTab);
            if (! $_sTab) {
                continue;
            }
            $_aOutput[] = $_sTab;
        }
        $_aContainerAttributes = $this->aAttributes + array( 'class' => null );
        $_aContainerAttributes[ 'class' ] = $this->getClassAttribute('nav-tab-wrapper', $_aContainerAttributes[ 'class' ]);
        return empty($_aOutput) ? '' : "<{$this->sTabTag} " . $this->getAttributes($_aContainerAttributes) . ">" . implode('', $_aOutput) . "</{$this->sTabTag}>";
    }
    private function _getTab(array $aTab)
    {
        $_aATagAttributes = isset($aTab[ 'attributes' ]) ? $aTab[ 'attributes' ] : array();
        $_sClassAttribute = $this->getClassAttribute('nav-tab', $this->getElement($aTab, 'class', ''), $this->getElement($_aATagAttributes, 'class', ''), $this->getAOrB(in_array($aTab[ 'slug' ], $this->aActiveSlugs), "nav-tab-active", ''), $this->getAOrB($aTab[ 'disabled' ], 'tab-disabled', ''));
        $_aATagAttributes = array( 'class' => $_sClassAttribute, ) + $_aATagAttributes + array( 'href' => $aTab[ 'href' ], 'title' => $aTab[ 'title' ], );
        if ($aTab[ 'disabled' ]) {
            unset($_aATagAttributes[ 'href' ]);
        }
        return $this->getHTMLTag('a', $_aATagAttributes, $aTab[ 'title' ]);
    }
}
