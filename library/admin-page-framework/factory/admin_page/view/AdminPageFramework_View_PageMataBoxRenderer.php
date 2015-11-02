<?php
class AdminPageFramework_View_PageMataBoxRenderer extends AdminPageFramework_WPUtility {
    public function render($sContext) {
        static $_iContainerID = 1;
        $_sCurrentScreenID = $this->getCurrentScreenID();
        $_aMetaBoxes = $this->getElementAsArray($GLOBALS, array('wp_meta_boxes', $_sCurrentScreenID, $sContext), array());
        if (count($_aMetaBoxes) <= 0) {
            return;
        }
        echo "<div id='postbox-container-{$_iContainerID}' class='postbox-container'>";
        do_meta_boxes('', $sContext, null);
        echo "</div>";
        $_iContainerID++;
    }
}