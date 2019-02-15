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
class APF_Demo_AdvancedUsage_Argument_DisabledRepeatableSection {

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
    public $sSectionID  = 'disabled_repeatable_section';

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
                'title'             => __( 'Disabled Repeatability', 'admin-page-framework-loader' ),
                'description'       => __( 'While showing the repeatable button, the repeatability can be turned off.', 'admin-page-framework-loader' ),
                'collapsible'       => array(
                    'toggle_all_button' => array( 'top-left', 'bottom-left' ),
                    'container'         => 'section',
                ),
                'repeatable'        => array(
                    'disabled'               => array(
                        'message'       => __( 'The ability to repeat sections is disabled.', 'admin-page-framework-loader' )
                            . ' ' . __( 'You can insert your custom message here such as \"<a>Upgrade the program</a> to enhance this feature!\"', 'admin-page-framework-loader' ),
                        'caption'       => __( 'Your Program Name', 'admin-page-framework-loader' ),
                        'box_width'     => 300,
                        'box_height'    => 100,
                    ),
                ),
            )
        );

        // Fields
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID
            array(
                'field_id'          => '_custom_content',
                'title'             => __( 'Arguments', 'admin-page-framework-loader' ),
                'content'           => "<pre>"
                . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
                    <<<EOD
array(
    'repeatable'        => array(
        'disabled'      => array(
            'message'       => __( 'The ability to repeat sections is disabled.' ),
            'caption'       => __( 'Your Program Name' ),
            'box_width'     => 300,
            'box_height'    => 100,
        ),
    ),
)
EOD
                )
                . "</pre>",
                'description'       => __( 'By showing the button, it is possible to let the users know an enhanced feature exists and encourage them to upgrade your program.', 'admin-page-framework-loader' ),
            ),
            array(
                'field_id'          => 'disabled_repeatable_field',
                'type'              => 'text',
                'title'             => __( 'Disabled Repeatable Fields', 'admin-page-framework-loader' ),
                'repeatable'        => array(
                    'disabled'               => array(
                        'message'       => __( 'The ability to repeat fields is disabled.', 'admin-page-framework-loader' )
                                           . ' ' . __( 'You can insert your custom message here such as \"<a>Upgrade the program</a> to enhance this feature!\"', 'admin-page-framework-loader' ),
                        'caption'       => __( 'Your Program Name', 'admin-page-framework-loader' ),
                        'box_width'     => 300,
                        'box_height'    => 100,
                    ),
                ),
                'description'       => "<pre>" . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
                        <<<EOD
array(
    'repeatable'        => array(
        'disabled'               => array(
            'message'       => __( 'The ability to repeat fields is disabled...' )
            'caption'       => __( 'Your Program Name' ),
            'box_width'     => 300,
            'box_height'    => 100,
        ),
    ),
)
EOD
                    )
                                       . "</pre>",
            )
        );

    }

}
