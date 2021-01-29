<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods for HTML link elements.
 *
 * @abstract
 * @since       2.0.0
 * @extends     AdminPageFramework_FrameworkUtility
 * @package     AdminPageFramework/Common/Factory/Link
 * @internal
 */
abstract class AdminPageFramework_Link_Base extends AdminPageFramework_FrameworkUtility {

    public $oProp;

    public $oMsg;

    /**
     * Sets up hooks and properties.
     */
    public function __construct( $oProp, $oMsg=null ) {

        if ( ! $this->_isLoadable( $oProp ) ) {
            return;
        }

        $this->oProp    = $oProp;
        $this->oMsg     = $oMsg;

        add_action( 'in_admin_footer', array( $this, '_replyToSetFooterInfo' ) );

        // Add an action link in the plugin listing page
        if ( $this->_shouldSetPluginActionLinks() ) {
            add_filter(
                'plugin_action_links_' . plugin_basename( $this->oProp->aScriptInfo[ 'sPath' ] ),
                array( $this, '_replyToAddSettingsLinkInPluginListingPage' ),
                20     // set a lower priority so that the link will be embedded at the beginning ( the most left hand side ).
            );
        }

    }

        /**
         * Determines if instantiating the class should be performed or not.
         *
         * @since       3.5.5
         * @return      boolean
         */
        private function _isLoadable( $oProp ) {
            if ( ! $oProp->bIsAdmin ) {
                return false;
            }
            if ( $oProp->bIsAdminAjax ) {
                return false;
            }
            return ! $this->hasBeenCalled( 'links_' . $oProp->sClassName );
        }

    /**
     * Modifies the action link of the plugin title column in the plugin listing page (plugins.php).
     *
     * @remark      This method should be overridden by the extended class.
     * @callback    filter      plugin_action_links_{plugin base name}
     * @return      array
     * @since       3.8.2
     * @internal
     */
    public function _replyToAddSettingsLinkInPluginListingPage( $aLinks ) {
        return $aLinks;
    }

    /**
     * Checks whether it is okay to set up action links in the plugin listing page (plugins.php).
     * @since       3.8.0
     * @return      boolean
     */
    protected function _shouldSetPluginActionLinks() {

        // It is possible that the sub-objects are not set when the class is considered not loadable.
        if ( ! isset( $this->oProp ) ) {
            return false;
        }

        if ( ! in_array( $this->oProp->sPageNow, array( 'plugins.php' ) ) ) {
            return false;
        }
        return 'plugin' === $this->oProp->aScriptInfo[ 'sType' ];

    }

    /**
     * Sets up footer information.
     *
     * @since           3.5.5
     * @callback        action      in_admin_footer
     */
    public function _replyToSetFooterInfo() {

        $this->_setDefaultFooterText();
        $this->_setFooterHooks();

    }

