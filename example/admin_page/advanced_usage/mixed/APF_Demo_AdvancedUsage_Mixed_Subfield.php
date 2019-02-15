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
class APF_Demo_AdvancedUsage_Mixed_Subfield {

    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_advanced_usage';

    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'mixed_types';

    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'subfields';

    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {

        // Section
        $oFactory->addSettingSections(
            $this->sPageSlug, // the target page slug
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Mixed Field Types with Sub-fields', 'admin-page-framework-loader' ),
                'description'   => __( 'As of v3, it is possible to mix field types in one field on a per-ID basis.', 'admin-page-framework-loader' ),
            )
        );

        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID
            array(
                'field_id'      => 'mixed_fields',
                'title'         => __( 'Text and Hidden', 'admin-page-framework-loader' ),
                'type'          => 'text',
                'default'       => 'abc',
                'attributes'    => array(
                    // 'field' => array(
                        // 'style' => 'display: inline; clear:none;' // since the rich editor does not accept the cols attribute, set the width by inline-style.
                    // ),
                ),
                array(
                    'type'          => 'textarea',
                    'default'       => __( 'A hidden field is embedded. This is useful when you need to embed extra information to be sent with the visible elements.', 'admin-page-framework-loader' ),
                ),
                array(
                    'type'          => 'hidden',
                    'value'         => 'xyz',
                ),
            )
        );

    }

}
