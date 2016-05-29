<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods that generates field input name HTML attribute values.
 * 
 * `
 * <input name='THIS VALUE' ... />
 * `
 * 
 * @package     AdminPageFramework
 * @subpackage  Common/Form/View/Generator
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Form_View___Generate_FieldInputName extends AdminPageFramework_Form_View___Generate_FlatFieldName {
    
    
    public $sIndex = '';
    
    /**
     * Sets up properties.
     */
    public function __construct( /* $aArguments, $isIndex, $hfCallback */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->aArguments, 
            $this->sIndex,
            $this->hfCallback,
        );
        $this->aArguments  = $_aParameters[ 0 ];        
        $this->sIndex      = ( string ) $_aParameters[ 1 ]; // a 0 value may have been interpreted as false.
        $this->hfCallback  = $_aParameters[ 2 ];
        
    }    
    
    /**
     * Returns the input tag name for the name attribute.
     * 
     * @since       2.0.0
     * @since       3.0.0       Dropped the page slug dimension. Deprecated the 'name' field key to override the name attribute since the new 'attribute' key supports the functionality.
     * @since       3.2.0       Added the $hfFilterCallback parameter.
     * @since       3.5.3       Added a type hint to the first parameter and dropped the default value to only accept an array.
     * @since       3.6.0       Moved from `AdminPageFramework_FormatField`.
     * @return      string      The generated string value.
     */
    public function get() {
        
        $_sIndex = $this->getAOrB(
            '0' !== $this->sIndex && empty( $this->sIndex ),
            '',
            "[" . $this->sIndex . "]"
        );        
        return $this->_getFiltered( $this->_getFieldName() . $_sIndex );
        
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
                        $this->aArguments,
                        $this->sIndex
                    )
                )
                : $sSubject;        
        }    

}
