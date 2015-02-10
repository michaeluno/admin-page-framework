<?php
/**
 * Displays notification in the administration area.
 *    
 * @package      Admin Page Framework
 * @copyright    Copyright (c) 2015, <Michael Uno>
 * @author       Michael Uno
 * @authorurl    http://michaeluno.jp
 */

/**
 * Displays notification in the administration area.
 * 
 * Usage: new AdminPageFramework_AdminNotice( 'There was an error while doing something...' );
 * <code>
 * new AdminPageFramework_AdminNotice( 'Error occurred', array( 'class' => 'error' ) );    
 * </code>
 * <code>
 * new AdminPageFramework_AdminNotice( 'Setting Updated', array( 'class' => 'updated' ) );
 * </code>
 * 
 * @since       3.5.0
 * @uses        AdminPageFramework_Parsedown
 * @package     AdminPageFramework
 * @subpackage  Utility
 */
class AdminPageFramework_AdminNotice {

    /**
     * Sets up hooks and properties.
     * 
     * @param       string      $sNotice        The message to display.
     * @param       array       $aAttributes    An attribute array. Set 'updated' to the 'class' element to display it in a green box.
     * @since       3.5.0
     */
    public function __construct( $sNotice, array $aAttributes=array( 'class' => 'error' ) ) {
        $this->sNotice          = $sNotice;
        $this->aAttributes      = $aAttributes + array(
            'class' => 'error',
        );
        $this->aAttributes['class'] .= ' admin-page-framework-settings-notice-message';
        if ( did_action( 'admin_notices' ) ) {
            $this->_replyToDisplayAdminNotice();
        } else {
            add_action( 'admin_notices', array( $this, '_replyToDisplayAdminNotice' ) );
        }
    }    
        /**
         * Displays the set admin notice.
         * @since       3.5.0
         */
        public function _replyToDisplayAdminNotice() {

            echo "<div " . $this->_getAttributes( $this->aAttributes ) . ">"
                    . "<p>"
                        . $this->sNotice 
                    . "</p>"
                . "</div>";
                
        }
        
        /**
         * Generates HTML tag attributes from an array.
         * @since       3.5.0
         */
        private function _getAttributes( array $aAttributes )  {
            
            $_sQuoteCharactor   = "'";
            $_aOutput           = array();
            foreach( $aAttributes as $_sAttribute => $_asProperty ) {
                
                if ( 'style' === $_sAttribute && is_array( $_asProperty ) ) {
                    $_asProperty = $this->_getInlineCSS( $_asProperty );
                }
                
                // Must be resolved as a string.
                if ( in_array( gettype( $_asProperty ), array( 'array', 'object', 'NULL' ) ) ) {
                    continue;
                }
                            
                $_aOutput[] = "{$_sAttribute}={$_sQuoteCharactor}" 
                        . esc_attr( $_asProperty )
                    . "{$_sQuoteCharactor}";
                
            }
            return trim( implode( ' ', $_aOutput ) );
            
        }
        /**
         * Generates HTML inline style attribute from an array.
         * @since       3.5.0
         * @return      string      The generated inline CSS rules.
         */               
        private function _getInlineCSS( array $aCSSRules ) {
            
            $_aOutput = array();
            foreach( $aCSSRules as $_sProperty => $_sValue ) {
                $_aOutput[] = $_sProperty . ': ' . $_sValue;
            }
            return implode( '; ', $_aOutput );
            
        }
    
}