<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods for views.
 * 
 * @abstract
 * @since       3.0.4
 * @package     AdminPageFramework
 * @subpackage  Factory
 * @internal
 */
abstract class AdminPageFramework_Factory_View extends AdminPageFramework_Factory_Model {
    
    /**
     * Sets up hooks and properties.
     * 
     * @internal
     */
    public function __construct( $oProp ) {
        
        parent::__construct( $oProp );

        if ( ! $this->_isInThePage() ) {
            return;
        }
        if ( $this->oProp->bIsAdminAjax ) {
            return;
        }
    
        // Admin Notices
        if ( is_network_admin() ) {
            add_action( 'network_admin_notices', array( $this, '_replyToPrintSettingNotice' ) );
        } else {
            add_action( 'admin_notices', array( $this, '_replyToPrintSettingNotice' ) );
        }     
        
        // [3.5.7+] Form field element output callbacks. 
        $this->oProp->aFieldCallbacks = $this->_getFormFieldElementCallbacks();
        
    }     

        /**
         * Returns an array holding callables for field type element outputs.
         * @internal
         * @since       3.5.7
         * @remark      These callbacks are defined in the `AdminPageFramework_Factory_View` class. Some factory classes will override these values.
         * @return      array
         */
        private function _getFormFieldElementCallbacks() {
            return array(
                'hfID'          => array( $this, '_replyToGetInputID' ), // the input id attribute
                'hfTagID'       => array( $this, '_replyToGetInputTagIDAttribute' ), // the fields & fieldset & field row container id attribute
                'hfName'        => array( $this, '_replyToGetInputNameAttribute' ), // the input name attribute
                'hfNameFlat'    => array( $this, '_replyToGetFlatInputName' ), // the flat input name attribute
                'hfClass'       => array( $this, '_replyToGetInputClassAttribute' ), // the class attribute
            ) + $this->oProp->aFieldCallbacks;
        }    
            
        /**
         * @internal    
         * @since       3.5.7
         * @return      string      the input id attribute
         */    
        public function _replyToGetInputID() {
            $_aParams           = func_get_args() + array( null, null, null, null );
            $sInputIDAttribute  = $_aParams[ 0 ];
            // $aField             = $_aParams[ 1 ];
            // $sKey               = $_aParams[ 2 ];
            // $sSectionIndex      = $_aParams[ 3 ];
            return $sInputIDAttribute;        
        }
        /**
         * @internal    
         * @since       3.5.7
         * @return      string      the fields & fieldset & field row container id attribute
         */    
        public function _replyToGetInputTagIDAttribute() {
            $_aParams           = func_get_args() + array( null, null, null, null );
            $sTagIDAttribute    = $_aParams[ 0 ];
            // $aField             = $_aParams[ 1 ];
            // $sKey               = $_aParams[ 2 ];
            // $sSectionIndex      = $_aParams[ 3 ];
            return $sTagIDAttribute;    
        }

        /**
         * Generates a name attribute value for a form input element.
         * @internal    
         * @since       3.5.7
         * @return      string      the input name attribute
         */    
        public function _replyToGetInputNameAttribute( /* $sNameAttribute, $aField, $sKey */ ) {
            $_aParams           = func_get_args() + array( null, null, null );
            $sNameAttribute     = $_aParams[ 0 ];
            return $sNameAttribute;    
        }
        
        /**
         * Generates a flat input name whose dimensional element keys are delimited by the pipe (|) character.
         * @internal    
         * @since       3.5.7
         * @return      string      the flat input name attribute
         */    
        public function _replyToGetFlatInputName( /* $sFlatNameAttribute, $aField, $sKey */ ) {
            $_aParams   = func_get_args() + array( null, null, null, null );
            $sFlatName  = $_aParams[ 0 ];
            return $sFlatName; 
        }

        /**
         * 
         * @internal    
         * @since       3.5.7
         * @return      string      the input class attribute.
         */
        public function _replyToGetInputClassAttribute() {
            $_aParams           = func_get_args() + array( null, null, null, null );
            $sClassAttribute    = $_aParams[ 0 ];
            // $aField             = $_aParams[ 1 ];
            // $sKey               = $_aParams[ 2 ];
            // $sSectionIndex      = $_aParams[ 3 ];
            return $sClassAttribute;    
        }
        
