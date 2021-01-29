<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to format and generate HTML attributes for container elements of section tables.
 *
 * @package     AdminPageFramework/Common/Form/View/Attribute
 * @since       3.6.0
 * @extends     AdminPageFramework_Form_View___Attribute_Base
 * @internal
 */
class AdminPageFramework_Form_View___Attribute_SectionTableContainer extends AdminPageFramework_Form_View___Attribute_Base {

    // public $sContext    = 'section';

    /**
     * Returns an attribute array.
     *
     * @since       3.6.0
     * @return      array
     */
    protected function _getAttributes() {

        $_aSectionAttributes    = $this->uniteArrays(
            $this->dropElementsByType( $this->aArguments[ 'attributes' ] ),   // remove elements of an array.
            array(
                'id'            => $this->aArguments[ '_tag_id' ], // section-{section id}__{index}
                'class'         => $this->getClassAttribute(
                    'admin-page-framework-section',
                    $this->getAOrB(
                        $this->aArguments[ 'section_tab_slug' ],
                        'admin-page-framework-tab-content',
                        null
                    ),
                    $this->getAOrB(
                        $this->aArguments[ '_is_collapsible' ],
                        'is_subsection_collapsible', // when this is present, the section repeater script does not repeat tabs.
                        null
                    )
                ),
                // [3.3.1+] The repeatable script refers to this model value to generate new IDs.
                // 'data-id_model' => 'section-' . $this->aArguments[ 'section_id' ] . '__' . '___i___',
            )
        );

        $_aSectionAttributes[ 'class' ]   = $this->getClassAttribute(
            $_aSectionAttributes[ 'class' ],
            $this->dropElementsByType( $this->aArguments[ 'class' ] )
        );  // 3.3.1+

        $_aSectionAttributes[ 'style' ]   = $this->getStyleAttribute(
            $_aSectionAttributes[ 'style' ],
            $this->getAOrB(
                $this->aArguments[ 'hidden' ],
                'display:none',
                null
            )
        );  // 3.3.1+

        return $_aSectionAttributes;

    }

}
