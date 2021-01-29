<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Handles displaying user meta field outputs.
 *
 * @abstract
 * @since           3.5.0
 * @package         AdminPageFramework/Factory/UserMeta
 */
abstract class AdminPageFramework_UserMeta_View extends AdminPageFramework_UserMeta_Model {

    /**
     * The content filter method,
     *
     * The user may just override this method instead of defining a `content_{...}` callback method.
     *
     * @since       3.5.1
     * @remark      Declare this method in each factory class as the form of parameters varies and if parameters are different, it triggers PHP strict standard warnings.
     * @param       string      $sContent       The filtering content string.
     */
    public function content( $sContent ) {
        return $sContent;
    }

    /**
     * Renders the fields.
     *
     * @remark      Called in the `_replyToDetermineToLoad()` method.
     * @since       3.5.0
     * @internal
     * @callback    action      show_user_profile
     * @callback    action      edit_user_profile
     * @callback    action      user_new_form
     */
    public function _replyToPrintFields( /* $oUser */ ) {

        $_aOutput = array();

        // Get the field outputs
        $_aOutput[] = $this->oForm->get();

        // Filter the output
        $_sOutput = $this->oUtil->addAndApplyFilters(
            $this,
            'content_' . $this->oProp->sClassName,
            $this->content( implode( PHP_EOL, $_aOutput ) )
        );

        // Do action
        $this->oUtil->addAndDoActions( $this, 'do_' . $this->oProp->sClassName, $this );

        // Output
        echo $_sOutput;

    }

}
