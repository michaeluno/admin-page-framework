<?php
class AdminPageFramework_Form_View___Script_Base extends AdminPageFramework_WPUtility {
    static public $_aEnqueued = array();
    public function __construct($oMsg = null) {
        $_sClassName = get_class($this);
        if (in_array($_sClassName, self::$_aEnqueued)) {
            return;
        }
        self::$_aEnqueued[$_sClassName] = $_sClassName;
        $this->oMsg = $oMsg;
        add_action('customize_controls_print_footer_scripts', array($this, '_replyToPrintScript'));
        add_action('admin_footer', array($this, '_replyToPrintScript'));
        $this->construct();
    }
    protected function construct() {
    }
    public function _replyToPrintScript() {
        $_sScript = $this->getScript($this->oMsg);
        if (!$_sScript) {
            return;
        }
        echo "<script type='text/javascript' class='" . strtolower(get_class($this)) . "'>" . $_sScript . "</script>";
    }
    static public function getScript() {
        $_aParams = func_get_args() + array(null);
        $_oMsg = $_aParams[0];
        return "";
    }
}