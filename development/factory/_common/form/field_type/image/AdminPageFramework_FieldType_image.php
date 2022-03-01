<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * A text field with an image uploader.
 *
 * This class defines the image field type.
 *
 * <h2>Field Definition Arguments</h2>
 * <h3>Field Type Specific Arguments</h3>
 * <ul>
 *      <li>**show_preview** - (optional, boolean) if this is set to false, the image preview will be disabled.</li>
 *      <li>**attributes_to_store** - [2.1.3+] (optional, array) the array of the attribute names of the image to save. If this is set, the field will be an array with the specified attributes. The supported attributes are, 'title', 'alt', 'width', 'height', 'caption', 'id', 'align', and 'link'. Note that for external URLs, ID will not be captured. e.g. `'attributes_to_store' => array( 'id', 'caption', 'description' )`</li>
 *      <li>**allow_external_source** - [2.1.3+] (optional, boolean) whether external URL can be set via the uploader.</li>
 *      <li>**attributes** - [3.0.0+] (optional, array) there are additional nested arguments.
 *          <ul>
 *              <li>`input` - (array) applies to the input tag element.</li>
 *              <li>`preview` - (array) applies to the preview container element.</li>
 *              <li>`button` - (array) applies to the image select (uploader) button. To set a custom text label instead on of an image icon, set it to the `data-label` attribute. e.g. `'button' => array( 'data-label' => 'Select Image' )`</li>
 *              <li>`remove_button` - (array) [3.2.0+] applies to the remove-image button. To set a custom text label instead on of an image icon, set it to the `data-label` attribute. e.g. `'remove_button' => array( 'data-label' => 'Remove Image' )`</li>
 *          </ul>
 *      </li>
 * </ul>
 * <h3>Common Field Definition Arguments</h3>
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 *
 * <h2>Example</h2>
 * <code>
 *  array(
 *      'field_id'      => 'image_select_field',
 *      'title'         => __( 'Select an Image', 'admin-page-framework-loader' ),
 *      'type'          => 'image',
 *      'label'         => __( 'First', 'admin-page-framework-loader' ),
 *      'default'       =>  plugins_url( 'asset/image/demo/wordpress-logo-2x.png', AdminPageFrameworkLoader_Registry::$sFilePath ),
 *      'allow_external_source' => false,
 *      'attributes'    => array(
 *          'preview' => array(
 *              'style' => 'max-width:300px;' // the size of the preview image.
 *          ),
 *      )
 *  )
 * </code>
 *
 * @image       http://admin-page-framework.michaeluno.jp/image/common/form/field_type/image.png
 * @package     AdminPageFramework/Common/Form/FieldType
 * @since       2.1.5
 * @since       3.5.3       Changed it to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
 * @extends     AdminPageFramework_FieldType
 */
