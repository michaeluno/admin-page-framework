<?php
class APF_Demo_CustomFieldTypes_Dial {
    
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
    public $sTabSlug    = 'dial';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'dial';
    
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
            
            include( dirname( APFDEMO_FILE ) . '/third-party/dial-custom-field-type/DialCustomFieldType.php' );
            new DialCustomFieldType( $sClassName );
            
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
                'title'     => __( 'Dials', 'admin-page-framework-demo' ),    
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
                'title'         => __( 'Dial Custom Field Type', 'admin-page-framework-demo' ),
            )
        );        
        
        // Fields   
        $oAdminPage->addSettingFields(
            $this->sSectionID, // the target section id
            array(
                'field_id'          => 'dials',
                'title'             => __( 'Multiple Dials', 'admin-page-framework-demo' ),
                'type'              => 'dial',
                'label'             => __( 'Default', 'admin-page-framework-demo' ),
                'attributes'        => array(    
                    'field' => array(
                        'style' => 'display: inline; clear: none', // this makes the field element inline, which means next fields continues from the right end of the field, not from the new line.
                    ),
                ),
                array(     
                    'label'         => __( 'Disable display input', 'admin-page-framework-demo' ),
                    'attributes'    => array(
                        // For details, see https://github.com/aterrien/jQuery-Knob
                        'data-width' => 100,
                        'data-displayInput' => 'false',
                    ),
                ),     
                array(     
                    'label'         => __( 'Cursor mode', 'admin-page-framework-demo' ),
                    'attributes'    => array(
                        'data-width' => 150,
                        'data-cursor' => 'true',
                        'data-thickness' => '.3', 
                        'data-fgColor' => '#222222',     
                    ),
                ),
                array(
                    'label'         => __( 'Display previous value (effect)', 'admin-page-framework-demo' ),
                    'attributes'    => array(
                        'data-width' => 200,
                        'data-min' => -100, 
                        'data-displayPrevious' => 'true', // a boolean value also needs to be passed as string
                    ),     
                ),
                array(
                    'label'         => __( 'Angle offset', 'admin-page-framework-demo' ),     
                    'attributes'    => array(
                        'data-angleOffset' => 90,
                        'data-linecap' => 'round',
                    ),     
                ),
                array(
                    'label'         => __( 'Angle offset and arc', 'admin-page-framework-demo' ),
                    'attributes'    => array(
                        'data-fgColor' => '#66CC66',
                        'data-angleOffset' => -125,
                        'data-angleArc' => 250,
                    ),     
                ),
                array(
                    'label'         => __( '5-digit values, step 1000', 'admin-page-framework-demo' ),
                    'attributes'    => array(
                        'data-step' => 1000,
                        'data-min' => -15000,
                        'data-max' => 15000,
                        'data-displayPrevious' =>    true,
                    ),     
                ),

            ),
            array(
                'field_id'      => 'dial_big',
                'title'         => __( 'Big', 'admin-page-framework-demo' ),
                'type'          => 'dial',
                'attributes'    => array(
                    'data-width' => 400,
                    'data-height' => 400,
                ),
            ),
            array(
                'field_id'      => 'dial_repeatable',
                'title'         => __( 'Repeatable', 'admin-page-framework-demo' ),
                'type'          => 'dial',
                'repeatable'    => true,
            ),
            array(
                'field_id'      => 'dial_sortable',
                'title'         => __( 'Sortable', 'admin-page-framework-demo' ),
                'type'          => 'dial',
                'sortable'      =>    true,
                'attributes'    => array(    
                    'field' => array(
                        'style' => 'display: inline; clear: none', // this makes the field element inline, which means next fields continues from the right end of the field, not from the new line.
                    ),
                    'data-width' => 100,
                    'data-height' =>     100,
                ),     
                array(), // the second item
                array(), // the third item
                array(), // the fourth item
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