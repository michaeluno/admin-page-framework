<?php
abstract class AdminPageFramework_MetaBox_Controller extends AdminPageFramework_MetaBox_View {
    public function setUp() {
    }
    public function enqueueStyles($aSRCs, $aPostTypes = array(), $aCustomArgs = array()) {
        return $this->oResource->_enqueueStyles($aSRCs, $aPostTypes, $aCustomArgs);
    }
    public function enqueueStyle($sSRC, $aPostTypes = array(), $aCustomArgs = array()) {
        return $this->oResource->_enqueueStyle($sSRC, $aPostTypes, $aCustomArgs);
    }
    public function enqueueScripts($aSRCs, $aPostTypes = array(), $aCustomArgs = array()) {
        return $this->oResource->_enqueueScripts($aSRCs, $aPostTypes, $aCustomArgs);
    }
    public function enqueueScript($sSRC, $aPostTypes = array(), $aCustomArgs = array()) {
        return $this->oResource->_enqueueScript($sSRC, $aPostTypes, $aCustomArgs);
    }
}