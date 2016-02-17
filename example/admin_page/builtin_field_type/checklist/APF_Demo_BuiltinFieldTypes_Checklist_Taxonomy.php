<?php
/**
 * Admin Page Framework - Demo
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds a section in a tab.
 * 
 * @package     AdminPageFramework
 * @subpackage  Example
 */
class APF_Demo_BuiltinFieldTypes_Checklist_Taxonomy {
    
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
    public $sSectionID  = 'taxonomy';
        
    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {
    
        // Section
        $oFactory->addSettingSections(
            $this->sPageSlug, // the target page slug                
            array(
                'tab_slug'          => $this->sTabSlug,
                'section_id'        => $this->sSectionID,
                'title'             => __( 'Post Type', 'admin-page-framework-loader' ),
                'description'       => __( 'Post type check lists (custom checkboxes).', 'admin-page-framework-loader' ),
            )
        );

        $_aTaxnomies = get_taxonomies( '', 'names' );
        
        // Fields
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID        
            array(
                'field_id'              => 'taxonomy_custom_queries',
                'title'                 => __( 'Custom Taxonomy Queries', 'admin-page-framework-loader' ),
                'type'                  => 'taxonomy',
                'description'           =>
                    array(
                        __( 'With the <code>query</code> argument array, you can customize how the terms should be retrieved.', 'admin-page-framework-loader' ),
                        sprintf( __( 'For the structure and the array key specifications, refer to the parameter section of the <a href="%1$s" target="_blank">get_term()</a> function.', 'admin-page-framework-loader' ), 'http://codex.wordpress.org/Function_Reference/get_terms#Parameters' ),
                    ),
                
                // (required)   Determines which taxonomies should be listed
                'taxonomy_slugs'        => $_aTaxnomies,
                    
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
                'title'                 => __( 'Multiple', 'admin-page-framework-loader' ),
                'type'                  => 'taxonomy',
                'taxonomy_slugs'        => $_aTaxnomies,
                'before_field'          => '<p style="clear:both; font-weight: bold;">' . __( 'For I', 'admin-page-framework-loader' ) . '</p>',
                array(
                    'before_field' => '<p style="clear:both; font-weight: bold;">' . __( 'For II', 'admin-page-framework-loader' ) . '</p>',
                ),
                array(
                    'before_field' => '<p style="clear:both; font-weight: bold;">' . __( 'For III', 'admin-page-framework-loader' ) . '</p>',
                ),
            ),
            array(
                'field_id'              => 'taxonomy_checklist_repeatable',
                'title'                 => __( 'Repeatable', 'admin-page-framework-loader' ),
                'type'                  => 'taxonomy',
                'repeatable'            => true,
                'taxonomy_slugs'        => $_aTaxnomies,
            )
        );
      
    }

}
