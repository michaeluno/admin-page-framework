<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Handles the contact form submissions.
 *
 * @since           3.6.3
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 */
class AdminPageFramework_Model__FormEmailHandler extends AdminPageFramework_WPUtility {
    
    /**
     * Stores the factory object.
     */
    public $oFactory;

    /**
     * Sets up properties.
     * @since       3.6.3
     */
    public function __construct( $oFactory ) {
       
        $this->oFactory         = $oFactory;        

        // Form emails.
        if ( ! isset( $_GET[ 'apf_action' ], $_GET[ 'transient' ] ) ) {
            return;
        }
        if ( 'email' !== $_GET[ 'apf_action' ] ) {
            return;
        }
      
        // Set the server not to abort even the client browser terminates.
        ignore_user_abort( true );
        
        // wp_mail() will be loaded by the time 'after_setup_theme' is loaded.
        $this->registerAction( 'after_setup_theme', array( $this, '_replyToSendFormEmail' ) );

    }   
    
        /**
         * Indicates whether the email method is triggered or not.
         * 
         * Since multiple factory instances can load the constructor, it is possible that the method is called multiple times.
         * 
         * @since       3.4.2
         * @since       3.6.3       Moved from `AdminPageFramework_Form_Model`.
         */
        static public $_bDoneEmail = false;
        
        /**
         * Sends a form email.
         * 
         * This should be called only in the background.
         * 
         * @since       3.4.2
         * @since       3.6.3       Moved from `AdminPageFramework_Form_Model`.
         * @callback    action      after_setup_theme
         */
        public function _replyToSendFormEmail() {

            if ( self::$_bDoneEmail ) {
                return;
            }
            self::$_bDoneEmail = true;

            $_sTransient = $this->getElement( $_GET, 'transient', '' );
            if ( ! $_sTransient ) {
                return;
            }
            $_aFormEmail = $this->getTransient( $_sTransient );
            $this->deleteTransient( $_sTransient );
            if ( ! is_array( $_aFormEmail ) ) {
                return;
            }

            $_oEmail = new AdminPageFramework_FormEmail( 
                $_aFormEmail[ 'email_options' ], 
                $_aFormEmail[ 'input' ], 
                $_aFormEmail[ 'section_id' ] 
            );
            $_bSent = $_oEmail->send();

            exit;
            
        }
    
}