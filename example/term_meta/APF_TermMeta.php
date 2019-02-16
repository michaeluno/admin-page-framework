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

class APF_TermMeta extends AdminPageFramework_TermMeta {

    /*
     * ( optional ) Use the setUp() method to define settings of this taxonomy fields.
     */
    public function setUp() {

        /*
         * ( optional ) Adds setting fields into the meta box.
         */
        $this->addSettingFields(
            array(
                'field_id'      => 'text_field',
                'type'          => 'text',
                'title'         => __( 'Text Input', 'admin-page-framework-loader' ),
                'description'   => __( 'The description for the field.', 'admin-page-framework-loader' ),
                'help'          => 'This is help text.',
                'help_aside'    => 'This is additional help text which goes to the side bar of the help pane.',
            ),
            array(
                'field_id'      => 'text_field_repeatable',
                'type'          => 'text',
                'title'         => __( 'Text Repeatable', 'admin-page-framework-loader' ),
                'repeatable'    => true
            ),
            array(
                'field_id'      => 'image_upload',
                'type'          => 'image',
                'title'         => __( 'Image Upload', 'admin-page-framework-loader' ),
                'attributes'    => array(
                    'preview' => array(
                        'style' => 'max-width: 200px;',
                    ),
                ),
            )
        );

        $this->addSettingSections(
            array(
               'section_id'     => 'term_section',
               'title'          => __( 'Section', 'admin-page-framework-loader' ),
            )
        );

        $this->addSettingFields(
            'term_section', // section ID
            array(
                'field_id'      => 'textarea_field',
                'type'          => 'textarea',
                'title'         => __( 'Text Area', 'admin-page-framework-loader' ),
                'description'   => __( 'The description for the field.', 'admin-page-framework-loader' ),
                'help'          => __( 'This a <em>text area</em> input field, which is larger than the <em>text</em> input field.', 'admin-page-framework-loader' ),
                'default'       => __( 'This is a default text value.', 'admin-page-framework-loader' ),
                'attributes'    => array(
                    'cols' => 40,
                ),
            )
        );

    }

    /**
     * ( optional ) modify the columns of the term listing table
     *
     * @callback        sortable_column_{instantiated class name}
     */
    public function sortable_columns_APF_TermMeta( $aColumn ) {

        return array(
                'custom' => 'custom',
            )
            + $aColumn;

    }

    /**
     *
     * @callback        column_{instantiated class name}
     */
    public function columns_APF_TermMeta( $aColumn ) {

        unset( $aColumn[ 'description' ] );
        // in term.php this method is also called but the `cb` element does not exist
        $_aCheckBox = isset( $aColumn[ 'cb' ] )
            ? array( 'cb' => $aColumn[ 'cb' ] )
            : array();
        return $_aCheckBox
            + array(
                'thumbnail' => __( 'Thumbnail', 'admin-page-framework-loader' ),
                'custom'    => __( 'Custom Column', 'admin-page-framework-loader' ),
            )
            + $aColumn;

    }

    /**
     * (optional) output the stored option to the custom column
     *
     * @callback        cell_{instantiated class name}
     */
    public function cell_APF_TermMeta( $sCellHTML, $sColumnSlug, $iTermID ) {

        if ( ! $iTermID || $sColumnSlug !== 'thumbnail' ) {
            return $sCellHTML;
        }

        $_sImageURL = get_term_meta( $iTermID, 'image_upload', true );
        return $_sImageURL
            ? "<img src='" . esc_url( $_sImageURL ) . "' style='max-height: 72px; max-width: 120px;'/>"
            : $sCellHTML;

    }

    /**
     *
     * @callback        cell_{instantiated class name}_{cell slug}
     */
    public function cell_APF_TermMeta_custom( $sCellHTML, $iTermID ) {
        return get_term_meta( $iTermID, 'text_field', true );
    }

    /**
     * ( optional ) Use this method to insert your custom text.
     *
     * @callback        do_{instantiated class name}
     */
    public function do_APF_TermMeta() {
        ?>
            <p><?php _e( 'This text is inserted with the <code>do_{instantiated class name}</code> hook.', 'admin-page-framework-loader' ) ?></p>
        <?php
    }

    /*
     * ( optional ) Use this method to validate submitted option values.
     */
    public function validation_APF_TermMeta( $aNewOptions, $aOldOptions ) {

        // Do something to compare the values.
        return $aNewOptions;
    }

}

new APF_TermMeta(
    'apf_sample_taxonomy'   // taxonomy slug
);
