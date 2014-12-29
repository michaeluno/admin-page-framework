<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides routing methods for creating meta boxes in pages added by the framework.
 * 
 * @abstract
 * @since           3.0.4
 * @package         AdminPageFramework
 * @subpackage      PageMetaBox
 * @internal
 * @todo            Examine the proper class to extend.
 */
abstract class AdminPageFramework_MetaBox_Page_Router extends AdminPageFramework_MetaBox_View {
    
    /**
     * Triggers the start_{...} action hook.
     *
     * @since       3.0.4
     * @internal
     */
    function __construct( $sMetaBoxID, $sTitle, $asPageSlugs=array(), $sContext='normal', $sPriority='default', $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {     
                        
        parent::__construct( $sMetaBoxID, $sTitle, $asPageSlugs, $sContext, $sPriority, $sCapability, $sTextDomain );
                
        $this->oUtil->addAndDoAction( $this, "start_{$this->oProp->sClassName}", $this );
    
    }
            
    /**
     * Determines whether the meta box class components should be loaded in the currently loading page.
     * 
     * @since       3.1.3    
     * @internal
     */
    protected  function _isInstantiatable() {
        
        // Disable the functionality in admin-ajax.php
        if ( isset( $GLOBALS['pagenow'] ) && 'admin-ajax.php' === $GLOBALS['pagenow'] ) {
            return false;
        }
        return true;
        
    }
    
    /**
     * Determines whether the meta box belongs to the loading page.
     * 
     * @since       3.0.3
     * @since       3.2.0   Changed the scope to public from protected as the head tag object will access it.
     * @internal
     */
    public function _isInThePage() {
        
        if ( ! $this->oProp->bIsAdmin ) {
            return false;     
        }
                    
        if ( ! isset( $_GET['page'] ) ) {
            return false;
        }
        
        // For in-page tabs.
        if ( array_key_exists( $_GET['page'], $this->oProp->aPageSlugs ) ) {
            return true;
        }
        
        return in_array( $_GET['page'], $this->oProp->aPageSlugs );
        
    }     
}