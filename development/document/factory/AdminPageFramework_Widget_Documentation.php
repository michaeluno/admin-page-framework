<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides an abstract base to create widgets and their forms.
 *
 * This factory class lets the user create a widget and define the outputs based on the form field inputs.
 *
 * <h2>Creating a Widget and Form Fields</h2>
 * 1. Extend the {@link AdminPageFramework_Widget} factory class.
 * 2. Define the form in {@link AdminPageFramework_Widget_Controller::load()} method. By adding a `text` field with the field ID of `title`, a title output will be inserted above the widget output in the front end defined in the `content()` method.
 *
 * 3. Instantiate the class by passing the widget title in the first parameter.
 *
 * <h2>Displaying Widget Contents by Retrieving Widget Form Data</h2>
 * Define the front-end output of the widget in {@link AdminPageFramework_Widget_View::content()} method.
 *
 * <h2>Example</h2>
 * <code>
 *  class APFDoc_ExampleWidget extends AdminPageFramework_Widget {
 *
 *      public function load( $oAdminWidget ) {
 *
 *          $this->addSettingFields(
 *              array(
 *                  'field_id'      => 'title',
 *                  'type'          => 'text',
 *                  'title'         => 'Title',
 *              ),
 *              array(
 *                  'field_id'      => 'image',
 *                  'type'          => 'image',
 *                  'title'         => 'Image',
 *              )
 *          );
 *
 *      }
 *
 *      public function content( $sContent, $aArguments, $aFormData ) {
 *
 *          $_sImageURL = esc_url( $this->oUtil->getElement( $aFormData, 'image' ) );
 *          return $sContent
 *              . "<img src='{$_sImageURL}' />";
 *
 *      }
 *
 *  }
 *  new APFDoc_ExampleWidget(
 *      'APF Documentation Example',    // widget title
 *      array(
 *          'description'   => 'Shows an image.',
 *      )
 *  );
 * </code>
 *
 * <h2>Hooks</h2>
 * See the Hooks section of the [Factory package summary](./package-AdminPageFramework.Common.Factory.html) page.
 *
 * @image       http://admin-page-framework.michaeluno.jp/image/factory/widget.png
 * @image       http://admin-page-framework.michaeluno.jp/image/factory/widget_form.png
 * @abstract
 * @since       3.3.0
 * @package     AdminPageFramework/Factory/Widget
 * @heading     Widget
 * @example     ../../../example/APF_Widget.php
 */
abstract class AdminPageFramework_Widget_Documentation {}
