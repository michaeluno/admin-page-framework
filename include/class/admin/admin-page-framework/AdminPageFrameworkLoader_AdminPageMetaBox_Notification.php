<?php
/**
 * Admin Page Framework - Loader
 *
 * Loads Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno
 *
 */

/**
 *
 */
class AdminPageFrameworkLoader_AdminPageMetaBox_Notification extends AdminPageFramework_PageMetaBox {

    /*
     * ( optional ) Use the setUp() method to define settings of this meta box.
     */
    public function setUp() {
        $this->oUtil->registerAction(
            'current_screen',
            array( $this, 'replyToDecideToLoad' ),
            1
        );
    }

    public $_sDevelopmentVersion;

    /**
     * @param       $oScreen
     * @callback    action      current_screen
     */
    public function replyToDecideToLoad( /* $oScreen */ ) {

        if ( ! $this->_isInThePage() ) {
            return;
        }

        // For debugging, uncomment the below line to remove the transient.
//         $this->oUtil->deleteTransient(
//             AdminPageFrameworkLoader_Registry::TRANSIENT_PREFIX . 'devver'
//         );

        // Retrieve the development version.
        $this->_sDevelopmentVersion = $this->oUtil->getTransient(
            AdminPageFrameworkLoader_Registry::TRANSIENT_PREFIX . 'devver'
        );

        // Disable the meta box if the development version is not above the running one.
        if ( version_compare( AdminPageFramework_Registry::VERSION, $this->_sDevelopmentVersion, '>=' ) ) {
            $this->oProp->aPageSlugs = array();
        }

        // If the value is not set, schedule retrieving the version.
        if ( empty( $this->_sDevelopmentVersion ) ) {
            $this->_scheduleEvent();
        }

    }
        /**
         * @since       3.6.2
         */
        private function _scheduleEvent() {

            $_sActionName = AdminPageFrameworkLoader_Registry::HOOK_SLUG . '_action_get_development_version';
            $_aArguments  = array();
            if ( wp_next_scheduled( $_sActionName, $_aArguments ) ) {
                return false;
            }
            wp_schedule_single_event(
                time(),
                $_sActionName,
                $_aArguments
            );

        }
    /**
     * The content filter callback method.
     *
     * Alternatively use the `content_{instantiated class name}` method instead.
     */
    public function content( $sContent ) {

        $_sInsert = ''
            . "<h4>"
                . "<span class='header-icon dashicons dashicons-warning'></span>"
                . __( 'Test Development Version', 'admin-page-framework-loader' )
            . "</h4>"
            . "<p class='new-version-notification'>"
                . sprintf(
                    __( 'A new development version <code>%1$s</code> is available!', 'admin-page-framework-loader' )
                    . ' '
                    . __( 'Please test it before it gets released.', 'admin-page-framework-loader' ),
                    $this->_sDevelopmentVersion,
                    esc_url( 'https://github.com/michaeluno/admin-page-framework/archive/dev.zip' )
                )
            . "</p>"
            . "<div style='width:100%; display:inline-block;'>"
                . '<a href="' . esc_url( 'https://github.com/michaeluno/admin-page-framework/archive/dev.zip' ). '">'
                    . "<div class='button button-primary float-right'>"
                        . __( 'Download', 'admin-page-framework-loader' )
                    . "</div>"
                . "</a>"
            . "</div>"
            ;

        return $_sInsert . $sContent;

    }

}
