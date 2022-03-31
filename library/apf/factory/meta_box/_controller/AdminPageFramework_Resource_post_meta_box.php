<?php
/*
 * Admin Page Framework v3.9.1b04 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Resource_post_meta_box extends AdminPageFramework_Resource_Base {
    protected function _enqueueSRCByCondition($aEnqueueItem)
    {
        $_sCurrentPostType = isset($_GET[ 'post_type' ]) ? $this->getHTTPQueryGET('post_type') : (isset($GLOBALS[ 'typenow' ]) ? $GLOBALS[ 'typenow' ] : null);
        if (empty($aEnqueueItem[ 'aPostTypes' ])) {
            $this->_enqueueSRC($aEnqueueItem);
            return;
        }
        if (in_array($_sCurrentPostType, $aEnqueueItem[ 'aPostTypes' ], true)) {
            $this->_enqueueSRC($aEnqueueItem);
        }
    }
}
