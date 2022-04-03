<?php
/**
 * Admin Page Framework - Demo
 *
 * Demonstrates the usage of Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed GPLv2
 *
 */

/**
 * Adds a section in a tab.
 *
 * @package     AdminPageFramework/Example
 */
class APF_Demo_BuiltinFieldTypes_Table_Table {

    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_builtin_field_types';

    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'table';

    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'tables';

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
                'title'             => __( 'Custom Data Tables', 'admin-page-framework-loader' ),
                'description'       => __( 'With the <code>table</code> field type, custom data tables can be generated.', 'admin-page-framework-loader' ),
            )
        );

        // Fields
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID
            array(
                'field_id'          => 'basic',
                'type'              => 'table',
                'title'             => __( 'Basic', 'admin-page-framework-loader' ),
                'data'              => array(
                    array(
                        'Hat', 'ZA2001'
                    ),
                    array(
                        'Jacket', 'ZB2002'
                    ),
                    array(
                        'Shoe', 'ZB2003'
                    ),
                ),
                'description'       => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'              => 'table',
    'data'              => array(
        array(
            'Hat', 'ZA2001'
        ),
        array(
            'Jacket', 'ZB2002'
        ),
        array(
            'Shoe', 'ZB2003'
        ),
    ),
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'          => 'header_and_footer',
                'type'              => 'table',
                'title'             => __( 'Header and Footer', 'admin-page-framework-loader' ),
                'header'            => array(
                    'Type', 'Code', 'Price'
                ),
                'footer'            => array(
                    'Total', '', 180
                ),
                'data'              => array(
                    array(
                        'Hat', 'ZA2001', 20,
                    ),
                    array(
                        'Jacket', 'ZB2002', 50,
                    ),
                    array(
                        'Shoe', 'ZB2003', 40,
                    ),
                    array(
                        'Watch', 'ZB2004', 70,
                    ),
                ),
                'description'       => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'              => 'table',
    'header'            => array(
        'Type', 'Code', 'Price'
    ),
    'footer'            => array(
        'Total', '', 180
    ),
    'data'              => array(
        array(
            'Hat', 'ZA2001', 20,
        ),
        array(
            'Jacket', 'ZB2002', 50,
        ),
        array(
            'Shoe', 'ZB2003', 40,
        ),
        array(
            'Watch', 'ZB2004', 70,
        ),
    ),
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'          => 'attributes',
                'type'              => 'table',
                'title'             => __( 'Attributes', 'admin-page-framework-loader' ),
                'header'            => array(
                    'Type', 'Code', 'Price'
                ),
                'footer'            => array(
                    'Total', '', 110
                ),
                'data'              => array(
                    array(
                        'Hat', 'ZA2001', 20,
                    ),
                    array(
                        'Jacket', 'ZB2002', 50,
                    ),
                    array(
                        'Shoe', 'ZB2003', 40,
                    ),
                ),
                'attributes'        => array(
                    'th' => array(
                        // zero-based second column
                        2 => array(
                            'style' => 'text-align: right; width: 10%;'
                        ),
                    ),
                    'td' => array(
                        // zero-based second column
                        2 => array(
                            'style' => 'text-align: right; width: 10%;'
                        ),
                    ),
                ),
                'description'       => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'              => 'table',
    'header'            => array(
        'Type', 'Code', 'Price'
    ),
    'footer'            => array(
        'Total', '', 110
    ),
    'data'              => array(
        array(
            'Hat', 'ZA2001', 20,
        ),
        array(
            'Jacket', 'ZB2002', 50,
        ),
        array(
            'Shoe', 'ZB2003', 40,
        ),
    ),
    'attributes'        => array(
        'th' => array(
            // zero-based second column
            2 => array(     
                'style' => 'text-align: right; width: 10%;'
            ),
        ),
        'td' => array(
            // zero-based second column
            2 => array(
                'style' => 'text-align: right; width: 10%;'
            ),
        ),
    ),
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'          => 'associative',
                'type'              => 'table',
                'title'             => __( 'Associative', 'admin-page-framework-loader' ),
                'data'              => array(
                    "foo" => "bar",
                    42    => 24,
                    "multi" => array(
                         "dimensional" => array(
                             "element" => "foo"
                         )
                    )
                ),
                'description'       => array(
                    __( 'When an associative array is set, a table of key-value pairs will be created.', 'admin-page-framework-loader' ),
                    __( 'This is more suitable to inspect data.', 'admin-page-framework-loader' ),
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'              => 'table',
    'data'              => array(
        "foo" => "bar",
        42    => 24,
        "multi" => array(
             "dimensional" => array(
                 "element" => "foo"
             )
        )
    )
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'          => 'wide_table',
                'type'              => 'table',
                'show_title_column' => false,
                'data'              => array(
                    'first_release' => '1995',
                    'latest_release' => '7.3.11',
                    'designed_by' => 'Rasmus Lerdorf',
                    'description' => array(
                        'extension' => '.php',
                        'typing_discipline' => 'Dynamic, weak',
                        'license' => 'PHP License (most of Zend engine
                             under Zend Engine License)'
                    )
                ),
                'caption'           => __( 'Caption', 'admin-page-framework-loader' ),
                'description'       => array(
                    __( 'This uses the <code>caption</code> argument to set the table caption.', 'admin-page-framework-loader' ),
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'              => 'table',
    'show_title_column' => false,
    'data'              => array(
        'first_release' => '1995',
        'latest_release' => '7.3.11',
        'designed_by' => 'Rasmus Lerdorf',
        'description' => array(
            'extension' => '.php',
            'typing_discipline' => 'Dynamic, weak',
            'license' => 'PHP License (most of Zend engine
                 under Zend Engine License)'
        )
    )
    'caption'           => 'Caption',
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'          => 'table_header_and_footer',
                'type'              => 'table',
                'data'              => array(
                    'first_release' => '1991', 
                    'latest_release' => '3.8.0', 
                    'designed_by' => 'Guido van Rossum',
                    'description' => array(
                        'extension' => '.py', 
                        'typing_discipline' => 'Duck, dynamic, gradual',
                        'license' => 'Python Software Foundation License'
                    )
                ),
                'title'             => __( 'Footer and Header for Associative', 'admin-page-framework-loader' ),
                // for associative arrays, set key-value pairs to the header and footer
                'header'            => array( 'Custom Header Key' => 'Custom Header Value' ),
                'footer'            => array( 'Custom Footer Key' => 'Custom Footer Value' ),
                'description'       => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'              => 'table',
    // for associative arrays, set key-value pairs to the header and footer
    'header'            => array( 'Custom Header Key' => 'Custom Header Value' ),
    'footer'            => array( 'Custom Footer Key' => 'Custom Footer Value' ),
    'data'              => array(
        'first_release' => '1991', 
        'latest_release' => '3.8.0', 
        'designed_by' => 'Guido van Rossum',
        'description' => array(
            'extension' => '.py', 
            'typing_discipline' => 'Duck, dynamic, gradual',
            'license' => 'Python Software Foundation License'
        )
    ),    
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'          => 'collapsible',
                'title'             => __( 'Collapsible', 'admin-page-framework-loader' ),
                'type'              => 'table',
                'caption'           => 'WordPress',
                'collapsible'       => true,
                'data'              => $oFactory->oUtil->getSiteData( array( 'wp-core', 'fields' ) ),
                'description'       => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'              => 'table',
    'data'              => \$data_array,    // set your data of an array
    'caption'           => 'WordPress',
    'collapsible'       => true,
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'          => 'multiple_tables',
                'type'              => 'table',
                'data'              => $oFactory->oUtil->getSiteData( array( 'wp-plugins-active', 'fields' ) ),
                'title'             => __( 'Multiple', 'admin-page-framework-loader' ),
                'caption'           => __( 'Active Plugins', 'admin-page-framework-loader' ),
                'collapsible'       => array(
                    'active'    => true,       // to open the content by default, set it `true`
                    'animate'   => 400,         // to speed up the animation, decrease the value
                ),
                'description'       => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'              => 'table',
    'data'              => \$array_active_plugins,    // set your data of an array
    'caption'           => 'Active Plugins',
    'collapsible'       => array(   // or just `true` will work
        'active'    => true,       // to open the content by default, set it `true`
        'animate'   => 400,         // to speed up the animation, decrease the value
    ),
    array(
        'caption'           => __( 'Inactive Plugins', 'admin-page-framework-loader' ),
        'data'              => \$array_inactive_plugins,    // set your data of an array
        'collapsible'       => array(
            'active'    => false,  
        ),    
    ),
    // ... continues
)
EOD
                        )
                        . "</pre>",
                ),
                array(
                    'data'              => $oFactory->oUtil->getSiteData( array( 'wp-plugins-inactive', 'fields' ) ),
                    'caption'           => __( 'Inactive Plugins', 'admin-page-framework-loader' ),
                    'collapsible'       => true,
                ),
                array(
                    'data'              => $oFactory->oUtil->getSiteData( array( 'wp-active-theme', 'fields' ) ),
                    'caption'           => __( 'Active Theme', 'admin-page-framework-loader' ),
                    'collapsible'       => true,
                ),
                array(
                    'data'              => $oFactory->oUtil->getSiteData( array( 'wp-themes-inactive', 'fields' ) ),
                    'caption'           => __( 'Inactive Themes', 'admin-page-framework-loader' ),
                    'collapsible'       => true,
                ),
            ),
            array(
                'field_id'          => 'simple_faq',
                'title'             => __( 'FAQ', 'admin-page-framework-loader' ),
                'type'              => 'table',
                'collapsible'       => true,
                'caption'           => __( 'What day is it today?', 'admin-page-framework-loader' ),
                'data'              => sprintf( __( 'Today is %1$s.', 'admin-page-framework-loader' ), $oFactory->oUtil->getSiteReadableDate( time(), 'l', true ) ),
                array(
                    'caption' => __( 'What time is it now?', 'admin-page-framework-loader' ),
                    'data'    => sprintf( __( 'Now is %1$s.', 'admin-page-framework-loader' ), $oFactory->oUtil->getSiteReadableDate( time(), null, true ) )
                ),
                'description'       => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'              => 'table',
    'collapsible'       => true,
    'caption'           => 'What day is it today?',
    'data'              => sprintf( 'Today is %1\$s.', date( 'l' ) ),
    array(
        'caption' => 'What time is it now?',
        'data'    => sprintf( 'Now is %1\$s.', date( get_option( 'date_format' ) ) ),
    )
)
EOD
                        )
                        . "</pre>",
                ),
            )
        );

    }

}
