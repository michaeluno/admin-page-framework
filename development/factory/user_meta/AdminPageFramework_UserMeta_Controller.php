<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides methods to manipulate the factory behaviour.
 *
 * @abstract
 * @since           3.5.0
 * @package         AdminPageFramework/Factory/UserMeta
 */
abstract class AdminPageFramework_UserMeta_Controller extends AdminPageFramework_UserMeta_View {

    /**
     * The set up method.
     *
     * <h4>Example</h4>
     *     public function setUp() {
     *
     *          $this->addSettingFields(
     *              array(
     *                  'field_id'      => 'text_field',
     *                  'type'          => 'text',
     *                  'title'         => __( 'Text', 'admin-page-framework-demo' ),
     *                  'repeatable'    => true,
     *                  'sortable'      => true,
     *                  'description'   => 'Type something here.',
     *              ),
     *              array(
     *                  'field_id'      => 'text_area',
     *                  'type'          => 'textarea',
     *                  'title'         => __( 'Text Area', 'admin-page-framework-demo' ),
     *                  'default'       => 'Hi there!',
     *              ),
     *              array(
     *                  'field_id'      => 'radio_buttons',
     *                  'type'          => 'radio',
     *                  'title'         => __( 'Radio', 'admin-page-framework-demo' ),
     *                  'label'         => array(
     *                      'a' => 'A',
     *                      'b' => 'B',
     *                      'c' => 'C',
     *                  ),
     *                  'default'       => 'a',
     *              )
     *          );
     *
     *      }
     *
     * @remark      should be overridden by the user definition class.
     * @since       3.5.0
     */
    public function setUp() {}

}
