<?php
/**
 * Admin Page Framework Loader
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
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

    /**
     * A user constructor.
     * 
     * @since       3.5.3
     * @return      void
     */
    public function construct( $oFactory ) {
        
        new AdminPageFrameworkLoader_AdminPage_Addon_Top( 
                $oFactory,        // factory object
                $this->sPageSlug,       // page slug
                array(                  // tab definition
                    'tab_slug'      => 'top',
                    'title'         => __( 'Add Ons', 'admin-page-framework-loader' ),
                )
            );
            
    }
        
}
