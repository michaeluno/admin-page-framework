<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides utility methods dealing with post data.
 *
 * @since 3.0.1
 * @extends AdminPageFramework_Utility
 * @package AdminPageFramework
 * @subpackage Utility
 * @internal
 */
class AdminPageFramework_WPUtility_Post extends AdminPageFramework_WPUtility_Option {
    
    /**
     * Retrieves the saved option value from the given option key, field ID, and section ID.
     * 
     * @since 3.0.4
     * @return array The saved meta data composed with the given keys.
     */
    static public function getSavedMetaArray( $iPostID, array $aKeys ) {
                    
        $_aSavedMeta = array();
        foreach ( $aKeys as $_sKey ) {
            $_aSavedMeta[ $_sKey ] = get_post_meta( $iPostID, $_sKey, true );
        }
        return $_aSavedMeta;
        
    }

}