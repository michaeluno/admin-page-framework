<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format definition arrays.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Format_NavigationTab_InPageTab extends AdminPageFramework_Format_Base {
    
    /**
     * Represents the structure of the definition array.
     */
    static public $aStructure = array();
    
    /**
     * Stores an in-page tab definition.
     */
    public $aTab        = array();
    public $aTabs       = array();
    public $aArguments  = array();
    public $oFactory    = array();
            
    /**
     * Sets up properties
     */
    public function __construct( /* array $aTab, array $aStructure, array $aTabs, array $aArguments=array(), $oFactory */ ) {
     
        $_aParameters = func_get_args() + array( 
            $this->aTab, 
            self::$aStructure, 
            $this->aTabs, 
            $this->aArguments, 
            $this->oFactory,
        );
        $this->aTab         = $_aParameters[ 0 ];
        self::$aStructure   = $_aParameters[ 1 ];
        $this->aTabs        = $_aParameters[ 2 ];
        $this->aArguments   = $_aParameters[ 3 ];
        $this->oFactory     = $_aParameters[ 4 ];
     
    }

    /**
     * 
     * @return      array       The formatted definition array.
     */
    public function get() {

        $_aTab = $this->uniteArrays(
            $this->aTab,
            array(
                'capability'        => 'manage_options',
                'show_in_page_tab'  => true,
            )
        );
        
        if ( ! $this->_isEnabled( $_aTab ) ) {
            return array();
        }
                
        $_sSlug = $this->_getSlug( $_aTab );

        $_aTab = array(
            'slug'  => $_sSlug,
            'title' => $this->aTabs[ $_sSlug ][ 'title' ],
            'href'  => $_aTab[ 'disabled' ]
                ? null
                : esc_url( 
                    $this->getElement( 
                        $_aTab, 
                        'url',  // if the 'url' argument is set, use it. Otherwise, use the below gnerated url.
                        $this->getQueryAdminURL( 
                            array( 
                                'page'  => $this->aArguments[ 'page_slug' ],
                                'tab'   => $_sSlug,
                            ), 
                            $this->oFactory->oProp->aDisallowedQueryKeys 
                        )
                    )
                ),
        ) + $this->uniteArrays(
            $_aTab,
            array(
                'attributes'    => array(
                    // 3.5.7+ Added for acceptance tests 
                    'data-tab-slug' => $_sSlug,     
                ),
            ),
            self::$aStructure
        );
          
        return $_aTab;       
        
    }
        
        /**
         * @return      boolean
         */
        private function _isEnabled( $aTab ) {
            return ! in_array(
                false,
                array(
                    ( bool ) current_user_can( $aTab[ 'capability' ] ), // whether the user has the sufficient capability level
                    ( bool ) $aTab[ 'show_in_page_tab' ], // whether it is a hidden tab                    
                    ( bool ) $aTab[ 'if' ],
                )
            );  
        }        
        
        /**
         * Determines the in-page tab slug.
         * @return      string
         * @since       3.6.0
         */
        private function _getSlug( $aTab ) {
            return isset( $aTab[ 'parent_tab_slug' ], $this->aTabs[ $aTab[ 'parent_tab_slug' ] ] )
                ? $aTab[ 'parent_tab_slug' ] 
                : $aTab[ 'tab_slug' ];            
        }
        
}