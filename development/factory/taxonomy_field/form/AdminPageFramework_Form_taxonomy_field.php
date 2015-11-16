<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to build forms of the `taxonomy_field` structure type.
 * 
 * The suffix represents the structure type of the form.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       DEVVER      
 * @extends     AdminPageFramework_Form
 * @internal
 */
class AdminPageFramework_Form_taxonomy_field extends AdminPageFramework_Form {
    
    public $sStructureType = 'taxonomy_field';    
    
    /**
     * Rerieves the form fields output.
     * 
     * This overrides the parent method as taxonomy fields do not support sections.
     * 
     * @remark      For the taxonomy field factory type which does not accept sections.
     * @return      string
     */
    public function get() {

        $this->sCapability = $this->callback(
            $this->aCallbacks[ 'capability' ],
            '' // default value
        );    
    
        if ( ! $this->canUserView( $this->sCapability ) ) {
            return '';
        }    

        // Format and update sectionset and fieldset definitions.
        $this->_formatElementDefinitions( $this->aSavedData ); 

        $_oFieldsets = new AdminPageFramework_Form_View___FieldsetRows(
            $this->getElementAsArray( $this->aFieldsets, '_default' ),
            null,
            $this->aSavedData,
            $this->getFieldErrors(),
            $this->aFieldTypeDefinitions,
            $this->aCallbacks,
            $this->oMsg
        );        
        return $_oFieldsets->get();
        
    }    
    
}