<?php
if ( ! class_exists( 'AdminPageFramework_Property_Base' ) ) :

/**
 * The base class for Property classes.
 * 
 * Provides the common methods  and properties for the property classes that are used by the main class, the meta box class, and the post type class.
 * @since			2.1.0
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Property
 */ 
abstract class AdminPageFramework_Property_Base {

	/**
	 * Stores the main (caller) object.
	 * 
	 * @since			2.1.5
	 */
	protected $oCaller;	
	
	/**
	 * Stores the script to be embedded in the head tag.
	 * 
	 * @remark			This should be an empty string by default since the related methods uses the append operator.
	 * @since			2.0.0
	 * @since			2.1.5			Moved from each extended property class.
	 * @internal
	 */ 			
	public $sScript = '';	

	/**
	 * Stores the CSS rules to be embedded in the head tag.
	 * 
	 * @remark			This should be an empty string by default since the related methods uses the append operator.
	 * @since			2.0.0
	 * @since			2.1.5			Moved from each extended property class.
	 * @internal
	 */ 		
	public $sStyle = '';	
	
	/**
	 * Stores the CSS rules for IE to be embedded in the head tag.
	 * 
	 * @remark			This should be an empty string by default since the related methods uses the append operator.
	 * @since			2.0.0 to 2.1.4
	 * @internal
	 */ 
	public $sStyleIE = '';	
	
	/**
	 * Stores the field type definitions.
	 * 
	 * @since			2.1.5
	 * @internal
	 */
	public $aFieldTypeDefinitions = array();
	
	/**
	 * The default CSS rules loaded in the head tag of the created admin pages.
	 * 
	 * @since			2.0.0
	 * @var				string
	 * @static
	 * @remark			It is accessed from the main class and meta box class.
	 * @access			public	
	 * @internal	
	 */
	public static $sDefaultStyle =
		".wrap div.updated, 
		.wrap div.settings-error { 
			clear: both; 
			margin-top: 16px;
		} 		

		.contextual-help-description {
			clear: left;	
			display: block;
			margin: 1em 0;
		}
		.contextual-help-tab-title {
			font-weight: bold;
		}
		
		/* Delimiter */
		.admin-page-framework-fields .delimiter {
			display: inline;
		}
		/* Description */
		.admin-page-framework-fields .admin-page-framework-fields-description {
			/* margin-top: 0px; */
			/* margin-bottom: 0.5em; */
			margin-bottom: 0;
		}
		/* Input form elements */
		.admin-page-framework-field {
			display: inline;
			margin-top: 1px;
			margin-bottom: 1px;
		}
		.admin-page-framework-field .admin-page-framework-input-label-container {
			margin-bottom: 0.25em;
		}
		@media only screen and ( max-width: 780px ) {	/* For WordPress v3.8 or greater */
			.admin-page-framework-field .admin-page-framework-input-label-container {
				margin-bottom: 0.5em;
			}
		}			
		.admin-page-framework-field input[type='radio'],
		.admin-page-framework-field input[type='checkbox']
		{
			margin-right: 0.5em;
		}		
		
		.admin-page-framework-field .admin-page-framework-input-label-string {
			padding-right: 1em;	/* for checkbox label strings, a right padding is needed */
		}
		.admin-page-framework-field .admin-page-framework-input-button-container {
			padding-right: 1em; 
		}
		.admin-page-framework-field-radio .admin-page-framework-input-label-container,
		.admin-page-framework-field-select .admin-page-framework-input-label-container,
		.admin-page-framework-field-checkbox .admin-page-framework-input-label-container 
		{
			padding-right: 1em;
		}

