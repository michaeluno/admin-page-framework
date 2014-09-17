<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Widget_View' ) ) :
/**
 * Provides methods of views for the widget factory class.
 * 
 * Those methods are internal and deal with printing outputs.
 * 
 * @abstract
 * @since       3.2.0
 * @package     AdminPageFramework
 * @subpackage  Widget
 */
abstract class AdminPageFramework_Widget_View extends AdminPageFramework_Widget_Model {    
 
    /**
     * Filters the post type post content.
     * 
     * This method is called in the same timing of the content_{instantiated class name.}. This is shorthand for it.
     * 
     * @remark  This class should be overridden in the extended class so that the user can display own contents.
     * @since   3.2.0
     */
    public function content( $sContent, $aArguments, $aFormData ) { 
        return $sContent; 
    }
    
    /**
     * Prints out the widget form.
     * 
     * @since       3.2.0
     * @internal
     */
    public function _printWidgetForm() {
             
        // Get the fields output.
        $_oFieldsTable  = new AdminPageFramework_FormTable( 
            $this->oProp->aFieldTypeDefinitions, 
            $this->_getFieldErrors(),                           // defined in the factory model class.
            $this->oMsg 
        );
        
        $_aOutput[]     = $_oFieldsTable->getFormTables( 
            $this->oForm->aConditionedSections, 
            $this->oForm->aConditionedFields, 
            array( $this, '_replyToGetSectionHeaderOutput' ),   // defined below
            array( $this, '_replyToGetFieldOutput' )            // defined in the factory view class.
        );
        
// AdminPageFramework_Debug::log( $this->oForm->aConditionedSections );        
        echo implode( PHP_EOL, $_aOutput );
        
    }
    
        /**
         * Returns the filtered section description output.
         * 
         * @since 3.2.0
         */
        public function _replyToGetSectionHeaderOutput( $sSectionDescription, $aSection ) {
            return $this->oUtil->addAndApplyFilters(
                $this,
                array( 'section_head_' . $this->oProp->sClassName . '_' . $aSection['section_id'] ), // section_ + {extended class name} + _ {section id}
                $sSectionDescription
            );     
        }
    
}
endif;