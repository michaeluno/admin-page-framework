<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to return CSS rules for form outputs.
 *
 * @since       3.7.0
 * @package     AdminPageFramework/Common/Form/View/CSS
 * @internal
 */
class AdminPageFramework_Form_View___CSS_Loading extends AdminPageFramework_Form_View___CSS_Base {
    
    /**
     * @since       3.7.0
     * @return      string
     */
    protected function _get() {

        $_sSpinnerPath = $this->getWPAdminDirPath() . '/images/wpspin_light-2x.gif';
        if ( ! file_exists( $_sSpinnerPath ) ) {
            return '';
        }
        $_sSpinnerURL  = esc_url( admin_url( '/images/wpspin_light-2x.gif' ) );
        return <<<CSSRULES
.admin-page-framework-form-loading {
    position: absolute;
    background-image: url({$_sSpinnerURL});
    background-repeat: no-repeat;
    background-size: 32px 32px;
    background-position: center;     
    display: block !important;
    width: 92%;
    height: 70%;
    opacity: 0.5;
}
CSSRULES;
            
        }
    
}
