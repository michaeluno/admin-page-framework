<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods that generates field related strings.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Generate_Field_Base extends AdminPageFramework_Generate_Base {
    
    /**
     * A field=set definition array from witch the id is generated.
     */
    public $aFieldset  = array();
    
    /**
     * A callback function to filter the generated id.
     */
    public $hfCallback = null;
    
    /**
     * Sets up properties.
     */
    public function __construct( /* $aFieldset, $hfCallback */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aFieldset, 
            $this->hfCallback,
        );
        $this->aFieldset   = $_aParameters[ 0 ];        
        $this->hfCallback  = $_aParameters[ 1 ];
        
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
                    $sSubject
                )
            )
            : $sSubject;        
    }
    
    /**
     * Checks whether a section is set.
     * 
     * @internal
     * @since       3.5.3
     * @since       3.6.0       Moved from `AdminPageFramework_FormDefinition`.
     * @param       array       $aFieldset     a field definition array.
     * @return      boolean
     */
    protected function _isSectionSet() {
        return isset( $this->aFieldset[ 'section_id' ] ) 
            && $this->aFieldset[ 'section_id' ] 
            && '_default' !== $this->aFieldset['section_id'];
    }       
    
}
