<?php
/**
 * Admin Page Framework Loader
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds a tab of the set page to the loader plugin.
 * 
 * @since       3.5.0    
 */
class APF_Demo_CustomFieldType_GitHub {

    public $oFactory;
    
    public $sPageSlug;
    
    public $sTabSlug = 'github';

    public function __construct( $oFactory, $sPageSlug ) {
    
        $this->oFactory     = $oFactory;
        $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = $sPageSlug; 
        $this->sSectionID   = $this->sTabSlug;
                
        
        $this->oFactory->addInPageTabs(    
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'GitHub', 'admin-page-framework-loader' ),
            )
        );  
        
        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToLoadTab' ) );
  
    }
        /**
         * Registers the field types.
         */
        private function registerFieldTypes( $sClassName ) {
            new GitHubCustomFieldType( $sClassName );            
        }
        
    /**
     * Triggered when the tab is loaded.
     * 
     * @callback        action      load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oAdminPage ) {
        
        $this->registerFieldTypes( $this->sClassName );
        
        add_action( 'do_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToDoTab' ) );
        
        // Section
        $oAdminPage->addSettingSections(    
            $this->sPageSlug, // the target page slug                
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'GitHub Buttons', 'admin-page-framework-loader' ),
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
                'title'         => __( 'Follow', 'admin-page-framework-loader' ),
                'label'         => __( 'Small & Count', 'admin-page-framework-loader' ),
                
                // field type specific settings
                'user_name'     => 'michaeluno',    // the GitHub account ID
                'button_type'   => 'follow',        // either of the followings: follow, star, watch, fork, issue     
                'repository'    => 'admin-page-framework',
                array(
                    'size'          => 'mega',   //   currently only 'mega' can be supported. Otherwise, a small icon will be used.
                    'label'         => __( 'Mega & Count', 'admin-page-framework-loader' ),    
                ),
                array(
                    'size'          => '',      // pass something not 'mega' to use a small icon.
                    'label'         => __( 'Small', 'admin-page-framework-loader' ),                    
                    'count'         => false,   // whether or not the count should be displayed
                ),
                array(
                    'size'          => 'mega',   
                    'label'         => __( 'Mega', 'admin-page-framework-loader' ),                    
                    'count'         => false,
                ),                                                                    
            ),          
            array(
                'field_id'      => 'github_star',
                'type'          => 'github',     
                'title'         => __( 'Star', 'admin-page-framework-loader' ),
                'label'         => __( 'Small & Count', 'admin-page-framework-loader' ),
                
                // field type specific settings
                'user_name'     => 'michaeluno',    // the GitHub account ID
                'button_type'   => 'star',        // either of the followings: follow, star, watch, fork, issue
                'repository'    => 'admin-page-framework',
                array(
                    'size'          => 'mega',   //   currently only 'mega' can be supported. Otherwise, a small icon will be used.
                    'label'         => __( 'Mega & Count', 'admin-page-framework-loader' ),    
                ),
                array(
                    'size'          => '',      //   pass something not 'mega' to use a small icon.
                    'label'         => __( 'Small', 'admin-page-framework-loader' ),                    
                    'count'         => false,
                ),
                array(
                    'size'          => 'mega',   
                    'label'         => __( 'Mega', 'admin-page-framework-loader' ),                    
                    'count'         => false,
                )                                          
            ),
            array(
                'field_id'      => 'github_watch',
                'type'          => 'github',     
                'title'         => __( 'Watch', 'admin-page-framework-loader' ),
                'label'         => __( 'Small & Count', 'admin-page-framework-loader' ),
                
                // field type specific settings
                'user_name'     => 'michaeluno',    // the GitHub account ID
                'button_type'   => 'watch',        // either of the followings: follow, star, watch, fork, issue
                'repository'    => 'admin-page-framework',
                array(
                    'size'          => 'mega',   //   currently only 'mega' can be supported. Otherwise, a small icon will be used.
                    'label'         => __( 'Mega & Count', 'admin-page-framework-loader' ),    
                ),
                array(
                    'size'          => '',      //   pass something not 'mega' to use a small icon.
                    'label'         => __( 'Small', 'admin-page-framework-loader' ),                    
                    'count'         => false,
                ),
                array(
                    'size'          => 'mega',   
                    'label'         => __( 'Mega', 'admin-page-framework-loader' ),                    
                    'count'         => false,
                )                                          
            ), 
            array(
                'field_id'      => 'github_fork',
                'type'          => 'github',     
                'title'         => __( 'Fork', 'admin-page-framework-loader' ),
                'label'         => __( 'Small & Count', 'admin-page-framework-loader' ),
                
                // field type specific settings
                'user_name'     => 'michaeluno',    // the GitHub account ID
                'button_type'   => 'fork',        // either of the followings: follow, star, watch, fork, issue
                'repository'    => 'admin-page-framework',
                array(
                    'size'          => 'mega',   //   currently only 'mega' can be supported. Otherwise, a small icon will be used.
                    'label'         => __( 'Mega & Count', 'admin-page-framework-loader' ),    
                ),
                array(
                    'size'          => '',      //   pass something not 'mega' to use a small icon.
                    'label'         => __( 'Small', 'admin-page-framework-loader' ),                    
                    'count'         => false,
                ),
                array(
                    'size'          => 'mega',   
                    'label'         => __( 'Mega', 'admin-page-framework-loader' ),                    
                    'count'         => false,
                )                                          
            ),      
            array(
                'field_id'      => 'github_issue',
                'type'          => 'github',     
                'title'         => __( 'Issue', 'admin-page-framework-loader' ),
                'label'         => __( 'Small & Count', 'admin-page-framework-loader' ),
                
                // field type specific settings
                'user_name'     => 'michaeluno',    // the GitHub account ID
                'button_type'   => 'issue',        // either of the followings: follow, star, watch, fork, issue
                'repository'    => 'admin-page-framework',
                array(
                    'size'          => 'mega',   //   currently only 'mega' can be supported. Otherwise, a small icon will be used.
                    'label'         => __( 'Mega & Count', 'admin-page-framework-loader' ),    
                ),
                array(
                    'size'          => '',      //   pass something not 'mega' to use a small icon.
                    'label'         => __( 'Small', 'admin-page-framework-loader' ),                    
                    'count'         => false,
                ),
                array(
                    'size'          => 'mega',   
                    'label'         => __( 'Mega', 'admin-page-framework-loader' ),                    
                    'count'         => false,
                )                                          
            ),             
            array(            
                'field_id'      => 'github_follow_custom_label',
                'type'          => 'github',     
                'title'         => __( 'Custom Label', 'admin-page-framework-loader' ),
                'value'         => __( 'Follow Me', 'admin-page-framework-loader' ),  // <-- the custom label 
                
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
                'title'         => __( 'Download', 'admin-page-framework-loader' ),
                'size'          => 'mega',   
                'count'         => false,
                'attributes'    =>  array(
                    'href'      =>  'https://github.com/michaeluno/admin-page-framework/archive/master.zip',   // the target link url.
                    'data-icon' => 'octicon-cloud-download',    // override the icon. Pass the octicon icon class name.
                ),
                'value'         => __( 'Download', 'admin-page-framework-loader' ),
            ),
            array(            
                'field_id'      => 'github_custom_link_b',
                'type'          => 'github',     
                'title'         => __( 'Gist', 'admin-page-framework-loader' ),
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
    
    public function replyToDoTab() {}
    
}
