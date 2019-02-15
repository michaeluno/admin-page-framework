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
 * Adds a tab that displays the `toggle` field examples.
 *
 * @since       3.8.4
 */
class APF_Demo_CustomFieldType_Toggle {

    public $oFactory;

    public $sClassName;

    public $sPageSlug;

    public $sTabSlug = 'toggle';

    public function __construct( $oFactory, $sPageSlug ) {

        $this->oFactory     = $oFactory;
        $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = $sPageSlug;
        $this->sSectionID   = $this->sTabSlug;

        $this->oFactory->addInPageTabs(
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Toggle', 'admin-page-framework-loader' ),
            )
        );

        // Register the field type.
        new ToggleCustomFieldType( $this->sClassName );

        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToLoadTab' ) );

    }

    /**
     * Triggered when the tab starts loading.
     *
     * @callback        action      load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oAdminPage ) {

        add_action( 'do_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToDoTab' ) );

         // Section
        $oAdminPage->addSettingSections(
            $this->sPageSlug, // the target page slug
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Toggle', 'admin-page-framework-loader' ),
                'description'   => array(
                    __( 'This field type lets the user toggle a button.', 'admin-page-framework-loader' ),
                ),
            )
        );

        // Fields
        $oAdminPage->addSettingFields(
            $this->sSectionID,
            array(
                'field_id'      => 'toggle_button',
                'type'          => 'toggle',
                'title'         => __( 'Toggle', 'admin-page-framework-loader' ),
                'default'       => true,
                // @see For the list of arguments, refer to https://github.com/simontabor/jquery-toggles#step-3-initialize
                'options'       => array(
                ),
                'description'   => array(
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'toggle',
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'toggle_no_dragging',
                'type'          => 'toggle',
                'title'         => __( 'No Dragging', 'admin-page-framework-loader' ),
                'default'       => true,
                // @see For the list of arguments, refer to https://github.com/simontabor/jquery-toggles#step-3-initialize
                'options'       => array(
                    'drag' => false,
                ),
                'description'   => array(
                    __( 'Dragging is disabled.', 'admin-page-framework-loader' ),
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'toggle',
    'options'       => array(
        'drag' =>  false,
    ),                        
)
EOD
                        )
                        . "</pre>"
                ),
            ),
            array(
                'field_id'      => 'toggle_no_clickign',
                'type'          => 'toggle',
                'title'         => __( 'No Clicking', 'admin-page-framework-loader' ),
                'default'       => true,
                // @see For the list of arguments, refer to https://github.com/simontabor/jquery-toggles#step-3-initialize
                'options'       => array(
                    'click' => false,
                ),
                'description'   => array(
                    __( 'Clicking is disabled.', 'admin-page-framework-loader' ),
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'toggle',
    'options'       => array(
        'click' => false,
    ),                        
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'toggle_custom_dimension',
                'type'          => 'toggle',
                'title'         => __( 'Custom Dimension', 'admin-page-framework-loader' ),
                'default'       => true,
                // @see For the list of arguments, refer to https://github.com/simontabor/jquery-toggles#step-3-initialize
                'options'       => array(
                    'width' => 200,
                    'height' => 30,
                ),
                'description'   => array(
                    __( 'Custom height and width can be set.', 'admin-page-framework-loader' ),
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'toggle',
    'options'       => array(
        'width' => 100,
        'height' => 40,
    ),                        
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'toggle_select_type',
                'type'          => 'toggle',
                'title'         => __( 'Select Type', 'admin-page-framework-loader' ),
                'options'       => array(
                    'type'  => 'select',
                ),
                'description'   => array(
                    __( 'If this is set to `select`, the select style toggle is used.', 'admin-page-framework-loader' ),
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'toggle',
    'options'       => array(
        'type'  => 'select',
    ),                        
) 
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'toggle_disabled',
                'type'          => 'toggle',
                'title'         => __( 'Disabled', 'admin-page-framework-loader' ),
                'attributes' => array(
                    'disabled' => 'disabled',
                ),
                'description'   => array(
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'toggle',
    'attributes'    => array( 
        'disabled' => 'disabled', 
    ),
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'toggle_theme_soft',
                'type'          => 'toggle',
                'title'         => __( 'Soft Theme', 'admin-page-framework-loader' ),
                'theme'         => 'soft',
                'default'       => true,
                'description'   => array(
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'toggle',
    'theme'         => 'soft',
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'toggle_theme_light',
                'type'          => 'toggle',
                'title'         => __( 'Light Theme', 'admin-page-framework-loader' ),
                'theme'         => 'light',
                'default'       => true,
                'description'   => array(
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'toggle',
    'theme'         => 'light',
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'toggle_theme_dark',
                'type'          => 'toggle',
                'title'         => __( 'Dark Theme', 'admin-page-framework-loader' ),
                'theme'         => 'dark',
                'default'       => true,
                'description'   => array(
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'toggle',
    'theme'         => 'dark',
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'toggle_theme_iphone',
                'type'          => 'toggle',
                'default'       => true,
                'title'         => __( 'iPhone Theme', 'admin-page-framework-loader' ),
                'theme'         => 'iphone',
                'description'   => array(
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'toggle',
    'theme'         => 'iphone',
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'toggle_custom_label',
                'type'          => 'toggle',
                'title'         => __( 'Custom Label', 'admin-page-framework-loader' ),
                'options'       => array(
                    'text' => array(
                        'on' => 'Yes',
                        'off' => 'No',
                    ),
                    'width'     => 72,
                ),
                'description'   => array(
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'toggle',
    'options'       => array(
        'text' => array(
            'on' => 'Yes',
            'off' => 'No',
        )
    ),
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'toggle_field_repeatable_sortable',
                'type'          => 'toggle',
                'title'         => __( 'Repeatable & Sortable', 'admin-page-framework-loader' ),
                'repeatable'    => true,
                'sortable'      => true,
                'description'   => array(
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'toggle',
    'repeatable'    => true,
    'sortable'      => true,
)
EOD
                        )
                        . "</pre>",
                ),
            )
        );

    }

    public function replyToDoTab() {
        submit_button();
    }

}
