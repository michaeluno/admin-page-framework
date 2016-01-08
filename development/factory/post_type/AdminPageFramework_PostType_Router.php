<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
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
     * Instantiates a link object based on the type.
     * 
     * @since       3.7.10
     * @internal
     * @return      null|object
     */
    protected function _getLinkObject() {
        return new AdminPageFramework_Link_post_type( $this->oProp, $this->oMsg );
    }          

    /**
     * Instantiates a link object based on the type.
     * 
     * @since       3.7.10
     * @internal
     * @return      null|object
     */    
    protected function _getPageLoadObject() {
        return new AdminPageFramework_PageLoadInfo_post_type( $this->oProp, $this->oMsg );
    }
  
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
                
        // 3.7.9+  Limitation: If the `page` argument is set in the query url, 
        // this factory will not be loaded to make the overall responses lighter.
        if ( isset( $_GET[ 'page' ] ) ) {
            return false;
        }

        return $this->oUtil->getCurrentPostType() === $this->oProp->sPostType;

    }
  
}