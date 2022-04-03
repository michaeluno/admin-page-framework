<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * A text field with a media uploader lets the user set a file URL.
 *
 * This class defines the media field type.
 *
 * <h2>Field Definition Arguments</h2>
 * <h3>Field Type Specific Arguments</h3>
 * <ul>
 *     <li>**attributes_to_store** - [2.1.3+] (optional, array) the array of the attribute names of the image to save. If this is set, the field will be an array with the specified attributes. The supported attributes are, 'id', 'caption', and 'description'. Note that for external URLs, ID will not be captured. e.g. `'attributes_to_store' => array( 'id', 'caption', 'description' )`</li>
 *     <li>**allow_external_source** - [2.1.3+] (optional, boolean) whether external URL can be set via the uploader.</li>
 *     <li>**attributes** - [3.2.0+] (optional, boolean) there are additional nested attribute arguments.
 *         <ul>
 *             <li>`button` - (array) applies to the Select File button.</li>
 *             <li>`remove_button` - (array) applies to the Remove button.</li>
 *         </ul>
 *     </li>
 *
 * </ul>
 *
 * <h3>Common Field Definition Arguments</h3>
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 *
 * <h2>Example</h2>
 ** <code>
 *   array(
 *       'field_id'              => 'media_with_attributes',
 *       'title'                 => __( 'Media File with Attributes', 'admin-page-framework-loader' ),
 *       'type'                  => 'media',
 *       'attributes_to_store'   => array( 'id', 'caption', 'description' ),
 *       'attributes'            => array(
 *           'button'        => array(
 *               'data-label' => __( 'Select File', 'admin-page-framework-loader' ),
 *           ),
 *           'remove_button' => array(      // 3.2.0+
 *               'data-label' => __( 'Remove', 'admin-page-framework-loader' ), // will set the Remove button label instead of the dashicon
 *           ),
 *       ),
 *   )
 ** </code>
 *
 * @image       http://admin-page-framework.michaeluno.jp/image/common/form/field_type/media.png
 * @package     AdminPageFramework/Common/Form/FieldType
 * @since       2.1.5
 * @extends     AdminPageFramework_FieldType_image
 */
class AdminPageFramework_FieldType_media extends AdminPageFramework_FieldType_image {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'media', );

    /**
     * Defines the default key-values of this field type.
     *
     * @remark $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'attributes_to_store'   => array(), // ( array ) This is for the image and media field type. The attributes to save besides URL. e.g. ( for the image field type ) array( 'title', 'alt', 'width', 'height', 'caption', 'id', 'align', 'link' ).
        'show_preview'          => true,
        'allow_external_source' => true,    // ( boolean ) Indicates whether the media library box has the From URL tab.
        'mime_types'            => array(   // [3.9.1+]
            // 'image',
            // 'text/plain',
            // 'application/pdf', // etc
        ),
        'attributes'            => array(
            'input'     => array(
                'size'      => 40,
                'maxlength' => 400,
            ),
            'button'    => array(
            ),
            'remove_button' =>  array(  // 3.2.0+
            ),
            'preview'   => array(
            ),
        ),
    );

    /**
     * @return array
     * @since  3.9.0
     */
    protected function getEnqueuingScripts() {
        return array(
            array(
                'handle_id'     => 'admin-page-framework-field-type-media',
                'src'           => dirname( __FILE__ ) . '/js/media.bundle.js',
                'in_footer'         => true,
                'dependencies'      => array( 'jquery', 'admin-page-framework-script-form-main' ),
                'translation_var'   => 'AdminPageFrameworkMediaFieldType',
                'translation'       => array(
                    'fieldTypeSlugs'    => $this->aFieldTypeSlugs,
                    'referer'           => 'admin_page_framework',
                    'hasMediaUploader'  => function_exists( 'wp_enqueue_media' ),
                    'label'             => array(
                        'uploadFile'     => $this->oMsg->get( 'upload_file' ),
                        'useThisFile'    => $this->oMsg->get( 'use_this_file' ),
                        'insertFromURL'  => $this->oMsg->get( 'insert_from_url' ),
                    ),
                ),
            ),
        );
    }

    /**
     * Returns an HTML output of an uploader button.
     * @since       3.5.3
     * @return      string      The generated HTML uploader button output.
     * @internal
     */
    protected function _getUploaderButtonHTML( $sInputID, array $aButtonAttributes, $bRepeatable, $bExternalSource ) {
        $_bIsLabelSet = isset( $aButtonAttributes[ 'data-label' ] ) && $aButtonAttributes[ 'data-label' ];
        $_aAttributes = $this->___getFormattedUploadButtonAttributes_Media(
            $sInputID,
            $aButtonAttributes,
            $_bIsLabelSet,
            $bExternalSource,
            $bRepeatable
        );
        return "<a " . $this->getAttributes( $_aAttributes ) . ">"
                . $this->getAOrB(
                    $_bIsLabelSet,
                    $_aAttributes[ 'data-label' ],
                    $this->getAOrB(
                        strrpos( $_aAttributes[ 'class' ], 'dashicons' ),
                        '',
                        $this->oMsg->get( 'select_file' )
                    )
                )
            ."</a>";

    }
        /**
         * Returns a formatted upload button attributes array.
         * @since       3.5.3
         * @return      array       The formatted upload button attributes array.
         * @internal
         */
        private function ___getFormattedUploadButtonAttributes_Media( $sInputID, array $aButtonAttributes, $_bIsLabelSet, $bExternalSource, $bRepeatable ) {

            $_aAttributes           = array(
                    'id'                          => "select_media_{$sInputID}",
                    'href'                        => '#',
                    'data-input_id'               => $sInputID,
                    'data-repeatable'             => ( string ) ( boolean ) $bRepeatable,
                    'data-uploader_type'          => ( string ) function_exists( 'wp_enqueue_media' ),    //  ? 1 : 0,
                    'data-enable_external_source' => ( string ) ( boolean ) $bExternalSource,    //  ? 1 : 0,
                )
                + $aButtonAttributes
                + array(
                    'title'     => $_bIsLabelSet
                        ? $aButtonAttributes['data-label']
                        : $this->oMsg->get( 'select_file' ),
                    'data-label' => null,
                );
            $_aAttributes[ 'class' ]  = $this->getClassAttribute(
                'select_media button button-small ',
                $this->getAOrB(
                    trim( $aButtonAttributes[ 'class' ] ),
                    $aButtonAttributes[ 'class' ],
                    $this->getAOrB(
                        ! $_bIsLabelSet && version_compare( $GLOBALS['wp_version'], '3.8', '>=' ),
                        'dashicons dashicons-portfolio',
                        ''
                    )
                )
            );
            return $_aAttributes;

        }

    /**
     * Override the parent method not to show the preview.
     * @param  array  $aField
     * @param  string $sImageURL
     * @param  array  $aPreviewAtrributes
     * @return string
     */
    protected function _getPreviewContainer( $aField, $sImageURL, $aPreviewAtrributes ) {
        return '';
    }

}