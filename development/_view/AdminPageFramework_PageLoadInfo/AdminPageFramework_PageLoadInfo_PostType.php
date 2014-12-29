<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Collects data of page loads of the added post type pages. 
 * @since 2.1.7
 * @extends AdminPageFramework_PageLoadInfo_Base
 * @package AdminPageFramework
 * @subpackage Debug
 * @internal
 */
class AdminPageFramework_PageLoadInfo_PostType extends AdminPageFramework_PageLoadInfo_Base {
    
    private static $_oInstance;
    private static $aClassNames = array();
    
    /**
     * Ensures that only one instance of this class object exists per class. ( no multiple instances of this object on a particular class ) 
     * 
     * @remark This class should be instantiated via this method.
     */
    public static function instantiate( $oProp, $oMsg ) {
        
        if ( in_array( $oProp->sClassName, self::$aClassNames ) )
            return self::$_oInstance;
        
        self::$aClassNames[] = $oProp->sClassName;
        self::$_oInstance = new AdminPageFramework_PageLoadInfo_PostType( $oProp, $oMsg );
        
        return self::$_oInstance;
        
    }    
        
    /**
     * Sets the hook if the current page is one of the framework's added post type pages.
     * @internal
     */ 
    public function _replyToSetPageLoadInfoInFooter() {

        // Some users sets $_GET['post_type'] element even in regular admin pages. In that case, do not load the style to avoid duplicates.
        if ( isset( $_GET['page'] ) && $_GET['page'] ) { return; }
    
        // For post type pages
        if ( 
            AdminPageFramework_WPUtility::getCurrentPostType() == $this->oProp->sPostType
            || AdminPageFramework_WPUtility::isPostDefinitionPage( $this->oProp->sPostType )
            || AdminPageFramework_WPUtility::isCustomTaxonomyPage( $this->oProp->sPostType )
        ) {
            add_filter( 'update_footer', array( $this, '_replyToGetPageLoadInfo' ), 999 );
        }
        
    }    
    
}