<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods for registering custom post types.
 *
 * @abstract
 * @since           2.0.0
 * @package         AdminPageFramework/Factory/PostType
 */
abstract class AdminPageFramework_PostType extends AdminPageFramework_PostType_Controller {

    /**
     * Defines the class object structure type.
     *
     * @since       3.7.12
     * @internal
     */
    protected $_sStructureType = 'post_type';

    /**
    * The constructor of the class object.
    *
    * Registers necessary hooks and sets up internal properties.
    *
    * <h4>Example</h4>
    * <code>new APF_PostType(
    *     'apf_posts',     // post type slug
    *       array(
    *           'labels' => array(
    *               'name'               => 'Demo',
    *               'all_items'          => __( 'Sample Posts', 'admin-page-framework-demo' ),
    *               'singular_name'      => 'Demo',
    *               'add_new'            => __( 'Add New', 'admin-page-framework-demo' ),
    *               'add_new_item'       => __( 'Add New APF Post', 'admin-page-framework-demo' ),
    *               'edit'               => __( 'Edit', 'admin-page-framework-demo' ),
    *               'edit_item'          => __( 'Edit APF Post', 'admin-page-framework-demo' ),
    *               'new_item'           => __( 'New APF Post', 'admin-page-framework-demo' ),
    *               'view'               => __( 'View', 'admin-page-framework-demo' ),
    *               'view_item'          => __( 'View APF Post', 'admin-page-framework-demo' ),
    *               'search_items'       => __( 'Search APF Post', 'admin-page-framework-demo' ),
    *               'not_found'          => __( 'No APF Post found', 'admin-page-framework-demo' ),
    *               'not_found_in_trash' => __( 'No APF Post found in Trash', 'admin-page-framework-demo' ),
    *               'parent'             => __( 'Parent APF Post', 'admin-page-framework-demo' ),
    *
    *               // (framework specific)
    *               'plugin_action_link' => __( 'APF Posts', 'admin-page-framework-demo' ), // framework specific key. [3.7.3+]
    *           ),
    *           'public'            => true,
    *           'menu_position'     => 110,
    *           'supports'          => array( 'title' ), // e.g. array( 'title', 'editor', 'comments', 'thumbnail', 'excerpt' ),
    *           'taxonomies'        => array( '' ),
    *           'has_archive'       => true,
    *           'show_admin_column' => true, // [3.5+ core] this is for custom taxonomies to automatically add the column in the listing table.
    *           'menu_icon'         => $this->oProp->bIsAdmin
    *               ? (
    *                   version_compare( $GLOBALS['wp_version'], '3.8', '>=' )
    *                       ? 'dashicons-wordpress'
    *                       : plugins_url( 'asset/image/wp-logo_16x16.png', APFDEMO_FILE )
    *               )
    *               : null, // do not call the function in the front-end.
    *
    *           // (framework specific) this sets the screen icon for the post type for WordPress v3.7.1 or below.
    *           // a file path can be passed instead of a url, plugins_url( 'asset/image/wp-logo_32x32.png', APFDEMO_FILE )
    *           'screen_icon' => dirname( APFDEMO_FILE  ) . '/asset/image/wp-logo_32x32.png',
    *
    *           // [3.5.10+] (framework specific) default: true
    *           'show_submenu_add_new'  => true,
    *
    *           // [3.7.4+] (framework specific) default: 10
    *           'submenu_order_manage'  => 20,
    *           'submenu_order_addnew'  => 21,
    *
    *       )
    * );</code>
    *
    * <h4>Framework Specific Post Type Arguments</h4>
    * In addition to the post type argument structure defined by the WordPress core, there are arguments defined by the framework.
    *
    * - screen_icon - For WordPress 3.7.x or below, set an icon url or path for the 32x32 screen icon displayed in the post listing page.
    * - show_submenu_add_new - [3.5.10+]    (boolean) Whether the sub-menu item of `Add New` should be displayed.
    * - submenu_order_manage - [3.7.4+]     (numeric) The menu position of the `Manage` sub-menu item which gets automatically crated by the system when the admin ui is enabled. Default: `5`
    * - submenu_order_addnew - [3.7.4+]     (numeric) The menu position of the `Manage` sub-menu item which gets automatically crated by the system when the admin ui is enabled. Default: `10`
    *
    * <h4>Framework Specific Post Type Label Arguments</h4>
    * - plugin_listing_table_title_cell_link' - [3.0.6+] Deprecated [3.7.3] use the `plugin_action_link` argument instead.
    * - plugin_action_link' - [3.7.3+] If the caller script is a plugin, this determines the label of the action link embedded in the plugin listing page (plugins.php).
    * To disable the action link, set an empty string `''`.

    *
    * @since        2.0.0
    * @since        2.1.6       Added the $sTextDomain parameter.
    * @see          http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
    * @param        string      The post type slug.
    * @param        array       The <a href="http://codex.wordpress.org/Function_Reference/register_post_type#Arguments">argument array</a> passed to register_post_type().
    * @param        string      The path of the caller script. This is used to retrieve the script information to insert it into the footer. If not set, the framework tries to detect it.
    * @param        string      The text domain of the caller script.
    * @return       void
    */
    public function __construct( $sPostType, $aArguments=array(), $sCallerPath=null, $sTextDomain='admin-page-framework' ) {

        if ( empty( $sPostType ) ) {
            return;
        }

        $_sPropertyClassName = isset( $this->aSubClassNames[ 'oProp' ] )
            ? $this->aSubClassNames[ 'oProp' ]
            : 'AdminPageFramework_Property_' . $this->_sStructureType;
        $this->oProp = new $_sPropertyClassName(
            $this,
            $this->_getCallerScriptPath( $sCallerPath ),
            get_class( $this ),     // class name
            'publish_posts',        // capability
            $sTextDomain,           // text domain
            $this->_sStructureType  // structure type
        );
        $this->oProp->sPostType     = AdminPageFramework_WPUtility::sanitizeSlug( $sPostType );

        // Post type argument array structure
        // @see http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
        $this->oProp->aPostTypeArgs = $aArguments;

        // Let the factory router set up sub-class objects.
        parent::__construct( $this->oProp );

    }
        /**
         * Attempts to find the caller script path.
         * @remark      This is important to do it here when separating the library into multiple files.
         * @since       3.7.0
         * @return      string|null
         */
        private function _getCallerScriptPath( $sCallerPath ) {

            $sCallerPath = trim( $sCallerPath );
            if ( $sCallerPath ) {
                return $sCallerPath;
            }

            if ( ! is_admin() ) {
                return null;
            }
            $_sPageNow = AdminPageFramework_Utility::getElement( $GLOBALS, 'pagenow' );
            if (
                in_array(
                    $_sPageNow,
                    array( 'edit.php', 'post.php', 'post-new.php', 'plugins.php', 'tags.php', 'edit-tags.php', 'term.php' )
                )
            ) {
                return AdminPageFramework_Utility::getCallerScriptPath( __FILE__ );
            }
            return null;

        }

}
