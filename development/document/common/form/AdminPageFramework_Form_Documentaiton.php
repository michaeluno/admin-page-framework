<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 */

/**
 * The form component provides functionality of building and managing forms.
 *
 * All the factory classes except the post type factory class provide means of building forms and the form component is part of them.
 *
 * <h2>Adding Form Fields</h2>
 * To add form fields, in the `setUp()` or `load()` method of the class extending a factory class, use {@link AdminPageFramework_Factory_Controller::addSettingField()} method.
 * For arguments, see the link of the method.
 * <code>
 *  $this->addSettingFields(
 *      array(
 *          'field_id'  => 'my_field_a',
 *          'title'     => 'Field A',
 *          'type'      => 'text',
 *      ),
 *      array(
 *          'field_id'  => 'my_field_b',
 *          'title'     => 'Field B',
 *          'type'      => 'text',
 *      ),
 *      array(
 *          'field_id'  => '_submit',
 *          'type'      => 'submit',
 *          'save'      => false,
 *      )
 *  );
 * </code>
 *
 * <h2>Adding Form Sections</h2>
 * If you want to group fields, use the {@link AdminPageFramework_Factory_Controller::addSettingSections()} method to put certain fields into a section.
 * For arguments, see the link of the method.
 *
 * <code>
 *      $this->addSettingSections(
 *          array(
 *              'section_id'        => '_my_post_meta_section',
 *              'title'             => 'My Post Meta Section',
 *          )
 *      );
 *      $this->addSettingFields(
 *          'my_post_meta_section', // section id
 *          array(
 *              'field_id'          => 'textarea',
 *              'title'             => __( 'Text Area', 'admin-page-framework-loader' ),
 *              'type'              => 'textarea',
 *              'rich'              => true,
 *          ),
 *          array(
 *              'field_id'          => 'color',
 *              'title'             => __( 'Color', 'admin-page-framework-loader' ),
 *              'type'              => 'color',
 *          )
 *      );
 * </code>
 *
 * <h2>Repeating and Sorting Form Sections</h2>
 * With the `repeatable` and `sortable` arguments, sections can be repeated or sorted. For the `sortable` arguments to take effect, the `repeatable` argument must be `true`.
 * <code>
 *  $this->addSettingSections(
 *      array(
 *          'section_id'    => 'my_section_id',
 *          'title'         => 'Repeatable & Sortable Sections',
 *          'repeatable'    => true,
 *          'sortable'      => true,
 *      )
 *  );
 * </code>
 *
 * With the `min` and `max` arguments, you can limit the maximum and minimum number of repeatable elements.
 * <code>
 *      array(
 *          'section_id'    => 'my_section_id',
 *          'title'         => 'Repeatable & Sortable Sections',
 *          'repeatable'    => array(
 *              'max' => 5,
 *              'min' => 2,
 *          ),
 *          'sortable'      => true,
 *      )
 * </code>
 *
 * <h2>Tabbing Sections</h2>
 * With the `section_tab_slug` argument, you can group sections and make them tabbed.
 * <code>
 *      array(
 *          'section_id'        => 'my_section_a',
 *          'section_tab_slug'  => 'tabbed_sections',   // <-- set a unique tab slug
 *          'title'             => 'Section Tab A',
 *      ),
 *      array(
 *          'section_id'        => 'my_section_b',
 *          'section_tab_slug'  => 'tabbed_sections',   // <-- use the same tab slug with the above
 *          'title'             => 'Section Tab B',
 *      ),*
 * </code>
 *
 * With the combination of the `repeatable` and `section_tab_slug` argument, you can create a repeatable tabbed section.
 * <code>
 *      array(
 *          'section_id'        => 'my_repeatable_tabbed_section',
 *          'section_tab_slug'  => 'repeatable_tabbed_section_tab',
 *          'title'             => 'Section Tab',
 *          'repeatable'        => true,
 *          'sortable'          => true,
 *      )
 * </code>
 *
 * <h2>Creating Collapsible Sections</h2>
 * With the `collapsible` argument, you can make the section collapsible and toggle the visibility.
 * <code>
 *      array(
 *          'section_id'        => 'collapsible_section_a',
 *          'title'             => 'Collapsible Section A',
 *          'collapsible'       => array(
 *              'toggle_all_button' => 'top-right',
 *          ),
 *      ),
 *      array(
 *          'section_id'        => 'collapsible_section_b',
 *          'title'             => 'Collapsible Section B',
 *          'collapsible'       => array(
 *              'is_collapsed'     => false,
 *          ),
 *      ),
 *      array(
 *          'section_id'        => 'collapsible_section_c',
 *          'title'             => 'Collapsible Section C',
 *          'collapsible'       => array(
 *              'collapse_others_on_expand' => false,
 *          ),
 *      ),
 *      array(
 *          'section_id'        => 'collapsible_section_d',
 *          'title'             => 'Collapsible Section D',
 *          'collapsible'       => array(
 *              'collapse_others_on_expand' => false,
 *              'toggle_all_button' => 'bottom-right',
 *          ),
 *      ),
 * </code>
 *
 * With the combination of the `repeatable` and `collapsible` argument, you can create repeatable & collapsible sections.
 * <code>
 *      array(
 *          'section_id'        => 'repeatable_and_collapsible',
 *          'title'             => 'Repeatable and Collapsible',
 *          'repeatable'        => true,
 *          'sortable'          => true,
 *          'collapsible'       => array(
 *              'toggle_all_button' => array( 'top-left', 'bottom-left' ),
 *              'container'         => 'section',
 *          ),
 *      )
 * </code>
 *
 * <h2>Repeating and Sorting Form Fields</h2>
 * With the `repeatable` and `sortable` arguments, fields can be repeated or sorted.
 * <code>
 *      $this->addSettingFields(
 *          array(
 *              'field_id'          => 'some_color',
 *              'title'             => 'Some Color',
 *              'type'              => 'color',
 *              'repeatable'        => true,            // <-- this part
 *              'sortable'          => true,            // <-- this part
 *          )
 *      );
 * </code>
 *
 * <h2>Nesting Form Fields</h2>
 * By setting an array holding field definitions to the `content` argument, you can nest fields. The fields set in the `content` argument will be nested.
 * <code>
 *  $this->addSettingFields(
 *      array(
 *          'field_id'      => 'Y',
 *          'title'         => __( 'Y', 'admin-page-framework-loader' ),
 *          'content'       => array(
 *              array(
 *                  'field_id'      => 'i',
 *                  'title'         => __( 'i', 'admin-page-framework-loader' ),
 *                  'type'          => 'textarea',
 *              ),
 *              array(
 *                  'field_id'      => 'ii',
 *                  'title'         => __( 'ii', 'admin-page-framework-loader' ),
 *                  'type'          => 'color',
 *              ),
 *              array(
 *                  'field_id'      => 'iii',
 *                  'title'         => __( 'iii', 'admin-page-framework-loader' ),
 *                  'repeatable'    => true,
 *                  'sortable'      => true,
 *                  'content'       => array(
 *                      array(
 *                          'field_id'      => 'a',
 *                          'title'         => __( 'a', 'admin-page-framework-loader' ),
 *                          'type'          => 'image',
 *                          'attributes'    => array(
 *                              'preview' => array(
 *                                  'style' => 'max-width: 200px;',
 *                              ),
 *                          ),
 *                      ),
 *                      array(
 *                          'field_id'      => 'b',
 *                          'title'         => __( 'b', 'admin-page-framework-loader' ),
 *                          'content'       => array(
 *                              array(
 *                                  'field_id'      => 'first',
 *                                  'title'         => __( '1st', 'admin-page-framework-loader' ),
 *                                  'type'          => 'color',
 *                                  'repeatable'    => true,
 *                                  'sortable'      => true,
 *                              ),
 *                              array(
 *                                  'field_id'      => 'second',
 *                                  'title'         => __( '2nd', 'admin-page-framework-loader' ),
 *                                  'type'          => 'size',
 *                              ),
 *                              array(
 *                                  'field_id'      => 'third',
 *                                  'title'         => __( '3rd', 'admin-page-framework-loader' ),
 *                                  'type'          => 'select',
 *                                  'label'         => array(
 *                                      'x' => 'X',
 *                                      'y' => 'Y',
 *                                      'z' => 'Z',
 *                                  ),
 *                              ),
 *                          ),
 *                      ),
 *                      array(
 *                          'field_id'      => 'c',
 *                          'title'         => __( 'c', 'admin-page-framework-loader' ),
 *                          'type'          => 'radio',
 *                          'label'         => array(
 *                              'a' => __( 'Apple', 'admin-page-framework-loader' ),
 *                              'b' => __( 'Banana', 'admin-page-framework-loader' ),
 *                              'c' => __( 'Cherry', 'admin-page-framework-loader' ),
 *                          ),
 *                          'default'       => 'b',
 *                      ),
 *                  )
 *              ),
 *          ),
 *      )
 *  );
 * </code>
 *
 * <h2>Mixing Form Fields</h2>
 * To repeat and sort a set of fields, it is useful to create an inline-mixed fields. For that use the `inline_mixed` field type and pass an array of field definitions to the `content` argument.
 *
 * <code>
 *      $this->addSettingFields(
 *          array(
 *              'field_id'      => 'checkbox_number_select',
 *              'type'          => 'inline_mixed',
 *              'title'         => __( 'Checkbox, Number & Select', 'admin-page-framework-loader' ),
 *              'content'       => array(
 *                  array(
 *                      'field_id'        => 'enable',
 *                      'type'            => 'checkbox',
 *                      'label_min_width' => '',
 *                      'label'           => __( 'Do something in', 'admin-page-framework-loader' ),
 *                  ),
 *                  array(
 *                      'field_id'        => 'interval',
 *                      'type'            => 'number',
 *                      'label_min_width' => '',
 *                      'default'         => 3,
 *                      'attributes'      => array(
 *                          'style'     => 'width: 80px',
 *                      ),
 *                  ),
 *                  array(
 *                      'field_id'        => 'interval_unit',
 *                      'type'            => 'select',
 *                      'label_min_width' => '',
 *                      'label'           => array(
 *                          'hour'    => __( 'hours', 'admin-page-framework-loader' ),
 *                          'day'     => __( 'days', 'admin-page-framework-loader' ),
 *                          'week'    => __( 'weeks', 'admin-page-framework-loader' ),
 *                      ),
 *                      'default'         => 'day',
 *                  ),
 *                  array(
 *                      'field_id'      => '_text',
 *                      'content'       => __( 'to do something else.', 'admin-page-framework-loader' ),
 *                  ),
 *              ),
 *          ),
 *          array(
 *              'field_id'      => 'text_number',
 *              'type'          => 'inline_mixed',
 *              'title'         => __( 'Text & Number', 'admin-page-framework-loader' ),
 *              'repeatable'    => true,
 *              'content'       => array(
 *                  __( 'Server', 'admin-page-framework-loader' ),
 *                  array(
 *                      'field_id'        => 'server',
 *                      'type'            => 'text',
 *                      'default'         => 'www.wordpress.org',
 *                      'attributes'      => array(
 *                          'fieldset'  => array(
 *                              'style'     => 'min-width: 400px;',
 *                          )
 *                      ),
 *                  ),
 *                  __( 'Port', 'admin-page-framework-loader' ),
 *                  array(
 *                      'field_id'        => 'port',
 *                      'type'            => 'number',
 *                      'label_min_width' => '',
 *                      'default'         => 3,
 *                      'attributes'      => array(
 *                          // 'style'     => 'width: 80px',
 *                      ),
 *                  ),
 *
 *              ),
 *          ),
 *          array(
 *              'field_id'      => 'shipping_address',
 *              'title'         => __( 'Shipping Information', 'admin-page-framework-loader' ),
 *              'type'          => 'inline_mixed',
 *              'repeatable'    => true,
 *              'sortable'      => true,
 *              'content'       => array(
 *                  array(
 *                      'field_id'      => 'first_name',
 *                      'type'          => 'text',
 *                      'title'         => __( 'First Name', 'admin-page-framework-loader' ),
 *                      'attributes'    => array(
 *                          'fieldset'  => array(
 *                              'style'  => 'width: 48%; padding-right: 2%;',
 *                          ),
 *                      ),
 *                  ),
 *                  array(
 *                      'field_id'      => 'last_name',
 *                      'type'          => 'text',
 *                      'title'         => __( 'Last Name', 'admin-page-framework-loader' ),
 *                      'attributes'    => array(
 *                          'fieldset'  => array(
 *                              'style'  => 'width: 48%; padding-right: 2%;',
 *                          ),
 *
 *                      ),
 *                  ),
 *                  array(
 *                      'field_id'      => 'mailing_address',
 *                      'type'          => 'text',
 *                      'title'         => __( 'Street Address', 'admin-page-framework-loader' ),
 *                      'attributes'    => array(
 *                          'fieldset'  => array(
 *                              'style'  => 'width: 98%; padding-right: 2%;',
 *                          ),
 *                      ),
 *                  ),
 *                  array(
 *                      'field_id'      => 'city',
 *                      'type'          => 'text',
 *                      'title'         => __( 'City/Town', 'admin-page-framework-loader' ),
 *                      'attributes'    => array(
 *                          'fieldset'  => array(
 *                              'style'  => 'width: 36%; padding-right: 2%;',
 *                          ),
 *                      ),
 *                  ),
 *                  array(
 *                      'field_id'      => 'state',
 *                      'type'          => 'text',
 *                      'title'         => __( 'State/Province', 'admin-page-framework-loader' ),
 *                      'attributes'    => array(
 *                          'fieldset'  => array(
 *                              'style'  => 'width: 36%; padding-right: 2%;',
 *                          ),
 *                      ),
 *                  ),
 *                  array(
 *                      'field_id'      => 'zip',
 *                      'type'          => 'text',
 *                      'title'         => __( 'Zip/Postal Code', 'admin-page-framework-loader' ),
 *                      'attributes'    => array(
 *                          'fieldset'  => array(
 *                              'style'  => 'width: 22%; padding-right: 2%;',
 *                          ),
 *                      ),
 *                  ),
 *                  array(
 *                      'field_id'      => 'telephone',
 *                      'type'          => 'text',
 *                      'title'         => __( 'Tel. No.', 'admin-page-framework-loader' ),
 *                      'attributes'    => array(
 *                          'fieldset'  => array(
 *                              'style'  => 'width: 31%; padding-right: 2%;',
 *                          ),
 *                      ),
 *                  ),
 *                  array(
 *                      'field_id'      => 'fax',
 *                      'type'          => 'text',
 *                      'title'         => __( 'Fax No.', 'admin-page-framework-loader' ),
 *                      'attributes'    => array(
 *                          'fieldset'  => array(
 *                              'style'  => 'width: 31%; padding-right: 2%;',
 *                          ),
 *                      ),
 *                  ),
 *                  array(
 *                      'field_id'      => 'email',
 *                      'type'          => 'text',
 *                      'title'         => __( 'Email', 'admin-page-framework-loader' ),
 *                      'attributes'    => array(
 *                          'fieldset'  => array(
 *                              'style'  => 'width: 32%; padding-right: 2%;',
 *                          ),
 *                      ),
 *                  ),
 *              ),
 *          )
 *      );
 * </code>
 *
 * <h2>Form Specific Common Hooks</h2>
 *
 * Except the Post Type factory class, all the other factory classes have the ability to build form element.
 *
 * There are hooks that are specific to the tasks of dealing with forms.
 *
 * <h3>Filter Hooks</h3>
 * <ul>
 *     <li>**field_types_{instantiated class name}** – receives the field type definition array. The first parameter: the field type definition array.</li>
 *     <li>**section_head_{instantiated class name}_{section ID}** – receives the title and the description output of the given form section ID. The first parameter: the output string.</li>
 *     <li>**field_{instantiated class name}_{field ID}** – receives the form input field output of the given input field ID. The first parameter: output string. The second parameter: the array of option.</li>
 *     <li>**field_{instantiated class name}_{field ID}** – receives the form input field output of the given input field ID that does not have a section. The first parameter: output string. The second parameter: the array of option.</li>
 *     <li>**field_{instantiated class name}_{section id}_{field ID}** – [3.0.0+] receives the form input field output of the given input field ID that has a section. The first parameter: output string. The second parameter: the array of option.</li>
 *     <li>**sections_{instantiated class name}** – receives the registered section arrays. The first parameter: sections container array.</li>
 *     <li>**fields_{instantiated class name}** – receives the registered field arrays. The first parameter: fields container array.</li>
 *     <li>**fields_{instantiated class name}_{section id}** – [3.0.0+] receives the registered field arrays which belong to the specified section. The first parameter: fields container array.</li>
 *     <li>**field_types_admin_page_framework** – [3.5.0+] receives a field type definitions array. The first parameter: a field type definitions array.</li>
 *     <li>**field_types_{instantiated class name}** – receives a field type definitions array. The first parameter: a field type definitions array.</li>
 *     <li>**field_definition_{instantiated class name}** – [3.1.0+] receives all the form field definition arrays of set in the class. The first parameter: the field definition arrays.</li>
 *     <li>**field_definition_{instantiated class name}_{field ID}** – [3.0.2+] receives the form field definition array of the given input field ID that does not have a section. The first parameter: the field definition array.</li>
 *     <li>**field_definition_{instantiated class name}_{section id}_{field ID}** – [3.0.2+] receives the form field definition array of the given input field ID that has a section. The first parameter: the field definition array. The second parameter: the integer representing sub-section index if the field belongs to a sub-section.</li>
 *     <li>**validation_{instantiated class name}** – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 *     <li>**validation_{instantiated class name}_{field id}** – [3.0.0+] receives the form submission value of the field that does not have a section. The first parameter: ( string|array ) submitted input value. The second parameter: ( string|array ) the old value stored in the database. The third parameter: ( object ) [3.1.0+] the caller object.</li>
 *     <li>**validation_{instantiated class name}_{section_id}_{field id}** – [3.0.0+] receives the form submission value of the field that has a section. The first parameter: ( string|array ) submitted input value. The second parameter: ( string|array ) the old value stored in the database. The third parameter: ( object ) [3.1.0+] the caller object.</li>
 *     <li>**validation_{instantiated class name}_{section id}** – [3.0.0+] receives the form submission values that belongs to the section.. The first parameter: ( array ) the array of submitted input values that belong to the section. The second parameter: ( array ) the array of the old values stored in the database. The third parameter: ( object ) [3.1.0+] the caller object.</li>
 *     <li>**validation_saved_options_{instantiated class name}** – [3.1.2+] receives the saved form options as an array. The first parameter: the stored options array. The second parameter: the caller object.</li>
 *     <li>**validation_saved_options_without_dynamic_elements_{instantiated class name}** – [3.4.1+] receives the saved form options as an array without dynamic elements such as repeatable and sortable fields. The first parameter: the stored options array. The second parameter: the caller object.</li>
 *     <li>**options_{instantiated class name}** – [3.1.0+] receives the option array.</li>
 * </ul>
 *
 * <h4>Callback Methods</h4>
 * To register callbacks to those hooks, use the [add_filter](https://developer.wordpress.org/reference/functions/add_filter/) function.
 *
 * <code>
 * function validateMyClassForm( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {
 *      // parse the $aInputs array and check errors.
 *      return $aInputs;
 * }
 * add_filter( 'validation_MyClass', 'validateMyClassForm', 10, 4 );
 * </code>
 *
 * Or define a method with the hook name.
 * <code>
 * class MyClass extends AdminPageFramework {
 *
 *      ...
 *
 *      public function validation_MyClass( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {
 *          // do validation here.
 *          return $aInputs;
 *      }
 *
 *      ...
 *
 * }
 * </code>
 *
 * @image       http://admin-page-framework.michaeluno.jp/image/common/form/form_component.png
 * @since       3.8.0
 * @package     AdminPageFramework/Common/Form
 * @heading     Form Component
 */
class AdminPageFramework_Form_Documentaiton {}
