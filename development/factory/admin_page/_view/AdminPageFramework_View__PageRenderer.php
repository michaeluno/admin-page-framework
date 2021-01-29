<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
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
class AdminPageFramework_View__PageRenderer extends AdminPageFramework_FrameworkUtility {

    public $oFactory;
    public $sPageSlug;
    public $sTabSlug;

    public $aCSSRules = array();
    public $aScripts  = array();

    /**
     * Sets up properties.
     * @since       3.6.3
     */
    public function __construct( $oFactory, $sPageSlug, $sTabSlug ) {

        $this->oFactory         = $oFactory;
        $this->sPageSlug        = $sPageSlug;
        $this->sTabSlug         = $sTabSlug;

    }

    /**
     * @since       3.6.3
     */
    public function render() {

        $_sPageSlug = $this->sPageSlug;
        $_sTabSlug  = $this->sTabSlug;

        // Do actions before rendering the page. In this order, global -> page -> in-page tab
        $this->addAndDoActions(
            $this->oFactory,  // the caller object
            $this->getFilterArrayByPrefix( 'do_before_', $this->oFactory->oProp->sClassName, $_sPageSlug, $_sTabSlug, true ), // the action hooks
            $this->oFactory   // the argument 1
        );
        ?>
        <div class="<?php echo esc_attr( $this->oFactory->oProp->sWrapperClassAttribute ); ?>">
            <?php echo $this->_getContentTop(); ?>
            <div class="admin-page-framework-container">
                <?php
                    $this->addAndDoActions(
                        $this->oFactory, // the caller object
                        $this->getFilterArrayByPrefix( 'do_form_', $this->oFactory->oProp->sClassName, $_sPageSlug, $_sTabSlug, true ), // the action hooks
                        $this->oFactory // the argument 1
                    );
                    $this->_printFormOpeningTag( $this->oFactory->oProp->bEnableForm ); // <form ... >
                ?>
                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-<?php echo $this->_getNumberOfColumns(); ?>">
                    <?php
                        $this->_printMainPageContent( $_sPageSlug, $_sTabSlug );
                        $this->_printPageMetaBoxes();
                    ?>
                    </div><!-- #post-body -->
                </div><!-- #poststuff -->

            <?php echo $this->_printFormClosingTag( $_sPageSlug, $_sTabSlug, $this->oFactory->oProp->bEnableForm );  // </form> ?>
            </div><!-- .admin-page-framework-container -->

            <?php
                // Apply the content_bottom filters.
                echo $this->addAndApplyFilters( $this->oFactory, $this->getFilterArrayByPrefix( 'content_bottom_', $this->oFactory->oProp->sClassName, $_sPageSlug, $_sTabSlug, false ), '' ); // empty string
            ?>
        </div><!-- .wrap -->
        <?php
        // Do actions after rendering the page.
        $this->addAndDoActions(
            $this->oFactory,  // the caller object
            $this->getFilterArrayByPrefix( 'do_after_', $this->oFactory->oProp->sClassName, $_sPageSlug, $_sTabSlug, true ), // the action hooks
            $this->oFactory   // the argument 1
        );

    }

        /**
         * Returns the number of columns in the page.
         *
         * @since           3.0.0
         * @since           3.6.3       Changed the visibility scope from `protected`. Moved from `AdminPageFramework_Page_Viee_MetaBox`.
         * @return          integer
         * @internal
         */
        private function _getNumberOfColumns() {

            if ( ! $this->doesMetaBoxExist( 'side' ) ) {
                return 1;
            }

            $_iColumns = $this->getNumberOfScreenColumns();
            return $_iColumns
                ? $_iColumns
                : 1;    // default - this is because generic pages do not have meta boxes.
        }
            // @deprecated
            // Make sure if no side meta box exists, set it 1.
            // $_iColumns = $this->doesSideMetaBoxExist()
                // ? $this->getNumberOfScreenColumns()
                // : 1;
            // return $_iColumns
                // ? $_iColumns
                // : 1;    // default - this is because generic pages do not have meta boxes.

        /**
         * Returns the top part of a page content.
         * @since       3.6.3
         * @return      string
         */
        private function _getContentTop() {

            // Screen icon, page heading tabs(page title), and in-page tabs.
            $_oScreenIcon       = new AdminPageFramework_View__PageRenderer__ScreenIcon(
                $this->oFactory,
                $this->sPageSlug,
                $this->sTabSlug
            );
            $_sContentTop       = $_oScreenIcon->get();

            $_oPageHeadingTabs  = new AdminPageFramework_View__PageRenderer__PageHeadingTabs(
                $this->oFactory,
                $this->sPageSlug
            );
            $_sContentTop      .= $_oPageHeadingTabs->get();
            $_oInPageTabs       = new AdminPageFramework_View__PageRenderer__InPageTabs(
                $this->oFactory,
                $this->sPageSlug
            );
            $_sContentTop      .= $_oInPageTabs->get();

            // Apply filters in this order, in-page tab -> page -> global.
            return $this->addAndApplyFilters(
                $this->oFactory,
                $this->getFilterArrayByPrefix(
                    'content_top_',
                    $this->oFactory->oProp->sClassName,
                    $this->sPageSlug,
                    $this->sTabSlug,
                    false
                ),
                $_sContentTop
            );
        }


