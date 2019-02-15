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
class APF_Demo_BuiltinFieldTypes_Checklist_PostType {

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
    public $sSectionID  = 'post_type';

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

        // Fields
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID
            array(
                'field_id'              => 'post_type_checklist',
                'title'                 => __( 'Post Types', 'admin-page-framework-loader' ),
                'type'                  => 'posttype',
                'description'           => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'                  => 'posttype',
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'              => 'post_type_checklist_custom_query',
                'title'                 => __( 'Custom Query', 'admin-page-framework-loader' ),
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
                'description'           => array(
                    __( 'With the <code>query</code> argument, you can query post types to retrieve.', 'admin-page-framework-loader' )
                    . ' ' . sprintf( __( 'For the specification, see the <a href="%1$s">Parameter</a> section of codex for the <code>get_post_types()</code> function.', 'admin-page-framework-loader' ), 'http://codex.wordpress.org/Function_Reference/get_post_types#Parameters' ),
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'                  => 'posttype',
    
    // Accepts query arguments. For the specification, 
    // see the arg parameter of get_post_types() function.
    // http://codex.wordpress.org/Function_Reference/get_post_types#Parameters
    'query'                 => array(
        'public'   => true,
        '_builtin' => false,
    ),
    
    // To change the label, set the label here
    'select_all_button'     => false,        
    
    // To change the label, set the label here        
    'select_none_button'    => false,        
    
    // Accepts either `and` or `or`
    'operator'              => 'and',   
    
    // If not set, the following slugs will be automatically removed. 
    // 'revision',  'attachment',  'nav_menu_item'.
    'slugs_to_remove'       => array(), 
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'              => 'post_type_checklist_repeatable',
                'title'                 => __( 'Repeatable', 'admin-page-framework-loader' ),
                'type'                  => 'posttype',
                'repeatable'            => true,
                'delimiter'             => '<hr />',
                'description'           => array(
                    __( 'With the <code>query</code> argument, you can query post types to retrieve.', 'admin-page-framework-loader' )
                    . ' ' . sprintf( __( 'For the specification, see the <a href="%1$s">Parameter</a> section of codex for the <code>get_post_types()</code> function.', 'admin-page-framework-loader' ), 'http://codex.wordpress.org/Function_Reference/get_post_types#Parameters' ),
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'                  => 'posttype',
    'repeatable'            => true,
)
EOD
                        )
                        . "</pre>",
                ),
            )
        );

    }

}
