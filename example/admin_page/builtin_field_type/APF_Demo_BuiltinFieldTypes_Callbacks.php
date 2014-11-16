<?php
/**
 * Admin Page Framework - Demo
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed GPLv2
 * 
 */

class APF_Demo_BuiltinFieldTypes_Callbacks {
 
    /**
     * Stores the caller class name, set in the constructor.
     */   
    public $sClassName = 'APF_Demo';
       
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_builtin_field_types';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'callbacks';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'callbacks';
    
    /**
     * Sets up hooks and properties.
     */
    public function __construct( $sClassName='', $sPageSlug='', $sTabSlug='' ) {
        
        $this->sClassName   = $sClassName ? $sClassName : $this->sClassName;
        $this->sPageSlug    = $sPageSlug ? $sPageSlug : $this->sPageSlug;
        $this->sTabSlug     = $sTabSlug ? $sTabSlug : $this->sTabSlug;
              
        // load_ + page slug
        add_action( 'load_' . $this->sPageSlug, array( $this, 'replyToAddTab' ) );
    
    }
    
    /**
     * Triggered when the page is loaded.
     */
    public function replyToAddTab( $oAdminPage ) {
        
        // Tab
        $oAdminPage->addInPageTabs(    
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'  => $this->sTabSlug,
                'title'         => __( 'Callbacks', 'admin-page-framework-demo' ), 
            )      
        );  
        
        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToAddFormElements' ) );
        

        
    }
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToAddFormElements( $oAdminPage ) {
        
        // Section
        $oAdminPage->addSettingSections(    
            $this->sPageSlug, // the target page slug                
            array(
                'tab_slug'          => $this->sTabSlug,
                'section_id'        => $this->sSectionID,
                'title'             => __( 'Using Callbacks', 'admin-page-framework-demo' ),
                'description'       => __( 'These fields are (re)defined with callbacks.', 'admin-page-framework-demo' ),
            )
        );        
        
        /**
         * Fields to be defined with callback methods - pass only the required keys: 'field_id', 'section_id', and the 'type'.
         */
        $oAdminPage->addSettingFields(
            $this->sSectionID,  // target section id
            array(
                'field_id'          => 'callback_example',
                'type'              => 'select',
            ),
            array(
                'field_id'          => 'apf_post_titles',
                'type'              => 'checkbox',
                'label_min_width'   => '100%',
            ),     
            array()
        );     
     
        // field_definition_{instantiated class name}_{section id}_{field_id}
        add_filter( 'field_definition_APF_Demo_callbacks_callback_example', array( $this, 'field_definition_APF_Demo_callbacks_callback_example' ) );
            
        // field_definition_{instantiated class name}_{section id}_{field_id}
        add_filter( 'field_definition_APF_Demo_callbacks_apf_post_titles', array( $this, 'field_definition_APF_Demo_callbacks_apf_post_titles' ) );                
     
    }
    
    /*
     * Field callback methods - for field definitions that require heavy tasks should be defined with the callback methods.
     */
    public function field_definition_APF_Demo_callbacks_callback_example( $aField ) { // field_definition_{instantiated class name}_{section id}_{field_id}
        
        $aField['title']        = __( 'Post Titles', 'admin-page-framework-demo' );
        $aField['description']  = sprintf( __( 'This description is inserted with the callback method: <code>%1$s</code>.', 'admin-page-framework-demo' ), __METHOD__ );
        $aField['label']        = $this->_getPostTitles();
        return $aField;
        
    }
    public function field_definition_APF_Demo_callbacks_apf_post_titles( $aField ) { // field_definition_{instantiated class name}_{section id}_{field_id}
        
        $aField['title'] = __( 'APF Custom Post Titles', 'admin-page-framework-demo' );
        $aField['label'] = $this->_getPostTitles( 'apf_posts' );
        return $aField;
        
    }    
        private function _getPostTitles( $sPostTypeSlug='post' ) {
            
            $_aArgs         = array(
                'post_type' => $sPostTypeSlug,
            );
            $_oResults      = new WP_Query( $_aArgs );
            $_aPostTitles   = array();
            foreach( $_oResults->posts as $_iIndex => $_oPost ) {
                $_aPostTitles[ $_oPost->ID ] = $_oPost->post_title;
            }
            return $_aPostTitles;
            
        }
        
    
}