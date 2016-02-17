<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to render debug information in a form table.
 * 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       3.6.0
 * @since       3.7.0      Renamed from `AdminPageFramework_FormPart_DebugInfo`.
 * @internal
 * @extends     AdminPageFramework_FrameworkUtility
 */
class AdminPageFramework_Form_View___DebugInfo extends AdminPageFramework_FrameworkUtility {

    public $sStructureType   = '';
    
    /**
     * Stores the message object.
     */
    public $oMsg;
    
    /**
     * Sets up properties.
     * @since       3.6.0
     */
    public function __construct( /* $sStructureType, $oMsg */ ) {

        $_aParameters = func_get_args() + array(
            $this->sStructureType,
            $this->oMsg,
        );

        $this->sStructureType   = $_aParameters[ 0 ];
        $this->oMsg             = $_aParameters[ 1 ];

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
        if ( ! in_array( $this->sStructureType, array( 'widget', 'post_meta_box', 'page_meta_box', 'user_meta' ) ) ) {
            return '';
        }
        
        return "<div class='admin-page-framework-info'>"
                . $this->oMsg->get( 'debug_info' ) . ': '
                    . AdminPageFramework_Registry::NAME . ' ' . AdminPageFramework_Registry::getVersion()
            . "</div>";
        
    }

}
