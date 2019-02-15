<?php
/**
 * @package         Admin Page Framework Loader
 * @copyright       Copyright (c) 2013-2019, Michael Uno
 * @license         http://opensource.org/licenses/gpl-2.0.php GNU Public License
*/

/**
 * Retrieves an array of RSS feed items.
 * @since       DEVVER
 */
class AdminPageFrameworkLoader_Utility {

    /**
     * Checks if the loader runs on the silent mode or not.
     * @return      boolean
     * @since       DEVVER
     */
    static public function isSilentMode() {
        return defined( 'APFL_SILENT_MODE' ) && APFL_SILENT_MODE;
    }

    /**
     * Caleld for upon a redirect failure.
     * @return      void
     * @since       DEVVER
     */
    static public function replyToShowRedirectError( $iType, $sURL ) {

        $_aErrors = array(
            1 => sprintf(
                __( 'The URL is not valid: %1$s', 'admin-page-framework-loader' ),
                $sURL
            ),
            2 => __( 'Header already sent.', 'admin-page-framework-loader' ),
        );
        if ( ! class_exists( 'AdminPageFramework_AdminNotice' ) ) {
            return;
        }
        new AdminPageFramework_AdminNotice(
            $_aErrors[ $iType ]
                . ' '
                . sprintf(
                    __( 'Could not be redirected to %1$s.', 'admin-page-framework-loader' ),
                    $sURL
                )
        );

    }



}
