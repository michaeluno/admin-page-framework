<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to build forms.
 *
 * This is a delegation class of `AdminPageFramework_Form_View`.
 *
 * @package     AdminPageFramework/Common/Form/View/Resource
 * @since       3.7.0
 * @extends     AdminPageFramework_FrameworkUtility
 * @internal
 */
class AdminPageFramework_Form_View__Resource extends AdminPageFramework_FrameworkUtility {

    /**
     * Stores the form object to let this class access the resource array.
     */
    public $oForm;

    /**
     * Sets up hooks.
     * @since       3.7.0
     */
    public function __construct( $oForm ) {

        $this->oForm = $oForm;

        // If it is loaded in the background, no need to load scripts and styles.
        if ( $this->isDoingAjax() ) {
            return;
        }

        // Widgets can be called multiple times for the number of user-created widget instances for one class instance
        // so make sure it is processed only once per page.
        if ( $this->hasBeenCalled( 'resource_' . $oForm->aArguments[ 'caller_id' ] ) ) {
             return;
        }

        $this->___setHooks();

    }

        /**
         * @since       3.7.0
         */
        private function ___setHooks() {

            if ( is_admin() ) {
                $this->___setAdminHooks();
                return;
            }

            // Hook the admin header to insert custom admin stylesheets and scripts.
            add_action( 'wp_enqueue_scripts', array( $this, '_replyToEnqueueScripts' ) );
            add_action( 'wp_enqueue_scripts', array( $this, '_replyToEnqueueStyles' ) );

            /// A low priority is required to let dependencies loaded fast especially in customizer.php.
            add_action( did_action( 'wp_print_styles' ) ? 'wp_print_footer_scripts' : 'wp_print_styles', array( $this, '_replyToAddStyle' ), 999 );
            // add_action( did_action( 'wp_print_scripts' ) ? 'wp_print_footer_scripts' : 'wp_print_scripts', array( $this, '_replyToAddScript' ), 999 ); // @deprecated 3.8.11 All the added scripts should be loaded in the footer.

            // Take care of items that could not be added in the head tag.

            /// For admin pages other than wp-admin/customizer.php
            add_action( 'wp_footer', array( $this, '_replyToEnqueueScripts' ) );
            add_action( 'wp_footer', array( $this, '_replyToEnqueueStyles' ) );

            /// For all admin pages.
            add_action( 'wp_print_footer_scripts', array( $this, '_replyToAddStyle' ), 999 );
            add_action( 'wp_print_footer_scripts', array( $this, '_replyToAddScript' ), 999 );

            // Required scripts in the head tag.
            new AdminPageFramework_Form_View__Resource__Head( $this->oForm, 'wp_head' );

        }
            private function ___setAdminHooks() {

                // Hook the admin header to insert custom admin stylesheets and scripts.
                add_action( 'admin_enqueue_scripts', array( $this, '_replyToEnqueueScripts' ) );
                add_action( 'admin_enqueue_scripts', array( $this, '_replyToEnqueueStyles' ) );

                add_action( did_action( 'admin_print_styles' ) ? 'admin_print_footer_scripts' : 'admin_print_styles', array( $this, '_replyToAddStyle' ), 999 );
                // add_action( did_action( 'admin_print_scripts' ) ? 'admin_print_footer_scripts' : 'admin_print_scripts', array( $this, '_replyToAddScript' ), 999 ); // @deprecated 3.8.11 All the added scripts should be loaded in the footer.

                // Take care of items that could not be added in the head tag.
                /// For wp-admin/customizer.php
                add_action( 'customize_controls_print_footer_scripts', array( $this, '_replyToEnqueueScripts' ) );
                add_action( 'customize_controls_print_footer_scripts', array( $this, '_replyToEnqueueStyles' ) );

                /// For admin pages other than wp-admin/customizer.php
                add_action( 'admin_footer', array( $this, '_replyToEnqueueScripts' ) );
                add_action( 'admin_footer', array( $this, '_replyToEnqueueStyles' ) );

                /// For all admin pages.
                add_action( 'admin_print_footer_scripts', array( $this, '_replyToAddStyle' ), 999 );
                add_action( 'admin_print_footer_scripts', array( $this, '_replyToAddScript' ), 999 );

                // Required scripts in the head tag.
                new AdminPageFramework_Form_View__Resource__Head( $this->oForm, 'admin_head' );

            }

