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
            // argument - for the array structure, see http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
            array(
                'labels'            => $this->_getLabels(),
                'public'            => true,
                'menu_position'     => 110,
                'supports'          => array( 'title' ), // e.g. array( 'title', 'editor', 'comments', 'thumbnail', 'excerpt' ),
                'taxonomies'        => array( '' ),
                'has_archive'       => true,
                'show_admin_column' => true, // [3.5+ core] this is for custom taxonomies to automatically add the column in the listing table.
                'menu_icon'         => $this->oProp->bIsAdmin
                    ? (
                        version_compare( $GLOBALS['wp_version'], '3.8', '>=' )
                            ? 'dashicons-wordpress'
                            : plugins_url( 'asset/image/wp-logo_16x16.png', AdminPageFrameworkLoader_Registry::$sFilePath )
                    )
                    : null, // do not call the function in the front-end.

                // (framework specific) this sets the screen icon for the post type for WordPress v3.7.1 or below.
                // a file path can be passed instead of a url, plugins_url( 'asset/image/wp-logo_32x32.png', APFDEMO_FILE )
                'screen_icon' => AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/image/wp-logo_32x32.png',

                // (framework specific) [3.5.10+] default: true
                'show_submenu_add_new'  => true,

                // (framework specific) [3.7.4+]
                'submenu_order_manage' => 5,   // default 5
                'submenu_order_addnew' => 9,   // default 10
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
                'show_table_filter'     => true,    // (framework specific)
                'show_in_sidebar_menus' => true,    // (framework specific)
                'submenu_order'         => 10,      // (framework specific) Default :15
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
                'show_table_filter'     => true,    // (framework specific)
                'show_in_sidebar_menus' => false,   // (framework specific)
                // 'submenu_order'         => 15,      // (framework specific)
            )
        );

    }
        /**
         * @return      array
         */
        private function _getLabels() {

            return $this->oProp->bIsAdmin
                ? array(
                    'name'               => __( 'APF Demo', 'admin-page-framework-loader' ),
                    'menu_name'          => __( 'APF Demo', 'admin-page-framework-loader' ),
                    'all_items'          => __( 'Manage Sample Posts', 'admin-page-framework-loader' ),
                    'singular_name'      => __( 'APF Demo', 'admin-page-framework-loader' ),
                    'add_new'            => __( 'Add New', 'admin-page-framework-loader' ),
                    'add_new_item'       => __( 'Add New APF Post', 'admin-page-framework-loader' ),
                    'edit'               => __( 'Edit', 'admin-page-framework-loader' ),
                    'edit_item'          => __( 'Edit APF Post', 'admin-page-framework-loader' ),
                    'new_item'           => __( 'New APF Post', 'admin-page-framework-loader' ),
                    'view'               => __( 'View', 'admin-page-framework-loader' ),
                    'view_item'          => __( 'View APF Post', 'admin-page-framework-loader' ),
                    'search_items'       => __( 'Search APF Post', 'admin-page-framework-loader' ),
                    'not_found'          => __( 'No APF Post found', 'admin-page-framework-loader' ),
                    'not_found_in_trash' => __( 'No APF Post found in Trash', 'admin-page-framework-loader' ),
                    'parent'             => __( 'Parent APF Post', 'admin-page-framework-loader' ),

                    // (framework specific)
                    'plugin_action_link' => __( 'APF Posts', 'admin-page-framework-loader' ), // framework specific key. [3.7.3+]
                )
            : array();

        }

    /**
     * Called when the edit.php page starts loading.
     *
     * Alternatively you can use the `load_{post type slug}` method and action hook.
     */
    public function load() {

        $this->setAutoSave( false );
        $this->setAuthorTableFilter( true );
        add_filter( 'request', array( $this, 'replyToSortCustomColumn' ) );

    }

    /**
     * Inserts a custom string into the left footer.
     *
     * @callback        filter      footer_left_{class name}
     */
    public function footer_left_APF_PostType( $sHTML ) {
        return __( 'Custom left footer text.', 'admin-page-framework-loader' ) . '<br />'
            . $sHTML;
    }
    /**
     * Inserts a custom string into the left footer.
     *
     * @callback        filter      footer_left_{class name}
     */
    public function footer_right_APF_PostType( $sHTML ) {
        return __( 'Custom right footer text.', 'admin-page-framework-loader' ) . '<br />'
            . $sHTML;
    }

    /*
     * Built-in callback methods
     *
     * @callback    filter      action_links_{post type slug}
     * @return      array
     */
    public function action_links_apf_posts( $aActionLinks, $oPost ) {
        $_sMessage = esc_attr(
            __( 'You can embed a custom link with the `action_links_{post type slug}` filter hook.', 'admin-page-framework-loader' )
        );
        $aActionLinks[] = "<a href='' title='{$_sMessage}'>"
            . __( 'Sample Link', 'admin-page-framework-loader' )
            . "</a>";
        return $aActionLinks;
    }

    /*
     * Built-in callback methods
     *
     * @callback        filter      columns_{post type slug}
     */
    public function columns_apf_posts( $aHeaderColumns ) {

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

    /**
     *
     * @callback        filter      sortable_columns_{post type slug}
     */
    public function sortable_columns_apf_posts( $aSortableHeaderColumns ) {
        return $aSortableHeaderColumns + array(
            'samplecolumn' => 'samplecolumn',
        );
    }

    /**
     *
     * @callback        filter      cell_{post type}_{column key}
     */
    public function cell_apf_posts_samplecolumn( $sCell, $iPostID ) {

        return sprintf( __( 'Post ID: %1$s', 'admin-page-framework-loader' ), $iPostID ) . "<br />"
            . __( 'Text', 'admin-page-framework-loader' ) . ': ' . get_post_meta( $iPostID, 'metabox_text_field', true );

    }

    /**
     * Custom callback methods
     */

    /**
     * Modifies the way how the sample column is sorted. This makes it sorted by post ID.
     *
     * @see http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters
     * @callback        filter      request
     */
    public function replyToSortCustomColumn( $aVars ){

        if ( isset( $aVars[ 'orderby' ] ) && 'samplecolumn' == $aVars[ 'orderby' ] ){
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

        foreach( ( array ) get_post_custom_keys( $_iPostID ) as $_sKey ) {    // This way, array will be unserialized; easier to view.
            $_aPostData[ $_sKey ] = get_post_meta( $_iPostID, $_sKey, true );
        }

        // Or you may do this but the nested elements will be a serialized array.
        // $_aPostData = get_post_custom( $_iPostID ) ;

        // 2. To retrieve the saved options in the setting pages created by the framework - use the get_option() function.
        // The key name is the class name by default. The key can be changed by passing an arbitrary string
        // to the first parameter of the constructor of the AdminPageFramework class.
        $_aSavedOptions = get_option( 'APF_Demo' );

        return "<h3>" . __( 'Saved Meta Field Values of the Post', 'admin-page-framework-loader' ) . "</h3>"
            . $this->oDebug->get( $_aPostData )
            . "<h3>" . __( 'Saved Setting Options of The Loader Plugin', 'admin-page-framework-loader' ) . "</h3>"
            . $this->oDebug->get( $_aSavedOptions )
            ;

    }

}

new APF_PostType(
    AdminPageFrameworkLoader_Registry::$aPostTypes[ 'demo' ],                // the post type slug
    array(),                    // the argument array. Here an empty array is passed because it is defined inside the class.
    AdminPageFrameworkLoader_Registry::$sFilePath,               // the caller script path.
    'admin-page-framework-loader' // the text domain.
);
