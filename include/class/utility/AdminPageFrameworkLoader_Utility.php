<?php
/**
 * @package         Admin Page Framework Loader
 * @copyright       Copyright (c) 2015, Michael Uno
 * @license         http://opensource.org/licenses/gpl-2.0.php GNU Public License
*/

/** 
 * Retrieves an array of RSS feed items.
 * @since       DEVVER
 */
class AdminPageFrameworkLoader_Utility {

    /**
     * Caleld for upon a redirect failure.
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