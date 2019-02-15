<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazon auto links/
 * Copyright (c) 2013-2019, Michael Uno
 *
 */

/**
 * @since       3.5.12
 */
class APF_Demo_PageMetaBox__Submit extends AdminPageFramework_PageMetaBox {

    /**
     * Sets up form fields.
     */
    public function setUp() {

        $this->addSettingFields(
            array(
                'field_id'          => '__submit',
                'type'              => 'submit',
                'value'             => __( 'Save', 'amazon-auto-links' ),
                'save'              => false,
                'label_min_width'   => '100%',
                'attributes'        => array(
                    'field'    => array(
                        'style' => 'width: 100%; text-align: center;',
                    ),
                )
            )
        );

    }


}
