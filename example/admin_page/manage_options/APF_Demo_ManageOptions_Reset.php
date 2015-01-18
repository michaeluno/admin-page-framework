<?php
/**
 * Admin Page Framework Loader
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds a tab of the set page to the loader plugin.
 * 
 * @since       3.5.0    
 */
class APF_Demo_ManageOptions_Reset {

    public function __construct( $oFactory, $sPageSlug, $sTabSlug ) {
    
        $this->oFactory     = $oFactory;
        $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = $sPageSlug; 
        $this->sTabSlug     = $sTabSlug;
        $this->sSectionID   = $this->sTabSlug;
        
        $this->_addTab();
    
    }
    
    private function _addTab() {
        
        $this->oFactory->addInPageTabs(    
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Reset', 'admin-page-framework-loader' ),
            )        
        );  
        
        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToLoadTab' ) );
  
    }
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {
        
        add_action( 'do_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToDoTab' ) );

        $oAdminPage->addSettingSections(    
            $this->sPageSlug,
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Reset Button', 'admin-page-framework-demo' ),
                'order'         => 10,
            )
        );        
        
        $oAdminPage->addSettingFields(     
            $this->sSectionID,
            array(
                'field_id'      => 'submit_manage',
                'title'         => __( 'Delete Options', 'admin-page-framework' ),
                'type'          => 'submit',
                'label'         => __( 'Delete Options', 'admin-page-framework' ),
                'href'          => add_query_arg( 
                    array(
                        'page'  => $this->sPageSlug,
                        'tab'   => 'reset_confirm',    // the hidden tab
                    )
                ),
                'attributes'    => array(
                    'class' => 'button-secondary',
                ),     
            )
        );          
        
        
    }
    
    public function replyToDoTab() {
        

    }
    
}