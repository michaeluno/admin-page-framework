<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Handles displaying field outputs.
 *
 * @abstract
 * @since           3.8.0
 * @package         AdminPageFramework/Factory/TermMeta
 * @internal
 */
abstract class AdminPageFramework_TermMeta_View extends AdminPageFramework_TermMeta_Model {

    /**
     * Generates a name attribute value for a form input element.
     * @internal
     * @since       3.8.0
     * @return      string      the input name attribute
     */
    public function _replyToGetInputNameAttribute( /* $sNameAttribute, $aField, $sKey */ ) {

        $_aParams = func_get_args() + array( null, null, null );
        return $_aParams[ 0 ];

    }
    /**
     * Generates a flat input name whose dimensional element keys are delimited by the pipe (|) character.
     * @internal
     * @since       3.8.0
     * @return      string      the flat input name attribute
     */
    public function _replyToGetFlatInputName( /* $sFlatNameAttribute, $aField, $sKey, $sSectionIndex */ ) {
        $_aParams   = func_get_args() + array( null, null, null );
        return $_aParams[ 0 ];
    }

}
