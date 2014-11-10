<?php
class APF_Demo_CustomFieldTypes_Font {
    
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
    public $sTabSlug    = 'font';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'font';
    
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
            
            include( dirname( APFDEMO_FILE ) . '/third-party/font-custom-field-type/FontCustomFieldType.php' );
            new FontCustomFieldType( $sClassName );
            
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
                'title'     => __( 'Fonts', 'admin-page-framework-demo' ),    
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
                'title'         => __( 'Font Custom Field Type', 'admin-page-framework-demo' ),
                'description'   => __( 'This is still experimental.', 'admin-page-framework-demo' ),     
            )
        );        
        
        // Fields   
        $oAdminPage->addSettingFields(
            $this->sSectionID, // the target section id
            array(
                'field_id'      => 'font_field',
                'section_id'    => 'font',
                'title'         => __( 'Font Upload', 'admin-page-framework-demo' ),
                'type'          => 'font',
                'description'   => __( 'Set the URL of the font.', 'admin-page-framework-demo' ),
            ),
            array(
                'field_id'      => 'font_field_repeatable',
                'title'         => __( 'Repeatable', 'admin-page-framework-demo' ),
                'type'          => 'font',
                'repeatable'    => true,
                'attributes'    => array(
                    'button'            =>  array(
                        'data-label'    => __( 'Select Font', 'admin-page-framework-demo' ),
                    ),
                    'remove_button'     =>  array(
                        'data-label'    => __( 'Remove Font', 'admin-page-framework-demo' ),
                    )                    
                ),
            ),     
            array(
                'field_id'      => 'font_field_sortable',
                'title'         => __( 'Sortable', 'admin-page-framework-demo' ),
                'type'          => 'font',
                'sortable'      =>  true,
                array(), // second
                array(), // third
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