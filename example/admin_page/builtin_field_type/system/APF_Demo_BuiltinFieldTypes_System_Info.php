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
class APF_Demo_BuiltinFieldTypes_System_Info {

    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_builtin_field_types';

    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'system';

    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'info';

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
                'title'         => __( 'System Custom Field Type', 'admin-page-framework-loader' ),
                'description'   => __( 'Displays the system information.', 'admin-page-framework-loader' ),
            )
        );

        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID
            array(
                'field_id'      => 'system_information',
                'type'          => 'system',
                'title'         => __( 'System Information', 'admin-page-framework-loader' ),
                'data'          => array(
                    __( 'Custom Data', 'admin-page-framework-loader' )    => __( 'Here you can insert your own custom data with the data argument.', 'admin-page-framework-loader' ),
                    __( 'Current Time', 'admin-page-framework' )        => '', // Removes the Current Time Section.
                ),
                'attributes'    => array(
                    'name'  => '',
                ),
                'description'   => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'system',     
    'data'          => array(
        'Custom Data'  => 'Here you can insert your own custom data with the data argument.',
        'Current Time' => '', // Removes the Current Time Section.
    ),
    'attributes'    => array(
        'name'  => '',
    ),
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'saved_options',
                'type'          => 'system',
                'title'         => __( 'Saved Options', 'admin-page-framework-loader' ),
                'data'          => array(
                    // Removes the default data by passing an empty value below.
                    'Admin Page Framework'  => '',
                    'WordPress'             => '',
                    'PHP'                   => '',
                    'Server'                => '',
                    'PHP Error Log'         => '',
                    'MySQL'                 => '',
                    'MySQL Error Log'       => '',
                    'Browser'               => '',
                )
                + $oFactory->oProp->aOptions,
                'attributes'    => array(
                    'name'  => '',
                    'rows'   => 20,
                ),
                'description'   => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'system',     
    'data'          => array(
        // Removes the default data by passing an empty value below.
        'Admin Page Framework'  => '', 
        'WordPress'             => '', 
        'PHP'                   => '', 
        'Server'                => '',
        'PHP Error Log'         => '',
        'MySQL'                 => '', 
        'MySQL Error Log'       => '',                    
        'Browser'               => '',                         
    ) 
    + \$oFactory->oProp->aOptions,
    'attributes'    => array(
        'name'  => '',
        'rows'   => 20,
    ),        
)
EOD
                        )
                        . "</pre>",
                ),
            )
        );

    }

}
