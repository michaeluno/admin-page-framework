<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to handle field errors.
 *
 * @package     AdminPageFramework/Common/Form
 * @since       3.7.0
 * @extends     AdminPageFramework_FrameworkUtility
 * @internal
 */
class AdminPageFramework_Form___FieldError extends AdminPageFramework_FrameworkUtility {

    /**
     * Stores field errors.
     *
     * At the script termination, these will be saved as a transient in the database.
     */
    static private $_aErrors = array();

    public $sCallerID;

    public $sTransientKey;

    /**
     * Sets up properties.
     */
    public function __construct( $sCallerID ) {

        $this->sCallerID = $sCallerID;
        $this->sTransientKey = $this->_getTransientKey();

    }
        /**
         * @remark      Up to 40 chars
         * @return      string
         */
        private function _getTransientKey() {
            $_sPageNow  = $this->getPageNow();
            $_sPageSlug = $this->getElement( $_GET, 'page', '' );
            $_sPageSlug = sanitize_text_field( $_sPageSlug );
            $_sTabSlug  = $this->getElement( $_GET, 'tab', '' );
            $_sTabSlug  = sanitize_text_field( $_sTabSlug );
            $_sUserID   = get_current_user_id();
            return "apf_fe_" . md5(
                $_sPageNow
                . $_sPageSlug
                . $_sTabSlug
                . $_sUserID
            );
        }

    /**
     * Checks if a field error exists for the caller (factory class).
     *
     * @return      boolean     Whether or not a field error exists.
     * @since       3.7.0
     */
    public function hasError() {
        return isset( self::$_aErrors[ $this->sCallerID ] );
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

        // For the first time of calling the method, schedule to set the data in the transient.
        if ( empty( self::$_aErrors ) ) {
            add_action( 'shutdown', array( $this, '_replyToSave' ) );
        }

        // Merge with previously set errors.
        self::$_aErrors[ $this->sCallerID ] = isset( self::$_aErrors[ $this->sCallerID ] )
            ? $this->uniteArrays(
                self::$_aErrors[ $this->sCallerID ],
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
        public function _replyToSave() {
            if ( empty( self::$_aErrors ) ) {
                return;
            }
            $this->setTransient(
                $this->sTransientKey, // "apf_field_erros_" . get_current_user_id(),
                self::$_aErrors,
                300     // for 5 minutes ( 60 seconds * 5 )
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

        // Use a cache if exists.
        self::$_aFieldErrorCaches[ $this->sTransientKey ]  = isset( self::$_aFieldErrorCaches[ $this->sTransientKey ] )
            ? self::$_aFieldErrorCaches[ $this->sTransientKey ]
            : $this->getTransient( $this->sTransientKey );

        return $this->getElementAsArray(
            self::$_aFieldErrorCaches[ $this->sTransientKey ],
            $this->sCallerID,
            array()
        );
    }
        private static $_aFieldErrorCaches = array();

    /**
     * Deletes the field errors from the database.
     * @since       3.7.0
     */
    public function delete() {
        if ( $this->hasBeenCalled( 'delete_' . $this->sTransientKey ) ) {
            return;
        }
        add_action( 'shutdown', array( $this, '_replyToDelete' ) );
    }
        /**
         * Deletes the field errors transient.
         *
         * @since       3.0.4
         * @callback    action      shutdown
         * @since       3.7.0      Moved from `AdminPageFramework_Factory_Model`.
         * @internal
         */
        public function _replyToDelete() {
            $this->deleteTransient( $this->sTransientKey );
        }

}
