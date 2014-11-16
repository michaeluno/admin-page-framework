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

class APF_Demo_CustomFieldTypes_Geometry {
    
    /**
     * Stores the caller class name, set in the constructor.
     */   
    public $sClassName  = 'APF_Demo_CustomFieldTypes';
    
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_custom_field_types';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'geometry';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'geometry';
    
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
            
            include( dirname( APFDEMO_FILE ) . '/third-party/geometry-custom-field-type/GeometryCustomFieldType.php' );
            new GeometryCustomFieldType( $sClassName );
            
        }    
        
    
    /**
     * Triggered when the page is loaded.
     */
    public function replyToAddTab( $oAdminPage ) {
        
        // Tab
        $oAdminPage->addInPageTabs(    
            'apf_custom_field_types', // target page slug
            array(
                'tab_slug'  => $this->sTabSlug,
                'title'     => __( 'Geometry', 'admin-page-framework-demo' ),    
            )
        );  
        
        // load_ + page slug + tab slug
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
                'title'         => __( 'Geometry Custom Field Type', 'admin-page-framework-demo' ),
                'description'   => array( 
                    sprintf( __( 'This field type uses <a href="%1$s" target="_blank">Google Maps API</a> and accesses <code>%2$s</code>.', 'admin-page-framework-demo' ), 
                        'https://developers.google.com/maps/',
                        'maps.googleapis.com' 
                    ),
                ),
            )
        );        
        
        // Fields   
        $oAdminPage->addSettingFields(
            $this->sSectionID, // the target section id
           array(
                'field_id'      => 'geometrical_coordinates',
                'section_id'    => 'geometry',
                'title'         => __( 'Geometrical Coordinates', 'admin-page-framework-demo' ),
                'type'          => 'geometry',
                'description'   => __( 'Get the coordinates from the map.', 'admin-page-framework-demo' ),
                'default'       => array(
                    'latitude'  => 20,
                    'longitude' => 20,
                ),
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