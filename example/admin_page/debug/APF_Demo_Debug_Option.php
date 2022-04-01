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
class APF_Demo_Debug_Option {

    public $oFactory;

    public $sClassName;

    public $sPageSlug;

    public $sTabSlug = 'option';

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
                'title'         => 'Option',
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

        /**
         * @var WP_Screen
         */
        $_oWPScreen = get_current_screen();
        $oAdminPage->addSettingFields(
            'debug',
            array(
                'field_id'          => 'option_key',
                'title'             => 'Option Key',
                'type'              => 'text',
                'description'       => 'Enter an option key to inspect',
            ),
            array(
                'field_id'          => '_submit',
                'save'              => false,
                'type'              => 'submit',
                'value'             => 'Inspect'
            ),
            array(
                'field_id'          => 'option_keys',
                'show_title_column' => false,
                'type'              => 'table',
                'collapsible'       => array(
                    'active' => false,
                ),
                'caption'           => 'Available Option Keys',
            ),
            array(
                'field_id'          => 'option_value',
                'show_title_column' => false,
                'type'              => 'table',
                'collapsible'       => array(
                    'active' => true,
                ),
                'caption'           => 'Stored Option Value',
            )
        );

        add_filter( 'field_definition_' . $oAdminPage->oProp->sClassName . '_debug_option_keys', array( $this, 'replyToGetOptionKeys' ) );
        add_filter( 'field_definition_' . $oAdminPage->oProp->sClassName . '_debug_option_value', array( $this, 'replyToGetOptionValue' ) );

    }

    public function replyToGetOptionKeys( $aFieldset ) {
        /**
         * @var wpdb $_oWPDB
         */
        $_oWPDB     = $GLOBALS[ 'wpdb' ];
        $_sQuery = <<<SQLQUERY
SELECT 'autoloaded data in KiB' as name, ROUND(SUM(LENGTH(option_value))/ 1024) as value FROM `{$_oWPDB->options}` WHERE autoload='yes'
UNION
SELECT 'autoloaded data count', count(*) FROM `{$_oWPDB->options}` WHERE autoload='yes'
UNION
(SELECT option_name, length(option_value) FROM `{$_oWPDB->options}` WHERE autoload='yes' ORDER BY length(option_value) DESC LIMIT 1000)
SQLQUERY;
        $_aResults = $_oWPDB->get_results( $_sQuery, ARRAY_N );
        $aFieldset[ 'data' ] = $_aResults;

        return $aFieldset;
    }

    /**
     * @param  array $aFieldset
     * @return array
     */
    public function replyToGetOptionValue( $aFieldset ) {
        $aFieldset[ 'caption' ] = 'Option Key: <code>' . $this->oFactory->getValue( 'debug', 'option_key' ) . '</code>';
        $aFieldset[ 'data' ]    = get_option( $this->oFactory->getValue( 'debug', 'option_key' ) );
        return $aFieldset;
    }

}