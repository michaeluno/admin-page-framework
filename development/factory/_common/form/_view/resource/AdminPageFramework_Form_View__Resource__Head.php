<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to insert required head scripts.
 *
 * Most form scripts can be inserted after the head tag. However there are a few that must be inserted in the header,
 * such as the ones that hides the form and shows a loading message.
 *
 * @package     AdminPageFramework/Common/Form/View/Resource
 * @since       3.7.0
 * @extends     AdminPageFramework_FrameworkUtility
 * @internal
 */
class AdminPageFramework_Form_View__Resource__Head extends AdminPageFramework_FrameworkUtility {

    /**
     * Stores a form object.
     * @var AdminPageFramework_Form_Base
     */
    public $oForm;

    /**
     * @since       3.7.0
     * @param       object      $oForm
     * @param       string      $sHeadActionHook        The action hook triggered inside the `<head>` tag. For front-end forms, use `wp_head`.
     */
    public function __construct( $oForm, $sHeadActionHook='admin_head' ) {

        $this->oForm = $oForm;

        if ( in_array( $this->oForm->aArguments[ 'structure_type' ], array( 'widget' ) ) ) {
            return;
        }

        add_action( $sHeadActionHook, array( $this, '_replyToInsertRequiredInternalScripts' ) );

    }

    /**
     * Inserts JavaScript scripts which must be inserted head.
     * @since       3.7.0
     */
    public function _replyToInsertRequiredInternalScripts() {

        /**
         * Make sure to perform this check prior to the below `hasBeenCalled()` method
         * as multiple instances of the factory class is loaded among separate pages,
         * one gets denied here and if it can load earlier than the one which should insert the below script.
         */
        if ( ! $this->oForm->isInThePage() ) {
            return;
        }

        // Ensure to load only once per page load
        if ( $this->hasBeenCalled( __METHOD__ ) ) {
            return;
        }
        echo "<script type='text/javascript' class='admin-page-framework-form-script-required-in-head'>"
                . '/* <![CDATA[ */ '
                . $this->_getScripts_RequiredInHead()
                . ' /* ]]> */'
            . "</script>";

    }

        /**
         * @since       3.7.0
         * @return      string
         */
        private function _getScripts_RequiredInHead() {
            return 'document.write( "<style class=\'admin-page-framework-js-embedded-internal-style\'>'
                    . str_replace(
                        '\\n',   // search
                        '',     // replace
                        esc_js( $this->_getInternalCSS() )    // subject
                    )
                . '</style>" );';
        }
            /**
             * @return      string
             * @since       3.7.0
             */
            private function _getInternalCSS() {
                $_oLoadingCSS = new AdminPageFramework_Form_View___CSS_Loading;
                $_oLoadingCSS->add( $this->_getScriptElementConcealerCSSRules() );
                return $_oLoadingCSS->get();
            }
                /**
                 * Hides the form initially to prevent unformatted layouts being displayed during document load.
                 * @remark      Use visibility to reserve the element area in the screen.
                 * @return      string
                 * @since       3.7.0
                 */
                private function _getScriptElementConcealerCSSRules() {

                    // Avoid hiding framework forms in widgets. In some cases, the user sets a form in any page,
                    // which causes a different structure form type including `admin_page` gets loaded even in widgets.php (or any page builder pages dealing with widgets)
                    // If that happens the head script gets called and widgets are hidden forever.
                    return <<<CSSRULES
.admin-page-framework-form-js-on {  
    visibility: hidden;
}
.widget .admin-page-framework-form-js-on { 
    visibility: visible; 
}
CSSRULES;
                }

}