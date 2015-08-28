<?php
abstract class AdminPageFramework_MetaBox_Page_Controller extends AdminPageFramework_MetaBox_Page_View {
    public function enqueueStyles($aSRCs, $sPageSlug = '', $sTabSlug = '', $aCustomArgs = array()) {
        if (method_exists($this->oResource, '_enqueueStyles')) {
            return $this->oResource->_enqueueStyles($aSRCs, $sPageSlug, $sTabSlug, $aCustomArgs);
        }
    }
    public function enqueueStyle($sSRC, $sPageSlug = '', $sTabSlug = '', $aCustomArgs = array()) {
        if (method_exists($this->oResource, '_enqueueStyle')) {
            return $this->oResource->_enqueueStyle($sSRC, $sPageSlug, $sTabSlug, $aCustomArgs);
        }
    }
    public function enqueueScripts($aSRCs, $sPageSlug = '', $sTabSlug = '', $aCustomArgs = array()) {
        if (method_exists($this->oResource, '_enqueueScripts')) {
            return $this->oResource->_enqueueScripts($sSRC, $sPageSlug, $sTabSlug, $aCustomArgs);
        }
    }
    public function enqueueScript($sSRC, $sPageSlug = '', $sTabSlug = '', $aCustomArgs = array()) {
        if (method_exists($this->oResource, '_enqueueScript')) {
            return $this->oResource->_enqueueScript($sSRC, $sPageSlug, $sTabSlug, $aCustomArgs);
        }
    }
}