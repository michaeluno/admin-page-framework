<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to return CSS rules.
 *
 * @since       3.2.0
 * @package     AdminPageFramework/Common/Factory/CSS
 * @internal
 * @deprecated 3.9.0    Uses external stylesheets
 */
class AdminPageFramework_CSS {

    /**
     * Returns the framework default CSS.
     *
     * @since   3.2.0
     * @since   3.9.0   Returning an empty string.
     * @internal
     */
    static public function getDefaultCSS() {
        return '';
    }

    /**
     * Returns the framework default CSS.
     *
     * @since       3.2.0
     * @internal
     */
    static public function getDefaultCSSIE() {
        return '';
    }

}
