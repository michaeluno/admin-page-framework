<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_FormTable_Base' ) ) :
/**
 * The base class of the form table class that provides methods to render setting sections and fields.
 * 
 * This base class mainly deals with setting properties in the constructor and internal methods. 
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.0.0
 * @internal
 */
class AdminPageFramework_FormTable_Base extends AdminPageFramework_FormOutput {
    
    /**
     * Sets up properties and hooks.
     * 
     * @since 3.0.0
     * @since 3.0.4 The $aFieldErrors parameter was added.
     */
    public function __construct( $aFieldTypeDefinitions, array $aFieldErrors, $oMsg=null ) {
        
        $this->aFieldTypeDefinitions    = $aFieldTypeDefinitions; // used to merge the field definition array with the default field type definition. This is for the 'section_title' field type.
        $this->aFieldErrors             = $aFieldErrors;
        $this->oMsg                     = $oMsg ? $oMsg: AdminPageFramework_Message::getInstance();
        
        $this->_loadScripts();
        
    }
        
        /**
         * Indicates whether the tab JavaScript plugin is loaded or not.
         */
        static private $_bIsLoadedTabPlugin;
        
        /**
         * Inserts necessary JavaScript scripts for fields.
         * 
         * @since       3.0.0
         * @internal
         */ 
        private function _loadScripts() {
            
            if ( self::$_bIsLoadedTabPlugin ) { return; }
            self::$_bIsLoadedTabPlugin = true;
            new AdminPageFramework_Script_Tab;
            
        }
       
    
    /**
     * Returns the title part of the field output.
     * 
     * @since       3.0.0
     * @internal
     */
    protected function _getFieldTitle( $aField ) {
        
        $_sInputTagID = AdminPageFramework_FormField::_getInputID( $aField );
        return "<label for='{$_sInputTagID}'>"
            . "<a id='{$aField['field_id']}'></a>"
                . "<span title='" 
                        . esc_attr( strip_tags( 
                            isset( $aField['tip'] ) 
                                ? $aField['tip'] 
                                : ( 
                                    is_array( $aField['description'] 
                                        ? implode( '&#10;', $aField['description'] )
                                        : $aField['description'] 
                                    ) 
                                ) 
                        ) ) 
                    . "'>"
                        . $aField['title'] 
                    . ( in_array( $aField[ '_fields_type' ], array( 'widget', 'post_meta_box', 'page_meta_box' ) ) && isset( $aField['title'] ) && '' !== $aField['title']
                        ? "<span class='title-colon'>:</span>" 
                        : ''
                    )
                . "</span>"
            . "</label>";
        
    }

    /**
     * Merge the given field definition array with the field type default key array that holds default values.
     * 
     * This is important for the getFieldRow() method to know if the field should have specific styling or the hidden key is set or not,
     * which affects the way of rendering the row that contains the field output (by the field output callback).
     * 
     * @internal
     * @since       3.0.0
     * @remark      The returning merged field definition array does not respect sub-fields so when passing the field definition to the callback,
     * do not use the array returned from this method but the raw (non-merged) array.
     */
    protected function _mergeDefault( $aField ) {

        return $this->uniteArrays( 
            $aField, 
            isset( $this->aFieldTypeDefinitions[ $aField['type'] ]['aDefaultKeys'] ) 
                ? $this->aFieldTypeDefinitions[ $aField['type'] ]['aDefaultKeys'] 
                : array()
        );
        
    }
    
}
endif;