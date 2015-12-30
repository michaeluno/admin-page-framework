<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to handle last inputs of form data.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.7.8
 * @extends     AdminPageFramework_FrameworkUtility
 */
class AdminPageFramework_Form_Model___LastInput extends AdminPageFramework_FrameworkUtility {
    
    /**
     * Stores last inputs.
     * 
     * At the script termination, these will be saved as a transient in the database.
     */
    static private $_aLastInputs = array();
    
    /**
     * The caller id. 
     * Usually a class name.
     */
    public $sCallerID;
    
    /**
     * The transient key. 
     * 
     * The key is generated with the current page base name.
     * This means the data is stored per page bases and user last inputs are stored in element by the caller id.
     */
    public $sTransientKey;
    
    /**
     * Sets up properties.
     */
    public function __construct( $sCallerID ) {
        
        $this->sCallerID = $sCallerID;
       
        // 40 chars (8 chars + 32 chars)
        $this->sTransientKey = 'apf_tfd_' . md5( $this->getPageNow() . get_current_user_id() );

    }
    
    /**
     * Sets the given last inputs.
     * 
     * @since        3.7.8
     * @return       void
     */
    public function set( $aLastInputs ) {
              
        if ( empty( self::$_aLastInputs ) ) {
            add_action( 'shutdown', array( $this, '_replyToSave' ) ); 
        }
        
        $_sID = $this->sCallerID;
        self::$_aLastInputs[ $_sID ] = isset( self::$_aLastInputs[ $_sID ] )
            ? $this->uniteArrays( 
                self::$_aLastInputs[ $_sID ], 
                $aLastInputs 
            )
            : $aLastInputs; 

    }     
        /**
         * Saves the data into the transient (database options row).
         * 
         * @since       3.7.8     
         * @internal
         * @callback    action      shutdown
         * @return      void
         */ 
        public function _replyToSave() {
            if ( ! isset( self::$_aLastInputs ) ) { 
                return; 
            }
            $this->setTransient( 
                $this->sTransientKey,
                self::$_aLastInputs, 
                60*60     // store it for 1 hour 
            );    
        }    
    
    /**
     * Returns the saved field errors.
     * 
     * Retrieves the settings error array set by the user in the validation callback.
     * 
     * @since       3.7.8
     * @param       boolean     $bDelete    whether or not the transient should be deleted after retrieving it. 
     * @return      array
     */
    public function get() {
        
        // Use a cache if exists.
        if ( isset( self::$_aCaches[ $this->sTransientKey ] ) ) {
            $_aLastInputs = self::$_aCaches[ $this->sTransientKey ];
        } else {
            $_aLastInputs = $this->getTransient( $this->sTransientKey );
            self::$_aCaches[ $this->sTransientKey ]  = $_aLastInputs;
            if ( false !== $_aLastInputs ) {
                $this->delete();    // deletes at the end of the script.
            }
        }        
        
        return $this->getElementAsArray(
            $_aLastInputs,
            $this->sCallerID,
            array()
        );
        
    }
        private static $_aCaches = array();
    
    /**
     * Deletes the data from the database.
     * 
     * The reason that the data should not be deleted right away when it is retrieved is 
     * because the transient key is used site-wide. Forms created with a different version of the framework can exist in one page.
     * So if the data is deleted in the middle of the script execution, another form becomes not able to access it.
     * 
     * @since       3.7.8
     */
    public function delete() {
        add_action( 'shutdown', array( $this, '_replyToDelete' ) );
    }
        /**
         * Deletes the field errors transient.
         * 
         * @since       3.7.8      
         * @callback    action      shutdown
         * @internal
         */
        public function _replyToDelete() {
            $this->deleteTransient( $this->sTransientKey );
        }               
        
}