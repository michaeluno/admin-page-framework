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
    function __construct( $oProp ) {
        
        parent::__construct( $oProp );

        if ( $this->_isInThePage() && ! $this->oProp->bIsAdminAjax ) {    
            if ( is_network_admin() ) {
                add_action( 'network_admin_notices', array( $this, '_replyToPrintSettingNotice' ) );
            } else {
                add_action( 'admin_notices', array( $this, '_replyToPrintSettingNotice' ) );
            }     
        }
        
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
            
        if ( ! $this->_isInThePage() ) { return; }
            
        // Ensure this method is called only once per a page load.
        if ( self::$_bSettingNoticeLoaded ) { return; }
        self::$_bSettingNoticeLoaded = true;

        $_iUserID  = get_current_user_id();
        $_aNotices = $this->oUtil->getTransient( "apf_notices_{$_iUserID}" );
        if ( false === $_aNotices ) { return; }
        $this->oUtil->deleteTransient( "apf_notices_{$_iUserID}" );
    
        // By setting false to the 'settings-notice' key, it's possible to disable the notifications set with the framework.
        if ( isset( $_GET['settings-notice'] ) && ! $_GET['settings-notice'] ) { return; }
        
        // Display the settings notices.
        $_aPeventDuplicates = array();
        foreach ( ( array ) $_aNotices as $__aNotice ) {
            if ( ! isset( $__aNotice['aAttributes'], $__aNotice['sMessage'] ) || ! is_array( $__aNotice ) ) {
                continue;
            }
            $_sNotificationKey = md5( serialize( $__aNotice ) );
            if ( isset( $_aPeventDuplicates[ $_sNotificationKey ] ) ) {
                continue;
            }
            $_aPeventDuplicates[ $_sNotificationKey ] = true;
            $__aNotice['aAttributes']['class'] = isset( $__aNotice['aAttributes']['class'] )
                ? $__aNotice['aAttributes']['class'] . ' admin-page-framework-settings-notice-container'
                : 'admin-page-framework-settings-notice-container';
            echo "<div " . $this->oUtil->generateAttributes( $__aNotice['aAttributes'] ). ">"
                    . "<p class='admin-page-framework-settings-notice-message'>" . $__aNotice['sMessage'] . "</p>"
                . "</div>";
            
        }
        
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

        return $this->oUtil->addAndApplyFilters(
            $this,
            array( 'field_' . $this->oProp->sClassName . '_' . $aField['field_id'] ), // field_ + {extended class name} + _ {field id}
            $_oField->_getFieldOutput(), // field output
            $aField // the field array
        );     
                        
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