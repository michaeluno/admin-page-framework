<?php 
/**
	Admin Page Framework v3.8.31b01 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AdminPageFramework_Resource_post_meta_box extends AdminPageFramework_Resource_Base {
    protected function _enqueueSRCByCondition($aEnqueueItem) {
        $_sCurrentPostType = isset($_GET['post_type']) ? $_GET['post_type'] : (isset($GLOBALS['typenow']) ? $GLOBALS['typenow'] : null);
        if (in_array($_sCurrentPostType, $aEnqueueItem['aPostTypes'], true)) {
            $this->_enqueueSRC($aEnqueueItem);
        }
    }
    }
    