<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to build forms.
 * 
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       DEVVER
 */
class AdminPageFramework_Form_View__Resource___Head extends AdminPageFramework_WPUtility {
 
   /**
    * @since        DEVVER
    * @param        string      $sHeadActionHook        The action hook triggered inside the `<head>` tag
    */
   public function __construct( $sHeadActionHook ) {
    
        add_action( $sHeadActionHook, array( $this, '_replyToInsertRequiredInlineScripts' ) );
    
   }

    /**
     * Inserts JavaScript scripts whihc must be inserted head.
     * @since       DEVVER
     * @return      string
     */
    public function _replyToInsertRequiredInlineScripts() {

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
         * @since       DEVVER
         * @return      string
         */
        private function _getScripts_RequiredInHead() {
            return 'document.write( "<style class=\'admin-page-framework-js-embedded-inline-style\'>'
                    . esc_js( $this->_getInlineCSS() )
                . '</style>" );';            
        }
            /**
             * @return      string
             * @since       DEVVER
             */
            private function _getInlineCSS() {
                $_oLoadingCSS = new AdminPageFramework_Form_View___CSS_Loading;
                $_oLoadingCSS->add( $this->_getScriptElementConcealerCSSRules() );
                return $_oLoadingCSS->get();
            }
                /**
                 * Hides the form initially to prevent unformatted layouts being displayed during document load. 
                 * @remark      Use visibility to reserve the element area in the screen.
                 * @return      string
                 * @since       DEVVER
                 */
                private function _getScriptElementConcealerCSSRules() {                    
                    return ".admin-page-framework-form-js-on { visibility: hidden; }";
                }
   
}