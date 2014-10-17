<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_FieldType_Documentation' ) ) :
/**
 * Provides a base abstract class to be extend to create field types.
 * 
 * 
 * <h3>Steps to Include a Custom Field Type</h3>
 * <ol>
 *     <li>
 *         Define a custom field type with a class extending the `AdminPageFramework_FieldType` class.
 *         <ol>
 *             <li>Set the field type slug such as `autocomplete` with the `$aFieldTypeSlugs` property.</li>
 *             <li>Set the default field array definition keys with the `$aDefaultKeys` property.</li>
 *             <li>Write additional code in the `setUp()` method that will be performed when the field type definition is parsed.</li>
 *             <li>Add scripts and styles with `getEnqueuingScripts()`, `getEnqueuingStyles()`, `getScripts()`, `getStyles()` etc.</li>
 *             <li>Compose the output HTML structure with the passed `$aField` field definition array in the `getField()` method.</li>
 *         </ol>
 *     </li>
 *     <li>
 *         Include the definition file and instantiate the class in the script (plugin,theme etc.).
 *         <code>
 *          // pass the PHP class name that extends the framework's class to the first parameter.
 *         new MyCustomFieldTypeClass( 'MY_CLASS_NAME' );   
 *         </code>
 *     </li>
 *     <li>
 *         Define fields with the custom field type with the `addSettingFields()` method in the framework extending class.
 *         <code>
 *          $this->addSettingFields(
 *               array(  
 *                   'field_id'     =>  'my_field_id',
 *                   'section_id'   =>  'my_section_id',
 *                   'type'         =>  'my_custom_field_type_slug',    // <-- here put the field type slug
 *                   '...'          => '...'
 *               )
 *          );
 *         </code>
 *     </li>
 * </ol>
 * 
 * @package     AdminPageFramework
 * @subpackage  FieldType
 * @since       3.3.0      
 * @heading
 */
abstract class AdminPageFramework_FieldType_Documentation {}
endif;