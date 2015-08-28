<?php
abstract class AdminPageFramework_UserMeta_Controller extends AdminPageFramework_UserMeta_View {
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