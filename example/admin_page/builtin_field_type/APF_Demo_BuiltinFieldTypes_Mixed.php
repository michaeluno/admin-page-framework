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

class APF_Demo_BuiltinFieldTypes_Mixed {
 
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
    public $sTabSlug    = 'mixed_types';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'mixed_types';
    
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
                'title'         => __( 'Mixed', 'admin-page-framework-demo' ),    
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
                'title'             => __( 'Mixed Field Types', 'admin-page-framework-demo' ),
                'description'       => __( 'As of v3, it is possible to mix field types in one field on a per-ID basis.', 'admin-page-framework-demo' ),
            )
        );        

        $oAdminPage->addSettingFields(     
            $this->sSectionID,  // target section id 
            array(
                'field_id' => 'mixed_fields',
                'title' => __( 'Text and Hidden', 'admin-page-framework-demo' ),
                'type' => 'text',
                'default' => 'abc',
                array(
                    'type' => 'hidden',
                    'value' => 'xyz',
                ),
                'attributes' => array(
                    'field' => array(
                        'style' => 'display: inline; clear:none;' // since the rich editor does not accept the cols attribute, set the width by inline-style.
                    ),
                ),     
                'description' => __( 'A hidden field is embedded. This is useful when you need to embed extra information to be sent with the visible elements.', 'admin-page-framework-demo' ),
            ),     
            array()
        );            

    }
    
}
