<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to format repeatable section arguments.
 *
 * @package     AdminPageFramework/Common/Form/Model/Format
 * @since       3.8.13
 * @extends     AdminPageFramework_FrameworkUtility
 * @internal
 */
class AdminPageFramework_Form_Model___Format_RepeatableField extends AdminPageFramework_Form_Model___Format_RepeatableSection {

    /**
     * @since   3.8.13
     * @return  string
     */
    protected function _getDefaultMessage() {
        return $this->_oMsg->get( 'repeatable_field_is_disabled' );
    }

}
