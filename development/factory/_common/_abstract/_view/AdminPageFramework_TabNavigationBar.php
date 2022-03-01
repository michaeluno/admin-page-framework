<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Creates a tab navigation bar.
 *
 * @since           3.5.10
 * @extends         AdminPageFramework_FrameworkUtility
 * @package         AdminPageFramework/Common/Factory/Tab
 * @internal
 */
class AdminPageFramework_TabNavigationBar extends AdminPageFramework_FrameworkUtility {

    /**
     * The HTML tag used for the tag.
     */
    public $sTabTag = 'h2';

    /**
     * Stores the tab items.
     */
    public $aTabs   = array();

    /**
     * Stores container attributes.
     */
    public $aAttributes = array(
        'class' => 'nav-tab-wrapper',
    );

    public $aTab = array(
        'slug'       => null,  // (string) tab slug (id)
        'title'      => null,  // (string) tab title
        'href'       => null,  // (string) link url
        'disabled'   => null,  // (boolean)
        'class'      => null,  // (string) class selector to append to the class attribute
        'attributes' => array(),
    );

    /**
     * Stores callbables.
     */
    public $aCallbacks = array(
        'format'    => null,
        'arguments' => array(),
    );

    /**
     * Sets up properties.
     *
     * @since       3.5.10
     * @param       array             $aTabs              An array holding each tab definitions
     * @param       array|string      $asActiveTabSlugs   The default tab slug.
     */
    public function __construct( array $aTabs, $asActiveTabSlugs, $sTabTag='h2', $aAttributes=array( 'class' => 'nav-tab-wrapper', ), $aCallbacks=array() ) {

        $this->aCallbacks           = $aCallbacks + array(
            'format'    => null,
            'arguments' => null,  // custom arguments to pass to the callback functions.
        );
        $this->aTabs                = $this->_getFormattedTabs( $aTabs );
        $this->aActiveSlugs         = $this->getAsArray( $asActiveTabSlugs );
        $this->sTabTag              = $sTabTag
            ? tag_escape( $sTabTag )
            : $this->sTabTag;
        $this->aAttributes          = $aAttributes;

    }
        /**
         * @return      array
         */
        private function _getFormattedTabs( array $aTabs ) {
            foreach( $aTabs as $_isKey => &$_aTab ) {
                $_aFormattedTab = $this->_getFormattedTab( $_aTab, $aTabs );
                if ( isset( $_aFormattedTab[ 'slug' ] ) ) {
                    $_aTab = $_aFormattedTab;
                } else {
                    unset( $aTabs[ $_isKey ] );
                }
            }
            return $aTabs;
        }
            /**
             * @return      array
             */
            private function _getFormattedTab( array $aTab, array $aTabs ) {

                $aTab = is_callable( $this->aCallbacks[ 'format' ] )
                    ? call_user_func_array(
                        $this->aCallbacks[ 'format' ],
                        array(
                            $aTab, // 1st parameter
                            $this->aTab, // 2nd parameter
                            $aTabs, // 3rd parameter
                            $this->aCallbacks[ 'arguments' ] // 4th parameter
                        )
                    )
                    : $aTab;
                if ( isset( $aTab[ 'attributes' ], $this->aTab[ 'attributes' ] ) ) {
                    $aTab[ 'attributes' ] = $aTab[ 'attributes' ] + $this->aTab[ 'attributes' ];
                }
                return $aTab + $this->aTab;

            }
    /**
     * Returns the HTML output of the tag navigation bar.
     *
     * @since       3.5.10
     * @return      string      the HTML output of the tag navigation bar.
     */
    public function get() {
        return $this->_getTabs();
    }
        /**
         * Returns the navigation bar output.
         *
         * By default, the HTML tag is used for this element is `h2`. It encloses all tag items of an `a` tag.
         * @internal
         * @return      string
         */
        private function _getTabs() {

            $_aOutput = array();
            foreach( $this->aTabs as $_aTab ) {
                $_sTab = $this->_getTab( $_aTab );
                if ( ! $_sTab ) {
                    continue;
                }
                $_aOutput[] = $_sTab;
            }

            $_aContainerAttributes = $this->aAttributes + array( 'class' => null );
            $_aContainerAttributes[ 'class' ] = $this->getClassAttribute(
                'nav-tab-wrapper',
                $_aContainerAttributes[ 'class' ]
            );

            return empty( $_aOutput )
                ? ''
                : "<{$this->sTabTag} " . $this->getAttributes( $_aContainerAttributes ) . ">"
                    . implode( '', $_aOutput )
                . "</{$this->sTabTag}>";

        }
            /**
             * Returns the output of each tab.
             *
             * Each tab item is an `a` tag HTML element.
             *
             * @return      string
             * @internal
             */
            private function _getTab( array $aTab ) {

                $_aATagAttributes = isset( $aTab[ 'attributes' ] )
                    ? $aTab[ 'attributes' ]
                    : array();
                $_sClassAttribute = $this->getClassAttribute(
                    'nav-tab',
                    $this->getElement(
                        $aTab, // subject array
                        'class', // element key
                        '' // default
                    ),
                    $this->getElement(
                        $_aATagAttributes,
                        'class',
                        ''
                    ),
                    $this->getAOrB(
                        in_array( $aTab[ 'slug' ], $this->aActiveSlugs ),
                        "nav-tab-active",
                        ''
                    ),
                    $this->getAOrB(
                        $aTab[ 'disabled' ],
                        'tab-disabled',
                        ''
                    )
                );
                $_aATagAttributes = array(
                    'class' => $_sClassAttribute,
                )
                + $_aATagAttributes
                + array(
                    'href'  => $aTab[ 'href' ],
                    'title' => $aTab[ 'title' ],
                );
                if ( $aTab[ 'disabled' ] ) {
                    unset( $_aATagAttributes[ 'href' ] );
                }
                return $this->getHTMLTag(
                    'a',
                    $_aATagAttributes,
                    $aTab[ 'title' ]
                );

            }


}
