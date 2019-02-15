<?php
/**
 * Admin Page Framework Loader
 *
 * Demonstrates the usage of Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 *
 */

/**
 * Adds a tab of the set page to the loader plugin.
 *
 * @since       3.5.0
 */
class APF_Demo_ManageOptions_Reset {

    private $_oFactory;
    private $_sClassName;
    private $_sPageSlug;

    private $_sTabSlug   = 'reset';
    private $_sSectionID = 'reset';

    /**
     * Sets uo properties, hooks, and in-page tabs.
     */
    public function __construct( $oFactory, $sPageSlug ) {

        $this->_oFactory     = $oFactory;
        $this->_sClassName   = $oFactory->oProp->sClassName;
        $this->_sPageSlug    = $sPageSlug;

        $this->_oFactory->addInPageTabs(
            $this->_sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->_sTabSlug,
                'title'         => __( 'Reset', 'admin-page-framework-loader' ),
            )
        );

        // load + page slug + tab slug
        add_action(
            'load_' . $this->_sPageSlug . '_' . $this->_sTabSlug,
            array( $this, 'replyToLoadTab' )
        );

        add_filter(
            'validation_' . $this->_sClassName . '_' . $this->_sSectionID,
            array( $this, 'replyToValidateFields' ),
            10,
            4
        );


    }

    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oFactory ) {

        $oFactory->addSettingSections(
            $this->_sPageSlug,
            array(
                'section_id'    => $this->_sSectionID,
                'tab_slug'      => $this->_sTabSlug,
                'title'         => __( 'Reset Button', 'admin-page-framework-loader' ),
                'order'         => 10,
            )
        );

        $oFactory->addSettingFields(
            $this->_sSectionID,
            // Reset options with a check box
            array(
                'field_id'          => 'reset_confirmation_check',
                'title'             => __( 'Confirm Reset', 'admin-page-framework-loader' ),
                'type'              => 'checkbox',
                'label'             => __( 'I understand the options will be erased by pressing the reset button.', 'admin-page-framework-loader' ),
                'save'              => false,
                'value'             => false,
            ),
            array(
                'field_id'          => 'submit_skip_confirmation',
                'type'              => 'submit',
                'label'             => __( 'Reset', 'admin-page-framework-loader' ),
                'reset'             => true,
                'skip_confirmation' => true,    // 3.7.6+
                'description'       => array(
                    __( 'With the <code>skip_confirmation</code> argument, you can skip the confirmation.', 'admin-page-framework-loader' ),
                    __( 'And use a checkbox to let the user perform the action by pressing the button only once.', 'admin-page-framework-loader' ),
                ),
                'redirect_url'  => add_query_arg(
                    array(
                        'page'  => $this->_sPageSlug,
                        'tab'   => 'saved_data',    // the hidden tab
                        'settings-updated' => true,
                    )
                ),
            )
        );

    }

    /**
     * @return      array
     * @callback    validtion_{instantiated class name}_{section id}
     */
    public function replyToValidateFields( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {

        $_bIsValid = true;
        $_aErrors  = array();

        // If the pressed button is not the one with the check box, do not set a field error.
        if ( 'submit_skip_confirmation' !== $aSubmitInfo[ 'field_id' ] ) {
            return $aInputs;
        }

        if ( ! $aInputs[ 'reset_confirmation_check' ] ) {

            $_bIsValid = false;
            $_aErrors[ $this->_sSectionID ][ 'reset_confirmation_check' ] = __( 'Please check the check box to confirm you want to reset the settings.', 'admin-page-framework-loader' );

        }

        if ( ! $_bIsValid ) {

            $oFactory->setFieldErrors( $_aErrors );
            $oFactory->setSettingNotice( __( 'Please help us to help you.', 'admin-page-framework-loader' ) );
            return $aOldInputs;

        }

        // Delete the basic usage example framework options as well.
        delete_option( 'APF_BasicUsage' );

        return $aInputs;

    }


}
