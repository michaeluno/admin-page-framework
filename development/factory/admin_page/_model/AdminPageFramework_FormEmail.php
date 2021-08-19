<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Sends email as a part of form validation.
 *
 * Usage: instantiate the class and perform the `send()` method.
 *
 * @abstract
 * @since       3.4.2
 * @package     AdminPageFramework/Factory/AdminPage/Form
 * @internal
 * @extends     AdminPageFramework_FrameworkUtility
 */
class AdminPageFramework_FormEmail extends AdminPageFramework_FrameworkUtility {

    /**
     * Stores email options.
     * @since       3.4.2
     */
    public $aEmailOptions = array();

    /**
     * Stores form submit data
     * @since       3.4.2
     */
    public $aInput = array();

    /**
     * Stores the section ID of the email form.
     * @since       3.4.2
     */
    public $sSubmitSectionID;

    /**
     * Stores file paths to be deleted.
     *
     * Used for download external attached files.
     *
     * @sicne       3.4.2
     */
    private $_aPathsToDelete = array();

    private $_sEmailSenderAddress;
    private $_sEmailSenderName;

    /**
     * Sets up properties.
     */
    public function __construct( array $aEmailOptions, array $aInput, $sSubmitSectionID ) {

        $this->aEmailOptions    = $aEmailOptions;
        $this->aInput           = $aInput;
        $this->sSubmitSectionID = $sSubmitSectionID;
        $this->_aPathsToDelete  = array();

    }

