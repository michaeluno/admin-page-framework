<?php 
/**
	Admin Page Framework v3.9.0b10 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
abstract class AdminPageFramework_MetaBox_Controller extends AdminPageFramework_MetaBox_View {
    public function setUp() {
    }
    public function enqueueStyles() {
        $_aParams = func_get_args() + array(array(), array(), array());
        return $this->oResource->_enqueueResourcesByType($_aParams[0], array('aPostTypes' => empty($_aParams[1]) ? $this->oProp->aPostTypes : $_aParams[1],) + $_aParams[2], 'style');
    }
    public function enqueueStyle() {
        $_aParams = func_get_args() + array('', array(), array());
        return $this->oResource->_addEnqueuingResourceByType($_aParams[0], array('aPostTypes' => empty($_aParams[1]) ? $this->oProp->aPostTypes : $_aParams[1],) + $_aParams[2], 'style');
    }
    public function enqueueScripts() {
        $_aParams = func_get_args() + array(array(), array(), array());
        return $this->oResource->_enqueueResourcesByType($_aParams[0], array('aPostTypes' => empty($_aParams[1]) ? $this->oProp->aPostTypes : $_aParams[1],) + $_aParams[2], 'script');
    }
    public function enqueueScript() {
        $_aParams = func_get_args() + array('', array(), array());
        return $this->oResource->_addEnqueuingResourceByType($_aParams[0], array('aPostTypes' => empty($_aParams[1]) ? $this->oProp->aPostTypes : $_aParams[1],) + $_aParams[2], 'script');
    }
    }
    