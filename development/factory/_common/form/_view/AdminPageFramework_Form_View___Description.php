<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to render form section table captions.
 * 
 * @package     AdminPageFramework
 * @subpackage  Common/Form/View
 * @since       3.6.0
 * @internal
 * @extends     AdminPageFramework_FrameworkUtility
 */
class AdminPageFramework_Form_View___Description extends AdminPageFramework_FrameworkUtility {            

    public $aDescriptions   = array();
    
    public $sClassAttribute = 'admin-page-framework-form-element-description';

    /**
     * Sets up properties.
     * @since       3.6.0
     */
    public function __construct( /* $asDescriptions, $sClassAttribute='admin-page-framework-form-element-description' */ ) {

        $_aParameters = func_get_args() + array( 
            $this->aDescriptions, 
            $this->sClassAttribute,
        );
        $this->aDescriptions    = $this->getAsArray( $_aParameters[ 0 ] );
        $this->sClassAttribute  = $_aParameters[ 1 ];

    }

    /**
     * Returns HTML formatted description blocks by the given description definition.
     * 
     * @since       3.5.3
     * @since       3.6.0       Moved from `AdminPageFramework_FormOutput`. Changed the name from `_getDescriptions()`.
     * @return      string      The description output.
     */
    public function get() {
        
        if ( empty( $this->aDescriptions ) ) {
            return '';
        }
        
        $_aOutput = array();
        foreach( $this->aDescriptions as $_sDescription ) {
            $_aOutput[] = "<p class='" . esc_attr( $this->sClassAttribute ) . "'>"
                    . "<span class='description'>"
                        . $_sDescription
                    . "</span>"
                . "</p>";
        }
        return implode( PHP_EOL, $_aOutput );
        
    }

}
