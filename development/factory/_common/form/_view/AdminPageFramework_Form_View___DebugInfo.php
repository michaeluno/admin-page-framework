<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to render debug information in a form table.
 * 
 * @package     AdminPageFramework/Common/Form/View
 * @since       3.6.0
 * @since       3.7.0      Renamed from `AdminPageFramework_FormPart_DebugInfo`.
 * @extends     AdminPageFramework_FrameworkUtility
 * @internal
 */
class AdminPageFramework_Form_View___DebugInfo extends AdminPageFramework_FrameworkUtility {            

    public $sStructureType   = '';
    
    /**
     * @since       3.8.5
     */
    public $aCallbacks = array();
    
    /**
     * Stores the message object.
     */
    public $oMsg;
    
    /**
     * Sets up properties.
     * @since       3.6.0
     */
    public function __construct( /* $sStructureType, $aCallbacks, $oMsg */ ) {

        $_aParameters = func_get_args() + array( 
            $this->sStructureType, 
            $this->aCallbacks,
            $this->oMsg,
        );

        $this->sStructureType   = $_aParameters[ 0 ];
        $this->aCallbacks       = $_aParameters[ 1 ];
        $this->oMsg             = $_aParameters[ 2 ];

    }

    /**
     * Returns some framework information for debugging.
     * 
     * @since       3.5.3
     * @return      string      Some information for debugging.
     */
    public function get() {
        
        if ( ! $this->_shouldProceed() ) {
            return '';
        }
        
        return "<div class='admin-page-framework-info'>" 
                . $this->oMsg->get( 'debug_info' ) . ': '
                    . $this->getFrameworkNameVersion()
            . "</div>";
        
    }
        /**
         * @since       3.8.5
         * @return      boolean
         */
        private function _shouldProceed() {

            if ( ! $this->callBack( $this->aCallbacks[ 'show_debug_info' ], true ) ) {
                return false;
            }        
            // For the generic admin pages, do no show debug information for each section.
            return in_array( 
                $this->sStructureType, 
                array( 'widget', 'post_meta_box', 'page_meta_box', 'user_meta' )
            );
        }
    
}
