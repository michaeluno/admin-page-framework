<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to retrieve post meta data for meta box form fields.
 * 
 * @since       DEVVER
 * @package     AdminPageFramework
 * @subpackage  UserMeta
 * @extends     AdminPageFramework_Property_Base
 * @internal
 */
class AdminPageFramework_UserMeta_Model___UserMeta extends AdminPageFramework_WPUtility {

    public $iUserID         = array();
    
    public $aFieldsets      = array();
    
    /**
     * Sets up hooks.
     * 
     * @since       DEVVER
     */
    public function __construct( /* $iUserID, array $aFieldsets */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->iUserID,
            $this->aFieldsets, 
        );
        $this->iUserID          = $_aParameters[ 0 ];
        $this->aFieldsets       = $_aParameters[ 1 ];
        
    }
    
    /**
     * @since       DEVVER
     * @return      array
     */
    public function get() {

        if ( ! $this->iUserID ) {
            return array();
        }
        return $this->_getSavedDataFromFieldsets(
            $this->iUserID,
            $this->aFieldsets
        );
    }
        /**
         * Returns an array hodlding post meta data associated with the given post ID and constructed with the given fieldsets.
         * 
         * @since       DEVVER
         * @uses        get_post_meta()
         * @return      array
         */
        private function _getSavedDataFromFieldsets( $iUserID, $aFieldsets ) {
            
            $_aMetaKeys  = array_keys( get_user_meta( $iUserID ) );
            $_aMetaData  = array();
            foreach( $aFieldsets as $_sSectionID => $_aFieldsets ) {
                
                if ( '_default' == $_sSectionID  ) {
                    foreach( $_aFieldsets as $_aFieldset ) {
                        if ( ! in_array( $_aFieldset[ 'field_id' ], $_aMetaKeys ) ) {
                            continue;
                        }                        
                        $_aMetaData[ $_aFieldset[ 'field_id' ] ] = get_user_meta( 
                            $iUserID, 
                            $_aFieldset[ 'field_id' ], 
                            true 
                        );
                    }
                }
                if ( ! in_array( $_sSectionID, $_aMetaKeys ) ) {
                    continue;
                }                                
                $_aMetaData[ $_sSectionID ] = get_user_meta( 
                    $iUserID, 
                    $_sSectionID, 
                    true 
                );
                
            }
            return $_aMetaData;
            
        }
        
}