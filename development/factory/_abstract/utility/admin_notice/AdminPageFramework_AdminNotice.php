<?php
/**
 * Displays notification in the administration area.
 *    
 * @package      Admin Page Framework
 * @copyright    Copyright (c) 2015, <Michael Uno>
 * @author       Michael Uno
 * @authorurl    http://michaeluno.jp
 */

/**
 * Displays notification in the administration area.
 * 
 * Usage: new AdminPageFramework_AdminNotice( 'There was an error while doing something...' );
 * `
 * new AdminPageFramework_AdminNotice( 'Error occurred', array( 'class' => 'error' ) );    
 * `
 * `
 * new AdminPageFramework_AdminNotice( 'Setting Updated', array( 'class' => 'updated' ) );
 * `
 * 
 * @since       3.5.0
 * @package     AdminPageFramework
 * @subpackage  Utility
 * @extends     AdminPageFramework_WPUtility
 */
class AdminPageFramework_AdminNotice extends AdminPageFramework_WPUtility {

    /**
     * Sets up hooks and properties.
     * 
     * @param       string      $sNotice        The message to display.
     * @param       array       $aAttributes    An attribute array. Set 'updated' to the 'class' element to display it in a green box.
     * @since       3.5.0
     */
    public function __construct( $sNotice, array $aAttributes=array( 'class' => 'error' ) ) {
        
        $this->sNotice                = $sNotice;
        $this->aAttributes            = $aAttributes + array(
            'class' => 'error', // 'updated' etc.
        );        
        $this->aAttributes[ 'class' ] = $this->getClassAttribute(
            $this->aAttributes[ 'class' ],
            'admin-page-framework-settings-notice-message',
            'admin-page-framework-settings-notice-container',   // Moved from `AdminPageFramework_Factory_View`.
            'notice',
            'is-dismissible'    // 3.5.12+
        );
  
        $this->_loadResources();
        
        // An empty value may be set in oreder only to laode the fade-in script.
        if ( ! $this->sNotice ) {
            return;
        }
        
        $this->registerAction( 
            'admin_notices', 
            array( $this, '_replyToDisplayAdminNotice' ) 
        );
        
    }    
        /**
         * Sets up scripts.
         * @since       DEVVER
         */
        private function _loadResources(){
            // Make sure to load once per page load.
            if ( self::$_bLoaded ) {
                return;
            }
            self::$_bLoaded = true;
            
// @todo    load script to fade-in admin notice

        }
            static private $_bLoaded = false;
            
        /**
         * Displays the set admin notice.
         * @since       3.5.0
         */
        public function _replyToDisplayAdminNotice() {
            echo "<div " . $this->getAttributes( $this->aAttributes ) . ">"
                    . "<p>" // class='admin-page-framework-settings-notice-message'
                        . $this->sNotice 
                    . "</p>"
                . "</div>";
        }

}