<?php
class APF_Demo_CustomFieldTypes_ACE {
    
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
    public $sTabSlug    = 'ace';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'ace';    
    
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
            
            include( dirname( APFDEMO_FILE ) . '/third-party/ace-custom-field-type/AceCustomFieldType.php' );
            new AceCustomFieldType( $sClassName );                             
            
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
                'title'     => __( 'Code', 'admin-page-framework-demo' ),    
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
                'title'         => __( 'ACE Code Editors', 'admin-page-framework-demo' ),
                'description'   => sprintf( 
                    __( 'This field type uses the external script located at %1$s.', 'admin-page-framework-demo' ),
                   ( is_ssl() ? 'https:' : 'http:' ) . '//cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/ace.js' 
                ),
            )
        );        
        
        // Fields
        $oAdminPage->addSettingFields(
            $this->sSectionID, // the target section id
            array(
                'field_id'      => 'ace_css',
                'type'          => 'ace',     
                'title'         => __( 'CSS', 'admin-page-framework-demo' ),
                'default'       => '.abc { color: #fff; }',
                'attributes'    =>  array(
                    'cols'        => 80,
                    'rows'        => 20,
                ),                
                'options'   => array(
                    'language'              => 'css',
                    'theme'                 => 'chrome',
                    'gutter'                => false,
                    'readonly'              => false,
                    'fontsize'              => 12,
                ),                
            ),
            array(
                'field_id'      => 'ace_php',
                'type'          => 'ace',     
                'title'         => __( 'PHP', 'admin-page-framework-demo' ),
                'default'       => 'echo "hello world!";',
                'attributes'    =>  array(
                    'cols'        => 80,
                    'rows'        => 10,
                ),                
                'options'   => array(
                    'language'              => 'php',
                ),           
                'repeatable'    => true,
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