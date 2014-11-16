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

class APF_Demo_CustomFieldTypes_ImageSelectors {
    
    /**
     * Stores the caller class name, set in the constructor.
     */   
    public $sClassName = 'APF_Demo_CustomFieldTypes';
    
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug  = 'apf_custom_field_types';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug   = 'image_selectors';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID = 'image_selectors';
    
    /**
     * Sets up hooks and properties.
     */
    public function __construct( $sClassName='', $sPageSlug='', $sTabSlug='' ) {
        
        $this->sClassName   = $sClassName ? $sClassName : $this->sClassName;
        $this->sPageSlug    = $sPageSlug ? $sPageSlug : $this->sPageSlug;
        $this->sTabSlug     = $sTabSlug ? $sTabSlug : $this->sTabSlug;
        
        // load_ + page slug
        add_action( 'load_' . $this->sPageSlug, array( $this, 'replyToAddTab' ) );       
        
        $this->registerFieldTypes( $this->sClassName );
        
    }
    
        /**
         * Registers the field types.
         */
        private function registerFieldTypes( $sClassName ) {
            
            $_sPluginDirName = dirname( APFDEMO_FILE );
            include( $_sPluginDirName . '/third-party/image_checkbox-custom-field-type/ImageCheckboxCustomFieldType.php' );
            include( $_sPluginDirName . '/third-party/image_radio-custom-field-type/ImageRadioCustomFieldType.php' );
            new ImageCheckboxCustomFieldType( $sClassName );     
            new ImageRadioCustomFieldType( $sClassName );     
            
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
                'title'     => __( 'Image Selectors', 'admin-page-framework-demo' ),    
            )
        );  
        
        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToAddFormElements' ) );

        // do_ + page slug + tab slug 
        add_action( 'do_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToInsertOutput' ) );        
        
    }
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToAddFormElements( $oAdminPage ) {
        
        // Section
        $oAdminPage->addSettingSections(    
            $this->sPageSlug, // the target page slug                
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Image Selectors', 'admin-page-framework-demo' ),
            )              
        );        
        
        // Fields
        $oAdminPage->addSettingFields(
            $this->sSectionID, // the target section id
            array(
                'field_id'      => 'image_checkbox',
                'type'          => 'image_checkbox',     
                'title'         => __( 'Image Checkbox', 'admin-page-framework-demo' ),
                'width'         => 96,
                'height'        => 64,  
                'label_min_width'   => 200,
                'label'         => array(
                    'a' => APFDEMO_DIRNAME . '/asset/image/a.jpg',
                    'b' => APFDEMO_DIRNAME . '/asset/image/b.jpg',
                    'c' => APFDEMO_DIRNAME . '/asset/image/c.jpg',
                ),
                'after_input'   => array(
                    'a' => "<br /><span>" . __( 'First Image' ) . "</span>",
                    'b' => "<br /><span>" . __( 'Second Image' ) . "</span>",
                    'c' => "<br /><span>" . __( 'Third Image' ) . "</span>",
                ),                
            ),
            array(
                'field_id'      => 'image_radio',
                'type'          => 'image_radio',     
                'title'         => __( 'Image Radio', 'admin-page-framework-demo' ),
                'width'         => 96,
                'height'        => 64,  
                'label_min_width'   => 200,
                'label'         => array(
                    'a' => APFDEMO_DIRNAME . '/asset/image/a.jpg',
                    'b' => APFDEMO_DIRNAME . '/asset/image/b.jpg',
                    'c' => APFDEMO_DIRNAME . '/asset/image/c.jpg',
                ),
                'after_input'   => array(
                    'a' => "<br /><span>" . __( 'First Image' ) . "</span>",
                    'b' => "<br /><span>" . __( 'Second Image' ) . "</span>",
                    'c' => "<br /><span>" . __( 'Third Image' ) . "</span>",
                ),   
                'default'   => 'b',
            )
        );             
        
    }
    
    /**
     * Inserts an output into the page.
     */
    public function replyToInsertOutput() {
        submit_button();
    }
        
}