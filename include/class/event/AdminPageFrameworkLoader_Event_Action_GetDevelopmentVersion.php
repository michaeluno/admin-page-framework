<?php
/**
 * Loads Admin Page Framework loader plugin components.
 *
 * @package      Admin Page Framework Loader
 * @copyright    Copyright (c) 2013-2019, Michael Uno
 * @author       Michael Uno
 * @authorurl    http://michaeluno.jp
 *
 */

/**
 * Performs an action.
 *
 * @since       3.5.2
 */
class AdminPageFrameworkLoader_Event_Action_GetDevelopmentVersion {

    /**
     * Stores the url of the text that has the development version number.
     */
    public $sVersionTextURL = 'https://raw.githubusercontent.com/michaeluno/admin-page-framework/dev/admin-page-framework-loader.php';

    /**
     * Performs the action.
     *
     * @since       3.6.2
     */
    public function __construct( $sActionName ) {
        add_action(
            $sActionName,
            array( $this, 'replyToDoAction' )
        );
    }

    /**
     * @callback        action      admin_page_framework_loader_action_get_development_version
     * @return          void
     */
    public function replyToDoAction() {
        AdminPageFramework_WPUtility::setTransient(
            AdminPageFrameworkLoader_Registry::TRANSIENT_PREFIX . 'devver',
            $this->___getVersion(), // data - if an error occurs, an empty string will be given
            604800 // for one week
        );
    }
        /**
         * Extracts the version number from the page contents.
         *
         * @return      string
         */
        private function ___getVersion() {

            $_oUtil     = new AdminPageFramework_WPUtility;
            $_aHeaders  = $_oUtil->getScriptData(
                $this->___getPageBody(),
                '',  /// context
                array( 'version' => 'Version' )
            );
            return $_oUtil->getElement(
                $_aHeaders, // subject array
                'version', // dimensional keys
                ''  // default
            );

        }
            /**
             * @return      string
             */
            private function ___getPageBody() {
                $_mResponse = wp_remote_get(
                    $this->sVersionTextURL,
                    array(
                        //  3.7  or later, it should be true
                        'sslverify'  => version_compare( $GLOBALS[ 'wp_version' ], '3.7', '>=' )
                    )
                );
                if ( is_wp_error( $_mResponse ) ) {
                    return '';
                }
                return wp_remote_retrieve_body( $_mResponse );
            }

}
