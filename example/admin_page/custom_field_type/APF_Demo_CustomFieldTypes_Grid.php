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

class APF_Demo_CustomFieldTypes_Grid {

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
    public $sTabSlug    = 'grid';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'grid';
    
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
            
            include( dirname( APFDEMO_FILE ) . '/third-party/grid-custom-field-type/GridCustomFieldType.php' );
            new GridCustomFieldType( $sClassName );
            
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
                'title'     => __( 'Grid', 'admin-page-framework-demo' ),
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
                'title'         => __( 'Grid Custom Field Type', 'admin-page-framework-demo' ),
                'description'   => __( 'This field will save the grid positions of the widgets.', 'admin-page-framework-demo' ),     
            )
        );        
        
        // Fields   
        $oAdminPage->addSettingFields(
            $this->sSectionID, // the target section id
            array(
                'field_id'          => 'grid_field',     
                'type'              => 'grid',     
                'description'       => __( 'Move the widgets.', 'admin-page-framework-demo' ),    
                'show_title_column' => false, // this removes the title column of the field output
                'grid_options'      => array(
                    'resize' => array(
                        'enabled' => false,
                    ),
                ),
                'default'           => array( // '[{"id":"","col":1,"row":1,"size_y":1,"size_x":1},{"id":"","col":1,"row":2,"size_y":1,"size_x":1}]',
                    array( 
                        'col'       => 1,
                        'row'       => 1,
                        'size_y'    => 1,
                        'size_x'    => 1,
                    ),
                    array(
                        'col'       => 2,
                        'row'       => 2,
                        'size_y'    => 1,
                        'size_x'    => 1,     
                    ),
                ),
            ),
            array(
                'field_id'          => 'grid_field2',     
                'description'       => __( 'Widgets can be expanded.', 'admin-page-framework-demo' ),    
                'type'              => 'grid',     
                'grid_options'      => array(
                    'resize' => array(
                        'enabled' =>    true,
                    ),
                ),
                'show_title_column' => false,    
                'default'           => array(    
                    array( 
                        'col'       => 1,
                        'row'       => 1,
                        'size_y'    => 2,
                        'size_x'    => 1,
                    ),
                    array(
                        'col'       => 2,
                        'row'       => 1,
                        'size_y'    => 1,
                        'size_x'    => 2,     
                    ),
                    array(
                        'col'       => 4,
                        'row'       => 1,
                        'size_y'    => 1,
                        'size_x'    => 2,     
                    ),     
                ),
            ),    
            array(
                'field_id'          => 'grid_field3',
                'type'              => 'grid',     
                'description'       => __( 'The base size can be different.', 'admin-page-framework-demo' ),    
                'grid_options'      => array(
                    'resize' => array(
                        'enabled' =>    true,
                    ),
                    'widget_margins' => array( 10, 10 ),
                    'widget_base_dimensions' => array( 100, 100 ),     
                ),
                'show_title_column' => false,    
                'default'           => array(    
                    array( 
                        'col' => 1,
                        'row' => 1,
                        'size_y' => 1,
                        'size_x' => 1,
                    ),     
                ),
            ),                
            array()
        );       

    }
    
    /**
     * Inserts an output into the page.
     */
    public function replyToInsertOutput() {
        submit_button();
    }
        
}