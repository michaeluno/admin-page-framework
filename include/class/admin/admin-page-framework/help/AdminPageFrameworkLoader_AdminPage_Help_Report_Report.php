<?php
/**
 * Admin Page Framework Loader
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 */

/**
 * Adds the 'Report' form section to the 'Report' tab.
 *
 * @since       3.5.4           Moved some methods from `AdminPageFrameworkLoader_AdminPage_Help_Repor`.
 */
class AdminPageFrameworkLoader_AdminPage_Help_Report_Report extends AdminPageFrameworkLoader_AdminPage_Section_Base {

    /**
     * Adds form fields.
     *
     * @since       3.5.4
     */
    public function addFields( $oFactory, $sSectionID ) {

        $_oCurrentUser = wp_get_current_user();

        $oFactory->addSettingFields(
            'report',
            array(
                'field_id'          => 'name',
                'title'             => __( 'Your Name', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'default'           => $_oCurrentUser->user_lastname || $_oCurrentUser->user_firstname
                    ? $_oCurrentUser->user_lastname . ' ' .  $_oCurrentUser->user_lastname
                    : '',
                'attributes'        => array(
                    'required'      => 'required',
                    'placeholder'   => __( 'Type your name.', 'admin-page-framewrok-demo' ),
                ),
            ),
            array(
                'field_id'          => 'from',
                'title'             => __( 'Your Email Address', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'default'           => $_oCurrentUser->user_email,
                'attributes'        => array(
                    'required'      => 'required',
                    'placeholder'   =>  __( 'Type your email that the developer replies backt to.', 'admin-page-framework-loader' )
                ),
            ),
            array(
                'field_id'          => 'expected_result',
                'title'             => __( 'Expected Behavior', 'admin-page-framework-loader' ),
                'type'              => 'textarea',
                'description'       => __( 'Tell how the framework should work.', 'admin-page-framework-loader' ),
                'attributes'        => array(
                    'required'  => 'required',
                ),
            ),
            array(
                'field_id'          => 'actual_result',
                'title'             => __( 'Actual Behavior', 'admin-page-framework-loader' ),
                'type'              => 'textarea',
                'description'      => __( 'Describe the behavior of the framework.', 'admin-page-framework-loader' ),
                'attributes'        => array(
                    'required'  => 'required',
                ),
            ),
            array(
                'field_id'          => 'attachments',
                'title'             => __( 'Screenshots', 'admin-page-framework-loader' ),
                'type'              => 'image',
                'repeatable'        => true,
                'attributes'        => array(
                    'size'  => 40,
                    'preview' => array(
                        'style' => 'max-width: 200px;'
                    ),
                ),
            ),
            array(
                'field_id'      => 'system_information',
                'type'          => 'system',
                'title'         => __( 'System Information', 'admin-page-framework-loader' ),
                'data'          => array(
                    __( 'Custom Data', 'admin-page-framework-loader' )  => __( 'This is custom data inserted with the data argument.', 'admin-page-framework-loader' ),
                    __( 'Current Time', 'admin-page-framework' )        => '', // Removes the Current Time Section.
                ),
                'attributes'    => array(
                    'rows'          =>  10,
                ),
            ),
            array(
                'field_id'      => 'saved_options',
                'type'          => 'system',
                'title'         => __( 'Saved Options', 'admin-page-framework-loader' ),
                'data'          => array(
                    // Removes the default data by passing an empty value below.
                    'WordPress'             => '',
                    'Admin Page Framework'  => '',
                    'Server'                => '',
                    'PHP'                   => '',
                    'PHP Error Log'         => '',
                    'MySQL'                 => '',
                    'MySQL Error Log'       => '',
                    'Browser'               => '',
                )
                + $this->oFactory->oProp->aOptions, // the stored options of the main demo class
                'attributes'    => array(
                    'rows'          =>  10,
                ),
                'hidden'        => true,
            ),
            array(
                'field_id'          => 'allow_sending_system_information',
                'title'             => __( 'Confirmation', 'admin-page-framework-loader' )
                    . ' (' . __( 'required', 'admin-page-framework-loader' ) . ')',
                'type'              => 'checkbox',
                'label'             => __( 'I understand that the system information including a PHP version and WordPress version etc. will be sent along with the messages to help developer trouble-shoot the problem.', 'admin-page-framework-loader' ),
                'attributes'        => array(
                    'required'  => 'required',
                ),
            ),
            array(
                'field_id'          => 'send',
                'type'              => 'submit',
                'label_min_width'   => 0,
                'value'             => $oFactory->oUtil->getAOrB(
                    'email' === $oFactory->oUtil->getElement( $_GET, 'confirmation' ),
                    __( 'Send', 'adimn-page-framework-demo' ),
                    __( 'Preview', 'adimn-page-framework-demo' )
                ),
                'attributes'        => array(
                    'field' => array(
                        'style' => 'float:right; clear:none; display: inline;',
                    ),
                    'class' => $oFactory->oUtil->getAOrB(
                        'email' === $oFactory->oUtil->getElement( $_GET, 'confirmation' ),
                        null,
                        'button-secondary'
                    ),
                ),
                'email'             => array(
                    // Each argument can accept a string or an array representing the dimensional array key.
                    // For example, if there is a field for the email title, and its section id is 'my_section'  and  the field id is 'my_field', pass an array, array( 'my_section', 'my_field' )
                    'to'            => 'admin-page-framework@michaeluno.jp',
                    'subject'       => 'Reporting Issue',
                    'message'       => array( 'report' ), // the section name enclosed in an array. If it is a field, set it to the second element like array( 'seciton id', 'field id' ).
                    'headers'       => '',
                    'attachments'   => '', // the file path(s)
                    'name'          => '', // The email sender name. If the 'name' argument is empty, the field named 'name' in this section will be applied
                    'from'          => '', // The sender email address. If the 'from' argument is empty, the field named 'from' in this section will be applied.
                    // 'is_html'       => true,
                ),
            ),
            array()
        );

    }

    /**
     * Validates the submitted form data.
     *
     * @since       3.5.4
     */
    public function validate( $aInput, $aOldInput, $oFactory, $aSubmit ) {

       // Local variables
        $_bIsValid = true;
        $_aErrors  = array();

        if ( ! $aInput[ 'allow_sending_system_information' ] ) {
            $_bIsValid = false;
            $_aErrors[ 'allow_sending_system_information' ] = __( 'We need necessary information to help you.', 'admin-page-framework-loader' );
        }

        if ( ! $_bIsValid ) {

            $oFactory->setFieldErrors( $_aErrors );
            $oFactory->setSettingNotice( __( 'Please help us to help you.', 'admin-page-framework-loader' ) );
            return $aOldInput;

        }

        // Otherwise, process the data.
        return $aInput;

    }


}
