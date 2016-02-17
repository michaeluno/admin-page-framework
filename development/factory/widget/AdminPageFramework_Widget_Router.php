<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides routing methods for the widget factory class.
 * 
 * @abstract
 * @since       3.2.0
 * @package     AdminPageFramework
 * @subpackage  Widget
 * @internal
 */
abstract class AdminPageFramework_Widget_Router extends AdminPageFramework_Factory {

    /**
     * Sets up hooks and properties.
     * 
     * @since       3.2.0
     * @since       3.7.10      Moved from `AdminPageFramework_Widget_Controller`.
     * @internal    
     */
    public function __construct( $oProp ) {
        
        parent::__construct( $oProp );
        
        // Other admin page framework factory classes uses wp_loaded hook but widget_init hook is called before that.
        // So we use widgets_init hook for this factory.
        $this->oUtil->registerAction( 'widgets_init', array( $this, '_replyToDetermineToLoad' ) );
        
    }

    /**
     * Loads sub-components.
     * 
     * Do not load components but let them auto-load to save performance because the widget factory is loaded anywhere.
     * 
     * @internal
     * @callback    action      current_screen
     * @return      void
     * @since       3.7.10
     */
    public function _replyToLoadComponents( /* $oScreen */ ) {
        return;
    }

}
