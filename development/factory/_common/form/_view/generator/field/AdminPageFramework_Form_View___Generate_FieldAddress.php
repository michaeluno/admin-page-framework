<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods that generates field address.
 *
 * This is similar to flat field names but this does not include an option key dimension
 * so can be used by different fields types.
 * @package     AdminPageFramework/Common/Form/View/Generator
 * @since       3.6.0
 * @internal
 */
class AdminPageFramework_Form_View___Generate_FieldAddress extends AdminPageFramework_Form_View___Generate_FlatFieldName {

    /**
     *
     * @remark      This does not apply any callback filters so the value generated here is the final form.
     * @return      string       The generated string value.
     */
    public function get() {
        return $this->_getFlatFieldName();
    }

    /**
     * Returns a name model that indicates which part is an index to be incremented / decremented.
     *
     * @return      string      The generated field name model.
     */
    public function getModel() {
        return $this->get() . '|' . $this->sIndexMark;
    }

}
