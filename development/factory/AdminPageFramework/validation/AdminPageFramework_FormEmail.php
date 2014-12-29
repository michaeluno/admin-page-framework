<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Sends email as a part of form validation.
 *   
 * Usage: instantiate the class and perform the `send()` method.
 *
 * @abstract
 * @since       3.4.2
 * @package     AdminPageFramework
 * @subpackage  Setting
 * @internal
 */
class AdminPageFramework_FormEmail extends AdminPageFramework_WPUtility {
     
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
        
        // @todo underscore local variables.
        $aEmailOptions      = $this->aEmailOptions;
        $aInput             = $this->aInput;
        $sSubmitSectionID   = $this->sSubmitSectionID;
        
        // Set up callbacks for arguments which cannot be set in the wp_mail() function.
        if ( $_bIsHTML = $this->_getEmailArgument( $aInput, $aEmailOptions, 'is_html', $sSubmitSectionID ) ) {
            add_filter( 'wp_mail_content_type', array( $this, '_replyToSetMailContentTypeToHTML' ) );
        }
 
        if ( $this->_sEmailSenderAddress = $this->_getEmailArgument( $aInput, $aEmailOptions, 'from', $sSubmitSectionID ) ) {
            add_filter( 'wp_mail_from', array( $this, '_replyToSetEmailSenderAddress' ) );
        }
        if ( $this->_sEmailSenderName = $this->_getEmailArgument( $aInput, $aEmailOptions, 'name', $sSubmitSectionID ) ) {
            add_filter( 'wp_mail_from_name', array( $this, '_replyToSetEmailSenderAddress' ) );
        }

        // Send mail.
        $_bSent         = wp_mail( 
            $this->_getEmailArgument( $aInput, $aEmailOptions, 'to', $sSubmitSectionID ),
            $this->_getEmailArgument( $aInput, $aEmailOptions, 'subject', $sSubmitSectionID ),
            $_bIsHTML 
                ? $this->getReadableListOfArrayAsHTML( ( array ) $this->_getEmailArgument( $aInput, $aEmailOptions, 'message', $sSubmitSectionID ) )
                : $this->getReadableListOfArray( ( array ) $this->_getEmailArgument( $aInput, $aEmailOptions, 'message', $sSubmitSectionID ) ),
            $this->_getEmailArgument( $aInput, $aEmailOptions, 'headers', $sSubmitSectionID ),
            $this->_formatAttachements( $this->_getEmailArgument( $aInput, $aEmailOptions, 'attachments', $sSubmitSectionID ) )
        );         
        
        remove_filter( 'wp_mail_content_type', array( $this, '_replyToSetMailContentTypeToHTML' ) );
        remove_filter( 'wp_mail_from', array( $this, '_replyToSetEmailSenderAddress' ) );
        remove_filter( 'wp_mail_from_name', array( $this, '_replyToSetEmailSenderAddress' ) );
        
        // Clean up.
        foreach( $this->_aPathsToDelete as $_sPath ) {
            unlink( $_sPath );
        }
        
        return $_bSent;
        
    }
        /**
         * Formats the attachement values.
         * 
         * If a url is passed, it attemps to convert it to a path. If it is an external url, it downloads it and set it as a path.
         * 
         * @since       3.4.2
         */
        private function _formatAttachements( $asAttachments ) {
            
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
                    if ( $_sPath = $this->_getPathFromURL( $_sPathORURL ) ) {
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
            private function _getPathFromURL( $sURL ) {
                
                // If it is on the server, this works.
                $_sPath = realpath( str_replace( get_bloginfo( 'url' ), ABSPATH, $sURL ) );
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
        private function _getEmailArgument( $aInput, array $aEmailOptions, $sKey, $sSectionID ) {
            
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