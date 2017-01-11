<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides abstract methods to retrieve meta data.
 * 
 * User meta, post meta, term meta will extend this class.
 * 
 * @since       3.8.0
 * @package     AdminPageFramework/Common/Factory
 * @internal
 * @extends     AdminPageFramework_FrameworkUtility
 */
abstract class AdminPageFramework_Factory_Model___Meta_Base extends AdminPageFramework_FrameworkUtility {

    /**
     * The callback function name or the callable object to retrieve meta data.
     */
    protected $osCallable   = 'get_post_meta';

    /**
     * The object ID such as post ID, user ID, and term ID.
     */
    public $iObjectID       = 0;
    
    /**
     * A form fieldsets array.
     */
    public $aFieldsets      = array();
    
    /**
     * Sets up properties.
     * 
     * @since       3.8.0
     */
    public function __construct( /* $iObjectID, array $aFieldsets */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->iObjectID,
            $this->aFieldsets, 
        );
        $this->iObjectID        = absint( $_aParameters[ 0 ] );
        $this->aFieldsets       = $_aParameters[ 1 ];
        
    }
    
    /**
     * @since       3.7.0
     * @return      array
     */
    public function get() {

        if ( ! $this->iObjectID ) {
            return array();
        }
        return $this->_getSavedDataFromFieldsets(
            $this->iObjectID,
            $this->aFieldsets
        );
    }
        /**
         * Returns an array hodlding post meta data associated with the given post ID and constructed with the given fieldsets.
         * 
         * @since       3.7.0
         * @uses        get_post_meta()
         * @return      array
         */
        private function _getSavedDataFromFieldsets( $iObjectID, $aFieldsets ) {
            
            $_aMetaKeys = $this->_getMetaKeys( $iObjectID );
            $_aMetaData = array();
            foreach( $aFieldsets as $_sSectionID => $_aFieldsets ) {
                
                if ( '_default' == $_sSectionID  ) {
                    foreach( $_aFieldsets as $_aFieldset ) {
                        if ( ! in_array( $_aFieldset[ 'field_id' ], $_aMetaKeys ) ) {
                            continue;
                        }
                        $_aMetaData[ $_aFieldset[ 'field_id' ] ] = call_user_func_array(
                            $this->osCallable,
                            array(
                                $iObjectID, 
                                $_aFieldset[ 'field_id' ], 
                                true 
                            )
                        );
                    }
                }
                if ( ! in_array( $_sSectionID, $_aMetaKeys ) ) {
                    continue;
                }                
                $_aMetaData[ $_sSectionID ] = call_user_func_array(
                    $this->osCallable,
                    array(
                        $iObjectID, 
                        $_sSectionID, 
                        true 
                    )
                );                
                
            }            
            return $_aMetaData;
            
        }
    
    /**
     * Returns an array holding the associated meta keys.
     * 
     * By default the post meta keys will be returned but for user meta keys, override this method and customize it.
     * 
     * @since       3.8.0
     * @return      array
     */
    protected function _getMetaKeys( $iObjectID ) {
        return array_keys(
            $this->getAsArray( 
                call_user_func_array(
                    $this->osCallable,
                    array( $iObjectID )
                )
            )
        );                     
    }
    
}
