<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 */

/**
 * Provides the base factory component to be extended by factory classes.
 *
 * The framework abstract classes extend the `AdminPageFramework_Factory` class to create their own.
 * So some common public methods are defined in this base component.
 *
 * For form related hooks, see [Form](./package-AdminPageFramework_Form_Documentation).
 *
 * <h2>Common Hooks</h2>
 *
 * When the user instantiates the class extending a factory class, action and filter hooks will be crated. For details about WordPress hooks, see [Hooks](https://codex.wordpress.org/Plugin_API/Hooks).
 *
 * The class methods corresponding to the name of the below actions and filters can be extended to do additional tasks such as setting a transient or modifying the data. Those methods are the callbacks of the filters and the actions.
 *
 * There are common hooks shared among different factory classes in addition to the factory-specific hooks provided by individual factories.
 *
 * <h3>Action Hooks</h3>
 * <ul>
 *     <li>**start_{instantiated class name}** – triggered at the end of the class constructor. This will be triggered in any admin page except admin-ajax.php. The class object will be passed to the first parameter [3.1.3+].</li>
 *     <li>**set_up_{instantiated class name}** – [3.1.3+] triggered after the setUp() method is called. The class object will be passed to the first parameter.</li>
 *     <li>**load_{instantiated class name}** – [2.1.0+] triggered when the framework's page is loaded before the header gets sent. This will not be triggered in the admin pages that are not registered by the framework. The first parameter: class object [3.1.2+].</li>
 *     <li>**load_after_{instantiated class name}** – [3.1.3+] triggered when one of the framework's pages is loaded before the header gets sent. This will not be triggered in the admin pages that are not registered by the framework. The first parameter: class object.</li>
 * </ul>
 *
 * <h3>Filter Hooks</h3>
 * <ul>
 *     <li>**style_common_admin_page_framework** –  receives the output of the base CSS rules applied to common CSS rules shared by the framework.</li>
 *     <li>**style_common_{instantiated class name}** –  receives the output of the base CSS rules applied to the pages of the associated post types with the meta box.</li>
 *     <li>**style_ie_common_{instantiated class name}** –  receives the output of the base CSS rules for Internet Explorer applied to the pages of the associated post types with the meta box.</li>
 *     <li>**style_{instantiated class name}** –  receives the output of the CSS rules applied to the pages of the associated post types with the meta box.</li>
 *     <li>**style_ie_{instantiated class name}** –  receives the output of the CSS rules for Internet Explorer applied to the pages of the associated post types with the meta box.</li>
 *     <li>**script_common_{instantiated class name}** – receives the output of the base JavaScript scripts applied to the pages of the associated post types with the meta box.</li>
 *     <li>**script_{instantiated class name}** – receives the output of the JavaScript scri
 * </ul>
 *
 * <h3>Callback Methods</h3>
 * To register callbacks to those hooks, simply use the [add_action()](https://developer.wordpress.org/reference/functions/add_action/) or [add_filter](https://developer.wordpress.org/reference/functions/add_filter/) functions.
 *
 * <h4>Action Hooks</h4>
 * For action hooks, use [add_action()](https://developer.wordpress.org/reference/functions/add_action/).
 * <code>
 * function startMyClass( $oFactory ) {
 *      // do some tasks when the class object is constructed.
 * }
 * add_action( 'start_MyClass', 'startMyClass' );
 * </code>
 *
 * Or define a method with the hook name. The listed hooks can also serve as a method name defined in the class.
 * <code>
 * class MyClass extends AdminPageFramework {
 *
 *      ...
 *
 *      // load_{instantiated class name}
 *      public function load_MyClass( $oFactory )  {
 *          // do set-ups here
 *      }
 *
 *      ...
 * }
 * </code>
 *
 * <h4>Filter Hooks</h4>
 * For filter hooks, use [add_filter()](https://developer.wordpress.org/reference/functions/add_filter/).
 * <code>
 * function styleMyClassForm( $sCSSRules ) {
 *      return $sCSSRules
 *          . '<!-- Add your Css ruels here. -->';
 * }
 * add_filter( 'style_MyClass', 'styleMyClassForm' );
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
 * <h3>Remark</h3>
 * - If the class name contains backslashes (`\`) such as `Foo\Bar\MyClass` using a name space, in the callback method name, the backslashes will be converted to an underscore (`_`). e.g. `Foo\Bar\MyClass` becomes `Foo_Bar_MyClass`. So the method name will be `validation_Foo_Bar_MyClass()`. This does not apply to the action and filter names.
 * - The post type factory class does not have the ability to create forms. Therefore, some common hooks such as `validation_{...}` and `options_{...}` are not available.
 *
 * @since       3.8.0
 * @package     AdminPageFramework/Common/Factory
 * @heading     Base Factory
 */
class AdminPageFramework_Common_Factory_Documentaiton {}
