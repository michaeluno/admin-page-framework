<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format collapsible section argument arrays.
 * 
 * @package     AdminPageFramework
 * @subpackage  Common/Form/Model/Format
 * @since       3.6.0
 * @extends     AdminPageFramework_FrameworkUtility
 * @internal
 */
class AdminPageFramework_Form_Model___Format_CollapsibleSection extends AdminPageFramework_FrameworkUtility {
    
    /**
     * Represents the structure of the 'collapsible' argument.
     * @since       3.4.0
     * @since       3.6.0       Moved from `AdminPageFramework_FormDefinition`.
     */
    static public $aStructure = array(
        'title'                     => null,        // (string)  the section title will be assigned by default in the section formatting method.
        'is_collapsed'              => true,        // (boolean) whether it is already collapsed or expanded
        'toggle_all_button'         => null,        // (boolean|string|array) the position of where to display the toggle-all button that toggles the folding state of all collapsible sections. Accepts the following values. 'top-right', 'top-left', 'bottom-right', 'bottom-left'. If true is passed, the default 'top-right' will be used. To not to display, do not set any or pass `false` or `null`.
        'collapse_others_on_expand' => true,        // (boolean) whether the other collapsible sections should be folded when the section is unfolded.
        'container'                 => 'sections',  // (string) the container element that collapsible styling gets applied to. Either 'sections' or 'section' is accepted.
        'type'                      => 'box',       // 3.7.0+  (string)  supported types 'box', 'button' Default: `box`. The `button` type is only supported when the `container` argument is `section`.
    );   
    
    public $abCollapsible = false;
    
    public $sTitle        = '';
    
    /**
     * Stores a section definition array.
     * 
     * @remark      The framework will not pass this parameter when formatting a section definition array.
     * It will be passed when the framework is rendering a form table to generate collapsible elements.
     */
    public $aSection      = array();
    
    /**
     * Sets up properties.
     */
    public function __construct( /* $abCollapsible, $sTitle, $aSection */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->abCollapsible, 
            $this->sTitle,
            $this->aSection,
        );
        $this->abCollapsible      = $_aParameters[ 0 ];        
        $this->sTitle             = $_aParameters[ 1 ];        
        $this->aSection           = $_aParameters[ 2 ];
        
    }
    
    /**
     * 
     * @return      array|boolean       The formatted definition array.
     */
    public function get() {
        
        if ( empty( $this->abCollapsible ) ) {
            return $this->abCollapsible;
        } 
        
        return $this->_getArguments( 
            $this->abCollapsible,
            $this->sTitle,
            $this->aSection
        );

    }
        /**
         * @since       3.6.0
         * @return      array
         */
        private function _getArguments( $abCollapsible, $sTitle, array $aSection ) {
            
            $_aCollapsible = $this->getAsArray( $this->abCollapsible ) + array(
                'title'     => $sTitle,
            ) +  self::$aStructure;
            
            $_aCollapsible[ 'toggle_all_button' ] = implode( 
                ',', 
                $this->getAsArray( $_aCollapsible[ 'toggle_all_button' ] ) 
            );
            
            if ( ! empty( $aSection ) ) {
                $_aCollapsible[ 'toggle_all_button' ] = $this->_getToggleAllButtonArgument( 
                    $_aCollapsible[ 'toggle_all_button' ], 
                    $aSection
                );
            }
            
            // [3.8.5+] An empty string `''` is considered as a value by a data attribute parser function since v3.8.4. So set it explicitly to `false` here.
            $_aCollapsible[ 'toggle_all_button' ] = $this->getAOrB( 
                '' === $_aCollapsible[ 'toggle_all_button' ],
                false,
                $_aCollapsible[ 'toggle_all_button' ] 
            );
            
            return $_aCollapsible;            
            
        }
          
            /**
             * Sanitizes the toggle all button argument.
             * @since       3.4.0
             * @since       3.6.0       Moved from `AdminPageFramework_FormPart_Table_Base`. Changed the name from `_sanitizeToggleAllButtonArgument`.
             * @param       string      $sToggleAll         Comma delimited button positions.
             * @param       array       $aSection           The section definition array.
             * @return      string
             * @remark      Some checked elements including `_is_first_index` and `_is_last_index` are assigned during rendering the section table.
             * So they won't be available when the user add a section and the framework calls this formatting method. It means the framework will call this formatting method again
             * when it tries to render a section.
             */
            private function _getToggleAllButtonArgument( $sToggleAll, array $aSection ) {
                
                if ( ! $aSection[ 'repeatable' ] ) {            
                    return $sToggleAll;
                }
                
                // If the both first index and last index is true, it means there is only one section. Treat it as a single non-repeatable section.
                if ( $aSection[ '_is_first_index' ] && $aSection[ '_is_last_index' ] ) {
                    return $sToggleAll;
                }
                
                // Disable the toggle all button for middle sub-sections in repeatable sections.
                if ( ! $aSection[ '_is_first_index' ] && ! $aSection[ '_is_last_index' ] ) {
                    return 0;
                }            
                
                $_aToggleAll = $this->getAOrB(
                    true === $sToggleAll || 1 ===  $sToggleAll, // evaluate
                    array( 'top-right', 'bottom-right' ),   // if true
                    explode( ',', $sToggleAll ) // if false
                );            
                $_aToggleAll = $this->getAOrB(
                    $aSection[ '_is_first_index' ],
                    $this->dropElementByValue( $_aToggleAll, array( 1, true, 0, false, 'bottom-right', 'bottom-left' ) ),
                    $_aToggleAll
                );
                $_aToggleAll = $this->getAOrB(
                    $aSection[ '_is_last_index' ],
                    $this->dropElementByValue( $_aToggleAll, array( 1, true, 0, false, 'top-right', 'top-left' ) ),
                    $_aToggleAll
                );
                $_aToggleAll = $this->getAOrB(
                    empty( $_aToggleAll ),
                    array( 0 ),
                    $_aToggleAll
                );
                return implode( ',', $_aToggleAll );
                
            }                        
          
}
