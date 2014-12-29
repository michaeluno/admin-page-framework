<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
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
     */
    public function _isInThePage() {
        
        // If it's not in one of the post type's pages
        if ( ! $this->oProp->bIsAdmin ) {
            return false;
        }
        if ( ! in_array( $this->oProp->sPageNow, array( 'edit.php', 'edit-tags.php', 'post.php', 'post-new.php' ) ) ) {
            return false;
        }
                
        return ( $this->oUtil->getCurrentPostType() == $this->oProp->sPostType );

    }
  
    /**
     * Redirects undefined callback methods or to the appropriate methods.
     * 
     * @internal
     */
    public function __call( $sMethodName, $aArgs=null ) {    
    
        if ( 'setup_pre' == $sMethodName ) { 
            $this->_setUp();
            $this->oUtil->addAndDoAction( $this, "set_up_{$this->oProp->sClassName}", $this );
            $this->oProp->_bSetupLoaded = true;
            return;
        }

        if ( has_filter( $sMethodName ) ) {
            return isset( $aArgs[ 0 ] ) ? $aArgs[ 0 ] : null;
        }
        
        parent::__call( $sMethodName, $aArgs );
                
    }
    
}