        /**
         * Renders the main content of the admin page.
         *
         * @since       3.0.0
         * @since       3.3.1       Moved from `AdminPageFramework_Page`.
         * @since       3.6.3       Moved from `AdminPageFramework_Page_View`.
         * @return      void
         */
        private function _printMainPageContent( $sPageSlug, $sTabSlug ) {

            $_bSideMetaboxExists = $this->doesMetaBoxExist( 'side' );

            echo "<!-- main admin page content -->";
            echo "<div class='admin-page-framework-content'>";
            if ( $_bSideMetaboxExists ) {
                echo "<div id='post-body-content'>";
            }

            // Apply the content filters.
            echo $this->addAndApplyFilters(
                $this->oFactory,
                $this->getFilterArrayByPrefix(
                    'content_',
                    $this->oFactory->oProp->sClassName,
                    $sPageSlug,
                    $sTabSlug,
                    false ),
                $this->oFactory->content(
                    $this->_getFormOutput( $sPageSlug )
                ) // triggers __call()
            );

            // Do the page actions.
            $this->addAndDoActions(
                $this->oFactory, // the caller object
                $this->getFilterArrayByPrefix( 'do_', $this->oFactory->oProp->sClassName, $sPageSlug, $sTabSlug, true ), // the action hooks
                $this->oFactory // the argument 1
            );

            if ( $_bSideMetaboxExists ) {
                echo "</div><!-- #post-body-content -->";
            }
            echo "</div><!-- .admin-page-framework-content -->";

        }

            /**
             * Returns the form output of the page.
             * @since       3.5.3
             * @since       3.6.3       Moved from `AdminPageFramework_Page_View`.
             * @internal
             * @return      string      The form output of the page.
             */
            private function _getFormOutput( $sPageSlug ) {

                if ( ! $this->oFactory->oProp->bEnableForm ) {
                    return '';
                }
                return $this->oFactory->oForm->get();

            }

        /**
         * Renders the page meta boxes.
         * @since       3.6.3
         * @internal
         * @return      void
         */
        private function _printPageMetaBoxes() {
            $_oPageMetaBoxRenderer = new AdminPageFramework_View__PageMataBoxRenderer();
            $_oPageMetaBoxRenderer->render( 'side' );
            $_oPageMetaBoxRenderer->render( 'normal' );
            $_oPageMetaBoxRenderer->render( 'advanced' );
        }

        /**
         * Retrieves the form opening tag.
         *
         * @since       2.0.0
         * @since       3.1.0       Changed to echo the output. Changed to remove disallowed query keys in the target action url.
         * @since       3.3.1       Moved from `AdminPageFramework_Page`.
         * @since       3.6.3       Moved from `AdminPageFramework_Page_View`.
         * @internal
         * @return      void
         */
        private function _printFormOpeningTag( $fEnableForm=true ) {

            if ( ! $fEnableForm ) {
                return;
            }

            echo "<form "
                    . $this->getAttributes(
                        array(
                            'method'    => 'post',
                            'enctype'   => $this->oFactory->oProp->sFormEncType,
                            'id'        => 'admin-page-framework-form',
                            'action'    => wp_unslash( remove_query_arg( 'settings-updated', $this->oFactory->oProp->sTargetFormPage ) ),
                        )
                    )
                . " >" . PHP_EOL;

            // [3.5.11+] Insert a mark that indicates the framework form has started.
            // This will be checked in the validation method with the `is_admin_page_framework` input value which gets inserted at the end of the form
            // in order to determine all the fields are sent for the PHP max_input_vars limitation set in the server configuration.
            echo "<input type='hidden' name='admin_page_framework_start' value='1' />" . PHP_EOL;

            // Embed the '_wp_http_referer' hidden field that is checked in the submit data processing method.
            settings_fields( $this->oFactory->oProp->sOptionKey );

        }
        /**
         * Prints out the form closing tag.
         *
         * @since       2.0.0
         * @since       3.1.0       Prints out the output.
         * @since       3.3.1       Moved from `AdminPageFramework_Page`.
         * @since       3.6.3       Moved from `AdminPageFramework_Page_View`.
         * @internal
         * @return      void
         */
        private function _printFormClosingTag( $sPageSlug, $sTabSlug, $fEnableForm=true ) {

            if ( ! $fEnableForm ) {
                return;
            }
            $_sNonce = wp_create_nonce( 'form_' . md5( $this->oFactory->oProp->sClassName . get_current_user_id() ) );
            echo "<input type='hidden' name='page_slug' value='{$sPageSlug}' />" . PHP_EOL
                . "<input type='hidden' name='tab_slug' value='{$sTabSlug}' />" . PHP_EOL
                . "<input type='hidden' name='_is_admin_page_framework' value='{$_sNonce}' />" . PHP_EOL
                . "</form><!-- End Form -->" . PHP_EOL;

        }

}
