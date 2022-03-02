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
 * Adds a tab of the test page to the loader plugin.
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
         add_action( 'do_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToDoTab' ) );

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
            )
        );

        add_filter( 'validation_' . $oAdminPage->oProp->sClassName . '_debug', array( $this, 'validate' ), 10, 4 );

    }

    /**
     * @param AdminPageFramework $oAdminPage
     */
    public function replyToDoTab( $oAdminPage ) {
        $_iPostID = absint( $oAdminPage->getValue( 'debug', 'post_id' ) );
        if ( empty( $_iPostID ) ) {
            return;
        }
        /**
         * @var WP_POST
         */
        $_oPost     = get_post( $_iPostID );
        if ( empty( $_oPost ) ) {
            echo "<p>Post not found with the post ID: {$_iPostID}.</p>";
            return;
        }
        $_aMetaKeys = $oAdminPage->oUtil->getAsArray( get_post_custom_keys( $_iPostID ) );
        $_aMeta     = array();
        foreach( $_aMetaKeys as $_sMetaKey ) {
            $_aMeta[ $_sMetaKey ] = get_post_meta( $_iPostID, $_sMetaKey, true );
        }

        $_aTableArguments = array(
            'table' => array(
                'class' => 'widefat striped fixed demo-options',
            ),
            'th'    => array(
                array( 'style' => 'width:10%;', ),  // first th
            ),
            'td'    => array(
                array( 'style' => 'width:10%;', ),  // first td
            )
        );
        echo '<h4>Post Table Row</h4>';
        echo $oAdminPage->oUtil->getTableOfArray(
            get_object_vars( $_oPost ),
            $_aTableArguments,
            array( 'Key' => 'Value' )
        );
        echo '<h4>Post Meta</h4>';
        echo $oAdminPage->oUtil->getTableOfArray(
            $_aMeta,
            $_aTableArguments,
            array( 'Key' => 'Value' )
        );

    }

    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
        $oAdminPage->setSettingNotice( '' );    // disable the setting notice
        return $aInputs;
    }

}