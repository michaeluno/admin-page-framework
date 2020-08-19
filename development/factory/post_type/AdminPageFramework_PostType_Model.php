<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods of models for the post type factory class.
 *
 * Those methods are internal and deal with internal properties.
 *
 * @abstract
 * @since           3.0.4
 * @package         AdminPageFramework/Factory/PostType
 * @internal
 */
abstract class AdminPageFramework_PostType_Model extends AdminPageFramework_PostType_Router {

    /**
     * Sets up hooks and properties.
     *
     * @internal
     * @remark      Make sure to call the parent construct first as the factory router need to set up sub-class objects.
     */
    public function __construct( $oProp ) {

        parent::__construct( $oProp );

        /**
         * Post type registration should be done after the setUp() method.
         * Post type has front-tend components so should not be admin_init. Also "if ( is_admin() )" should not be used either.
         */
        add_action( "set_up_{$this->oProp->sClassName}", array( $this, '_replyToRegisterPostType' ), 999 );

        if ( $this->oProp->bIsAdmin ) {

            add_action( 'load_' . $this->oProp->sPostType, array( $this, '_replyToSetUpHooksForModel' ) );

            if ( $this->oProp->sCallerPath ) {
                new AdminPageFramework_PostType_Model__FlushRewriteRules( $this );
            }

        }

    }

    /**
     * Called when the current screen is determined.
     * @callback    action      load_{post type slug}
     * @since       3.7.9
     */
    public function _replyToSetUpHooksForModel() {

        // For table columns
        add_filter( "manage_{$this->oProp->sPostType}_posts_columns", array( $this, '_replyToSetColumnHeader' ) );
        add_filter( "manage_edit-{$this->oProp->sPostType}_sortable_columns", array( $this, '_replyToSetSortableColumns' ) );
        add_action( "manage_{$this->oProp->sPostType}_posts_custom_column", array( $this, '_replyToPrintColumnCell' ), 10, 2 );

        // Auto-save
        add_action( 'admin_enqueue_scripts', array( $this, '_replyToDisableAutoSave' ) );

        // Properties - sets translatable labels.
        $this->oProp->aColumnHeaders = array(
            'cb'        => '<input type="checkbox" />',     // Checkbox for bulk actions.
            'title'     => $this->oMsg->get( 'title' ),     // Post title. Includes "edit", "quick edit", "trash" and "view" links. If $mode (set from $_REQUEST['mode']) is 'excerpt', a post excerpt is included between the title and links.
            'author'    => $this->oMsg->get( 'author' ),    // Post author.
            'comments'  => '<div class="comment-grey-bubble"></div>', // Number of pending comments.
            'date'      => $this->oMsg->get( 'date' ),      // The date and publish status of the post.
        );

    }

    /**
     * Defines the sortable column items in the custom post listing table.
     *
     * This method should be overridden by the user in their extended class.
     *
     * @internal
     * @since       2.0.0
     * @remark      A callback for the `manage_edit-{post type}_sortable_columns` hook.
     * @filter      add|apply       sortable_columns_{post type slug}
     * @return      array
     */
    public function _replyToSetSortableColumns( $aColumns ) {
        return $this->oUtil->getAsArray(
            $this->oUtil->addAndApplyFilter(
                $this,
                "sortable_columns_{$this->oProp->sPostType}",
                $aColumns
            )
        );
    }


    /**
     * Defines the column header items in the custom post listing table.
     *
     * This method should be overridden by the user in their extended class.
     *
     * @internal
     * @since       2.0.0
     * @remark      A callback for the <em>manage_{post type}_post)_columns</em> hook.
     * @filter      add|apply       columns_{post type slug}
     * @callback    filter          manage_{post type slug}_posts_columns
     * @return      array
     */
    public function _replyToSetColumnHeader( $aHeaderColumns ) {
        return $this->oUtil->getAsArray(
            $this->oUtil->addAndApplyFilter(
                $this,
                "columns_{$this->oProp->sPostType}",
                $aHeaderColumns
            )
        );
    }

