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
class APF_Demo_AdvancedUsage_Verification_Field {


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
    public $sSectionID  = 'field_verification';

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
                'title'         => __( 'Verify Submitted Data', 'admin-page-framework-loader' ),
                'description'   => __( 'Show error messages when the user submits improper option value.', 'admin-page-framework-loader' ),
            )
        );

        /*
         * Text area fields.
         */
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section id
            array(
                'field_id'      => 'numeric',
                'title'         => __( 'Verify Text Input', 'admin-page-framework-loader' ),
                'type'          => 'text',
                'description'   => __( 'Try setting a non numeric value here.', 'admin-page-framework-loader' ),
            ),
            array(
                'field_id'      => 'other_text_field',
                'title'         => __( 'Other Field', 'admin-page-framework-loader' ),
                'type'          => 'text',
                'description'   => __( 'This field will not be validated.', 'admin-page-framework-loader' ),
            ),
            array(
                'field_id'      => 'verify_text_field_submit', // this submit field ID can be used in a validation callback method
                'type'          => 'submit',
                'save'          => false,
                'value'         => __( 'Verify', 'admin-page-framework-loader' ),
            )
        );

        add_filter(
            'validation_' . $oFactory->oProp->sClassName . '_' . 'field_verification' . '_' . 'numeric',
            array( $this, 'replyToValidateField' ),
            10, // priority
            4   // number of parameters
        );

    }

    /**
     * Validates the 'numeric' field in the 'field_verification' section of the 'APF_Demo' class.
     *
     * @callback        filter      validation_{instantiated class name}_{section id}_{field id}
     */
    public function replyToValidateField( $sNewInput, $sOldInput, $oAdmin ) {

        /* 1. Set a flag. */
        $_bVerified = true;

        /* 2. Prepare an error array.
             We store values that have an error in an array and pass it to the setFieldErrors() method.
            It internally stores the error array in a temporary area of the database called transient.
            The used name of the transient is a md5 hash of 'instantiated class name' + '_' + 'page slug'.
            The library class will search for this transient when it renders the form fields
            and if it is found, it will display the error message set in the field array.
        */
        $_aErrors = array();

        /* 3. Check if the submitted value meets your criteria. As an example, here a numeric value is expected. */
        if ( ! is_numeric( $sNewInput ) ) {

            // $variable[ 'sectioni_id' ]['field_id']
            $_aErrors[ $this->sSectionID ][ 'numeric' ] = __( 'The value must be numeric:', 'admin-page-framework-loader' ) . ' ' . $sNewInput;
            $_bVerified = false;

        }

        /* 4. An invalid value is found. */
        if ( ! $_bVerified ) {

            /* 4-1. Set the error array for the input fields. */
            $oAdmin->setFieldErrors( $_aErrors );
            $oAdmin->setSettingNotice( __( 'There was an error in a form field.', 'admin-page-framework-loader' ) );

            return $sOldInput;

        }

        return $sNewInput;

    }

}
