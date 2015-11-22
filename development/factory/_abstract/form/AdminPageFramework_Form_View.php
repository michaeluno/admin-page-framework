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
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       DEVVER
 */
class AdminPageFramework_Form_View extends AdminPageFramework_Form_Model {
    
    /**
     * Sets up hooks.
     * @since       DEVVER
     */
    public function __construct() {

        parent::__construct();
        
        new AdminPageFramework_Form_View__Resource( $this );
        
    }
  
    /**
     * Returns the form output.
     * @return      string  The form output.
     */
    public function get() {
        
        $this->sCapability = $this->callBack(
            $this->aCallbacks[ 'capability' ],
            '' // default value
        );        

        if ( ! $this->canUserView( $this->sCapability ) ) {
            return '';
        }     
        
        // Format and update sectionset and fieldset definitions.
        $this->_formatElementDefinitions( $this->aSavedData );

        $_oFormTables = new AdminPageFramework_Form_View___Sectionsets(
            // Arguments which determine the object behaviour
            array(
                'capability'                => $this->sCapability,
            ) + $this->aArguments,
            // Form structure definitions
            array(
                'field_type_definitions'    => $this->aFieldTypeDefinitions,
                'sectionsets'               => $this->aSectionsets,
                'fieldsets'                 => $this->aFieldsets,
            ),
            $this->aSavedData,            
            $this->getFieldErrors(),
            $this->aCallbacks,
            $this->oMsg
        );        
        return $_oFormTables->get();
        
    }
    
    /**
     * Outputs submit notices stored in the database transient.
     * @since       DEVVER
     * @return      void
     */
    public function printSubmitNotices() {
        $this->oSubmitNotice->render();        
    }

}