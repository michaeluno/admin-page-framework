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
class APF_Demo_AdvancedUsage_Nested_Fields {

    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_advanced_usage';

    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'nested';

    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'nested_fields';

    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {

        add_filter( 'validation_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'validate' ), 10, 4 );


        // Sections
        $oFactory->addSettingSections(
            $this->sPageSlug, // the target page slug
            array(
                'section_id'        => $this->sSectionID,
                'title'             => __( 'Nested Fields', 'admin-page-framework-loader' ),
                'description'       => __( 'You can include fields inside a field.', 'admin-page-framework-loader' ),
            )
        );

        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID - pass dimensional keys of the section
            array(
                'field_id'      => 'X',
                'title'         => __( 'X', 'admin-page-framework-loader' ),
                'type'          => 'text',
                'repeatable'    => true,
                'sortable'      => true,
            ),
            array(
                'field_id'      => 'Y',
                'title'         => __( 'Y', 'admin-page-framework-loader' ),
                'description'   => __( 'By passing an array of field definition to the <code>content</code> argument, you can nest fields.', 'admin-page-framework-loader' )
                    . ' ' . __( 'Also the <code>type</code> argument can be omitted.', 'admin-page-framework-loader' ),
                'content'       => array(
                    array(
                        'field_id'      => 'i',
                        'title'         => __( 'i', 'admin-page-framework-loader' ),
                        'type'          => 'textarea',
                    ),
                    array(
                        'field_id'      => 'ii',
                        'title'         => __( 'ii', 'admin-page-framework-loader' ),
                        'type'          => 'color',
                    ),
                    array(
                        'field_id'      => 'iii',
                        'title'         => __( 'iii', 'admin-page-framework-loader' ),
                        'repeatable'    => true,
                        'sortable'      => true,
                        'content'       => array(
                            array(
                                'field_id'      => 'a',
                                'title'         => __( 'a', 'admin-page-framework-loader' ),
                                'type'          => 'image',
                                'attributes'    => array(
                                    'preview' => array(
                                        'style' => 'max-width: 200px;',
                                    ),
                                ),
                            ),
                            array(
                                'field_id'      => 'b',
                                'title'         => __( 'b', 'admin-page-framework-loader' ),
                                'content'       => array(
                                    array(
                                        'field_id'      => 'first',
                                        'title'         => __( '1st', 'admin-page-framework-loader' ),
                                        'type'          => 'color',
                                        'repeatable'    => true,
                                        'sortable'      => true,
                                    ),
                                    array(
                                        'field_id'      => 'second',
                                        'title'         => __( '2nd', 'admin-page-framework-loader' ),
                                        'type'          => 'size',
                                    ),
                                    array(
                                        'field_id'      => 'third',
                                        'title'         => __( '3rd', 'admin-page-framework-loader' ),
                                        'type'          => 'select',
                                        'label'         => array(
                                            'x' => 'X',
                                            'y' => 'Y',
                                            'z' => 'Z',
                                        ),
                                    ),
                                ),
                                // 'description'   => '',
                            ),
                            array(
                                'field_id'      => 'c',
                                'title'         => __( 'c', 'admin-page-framework-loader' ),
                                'type'          => 'radio',
                                'label'         => array(
                                    'a' => __( 'Apple', 'admin-page-framework-loader' ),
                                    'b' => __( 'Banana', 'admin-page-framework-loader' ),
                                    'c' => __( 'Cherry', 'admin-page-framework-loader' ),
                                ),
                                'default'       => 'b',
                            ),
                        )
                    ),
                ),
            ),
            array(
                'field_id'      => 'Z',
                'title'         => __( 'Z', 'admin-page-framework-loader' ),
                'content'       => '<p>'
                        . __( 'This message is inserted with the <code>content</code> argument.', 'admin-page-framework-loader' )
                        . ' ' . __( 'The <code>type</code> argument can be omitted.', 'admin-page-framework-loader' )
                    . '</p>',
            ),
            array()
        );

    }

    /**
     * @callback        filter      validation_{page slug}_{tab slug}
     * @return          string
     */
    public function validate( $aInputs, $aOldInputs, $aSavedData, $aSubmitInfo ) {
        return $aInputs;
    }

}
