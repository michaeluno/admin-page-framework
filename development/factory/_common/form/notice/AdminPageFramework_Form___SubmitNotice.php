<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to handle setting notices.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.7.0
 * @extends     AdminPageFramework_FrameworkUtility
 */
class AdminPageFramework_Form___SubmitNotice extends AdminPageFramework_FrameworkUtility {
    
    /**
     * Stores form submit notifications.
     * 
     * At the script termination, these will be saved as a transient in the database.
     */
    static private $_aNotices = array();
    
    public $sTransientKey;
    
    /**
     * Sets up properties.
     */
    public function __construct() {
        
        $this->sTransientKey = $this->_getTransientKey();
        
    }
        /**
         * @remark      Up to 40 chars
         * @remark      Not using the page-now value nor page slug because the user may be redirected to a different page.
         * @return      string
         */
        private function _getTransientKey() {
            return 'apf_ntc_' . get_current_user_id();
        }
    
    /**
     * Checks if a submit notice has been set in the current screen (not in the database).
     * 
     * This is used in the internal validation callback method to decide whether the system error or update notice should be added or not.
     * If this method yields true, the framework discards the system message and displays the user set notification message.
     * 
     * @param       string      $sType If empty, the method will check if a message exists in all types. Otherwise, it checks the existence of a message of the specified type.
     * @return      boolean     True if a setting notice is set; otherwise, false.
     */
    public function hasNotice( $sType='' ) {
                
        if ( ! $sType ) {
            return ( boolean ) count( self::$_aNotices );
        }
        
        // Check if there is a message of the type.
        foreach( self::$_aNotices as $_aNotice ) {
            $_sClassAttribute = $this->getElement( 
                $_aNotice, 
                array( 
                    'aAttributes', 
                    'class' 
                ),
                ''
            );
            if ( $_sClassAttribute === $sType ) {
                return true;
            }
        }
        return false;
        
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
    public function set( $sMessage, $sType='error', $asAttributes=array(), $bOverride=true ) {
        
        // If the array is empty, schedule the task of saving the array at shutdown.
        if ( empty( self::$_aNotices ) ) {
            add_action( 'shutdown', array( $this, '_replyToSaveNotices' ) ); // the method is defined in the model class.
        }
        
        $_sID = md5( trim( $sMessage ) );
            
        // If the override is false and a message is already set, do not add.
        if ( ! $bOverride && isset( self::$_aNotices[ $_sID ] ) ) {
            return;
        }

        // If the override is true, reset the notice array.
        if ( $bOverride ) {
            self::$_aNotices = array();
        }
        
        $_aAttributes = $this->getAsArray( $asAttributes );
        if ( is_string( $asAttributes ) && ! empty( $asAttributes ) ) {
            $_aAttributes[ 'id' ] = $asAttributes;
        }
        self::$_aNotices[ $_sID ] = array(
            'sMessage'      => $sMessage,
            'aAttributes'   => $_aAttributes + array(
                'class'     => $sType,
                'id'        => 'form_submit_notice_' . $_sID,
            ),
        );


    }       
        /**
         * Saves the notification array set via the setSettingNotice() method.
         * 
         * @since       3.0.4 
         * @sine        3.7.0      Moved from `AdminPageFramework_Factory_Model`.
         * @internal
         * @callback    action     shutdown
         * @return      void
         */
        public function _replyToSaveNotices() {
            if ( empty( self::$_aNotices ) ) { 
                return; 
            }            
            $_bResult = $this->setTransient( 
                $this->sTransientKey, 
                self::$_aNotices
            );
        }        
    
    /**
     * Outputs the stored submit notices in the database.
     * @return      void
     */
    public function render() {

        // This will load scripts for the fade-in effect.
        new AdminPageFramework_AdminNotice( '' );
        
        // Retrieve the notifications set in a transient.
        $_aNotices = $this->_getNotices();
        if ( false === $_aNotices ) { 
            return; 
        }
            
        // By setting false to the 'settings-notice' key, it's possible to disable the notifications set with the framework.
        if ( isset( $_GET[ 'settings-notice' ] ) && ! $_GET[ 'settings-notice' ] ) { 
            return; 
        }
            
        $this->_printNotices( $_aNotices );                
        
    }
        /**
         * @since       3.7.8
         * @return      array|boolean
         */
        private function _getNotices() {
        
            if ( isset( self::$_aNoticeCaches[ $this->sTransientKey ] ) ) {
                return self::$_aNoticeCaches[ $this->sTransientKey ];
            }

            $_abNotices = $this->getTransient( $this->sTransientKey );            
            if ( false !== $_abNotices ) {
                $this->deleteTransient( $this->sTransientKey );
            }

            self::$_aNoticeCaches[ $this->sTransientKey ] = $_abNotices;
            return self::$_aNoticeCaches[ $this->sTransientKey ];
            
        }
            static private $_aNoticeCaches = array();
            
        /**
         * Displays settings notices.
         * @since       3.5.3
         * @since       3.7.0      Moved from `AdminPageFramework_Factory_View`. Renamed from `_printSettingNotices()`.
         * @internal
         * @return      void
         */
        private function _printNotices( $aNotices ) {
            
            $_aPeventDuplicates = array();
            foreach ( array_filter( ( array ) $aNotices, 'is_array' ) as $_aNotice ) {
                
                $_sNotificationKey = md5( serialize( $_aNotice ) );
                if ( isset( $_aPeventDuplicates[ $_sNotificationKey ] ) ) {
                    continue;
                }
                $_aPeventDuplicates[ $_sNotificationKey ] = true;
                
                new AdminPageFramework_AdminNotice(
                    $this->getElement( $_aNotice, 'sMessage' ),
                    $this->getElement( $_aNotice, 'aAttributes' )
                );              
              
            }            
            
        }    
        
}
