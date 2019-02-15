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
class APF_Demo_BuiltinFieldTypes_Selector_Checkbox {

    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_builtin_field_types';

    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'selectors';

    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'checkbox';

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
                'title'         => __( 'Check-boxes', 'admin-page-framework-loader' ),
                'tip'           => __( 'These are check boxes.', 'admin-page-framework-loader' ),
            )
        );

        // Fields
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID
            array(
                'field_id'      => 'checkbox',
                'title'         => __( 'Checkbox', 'admin-page-framework-loader' ),
                'tip'           => __( 'For a single check box item, set a string to the <code>label</code> argument.', 'admin-page-framework-loader' ),
                'type'          => 'checkbox',
                'label'         => __( 'This is a check box.', 'admin-page-framework-loader' )
                    . ' ' . __( 'A string can be passed to the <code>label</code> argument for a single item.', 'admin-page-framework-loader' ),
                'default'       => false,
                'description'   => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'      => 'checkbox',
    'label'     => 'This is a check box...',
    'default'   => false,
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'checkbox_multiple_items',
                'title'         => __( 'Multiple', 'admin-page-framework-loader' ),
                'type'          => 'checkbox',
                'label'         => array(
                    'moon'  => 'Moon',
                    'earth' => 'Earth (this option is disabled)',
                    'sun'   => 'Sun',
                    'mars'  => 'Mars',
                ),
                'default'       => array(
                    'moon'  => true,
                    'earth' => false,
                    'sun'   => true,
                    'mars'  => false,
                ),
                'attributes'    => array(
                    'earth' => array(
                        'disabled' => 'disabled',
                    ),
                ),
                'label_min_width' => '100%',
                'tip'             => __( 'With the <code>attributes</code> argument, check box items can be disabled.', 'admin-page-framework-loader' ),
                'description'   => array(
                    __( 'For for multiple checkbox items, set an array to the <code>label</code> argument.', 'admin-page-framework-loader' ),
                    __( 'It is possible to disable checkbox items on an individual basis.', 'admin-page-framework-loader' ),
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
    'type'          => 'checkbox',
    'label'         => array( 
        'moon'  => __( 'Moon', 'admin-page-framework-loader' ),
        'earth' => __( 'Earth', 'admin-page-framework-loader' ) . ' (' . __( 'this option is disabled.', 'admin-page-framework-loader' ) . ')',
        'sun'   => __( 'Sun', 'admin-page-framework-loader' ),
        'mars'  => __( 'Mars', 'admin-page-framework-loader' ),
    ),
    'default'       => array( 
        'moon'  => true, 
        'earth' => false, 
        'sun'   => true, 
        'mars'  => false,
    ),
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'              => 'checkbox_multiple_fields',
                'title'                 => __( 'Multiple Sets', 'admin-page-framework-loader' ),
                'type'                  => 'checkbox',
                'select_all_button'     => true, // 3.3.0+   to change the label, set the label here
                'select_none_button'    => true, // 3.3.0+   to change the label, set the label here
                'label'                 => array(
                    'a' => 'A',
                    'b' => 'B',
                    'c' => 'C'
                ),
                'default'               => array(
                    'a' => false,
                    'b' => true,
                    'c' => false
                ),
                'delimiter'             => '<hr />',
                'attributes'            => array(
                    'field' => array(
                        'style' => 'width: 100%;',
                    ),
                ),
                array(
                    'label' => array(
                        'd' => 'D',
                        'e' => 'E',
                        'f' => 'F'
                    ),
                    'default' => array(
                        'd' => true,
                        'e' => false,
                        'f' => false
                    ),
                ),
                array(
                    'label' => array(
                        'g' => 'G',
                        'h' => 'H',
                        'i' => 'I'
                    ),
                    'default' => array(
                        'g' => false,
                        'h' => false,
                        'i' => true
                    ),
                ),
                'tip'           => __( 'To create multiple fields for one field ID, you can use the numeric keys in the field definition array.', 'admin-page-framework-loader' ),
                'description'   => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
    'type'                  => 'checkbox',
    'label'                 => array( 
        'a' => 'A',
        'b' => 'B',
        'c' => 'C' 
    ),
    'default'               => array( 
        'a' => false,
        'b' => true,
        'c' => false 
    ),
    array(
        'label' => array(
            'd' => 'D',
            'e' => 'E',
            'f' => 'F' 
        ),
        'default' => array(
            'd' => true,
            'e' => false,
            'f' => false 
        ),
    ),
    array(
        'label' => array(
            'g' => 'G',
            'h' => 'H',
            'i' => 'I'
        ),
        'default' => array(
            'g' => false,
            'h' => false,
            'i' => true 
        ),
    ),
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'           => 'checkbox_repeatable_fields',
                'title'              => __( 'Repeatable', 'admin-page-framework-loader' ),
                'type'               => 'checkbox',
                'label'              => array( 'x', 'y', 'z' ),
                'repeatable'         => true,
                'select_all_button'  => __( 'Check All', 'admin-page-framework-loader' ), // 3.3.0+   to change the label, set the label here
                'select_none_button' => __( 'Uncheck All', 'admin=page-framework-demo' ), // 3.3.0+   to change the label, set the label here
                'description'        => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
    'type'               => 'checkbox',
    'label'              => array( 'x', 'y', 'z' ),
    'repeatable'         => true,
    'select_all_button'  => 'Check All',
    'select_none_button' => 'Uncheck All',
EOD
                        )
                        . "</pre>",
                ),
            ),
            array( // sortable check boxes
                'field_id'      => 'checkbox_sortable_fields',
                'title'         => __( 'Sortable', 'admin-page-framework-loader' ),
                'type'          => 'checkbox',
                'label'         => array( 'x', 'y', 'z' ),
                'sortable'      => true,
                array(), // the second item
                array(), // the third item
                array(), // the fourth item
                'description'        => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
    'type'          => 'checkbox',
    'label'         => array( 'x', 'y', 'z' ),
    'sortable'      => true,
    array(), // the second item
    array(), // the third item
    array(), // the fourth item
EOD
                        )
                        . "</pre>",
                ),
            )
        );

    }

}
