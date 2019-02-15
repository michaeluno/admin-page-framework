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
class APF_Demo_AdvancedUsage_Verification_Section {

    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_advanced_usage';

    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'verification';

    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'section_verification';

    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {

        // Section
        $oFactory->addSettingSections(
            $this->sPageSlug, // the target page slug
            array(
                'section_id'    => $this->sSectionID,       // avoid hyphen(dash), dots, and white spaces
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Text Areas', 'admin-page-framework-loader' ),
                'description'   => __( 'These are text area fields.', 'admin-page-framework-loader' ),
                'order'         => 20,
            )
        );

        /*
         * Text area fields.
         */
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section id
            array(
                'field_id'  => 'item_a',
                'title'     => __( 'Choose Item', 'admin-page-framework-loader' ),
                'type'      => 'select',
                'label'     => array(
                    0       => '--- ' . __( 'Select Item', 'admin-page-framework-loader' ) . ' ---',
                    'one'   => __( 'One', 'admin-page-framework-loader' ),
                    'two'   => __( 'Two', 'admin-page-framework-loader' ),
                    'three' => __( 'Three', 'admin-page-framework-loader' ),
                ),
            ),
            array(
                'field_id'      => 'item_b', // this submit field ID can be used in a validation callback method
                'type'          => 'text',
                'description'   => __( 'Select one above or enter text here.', 'admin-page-framework-loader' ),
            )
        );

        add_filter(
            'validation_' . $oFactory->oProp->sClassName . '_section_verification',
            array( $this, 'replyToValidateSection' ),
            10, // priority
            4   // number of parameters
        );

    }

    /**
     * Validates the 'section_verification' section items.
     *
     * @callback        filter      validation_{instantiated class name}_{section id}
     */
    public function replyToValidateSection( $aInput, $aOldInput, $oAdminPage, $aSubmitInfo ) {

        // Local variables
        $_bIsValid = true;
        $_aErrors  = array();

        if ( '0' === (string) $aInput['item_a'] && '' === trim( $aInput['item_b'] ) ) {
            $_bIsValid = false;
            $_aErrors[ 'section_verification' ] = __( 'At least one item must be set', 'admin-page-framework-loader' );
        }

        if ( ! $_bIsValid ) {

            $oAdminPage->setFieldErrors( $_aErrors );
            $oAdminPage->setSettingNotice( __( 'There was an error setting an option in a form section.', 'admin-page-framework-loader' ) );
            return $aOldInput;

        }

        // Otherwise, process the data.
        return $aInput;

    }


}
