<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to build forms of the `page_meta_box` structure type.
 *
 * The suffix represents the structure type of the form.
 *
 * @package     AdminPageFramework/Factory/PageMetaBox/Form
 * @since       3.7.0
 * @extends     AdminPageFramework_Form
 * @internal
 */
class AdminPageFramework_Form_page_meta_box extends AdminPageFramework_Form_post_meta_box {

    public $sStructureType = 'page_meta_box';

    /**
     * Does set-ups.
     * @since       3.7.0
     * @return      void
     */
    public function construct() {

        add_filter(
            'options_' . $this->aArguments[ 'caller_id' ],
            array( $this, '_replyToSanitizeSavedFormData' ),
            5   //  high priority as it must be done earlier
        );

        // The post_meta_box parent method adds default CSS resources.
        parent::construct();

    }

    /**
     * Sanitizes the set form data for the page meta box.
     *
     * By default, the set form data (options) which belongs to the page will be returned.
     * This means it includes data that is nothing to do with the fields added to this page meta box.
     *
     * @remark      Assumes the user already adds items to `$aFieldsets` property by the time this method is triggered.
     * @callback    filter      options_{caller id}
     * @return      array       The sanitized saved form data.
     */
    public function _replyToSanitizeSavedFormData( $aSavedFormData ) {

        // Extract the meta box field form data (options) from the page form data (options).
        return $this->castArrayContents(
            $this->getDataStructureFromAddedFieldsets(),    // form data structure generate from fieldsets
            $aSavedFormData
        );

    }


}
