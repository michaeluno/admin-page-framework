<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Handles displaying meta box outputs.
 *
 * @abstract
 * @since           3.3.0
 * @package         AdminPageFramework
 * @subpackage      MetaBox
 */
abstract class AdminPageFramework_MetaBox_View extends AdminPageFramework_MetaBox_Model {
    
    /**
     * Echoes the meta box contents.
     * 
     * @since       2.0.0
     * @param       object      $oPost      The object of the post associated with the meta box.
     * @param       array       $vArgs      The array of arguments.
     * @callback    function    add_meta_box()
     * @return      void
     * @internal    
     */ 
    public function _replyToPrintMetaBoxContents( $oPost, $vArgs ) {    

        // Use nonce for verification
        $_aOutput   = array();
        $_aOutput[] = wp_nonce_field( $this->oProp->sMetaBoxID, $this->oProp->sMetaBoxID, true, false );
                                 
        // Get the fields output.
        $_oFieldsTable  = new AdminPageFramework_FormTable( 
            $this->oProp->aFieldTypeDefinitions, 
            $this->_getFieldErrors(), 
            $this->oMsg
        );

        $_aOutput[]     = $_oFieldsTable->getFormTables(
            $this->oForm->aConditionedSections, 
            $this->oForm->aConditionedFields, 
            array( $this, '_replyToGetSectionHeaderOutput' ), 
            array( $this, '_replyToGetFieldOutput' ) 
        );

        // Do actions
        $this->oUtil->addAndDoActions( $this, 'do_' . $this->oProp->sClassName, $this );
        
        // Render the filtered output.
        echo $this->oUtil->addAndApplyFilters(
            $this, 
            "content_{$this->oProp->sClassName}", 
            $this->content( implode( PHP_EOL, $_aOutput ) )
        );            
        
    }
    
    /**
     * The content filter method,
     * 
     * The user may just override this method instead of defining a `content_{...}` callback method.
     * 
     * @since       3.4.1
     * @remark      Declare this method in each factory class as the form of parameters varies and if parameters are different, it triggers PHP strict standard warnings.
     * @param       string      $sContent       The filtering content string.
     */
    public function content( $sContent ) {
        return $sContent;
    }         
    
}