    /**
     * Prints the cell column output.
     *
     * @internal
     * @since       3.0.x
     * @since       3.5.0       Renamed from `_replyToSetColumnCell`.
     * @callback    action      manage_{post type slug}_posts_custom_column
     * @return      string
     */
    public function _replyToPrintColumnCell( $sColumnKey, $iPostID ) {
        echo $this->oUtil->addAndApplyFilter(
            $this,
            "cell_{$this->oProp->sPostType}_{$sColumnKey}",
            '',  // value to be filtered - cell output
            $iPostID
        );
    }

    /**
     * Disables the WordPress's built-in auto-save functionality.
     *
     * @internal
     * @callback    action      admin_enqueue_scripts
     * @return      void
     */
    public function _replyToDisableAutoSave() {

        if ( $this->oProp->bEnableAutoSave ) {
            return;
        }
        if ( $this->oProp->sPostType != get_post_type() ) {
            return;
        }
        wp_dequeue_script( 'autosave' );

    }

    /**
     * Registers the post type passed to the constructor.
     *
     * @internal
     * @callback    action      set_up_{instantiated class name}
     * @return      void
     */
    public function _replyToRegisterPostType() {

        register_post_type(
            $this->oProp->sPostType,
            $this->oProp->aPostTypeArgs
        );

        new AdminPageFramework_PostType_Model__SubMenuOrder( $this );

    }

    /**
     * Registers the set custom taxonomies.
     *
     * @see         http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
     * @internal
     */
    public function _replyToRegisterTaxonomies() {
        foreach( $this->oProp->aTaxonomies as $_sTaxonomySlug => $_aArguments ) {
            $this->_registerTaxonomy(
                $_sTaxonomySlug,
                $this->oUtil->getAsArray( $this->oProp->aTaxonomyObjectTypes[ $_sTaxonomySlug ] ), // object types
                $_aArguments
            );
        }
    }

    /**
     * Registers a taxonomy.
     * @since       3.2.0
     * @internal
     */
    public function _registerTaxonomy( $sTaxonomySlug, array $aObjectTypes, array $aArguments ) {

        if ( ! in_array( $this->oProp->sPostType, $aObjectTypes ) ) {
            $aObjectTypes[] = $this->oProp->sPostType;
        }
        register_taxonomy(
            $sTaxonomySlug,
            array_unique( $aObjectTypes ), // object types
            $aArguments // for the argument array keys, refer to: http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
        );

        $this->_setCustomMenuOrderForTaxonomy(
            $this->oUtil->getElement( $aArguments, 'submenu_order', 15 ),
            $sTaxonomySlug
        );


    }
        /**
         * Allows the user set a custom sub-menu order (index, position).
         *
         * @since       3.7.4
         * @internal
         */
        private function _setCustomMenuOrderForTaxonomy( $nSubMenuOrder, $sTaxonomySlug ) {

            // If the user does not set a custom value, no need to modify the sub-menu array.
            if ( 15 == $nSubMenuOrder ) {
                return;
            }
            $this->oProp->aTaxonomySubMenuOrder[ "edit-tags.php?taxonomy={$sTaxonomySlug}&amp;post_type={$this->oProp->sPostType}" ] = $nSubMenuOrder;

        }

    /**
     * Removes taxonomy menu items from the sidebar menu.
     *
     * @internal
     */
    public function _replyToRemoveTexonomySubmenuPages() {

        foreach( $this->oProp->aTaxonomyRemoveSubmenuPages as $sSubmenuPageSlug => $sTopLevelPageSlug ) {

            remove_submenu_page( $sTopLevelPageSlug, $sSubmenuPageSlug );

            // This method is called directly without a hook if the admin_menu hook is already passed.
            // In that case, when registering multiple taxonomies, this method can be called multiple times.
            // For that, the removed item should be cleared to avoid multiple menu removals.
            unset( $this->oProp->aTaxonomyRemoveSubmenuPages[ $sSubmenuPageSlug ] );

        }

    }

}
