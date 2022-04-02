<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to display setting notices.
 *
 * @since    3.7.0
 * @package  AdminPageFramework/Common/Factory
 * @internal
 */
class AdminPageFramework_Factory_View__SettingNotice extends AdminPageFramework_FrameworkUtility {

    public $oFactory;

    /**
     * Sets up hooks and properties.
     *
     * @since 3.7.0
     * @since 3.7.9 Added the second parameter to accept an action hook name.
     */

    public function __construct( $oFactory, $sActionHookName='admin_notices' ) {
        $this->oFactory = $oFactory;
        add_action( $sActionHookName, array( $this, '_replyToPrintSettingNotice' ) );
    }

    /**
     * Displays stored setting notification messages.
     *
     * @since    3.0.4
     * @since    3.7.0        Moved from `AdminPageFramework_Factory_View`.
     * @internal
     * @callback add_action() network_admin_notices
     * @callback add_action() admin_notices
     */
    public function _replyToPrintSettingNotice() {
        if ( ! $this->___shouldProceed() ) {
            return;
        }
        $this->oFactory->oForm->printSubmitNotices();
    }

        /**
         * Determines whether to proceed.
         * @sine   3.7.0
         * @return boolean
         */
        private function ___shouldProceed() {

            if ( ! $this->oFactory->isInThePage() ) {
                return false;
            }

            // Ensure this method is called only once per a page load.
            if ( $this->hasBeenCalled( __METHOD__ ) ) {
                return false;
            }

            // @deprecated 3.9.1 Post type classes don't use the form object but can set setting-notices. And this below check prevents messages from being shown.
            // Some factory classes including the page meta box factory can leave the form object uninstantiated.
            // return isset( $this->oFactory->oForm );

            return true;

        }

}
