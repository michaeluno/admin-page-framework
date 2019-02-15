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
class APF_Demo_BuiltinFieldTypes_Selector_Size {

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
    public $sSectionID  = 'size';

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
                'title'         => __( 'Sizes', 'admin-page-framework-loader' ),
                'description'   => __( 'These are size fields.', 'admin-page-framework-loader' ),
            )
        );

        // Fields
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID
            array( // Size
                'field_id'      => 'size_field',
                'title'         => __( 'Size', 'admin-page-framework-loader' ),
                'help'          => $sDescription = __( 'In order to set a default value for the size field type, an array with the <code>size</code> and the <code>unit</code> arguments needs to be set.', 'admin-page-framework-loader' ),
                'tip'           => __( 'The default units and the lengths for CSS.', 'admin-page-framework-loader' )
                    . ' ' . $sDescription,
                'type'          => 'size',
                'default'       => array(
                    'size' => 5,
                    'unit' => '%',
                ),
                'description'        => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'size',
    'default'       => array( 
        'size' => 5, 
        'unit' => '%',
    ),
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'size_custom_unit_field',
                'title'         => __( 'Size with Custom Units', 'admin-page-framework-loader' ),
                'help'          => $sDescription = __( 'The units can be specified so it can be quantity, length, or capacity etc.', 'admin-page-framework-loader' ),
                'description'   => $sDescription,
                'type'          => 'size',
                'units'         => array(
                    'grain'     => __( 'grains', 'admin-page-framework-loader' ),
                    'dram'      => __( 'drams', 'admin-page-framework-loader' ),
                    'ounce'     => __( 'ounces', 'admin-page-framework-loader' ),
                    'pounds'    => __( 'pounds', 'admin-page-framework-loader' ),
                ),
                'default' => array(
                    'size'      => 200,
                    'unit'      => 'ounce'
                ),
                'description'        => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'size',
    'units'         => array(
        'grain'     => __( 'grains', 'admin-page-framework-loader' ),
        'dram'      => __( 'drams', 'admin-page-framework-loader' ),
        'ounce'     => __( 'ounces', 'admin-page-framework-loader' ),
        'pounds'    => __( 'pounds', 'admin-page-framework-loader' ),
    ),    
    'default' => array( 
        'size'      => 200,
        'unit'      => 'ounce' 
    ),
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'size_field_custom_attributes',
                'title'         => __( 'Size with Custom Attributes', 'admin-page-framework-loader' ),
                'type'          => 'size',
                'units'         => array( // Pass the group label as the key of an option array.
                    __( 'Metric Unit System', 'admin-page-framework' ) => array(     // each key must be unique throughout this 'label' element array.
                        'mm'    => 'mm (' . __( 'millimetre', 'admin-page-framework' ) . ')',
                        'cm'    => 'cm (' . __( 'centmeter', 'admin-page-framework' ) . ')',
                        'm'     => 'm (' . __( 'meter', 'admin-page-framework' ) . ')',
                        'km'    => 'km (' . __( 'kilometer', 'admin-page-framework' ) . ')',
                    ),
                    __( 'Imperial and US Unit System', 'admin-page-framework' ) => array(
                        'in'    => 'in (' . __( 'inch', 'admin-page-framework' ) . ')',
                        'ft'    => 'ft (' . __( 'foot', 'admin-page-framework' ) . ')',
                        'yd'    => 'yd (' . __( 'yard', 'admin-page-framework' ) . ')',
                        'ml'    => 'ml (' . __( 'mile', 'admin-page-framework' ) . ')',
                    ),
                    __( 'Astronomical Units', 'admin-page-framework' ) => array(
                        'au'    => 'au (' . __( 'astronomical unit', 'admin-page-framework' ) . ')',
                        'ly'    => 'ly (' . __( 'light year', 'admin-page-framework' ) . ')',
                        'pc'    => 'pc (' . __( 'parsec', 'admin-page-framework' ) . ')',
                    ),
                ),
                'default'       => array(
                    'size' => 15.2,
                    'unit' => 'ft'
                ),
                'attributes'    => array( // the size field type has four nested arguments: size, option, optgroup.
                    'size'      => array(
                        'style' => 'background-color: #FAF0F0;',
                        'step'  => 0.1,
                    ),
                    'unit'      => array(
                        'style' => 'background-color: #F0FAF4',
                    ),
                    'option'    => array(
                        'cm' => array( // applies only to the 'cm' element of the option elements
                            'disabled'  => 'disabled',
                            'class'     => 'disabled',
                        ),
                        'style' => 'background-color: #F7EFFF', // applies to all the option elements
                    ),
                    'optgroup'  => array(
                        'style' => 'background-color: #EFEFEF',
                        __( 'Astronomical Units', 'admin-page-framework' ) => array(
                            'disabled' => 'disabled',
                        ),
                    ),
                ),
                'tip'               => __( 'The <code>size</code> field type has four nested arguments in the <code>attributes</code> array element: <code>size</code>, <code>unit</code>, <code>optgroup</code>, and <code>option</code>.', 'admin-page-framework-loader' ),
                'description'       => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'size',
    // Set the group label as the key of an option array.
    'units'         => array( 
        __( 'Metric Unit System', 'admin-page-framework' ) => array(    
            'mm'    => 'mm (' . __( 'millimetre', 'admin-page-framework' ) . ')', 
            'cm'    => 'cm (' . __( 'centmeter', 'admin-page-framework' ) . ')', 
            'm'     => 'm (' . __( 'meter', 'admin-page-framework' ) . ')', 
            'km'    => 'km (' . __( 'kilometer', 'admin-page-framework' ) . ')', 
        ),
        __( 'Imperial and US Unit System', 'admin-page-framework' ) => array( 
            'in'    => 'in (' . __( 'inch', 'admin-page-framework' ) . ')', 
            'ft'    => 'ft (' . __( 'foot', 'admin-page-framework' ) . ')', 
            'yd'    => 'yd (' . __( 'yard', 'admin-page-framework' ) . ')', 
            'ml'    => 'ml (' . __( 'mile', 'admin-page-framework' ) . ')', 
        ),     
        __( 'Astronomical Units', 'admin-page-framework' ) => array( 
            'au'    => 'au (' . __( 'astronomical unit', 'admin-page-framework' ) . ')', 
            'ly'    => 'ly (' . __( 'light year', 'admin-page-framework' ) . ')', 
            'pc'    => 'pc (' . __( 'parsec', 'admin-page-framework' ) . ')', 
        ),     
    ),
    'default'       => array( 
        'size' => 15.2, 
        'unit' => 'ft' 
    ),
    // the size field type has four nested arguments: size, option, optgroup.
    'attributes'    => array( 
        'size'      => array(
            'style' => 'background-color: #FAF0F0;',
            'step'  => 0.1,
        ),
        'unit'      => array(
            'style' => 'background-color: #F0FAF4',
        ),
        'option'    => array(
            // applies only to the 'cm' element of the option elements
            'cm' => array( 
                'disabled'  => 'disabled',
                'class'     => 'disabled',
            ),
            // applies to all the option elements
            'style' => 'background-color: #F7EFFF', 
        ),
        'optgroup'  => array(
            'style' => 'background-color: #EFEFEF',
            __( 'Astronomical Units', 'admin-page-framework' ) => array(
                'disabled' => 'disabled',
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
                'field_id'      => 'sizes_field',
                'title'         => __( 'Multiple', 'admin-page-framework-loader' ),
                'type'          => 'size',
                'label'         => __( 'Weight', 'admin-page-framework-loader' ),
                'units'         => array( 'mg'=>'mg', 'g'=>'g', 'kg'=>'kg' ),
                'default'       => array( 'size' => 15, 'unit' => 'g' ),
                'delimiter'     => '<hr />',
                array(
                    'label'     => __( 'Length', 'admin-page-framework-loader' ),
                    'units'     => array( 'cm'=>'cm', 'mm'=>'mm', 'm'=>'m' ),
                    'default'   => array( 'size' => 100, 'unit' => 'mm' ),
                ),
                array(
                    'label'     => __( 'File Size', 'admin-page-framework-loader' ),
                    'units'     => array( 'b'=>'b', 'kb'=>'kb', 'mb'=>'mb', 'gb' => 'gb', 'tb' => 'tb' ),
                    'default'   => array( 'size' => 30, 'unit' => 'mb' ),
                ),
                'description'       => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'size',
    'label'         => 'Weight',
    'units'         => array( 'mg'=>'mg', 'g'=>'g', 'kg'=>'kg' ),
    'default'       => array( 'size' => 15, 'unit' => 'g' ),
    'delimiter'     => '<hr />',
    array(
        'label'     => __( 'Length', 'admin-page-framework-loader' ),
        'units'     => array( 'cm'=>'cm', 'mm'=>'mm', 'm'=>'m' ),
        'default'   => array( 'size' => 100, 'unit' => 'mm' ),
    ),
    array(
        'label'     => __( 'File Size', 'admin-page-framework-loader' ),
        'units'     => array( 'b'=>'b', 'kb'=>'kb', 'mb'=>'mb', 'gb' => 'gb', 'tb' => 'tb' ),
        'default'   => array( 'size' => 30, 'unit' => 'mb' ),
    ),
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'          => 'sizes_field_multiple_labels',
                'title'             => __( 'Sub-fields by Label', 'admin-page-framework-loader' ),
                'type'              => 'size',
                'label'             => array(
                    'weight'    => __( 'Weight', 'admin-page-framework-loader' ),
                    'length'    => __( 'Length', 'admin-page-framework-loader' ),
                    'size'      => __( 'File Size', 'admin-page-framework-loader' ),
                ),
                'after_label'       => array(
                    'weight'    => '<br />',
                    'length'    => '<br />',
                    'size'      => '<br />',
                ),
                'units'             => array(
                    'weight'    => array( 'mg'=>'mg', 'g'=>'g', 'kg'=>'kg' ),
                    'length'    => array( 'cm'=>'cm', 'mm'=>'mm', 'm'=>'m' ),
                    'size'      => array( 'b'=>'b', 'kb'=>'kb', 'mb'=>'mb', 'gb' => 'gb', 'tb' => 'tb' ),
                ),
                'default'           => array(
                    'weight'    => array( 'size' => 15, 'unit' => 'g' ),
                    'length'    => array( 'size' => 100, 'unit' => 'mm' ),
                    'size'      => array( 'size' => 30, 'unit' => 'mb' ),
                ),
                'description'       => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'              => 'size',
    'label'             => array(
        'weight'    => __( 'Weight', 'admin-page-framework-loader' ),
        'length'    => __( 'Length', 'admin-page-framework-loader' ),
        'size'      => __( 'File Size', 'admin-page-framework-loader' ),
    ),
    'after_label'       => array(
        'weight'    => '<br />',
        'length'    => '<br />',
        'size'      => '<br />',
    ),                
    'units'             => array(
        'weight'    => array( 'mg'=>'mg', 'g'=>'g', 'kg'=>'kg' ),
        'length'    => array( 'cm'=>'cm', 'mm'=>'mm', 'm'=>'m' ),
        'size'      => array( 'b'=>'b', 'kb'=>'kb', 'mb'=>'mb', 'gb' => 'gb', 'tb' => 'tb' ),
    ),
    'default'           => array(
        'weight'    => array( 'size' => 15, 'unit' => 'g' ),
        'length'    => array( 'size' => 100, 'unit' => 'mm' ),               
        'size'      => array( 'size' => 30, 'unit' => 'mb' ),
    ),
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'size_repeatable_fields',
                'title'         => __( 'Repeatable', 'admin-page-framework-loader' ),
                'type'          => 'size',
                'repeatable'    => true,
                'description'       => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'size',
    'repeatable'    => true,
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'size_sortable_fields',
                'title'         => __( 'Sortable', 'admin-page-framework-loader' ),
                'type'          => 'size',
                'sortable'      => true,
                array(), // the second item
                array(), // the third item
                array(), // the fourth item
                'description'       => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'size',
    'sortable'      => true,
)
EOD
                        )
                        . "</pre>",
                ),
            )
        );

    }

}