    /**
     * Enqueues page script resources.
     *
     * @since       3.7.0
     */
    public function _replyToEnqueueScripts() {
        if ( ! $this->oForm->isInThePage() ) {
            return;
        }
        foreach( $this->oForm->getResources( 'src_scripts' ) as $_isIndex => $_asEnqueue ) {
            $this->___enqueueScript( $_asEnqueue );
            $this->oForm->unsetResources( array( 'src_scripts', $_isIndex ) );  // no longer needed
        }
    }
        /**
         * Stores flags of enqueued items.
         * @since       3.7.0
         */
        static private $_aEnqueued = array();
        /**
         * @return      void
         * @since       3.7.0
         */
        private function ___enqueueScript( $asEnqueue ) {

            $_aEnqueueItem = $this->___getFormattedEnqueueScript( $asEnqueue );

            // Do not load the same items multiple times.
            if ( isset( self::$_aEnqueued[ $_aEnqueueItem[ 'src' ] ] ) ) {
                return;
            }
            self::$_aEnqueued[ $_aEnqueueItem[ 'src' ] ] = $_aEnqueueItem;

            wp_enqueue_script(
                $_aEnqueueItem[ 'handle_id' ],
                $_aEnqueueItem[ 'src' ],
                $_aEnqueueItem[ 'dependencies' ],
                $_aEnqueueItem[ 'version' ],
                did_action( 'admin_body_class' ) || ( boolean ) $_aEnqueueItem['in_footer']
            );
            if ( $_aEnqueueItem[ 'translation' ] ) {
                wp_localize_script(
                    $_aEnqueueItem[ 'handle_id' ],
                    $_aEnqueueItem[ 'handle_id' ],
                    $_aEnqueueItem[ 'translation' ]
                );
            }

        }
            /**
             * @return      array
             * @since       3.7.0
             */
            private function ___getFormattedEnqueueScript( $asEnqueue ) {
                static $_iCallCount = 1;
                $_aEnqueueItem = $this->getAsArray( $asEnqueue ) + array(
                    'handle_id'     => 'script_form_' . $this->oForm->aArguments[ 'caller_id' ] . '_' . $_iCallCount,
                    'src'           => null,
                    'dependencies'  => null,
                    'version'       => null,
                    'in_footer'     => false,
                    'translation'   => null,
                );
                if ( is_string( $asEnqueue ) ) {
                    $_aEnqueueItem[ 'src' ] = $asEnqueue;
                }
                $_aEnqueueItem[ 'src' ] = $this->___getSRCFormatted( $_aEnqueueItem );
                $_iCallCount++;
                return $_aEnqueueItem;
            }


    /**
     * Enqueues page stylesheet resources.
     *
     * @since       3.7.0
     */
    public function _replyToEnqueueStyles() {

        if ( ! $this->oForm->isInThePage() ) {
            return;
        }
        foreach( $this->oForm->getResources( 'src_styles' ) as $_isIndex => $_asEnqueueItem ) {
            $this->___enqueueStyle( $_asEnqueueItem );
            $this->oForm->unsetResources( array( 'src_styles', $_isIndex ) ); // no longer needed
        }

    }
        private function ___enqueueStyle( $asEnqueue ) {
            $_aEnqueueItem = $this->___getFormattedEnqueueStyle( $asEnqueue );
            wp_enqueue_style(
                $_aEnqueueItem[ 'handle_id' ],
                $_aEnqueueItem[ 'src' ],
                $_aEnqueueItem[ 'dependencies' ],
                $_aEnqueueItem[ 'version' ],
                $_aEnqueueItem[ 'media' ]
            );
        }
            /**
             * @return      array
             */
            private function ___getFormattedEnqueueStyle( $asEnqueue ) {
                static $_iCallCount = 1;
                $_aEnqueueItem = $this->getAsArray( $asEnqueue ) + array(
                    'handle_id'     => 'style_form_' . $this->oForm->aArguments[ 'caller_id' ] . '_' . $_iCallCount,
                    'src'           => null,
                    'dependencies'  => null,
                    'version'       => null,
                    'media'         => null,
                );
                if ( is_string( $asEnqueue ) ) {
                    $_aEnqueueItem[ 'src' ] = $asEnqueue;
                }
                $_aEnqueueItem[ 'src' ] = $this->___getSRCFormatted( $_aEnqueueItem );
                $_iCallCount++;
                return $_aEnqueueItem;
            }

