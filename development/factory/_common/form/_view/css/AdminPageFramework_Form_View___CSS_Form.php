<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to return CSS rules for form outputs.
 *
 * @since       3.7.0
 * @package     AdminPageFramework
 * @subpackage  Form
 * @internal
 */
class AdminPageFramework_Form_View___CSS_Form extends AdminPageFramework_Form_View___CSS_Base {
    
    /**
     * @since       3.7.0
     * @return      string
     */
    protected function _get() {

        $_sSpinnerURL  = esc_url( admin_url( '/images/wpspin_light-2x.gif' ) );
        return <<<CSSRULES
.admin-page-framework-form-warning {
    font-weight: bold;
    color: red;
    font-size: 1.32em;
}
CSSRULES;
            
        }
    
}