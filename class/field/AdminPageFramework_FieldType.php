<?php
if ( ! class_exists( 'AdminPageFramework_FieldType' ) ) :
/**
 * The base class for the users to create their custom field types.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 * @since			3.0.0			Changed the name from AdminPageFramework_CustomFieldType to AdminPageFramework_FieldType.
 */
abstract class AdminPageFramework_FieldType extends AdminPageFramework_InputFieldTypeDefinition_Base {}
endif;