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
class APF_Demo_AdvancedUsage_Argument_CustomContent {

    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_advanced_usage';

    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'argument';

    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'custom_content';

    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {

        // Section
        $oFactory->addSettingSections(
            $this->sPageSlug, // the target page slug
            array(
                'tab_slug'          => $this->sTabSlug,
                'section_id'        => $this->sSectionID,
                'title'             => __( 'Custom HTML Output', 'admin-page-framework-loader' ),
                'description'       => __( 'You can insert custom HTML output along with the field output.', 'admin-page-framework-loader' ),
            )
        );

        // Fields
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID
            array(
                'field_id'          => 'raw_html',
                'title'             => __( 'Raw HTML', 'admin-page-framework-loader' ),
                'type'              => 'my_custom_made_up_non_exisitng_field_type',
                'before_field'      => "<p>This is a custom output inserted with the <code>before_field</code> argument.</p>",
                'after_field'       => "<p>This is a custom output inserted with the <code>after_field</code> argument.</p>",
                'before_fields'     => "<p>This is a custom output inserted with the <code>before_fields</code> argument.</p>",
                'after_fields'      => "<p>This is a custom output inserted with the <code>after_fields</code> argument.</p>",
                'before_fieldset'   => "<p>This is a custom output inserted with the <code>before_fieldset</code> argument.</p>",
                'after_fieldset'    => "<p>This is a custom output inserted with the <code>after_fieldset</code> argument.</p>",
            ),
            array(
                'field_id'          => 'custom_field_content',
                'title'             => __( 'Custom Content', 'admin-page-framework-loader' ),
                'type'              => 'whatever_of_your_choosing_slug',
                'content'           => "<p>This is a custom content output inserted with the <code>content</code> argument.</p>",
                'description'       => __( 'The description part is reserved.', 'admin-page-framework-loader' ),
            )
        );

    }

}
