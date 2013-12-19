<?php
if ( ! class_exists( 'AdminPageFramework_Property_MetaBox' ) ) :
/**
 * Provides the space to store the shared properties for meta boxes.
 * 
 * This class stores various types of values. This is used to encapsulate properties so that it helps to avoid naming conflicts.
 * 
 * @since			2.1.0
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Property
 * @extends			AdminPageFramework_Property_Base
 */
class AdminPageFramework_Property_MetaBox extends AdminPageFramework_Property_Base {

	/**
	 * Stores the meta box id(slug).
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @var				string
	 */ 	
	public $sMetaBoxID ='';
	
	/**
	 * Stores the meta box title.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @var				string
	 */ 
	public $sTitle = '';

	/**
	 * Stores the post type slugs associated with the meta box.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @var				array
	 */ 	
	public $aPostTypes = array();
	
	/**
	 * Stores the parameter value, context, for the add_meta_box() function. 
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @remark			The value can be either 'normal', 'advanced', or 'side'.
	 * @var				string
	 * @see				http://codex.wordpress.org/Function_Reference/add_meta_box#Parameters
	 */ 
	public $sContext = 'normal';

	/**
	 * Stores the parameter value, priority, for the add_meta_box() function. 
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @remark			The value can be either 'high', 'core', 'default' or 'low'.
	 * @var				string
	 * @see				http://codex.wordpress.org/Function_Reference/add_meta_box#Parameters
	 */ 	
	public $sPriority = 'default';
	
	/**
	 * Stores the extended class name.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 */ 
	public $sClassName = '';
	
	/**
	 * Stores the capability for displayable elements.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 */ 	
	public $sCapability = 'edit_posts';
	
	/**
	 * @internal
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	*/ 		
	public $sPrefixStart = 'start_';	
	
	/**
	 * Stores the field arrays for meta box form elements.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @internal
	 */ 			
	public $aFields = array();
	
	/**
	 * Stores option values for form fields.
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @internal
	 */	 
	public $aOptions = array();
	
	/**
	 * Stores the media uploader box's title.
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @internal
	 */ 
	public $sThickBoxTitle = '';
	
	/**
	 * Stores the label for for the "Insert to Post" button in the media uploader box.
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @internal
	 */ 	
	public $sThickBoxButtonUseThis = '';

	/**
	 * Stores text to insert into the contextual help tab.
	 * @since			2.1.0
	 */ 
	public $aHelpTabText = array();
	
	/**
	 * Stores text to insert into the sidebar of a contextual help tab.
	 * @since			2.1.0
	 */ 
	public $aHelpTabTextSide = array();
	
	// Default values
	/**
	 * Represents the structure of field array for meta box form fields.
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @internal
	 */ 
	public static $_aStructure_Field = array(
		'field_id'		=> null,	// ( mandatory ) the field ID
		'type'			=> null,	// ( mandatory ) the field type.
		'title' 			=> null,	// the field title
		'description'	=> null,	// an additional note 
		'sCapability'		=> null,	// an additional note 
		'tip'			=> null,	// pop up text
		// 'options'			=> null,	// ? don't remember what this was for
		'vValue'			=> null,	// allows to override the stored value
		'default'			=> null,	// allows to set default values.
		'sName'			=> null,	// allows to set custom field name
		'label'			=> '',		// sets the label for the field. Setting a non-null value will let it parsed with the loop ( foreach ) of the input element rendering method.
		'fIf'				=> true,
		'help'			=> null,	// since 2.1.0
		'help_aside'		=> null,	// since 2.1.0
		'show_inpage_tabTitleColumn'	=> null,	// since 2.1.2
		
		// The followings may need to be uncommented.
		// 'sClassName' => null,		// This will be assigned automatically in the formatting method.
		// 'sError' => null,			// error message for the field
		// 'sBeforeField' => null,
		// 'sAfterField' => null,
		// 'order' => null,			// do not set the default number here for this key.		

		'repeatable'		=> null,	// since 2.1.3		
	);
	
}
endif;