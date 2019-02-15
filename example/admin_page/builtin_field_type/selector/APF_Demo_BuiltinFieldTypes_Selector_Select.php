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
class APF_Demo_BuiltinFieldTypes_Selector_Select {

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
    public $sSectionID  = 'select';

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
                'title'         => __( 'Drop-down Lists', 'admin-page-framework-loader' ),
                'tip'           => __( 'These are drop-down (pull-down) lists.', 'admin-page-framework-loader' ),
            )
        );

        /*
         * Selector type fields - dropdown (pulldown) list, checkbox, radio buttons, size selector
         */
        $oFactory->addSettingFields(
            $this->sSectionID,
            array(
                'field_id'      => 'select',
                'title'         => __( 'Dropdown List', 'admin-page-framework-loader' ),
                'type'          => 'select',
                'help'          => __( 'This is the <em>select</em> field type.', 'admin-page-framework-loader' ),
                'default'       => 2, // the index key of the label array below which yields 'Yellow'.
                'label'         => array(
                    0 => __( 'Red', 'admin-page-framework-loader' ),
                    1 => __( 'Blue', 'admin-page-framework-loader' ),
                    2 => __( 'Yellow', 'admin-page-framework-loader' ),
                    3 => __( 'Orange', 'admin-page-framework-loader' ),
                ),
                'tip'           => __( 'The key of the array of the <code>label</code> argument serves as the value of the option tag which will be sent to the form and saved in the database.', 'admin-page-framework-loader' )
                    . ' ' . __( 'So when you specify the default value with the <code>default</code> or <code>value</code> argument, specify the <em>KEY</em>.', 'admin-page-framework-loader' ),
                'description'   => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'select',
    'default'       => 2, // the index key of the label array
    'label'         => array( 
        0 => __( 'Red', 'admin-page-framework-loader' ),
        1 => __( 'Blue', 'admin-page-framework-loader' ),
        2 => __( 'Yellow', 'admin-page-framework-loader' ),
        3 => __( 'Orange', 'admin-page-framework-loader' ),
    ),
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'select_multiple_options',
                'title'         => __( 'Multiple', 'admin-page-framework-loader' ),
                'help'          => __( 'This is the <em>select</em> field type with multiple elements.', 'admin-page-framework' ),
                'type'          => 'select',
                'is_multiple'   => true,
                'default'       => array( 3, 4 ), // note that PHP array indices are zero-base, meaning the index count starts from 0 (not 1). 3 here means the fourth item of the array. array( 3, 4 ) will select the fourth and fifth elements.
                'label'         => array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'November', 'October', 'December' ),
                'tip'           => __( 'Use <code>is_multiple</code> argument to enable multiple selections.' ),
                'attributes'    =>  array(
                    'select'    =>  array(
                        'size'  => 10,
                    ),
                ),
                'description'   => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'select',
    'is_multiple'   => true,
    'default'       => array( 3, 4 ), // note that PHP array indices are zero-base
    'label'         => array( 'January', 'February', 'March', 
        'April', 'May', 'June', 'July', 
        'August', 'September', 'November', 
        'October', 'December' 
    ),    
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'select_multiple_groups',
                'title'         => __( 'Grouping', 'admin-page-framework-loader' ),
                'type'          => 'select',
                'default'       => 'b',
                'label'         => array(
                    'alphabets' => array(     // each key must be unique throughout this 'label' element array.
                        'a' => 'a',
                        'b' => 'b',
                        'c' => 'c',
                    ),
                    'numbers' => array(
                        0 => '0',
                        1 => '1',
                        2 => '2',
                    ),
                ),
                'attributes'    => array( // the 'attributes' element of the select field type has three keys: select, 'option', and 'optgroup'.
                    'select' => array(
                        'style' => "width: 200px;",
                    ),
                    'option' => array(
                        1 => array(
                            'disabled' => 'disabled',
                            'style' => 'background-color: #ECECEC; color: #888;',
                        ),
                    ),
                    'optgroup' => array(
                        'style' => 'background-color: #DDD',
                    )
                ),
                'tip'       => array(
                    __( 'To create grouped options, pass arrays with the key of the group label and pass the options as an array inside them.', 'admin-page-framework-loader' ),
                    __( 'To style the pulldown (dropdown) list, use the <code>attributes</code> argument. For the <code>select</code> field type, it has three major keys, <code>select</code>, <code>option</code>, and <code>optgroup</code>, representing the tag names.', 'admin-page-framework-loader' )
                ),
                'description'   => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'select',
    'default'       => 'b',
    'label'         => array(     
        'alphabets' => array(   
            'a' => 'a',     
            'b' => 'b', 
            'c' => 'c',
        ),
        'numbers' => array( 
            0 => '0',
            1 => '1',
            2 => '2', 
        ),
    ),
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'select_multiple_fields',
                'title'         => __( 'Multiple', 'admin-page-framework-loader' ),
                'tip'           => __( 'These are multiple sets of drop down list using sub fields.', 'admin-page-framework-loader' ),
                'type'          => 'select',
                'label'         => array( 'dark', 'light' ),
                'default'       => 1,
                'attributes'    => array(
                    'field'     => array(
                        'style' => 'display: inline; clear: none', // this makes the field element inline, which means next fields continues from the right end of the field, not from the new line.
                    ),
                ),
                array(
                    'label'     => array( 'river', 'mountain', 'sky', ),
                    'default'   => 2,
                ),
                array(
                    'label'         => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ),
                    'default'       => array( 3, 4 ), // 'default' => '', will select none
                    'attributes'    => array(
                        'select' => array(
                            'size' => 5,
                            'multiple' => 'multiple', // instead of 'is_multiple' =>    true, it is possible by setting it by the attribute key.
                        ),
                    )
                ),
                'description'   => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'select',
    'label'         => array( 'dark', 'light' ),
    'default'       => 1,
    'attributes'    => array(    
        'field'     => array(
            'style' => 'display: inline; clear: none',
        ),
    ),
    array(
        'label'     => array( 'river', 'mountain', 'sky', ),
        'default'   => 2,
    ),
    array(
        'label'         => array( 
            'Monday', 'Tuesday', 'Wednesday', 
            'Thursday', 'Friday', 'Saturday', 'Sunday' 
        ),
        'default'       => array( 3, 4 ),
        'attributes'    => array(
            'select' => array(
                'size' => 5,
                'multiple' => 'multiple', 
            ),
        )     
    ),
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'select_repeatable',
                'title'         => __( 'Repeatable', 'admin-page-framework-loader' ),
                'type'          => 'select',
                'repeatable'    => true,
                'tip'           => __( 'To enable repeatable fields, pass <code>true</code> to the <code>repeatable</code> argument.', 'admin-page-framework-loader' ),
                'default'       => 'y',
                'label' => array(
                    'x' => 'X',
                    'y' => 'Y',
                    'z' => 'Z',
                ),
                'description'   => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'select',
    'repeatable'    => true,
    'default'       => 'y',
    'label' => array( 
        'x' => 'X',
        'y' => 'Y',     
        'z' => 'Z',     
    ),
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'select_sortable',
                'title'         => __( 'Sortable', 'admin-page-framework-loader' ),
                'type'          => 'select',
                'sortable'      => true,
                'default'       => 'iii',
                'before_label'  =>
                    "<span style='vertical-align:baseline; min-width: 140px; display:inline-block; margin-top: 0.5em; padding-bottom: 0.2em;'>"
                        . __( 'Sortable Item', 'admin-page-framework-loader' )
                    . "</span>",
                'label'         => array(
                    'i'     => 'I',
                    'ii'    => 'II',
                    'iii'   => 'III',
                    'iiv'   => 'IIV',
                ),
                array(), // the second item - will inherit the main field's arguments
                array(), // the third item
                array(), // the forth item
                'description'   => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'select',
    'sortable'      => true,
    'default'       => 'iii',
    'label'         => array( 
        'i'     => 'I',
        'ii'    => 'II',    
        'iii'   => 'III',     
        'iiv'   => 'IIV',     
    ),
    array(), // the second item 
    array(), // the third item
    array(), // the forth item
)
EOD
                        )
                        . "</pre>",
                ),
            )
        );

    }


}
