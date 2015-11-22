<?php
class AdminPageFramework_PointerToolBox extends AdminPageFramework_WPUtility {
    static private $_bResourceLoaded = false;
    public $sPointerID;
    public $aPointerData;
    public $aScreenIDs = array();
    public function __construct($asScreenIDs, $sPointerID, array $aPointerData) {
        if (version_compare($GLOBALS['wp_version'], '3.3', '<')) {
            return false;
        }
        if ('admin-page-framework-pointer-tool-box' === $this->getElement($_GET, 'script')) {
            exit($this->_renderScript());
        }
        $this->aScreenIDs = $this->getAsArray($asScreenIDs);
        $this->sPointerID = $sPointerID;
        $this->aPointerData = $aPointerData;
        $this->_setHooks($this->aScreenIDs);
        if (!$this->_shouldProceed()) {
            return;
        }
        add_action('admin_enqueue_scripts', array($this, '_replyToLoadPointer'), 1000);
    }
    private function _setHooks($aScreenIDs) {
        foreach ($aScreenIDs as $_sScreenID) {
            if (!$_sScreenID) {
                continue;
            }
            add_filter(get_class($this) . '-' . $_sScreenID, array($this, '_replyToSetPointer'));
        }
    }
    private function _shouldProceed() {
        if (self::$_bResourceLoaded) {
            return false;
        }
        self::$_bResourceLoaded = true;
        return true;
    }
    public function _replyToSetPointer($aPointers) {
        return array($this->sPointerID => $this->aPointerData) + $aPointers;
    }
    public function _replyToLoadPointer() {
        $_aPointers = $this->_getPointers();
        if (empty($_aPointers) || !is_array($_aPointers)) {
            return;
        }
        $this->_loadScripts($this->_getValidPointers($_aPointers));
    }
    private function _getPointers() {
        $_oScreen = get_current_screen();
        $_sScreenID = $_oScreen->id;
        if (in_array($_sScreenID, $this->aScreenIDs)) {
            return apply_filters(get_class($this) . '-' . $_sScreenID, array());
        }
        if (isset($_GET['page'])) {
            return apply_filters(get_class($this) . '-' . $_GET['page'], array());
        }
        return array();
    }
    private function _getValidPointers($_aPointers) {
        $_aDismissed = explode(',', ( string )get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true));
        $_aValidPointers = array('pointers' => array(),);
        foreach ($_aPointers as $_iPointerID => $_aPointer) {
            $_aPointer = $_aPointer + array('target' => null, 'options' => null, 'pointer_id' => null,);
            if ($this->_shouldSkip($_iPointerID, $_aDismissed, $_aPointer)) {
                continue;
            }
            $_aPointer['pointer_id'] = $_iPointerID;
            $_aValidPointers['pointers'][] = $_aPointer;
        }
        return $_aValidPointers;
    }
    private function _shouldSkip($_iPointerID, $_aDismissed, $_aPointer) {
        if (in_array($_iPointerID, $_aDismissed)) {
            return true;
        }
        if (empty($_aPointer)) {
            return true;
        }
        if (empty($_iPointerID)) {
            return true;
        }
        if (empty($_aPointer['target'])) {
            return true;
        }
        if (empty($_aPointer['options'])) {
            return true;
        }
        return false;
    }
    private function _loadScripts($_aValidPointers) {
        if (empty($_aValidPointers)) {
            return;
        }
        wp_enqueue_script('jquery');
        wp_enqueue_style('wp-pointer');
        wp_enqueue_script('admin-page-framework-pointer', add_query_arg(array('script' => 'admin-page-framework-pointer-tool-box'), admin_url()), array('wp-pointer'));
        wp_localize_script('admin-page-framework-pointer', 'AdminPageFrameworkPointerToolBoxes', $_aValidPointers);
    }
    public function _renderScript() {
        echo $this->_getScript();
    }
    public function _getScript() {
        return <<<JAVASCRIPTS
( function( $ ) {
jQuery( document ).ready( function( $ ) {
    
    $.each( AdminPageFrameworkPointerToolBoxes.pointers, function( iIndex, _aPointer ) {
        
        var _aOptions = $.extend( _aPointer.options, {
            close: function() {
                $.post( ajaxurl, {
                    pointer: _aPointer.pointer_id,
                    action: 'dismiss-wp-pointer'
                });
            }
        });
 
        $( _aPointer.target ).pointer( _aOptions ).pointer( 'open' );

    });
});
}( jQuery ));
JAVASCRIPTS;
        
    }
}