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

class APF_PostType extends AdminPageFramework_PostType {
    
    /**
     * This method is called at the end of the constructor.
     * 
     * ALternatevely, you may use the start_{instantiated class name} method, which also is called at the end of the constructor.
     */
    public function start() {}
    
    /**
     * Use this method to set up the post type.
     * 
     * ALternatevely, you may use the set_up_{instantiated class name} method, which also is called at the end of the constructor.
     */
    public function setUp() {    

        $this->setArguments(
            array( // argument - for the array structure, refer to http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
                'labels' => array(
                    'name'               => 'Admin Page Framework',
                    'all_items'          => __( 'Sample Posts', 'admin-page-framework-demo' ),
                    'singular_name'      => 'Admin Page Framework',
                    'add_new'            => __( 'Add New', 'admin-page-framework-demo' ),
                    'add_new_item'       => __( 'Add New APF Post', 'admin-page-framework-demo' ),
                    'edit'               => __( 'Edit', 'admin-page-framework-demo' ),
                    'edit_item'          => __( 'Edit APF Post', 'admin-page-framework-demo' ),
                    'new_item'           => __( 'New APF Post', 'admin-page-framework-demo' ),
                    'view'               => __( 'View', 'admin-page-framework-demo' ),
                    'view_item'          => __( 'View APF Post', 'admin-page-framework-demo' ),
                    'search_items'       => __( 'Search APF Post', 'admin-page-framework-demo' ),
                    'not_found'          => __( 'No APF Post found', 'admin-page-framework-demo' ),
                    'not_found_in_trash' => __( 'No APF Post found in Trash', 'admin-page-framework-demo' ),
                    'parent'             => __( 'Parent APF Post', 'admin-page-framework-demo' ),
                    'plugin_listing_table_title_cell_link' => __( 'APF Posts', 'admin-page-framework-demo' ), // framework specific key. [3.0.6+]
                ),
                'public'            => true,
                'menu_position'     => 110,
                'supports'          => array( 'title' ), // e.g. array( 'title', 'editor', 'comments', 'thumbnail', 'excerpt' ),    
                'taxonomies'        => array( '' ),
                'has_archive'       => true,
                'show_admin_column' => true, // [3.5+ core] this is for custom taxonomies to automatically add the column in the listing table.
                'menu_icon'         => $this->oProp->bIsAdmin 
                    ? ( version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ? 'dashicons-wordpress' : plugins_url( 'asset/image/wp-logo_16x16.png', APFDEMO_FILE ) )
                    : null, // do not call the function in the front-end.
                // ( framework specific key ) this sets the screen icon for the post type for WordPress v3.7.1 or below.
                'screen_icon' => dirname( APFDEMO_FILE  ) . '/asset/image/wp-logo_32x32.png', // a file path can be passed instead of a url, plugins_url( 'asset/image/wp-logo_32x32.png', APFDEMO_FILE )
            )    
        );
        
        $this->addTaxonomy( 
            'apf_sample_taxonomy',  // taxonomy slug
            array(                  // argument - for the argument array keys, refer to : http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
                'labels'                => array(
                    'name'          => 'Sample Genre',
                    'add_new_item'  => 'Add New Genre',
                    'new_item_name' => "New Genre"
                ),
                'show_ui'               => true,
                'show_tagcloud'         => false,
                'hierarchical'          => true,
                'show_admin_column'     => true,
                'show_in_nav_menus'     => true,
                'show_table_filter'     => true,    // framework specific key
                'show_in_sidebar_menus' => true,    // framework specific key
            )
        );
        $this->addTaxonomy( 
            'apf_second_taxonomy',
            array(
                'labels'                => array(
                    'name'          => 'Non Hierarchical',
                    'add_new_item'  => 'Add New Taxonomy',
                    'new_item_name' => "New Sample Taxonomy"
                ),
                'show_ui'               => true,
                'show_tagcloud'         => false,
                'hierarchical'          => false,
                'show_admin_column'     => true,
                'show_in_nav_menus'     => false,
                'show_table_filter'     => true,    // framework specific key
                'show_in_sidebar_menus' => false,   // framework specific key
            )
        );
                
        if ( $this->oProp->bIsAdmin ) {
                
            $this->setAutoSave( false );
            $this->setAuthorTableFilter( true );     
            $this->setFooterInfoLeft( '<br />Custom Text on the left hand side.' );
            $this->setFooterInfoRight( '<br />Custom text on the right hand side' );     
            add_filter( 'request', array( $this, 'replyToSortCustomColumn' ) );
            
        }    
        
    }
    
