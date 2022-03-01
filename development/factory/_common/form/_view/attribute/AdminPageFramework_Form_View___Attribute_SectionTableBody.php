<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to format and generate the <em>body</em> tag of <em>section table</em> HTML attributes.
 *
 * @package     AdminPageFramework/Common/Form/View/Attribute
 * @since       3.6.0
 * @extends     AdminPageFramework_Form_View___Attribute_Base
 * @internal
 */
class AdminPageFramework_Form_View___Attribute_SectionTableBody extends AdminPageFramework_Form_View___Attribute_Base {

    public $sContext    = 'section_table_content';

    /**
     * Returns an attribute array.
     *
     * @since       3.6.0
     * @return      array
     */
    protected function _getAttributes() {

        $_sCollapsibleType = $this->getElement(
            $this->aArguments,
            array( 'collapsible', 'type' ),
            'box'
        );
        return array(
            'class' => $this->getAOrB(
                $this->aArguments[ '_is_collapsible' ],
                'admin-page-framework-collapsible-section-content' . ' '
                    . 'admin-page-framework-collapsible-content' . ' '
                    . 'accordion-section-content' . ' '
                    . 'admin-page-framework-collapsible-content-type-' . $_sCollapsibleType,
                null
            ),
        );
    }

}
