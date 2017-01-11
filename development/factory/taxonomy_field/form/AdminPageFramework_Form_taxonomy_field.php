<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to build forms of the `taxonomy_field` structure type.
 * 
 * The suffix represents the structure type of the form.
 * 
 * @package     AdminPageFramework/Factory/TaxonomyField/Form
 * @since       3.7.0      
 * @extends     AdminPageFramework_Form
 * @internal
 */
class AdminPageFramework_Form_taxonomy_field extends AdminPageFramework_Form {
    
    public $sStructureType = 'taxonomy_field';    
    
    /**
     * Retrieves the form fields output.
     * 
     * This overrides the parent method as taxonomy fields do not support sections.
     * 
     * @remark      For the taxonomy field factory type which does not accept sections.
     * @return      string
     */
    public function get( /* $bEditTerm */ ) {

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
