<?php
/**
 * Admin Page Framework Loader
 *
 * Demonstrates the usage of Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed GPLv2
 *
 */

/**
 * Adds a tab.
 *
 * @since 3.9.1
 */
class APF_Demo_Debug_PostMeta {

    public $oFactory;

    public $sClassName;

    public $sPageSlug;

    public $sTabSlug = 'post_meta';

    public $sSectionID;

    public function __construct( $oFactory, $sPageSlug ) {

        $this->oFactory     = $oFactory;
        $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = $sPageSlug;
        $this->sSectionID   = $this->sTabSlug;

        $this->oFactory->addInPageTabs(
            $this->sPageSlug,
            array(
                'tab_slug'      => $this->sTabSlug,
                'title'         => 'Post',
            )
        );

        // load + page slug + tab slug
         add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToLoadTab' ) );

    }

    /**
     * Triggered when the tab starts loading.
     *
     * @callback add_action() load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oAdminPage ) {

        $oAdminPage->addSettingSection( array(
            'section_id' => 'debug',
        ) );
        $oAdminPage->addSettingFields(
            'debug',
            array(
                'field_id'          => 'post_id',
                'title'             => 'Post ID',
                'type'              => 'number',
                'description'       => 'Enter a post ID to inspect',
                'attributes'        => array(
                    'min' => 1,
                ),
                'default'           => 0,
            ),
            array(
                'field_id'          => '_submit',
                'save'              => false,
                'type'              => 'submit',
                'value'             => 'Inspect'
            ),
            array(
                'field_id'          => 'post_columns',
                'type'              => 'table',
                'show_title_column' => false,
                'collapsible'       => array(
                    'active' => true,
                ),
                'caption'           => 'Post Columns',
            ),
            array(
                'field_id'          => 'post_meta',
                'type'              => 'table',
                'show_title_column' => false,
                'collapsible'       => array(
                    'active' => true,
                ),
                'caption'           => 'Post Meta',
            )
        );

        add_filter( 'validation_' . $oAdminPage->oProp->sClassName . '_debug', array( $this, 'validate' ), 10, 4 );
        add_filter( 'field_definition_' . $oAdminPage->oProp->sClassName . '_debug_post_columns', array( $this, 'replyToGetFieldPostColumns' ) );
        add_filter( 'field_definition_' . $oAdminPage->oProp->sClassName . '_debug_post_meta', array( $this, 'replyToGetFieldPostMeta' ) );
    }

    /**
     * @param  array $aFieldset
     * @return array
     */
    public function replyToGetFieldPostColumns( $aFieldset ) {
        $_iPostID = absint( $this->oFactory->getValue( 'debug', 'post_id' ) );
        $_oPost   = get_post( $_iPostID );
        $aFieldset[ 'data' ] = ( $_oPost instanceof WP_Post )
            ? get_object_vars( get_post( $_iPostID ) )
            : "<p>Post not found with the post ID: {$_iPostID}.</p>";
        return $aFieldset;
    }

    /**
     * @param  array $aFieldset
     * @return array
     */
    public function replyToGetFieldPostMeta( $aFieldset ) {
        $_iPostID   = absint( $this->oFactory->getValue( 'debug', 'post_id' ) );
        $_aMetaKeys = $this->oFactory->oUtil->getAsArray( get_post_custom_keys( $_iPostID ) );
        $_aMeta     = array();
        foreach( $_aMetaKeys as $_sMetaKey ) {
            $_aMeta[ $_sMetaKey ] = get_post_meta( $_iPostID, $_sMetaKey, true );
        }
        $aFieldset[ 'data' ] = empty( $_aMeta )
            ? "<p>No meta data found.</p>"
            : $_aMeta;
        return $aFieldset;
    }

    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
        $oAdminPage->setSettingNotice( '' );    // disable the setting notice
        return $aInputs;
    }

}