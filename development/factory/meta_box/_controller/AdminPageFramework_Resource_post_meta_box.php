<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * {@inheritdoc}
 *
 * {@inheritdoc}
 *
 * This is for post definition pages that have meta box fields added by the framework.
 *
 * @since       2.1.5
 * @since       3.3.0       Changed the name from AdminPageFramework_HeadTag_MetaBox.
 * @use         AdminPageFramework_Utility
 * @package     AdminPageFramework/Factory/MetaBox/Resource
 * @extends     AdminPageFramework_Resource_Base
 * @internal
 */
class AdminPageFramework_Resource_post_meta_box extends AdminPageFramework_Resource_Base {

    /**
     * A helper function for the _replyToEnqueueScripts() and the _replyToEnqueueStyle() methods.
     *
     * @since       2.1.5
     * @since       3.7.0      Fixed a typo in the method name.
     * @param       array      $aEnqueueItem
     * @internal
     */
    protected function _enqueueSRCByCondition( $aEnqueueItem ) {
        $_sCurrentPostType = isset( $_GET[ 'post_type' ] ) ? $_GET[ 'post_type' ] : ( isset( $GLOBALS[ 'typenow' ] ) ? $GLOBALS[ 'typenow' ] : null );
        if ( in_array( $_sCurrentPostType, $aEnqueueItem[ 'aPostTypes' ], true ) ) {
            $this->_enqueueSRC( $aEnqueueItem );
        }
    }

}