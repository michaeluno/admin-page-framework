<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Enqueues page resources set with the `style` and `script` arguments.
 *
 * @abstract
 * @since           3.6.3
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 * @extends         AdminPageFramework_FrameworkUtility
 */
class AdminPageFramework_View__PageMataBoxRenderer extends AdminPageFramework_FrameworkUtility {
           
    /**
     * Renders a registered meta box.
 
     * @return      void
     * @param       string $sContext `side`, `normal`, or `advanced`.
     * @since       3.0.0
     * @since       3.6.3       Moved from `AdminPageFramework_Page_View_MetaBox`. Changed the name from `_printMetaBox()`.
     */
    public function render( $sContext ) {
               
        // If nothing is registered do not render even the container.
        if ( ! $this->doesMetaBoxExist() ) {
            return;
        }
        
        $this->_doRender( 
            $sContext,
            ++self::$_iContainerID
        );

    }
        /**
         * Stored meta box container id. 
         * @remark      It should start from 1 (1-base) but here 0 is set because it gets initially incremented.
         * @since       3.7.0
         */
        private static $_iContainerID = 0;
            
        /**
         * Renders a metabox.
         */
        private function _doRender( $sContext, $iContainerID ) {
            
            echo "<div id='postbox-container-{$iContainerID}' class='postbox-container'>";
            do_meta_boxes( '', $sContext, null ); 
            echo "</div>";  
            
        }
    
}
