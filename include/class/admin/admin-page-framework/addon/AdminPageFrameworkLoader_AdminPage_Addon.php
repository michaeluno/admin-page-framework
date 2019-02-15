<?php
/**
 * Admin Page Framework Loader
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds the Addon page to the loader plugin.
 * 
 * @since       3.5.0       
 * @since       3.5.3       Extends `AdminPageFrameworkLoader_AdminPage_Page_Base`.
 * @extends     AdminPageFrameworkLoader_AdminPage_Page_Base
 */
class AdminPageFrameworkLoader_AdminPage_Addon extends AdminPageFrameworkLoader_AdminPage_Page_Base {
    
    public function replyToLoadPage( $oFactory ) {
        
        new AdminPageFrameworkLoader_AdminPage_Addon_Top( 
            $oFactory,          // factory object
            $this->sPageSlug,   // page slug
            array(              // tab definition
                'tab_slug'      => 'top',
                'title'         => __( 'Add Ons', 'admin-page-framework-loader' ),
            )
        );       
        
    }
        
}
