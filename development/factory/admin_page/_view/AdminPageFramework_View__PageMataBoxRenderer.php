<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
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
 */
class AdminPageFramework_View__PageMataBoxRenderer extends AdminPageFramework_WPUtility {
        
    /**
     * Renders a registered meta box.
 
     * @return      void
     * @param       string $sContext `side`, `normal`, or `advanced`.
     * @since       3.0.0
     * @since       3.6.3       Moved from `AdminPageFramework_Page_View_MetaBox`. Changed the name from `_printMetaBox()`.
     */
    public function render( $sContext ) {
       
        static $_iContainerID = 1;
        
        $_sCurrentScreenID =  $this->getCurrentScreenID();

        /* If nothing is registered do not render even the container */
        $_aMetaBoxes = $this->getElementAsArray(
            $GLOBALS,
            array( 'wp_meta_boxes', $_sCurrentScreenID, $sContext ),
            array()
        );
        if ( count( $_aMetaBoxes ) <= 0 ) {
            return;
        }
     
        echo "<div id='postbox-container-{$_iContainerID}' class='postbox-container'>";
        do_meta_boxes( '', $sContext, null ); 
        echo "</div>";
        
        $_iContainerID++;

    }
    
}