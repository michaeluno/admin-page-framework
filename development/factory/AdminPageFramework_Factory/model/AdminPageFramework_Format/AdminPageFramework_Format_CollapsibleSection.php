<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format form sub-field definition arrays.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Format_CollapsibleSection extends AdminPageFramework_Format_Base {
    
    /**
     * Represents the structure of the 'collapsible' argument.
     * @since       3.4.0
     * @since       3.6.0       Moved from `AdminPageFramework_FormElement`.
     */
    static public $aStructure = array(
        'title'                     => null,        // (string)  the section title will be assigned by default in the section formatting method.
        'is_collapsed'              => true,        // (boolean) whether it is already collapsed or expanded
        'toggle_all_button'         => null,        // (boolean|string|array) the position of where to display the toggle-all button that toggles the folding state of all collapsible sections. Accepts the following values. 'top-right', 'top-left', 'bottom-right', 'bottom-left'. If true is passed, the default 'top-right' will be used. To not to display, do not set any or pass `false` or `null`.
        'collapse_others_on_expand' => true,        // (boolean) whether the other collapsible sections should be folded when the section is unfolded.
        'container'                 => 'sections'   // (string) the container element that collapsible styling gets applied to. Either 'sections' or 'section' is accepted.
    );   
    
    public $abCollapsible = false;
    
    public $sTitle = '';
    
    /**
     * Sets up properties.
     */
    public function __construct( /* $abCollapsible, $sTitle */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->abCollapsible, 
            $this->sTitle
        );
        $this->abCollapsible             = $_aParameters[ 0 ];        
        $this->sTitle                    = $_aParameters[ 1 ];        
        
    }
    
    /**
     * 
     * @return      array|boolean       The formatted definition array.
     */
    public function get() {
        
        if ( empty( $this->abCollapsible ) ) {
            return $this->abCollapsible;
        } 
        
        $_aCollapsible = $this->getAsArray( $this->abCollapsible ) + array(
            'title'     => $this->sTitle,
        ) +  self::$aStructure;
        $_aCollapsible[ 'toggle_all_button' ] = implode( 
            ',', 
            $this->getAsArray( $_aCollapsible[ 'toggle_all_button' ] ) 
        );
        return $_aCollapsible;

    }
           
}