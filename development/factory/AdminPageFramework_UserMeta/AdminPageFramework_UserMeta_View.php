<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Handles displaying user meta field outputs.
 *
 * @abstract
 * @since           3.5.0
 * @package         AdminPageFramework
 * @subpackage      UserMeta
 */
abstract class AdminPageFramework_UserMeta_View extends AdminPageFramework_UserMeta_Model {
    
    /**
     * The content filter method,
     * 
     * The user may just override this method instead of defining a `content_{...}` callback method.
     * 
     * @since       3.5.1
     * @remark      Declare this method in each factory class as the form of parameters varies and if parameters are different, it triggers PHP strict standard warnings.
     * @param       string      $sContent       The filtering content string.
     */
    public function content( $sContent ) {
        return $sContent;
    }         
    
    /**
     * Renders the fields.
     * 
     * @remark      Called in the _replyTODetermineToLoad() method.
     * @since       3.5.0
     * @internal
     * 
     * @callback    action      show_user_profile
     * @callback    action      edit_user_profile
     * @callback    action      user_new_form
     */
    public function _replyToPrintFields( $oUser ) {
        
        $_iUserID = isset( $oUser->ID ) ? $oUser->ID : 0;
        
        $this->_setOptionArray( $_iUserID );
        
        // Ouptut the fields
        echo $this->_getFieldsOutput( $_iUserID );
        
    }
        /**
         * Retrieves the fields output.
         * 
         * @since       3.5.0
         * @internal
         */
        private function _getFieldsOutput( $iUserID ) {
        
            $_aOutput = array();
            
            // Get the field outputs
            $_oFieldsTable = new AdminPageFramework_FormTable( $this->oProp->aFieldTypeDefinitions, $this->_getFieldErrors(), $this->oMsg );
            $_aOutput[]    = $_oFieldsTable->getFormTables( 
                $this->oForm->aConditionedSections, 
                $this->oForm->aConditionedFields, 
                array( $this, '_replyToGetSectionHeaderOutput' ), 
                array( $this, '_replyToGetFieldOutput' ) 
            );
            
            // Filter the output
            $_sOutput = $this->oUtil->addAndApplyFilters( 
                $this, 
                'content_' . $this->oProp->sClassName, 
                $this->content( implode( PHP_EOL, $_aOutput ) )
            );
            
            // Do action 
            $this->oUtil->addAndDoActions( $this, 'do_' . $this->oProp->sClassName, $this );
                
            return $_sOutput;
               
            
        }

        /**
         * Returns the filtered section description output.
         * 
         * @since       3.5.0
         * @internal
         */
        public function _replyToGetSectionHeaderOutput( $sSectionDescription, $aSection ) {
            return $this->oUtil->addAndApplyFilters(
                $this,
                array( 'section_head_' . $this->oProp->sClassName . '_' . $aSection['section_id'] ), // section_ + {extended class name} + _ {section id}
                $sSectionDescription
            );     
        }        
    
}