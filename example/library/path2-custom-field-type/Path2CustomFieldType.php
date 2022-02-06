<?php
/**
 * Admin Page Framework
 *
 * Facilitates WordPress plugin and theme development.
 *
 * @author      Michael Uno <michael@michaeluno.jp>
 * @copyright   2013-2021 (c) Michael Uno
 * @license     MIT <http://opensource.org/licenses/MIT>
 * @package     AdminPageFramework
 */

if ( ! class_exists( 'Path2CustomFieldType' ) ) :
/**
 * A field type that lets the user pick a file located on the server.
 * 
 * @since       3.9.0
 * @version     0.0.1
 */
class Path2CustomFieldType extends AdminPageFramework_FieldType_image {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'path2', );

    /**
     * Defines the default key-values of this field type settings.
     *
     * @remark\ $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'attributes'    =>  array(
            'input'         => array(),
            'remove_button' => array(),
            'select_button' => array(),
        ),
        /**
         * @see https://github.com/vakata/jstree/wiki#more-on-configuration
         */
        'options'   => array(
            'root'              => '',    // (string) root folder to display, relative to the web document root, set to $_SERVER[ 'DOCUMENT_ROOT' ] or an absolute path.
            'onlyFolders'       => false, // (boolean|string) Filter files and only return folders
            'onlyFiles'         => false, // (boolean|string) Filter folders and only return files
            'fileExtensions'    => '',    // (string) file extensions to be listed without a dot, separated with commas. e.g. php,txt,js
        ),
    );
    
    protected function construct() {
        // wp_ajax_{action name}
        // This is a dummy callback. Adding a dummy callback because WordPress does not proceed in admin-ajax.php
        // and the `admin_init` action is not triggered if no `wp_ajax_{...}` action is registered.
        add_action( 'wp_ajax_apf_path2_field_type-admin-page-framework' , '__return_empty_string' );
    }

    /**
     * Loads the field type necessary components.
     */
    public function setUp() {
        add_thickbox();
    }
            
    /**
     * Returns an array holding the urls of enqueuing scripts.
     * @return array
     */
    protected function getEnqueuingScripts() {
        $_sNonce = wp_create_nonce( get_class( $this ) );
        return array(
            array(
                'handle_id'       => 'jstree',
                'src'             => dirname( __FILE__ ) . '/asset/jstree/jstree.js',
                'dependencies'    => array( 'jquery' )
            ),
            array(
                'handle_id'       => 'path2-initializer',
                'src'             => dirname( __FILE__ ) . '/asset/js/path2-initializer.js',
                'dependencies'    => array( 'jstree' ),
                'translation'    => array(
                    'ajaxURL'   => admin_url( 'admin-ajax.php' ),
                    'nonce'     => $_sNonce,
                    'label'     => array(
                        'selectPath' => __( 'Select Path', 'admin-page-framework' ),
                        'select'     => __( 'Select', 'admin-page-framework' ),
                    ),
                ),
                'translation_var' => 'AdminPageFrameworkPath2FieldType',
            ),
        );
    }

    /**
     * @return array
     */
    protected function getEnqueuingStyles() {
        return array(
            dirname( __FILE__ ) . '/asset/css/style.css',
            dirname( __FILE__ ) . '/asset/jstree/themes/default/style.css',
        );
    }

    /**
     * Returns the output of the field type.
     */
    public function getField( $aField ) {
        
        $_sPath             = $this->getElement( $aField, array( 'attributes', 'value' ), '' );
        $_aBaseAttributes   = $this->___getBaseAttributes( $aField );
    
        return $aField[ 'before_label' ]
            . "<div class='admin-page-framework-input-label-container admin-page-framework-input-container {$aField[ 'type' ]}-field'>" 
                . "<label for='{$aField[ 'input_id' ]}'>"
                    . $aField[ 'before_input' ]
                    . $this->getAOrB(
                        $aField[ 'label' ] && ! $aField[ 'repeatable' ],
                        "<span " . $this->getLabelContainerAttributes( $aField, 'admin-page-framework-input-label-string' ) . ">" 
                            . $aField[ 'label' ] 
                        . "</span>",                        
                        ''                        
                    )
                    . "<input " . $this->getAttributes( $this->___getPathInputAttributes( $aField, $_sPath, $_aBaseAttributes ) ) . " />"
                    . $this->___getSelectButtonHTML( $aField[ 'input_id' ], $this->getElementAsArray( $aField, array( 'attributes', 'select_button' ) ) + $_aBaseAttributes )
                    . $this->___getRemoveButtonHTML( $aField[ 'input_id' ], $this->getElementAsArray( $aField, array( 'attributes', 'remove_button' ) ) + $_aBaseAttributes, 'path2' )
                    . $aField[ 'after_input' ]
                    . "<div class='repeatable-field-buttons'></div>" 
                . "</label>"
            . "</div>"     
            . $aField[ 'after_label' ]
            . $this->___getModalContent( $aField[ 'input_id' ], $aField[ 'options' ] )
            ;

    }
        /**
         * Returns a base attribute array.
         * @since       3.8.4
         * @since       3.9.0       Moved from `PathCustomFieldType`.
         * @param       array       $aField
         * @return      array       The generated base attribute array.
         * @internal
         */
        private function ___getBaseAttributes( $aField ) {
            $_aBaseAttributes   = $aField[ 'attributes' ] + array( 'class' => null );
            unset(
                $_aBaseAttributes[ 'input' ],
                $_aBaseAttributes[ 'select_button' ],
                $_aBaseAttributes[ 'name' ],
                $_aBaseAttributes[ 'value' ],
                $_aBaseAttributes[ 'type' ],
                $_aBaseAttributes[ 'remove_button' ]
            );
            return $_aBaseAttributes;
        }

        private function ___getPathInputAttributes( $aField, $sPath, $aBaseAttributes ) {
            return array(
                'name'              => $aField[ 'attributes' ][ 'name' ],
                'value'             => $sPath,
                'type'              => 'text',
            )
                + $this->getElementAsArray( $aField, array( 'attributes', 'input' ) )
                + $aBaseAttributes;

        }

        /**
         * Returns an HTML output of a remove button.
         * @since  3.9.0
         * @return string      The generated HTML remove button output.
         */
        private function ___getRemoveButtonHTML( $sInputID, array $aButtonAttributes, $sType='path2' ) {

            $_bIsLabelSet   = isset( $aButtonAttributes[ 'data-label' ] ) && $aButtonAttributes[ 'data-label' ];
            $_aAttributes   = $this->_getFormattedRemoveButtonAttributesByType( $sInputID, $aButtonAttributes, $_bIsLabelSet, $sType );
            $_aAttributes[ 'class' ]  = $this->getClassAttribute( 'remove_path', $_aAttributes[ 'class' ] );
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
         * Returns an HTML output of a select button.
         * @since  3.8.4
         * @since  3.9.0  Moved from `PathCustomFieldType`.
         * @param  string $sInputID
         * @param  array  $aButtonAttributes
         * @return string The generated HTML uploader button output.
         */
        private function ___getSelectButtonHTML( $sInputID, array $aButtonAttributes ) {

            $_bIsLabelSet = isset( $aButtonAttributes[ 'data-label' ] ) && $aButtonAttributes[ 'data-label' ];
            $_aAttributes = $this->___getFormattedSelectButtonAttributes(
                $sInputID,
                $aButtonAttributes,
                $_bIsLabelSet
            );
            return "<a " . $this->getAttributes( $_aAttributes ) . ">"
                    . $this->getAOrB(
                        $_bIsLabelSet,
                        $_aAttributes[ 'data-label' ],
                        $this->getAOrB(
                            strrpos( $_aAttributes[ 'class' ], 'dashicons' ),
                            '',
                            __( 'Select Path', 'admin-page-framework' )
                        )
                    )
                ."</a>";

        }
            /**
             * Returns a formatted upload button attributes array.
             * @since  3.8.4
             * @since  3.9.0   Moved from `PathCustomFieldType`.
             * @param  string  $sInputID
             * @param  array   $aButtonAttributes
             * @param  boolean $_bIsLabelSet
             * @return array   The formatted upload button attributes array.
             */
            private function ___getFormattedSelectButtonAttributes( $sInputID, array $aButtonAttributes, $_bIsLabelSet ) {

                $_aAttributes           = array(
                        'id'        => "select_path_{$sInputID}",
                        'data-input_id'   => $sInputID,
                    )
                    + $aButtonAttributes
                    + array(
                        'title'     => $_bIsLabelSet
                            ? $aButtonAttributes[ 'data-label' ]
                            : __( 'Select Path', 'admin-page-framework' ),
                        'data-label' => null,
                    );
                $_aAttributes[ 'class' ]  = $this->getClassAttribute(
                    'select_path button-select-path2 button button-small ',
                    $this->getAOrB(
                        trim( $aButtonAttributes[ 'class' ] ),
                        $aButtonAttributes[ 'class' ],
                        $this->getAOrB(
                            ! $_bIsLabelSet && version_compare( $GLOBALS[ 'wp_version' ], '3.8', '>=' ),
                            'dashicons dashicons-portfolio',
                            ''
                        )
                    )
                );
                return $_aAttributes;

            }

        /**
         * @param  string $sInputID
         * @param  array  $aOptions
         * @return string
         */
        private function ___getModalContent( $sInputID, $aOptions ) {
            $_sAttributesContainer = $this->getAttributes(
                array(
                    'id'            => "path_selector_{$sInputID}",
                    'class'         => 'jstree-path-modal',
                    'data-input_id' => $sInputID,
                    'style'         => 'display: none;'
                )
            );
            $aOptions[ 'fileExtensions' ] = is_array( $aOptions[ 'fileExtensions' ] ) ? implode( ',', $aOptions[ 'fileExtensions' ] ) : $aOptions[ 'fileExtensions' ];
            $_sAttributesTree     = $this->getAttributes( $this->getDataAttributeArray( $aOptions ) );
            return "<div {$_sAttributesContainer}>"
                    . "<span class='path2-field-options' {$_sAttributesTree}></span>"
                    . "<div class='path2-node-tree'></div>"
                . "</div>";
        }

    /**
     * Calls back the callback function if it is set.
     *
     * Called when the field type is registered.
     */
    protected function doOnFieldRegistration( $aFieldset ) {
        $this->___handleQuery();
    }
        /**
         * @since  3.9.0
         */
        private function ___handleQuery() {
            if ( empty( $_POST[ 'admin-page-framework_path2_field_type' ] ) ||  empty( $_POST[ 'nonce' ] ) ) {
                return;
            }
            if ( ! wp_verify_nonce( $_POST[ 'nonce' ], get_class( $this ) ) ) {
                exit();   // silence is golden
            }
            $_aPath2Options = $this->getArrayMappedRecursive( 'sanitize_text_field', $this->getElementAsArray( $_POST, array( 'options' ) ) );
            $_sNodeID       = sanitize_text_field( $_POST[ 'id' ] );
            $_sPath         = empty( $_POST[ 'id' ] ) || '#' === $_sNodeID
                ? $this->___getRootDirectoryPath( $this->getElement( $_aPath2Options,  array( 'root' ), '' ) )
                : $_sNodeID;
            $_oTreeNode     = new Path2CustomFieldType_Node( $_sPath, $_aPath2Options );
            $_aTreeData     = $_oTreeNode->get();
            wp_send_json( $_aTreeData );
        }

            /**
             * @since  3.9.0
             * @param  string $sUserSetPath
             * @return string
             */
            private function ___getRootDirectoryPath( $sUserSetPath ) {

                $_sServerDocumentRoot = wp_normalize_path( sanitize_text_field( $_SERVER[ 'DOCUMENT_ROOT' ] ) );
                if ( empty( $sUserSetPath ) || '/' === $sUserSetPath || '\\' === $sUserSetPath ) {
                    return $_sServerDocumentRoot;
                }

                // If an absolute path is given, use it.
                if ( file_exists( $sUserSetPath ) && is_dir( $sUserSetPath ) ) {
                    return wp_normalize_path( untrailingslashit( $sUserSetPath ) );
                }

                // At this point, it is assumed that the given value is a relative path to the document root.
                return untrailingslashit( wp_normalize_path( trailingslashit( $_sServerDocumentRoot ) . ltrim( $sUserSetPath ), '\\/' ) );

            }

}
endif;