    /**
     * Sends email(s) based on the set settings in the constructor.
     *
     * @since       3.4.2
     * @return      boolean     Whether a mail has been sent or not.
     */
    public function send() {

        $_aEmailOptions     = $this->aEmailOptions;
        $_aInputs           = $this->aInput;
        $_sSubmitSectionID  = $this->sSubmitSectionID;

        /**
         * Allows the user to hook into the hooks used in wp_mail() including the `wp_mail` action hook.
         * @since 3.9.0
         * @see wp_mail()
         * @param array  $_aEmailOptions The email arguments set in the `contact` field argument.
         * @param array  $_aInputs The user form inputs.
         * @param string $_sSubmitSectionID The section ID that the `contact` field belongs to.
         */
        do_action( 'admin-page-framework_action_before_sending_form_email', $_aEmailOptions, $_aInputs, $_sSubmitSectionID );

        // Set up callbacks for arguments which cannot be set in the wp_mail() function.
        if ( $_bIsHTML = $this->___getEmailArgument( $_aInputs, $_aEmailOptions, 'is_html', $_sSubmitSectionID ) ) {
            add_filter( 'wp_mail_content_type', array( $this, '_replyToSetMailContentTypeToHTML' ) );
        }

        if ( $this->_sEmailSenderAddress = $this->___getEmailArgument( $_aInputs, $_aEmailOptions, 'from', $_sSubmitSectionID ) ) {
            add_filter( 'wp_mail_from', array( $this, '_replyToSetEmailSenderAddress' ) );
        }
        if ( $this->_sEmailSenderName = $this->___getEmailArgument( $_aInputs, $_aEmailOptions, 'name', $_sSubmitSectionID ) ) {
            add_filter( 'wp_mail_from_name', array( $this, '_replyToSetEmailSenderName' ) );
        }

        // Send mail.
        $_bSent         = wp_mail(
            $this->___getEmailArgument( $_aInputs, $_aEmailOptions, 'to', $_sSubmitSectionID ),
            $this->___getEmailArgument( $_aInputs, $_aEmailOptions, 'subject', $_sSubmitSectionID ),
            $_bIsHTML
                ? $this->getReadableListOfArrayAsHTML( ( array ) $this->___getEmailArgument( $_aInputs, $_aEmailOptions, 'message', $_sSubmitSectionID ) )
                : $this->getReadableListOfArray( ( array ) $this->___getEmailArgument( $_aInputs, $_aEmailOptions, 'message', $_sSubmitSectionID ) ),
            $this->___getEmailArgument( $_aInputs, $_aEmailOptions, 'headers', $_sSubmitSectionID ),
            $this->___getAttachmentsFormatted( $this->___getEmailArgument( $_aInputs, $_aEmailOptions, 'attachments', $_sSubmitSectionID ) )
        );

        // Clean up.
        foreach( $this->_aPathsToDelete as $_sPath ) {
            unlink( $_sPath );
        }

        /**
         * Allows the user to clean up things after performing `wp_email()`.
         * @since 3.9.0
         * @see wp_mail()
         * @param boolean $_bSent         Whether the email is sent or not.
         * @param array   $_aEmailOptions The email arguments set in the `contact` field argument.
         */
        do_action( 'admin-page-framework_action_after_sending_form_email', $_bSent, $_aEmailOptions);

        // Remove the filter callbacks after the above action to let the action callbacks use the custom Email options modified with these filters.
        remove_filter( 'wp_mail_content_type', array( $this, '_replyToSetMailContentTypeToHTML' ) );
        remove_filter( 'wp_mail_from', array( $this, '_replyToSetEmailSenderAddress' ) );
        remove_filter( 'wp_mail_from_name', array( $this, '_replyToSetEmailSenderAddress' ) );

        return $_bSent;

    }
        /**
         * Formats the attachment values.
         *
         * If a url is passed, it attempts to convert it to a path. If it is an external url, it downloads it and set it as a path.
         *
         * @since       3.4.2
         */
        private function ___getAttachmentsFormatted( $asAttachments ) {

            if ( empty( $asAttachments ) ) {
                return '';
            }

            $_aAttachments = $this->getAsArray( $asAttachments );
            foreach( $_aAttachments as $_iIndex => $_sPathORURL ) {

                // If it is a file path, fine.
                if ( is_file( $_sPathORURL ) ) {
                    continue;
                }

                // If it is a url, convert it to a path or download it.
                if ( false !== filter_var( $_sPathORURL, FILTER_VALIDATE_URL ) ) {
                    if ( $_sPath = $this->___getPathFromURL( $_sPathORURL ) ) {
                        $_aAttachments[ $_iIndex ] = $_sPath;
                        continue;
                    }
                }

                // At this point, it is not either path or url.
                unset( $_aAttachments[ $_iIndex ] );

            }

            return $_aAttachments;

        }
            /**
             * Attempts to convert the url into a path. Otherwise, downloads the file.
             *
             * @since       3.4.2
             */
            private function ___getPathFromURL( $sURL ) {

                // If it is on the server, this works.
                $_sPath = $this->___getPathFromURLWithinSite( $sURL );
                if ( $_sPath ) {
                    return $_sPath;
                }

                // Download the file. It returns a string value on success. WP Error object on failure.
                $_sPath = $this->download( $sURL, 10 );
                if ( is_string( $_sPath ) ) {
                    $this->_aPathsToDelete[ $_sPath ] = $_sPath;
                    return $_sPath;
                }

                return '';

            }
                /**
                 * @return string|false
                 * @since 3.8.31
                 */
                private function ___getPathFromURLWithinSite( $sURL ) {
                    $_sPath = realpath( str_replace(
                        content_url(),
                        WP_CONTENT_DIR,
                        $sURL
                    ) );
                    if ( $_sPath ) {
                        return $_sPath;
                    }
                    return realpath( str_replace(
                        get_bloginfo( 'url' ),
                        ABSPATH,
                        $sURL
                    ) );
                }
       /**
         * Sets the mail content type to HTML.
         * @since       3.3.0
         * @since       3.4.2       Moved from the validation class.
         */
        public function _replyToSetMailContentTypeToHTML( $sContentType ) {
            return 'text/html';
        }
        /**
         * Sets the email sender address.
         *
         * @since       3.3.0
         * @since       3.4.2       Moved from the validation class.
         */
        function _replyToSetEmailSenderAddress( $sEmailSenderAddress ) {
            return $this->_sEmailSenderAddress;
        }
        /**
         * Sets the email sender name.
         *
         * @since       3.3.0
         * @since       3.4.2       Moved from the validation class.
         */
        function _replyToSetEmailSenderName( $sEmailSenderAddress ) {
            return $this->_sEmailSenderName;
        }


        /**
         * Returns the email argument value by the given key.
         *
         * If the element is a string, pass it as it is. If it is an array representing the dimensional key structure, retrieve it from the input array.
         *
         * @since       3.3.0
         * @since       3.4.2       Moved from the validation class.
         */
        private function ___getEmailArgument( $aInput, array $aEmailOptions, $sKey, $sSectionID ) {

            // If the dimensional key representation array is passed, find the value from the given input array.
            if ( is_array( $aEmailOptions[ $sKey ] ) ) {
                return $this->getArrayValueByArrayKeys( $aInput, $aEmailOptions[ $sKey ] );
            }

            // If the key element is empty, search the corresponding item in the same section.
            if ( ! $aEmailOptions[ $sKey ] ) {
                return $this->getArrayValueByArrayKeys( $aInput, array( $sSectionID, $sKey ) );
            }

            // At this point, a string value should be set.
            return $aEmailOptions[ $sKey ];

        }

}