		.admin-page-framework-field .admin-page-framework-input-container {
			display: inline-block;
			vertical-align: middle; 
		}
		.admin-page-framework-field-text .admin-page-framework-field .admin-page-framework-input-label-container,
		.admin-page-framework-field-textarea .admin-page-framework-field .admin-page-framework-input-label-container,
		.admin-page-framework-field-color .admin-page-framework-field .admin-page-framework-input-label-container,
		.admin-page-framework-field-select .admin-page-framework-field .admin-page-framework-input-label-container
		{
			vertical-align: top; 
		}
		.admin-page-framework-field-image .admin-page-framework-field .admin-page-framework-input-label-container {			
			vertical-align: middle;
		}
		.admin-page-framework-field .admin-page-framework-input-label-container,
		.admin-page-framework-field .admin-page-framework-input-label-string
		{
			display: inline-block;		
			vertical-align: middle;
		}
		.admin-page-framework-field-textarea .admin-page-framework-input-label-string {
			vertical-align: top;
			margin-top: 2px;
		}
		
		.admin-page-framework-field-posttype .admin-page-framework-field input[type='checkbox'] { 
			margin-top: 0px;
		}
		.admin-page-framework-field-posttype .admin-page-framework-field {
			display: inline-block;
		}
		.admin-page-framework-field-radio .admin-page-framework-field .admin-page-framework-input-container {
			display: inline;
		}
		
		/* Repeatable Fields */		
		.admin-page-framework-field.repeatable {
			clear: both;
			display: block;
		}
		.admin-page-framework-repeatable-field-buttons {
			float: right;
			margin-bottom: 0.5em;
		}
		.admin-page-framework-repeatable-field-buttons .repeatable-field-button {
			margin: 0 2px;
			font-weight: normal;
			vertical-align: middle;
			text-align: center;
		}

		/* Import Field */
		.admin-page-framework-field-import input {
			margin-right: 0.5em;
		}
		/* Page Load Stats */
		#admin-page-framework-page-load-stats {
			clear: both;
			display: inline-block;
			width: 100%
		}
		#admin-page-framework-page-load-stats li{
			display: inline;
			margin-right: 1em;
		}		
		
		/* To give the footer area more space */
		#wpbody-content {
			padding-bottom: 140px;
		}
		";	
		
	/**
	 * The default CSS rules for IE loaded in the head tag of the created admin pages.
	 * @since			2.1.1
	 * @since			2.1.5			Moved the contents to the taxonomy field definition so it become an empty string.
	 */
	public static $sDefaultStyleIE = '';
		

	/**
	 * Stores enqueuing script URLs and their criteria.
	 * @since			2.1.2
	 * @since			2.1.5			Moved to the base class.
	 */
	public $aEnqueuingScripts = array();
	/**	
	 * Stores enqueuing style URLs and their criteria.
	 * @since			2.1.2
	 * @since			2.1.5			Moved to the base class.
	 */	
	public $aEnqueuingStyles = array();
	/**
	 * Stores the index of enqueued scripts.
	 * 
	 * @since			2.1.2
	 * @since			2.1.5			Moved to the base class.
	 */
	public $iEnqueuedScriptIndex = 0;
	/**
	 * Stores the index of enqueued styles.
	 * 
	 * The index number will be incremented as a script is enqueued regardless a previously added enqueue item has been removed or not.
	 * This is because this index number will be used for the script handle ID which is automatically generated.
	 * 
	 * @since			2.1.2
	 * @since			2.1.5			Moved to the base class.
	 */	
	public $iEnqueuedStyleIndex = 0;		
		
	function __construct( $oCaller ) {
		
		$this->oCaller = $oCaller;
		$GLOBALS['aAdminPageFramework'] = isset( $GLOBALS['aAdminPageFramework'] ) && is_array( $GLOBALS['aAdminPageFramework'] ) 
			? $GLOBALS['aAdminPageFramework']
			: array();

	}
	
	/**
	 * Calculates the subtraction of two values with the array key of <em>order</em>
	 * 
	 * This is used to sort arrays.
	 * 
	 * @since			2.0.0
	 * @remark			a callback method for uasort().
	 * @return			integer
	 * @internal
	 */ 
	public function _sortByOrder( $a, $b ) {	
		return $a['order'] - $b['order'];
	}		
	
	/**
	 * Returns the caller object.
	 * 
	 * This is used from other sub classes that need to retrieve the caller object.
	 * 
	 * @since			2.1.5
	 * @access			public	
	 * @return			object			The caller class object.
	 * @internal
	 */		
	public function _getParentObject() {
		return $this->oCaller;
	}
	
}
endif;