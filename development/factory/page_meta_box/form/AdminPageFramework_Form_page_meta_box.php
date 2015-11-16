<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to build forms of the `page_meta_box` structure type.
 * 
 * The suffix represents the structure type of the form.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       DEVVER      
 * @extends     AdminPageFramework_Form
 * @internal
 */
class AdminPageFramework_Form_page_meta_box extends AdminPageFramework_Form {
    
    public $sStructureType = 'page_meta_box';
        
    /**
     * Does set-ups.
     * @since       DEVVER
     * @return      void
     */
    public function construct() {
        
        add_filter(
            'options_' . $this->aArguments[ 'caller_id' ],
            array( $this, '_replyToSanitizeSavedFormData' ),
            5   //  high priority as it must be done eariler
        );
        
    }
    
    /**
     * Sanitizes the set form data for the page meta box.
     * 
     * By default, the set form data (options) which belongs to the page will be returned. 
     * This means it includes data that is nothing to do with the fields added to this page meta box.
     * 
     * remark      Assumes the user already adds items to `$aFieldsets` property by the time this method is triggered.
     * @callback    filter      options_{caller id}
     * @return      array       The sanitized saved form data.
     */
    public function _replyToSanitizeSavedFormData( $aSavedFormData ) {
        
        // Extract the meta box field form data (options) from the page form data (options).
        return $this->castArrayContents( 
            $this->getDataStructureFromAddedFieldsets(),    // form data structure generate from fieldsets
            $aSavedFormData
        );
        
        // Extract the meta box field options from the page options.
        // return $this->_getPageMetaBoxOptionsFromPageOptions( 
            // $aSavedFormData, 
            // $this->aFieldsets
        // );        
    }
        /**
         * Extracts meta box form fields options array from the given options array of an admin page.
         * 
         * @since       3.5.6
         * @since       DEVVER      Moved from `AdminPageFramework_PageMetaBox_Model`.
         * @return      array       The extracted options array.
         * @internal
         * @deprecated  DEVVER
         */
    /*     private function _getPageMetaBoxOptionsFromPageOptions( array $aFormDataOfPage, array $aFieldsets ) {    
     
            $_aFormData = array();
            foreach( $aFieldsets as $_sSectionID => $_aFieldsets ) {
                if ( '_default' === $_sSectionID  ) {
                    foreach( $_aFieldsets as $_aFieldset ) {
                        if ( array_key_exists( $_aFieldset[ 'field_id' ], $aFormDataOfPage ) ) {
                            $_aFormData[ $_aFieldset[ 'field_id' ] ] = $aFormDataOfPage[ $_aFieldset[ 'field_id' ] ];
                        }
                    }
                }
                if ( array_key_exists( $_sSectionID, $aFormDataOfPage ) ) {
                    $_aFormData[ $_sSectionID ] = $aFormDataOfPage[ $_sSectionID ];
                }
            }       
            return $_aFormData;
        
        }     */
    
}