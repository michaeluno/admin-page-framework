<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to handle field errors.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.7.0
 * @extends     AdminPageFramework_FrameworkUtility
 */
class AdminPageFramework_Form___FieldError extends AdminPageFramework_FrameworkUtility {
    
    /**
     * Stores field errors.
     * 
     * At the script termination, these will be saved as a transient in the database.
     */
    static private $_aErrors = array();
    
    public $sCallerID;
    
    /**
     * Sets up properties.
     */
    public function __construct( $sCallerID ) {
        
        $this->sCallerID = $sCallerID;
        
    }
    
    /**
     * Checks if a field error exists for the caller (factory class).
     * 
     * @return      boolean     Whether or not a field error exists.
     * @since       3.7.0
     */
    public function hasError() {
        return isset( self::$_aErrors[ md5( $this->sCallerID ) ] );
    }
    
    /**
     * Sets the given message to be displayed in the next page load. 
     * 
     * This is used to inform users about the submitted input data, such as "Updated successfully." or "Problem occurred." etc. 
     * and normally used in validation callback methods.
     * 
     * <h4>Example</h4>
     * `
     * if ( ! $bVerified ) {
     *       $this->setFieldErrors( $aErrors );     
     *       $this->setSettingNotice( 'There was an error in your input.' );
     *       return $aOldPageOptions;
     * }
     * `
     * @since        3.7.0
     * @access       public
     * @param        string      $sMessage       the text message to be displayed.
     * @param        string      $sType          (optional) the type of the message, either "error" or "updated"  is used.
     * @param        array       $asAttributes   (optional) the tag attribute array applied to the message container HTML element. If a string is given, it is used as the ID attribute value.
     * @param        boolean     $bOverride      (optional) If true, only one message will be shown in the next page load. false: do not override when there is a message of the same id. true: override the previous one.
     * @return       void
     */
    public function set( $aErrors ) {
              
        if ( empty( self::$_aErrors ) ) {
            add_action( 'shutdown', array( $this, '_replyToSaveFieldErrors' ) ); 
        }
        
        $_sID = md5( $this->sCallerID );
        self::$_aErrors[ $_sID ] = isset( self::$_aErrors[ $_sID ] )
            ? $this->uniteArrays( 
                self::$_aErrors[ $_sID ], 
                $aErrors 
            )
            : $aErrors; 

    }     
        /**
         * Saves the field error array into the transient (database options row).
         * 
         * @since       3.0.4
         * @since       3.7.0      Moved from `AdminPageFramework_Factory_Model`.
         * @internal
         * @callback    action      shutdown
         * @return      void
         */ 
        public function _replyToSaveFieldErrors() {
            if ( ! isset( self::$_aErrors ) ) { 
                return; 
            }
            $this->setTransient( 
                "apf_field_erros_" . get_current_user_id(),  
                self::$_aErrors, 
                300     // store it for 5 minutes ( 60 seconds * 5 )
            );    
        }    
    
    /**
     * Returns the saved field errors.
     * 
     * Retrieves the settings error array set by the user in the validation callback.
     * 
     * @since       3.7.0
     * @param       boolean     $bDelete    whether or not the transient should be deleted after retrieving it. 
     * @return      array
     */
    public function get() {
        
        static $_aFieldErrors;
        
        // Find the transient.
        $_sTransientKey = "apf_field_erros_" . get_current_user_id();
        $_sID           = md5( $this->sCallerID );
        
        $_aFieldErrors  = isset( $_aFieldErrors ) 
            ? $_aFieldErrors 
            : $this->getTransient( $_sTransientKey );
                    
        return $this->getElementAsArray(
            $_aFieldErrors,
            $_sID,
            array()
        );
    }
    
    /**
     * Deletes the field errors from the database.
     * @since       3.7.0
     */
    public function delete() {
        add_action( 'shutdown', array( $this, '_replyToDeleteFieldErrors' ) );
    }
        /**
         * Deletes the field errors transient.
         * 
         * @since       3.0.4
         * @callback    action      shutdown
         * @since       3.7.0      Moved from `AdminPageFramework_Factory_Model`.
         * @internal
         */
        public function _replyToDeleteFieldErrors() {
            $this->deleteTransient( "apf_field_erros_" . get_current_user_id() );
        }                
        
}