<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
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
     * Determines whether the currently loaded page is of the post type page.
     * 
     * @since       3.2.0
     * @remark      The available widget areas are widgets.php and customize.php. However, some plugins implements widgets form interface in post editing page.
     * @internal
     */
    public function _isInThePage() {
        return true;
    }    
    
}