    /**
     * Checks whether a section is set.
     * @internal
     * @since       3.5.7       Moved from `AdminPageFramework_FormField`.
     * @param       array       $aField     a field definition array.
     * @return      boolean
     */
    public function isSectionSet( array $aField ) {
        $aField = $aField + array(
            'section_id'  => null,
        );
        return $aField[ 'section_id' ] && '_default' !== $aField[ 'section_id' ];
    }
    
    
    /**
     * Stores a flag value indicating whether the setting notice method is called or not.
     * 
     * @since       3.1.3
     * @internal
     */
    static private $_bSettingNoticeLoaded = false;
    
    /**
     * Displays stored setting notification messages.
     * 
     * @since       3.0.4
     * @internal
     */
    public function _replyToPrintSettingNotice() {
            
        if ( ! $this->_isInThePage() ) { 
            return; 
        }
            
        // Ensure this method is called only once per a page load.
        if ( self::$_bSettingNoticeLoaded ) { 
            return;
        }
        self::$_bSettingNoticeLoaded = true;

        $_iUserID  = get_current_user_id();
        $_aNotices = $this->oUtil->getTransient( "apf_notices_{$_iUserID}" );
        if ( false === $_aNotices ) { 
            return; 
        }
        $this->oUtil->deleteTransient( "apf_notices_{$_iUserID}" );
    
        // By setting false to the 'settings-notice' key, it's possible to disable the notifications set with the framework.
        if ( isset( $_GET['settings-notice'] ) && ! $_GET['settings-notice'] ) { 
            return; 
        }
        
        $this->_printSettingNotices( $_aNotices );
        
    }
        /**
         * Displays settings notices.
         * @since       3.5.3
         * @internal
         */
        private function _printSettingNotices( $aNotices ) {
            
            $_aPeventDuplicates = array();
            foreach ( array_filter( ( array ) $aNotices, 'is_array' ) as $_aNotice ) {
                
                $_sNotificationKey = md5( serialize( $_aNotice ) );
                if ( isset( $_aPeventDuplicates[ $_sNotificationKey ] ) ) {
                    continue;
                }
                $_aPeventDuplicates[ $_sNotificationKey ] = true;
                
                echo $this->_getSettingNotice( $_aNotice );
                
            }            
            
        }
            /**
             * Returns an admin setting notice HTML output generated from the given notification definition array.
             * @since       3.5.3
             * @internal
             * @return      string      The admin setting notice HTML output.
             */
            private function _getSettingNotice( array $aNotice ) {
                
                if ( ! isset( $aNotice['aAttributes'], $aNotice['sMessage'] ) ) {
                    return '';
                }
                $aNotice['aAttributes']['class'] = isset( $aNotice['aAttributes']['class'] )
                    ? $aNotice['aAttributes']['class'] . ' admin-page-framework-settings-notice-container'
                    : 'admin-page-framework-settings-notice-container';
                return "<div " . $this->oUtil->generateAttributes( $aNotice['aAttributes'] ). ">"
                        . "<p class='admin-page-framework-settings-notice-message'>" 
                            . $aNotice['sMessage'] 
                        . "</p>"
                    . "</div>";  
                    
            }    
    
    /**
     * Returns the field output from the given field definition array.
     * 
     * @remark      This method will be called multiple times in a single page load depending on how many fields have been registered.
     * @since       3.0.0
     * @internal
     */
    public function _replyToGetFieldOutput( $aField ) {

        $_oField = new AdminPageFramework_FormField( 
            $aField,                                // the field definition array
            // @todo change it to $this->getSavedOptions()
            $this->oProp->aOptions,                 // the stored form data
            $this->_getFieldErrors(),               // the field error array.
            $this->oProp->aFieldTypeDefinitions,    // the field type definition array.
            $this->oMsg,                            // the system message object
            $this->oProp->aFieldCallbacks           // field output element callables.
        );

        $_sOutput = $this->oUtil->addAndApplyFilters(
            $this,
            array( 'field_' . $this->oProp->sClassName . '_' . $aField['field_id'] ), // field_ + {extended class name} + _ {field id}
            $_oField->_getFieldOutput(), // field output
            $aField // the field array
        );     

        return $_sOutput;
        
    }    
        
    /**
     * The content filter method,
     * 
     * The user may just override this method instead of defining a `content_{...}` callback method.
     * 
     * @since       3.4.1
     * @remark      Declare this method in each factory class as the form of parameters varies and if parameters are different, it triggers PHP strict standard warnings.
     * @param       string      $sContent       The filtering content string.
     */
    // public function content( $sContent ) {
        // return $sContent;
    // }            
    
}