    /*
     * Built-in callback methods
     */
    public function columns_apf_posts( $aHeaderColumns ) { // columns_{post type slug}
        
        return array_merge( 
            $aHeaderColumns,
            array(
                'cb'                => '<input type="checkbox" />', // Checkbox for bulk actions. 
                'title'             => __( 'Title', 'admin-page-framework' ), // Post title. Includes "edit", "quick edit", "trash" and "view" links. If $mode (set from $_REQUEST['mode']) is 'excerpt', a post excerpt is included between the title and links.
                'author'            => __( 'Author', 'admin-page-framework' ), // Post author.
                // 'categories'     => __( 'Categories', 'admin-page-framework' ), // Categories the post belongs to. 
                // 'tags' => __( 'Tags', 'admin-page-framework' ), // Tags for the post. 
                'comments'          => '<div class="comment-grey-bubble"></div>', // Number of pending comments. 
                'date'              => __( 'Date', 'admin-page-framework' ),     // The date and publish status of the post. 
                'samplecolumn'      => __( 'Sample Column' ),
            )     
        );
        
    }
    public function sortable_columns_apf_posts( $aSortableHeaderColumns ) { // sortable_columns_{post type slug}
        return $aSortableHeaderColumns + array(
            'samplecolumn' => 'samplecolumn',
        );
    }    
    public function cell_apf_posts_samplecolumn( $sCell, $iPostID ) { // cell_{post type}_{column key}
        
        return sprintf( __( 'Post ID: %1$s', 'admin-page-framework-demo' ), $iPostID ) . "<br />"
            . __( 'Text', 'admin-page-framework-demo' ) . ': ' . get_post_meta( $iPostID, 'metabox_text_field', true );
        
    }
    
    /**
     * Custom callback methods
     */
    
    /**
     * Modifies the way how the sample column is sorted. This makes it sorted by post ID.
     * 
     * @see http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters
     */
    public function replyToSortCustomColumn( $aVars ){

        if ( isset( $aVars['orderby'] ) && 'samplecolumn' == $aVars['orderby'] ){
            $aVars = array_merge( 
                $aVars, 
                array(
                    'meta_key'  => 'metabox_text_field',
                    'orderby'   => 'meta_value',
                )
            );
        }
        return $aVars;
    }    
    
    /**
     * Modifies the output of the post content.
     * 
     * This method is called in the single page of this class post type.
     * 
     * Alternatively, you may use the 'content_{instantiated class name}' method,
     */
    public function content( $sContent ) { 
                    
        // 1. To retrieve the meta box data - get_post_meta( $post->ID ) will return an array of all the meta field values.
        // or if you know the field id of the value you want, you can do $value = get_post_meta( $post->ID, $field_id, true );
        $_iPostID   = $GLOBALS['post']->ID;
        $_aPostData = array();
        foreach( ( array ) get_post_custom_keys( $_iPostID ) as $sKey ) {    // This way, array will be unserialized; easier to view.
            $_aPostData[ $sKey ] = get_post_meta( $_iPostID, $sKey, true );
        }    
        
        // Or you may do this but the nested elements will be a serialized array.
        // $_aPostData = get_post_custom( $_iPostID ) ;
        
        // 2. To retrieve the saved options in the setting pages created by the framework - use the get_option() function.
        // The key name is the class name by default. The key can be changed by passing an arbitrary string 
        // to the first parameter of the constructor of the AdminPageFramework class.     
        $_aSavedOptions = get_option( 'APF_Demo' );
            
        return "<h3>" . __( 'Saved Meta Field Values', 'admin-page-framework-demo' ) . "</h3>" 
            . $this->oDebug->getArray( $_aPostData )
            . "<h3>" . __( 'Saved Setting Options', 'admin-page-framework-demo' ) . "</h3>" 
            . $this->oDebug->getArray( $_aSavedOptions )
            ;

    }    
    
}