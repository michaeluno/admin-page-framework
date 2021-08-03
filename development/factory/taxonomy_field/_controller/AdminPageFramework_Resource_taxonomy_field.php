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
 * This is for custom taxonomy pages added by the framework.
 *
 * @since       3.0.0
 * @since       3.3.0       Changed the name from AdminPageFramework_HeadTag_TaxonomyField.
 * @package     AdminPageFramework/Factory/TaxonomyField/Resource
 * @extends     AdminPageFramework_Resource_post_meta_box
 * @internal
 */
class AdminPageFramework_Resource_taxonomy_field extends AdminPageFramework_Resource_post_meta_box {

    /**
     * A helper function for the _replyToEnqueueScripts() and _replyToEnqueueStyle() methods.
     *
     * @since       3.0.0
     * @since       3.7.0      Fixed a typo in the method name.
     * @remark      the taxonomy page is checked in the constructor, so there is no need to apply a condition.
     * @internal
     */
    protected function _enqueueSRCByCondition( $aEnqueueItem ) {
        $this->_enqueueSRC( $aEnqueueItem );
    }

}