        /**
         * Set the default footer text values.
         * @internal
         * @since       3.5.5
         * @return      void
         */
        protected function _setDefaultFooterText() {

            $this->oProp->aFooterInfo[ 'sLeft' ] = str_replace(
                '__SCRIPT_CREDIT__',
                $this->_getFooterInfoLeft( $this->oProp->aScriptInfo ),
                $this->oProp->aFooterInfo[ 'sLeft' ]
            );
            $this->oProp->aFooterInfo[ 'sRight' ] = str_replace(
                '__FRAMEWORK_CREDIT__',
                $this->_getFooterInfoRight( $this->oProp->_getLibraryData() ),
                $this->oProp->aFooterInfo[ 'sRight' ]
            );

        }
            /**
             * Sets the default footer text on the left hand side.
             *
             * @since       2.1.1
             * @since       3.5.5       Changed the name from `_setFooterInfoLeft()` and dropped the second parameter.
             * @return      string
             */
            private function _getFooterInfoLeft( $aScriptInfo ) {

                $_sDescription = $this->getAOrB(
                    empty( $aScriptInfo[ 'sDescription' ] ),
                    '',
                    "&#13;{$aScriptInfo[ 'sDescription' ]}"
                );
                $_sVersion = $this->getAOrB(
                    empty( $aScriptInfo[ 'sVersion' ] ),
                    '',
                    "&nbsp;{$aScriptInfo[ 'sVersion' ]}"
                );
                $_sPluginInfo = $this->getAOrB(
                    empty( $aScriptInfo[ 'sURI' ] ),
                    $aScriptInfo[ 'sName' ],
                    $this->getHTMLTag(
                        'a',
                        array(
                            'href'      => $aScriptInfo[ 'sURI' ],
                            'target'    => '_blank',
                            'title'     => $aScriptInfo[ 'sName' ] . $_sVersion . $_sDescription
                        ),
                        $aScriptInfo[ 'sName' ]
                    )
                );

                $_sAuthorInfo = $this->getAOrB(
                    empty( $aScriptInfo[ 'sAuthorURI' ] ),
                    '',
                    $this->getHTMLTag(
                        'a',
                        array(
                            'href'      => $aScriptInfo[ 'sAuthorURI' ],
                            'target'    => '_blank',
                            'title'     => $aScriptInfo[ 'sAuthor' ],
                        ),
                        $aScriptInfo[ 'sAuthor' ]
                    )
                );
                $_sAuthorInfo = $this->getAOrB(
                    empty( $aScriptInfo[ 'sAuthor' ] ),
                    $_sAuthorInfo,
                    ' by ' . $_sAuthorInfo
                );

                // Enclosing the output in a span tag as the outer element is a '<p>' tag. So this cannot be div.
                // 3.5.7+ Added the class attribute for acceptance testing
                return "<span class='apf-script-info'>"
                        . $_sPluginInfo . $_sAuthorInfo
                    . "</span>";

            }
            /**
             * Sets the default footer text on the right hand side.
             *
             * @since       2.1.1
             * @since       3.5.5       Changed the name from `_setFooterInfoRight()` and dropped the second parameter.
             * @return      string
             */
            private function _getFooterInfoRight( $aScriptInfo ) {

                $_sDescription = $this->getAOrB(
                    empty( $aScriptInfo[ 'sDescription' ] ),
                    '',
                    "&#13;{$aScriptInfo[ 'sDescription' ]}"
                );
                $_sVersion = $this->getAOrB(
                    empty( $aScriptInfo[ 'sVersion' ] ),
                    '',
                    "&nbsp;{$aScriptInfo[ 'sVersion' ]}"
                );
                $_sLibraryInfo = $this->getAOrB(
                    empty( $aScriptInfo[ 'sURI' ] ),
                    $aScriptInfo[ 'sName' ],
                    $this->getHTMLTag(
                        'a',
                        array(
                            'href'      => $aScriptInfo[ 'sURI' ],
                            'target'    => '_blank',
                            'title'     => $aScriptInfo[ 'sName' ] . $_sVersion . $_sDescription,
                        ),
                        $aScriptInfo[ 'sName' ]
                    )
                );

                // Update the variable
                // 3.5.7+ added the 'apf-credit' class attribute for acceptance testing
                // 3.7.0+  added the footer-thankyou id attribute.
                return "<span class='apf-credit' id='footer-thankyou'>"
                    . $this->oMsg->get( 'powered_by' ) . '&nbsp;'
                    . $_sLibraryInfo
                    . ",&nbsp;"
                    . $this->oMsg->get( 'and' ) . '&nbsp;'
                    . $this->getHTMLTag(
                        'a',
                        array(
                            'href'      => 'https://wordpress.org',
                            'target'    => '_blank',
                            'title'     => 'WordPress ' . $GLOBALS[ 'wp_version' ]
                        ),
                        'WordPress'
                    )
                    . "</span>";

            }

        /**
         * Sets up hooks to insert admin footer text strings.
         * @internal
         * @since       3.5.5
         * @return      void
         */
        protected function _setFooterHooks() {

            add_filter( 'admin_footer_text' , array( $this, '_replyToAddInfoInFooterLeft' ) );
            add_filter( 'update_footer', array( $this, '_replyToAddInfoInFooterRight' ), 11 );

        }
            /**
             * Inserts the left footer text.
             * @since       2.0.0
             * @since       3.5.5       Moved from `AdminPageFramework_Link_post_type`.
             * @remark      The page link class will override this method.
             * @callback    filter      admin_footer_text
             * @internal
             */
            public function _replyToAddInfoInFooterLeft( $sLinkHTML='' ) {

                $sLinkHTML = empty( $this->oProp->aScriptInfo[ 'sName' ] )
                    ? $sLinkHTML
                    : $this->oProp->aFooterInfo[ 'sLeft' ];

                return $this->addAndApplyFilters(
                    $this->oProp->oCaller,
                    'footer_left_' . $this->oProp->sClassName,
                    $sLinkHTML
                );

            }
            /**
             * Inserts the right footer text.
             * @since       2.0.0
             * @since       3.5.5       Moved from `AdminPageFramework_Link_post_type`.
             * @remark      The page link class will override this method.
             * @callback    filter      admin_footer_text
             * @internal
             */
            public function _replyToAddInfoInFooterRight( $sLinkHTML='' ) {
                return $this->addAndApplyFilters(
                    $this->oProp->oCaller,
                    'footer_right_' . $this->oProp->sClassName,
                    $this->oProp->aFooterInfo[ 'sRight' ]
                );
            }

}
