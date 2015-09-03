<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format form in-page tabs definition arrays.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Format_InPageTab extends AdminPageFramework_Format_Base {
    
    /**
     * Represents the structure of the sub-field definition array.
     * @since       2.0.0
     * @since       3.3.1       Moved from `AdminPageFramework_Page`.
     * @since       3.6.0       Moved from `AdminPageFramework_Page_Model`.
     * @var         array
     * @static
     * @access      private
     * @internal
     */
    static public $aStructure = array(
        'page_slug'         => null,
        'tab_slug'          => null,
        'title'             => null,
        'order'             => null,
        'show_in_page_tab'  => true,
        'parent_tab_slug'   => null,   // this needs to be set if the above show_in_page_tab is false so that the framework can mark the parent tab to be active when the hidden page is accessed.
        'url'               => null,   // 3.5.0+ This allows the user set custom link.
        'disabled'          => null,   // 3.5.10+ (boolean) If true, the link will be unlinked.
        'attributes'        => null,   // 3.5.10+ (array) Applies to the navigation tab bar element.    
    );
    
    /**
     * Stores an in-page tab definition.
     */
    public $aInPageTab = array();
    
    
    /**
     * Sets up properties
     */
    public function __construct( /* $aInPageTab */ ) {
     
        $_aParameters = func_get_args() + array( 
            $this->aInPageTab, 
        );
        $this->aInPageTab  = $_aParameters[ 0 ];
     
    }

    /**
     * 
     * @return      array       A sub-fields definition array.
     */
    public function get() {

        $_aInPageTab = $this->aInPageTab + self::$aStructure;
        
        $_aInPageTab[ 'order' ] = is_null( $_aInPageTab[ 'order' ] ) 
            ? 10 
            : $_aInPageTab[ 'order' ];
        
        return $_aInPageTab;
        
    }
    
}
