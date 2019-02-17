<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed MIT
 *
 */

/**
 * Handles routing of function calls and instantiation of associated classes.
 *
 * @abstract
 * @since           3.3.0
 * @package         AdminPageFramework/Factory/MetaBox
 * @internal
 */
abstract class AdminPageFramework_MetaBox_Router extends AdminPageFramework_Factory {

    /**
     * Constructs the class object instance of AdminPageFramework_MetaBox.
     *
     * Mainly sets up properties and hooks.
     *
     * @see         http://codex.wordpress.org/Function_Reference/add_meta_box#Parameters
     * @since       2.0.0
     * @param       string          $sMetaBoxID             The meta box ID. [3.3.0+] If an empty value is passed, the ID will be automatically generated and the lower-cased class name will be used.
     * @param       string          $sTitle                 The meta box title.
     * @param       string|array    $asPostTypeOrScreenID   (optional) The post type(s) or screen ID that the meta box is associated with.
     * @param       string          $sContext               (optional) The part of the page where the edit screen section should be shown ('normal', 'advanced', or 'side') Default: `normal`.
     * @param       string          $sPriority              (optional) The priority within the context where the boxes should show ('high', 'core', 'default' or 'low') Default: `default`.
     * @param       string          $sCapability            (optional) The <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> to the meta box. Default: `edit_posts`.
     * @param       string          $sTextDomain            (optional) The text domain applied to the displayed text messages. Default: `admin-page-framework`.
     * @return      void
     */
    public function __construct( $sMetaBoxID, $sTitle, $asPostTypeOrScreenID=array( 'post' ), $sContext='normal', $sPriority='default', $sCapability='edit_posts', $sTextDomain='admin-page-framework' ) {

        parent::__construct( $this->oProp );

        $this->oProp->sMetaBoxID    = $sMetaBoxID
            ? $this->oUtil->sanitizeSlug( $sMetaBoxID )
            : strtolower( $this->oProp->sClassName );
        $this->oProp->sTitle        = $sTitle;
        $this->oProp->sContext      = $sContext;    // 'normal', 'advanced', or 'side'
        $this->oProp->sPriority     = $sPriority;   // 'high', 'core', 'default' or 'low'

        if ( ! $this->oProp->bIsAdmin ) {
            return;
        }

        // 3.8.14
        add_action( 'set_up_' . $this->oProp->sClassName, array( $this, '_replyToCallLoadMethods' ), 100 );

        $this->oUtil->registerAction(
            $this->oProp->bIsAdminAjax
                ? 'wp_loaded'
                : 'current_screen',
            array( $this, '_replyToDetermineToLoad' )
        );

    }

    /**
     * Calls the load method and callbacks.
     * @since       3.8.14
     * @return      void
     * @internal
     */
    public function _replyToCallLoadMethods() {
        $this->_load();
    }

    /**
     * Determines whether the meta box belongs to the loading page.
     *
     * @since       3.0.3
     * @since       3.2.0       Changed the scope to `public` from `protected` as the head tag object will access it.
     * @since       3.3.0       Moved from `AdminPageFramework_MetaBox`.
     * @since       3.8.14      Changed the visibility scope to `protected` from `public` as there is the `isInThePage()` public method.
     * @internal
     */
    protected function _isInThePage() {

        // 3.8.14 The ajax check was added. 3.8.19 The method was added.
        if ( $this->_isValidAjaxReferrer() ) {
            return true;
        }

        if ( ! in_array( $this->oProp->sPageNow, array( 'post.php', 'post-new.php' ) ) ) {
            return false;
        }

        if ( ! in_array( $this->oUtil->getCurrentPostType(), $this->oProp->aPostTypes ) ) {
            return false;
        }
        return true;

    }

    /**
     * Checks if the `admin-ajax.php` is called from the page that this meta box belongs to.
     * @sicne   3.8.19
     * @remark  since 3.8.14, the check for `admin-ajax.php` has been added.
     * @return  boolean
     */
    protected function _isValidAjaxReferrer() {

        if ( ! $this->oProp->bIsAdminAjax ) {
            return false;
        }
        $_aReferrer = parse_url( $this->oProp->sAjaxReferrer ) + array( 'query' => '', 'path' => '' );
        parse_str( $_aReferrer[ 'query' ], $_aQuery );

        $_sBaseName = basename( $_aReferrer[ 'path' ] );
        if ( ! in_array( $_sBaseName, array( 'post.php', 'post-new.php' ) ) ) {
            return false;
        }
        // post-new.php?post_type={...} or post.php?post={n}&action=edit
        $_iPost      = $this->oUtil->getElement( $_aQuery, array( 'post' ), 0 );
        $_sPostType  = $this->oUtil->getElement( $_aQuery, array( 'post_type' ), '' );
        $_sPostType  = $_sPostType
            ? $_sPostType
            : get_post_type( $_iPost );
        return in_array( $_sPostType, $this->oProp->aPostTypes );

    }

    /**
     * Determines whether the meta box class components should be loaded in the currently loading page.
     * @since       3.1.3
     * @internal
     */
    protected  function _isInstantiatable() {

// @deprecated      3.8.14
        // Disable in admin-ajax.php
//        if ( isset( $GLOBALS[ 'pagenow' ] ) && 'admin-ajax.php' === $GLOBALS[ 'pagenow' ] ) {
//            return false;
//        }
        return true;

    }



}
