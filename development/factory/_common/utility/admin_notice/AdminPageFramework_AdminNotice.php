<?php
/**
 * Displays notification in the administration area.
 *
 * @package      Admin Page Framework
 * @copyright    Copyright (c) 2013-2022, Michael Uno
 * @author       Michael Uno
 * @authorurl    http://michaeluno.jp
 */

/**
 * Displays notification in the administration area.
 *
 * <h2>Usage</h2>
 * <code>
 * new AdminPageFramework_AdminNotice( 'There was an error while doing something...' );
 * </code>
 *
 * <h2>Example</h2>
 * <code>
 * new AdminPageFramework_AdminNotice( 'Error occurred', array( 'class' => 'error' ) );
 * </code>
 *
 * <code>
 * new AdminPageFramework_AdminNotice( 'Setting Updated', array( 'class' => 'updated' ) );
 * </code>
 *
 * For details of arguments, see the <code>__construct()</code> method below.
 *
 * @image       http://admin-page-framework.michaeluno.jp/image/common/utility/admin_notice.png
 * @since       3.5.0
 * @package     AdminPageFramework/Common/Utility
 * @extends     AdminPageFramework_FrameworkUtility
 */
class AdminPageFramework_AdminNotice extends AdminPageFramework_FrameworkUtility {

    /**#@+
     * @internal
     */
    /**
     * Stores all the registered notification messages.
     */
    static private $___aNotices = array();

    public $sNotice     = '';
    public $aAttributes = array();
    public $aCallbacks  = array(
        'should_show'   => null,    // determines whether the admin notice should be displayed.
    );
    /**#@-*/

    /**
     * Sets up hooks and properties.
     *
     * @param string $sNotice        The message to display.
     * @param array  $aAttributes    An attribute array. Set 'updated' to the 'class' element to display it in a green box.
     * @param array  $aCallbacks     [3.7.0+] An array storing callbacks.
     * <h4>Arguments</h4>
     * <ul>
     *      <li>`should_show` - (callable) Determines whether the admin notice should be displayed or not.</li>
     * </ul>
     * @since 3.5.0
     */
    public function __construct( $sNotice, array $aAttributes=array( 'class' => 'error' ), array $aCallbacks=array() ) {

        $this->aAttributes            = $aAttributes + array(
            'class' => 'error', // 'updated' etc.
        );
        $this->aAttributes[ 'class' ] = $this->getClassAttribute(
            $this->aAttributes[ 'class' ],
            'admin-page-framework-settings-notice-message',
            'admin-page-framework-settings-notice-container',   // Moved from `AdminPageFramework_Factory_View`.
            'notice',
            'is-dismissible'    // 3.5.12+
        );
        $this->aCallbacks             = $aCallbacks + $this->aCallbacks;

        // Load resources.
        new AdminPageFramework_AdminNotice___Script;

        // An empty value may be set in order only to load the fade-in script.
        if ( ! $sNotice ) {
            return;
        }

        // This prevents duplicates
        $this->sNotice = $sNotice;
        self::$___aNotices[ $sNotice ] = $sNotice;

        $this->registerAction( 'admin_notices', array( $this, '_replyToDisplayAdminNotice' ) );
        $this->registerAction( 'network_admin_notices', array( $this, '_replyToDisplayAdminNotice' ) );

    }

        /**
         * Displays the set admin notice.
         * @since    3.5.0
         * @internal
         */
        public function _replyToDisplayAdminNotice() {

            if ( ! $this->___shouldProceed() ) {
                return;
            }

            // For a browser that enables JavaScript, hide the admin notice.
            $_aAttributes = $this->aAttributes + array( 'style' => '' );
            $_aAttributes[ 'style' ] = $this->getStyleAttribute(
                $_aAttributes[ 'style' ],
                'display: none'
            );

            echo "<div " . $this->getAttributes( $_aAttributes ) . ">"
                    . "<p>"
                        . self::$___aNotices[ $this->sNotice ]
                    . "</p>"
                . "</div>"
                // Insert the same message except it is not hidden.
                . "<noscript>"
                    . "<div " . $this->getAttributes( $this->aAttributes ) . ">"
                        . "<p>"
                            . self::$___aNotices[ $this->sNotice ]
                        . "</p>"
                    . "</div>"
                . "</noscript>";

            unset( self::$___aNotices[ $this->sNotice ] );

        }
            /**
             * Decides whether the notification should be displayed or not.
             * @return   boolean
             * @since    3.7.0
             * @internal
             */
            private function ___shouldProceed() {
                if ( ! is_callable( $this->aCallbacks[ 'should_show' ] ) ) {
                    return true;
                }
                return call_user_func_array( $this->aCallbacks[ 'should_show' ], array( true, ) );
            }

}