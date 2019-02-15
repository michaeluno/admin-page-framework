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
class APF_Demo_BuiltinFieldTypes_MISC_Submit {

    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_builtin_field_types';

    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'misc';

    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'submit_buttons';

    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {

        // Validation
        add_filter(
            'validation_' . $oFactory->oProp->sClassName . '_' . $this->sSectionID,
            array( $this, 'validate' ),
            10,
            4
        );

        // Section
        $oFactory->addSettingSections(
            $this->sPageSlug, // the target page slug
            array(
                'tab_slug'          => $this->sTabSlug,
                'section_id'        => $this->sSectionID,
                'title'             => __( 'Submit Buttons', 'admin-page-framework-loader' ),
                'description'       => __( 'These are custom submit buttons.', 'admin-page-framework-loader' ),
            )
        );

        // Fields
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID
            array(
                'field_id'          => 'submit_button_field',
                'title'             => __( 'Submit Button', 'admin-page-framework-loader' ),
                'type'              => 'submit',
                'description'       => array(
                    __( 'This is the default submit button.', 'admin-page-framework-loader' ),
                    __( 'Use the <code>value</code> argument to set a custom label.', 'admin-page-framework-loader' ),
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'              => 'submit',
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array( // Submit button as a link
                'field_id'          => 'submit_button_link',
                'type'              => 'submit',
                'title'             => __( 'Link Button', 'admin-page-framework-loader' ),
                'label'             => __( 'WordPress', 'admin-page-framework-loader' ),
                'href'              => 'https://wordpress.org',
                'attributes'        => array(
                    'class'     => 'button button-secondary',
                    'title'     => __( 'Go to the WordPress official site.', 'admin-page-framework-loader' ),
                    'style'     => 'background-color: #C1DCFA;',
                    'field'     => array(
                        'style' => 'display: inline; clear: none;',
                    ),
                ),
                array(
                    'label'         => __( 'Tutorials', 'admin-page-framework-loader' ),
                    'href'          => 'http://admin-page-framework.michaeluno.jp/tutorials',
                    'attributes'    => array(
                        'class' => 'button button-secondary',
                        'title' => __( 'Go to the tutorial page of the Admin Page Framework site.', 'admin-page-framework-loader' ),
                        'style' => 'background-color: #C8AEFF;',
                    ),
                ),
                array(
                    'label'         => __( 'Documentation', 'admin-page-framework-loader' ),
                    'href'          => 'http://admin-page-framework.michaeluno.jp/en/v3/package-AdminPageFramework.html',
                    'attributes'    => array(
                        'class' => 'button button-secondary',
                        'title' => __( 'Go to the documentation page of Admin Page Framework.', 'admin-page-framework-loader' ),
                        'style' => 'background-color: #FFE5AE;',
                    ),
                ),
                'description'       => array(
                    __( 'These buttons serve as a hyper link. Set the url to the <code>href</code> argument to enable this option.', 'admin-page-framework-loader' ),
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'              => 'submit',
    'label'             => 'WordPress',
    'href'              => 'https://wordpress.org',
    'attributes'        => array(
        'class'     => 'button button-secondary',     
        'title'     => 'Go to the WordPress official site.',
        'style'     => 'background-color: #C1DCFA;',
        'field'     => array(
            'style' => 'display: inline; clear: none;',
        ),
    ),
    array(
        'label'         => 'Tutorials',
        'href'          => 'http://admin-page-framework.michaeluno.jp/tutorials',
        'attributes'    => array(
            'class' => 'button button-secondary',     
            'title' => 'Go to the tutorial page of the Admin Page Framework site.',
            'style' => 'background-color: #C8AEFF;',
        ),
    ),
    array(
        'label'         => 'Documentation',
        'href'          => 'http://admin-page-framework.michaeluno.jp/en/v3/package-AdminPageFramework.html',
        'attributes'    => array(
            'class' => 'button button-secondary',     
            'title' => 'Go to the documentation page ...',
            'style' => 'background-color: #FFE5AE;',
        ),     
    ),
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'submit_button_download',
                'title'         => __( 'Download Button', 'admin-page-framework-loader' ),
                'type'          => 'submit',
                'value'         => __( 'Admin Page Framework', 'admin-page-framework-loader' ),
                'href'          => 'http://downloads.wordpress.org/plugin/admin-page-framework.latest-stable.zip',
                'description'   => array(
                    __( 'Download the latest version of the Admin Page Framework Demo plugin.', 'admin-page-framework-loader' ),
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'submit',
    'value'         => 'Admin Page Framework',
    'href'          => 'http://downloads.wordpress.org/plugin/admin-page-framework.latest-stable.zip',
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'submit_button_redirect',
                'title'         => __( 'Redirect Button', 'admin-page-framework-loader' ),
                'type'          => 'submit',
                'value'         => __( 'Dashboard', 'admin-page-framework-loader' ),
                'redirect_url'  => admin_url(),
                'attributes'    => array(
                    'class' => 'button button-secondary',
                ),
                'description'   => array(
                    sprintf( __( 'Unlike the above link buttons, this button saves the options and then redirects to: <code>%1$s</code>', 'admin-page-framework-loader' ), admin_url() )
                    . ' ' . __( 'To enable this functionality, set the url to the <code>redirect_url</code> argument in the field definition array.', 'admin-page-framework-loader' ),
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'submit',
    'value'         => 'Dashboard',
    'redirect_url'  => admin_url(),
    'attributes'    => array(
        'class' => 'button button-secondary',
    ),
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'          => 'image_submit_button',
                'title'             => __( 'Image Submit Button', 'admin-page-framework-loader' ),
                'type'              => 'submit',
                'href'              => 'http://en.michaeluno.jp/donate',
                'attributes'        =>  array(
                   'src'    => AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/image/donation.gif',
                   'alt'    => __( 'Submit', 'admin-page-framework-loader' ),
                   'class'  => '',
                ),
                'description'   => array(
                    __( 'For a custom image submit button, set the image url in the <code>src</code> attribute with the <code>attributes</code> argument.', 'admin-page-framework-loader' )
                    . ' ' . __( 'This button will take you to the donation page for the developer of this framework. If you like to donate, please do so to help the development!', 'admin-page-framework-loader' ),
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'              => 'submit',
    'href'              => 'http://en.michaeluno.jp/donate',
    'attributes'        =>  array(
       'src'    => AdminPageFrameworkLoader_Registry::\$sDirPath . '/asset/image/donation.gif',
       'alt'    => 'Submit',
       'class'  => '',
    ),
)
EOD
                        )
                        . "</pre>",
                ),
            ),

            array( // Reset Submit button
                'field_id'      => 'submit_button_reset',
                'title'         => __( 'Reset Button', 'admin-page-framework-loader' ),
                'type'          => 'submit',
                'value'         => __( 'Reset', 'admin-page-framework-loader' ),
                'reset'         => true,
                'attributes'    => array(
                    'class' => 'button button-secondary',
                ),
                'description'   => array(
                    __( 'If you press this button, a confirmation message will appear and then if you press it again, it resets the option.', 'admin-page-framework-loader' ),
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'submit',
    'value'         => 'Reset',
    'reset'         => true,
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'submit_button_reset_section',
                'title'         => __( 'Reset Section', 'admin-page-framework-loader' ),
                'type'          => 'submit',
                'value'         => __( 'Reset Section', 'admin-page-framework-loader' ),
                'reset'         => 'color_picker',    // the section ID to reset
                'attributes'    => array(
                    'class' => 'button button-secondary',
                ),
                'description'   => array(
                    __( 'To reset values of a section, set the section ID to the <code>reset</code> argument.', 'admin-page-framework-loader' ),
                    __( 'As an example, this reset button rests the Color section above.', 'admin-page-framework-loader' ),
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'submit',
    'value'         => 'Reset Section',
    
    // The section ID to reset 
    'reset'         => 'color_picker', 
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'      => 'submit_button_reset_field',
                'title'         => __( 'Reset Field', 'admin-page-framework-loader' ),
                'type'          => 'submit',
                'value'         => __( 'Reset Field', 'admin-page-framework-loader' ),
                'reset'         => array(
                    'color_picker',         // section ID
                    'color_picker_field'    // field ID to reset
                ),
                'attributes'    => array(
                    'class' => 'button button-secondary',
                ),
                'description'   => array(
                    __( 'To reset a value of a particular field, set an array with the the section ID in the first element and field ID in the second element to the <code>reset</code> argument.', 'admin-page-framework-loader' ),
                    __( 'If a field does not have a section, just set the field ID.', 'admin-page-framework-loader' ),
                    __( 'As an example, this reset button rests the first item of the Color section above.', 'admin-page-framework-loader' ),
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'submit',
    'value'         => 'Reset Field',
    'reset'         => array( 
        'color_picker',         // section ID   
        'color_picker_field'    // field ID to reset
    ),
    'attributes'    => array(
        'class' => 'button button-secondary',
    ),
)
EOD
                        )
                        . "</pre>",
                ),
            )
        );

    }

    /**
     * @return      array
     */
    public function validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {

        $_bIsValid = true;
        $_aErrors  = array();

        if ( ! $_bIsValid ) {

            $oFactory->setFieldErrors( $_aErrors );
            $oFactory->setSettingNotice( __( 'Please help us to help you.', 'admin-page-framework-loader' ) );
            return $aOldInputs;

        }

        return $aInputs;

    }

}
