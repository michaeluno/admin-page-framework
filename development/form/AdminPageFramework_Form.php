<?php
if ( ! class_exists( 'AdminPageFramework_Form' ) ) :
/**
 * Provides properties for form elements
 * 
 * @package			AdminPageFramework
 * @subpackage		Form
 * @since			3.0.0
 * @internal
 */
class AdminPageFramework_Form  {
	
	/**
	 * Represents the structure of the form section array.
	 * 
	 * @since			2.0.0
	 * @remark			Not for the user.
	 * @var				array			Holds array structure of form section.
	 * @static
	 * @internal
	 */ 	
	public static $_aStructure_Section = array(	
		'section_id' => null,
		'page_slug' => null,
		'tab_slug' => null,
		'title' => null,
		'description' => null,
		'capability' => null,
		'if' => true,	
		'order' => null,	// do not set the default number here because incremented numbers will be added when registering the sections.
		'help' => null,
		'help_aside' => null,
	);	
	
	/**
	 * Represents the structure of the form field array.
	 * 
	 * @since			2.0.0
	 * @remark			Not for the user.
	 * @var				array			Holds array structure of form field.
	 * @static
	 * @internal
	 */ 
	public static $_aStructure_Field = array(
		'field_id'			=> null, 		// ( required )
		'section_id'		=> null,		// ( required )
		'type'				=> null,		// ( required )
		'section_title'		=> null,		// This will be assigned automatically in the formatting method.
		'page_slug'			=> null,		// This will be assigned automatically in the formatting method.
		'tab_slug'			=> null,		// This will be assigned automatically in the formatting method.
		'option_key'		=> null,		// This will be assigned automatically in the formatting method.
		'class_name'		=> null,		// This will be assigned automatically in the formatting method.
		'capability'		=> null,		
		'title'				=> null,
		'tip'				=> null,
		'description'		=> null,
		'error_message'		=> null,		// error message for the field
		'before_label'		=> null,
		'after_label'		=> null,
		'if' 				=> true,
		'order'				=> null,		// do not set the default number here for this key.		
		'default'			=> null,
		'value'				=> null,
		'help'				=> null,		// since 2.1.0
		'help_aside'		=> null,		// since 2.1.0
		'repeatable'		=> null,		// since 2.1.3
		'sortable'			=> null,		// since 2.1.3
		'attributes'		=> null,		// since 3.0.0 - the array represents the attributes of input tag
		'show_title_column' => true,		// since 3.0.0
		'hidden'			=> null,		// since 3.0.0
		'_fields_type'		=> null,		// since 3.0.0 - an internal key that indicates the fields type such as page, meta box for pages, meta box for posts, or taxonomy.
	);	
			
}
endif;