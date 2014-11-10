<?php
class APF_Demo_CustomFieldTypes_GitHub {
    
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
    public $sTabSlug    = 'github';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'github';
    
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
            
            include( dirname( APFDEMO_FILE ) . '/third-party/github-custom-field-type/GitHubCustomFieldType.php' );
            new GitHubCustomFieldType( $sClassName );     
                        
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
                'title'     => __( 'GitHub', 'admin-page-framework-demo' ),    
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
                'title'         => __( 'GitHub Buttons', 'admin-page-framework-demo' ),
                'description'   => sprintf( __( 'These buttons use GitHub API and perform asynchronomus external access to %1$s.', 'admin-paeg-framework-demo' ), 'https://api.github.com' ),
            )
        );        
        
        // Fields
        // Github buttons. For the arguments, see https://github.com/ntkme/github-buttons#syntax
        $oAdminPage->addSettingFields(
            $this->sSectionID, // the target section id
            array(
                'field_id'      => 'github_follow',
                'type'          => 'github',     
                'title'         => __( 'Follow', 'admin-page-framework-demo' ),
                'label'         => __( 'Small & Count', 'admin-page-framework-demo' ),
                
                // field type specific settings
                'user_name'     => 'michaeluno',    // the GitHub account ID
                'button_type'   => 'follow',        // either of the followings: follow, star, watch, fork, issue     
                'repository'    => 'admin-page-framework',
                array(
                    'size'          => 'mega',   //   currently only 'mega' can be supported. Otherwise, a small icon will be used.
                    'label'         => __( 'Mega & Count', 'admin-page-framework-demo' ),    
                ),
                array(
                    'size'          => '',      // pass something not 'mega' to use a small icon.
                    'label'         => __( 'Small', 'admin-page-framework-demo' ),                    
                    'count'         => false,   // whether or not the count should be displayed
                ),
                array(
                    'size'          => 'mega',   
                    'label'         => __( 'Mega', 'admin-page-framework-demo' ),                    
                    'count'         => false,
                ),                                                                    
            ),          
            array(
                'field_id'      => 'github_star',
                'type'          => 'github',     
                'title'         => __( 'Star', 'admin-page-framework-demo' ),
                'label'         => __( 'Small & Count', 'admin-page-framework-demo' ),
                
                // field type specific settings
                'user_name'     => 'michaeluno',    // the GitHub account ID
                'button_type'   => 'star',        // either of the followings: follow, star, watch, fork, issue
                'repository'    => 'admin-page-framework',
                array(
                    'size'          => 'mega',   //   currently only 'mega' can be supported. Otherwise, a small icon will be used.
                    'label'         => __( 'Mega & Count', 'admin-page-framework-demo' ),    
                ),
                array(
                    'size'          => '',      //   pass something not 'mega' to use a small icon.
                    'label'         => __( 'Small', 'admin-page-framework-demo' ),                    
                    'count'         => false,
                ),
                array(
                    'size'          => 'mega',   
                    'label'         => __( 'Mega', 'admin-page-framework-demo' ),                    
                    'count'         => false,
                )                                          
            ),
            array(
                'field_id'      => 'github_watch',
                'type'          => 'github',     
                'title'         => __( 'Watch', 'admin-page-framework-demo' ),
                'label'         => __( 'Small & Count', 'admin-page-framework-demo' ),
                
                // field type specific settings
                'user_name'     => 'michaeluno',    // the GitHub account ID
                'button_type'   => 'watch',        // either of the followings: follow, star, watch, fork, issue
                'repository'    => 'admin-page-framework',
                array(
                    'size'          => 'mega',   //   currently only 'mega' can be supported. Otherwise, a small icon will be used.
                    'label'         => __( 'Mega & Count', 'admin-page-framework-demo' ),    
                ),
                array(
                    'size'          => '',      //   pass something not 'mega' to use a small icon.
                    'label'         => __( 'Small', 'admin-page-framework-demo' ),                    
                    'count'         => false,
                ),
                array(
                    'size'          => 'mega',   
                    'label'         => __( 'Mega', 'admin-page-framework-demo' ),                    
                    'count'         => false,
                )                                          
            ), 
            array(
                'field_id'      => 'github_fork',
                'type'          => 'github',     
                'title'         => __( 'Fork', 'admin-page-framework-demo' ),
                'label'         => __( 'Small & Count', 'admin-page-framework-demo' ),
                
                // field type specific settings
                'user_name'     => 'michaeluno',    // the GitHub account ID
                'button_type'   => 'fork',        // either of the followings: follow, star, watch, fork, issue
                'repository'    => 'admin-page-framework',
                array(
                    'size'          => 'mega',   //   currently only 'mega' can be supported. Otherwise, a small icon will be used.
                    'label'         => __( 'Mega & Count', 'admin-page-framework-demo' ),    
                ),
                array(
                    'size'          => '',      //   pass something not 'mega' to use a small icon.
                    'label'         => __( 'Small', 'admin-page-framework-demo' ),                    
                    'count'         => false,
                ),
                array(
                    'size'          => 'mega',   
                    'label'         => __( 'Mega', 'admin-page-framework-demo' ),                    
                    'count'         => false,
                )                                          
            ),      
            array(
                'field_id'      => 'github_issue',
                'type'          => 'github',     
                'title'         => __( 'Issue', 'admin-page-framework-demo' ),
                'label'         => __( 'Small & Count', 'admin-page-framework-demo' ),
                
                // field type specific settings
                'user_name'     => 'michaeluno',    // the GitHub account ID
                'button_type'   => 'issue',        // either of the followings: follow, star, watch, fork, issue
                'repository'    => 'admin-page-framework',
                array(
                    'size'          => 'mega',   //   currently only 'mega' can be supported. Otherwise, a small icon will be used.
                    'label'         => __( 'Mega & Count', 'admin-page-framework-demo' ),    
                ),
                array(
                    'size'          => '',      //   pass something not 'mega' to use a small icon.
                    'label'         => __( 'Small', 'admin-page-framework-demo' ),                    
                    'count'         => false,
                ),
                array(
                    'size'          => 'mega',   
                    'label'         => __( 'Mega', 'admin-page-framework-demo' ),                    
                    'count'         => false,
                )                                          
            ),             
            array(            
                'field_id'      => 'github_follow_custom_label',
                'type'          => 'github',     
                'title'         => __( 'Custom Label', 'admin-page-framework-demo' ),
                'value'         => __( 'Follow Me', 'admin-page-framework-demo' ),  // <-- the custom label 
                
                // field type specific settings
                'user_name'     => 'michaeluno',    // the GitHub account ID
                'button_type'   => 'follow',        // either of the followings: follow, star, watch, fork, issue     
                'repository'    => 'admin-page-framework',
                'size'          => 'mega',   
                'count'         => false,
            ),
            array(            
                'field_id'      => 'github_custom_link_a',
                'type'          => 'github',     
                'title'         => __( 'Download', 'admin-page-framework-demo' ),
                'size'          => 'mega',   
                'count'         => false,
                'attributes'    =>  array(
                    'href'      =>  'https://github.com/michaeluno/admin-page-framework/archive/master.zip',   // the target link url.
                    'data-icon' => 'octicon-cloud-download',    // override the icon. Pass the octicon icon class name.
                ),
                'value'         => __( 'Download', 'admin-page-framework-demo' ),
            ),
            array(            
                'field_id'      => 'github_custom_link_b',
                'type'          => 'github',     
                'title'         => __( 'Gist', 'admin-page-framework-demo' ),
                'size'          => 'mega',   
                'count'         => false,
                'attributes'    =>  array(
                    'href'      =>  'https://gist.github.com/schacon/1',   // the target link url.
                    'data-icon' => 'octicon-gist',    // override the icon. Pass the octicon icon class name.
                ),
                'value'         => 'The Meaning of Gist', 
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