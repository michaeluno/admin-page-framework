<?php
class APF_Demo_CustomFieldTypes_Sample {
    
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
    public $sTabSlug    = 'sample';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'sample';
    
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
            
            include( dirname( APFDEMO_FILE ) . '/third-party/sample-custom-field-type/SampleCustomFieldType.php' );
            new SampleCustomFieldType( $sClassName );                        
            
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
                'title'     => __( 'Sample', 'admin-page-framework-demo' ),    
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
                'title'         => __( 'Sample Custom Field Type', 'admin-page-framework-demo' ),
                'description'   => __( 'This is just an example of creating a custom field type with Admin Page Framework.', 'admin-page-framework-demo' ),     
            )            
        );        
                    
        // Fields   
        $oAdminPage->addSettingFields(
            $this->sSectionID,
            array(
                'field_id'      => 'sample_field',
                'type'          => 'sample',
                'title'         => __( 'Sample', 'admin-page-framework-demo' ),
                'description'   => __( 'This sample custom field demonstrates how to display a certain element after selecting a radio button.', 'admin-page-framework-demo' ),
                // 'default' => 'red',
                'label'         => array(
                    'red'   => __( 'Red', 'admin-page-framework-demo' ),
                    'blue'  => __( 'Blue', 'admin-page-framework-demo' ),
                    'green' => __( 'Green', 'admin-page-framework-demo' ),
                ),
                'reveal'        => array( // the field type specific key. This is defined in the
                    'red'   => '<p style="color:red;">' . __( 'You selected red!', 'admin-page-framework-demo' ) . '</p>',
                    'blue'  => '<p style="color:blue;">' . __( 'You selected blue!', 'admin-page-framework-demo' ) . '</p>',
                    'green' => '<p style="color:green;">' . __( 'You selected green!', 'admin-page-framework-demo' ) . '</p>',
                ),
            ),
            array(
                'field_id'  => 'sample_field_repeatable',
                'type'      => 'sample',
                'title'     => __( 'Sample', 'admin-page-framework-demo' ),
                // 'default' => 'red',
                'label' => array(
                    'red'   => __( 'Red', 'admin-page-framework-demo' ),
                    'blue'  => __( 'Blue', 'admin-page-framework-demo' ),
                    'green' => __( 'Green', 'admin-page-framework-demo' ),
                ),
                'reveal' => array( // the field type specific key. This is defined in the
                    'red'   => '<p style="color:red;">' . __( 'You selected red!', 'admin-page-framework-demo' ) . '</p>',
                    'blue'  => '<p style="color:blue;">' . __( 'You selected blue!', 'admin-page-framework-demo' ) . '</p>',
                    'green' => '<p style="color:green;">' . __( 'You selected green!', 'admin-page-framework-demo' ) . '</p>',
                ),
                'repeatable' => true,
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