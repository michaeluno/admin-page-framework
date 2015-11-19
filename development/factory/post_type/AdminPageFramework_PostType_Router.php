<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides routing methods for the post type factory class.
 * 
 * @abstract
 * @since           3.0.4
 * @package         AdminPageFramework
 * @subpackage      PostType
 * @internal
 */
abstract class AdminPageFramework_PostType_Router extends AdminPageFramework_Factory {    
  
    /**
     * Determines whether the currently loaded page is of the post type page.
     * 
     * @internal
     * @since       3.0.4
     * @since       3.2.0       Changed the scope to public from protected as the head tag object will access it.
     * @return      boolean
     */
    public function _isInThePage() {
        
        // If it's not in one of the post type's pages
        if ( ! $this->oProp->bIsAdmin ) {
            return false;
        }

        // Post table columns use ajax to update when the user modifies the post meta via quick edit.
        if ( $this->oUtil->getElement( $this->oProp->aPostTypeArgs, 'public', true ) && $this->oProp->bIsAdminAjax ) {
            return true;
        }        
        
        if ( ! in_array( $this->oProp->sPageNow, array( 'edit.php', 'edit-tags.php', 'post.php', 'post-new.php' ) ) ) {
            return false;
        }
                
        return ( $this->oUtil->getCurrentPostType() == $this->oProp->sPostType );

    }
  
}