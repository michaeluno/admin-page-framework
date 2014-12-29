<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides an abstract base class to create custom fields in texonomy definition pages.
 *
 * <h2>Hooks</h2>
 * <p>The class automatically creates WordPress action and filter hooks associated with the class methods.
 * The class methods corresponding to the name of the below actions and filters can be extended to modify the page output. Those methods are the callbacks of the filters and actions.</p>
 * <h3>Methods and Action Hooks</h3>
 * <ul>
 *     <li>**start_{instantiated class name}** – triggered at the end of the class constructor.</li>
 *     <li>**do_{instantiated class name}** – triggered when the meta box gets rendered. The first parameter: the class object [3.1.3+]. </li>
 * </ul>
 * <h3>Methods and Filter Hooks</h3>
 * <ul>
 *     <li>**field_types_{instantiated class name}** – receives the field type definition array. The first parameter: the field type definition array.</li>
 *     <li>**field_{instantiated class name}_{field ID}** – receives the form input field output of the given input field ID. The first parameter: output string. The second parameter: the array of option.</li>
 *     <li>**content_{instantiated class name}** – receives the entire output of the meta box. The first parameter: the output HTML string.</li>
 *     <li>**style_common_admin_page_framework** –  [3.2.1+] receives the output of the base CSS rules applied to common CSS rules shared by the framework.</li>
 *     <li>**style_common_{instantiated class name}** –  receives the output of the base CSS rules applied to the pages of the associated post types with the meta box.</li>
 *     <li>**style_ie_common_{instantiated class name}** –  receives the output of the base CSS rules for Internet Explorer applied to the pages of the associated post types with the meta box.</li>
 *     <li>**style_{instantiated class name}** –  receives the output of the CSS rules applied to the pages of the associated post types with the meta box.</li>
 *     <li>**style_ie_{instantiated class name}** –  receives the output of the CSS rules for Internet Explorer applied to the pages of the associated post types with the meta box.</li>
 *     <li>**script_common_{instantiated class name}** – receives the output of the base JavaScript scripts applied to the pages of the associated post types with the meta box.</li>
 *     <li>**script_{instantiated class name}** – receives the output of the JavaScript scripts applied to the pages of the associated post types with the meta box.</li>
 *     <li>**validation_{instantiated class name}** – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 *     <li>**columns_{taxonomy slug}** – receives the header columns array. The first parameter: the header columns array.</li>
 *     <li>**columns_{instantiated class name}** – receives the header sortable columns array. The first parameter: the header columns array.</li>
 *     <li>**sortable_columns_{taxonomy slug}** – receives the header sortable columns array. The first parameter: the header columns array.</li>
 *     <li>**sortable_columns_{instantiated class name}** – receives the header columns array. The first parameter: the header columns array.</li>
 *     <li>**cell_{taxonomy slug}** – receives the cell output of the term listing table. The first parameter: the output string. The second parameter: the column slug. The third parameter: the term ID.</li>
 *     <li>**cell_{instantiated class name}** – receives the cell output of the term listing table. The first parameter: the output string. The second parameter: the column slug. The third parameter: the term ID.</li>
 *     <li>**cell_{instantiated class name}_{column slug}** – receives the cell output of the term listing table. The first parameter: the output string. The second parameter: the term ID.</li>
 * </ul> 
 *
 * @since       3.3.0
 * @package     AdminPageFramework
 * @subpackage  TaxonomyField
 * @heading     Taxonomy Field
 */
abstract class AdminPageFramework_TaxonomyField_Documentaion {}