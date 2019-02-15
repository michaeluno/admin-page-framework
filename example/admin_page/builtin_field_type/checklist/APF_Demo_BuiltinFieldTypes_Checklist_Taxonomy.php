<?php
/**
 * Admin Page Framework - Demo
 *
 * Demonstrates the usage of Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 *
 */

/**
 * Adds a section in a tab.
 *
 * @package     AdminPageFramework/Example
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
                'title'             => __( 'Taxonomies', 'admin-page-framework-loader' ),
                'description'       => __( 'Taxonomy term check lists (custom checkboxes).', 'admin-page-framework-loader' ),
            )
        );

        $_aTaxnomies = get_taxonomies( '', 'names' );

        // Fields
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID
            array(
                'field_id'              => 'taxonomy_checklist',
                'title'                 => __( 'Taxonomy Checklist', 'admin-page-framework-loader' ),
                'type'                  => 'taxonomy',
                'height'                => '200px', // (optional)
                'width'                 => '400px', // (optional)
                'show_post_count'       => true,    // (optional) whether to show the post count. Default: false.
                'taxonomy_slugs'        => array( 'category', 'post_tag', ),
                'select_all_button'     => false,        // 3.3.0+   to change the label, set the label here
                'select_none_button'    => false,        // 3.3.0+   to change the label, set the label here
                'description'           => array(
                    __( 'With the <code>query</code> argument, you can query post types to retrieve.', 'admin-page-framework-loader' )
                    . ' ' . sprintf( __( 'For the specification, see the <a href="%1$s">Parameter</a> section of codex for the <code>get_post_types()</code> function.', 'admin-page-framework-loader' ), 'http://codex.wordpress.org/Function_Reference/get_post_types#Parameters' ),
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'                  => 'taxonomy',
    'height'                => '200px', 
    'width'                 => '400px', 
    
    // Whether to show the post count. Default: false.
    'show_post_count'       => true,    
    'taxonomy_slugs'        => array( 'category', 'post_tag', ),
    
    // To change the label, set the label here
    'select_all_button'     => false,        
    'select_none_button'    => false,
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'              => 'taxonomy_custom_queries',
                'title'                 => __( 'Custom Taxonomy Queries', 'admin-page-framework-loader' ),
                'type'                  => 'taxonomy',

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
                'save_unchecked'       => false,   // 3.8.8+
                'description'           => array(
                    __( 'With the <code>query</code> argument array, you can customize how the terms should be retrieved.', 'admin-page-framework-loader' ),
                    sprintf( __( 'For the structure and the array key specifications, refer to the parameter section of the <a href="%1$s" target="_blank">get_term()</a> function.', 'admin-page-framework-loader' ), 'http://codex.wordpress.org/Function_Reference/get_terms#Parameters' ),
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'                  => 'taxonomy',
                
    // (required)   Determines which taxonomies should be listed
    'taxonomy_slugs'        => get_taxonomies( '', 'names' ),
        
    // (optional) This defines the default query argument. 
    // For the structure and supported arguments, 
    // see http://codex.wordpress.org/Function_Reference/get_terms#Parameters
    'query'                 => array(
        'depth'     => 2,        
        'orderby'   => 'term_id', // either 'ID', 'term_id', or 'name'
        'order'     => 'DESC',
        // 'exclude'   => '1',  // removes the 'Uncategorized' category.
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
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'              => 'taxonomy_checklist_repeatable',
                'title'                 => __( 'Repeatable', 'admin-page-framework-loader' ),
                'type'                  => 'taxonomy',
                'repeatable'            => true,
                'taxonomy_slugs'        => $_aTaxnomies,
                'description'           => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'                  => 'taxonomy',
    'repeatable'            => true,
    
    // (required)   Determines which taxonomies should be listed
    'taxonomy_slugs'        => get_taxonomies( '', 'names' ),
    
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array()
        );

    }

}
