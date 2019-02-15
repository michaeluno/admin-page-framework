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
 * Creates a widget with a form section.
 *
 * @since   DEVVER
 */
class APF_Widget_WithSection extends AdminPageFramework_Widget {

    /**
     * The user constructor.
     *
     * Alternatively you may use start_{instantiated class name} method.
     */
    public function start() {}

    /**
     * Sets up arguments.
     *
     * Alternatively you may use set_up_{instantiated class name} method.
     */
    public function setUp() {

        $this->setArguments(
            array(
                'description'   =>  __( 'This is a sample widget with form sections created by Admin Page Framework.', 'admin-page-framework-loader' ),
            )
        );

    }

    /**
     * Sets up the form.
     *
     * Alternatively you may use load_{instantiated class name} method.
     */
    public function load() {

        $this->addSettingSections(
            array(
                'section_id'    => 'apf_sample_image',
                'title'         => __( 'Image', 'admin-page-framework-loader' ),
                'description'   => __( 'Shows a selected image in the front-end.', 'admin-page-framework-loader' ),
            )
        );

        $this->addSettingFields(
            'apf_sample_image',
            array(
                'field_id'      => 'image',
                'type'          => 'image',
                'title'         => __( 'Image', 'admin-page-framework-loader' ),
            ),
            array(
                'field_id'      => 'bg_color',
                'type'          => 'color',
                'title'         => __( 'Bacground Color', 'admin-page-framework-loader' ),
            ),
            array(
                'field_id'      => 'todays_word',
                'tip'           => __( 'One of the listed items will be displayed randomly at a time.', 'admin-page-framework-loader' ),
                'type'          => 'text',
                'title'         => __( 'Today\'s Word', 'admin-page-framework-loader' ),    // '
                'repeatable'    => true,
                // 'label'         => array(
                    // 'a' => 'A',
                    // 'b' => 'B',
                    // 'c' => 'C',
                // ),
            ),
            array()
        );


    }

    /**
     * Validates the submitted form data.
     *
     * Alternatively you may use validation_{instantiated class name} method.
     */
    public function validate( $aSubmit, $aStored, $oAdminWidget ) {

        // Uncomment the following line to check the submitted value.
        // AdminPageFramework_Debug::log( $aSubmit );

        return $aSubmit;

    }

    /**
     * Print out the contents in the front-end.
     *
     * Alternatively you may use the content_{instantiated class name} method.
     */
    public function content( $sContent, $aArguments, $aFormData ) {
        $_sImageURL = esc_url(
            $this->oUtil->getElement( $aFormData, array( 'apf_sample_image', 'image' ) )
        );
        $_sAlt      = esc_attr( __( 'Sample Image', 'admin-page-framework-loader' ) );
        $_sBGColor  = esc_attr(
            $this->oUtil->getElement( $aFormData, array( 'apf_sample_image', 'bg_color' ) )
        );
        $_aTodaysWord = $this->oUtil->getElementAsArray( $aFormData, array( 'apf_sample_image', 'todays_word' ) );
        shuffle( $_aTodaysWord );
        $_sTodaysWord = $this->oUtil->getElement( $_aTodaysWord, 0 );
        return $sContent
            . "<div style='padding: 1em; background-color:{$_sBGColor}; text-align:center;'>"
                . "<img style='margin-bottom: 0.6em;' src='{$_sImageURL}' alt='{$_sAlt}' />"
                . "<strong>" . $_sTodaysWord . "</strong>"
            . "</div>";

    }

}

new APF_Widget_WithSection(
    __( 'Admin Page Framework - With Section', 'admin-page-framework-loader' ) // the widget title
);
