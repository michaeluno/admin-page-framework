<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides routing methods for creating meta boxes in pages added by the framework.
 * 
 * @abstract
 * @since           3.0.4
 * @package         AdminPageFramework/Factory/PageMetaBox
 * @internal
 * @extends         AdminPageFramework_MetaBox_View
 */
abstract class AdminPageFramework_PageMetaBox_Router extends AdminPageFramework_MetaBox_View {
            
    /**
     * Determines whether the meta box class components should be loaded in the currently loading page.
     * 
     * @since       3.1.3    
     * @internal
     */
    protected function _isInstantiatable() {
        
        // Disable the functionality in admin-ajax.php
        if ( isset( $GLOBALS[ 'pagenow' ] ) && 'admin-ajax.php' === $GLOBALS[ 'pagenow' ] ) {
            return false;
        }
        return true;
        
    }
    
    /**
     * Determines whether the meta box belongs to the loading page.
     * 
     * @since       3.0.3
     * @since       3.2.0   Changed the scope to `public` from `protected` as the head tag object will access it.
     * @since       3.8.14  Changed the visibility scope to `protected` from `public` as there is the `isInThePage()` public method.
     * @internal
     */
    protected function _isInThePage() {
        
        if ( ! $this->oProp->bIsAdmin ) {
            return false;     
        }
                    
        if ( ! isset( $_GET[ 'page' ] ) ) {
            return false;
        }
        
        // For in-page tabs.
        if ( array_key_exists( $_GET[ 'page' ], $this->oProp->aPageSlugs ) ) {
            return true;
        }
        
        return in_array( $_GET[ 'page' ], $this->oProp->aPageSlugs );
        
    }     
    
}
