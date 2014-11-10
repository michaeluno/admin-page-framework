<?php
class APF_Demo_CustomFieldTypes_Revealer {
    
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
    public $sTabSlug    = 'revealer';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'revealer';
    
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
            
            include( dirname( APFDEMO_FILE ) . '/third-party/revealer-custom-field-type/RevealerCustomFieldType.php' );
            new RevealerCustomFieldType( $sClassName );
                        
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
                'title'     => __( 'Revealer', 'admin-page-framework-demo' ),    
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
                'section_id'    => 'revealer',
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Revealer Custom Field Type', 'admin-page-framework-demo' ),
                'description'   => __( 'When the user selects an item from the selector, it reveals one of the predefined fields.', 'admin-page-framework-demo' ),     
            ),    
            array(
                'section_id'    => 'revealer_section_a',
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Section A', 'admin-page-framework-demo' ),
                'hidden'        => true,
                'class'         => array(
                    'revealer_section_class_a',
                ),
            ),
            array(
                'section_id'    => 'revealer_section_b',
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Section B', 'admin-page-framework-demo' ),
                'hidden'        => true,
                'class'         => array(
                    'revealer_section_class_b',
                ),
            )            
        );        
        
        // Fields   
        $oAdminPage->addSettingFields(
            'revealer', // the target section id
            array(
                'field_id'      => 'revealer_field_by_id',
                'type'          => 'revealer',     
                'title'         => __( 'Reveal Hidden Fields', 'admin-page-framework-demo' ),
                'default'       => 'undefined',
                'label'         => array( // the keys represent the selector to reveal, in this case, their tag id : #fieldrow-{section id}_{field id}
                    'undefined' => __( '-- Select a Field --', 'admin-page-framework-demo' ),     
                    '#fieldrow-revealer_revealer_field_option_a' => __( 'Option A', 'admin-page-framework-demo' ),     
                    '#fieldrow-revealer_revealer_field_option_b, #fieldrow-revealer_revealer_field_option_c' => __( 'Option B and C', 'admin-page-framework-demo' ),
                    '#fieldrow-revealer_another_revealer_field' => __( 'Another Revealer', 'admin-page-framework-demo' ),
                ),
                'description'   => __( 'Specify the selectors to reveal in the <code>label</code> argument keys in the field definition array.', 'admin-page-framework-demo' ),
            ),
            array(
                'field_id'      => 'revealer_field_option_a',
                'type'          => 'textarea',     
                'default'       => __( 'Hi there!', 'admin-page-framework-demo' ),
                'hidden'        => true,
            ),
            array(
                'field_id'      => 'revealer_field_option_b',     
                'type'          => 'password',     
                'description'   => __( 'Type a password.', 'admin-page-framework-demo' ),     
                'hidden'        => true,
            ),
            array(
                'field_id'      => 'revealer_field_option_c',
                'type'          => 'text',     
                'description'   => __( 'Type text.', 'admin-page-framework-demo' ),     
                'hidden'        => true,
            ),
            array(
                'field_id'      => 'another_revealer_field',
                'type'          => 'revealer',  
                'select_type'   => 'radio',
                'title'         => __( 'Another Hidden Fields', 'admin-page-framework-demo' ),
                'label'         => array( // the keys represent the selector to reveal, in this case, their tag id : #fieldrow-{field id}
                    '.revealer_field_option_d' => __( 'Option D', 'admin-page-framework-demo' ),     
                    '.revealer_field_option_e' => __( 'Option E', 'admin-page-framework-demo' ),
                    '.revealer_field_option_f' => __( 'Option F', 'admin-page-framework-demo' ),
                ),
                'hidden'        => true,
                'default'       => '.revealer_field_option_e',
                'delimiter'     => '<br /><br />',
                // Sub-fields
                array(
                    'type'          => 'textarea',     
                    'class'         => array(
                        'field' => 'revealer_field_option_d',
                    ),
                    'label'         => '',
                    'default'       => '',
                    'delimiter'     => '',
                ),        
                array(
                    'type'          => 'radio',
                    'label'         => array(
                        'a' => __( 'A', 'admin-page-framework-demo' ),
                        'b' => __( 'B', 'admin-page-framework-demo' ),
                        'c' => __( 'C', 'admin-page-framework-demo' ),
                    ),
                    'default'       => 'c',
                    'class'         => array(
                        'field' => 'revealer_field_option_e',
                    ),
                    'delimiter'     => '',
                ),                        
                array(
                    'type'          => 'select',     
                    'label'         => array(
                        'i'     => __( 'i', 'admin-page-framework-demo' ),
                        'ii'    => __( 'ii', 'admin-page-framework-demo' ),
                        'iii'   => __( 'iii', 'admin-page-framework-demo' ),
                    ),                
                    'default'       => 'ii',
                    'class'         => array(
                        'field' => 'revealer_field_option_f',
                    ),
                    'delimiter'     => '',
                ),   
                
            ),     
            array()            
        ); 
        $oAdminPage->addSettingFields(
            'revealer', // the target section id
            array(
                'field_id'      => 'reveal_section',
                'type'          => 'revealer',     
                'select_type'   => 'checkbox',
                'title'         => __( 'Reveal Hidden Sections', 'admin-page-framework-demo' ),
                // The revealer field type needs the label argument to be an array, not string.
                'label'         => array( // the keys represent the selector to reveal, in this case, their tag id : #fieldrow-{section id}_{field id}
                    '.revealer_section_class_a' => __( 'Section A', 'admin-page-framework-demo' ),     
                    '.revealer_section_class_b' => __( 'Section B', 'admin-page-framework-demo' ),     
                ),
                'default'       => '.revealer_section_class_a',
                'description'   => __( 'Specify the selectors to reveal in the <code>label</code> argument keys in the field definition array.', 'admin-page-framework-demo' ),
            ),
            array()
        );
            $oAdminPage->addSettingFields(
                'revealer_section_a', // the target section id
                array(
                    'field_id'      => 'checkbox_in_revealer_section_a',
                    'type'          => 'radio',
                    'title'         => __( 'Radio Buttons', 'admin-page-framework-demo' ),
                    'label'         => array(
                        'a' => __( 'Option A', 'admin-page-framework-demo' ),
                        'b' => __( 'Option B', 'admin-page-framework-demo' ),
                    ),
                    'default'   => 'a',
                ),    
                array(
                    'field_id'      => 'color_in_revealer_section_a',
                    'title'         => __( 'Color', 'admin-page-framework-demo' ),
                    'type'          => 'color',
                ),
                array()            
            );
            $oAdminPage->addSettingFields(
                'revealer_section_b', // the target section id
                array(
                    'field_id'      => 'checkbox_in_revealer_section_b',
                    'type'          => 'checkbox',
                    'title'         => __( 'Checkbox', 'admin-page-framework-demo' ),
                    'label'         => __( 'Check me', 'admin-page-framework-demo' ),
                ),      
                array(
                    'field_id'      => 'text_in_revealer_section_b',
                    'title'         => __( 'Text', 'admin-page-framework-demo' ),
                    'type'          => 'text',
                    'repeatable'    => true,
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