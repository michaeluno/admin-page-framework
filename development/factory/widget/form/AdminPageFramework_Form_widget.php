<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to build forms of the `widget` structure type.
 * 
 * The suffix represents the structure type of the form.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       DEVVER      
 * @extends     AdminPageFramework_Form
 * @internal
 */
class AdminPageFramework_Form_widget extends AdminPageFramework_Form {
    
    public $sStructureType = 'widget';    
    
    /**
     * Does set-ups.
     * @since       DEVVER
     * @return      void
     */
    public function construct() {        
        $this->_addDefaultResources();
    }

        /**
         * @return      void
         * @since       DEVVER
         */
        private function _addDefaultResources() {
            $_oCSS = new AdminPageFramework_Form_View___CSS_widget;
            $this->addResource( 'inline_styles', $_oCSS->get() );
        }
        
    
}