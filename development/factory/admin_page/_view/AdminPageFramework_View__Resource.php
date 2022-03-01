<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Enqueues page resources set with the `style` and `script` arguments.
 *
 * @abstract
 * @since           3.6.3
 * @package         AdminPageFramework/Factory/AdminPage/View
 * @internal
 * @extends         AdminPageFramework_FrameworkUtility
 */
class AdminPageFramework_View__Resource extends AdminPageFramework_FrameworkUtility {

    public $oFactory;
    public $sCurrentPageSlug;
    public $sCurrentTabSlug;

    public $aCSSRules = array();
    public $aScripts  = array();

    /**
     * Sets up properties.
     * @since       3.6.3
     */
    public function __construct( $oFactory ) {

        $this->oFactory         = $oFactory;
        $this->sCurrentPageSlug = $oFactory->oProp->getCurrentPageSlug();
        $this->sCurrentTabSlug  = $oFactory->oProp->getCurrentTabSlug( $this->sCurrentPageSlug );

        $this->_parseAssets( $oFactory );
        $this->_setHooks();

    }
        /**
         * Sets up hooks.
         * @since       3.6.3
         * @return      void
         */
        private function _setHooks() {
            add_action( "style_{$this->sCurrentPageSlug}", array( $this, '_replyToAddInternalCSSRules' ) );
            if ( $this->sCurrentTabSlug ) {
                add_action( "style_{$this->sCurrentPageSlug}_{$this->sCurrentTabSlug}", array( $this, '_replyToAddInternalCSSRules' ) );
            }
            add_action( "script_{$this->sCurrentPageSlug}", array( $this, '_replyToAddInternalScripts' ) );
            if ( $this->sCurrentTabSlug ) {
                add_action( "script_{$this->sCurrentPageSlug}_{$this->sCurrentTabSlug}", array( $this, '_replyToAddInternalScripts' ) );
            }
        }
            public function _replyToAddInternalCSSRules( $sCSS ) {
                return $this->_appendInternalAssets( $sCSS, $this->aCSSRules );
            }
            public function _replyToAddInternalScripts( $sScript ) {
                return $this->_appendInternalAssets( $sScript, $this->aScripts );
            }
                /**
                 * Appends internal contents (script/CSS) to the given value and updates the container array.
                 * @return      string
                 * @since       3.6.3
                 */
                public function _appendInternalAssets( $sInternal, &$aContainer ) {
                    $_aInternals = array_unique( $aContainer );
                    $sInternal   = PHP_EOL . $sInternal;
                    foreach( $_aInternals as $_iIndex => $_sInternal ) {
                        $sInternal .= $_sInternal . PHP_EOL;
                        unset( $_aInternals[ $_iIndex ] );
                    }
                    $aContainer = $_aInternals; // update the container array.
                    return $sInternal;
                }

        /**
         * @since       3.6.3
         * @return      void
         */
        private function _parseAssets( $oFactory ) {

            // page
            $_aPageStyles      = $this->getElementAsArray(
                $oFactory->oProp->aPages,
                array( $this->sCurrentPageSlug, 'style' )
            );
            $this->_enqueuePageAssets( $_aPageStyles, 'style' );

            $_aPageScripts     = $this->getElementAsArray(
                $oFactory->oProp->aPages,
                array( $this->sCurrentPageSlug, 'script' )
            );
            $this->_enqueuePageAssets( $_aPageScripts, 'script' );

            // In-page tabs
            if ( ! $this->sCurrentTabSlug ) {
                return;
            }
            $_aInPageTabStyles  = $this->getElementAsArray(
                $oFactory->oProp->aInPageTabs,
                array( $this->sCurrentPageSlug, $this->sCurrentTabSlug, 'style' )
            );
            $this->_enqueuePageAssets( $_aInPageTabStyles, 'style' );

            $_aInPageTabScripts = $this->getElementAsArray(
                $oFactory->oProp->aInPageTabs,
                array( $this->sCurrentPageSlug, $this->sCurrentTabSlug, 'script' )
            );
            $this->_enqueuePageAssets( $_aInPageTabScripts, 'script' );

        }
            /**
             * @since       3.6.3
             * @return      void
             */
            private function _enqueuePageAssets( array $aPageAssets, $sType='style' ) {
                $_sMethodName = "_enqueueAsset_" . $sType;
                foreach( $aPageAssets as $_asPageAsset ) {
                    $this->{$_sMethodName}( $_asPageAsset);
                }
            }

                /**
                 * @since       3.6.3
                 * @return      void
                 */
                private function _enqueueAsset_style( $asPageStyle ) {

                    $_oFormatter = new AdminPageFramework_Format_PageResource_Style( $asPageStyle );
                    $_aPageStyle = $_oFormatter->get();
                    $_sSRC       = $_aPageStyle[ 'src' ];

                    // At this point, it may be a url/path or a text CSS rules.
                    if ( file_exists( $_sSRC ) || filter_var( $_sSRC, FILTER_VALIDATE_URL ) ) {
                        return $this->oFactory->enqueueStyle(
                            $_sSRC,
                            $this->sCurrentPageSlug,
                            $this->sCurrentTabSlug, // tab slug
                            $_aPageStyle
                        );
                    }

                    // Insert the CSS rule in the head tag.
                    $this->aCSSRules[] = $_sSRC;

                }

                /**
                 * @since       3.6.3
                 * @return      void
                 */
                private function _enqueueAsset_script( $asPageScript ) {

                    $_oFormatter  = new AdminPageFramework_Format_PageResource_Script( $asPageScript );
                    $_aPageScript = $_oFormatter->get();
                    $_sSRC        = $_aPageScript[ 'src' ];

                    // At this point, it may be a url/path or a text CSS rules.
                    if ( $this->isResourcePath( $_sSRC ) ) {
                        return $this->oFactory->enqueueScript(
                            $_sSRC,
                            $this->sCurrentPageSlug,
                            $this->sCurrentTabSlug, // tab slug
                            $_aPageScript
                        );
                    }

                    // Insert the scripts in the head tag.
                    $this->aScripts[] = $_sSRC;

                }

}