class AdminPageFramework_FieldType_image extends AdminPageFramework_FieldType {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'image', );

    /**
     * Defines the default key-values of this field type.
     *
     * @remark $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'attributes_to_store'       => array(), // ( array ) This is for the image and media field type. The attributes to save besides URL. e.g. ( for the image field type ) array( 'title', 'alt', 'width', 'height', 'caption', 'id', 'align', 'link' ).
        'show_preview'              => true,    // ( boolean ) Indicates whether the image preview should be displayed or not.
        'allow_external_source'     => true,    // ( boolean ) Indicates whether the media library box has the From URL tab.
        'attributes'                => array(
            'input'     => array(
                'size'      => 40,
                'maxlength' => 400,
            ),
            'button'            => array(
            ),
            'remove_button'     => array(       // 3.2.0+
            ),
            'preview'           => array(),
        ),
    );

    /**
     * Loads the field type necessary components.
     * @internal
     */
    protected function setUp() {
        $this->enqueueMediaUploader();
    }

    /**
     * @return array
     * @since  3.9.0
     */
    protected function getEnqueuingScripts() {
        return array(
            array(
                'handle_id'     => 'admin-page-framework-field-type-image',
                'src'           => dirname( __FILE__ ) . '/js/image.bundle.js',
                'in_footer'         => true,
                'dependencies'      => array( 'jquery', 'admin-page-framework-script-form-main' ),
                'translation_var'   => 'AdminPageFrameworkImageFieldType',
                'translation'       => array(
                    'fieldTypeSlugs'    => $this->aFieldTypeSlugs,
                    'referer'           => 'admin_page_framework',
                    'hasMediaUploader'  => function_exists( 'wp_enqueue_media' ),
                    'label'             => array(
                        'uploadImage'    => $this->oMsg->get( 'upload_image' ),
                        'useThisImage'   => $this->oMsg->get( 'use_this_image' ),
                        'insertFromURL'  => $this->oMsg->get( 'insert_from_url' ),
                    ),
                ),
            ),
        );
    }

    /**
     * Returns the output of the field type.
     *
     * @since    2.1.5
     * @since    3.0.0   Reconstructed entirely.
     * @since    3.5.3   Changed the name from `_replyToGetField()`.
     * @return   string
     * @internal
     */
    protected function getField( $aField ) {

        // If the saving extra attributes are not specified, the input field will be single only for the URL.
        $_iCountAttributes  = count( $this->getElementAsArray( $aField, 'attributes_to_store' ) );
        $_sImageURL         = $this->___getTheSetImageURL( $aField, $_iCountAttributes );
        $_aBaseAttributes   = $this->___getBaseAttributes( $aField );

        $_aUploadButtonAttributes = $this->getElementAsArray( $aField, array( 'attributes', 'button' ) ) + $_aBaseAttributes;
        $_aRemoveButtonAttributes = $this->getElementAsArray( $aField, array( 'attributes', 'remove_button' ) ) + $_aBaseAttributes;
        $_bIsLabelSet             = isset( $_aRemoveButtonAttributes[ 'data-label' ] ) && $_aRemoveButtonAttributes[ 'data-label' ];
        $_aRemoveButtonAttributes = $this->_getFormattedRemoveButtonAttributesByType( $aField[ 'input_id' ], $_aRemoveButtonAttributes, $_bIsLabelSet, strtolower( $this->getFirstElement( $this->aFieldTypeSlugs ) ) );

        // Output
        return
            $aField[ 'before_label' ]
            . "<div class='admin-page-framework-input-label-container admin-page-framework-input-container {$aField[ 'type' ]}-field'>" // image-field ( this will be media-field for the media field type )
                . "<label for='{$aField[ 'input_id' ]}'>"
                    . $aField[ 'before_input' ]
                    . $this->getAOrB(
                        $aField[ 'label' ] && ! $aField[ 'repeatable' ],
                        "<span " . $this->getLabelContainerAttributes( $aField, 'admin-page-framework-input-label-string' ) . ">"
                            . $aField[ 'label' ]
                        . "</span>",
                        ''
                    )
                    . "<input " . $this->getAttributes( $this->___getImageInputAttributes( $aField, $_iCountAttributes, $_sImageURL, $_aBaseAttributes ) ) . " />"
                    . $this->_getUploaderButtonHTML( $aField[ 'input_id' ], $_aUploadButtonAttributes, ! empty( $aField[ 'repeatable' ] ), $aField[ 'allow_external_source' ] )
                    . $this->_getRemoveButtonHTMLByType( $aField[ 'input_id' ], $_aRemoveButtonAttributes, strtolower( $this->getFirstElement( $this->aFieldTypeSlugs ) ) )
                    . $aField[ 'after_input' ]
                    . "<div class='repeatable-field-buttons'></div>" // the repeatable field buttons will be replaced with this element.
                    . $this->getExtraInputFields( $aField )
                . "</label>"
            . "</div>"
            . $aField[ 'after_label' ]
            . $this->_getPreviewContainer(
                $aField,
                $_sImageURL,
                // Preview container attributes
                $this->getElementAsArray( $aField, array( 'attributes', 'preview' ) )
                + $_aBaseAttributes
            );

    }
        /**
         * Returns a base attribute array.
         * @since       3.5.3
         * @return      array       The generated base attribute array.
         * @internal
         */
        private function ___getBaseAttributes( array $aField ) {

            $_aBaseAttributes   = $aField[ 'attributes' ] + array( 'class' => null );
            unset(
                $_aBaseAttributes[ 'input' ],
                $_aBaseAttributes[ 'button' ],
                $_aBaseAttributes[ 'preview' ],
                $_aBaseAttributes[ 'name' ],
                $_aBaseAttributes[ 'value' ],
                $_aBaseAttributes[ 'type' ],
                $_aBaseAttributes[ 'remove_button' ]
            );
            return $_aBaseAttributes;

        }
        /**
         * Returns the set image url.
         *
         * When the 'attributes_to_store' argument is present, there will be sub elements to the field value.
         * This method checks that and returns the set (stored) image url.
         *
         * This value will be used for the preview container as well.
         *
         * @since       3.5.3
         * @return      string      The found image url.
         * @internal
         */
        private function ___getTheSetImageURL( array $aField, $iCountAttributes ) {

            $_sCaptureAttribute = $this->getAOrB( $iCountAttributes, 'url', '' );
            return $_sCaptureAttribute
                ? $this->getElement( $aField, array( 'attributes', 'value', $_sCaptureAttribute ), '' )
                : $aField[ 'attributes' ][ 'value' ];


        }
        /**
         * Returns an image field input attribute for the url input tag.
         * @since       3.5.3
         * @return      array
         * @internal
         */
        private function ___getImageInputAttributes( array $aField, $iCountAttributes, $sImageURL, array $aBaseAttributes ) {

            return array(
                'name'              => $aField[ 'attributes' ][ 'name' ]
                    . $this->getAOrB( $iCountAttributes, '[url]', '' ),
                'value'             => $sImageURL,
                'type'              => 'text',

                // 3.4.2+ Referenced to bind an input update event to the preview updater script.
                'data-show_preview' => $aField[ 'show_preview' ],
            )
            + $aField[ 'attributes' ][ 'input' ]
            + $aBaseAttributes;

        }

        /**
         * Returns extra input fields to set capturing attributes.
         *
         * This adds input fields for saving extra attributes.
         * It overrides the name attribute of the default text field for URL and saves them as an array.
         *
         * @since       3.0.0
         * @return      string
         * @internal
         */
        protected function getExtraInputFields( array $aField ) {

            $_aOutputs = array();
            foreach( $this->getElementAsArray( $aField, 'attributes_to_store' ) as $sAttribute ) {
                $_aOutputs[] = "<input " . $this->getAttributes(
                    array(
                        'id'        => "{$aField[ 'input_id' ]}_{$sAttribute}",
                        'type'      => 'hidden',
                        'name'      => "{$aField[ '_input_name' ]}[{$sAttribute}]",
                        'disabled'  => $this->getAOrB(
                            isset( $aField[ 'attributes' ][ 'disabled' ] ) && $aField[ 'attributes' ][ 'disabled' ],
                            'disabled',
                            null
                        ),
                        'value'     => $this->getElement(
                            $aField,
                            array( 'attributes', 'value', $sAttribute ),
                            ''
                        ),
                    )
                ) . "/>";
            }
            return implode( PHP_EOL, $_aOutputs );

        }

        /**
         * Returns the output of the preview box.
         * @since   3.0.0
         * @internal
         */
        protected function _getPreviewContainer( $aField, $sImageURL, $aPreviewAtrributes ) {

            if ( ! $aField[ 'show_preview' ] ) {
                return '';
            }

            $sImageURL = esc_url( $this->getResolvedSRC( $sImageURL, true ) );
            return
                "<div " . $this->getAttributes(
                        array(
                            'id'    => "image_preview_container_{$aField[ 'input_id' ]}",
                            'class' => 'image_preview ' . $this->getElement( $aPreviewAtrributes, 'class', '' ),
                            'style' => $this->getAOrB( $sImageURL, '', "display: none; "  )
                                . $this->getElement( $aPreviewAtrributes, 'style', '' ),
                        ) + $aPreviewAtrributes
                    )
                . ">"
                    . "<img src='{$sImageURL}' "
                        . "id='image_preview_{$aField[ 'input_id' ]}' "
                    . "/>"
                . "</div>";

        }

        /**
         * A helper function for the above getImageInputTags() method to add a image button script.
         *
         * @since       2.1.3
         * @since       2.1.5   Moved from AdminPageFramework_FormField.
         * @since       3.2.0   Made it use dashicon for the select image button.
         * @remark      This class is extended by the media field type and this method will be overridden. So the scope needs to be protected rather than private.
         * @internal
         * @deprecated  3.9.0   Kept for backward compatibility.
         */
        protected function _getUploaderButtonScript( $sInputID, $abRepeatable, $bExternalSource, array $aButtonAttributes ) {

            $_bRepeatable     = ! empty( $abRepeatable );

            // Do not include the escaping character (backslash) in the heredoc variable declaration
            // because the minifier script will parse it and the <<<JAVASCRIPTS and JAVASCRIPTS; parts are converted to double quotes (")
            // which causes the PHP syntax error.
            $_sButtonHTML     = '"' . $this->_getUploaderButtonHTML( $sInputID, $aButtonAttributes, $_bRepeatable, $bExternalSource ) . '"';
            $_sRepeatable     = $this->getAOrB( $_bRepeatable, 'true', 'false' );
            $_bExternalSource = $this->getAOrB( $bExternalSource, 'true', 'false' );
            $_sScript = <<<JAVASCRIPTS
if ( 0 === jQuery( 'a#select_image_{$sInputID}' ).length ) {
    jQuery( 'input#{$sInputID}' ).after( $_sButtonHTML );
}
jQuery( document ).ready( function(){     
    setAdminPageFrameworkImageUploader( '{$sInputID}', 'true' === '{$_sRepeatable}', 'true' === '{$_bExternalSource}' );
});
JAVASCRIPTS;

            return "<script type='text/javascript' class='admin-page-framework-image-uploader-button'>"
                    . '/* <![CDATA[ */'
                    . $_sScript
                    . '/* ]]> */'
                . "</script>". PHP_EOL;

        }
            /**
             * Returns an HTML output of an uploader button.
             * @since  3.5.3
             * @since  3.9.0  Changed the visibility scope to protected from private to be overridden by extended classes.
             * @return string The generated HTML uploader button output.
             */
            protected function _getUploaderButtonHTML( $sInputID, array $aButtonAttributes, $bRepeatable, $bExternalSource ) {
                
                $_bIsLabelSet = isset( $aButtonAttributes[ 'data-label' ] ) && $aButtonAttributes[ 'data-label' ];
                $_aAttributes = $this->_getFormattedUploadButtonAttributes(
                    $sInputID,
                    $aButtonAttributes,
                    $_bIsLabelSet,
                    $bRepeatable,
                    $bExternalSource
                );
                return "<a " . $this->getAttributes( $_aAttributes ) . ">"
                        . ( $_bIsLabelSet
                            ? $_aAttributes[ 'data-label' ]
                            : ( strrpos( $_aAttributes[ 'class' ], 'dashicons' )
                                ? ''
                                : $this->oMsg->get( 'select_image' )
                            )
                        )
                    ."</a>";

            }
                /**
                 * Returns a formatted upload button attributes array.
                 * @since  3.5.3
                 * @since  3.9.0 Changed the visibility scope to protected from private to be overridden by extended classes.
                 * @return array The formatted upload button attributes array.
                 */
                protected function _getFormattedUploadButtonAttributes( $sInputID, array $aButtonAttributes, $_bIsLabelSet, $bRepeatable, $bExternalSource ) {
                    $_aAttributes           = array(
                            'id'        => "select_image_{$sInputID}",
                            'href'      => '#',
                            'data-input_id'                 => $sInputID,
                            'data-repeatable'               => ( string ) ( boolean ) $bRepeatable,
                            'data-uploader_type'            => ( string ) function_exists( 'wp_enqueue_media' ),
                            'data-enable_external_source'   => ( string ) ( boolean ) $bExternalSource, // ? 1 : 0,
                        )
                        + $aButtonAttributes
                        + array(
                            'title'     => $_bIsLabelSet
                                ? $aButtonAttributes[ 'data-label' ]
                                : $this->oMsg->get( 'select_image' ),
                            'data-label' => null,
                        );
                    $_aAttributes[ 'class' ]  = $this->getClassAttribute(
                        'select_image button button-small ',
                        $this->getAOrB(
                            trim( $aButtonAttributes[ 'class' ] ),
                            $aButtonAttributes[ 'class' ],
                            $this->getAOrB(
                                $_bIsLabelSet,
                                '',
                                $this->getAOrB(
                                    $bRepeatable,
                                    $this->___getDashIconSelectorsBySlug( 'images-alt2' ),
                                    $this->___getDashIconSelectorsBySlug( 'format-image' )
                                )
                            )
                        )
                    );
                    return $_aAttributes;
                }

        /**
         * Removes the set image values and attributes.
         *
         * @since       3.2.0
         * @since       3.5.3       Added the `$sType` parameter.
         * @return      string
         * @internal
         * @deprecatead 3.9.0  Currently, not used but Kept for backward compatibility.
         */
        protected function _getRemoveButtonScript( $sInputID, array $aButtonAttributes, $sType='image' ) {

            if ( ! function_exists( 'wp_enqueue_media' ) ) {
                return '';
            }

            // Do not include the escaping character (backslash) in the heredoc variable declaration
            // because the minifier script will parse it and the <<<JAVASCRIPTS and JAVASCRIPTS; parts are converted to double quotes (")
            // which causes the PHP syntax error.
            $_sButtonHTML  = '"' . $this->_getRemoveButtonHTMLByType( $sInputID, $aButtonAttributes, $sType ) . '"';
            $_sScript = <<<JAVASCRIPTS
                if ( 0 === jQuery( 'a#remove_{$sType}_{$sInputID}' ).length ) {
                    jQuery( 'input#{$sInputID}' ).after( $_sButtonHTML );
                }
JAVASCRIPTS;

            return "<script type='text/javascript' class='admin-page-framework-{$sType}-remove-button'>"
                    . '/* <![CDATA[ */'
                    . $_sScript
                    . '/* ]]> */'
                . "</script>". PHP_EOL;

        }

        /**
         * Returns an HTML output of a remove button.
         * @since       3.5.3
         * @return      string      The generated HTML remove button output.
         * @internal
         */
        protected function _getRemoveButtonHTMLByType( $sInputID, array $aButtonAttributes, $sType='image' ) {

            $_bIsLabelSet   = isset( $aButtonAttributes[ 'data-label' ] ) && $aButtonAttributes[ 'data-label' ];
            $_aAttributes   = $this->_getFormattedRemoveButtonAttributesByType( $sInputID, $aButtonAttributes, $_bIsLabelSet, $sType );
            return "<a " . $this->getAttributes( $_aAttributes ) . ">"
                    . ( $_bIsLabelSet
                        ? $_aAttributes[ 'data-label' ]
                        : $this->getAOrB(
                            strrpos( $_aAttributes[ 'class' ], 'dashicons' ),
                            '',
                            'x'
                        )
                    )
                . "</a>";

        }

            /**
             * Returns a formatted remove button attributes array.
             * @since       3.5.3
             * @return      array       The formatted remove button attributes array.
             * @internal
             */
            protected function _getFormattedRemoveButtonAttributesByType( $sInputID, array $aButtonAttributes, $_bIsLabelSet, $sType='image' ) {

                // $_sOnClickFunctionName  = 'removeInputValuesFor' . ucfirst( $sType );
                $_aAttributes           = array(
                        'id'            => "remove_{$sType}_{$sInputID}",
                        'href'          => '#',
                        'data-input_id' => $sInputID,
                        // @deprecated 3.9.0
                        // @todo update the Path custom field type that relies on this functionality
                        // 'onclick'   => esc_js( "{$_sOnClickFunctionName}( this ); return false;" ),
                    )
                    + $aButtonAttributes
                    + array(
                        'title' => $_bIsLabelSet
                            ? $aButtonAttributes[ 'data-label' ]
                            : $this->oMsg->get( 'remove_value' ),
                    );
                $_aAttributes[ 'class' ]  = $this->getClassAttribute(
                    "remove_value remove_{$sType} button button-small",
                    $this->getAOrB(
                        trim( $aButtonAttributes[ 'class' ] ),
                        $aButtonAttributes[ 'class' ],
                        $this->getAOrB(
                            $_bIsLabelSet,
                            '',
                            $this->___getDashIconSelectorsBySlug( 'dismiss' )
                        )
                    )
                );
                return $_aAttributes;

            }

        /**
         * Returns a set of dash-icon selectors by the given dash-icon slug.
         *
         * It checks whether the WordPress version is enough to support dash-icons.
         *
         * @since       3.5.3
         * @return      string      The generated class selectors.
         * @internal
         */
        private function ___getDashIconSelectorsBySlug( $sDashIconSlug ) {

            static $_bDashIconSupported;

            $_bDashIconSupported = isset( $_bDashIconSupported )
                ? $_bDashIconSupported
                : version_compare( $GLOBALS[ 'wp_version' ], '3.8', '>=' );

            return $this->getAOrB(
                $_bDashIconSupported,
                "dashicons dashicons-{$sDashIconSlug}",
                ''
            );

        }

}