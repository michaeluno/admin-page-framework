<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Collects data of page loads of the added pages.
 *
 * @since 2.1.7
 * @extends AdminPageFramework_PageLoadInfo_Base
 * @package AdminPageFramework
 * @subpackage Debug
 * @internal
 */
class AdminPageFramework_PageLoadInfo_Page extends AdminPageFramework_PageLoadInfo_Base {
    
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
        self::$_oInstance = new AdminPageFramework_PageLoadInfo_Page( $oProp, $oMsg );
        
        return self::$_oInstance;
        
    }     
    
    /**
     * Sets the hook if the current page is one of the framework's added pages.
     * @internal
     */ 
    public function _replyToSetPageLoadInfoInFooter() {
        
        // For added pages
        if ( $this->oProp->isPageAdded() ) {
            add_filter( 'update_footer', array( $this, '_replyToGetPageLoadInfo' ), 999 );
        }
        
    }     
    
}