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
class APF_Demo_ManageOptions_ResetConfirm {

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
            array( // TIPS: you can hide an in-page tab by setting the 'show_in_page_tab' key
                'tab_slug'          => $this->sTabSlug,
                'title'             => __( 'Reset Confirmation', 'admin-page-framework-demo' ),
                'show_in_page_tab'  => false,
                'parent_tab_slug'   => 'reset',
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
                'title'         => __( 'Confirmation', 'admin-page-framework-demo' ),
                'description' => "<div class='settings-error error'>"
                        . "<p>"
                            . "<strong>"
                                . "Are you sure you want to delete all the options?"
                           . "</strong>"
                        . "</p>"
                    . "</div>",
                'order' => 10,
            )
        );        
        
        /*
         * Fields for the manage option page.
         */
        $oAdminPage->addSettingFields(   
            $this->sSectionID,
            array( // Delete Option Confirmation Button
                'field_id'      => 'submit_delete_options_confirmation',
                'title'         => __( 'Delete Options', 'admin-page-framework' ),
                'type'          => 'submit',     
                'label'         => __( 'Delete Options', 'admin-page-framework' ),
                // 'redirect_url'  => admin_url( 'admin.php?page=apf_manage_options&tab=saved_data&settings-updated=true' ),
                'redirect_url'  => add_query_arg( 
                    array(
                        'page'  => $this->sPageSlug,
                        'tab'   => 'saved_data',    // the hidden tab
                        'settings-updated' => true,
                    )
                ),
                'attributes'    => array(
                    'class' => 'button-secondary',
                ),
            )
        );     
                
        // validation_APF_Demo_ManageOptions
        add_filter( "validation_{$this->sClassName}", array( $this, 'replyToValidateFormData' ), 10, 3 );
        
    }
    
    public function replyToDoTab() {}
    
    /**
     * 
     * @remark  // validation_{instantiated class name}
     */
    public function replyToValidateFormData( $aInput, $aOldOptions, $oFactory ) { 
           
        /* If the delete options button is pressed, return an empty array that will delete the entire options stored in the database. */
        if ( isset( $_POST[ $this->oFactory->oProp->sOptionKey ][ $this->sSectionID ]['submit_delete_options_confirmation'] ) ) { 
            return array();
        }
        return $aInput;
        
    }        
    
}