            /**
             * Formats the SRC value.
             *
             * Also, adds the ability to auto-load a .min file if exists when a path is given (not url).
             *
             * @param   array $aEnqueueItem
             * @return  string
             * @since   3.8.31
             * @remark  This is identical to the AdminPageFramework_Resource_Base::___getSRCFormatted() method.
             * @see     AdminPageFramework_Resource_Base::___getSRCFormatted()
             */
            private function ___getSRCFormatted( array $aEnqueueItem ) {
                $_sSRCRaw = wp_normalize_path( $aEnqueueItem[ 'src' ] );
                $_sSRC    = $this->getResolvedSRC( $_sSRCRaw ); // at this point, it is a URL

                if ( ! $this->oForm->aArguments[ 'autoload_min_resource' ] ) {
                    return $_sSRC;
                }

                // If the site debug mode is on, use the one that user gave.
                if ( $this->isDebugMode() ) {
                    return $_sSRC;
                }

                // If the user gave a url, use it.
                if ( $this->isURL( $_sSRCRaw ) ) {
                    return $_sSRC;
                }

                // At this point, the user gave a path.
                $_sMinPrefix = '.min';

                // If the user already handles a min version, then use it.
                if ( false !== stripos( $_sSRC, $_sMinPrefix ) ) {
                    return $_sSRC;
                }

                $_aPathParts = pathinfo( $_sSRCRaw )
                    + array( 'dirname' => '', 'filename' => '', 'basename' => '', 'extension' => '' ); // avoid undefined index warnings

                // If there is no extension, avoid using a minified version.
                if ( ! $_aPathParts[ 'extension' ] ) {
                    return $_sSRC;
                }

                $_aPathPartsURL = pathinfo( $_sSRC )
                    + array( 'dirname' => '', 'filename' => '', 'basename' => '', 'extension' => '' ); // avoid undefined index warnings

                $_sPathMinifiedVersion = $_aPathParts[ 'dirname' ] . '/' . $_aPathParts[ 'filename' ] . $_sMinPrefix . '.' . $_aPathParts[ 'extension' ];
                return file_exists( $_sPathMinifiedVersion )
                    ? $_aPathPartsURL[ 'dirname' ] . '/' . $_aPathPartsURL[ 'filename' ] . $_sMinPrefix . '.' . $_aPathPartsURL[ 'extension' ]
                    : $_sSRC;

            }

    /**
     * Enqueues inline styles.
     *
     * @since       3.7.0
     * @callback    action      wp_print_footer_scripts
     * @callback    action      wp_print_styles
     * @return      void
     */
    public function _replyToAddStyle() {

        if ( ! $this->oForm->isInThePage() ) {
            return;
        }
        $_sCSSRules = $this->___getFormattedInternalStyles(
            $this->oForm->getResources( 'internal_styles' )
        );

        $_sID = $this->sanitizeSlug( strtolower( $this->oForm->aArguments[ 'caller_id' ] ) );
        if ( $_sCSSRules ) {
            echo "<style type='text/css' id='internal-style-{$_sID}' class='admin-page-framework-form-style'>"
                    . $_sCSSRules
                . "</style>";
        }
        $_sIECSSRules = $this->___getFormattedInternalStyles(
            $this->oForm->getResources( 'internal_styles_ie' )
        );
        if ( $_sIECSSRules ) {
            echo "<!--[if IE]><style type='text/css' id='internal-style-ie-{$_sID}' class='admin-page-framework-form-ie-style'>"
                    . $_sIECSSRules
                . "</style><![endif]-->";
        }

        // Empty the values as this method can be called multiple times, in the head tag and the footer.
        $this->oForm->setResources( 'internal_styles', array() );
        $this->oForm->setResources( 'internal_styles_ie', array() );

    }
        /**
         * @since       3.7.0
         * @string
         */
        private function ___getFormattedInternalStyles( array $aInternalStyles ) {
            return implode( PHP_EOL, array_unique( $aInternalStyles ) );
        }

    /**
     * Enqueues page inline scripts.
     *
     * @since       3.7.0
     */
    public function _replyToAddScript() {

        if ( ! $this->oForm->isInThePage() ) {
            return;
        }

        $_sScript = implode( PHP_EOL, array_unique( $this->oForm->getResources( 'internal_scripts' ) ) );
        if ( $_sScript ) {
            $_sID = $this->sanitizeSlug( strtolower( $this->oForm->aArguments[ 'caller_id' ] ) );
            echo "<script type='text/javascript' id='internal-script-{$_sID}' class='admin-page-framework-form-script'>"
                    . '/* <![CDATA[ */'
                    . $_sScript
                    . '/* ]]> */'
                . "</script>";
        }
        $this->oForm->setResources( 'internal_scripts', array() );

    }

}
