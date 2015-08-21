<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to render debug information in a form table.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_FormPart_DebugInfo extends AdminPageFramework_FormPart_Base {            

    public $sFieldsType   = '';
    
    /**
     * Sets up properties.
     * @since       3.6.0
     */
    public function __construct( /* $sFieldsType */ ) {

        $_aParameters = func_get_args() + array( 
            $this->sFieldsType, 
        );
        $this->sFieldsType    =  $_aParameters[ 0 ];

    }

    /**
     * Returns some framework information for debugging.
     * 
     * @since       3.5.3
     * @return      string      Some information for debugging.
     */
    public function get() {
        
        if ( ! $this->isDebugModeEnabled() ) {
            return '';
        }
        // For the generic admin pages, do no show debug information for each section.
        if ( ! in_array( $this->sFieldsType, array( 'widget', 'post_meta_box', 'page_meta_box', 'user_meta' ) ) ) {
            return '';
        }
        
        // @todo    Use the message object to display the words 'Debug Info'.
        return "<div class='admin-page-framework-info'>" 
                . 'Debug Info: ' . AdminPageFramework_Registry::NAME . ' '. AdminPageFramework_Registry::getVersion() 
            . "</div>";
        
    }

}