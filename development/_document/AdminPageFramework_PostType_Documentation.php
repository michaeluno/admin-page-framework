<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides an abstract base class to create custom post types with some additional features.
 * 
 * <h2>Hooks</h2>
 * <p>The class automatically creates WordPress action and filter hooks associated with the class methods.
 * The class methods corresponding to the name of the below actions and filters can be extended to modify the page output. Those methods are the callbacks of the filters and actions.</p>
 * <h3>Methods and Action Hooks</h3>
 * <ul>
 *     <li>**start_{instantiated class name}** – triggered at the end of the class constructor. This receives the class object in the first parameter.</li>
 *     <li>**set_up_{instantiated class name}** – triggered after the setUp() method is called. This receives the class object in the first parameter.</li>
 * </ul>
 * <h3>Methods and Filter Hooks</h3>
 * <ul>
 *     <li>**cell_{post type slug}_{column key}** – receives the output string for the listing table of the custom post type's post. The first parameter: output string. The second parameter: the post ID.</li>
 *     <li>**columns_{post type slug}** – receives the array containing the header columns for the listing table of the custom post type's post. The first parameter: the header columns container array.</li>
 *     <li>**sortable_columns_{post type slug}** – receives the array containing the sortable header column array for the listing table of the custom post type's post. The first parameter: the sortable header columns container array.</li>
 * </ul>
 * <h3>Remarks</h3>
 * <p>The slugs must not contain a dot(.) or a hyphen(-) since it is used in the callback method name.</p> 
 * 
 * @since       3.3.0
 * @package     AdminPageFramework
 * @subpackage  PostType
 * @heading     Post Type
 */
abstract class AdminPageFramework_PostType_Documentation {}