<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to format and generate <em>section table</em> HTML attributes.
 *
 * @package     AdminPageFramework/Common/Form/View/Attribute
 * @since       3.6.0
 * @extends     AdminPageFramework_Form_View___Attribute_Base
 * @internal
 */
class AdminPageFramework_Form_View___Attribute_SectionTable extends AdminPageFramework_Form_View___Attribute_Base {

    public $sContext    = 'section_table';

    /**
     * Returns an attribute array.
     *
     * @since       3.6.0
     * @return      array
     */
    protected function _getAttributes() {
        return array(
            'id'    => 'section_table-' . $this->aArguments[ '_tag_id' ], // 'section-' . $sSectionID . '__' . $iSectionIndex
            'class' =>  $this->getClassAttribute(
                'form-table',
                'admin-page-framework-section-table'   // referred by the collapsible section script
            ),
        );
    }

}
