<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides the space to store the shared properties for custom post types.
 *
 * This class stores various types of values. This is used to encapsulate properties so that it helps to avoid naming conflicts.
 *
 * @since       2.1.0
 * @package     AdminPageFramework/Factory/PostType/Property
 * @extends     AdminPageFramework_Property_Base
 * @internal
 */
class AdminPageFramework_Property_post_type extends AdminPageFramework_Property_Base {

    /**
     * Defines the property type.
     *
     * @remark      Setting the property type helps to check whether some components are loaded such as scripts that can be reused per a class type basis.
     * @since       3.0.0
     * @internal
     */
    public $_sPropertyType = 'post_type';

    /**
     * Stores the post type slug.
     *
     * @since   2.0.0
     * @since   2.1.0   Moved to AdminPageFramework_Property_post_type.
     * @var     string
     * @access  public
     */
    public $sPostType = '';

    /**
     * Stores the post type argument.
     *
     * @since   2.0.0
     * @since   2.1.0   Moved to AdminPageFramework_Property_post_type.
     * @var     array
     * @access  public
     */
    public $aPostTypeArgs = array();

    /**
     * Stores the extended class name.
     *
     * @since       2.0.0
     * @since       2.1.0     Moved to AdminPageFramework_Property_post_type.
     * @var         string
     * @access      public
     */
    public $sClassName = '';

    /**
     * Stores the column headers of the post listing table.
     *
     * @since       2.0.0
     * @since       2.1.0       Moved to AdminPageFramework_Property_post_type.
     * @see         http://codex.wordpress.org/Plugin_API/Filter_Reference/manage_edit-post_type_columns
     * @remark      This should be overriden in the constructor because it includes translated text.
     * @internal
     * @access      public
     */
    public $aColumnHeaders = array(
        'cb'        => '<input type="checkbox" />', // Checkbox for bulk actions.
        'title'     => 'Title', // Post title. Includes "edit", "quick edit", "trash" and "view" links. If $mode (set from $_REQUEST['mode']) is 'excerpt', a post excerpt is included between the title and links.
        'author'    => 'Author', // Post author.
        'comments'  => '<div class="comment-grey-bubble"></div>', // Number of pending comments.
        'date'      => 'Date',     // The date and publish status of the post.
    );

    /**
     * Stores the sortable column items.
     * @since       2.0.0
     * @since       2.1.0       Moved to AdminPageFramework_Property_post_type.
     * @internal
     */
    public $aColumnSortable = array(
        'title' => true,
        'date'  => true,
    );

    /**
     * Stores the caller script path.
     *
     * @since 2.0.0
     * @since 2.1.0 Moved to AdminPageFramework_Property_post_type.
     * @var string
     * @access public
     */
    public $sCallerPath = '';

    // Containers
    /**
     * Stores custom taxonomy slugs.
     *
     * @since       2.0.0
     * @since       2.1.0       Moved to AdminPageFramework_Property_post_type.
     * @internal
     */
    public $aTaxonomies; // stores the registering taxonomy info.

    /**
     * Stores the object types for the set taxonomies.
     *
     * It will be a multi-dimensional array. The first depth keys are the added taxonomy slugs.
     * Each of them contains an array holding the object types.
     *
     * @since       3.1.1
     * @internal
     */
    public $aTaxonomyObjectTypes = array();

    /**
     * Stores the taxonomy IDs as value to indicate whether the drop-down filter option should be displayed or not.
     *
     * @since       2.0.0
     * @since       2.1.0       Moved to AdminPageFramework_Property_post_type.
     * @internal
     */
    public $aTaxonomyTableFilters = array();

    /**
     * Stores removing taxonomy menus' info.
     *
     * @since       2.0.0
     * @since       2.1.0       Moved to AdminPageFramework_Property_post_type.
     * @internal
     */
    public $aTaxonomyRemoveSubmenuPages = array();

    // Default Values
    /**
     * @since 2.0.0
     * @since 2.1.0 Moved to AdminPageFramework_Property_post_type.
     * @internal
     */
    public $bEnableAutoSave = true;

    /**
     * Stores the flag value which indicates whether author table filters should be enabled or not.
     *
     * @since       2.0.0
     * @since       2.1.0       Moved to AdminPageFramework_Property_post_type.
     * @internal
     */
    public $bEnableAuthorTableFileter = false;

    /**
     * Stores key value pairs of sub-menu link and the order.
     * @since       3.7.4
     */
    public $aTaxonomySubMenuOrder = array();

}
