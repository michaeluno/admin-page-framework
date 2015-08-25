<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods that generates section related strings.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.0
 * @internal
 */
abstract class AdminPageFramework_Generate_Section_Base extends AdminPageFramework_Generate_Base {
    
    
    /**
     * A callback function to filter the generated id.
     */
    public $hfCallback = null;
    
    public $sIndexMark = '___i___';
    
    /**
     * Sets up properties.
     */
    public function __construct( /* $aArguments, $hfCallback */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aArguments, 
            $this->hfCallback,
        );
        $this->aArguments = $_aParameters[ 0 ];        
        $this->hfCallback  = $_aParameters[ 1 ];

    }    
    
    /**
     * Returns a model that indicates where the index digit is placed.
     * @since       3.6.0
     * @return      string
     */
    public function getModel() {
        return '';
    }
 
    /**
     * Applies the subject string to the set callback filter function.
     * @since       3.6.0
     */
    protected function _getFiltered( $sSubject ) {
        return is_callable( $this->hfCallback )
            ? call_user_func_array( 
                $this->hfCallback, 
                array( 
                    $sSubject,
                    $this->aArguments, // aSectionset
                )
            )
            : $sSubject;        
    }
       
}