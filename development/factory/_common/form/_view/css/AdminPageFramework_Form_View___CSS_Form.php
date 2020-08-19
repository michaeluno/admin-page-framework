<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to return CSS rules for form outputs.
 *
 * @since       3.7.0
 * @package     AdminPageFramework/Common/Form/View/CSS
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
    font-size: 1em;
}
CSSRULES;

        }

}
