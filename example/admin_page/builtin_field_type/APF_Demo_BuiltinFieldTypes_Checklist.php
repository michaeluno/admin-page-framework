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

class APF_Demo_BuiltinFieldTypes_Checklist {
  
    /**
     * Stores the caller class name, set in the constructor.
     */   
    public $sClassName = 'APF_Demo';
    
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_builtin_field_types';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'checklist';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'checklists';
    
    /**
     * Sets up hooks and properties.
     */
    public function __construct( $sClassName='', $sPageSlug='', $sTabSlug='' ) {
        
        $this->sClassName   = $sClassName ? $sClassName : $this->sClassName;
        $this->sPageSlug    = $sPageSlug ? $sPageSlug : $this->sPageSlug;
        $this->sTabSlug     = $sTabSlug ? $sTabSlug : $this->sTabSlug;
              
        // load_ + page slug
        add_action( 'load_' . $this->sPageSlug, array( $this, 'replyToAddTab' ) );
        
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
                'title'     => __( 'Checklist', 'admin-page-framework-demo' ),
            )      
        );  
        
        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToAddFormElements' ) );
        
    }
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToAddFormElements( $oAdminPage ) {
        
        // Section
        $oAdminPage->addSettingSections(    
            $this->sPageSlug, // the target page slug                
            array(
                'tab_slug'          => $this->sTabSlug,
                'section_id'        => $this->sSectionID,
                'title'             => __( 'Checklists', 'admin-page-framework-demo' ),
                'description'       => __( 'Post type and taxonomy checklists ( custom checkbox ).', 'admin-page-framework-demo' ),
            )
        );        
        
        /*
         * Check lists
         */
        $oAdminPage->addSettingFields(     
            $this->sSectionID,  // target section id
            array(
                'field_id'              => 'post_type_checklist',
                'title'                 => __( 'Post Types', 'admin-page-framework-demo' ),
                'type'                  => 'posttype',
            ),
            array(
                'field_id'              => 'post_type_checklist_custom_query',
                'title'                 => __( 'Custom Query', 'admin-page-framework-demo' ),
                'type'                  => 'posttype',
                // Accepts query arguments. For the specification, see the arg parameter of get_post_types() function.
                // See:  http://codex.wordpress.org/Function_Reference/get_post_types#Parameters
                'query'                 => array(
                    'public'   => true,
                    '_builtin' => false,
                ),
                'select_all_button'     => false,        // 3.3.0+   to change the label, set the label here
                'select_none_button'    => false,        // 3.3.0+   to change the label, set the label here        
                'operator'              => 'and',   // can be 'or'
                'slugs_to_remove'       => array(), // if not set, the following slugs will be automatically removed. 'revision',  'attachment',  'nav_menu_item'.
                'description'           => __( 'With the <code>query</code> argument, you can query post types to retrieve.', 'admin-page-framework-demo' )
                    . ' ' . sprintf( __( 'For the specification, see the <a href="%1$s">Parameter</a> section of codex for the <code>get_post_types()</code> function.', 'admin-page-framework-demo' ), 'http://codex.wordpress.org/Function_Reference/get_post_types#Parameters' ) ,
            ),    
            array(
                'field_id'              => 'post_type_checklist_repeatable',
                'title'                 => __( 'Repeatable', 'admin-page-framework-demo' ),
                'type'                  => 'posttype',
                'repeatable'            => true,
                'delimiter'             => '<hr />',
            ),      
            array(  
                'field_id'              => 'taxonomy_checklist',
                'title'                 => __( 'Taxonomy Checklist', 'admin-page-framework-demo' ),
                'type'                  => 'taxonomy',
                'height'                => '200px', // (optional)
                'show_post_count'       => true,    // (optional) whether to show the post count. Default: false.
                'taxonomy_slugs'        => array( 'category', 'post_tag' ),
                'select_all_button'     => false,        // 3.3.0+   to change the label, set the label here
                'select_none_button'    => false,        // 3.3.0+   to change the label, set the label here        
            ),      
            array(  
                'field_id'              => 'taxonomy_custom_queries',
                'title'                 => __( 'Custom Taxonomy Queries', 'admin-page-framework-demo' ),
                'type'                  => 'taxonomy',
                'description'           => 
                    array(
                        __( 'With the <code>query</code> argument array, you can customize how the terms should be retrieved.', 'admin-page-framework-demo' ),
                        sprintf( __( 'For the structure and the array key specifications, refer to the parameter section of the <a href="%1$s" target="_blank">get_term()</a> function.', 'admin-page-framework-demo' ), 'http://codex.wordpress.org/Function_Reference/get_terms#Parameters' ),
                    ),
                
                // (required)   Determines which taxonomies should be listed
                'taxonomy_slugs'        => $aTaxnomies = get_taxonomies( '', 'names' ),    
                    
                // (optional) This defines the default query argument. For the structure and supported arguments, see http://codex.wordpress.org/Function_Reference/get_terms#Parameters
                'query'                 => array(
                    'depth'     => 2,
                    'orderby'   => 'term_id',       // accepts 'ID', 'term_id', or 'name'
                    'order'     => 'DESC',
                    // 'exclude'   => '1', // removes the 'Uncategorized' category.
                    // 'search' => 'PHP',   // the search keyward
                    // 'parent'    => 9,    // only show terms whose direct parent ID is 9.
                    // 'child_of'  => 8,    // only show child terms of the term ID of 8.
                ),
                // (optional) This allows to set a query argument for each taxonomy. 
                // Note that each element will be merged with the above default 'query' argument array. 
                // So unset keys here will be overridden by the default argument array above. 
                'queries'               => array(
                    // taxonomy slug => query argument array
                    'category'  =>  array(
                        'depth'     => 2,
                        'orderby'   => 'term_id',  
                        'order'     => 'DESC',
                        'exclude'   => array( 1 ), 
                    ),
                    'post_tag'  => array(
                        'orderby'   => 'name',
                        'order'     => 'ASC',
                        // 'include'   => array( 4, ), // term ids
                    ),
                ), 
            ),
            array(
                'field_id'              => 'taxonomy_multiple_checklists',
                'title'                 => __( 'Multiple', 'admin-page-framework-demo' ),
                'type'                  => 'taxonomy',
                'taxonomy_slugs'        => $aTaxnomies,
                'before_field'          => '<p style="clear:both; font-weight: bold;">' . __( 'For I', 'admin-page-framework-demo' ) . '</p>',
                array(  
                    'before_field' => '<p style="clear:both; font-weight: bold;">' . __( 'For II', 'admin-page-framework-demo' ) . '</p>',
                ),
                array(  
                    'before_field' => '<p style="clear:both; font-weight: bold;">' . __( 'For III', 'admin-page-framework-demo' ) . '</p>',
                ),     
            ),
            array(
                'field_id'              => 'taxonomy_checklist_repeatable',
                'title'                 => __( 'Repeatable', 'admin-page-framework-demo' ),
                'type'                  => 'taxonomy',
                'repeatable'            => true,
                'taxonomy_slugs'        => $aTaxnomies,
            ),
            array()
        );


    }
    
}