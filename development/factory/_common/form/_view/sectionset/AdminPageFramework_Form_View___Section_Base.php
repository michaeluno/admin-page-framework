<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides shared methods for rendering forms.
 * 
 * @package     AdminPageFramework/Common/Form/View/Section
 * @since       3.7.0      
 * @internal
 */
class AdminPageFramework_Form_View___Section_Base extends AdminPageFramework_Form_Base {
     
    /**
     * @since       3.7.0
     * @return      boolean
     */
    public function isSectionsetVisible( $aSectionset ) {
        if ( empty( $aSectionset ) ) {
            return false;
        }        
        return $this->callBack( 
            $this->aCallbacks[ 'is_sectionset_visible' ], 
            array( true, $aSectionset ) 
        );        
    }

    /**
     * @since       3.7.0
     * @return      boolean
     */    
    public function isFieldsetVisible( $aFieldset ) {
        if ( empty( $aFieldset ) ) {
            return false;
        }
        return $this->callBack( 
            $this->aCallbacks[ 'is_fieldset_visible' ], 
            array( true, $aFieldset ) 
        );
    }
 
    /**
     * The output of the fieldset.
     *
     * @remark      Accessed from section title class and fieldset table-row class.
     * @return      string
     */
    public function getFieldsetOutput( $aFieldset ) {

        // Check if the field is visible
        if ( ! $this->isFieldsetVisible( $aFieldset ) ) {          
            return '';
        }

        $_oFieldset = new AdminPageFramework_Form_View___Fieldset( 
            $aFieldset, 
            $this->aSavedData,    // passed by reference. @todo: examine why it needs to be passed by reference.
            $this->aFieldErrors, 
            $this->aFieldTypeDefinitions, 
            $this->oMsg,
            $this->aCallbacks // field output element callables.
        );
        $_sFieldOutput = $_oFieldset->get(); // field output

        return $_sFieldOutput;
        
    } 
    
}
