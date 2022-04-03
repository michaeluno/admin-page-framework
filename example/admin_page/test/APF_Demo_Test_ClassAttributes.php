<?php
/**
 * Admin Page Framework Loader
 *
 * Demonstrates the usage of Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed GPLv2
 *
 */

/**
 * Adds a tab of the set page to the loader plugin.
 *
 * @since       3.8.29
 */
class APF_Demo_Test_ClassAttributes {

    public $oFactory;

    public $sClassName;

    public $sPageSlug;

    public $sTabSlug = 'class_attributes';

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
                'title'         => 'Class Attributes',
            )
        );

        // load + page slug + tab slug
         add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToLoadTab' ) );
         add_action( 'do_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToDoTab' ) );

    }

    /**
     * Triggered when the tab starts loading.
     *
     * @callback        add_action      load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oAdminPage ) {

        // Section
        $oAdminPage->addSettingSections(
            $this->sPageSlug, // the target page slug
            array(
                'section_id'  => $this->sSectionID,
                'tab_slug'    => $this->sTabSlug,
                'title'       => __( 'Class Attributes', 'admin-page-framework-loader' ),
                'description' => array(
                    __( 'Tests class attributes.', 'admin-page-framework-loader' ),
                ),
            )
        );

        // Fields
        $oAdminPage->addSettingFields(
            $this->sSectionID, // the target section id
            array(
                'field_id' => '_class_argument',
                'type'     => 'text',
                'title'    => 'class argument',
                    'class'    => array(
                        'fieldrow'  =>  array( 'test-fieldrow1', 'test-fieldrow2' ),
                        'fieldset'  =>  'test-fieldset1 test-fieldset2',
                        'fields'    =>  'test-fields',
                        'field'     =>  'test-field',
                        'test-input'
                    ),
            ),
            array(
                'field_id' => '_attributes_argument',
                'type'     => 'text',
                'title'    => 'attributes argument',
                    'attributes' => array(
                        'fieldrow' => array(
                            'class' => array( 'test-attr-fieldrow1', 'test-attr-fieldrow2' )    // accepts an array for multiple items
                        ),
                        'fieldset' => array(
                            'class' => 'test-attr-fieldset1 test-attr-fieldset2'
                        ),
                        'fields' => array(
                            'class' => 'test-attr-fields1 test-attr-fields2s'
                        ),
                        'field' => array(
                            'class' => 'test-attr-field'
                        ),
                        'class'    => 'test-attr-input',
                    )
            )
        );


    }

    /**
     * @param AdminPageFramework $oAdminPage
     */
    public function replyToDoTab( $oAdminPage ) {}

}