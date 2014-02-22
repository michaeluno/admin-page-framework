<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_MetaBox' ) ) :
/**
 * @abstract
 * @since			2.0.0
 * @use				AdminPageFramework_Utility
 * @use				AdminPageFramework_Message
 * @use				AdminPageFramework_Debug
 * @use				AdminPageFramework_Property_MetaBox
 * @package			AdminPageFramework
 * @subpackage		MetaBox
 * @internal
 */
abstract class AdminPageFramework_MetaBox_Base {
	
	// Objects
	/**
	* @internal
	* @since			2.0.0
	*/ 	
	protected $oDebug;
	/**
	* @internal
	* @since			2.0.0
	*/ 		
	protected $oUtil;
	/**
	* @since			2.0.0
	* @internal
	*/ 		
	protected $oMsg;
	/**
	 * @since			2.1.5
	 * @internal
	 */
	protected $oHeadTag;
	
	/**
	 * Defines the fields type.
	 * @since			3.0.0
	 * @internal
	 */
	static protected $_sFieldsType;
	
	/**
	 * Stores the target section tab slug for the addSettingSection() method.
	 * @internal
	 */
	protected $_sTargetSectionTabSlug;	
	
	/**
	 * Constructs the class object instance of AdminPageFramework_MetaBox.
	 * 
	 * @see				http://codex.wordpress.org/Function_Reference/add_meta_box#Parameters
	 * @since			2.0.0
	 * @param			string			$sMetaBoxID			The meta box ID.
	 * @param			string			$sTitle				The meta box title.
	 * @param			string|array	$asPostTypeOrScreenID ( optional ) The post type(s) or screen ID that the meta box is associated with.
	 * @param			string			$sContext			( optional ) The part of the page where the edit screen section should be shown ('normal', 'advanced', or 'side') Default: normal.
	 * @param			string			$sPriority			( optional ) The priority within the context where the boxes should show ('high', 'core', 'default' or 'low') Default: default.
	 * @param			string			$sCapability		( optional ) The <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> to the meta box. Default: edit_posts.
	 * @param			string			$sTextDomain		( optional ) The text domain applied to the displayed text messages. Default: admin-page-framework.
	 * @return			void
	 */ 
	function __construct( $sMetaBoxID, $sTitle, $asPostTypeOrScreenID=array( 'post' ), $sContext='normal', $sPriority='default', $sCapability='edit_posts', $sTextDomain='admin-page-framework' ) {
		
		if ( empty( $asPostTypeOrScreenID ) ) return;
		
		// Objects
		$this->oUtil = new AdminPageFramework_WPUtility;
		$this->oMsg = AdminPageFramework_Message::instantiate( $sTextDomain );
		$this->oDebug = new AdminPageFramework_Debug;
		
		// Properties
		$this->oProp = isset( $this->oProp )
			? $this->oProp
			: new AdminPageFramework_Property_MetaBox( $this, get_class( $this ), $sCapability );
		$this->oProp->sMetaBoxID = $this->oUtil->sanitizeSlug( $sMetaBoxID );
		$this->oProp->sTitle = $sTitle;
		$this->oProp->sContext = $sContext;	//  'normal', 'advanced', or 'side' 
		$this->oProp->sPriority = $sPriority;	// 	'high', 'core', 'default' or 'low'	
		
		if ( $this->oProp->bIsAdmin ) {
			
			add_action( 'wp_loaded', array( $this, '_replyToLoadDefaultFieldTypeDefinitions' ), 10 );	// should be loaded before the setUp() method.
			add_action( 'wp_loaded', array( $this, 'setUp' ), 11 );
			add_action( 'current_screen', array( $this, '_replyToRegisterFormElements' ) );	// the screen object should be established to detect the loaded page. 
			add_action( 'add_meta_boxes', array( $this, '_replyToAddMetaBox' ) );
			add_action( 'save_post', array( $this, '_replyToSaveMetaBoxFields' ) );
								
		}		
	}

	/*
	 * Should be extended
	 */
	public function setUp() {}
	
	/*
	 * Help Pane
	 */
	/**
	 * Adds the given HTML text to the contextual help pane.
	 * 
	 * The help tab will be the meta box title and all the added text will be inserted into the content area within the tab.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->addHelpText( 
	 *		__( 'This text will appear in the contextual help pane.', 'admin-page-framework-demo' ), 
	 *		__( 'This description goes to the sidebar of the help pane.', 'admin-page-framework-demo' )
	 *	);</code>
	 * 
	 * @since			2.1.0
	 * @remark			This method just adds the given text into the class property. The actual registration will be performed with the <em>replyToRegisterHelpTabTextForMetaBox()</em> method.
	 */ 
	public function addHelpText( $sHTMLContent, $sHTMLSidebarContent="" ) {
		$this->oHelpPane->_addHelpText( $sHTMLContent, $sHTMLSidebarContent );
	}
	
	/*
	 * Head Tag Methods - should be extended.
	 */
	public function enqueueStyles( $aSRCs, $_vArg2=null, $_vArg3=null ) {}	// the number of arguments depend on the extended class
	public function enqueueStyle( $sSRC, $_vArg2=null, $_vArg3=null ) {}
	public function enqueueScripts( $aSRCs, $_vArg2=null, $_vArg3=null ) {}
	public function enqueueScript( $sSRC, $_vArg2=null, $_vArg3=null ) {}
				
	/*
	 * Internal methods that should be extended.
	 */
	public function _replyToAddMetaBox() {}
	public function _replyToRegisterFormElements() {}
	
	/**
	 * Loads the default field type definition.
	 * 
	 * @since			2.1.5
	 * @internal
	 */
	public function _replyToLoadDefaultFieldTypeDefinitions() {
		
		// This class adds filters for the field type definitions so that framework's default field types will be added.
		new AdminPageFramework_FieldTypeRegistration( $this->oProp->aFieldTypeDefinitions, $this->oProp->sClassName, $this->oMsg );		
		$this->oProp->aFieldTypeDefinitions = $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $sFilter, $vInput, $vArgs...
			$this,
			'field_types_' . $this->oProp->sClassName,	// 'field_types_' . {extended class name}
			$this->oProp->aFieldTypeDefinitions
		);				
		
	}

	/**
	 * Adds the given form section items into the property. 
	 * 
	 * The passed section array must consist of the following keys.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->addSettingSections(
	 *		array(
	 *			'section_id'	=> 'text_fields',
	 *			'page_slug'	=> 'first_page',
	 *			'tab_slug'	=> 'textfields',
	 *			'title'	=> 'Text Fields',
	 *			'description'	=> 'These are text type fields.',
	 *			'order'	=> 10,
	 *		),	
	 *		array(
	 *			'section_id'	=> 'selectors',
	 *			'page_slug'	=> 'first_page',
	 *			'tab_slug'	=> 'selectors',
	 *			'title'	=> 'Selectors and Checkboxes',
	 *			'description'	=> 'These are selector type options such as dropdown lists, radio buttons, and checkboxes',
	 *		)</code>
	 *
	 * @since			3.0.0			
	 * @access 			public
	 * @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	 * @param			array|string			the section array or the target page slug. If the target page slug is set, the next section array can omit the page slug key.
	 * <strong>Section Array</strong>
	 * <ul>
	 * <li><strong>section_id</strong> - ( string ) the section ID. Avoid using non-alphabetic characters exept underscore and numbers.</li>
	 * <li><strong>title</strong> - ( optional, string ) the title of the section.</li>
	 * <li><strong>capability</strong> - ( optional, string ) the <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> of the section. If the page visitor does not have sufficient capability, the section will be invisible to them.</li>
	 * <li><strong>if</strong> - ( optional, boolean ) if the passed value is false, the section will not be registered.</li>
	 * <li><strong>order</strong> - ( optional, integer ) the order number of the section. The higher the number is, the lower the position it gets.</li>
	 * <li><strong>help</strong> - ( optional, string ) the help description added to the contextual help tab.</li>
	 * <li><strong>help_aside</strong> - ( optional, string ) the additional help description for the side bar of the contextual help tab.</li>
	 * </ul>
	 * @param			array					( optional ) another section array.
	 * @param			array					( optional ) add more section array to the next parameters as many as necessary.
	 * @return			void
	 */	
	public function addSettingSections( $aSection1, $aSection2=null, $_and_more=null ) {
		foreach( func_get_args() as $asSection ) $this->addSettingSection( $asSection );
	}
	
	/**
	 * A singular form of the adSettingSections() method which takes only a single parameter.
	 * 
	 * This is useful when adding section arrays in loops.
	 * 
	 * @since			3.0.0			Changed the scope to public from protected.
	 * @access			public
	 * @remark			The actual registration will be performed in the <em>_replyToRegisterSettings()</em> method with the <em>admin_menu</em> hook.
	 * @param			array|string			the section array. If a string is passed, it is considered as a target page slug that will be used as a page slug element from the next call so that the element can be ommited.
	 * @remark			The $oForm property should be created in each extended class.
	 * @return			void
	 */
	public function addSettingSection( $aSection ) {
		
		if ( ! is_array( $aSection ) ) return;
		
		$this->_sTargetSectionTabSlug = isset( $aSection['section_tab_slug'] ) ? $this->oUtil->sanitizeSlug( $aSection['section_tab_slug'] ) : $this->_sTargetSectionTabSlug;	
		$aSection['section_tab_slug'] = $this->_sTargetSectionTabSlug ?  $this->_sTargetSectionTabSlug : null;
				
		$this->oForm->addSection( $aSection );
			
	}		
		
	/**
	* Adds the given field array items into the field array property by the given field definition array(s).
	* 
	* The field definition array requires specific keys. Refer to the parameter section of this method.
	* 
	* <h4>Example</h4>
	* <code>    $this->addSettingFields(
    *     array(
    *         'field_id'        => 'sample_metabox_text_field',
    *         'title'          => 'Text Input',
    *         'description'    => 'The description for the field.',
    *         'type'           => 'text',
    *     ),
    *     array(
    *         'field_id'        => 'sample_metabox_textarea_field',
    *         'title'          => 'Textarea',
    *         'description'    => 'The description for the field.',
    *         'type'           => 'textarea',
    *         'default'          => 'This is a default text.',
    *     )
    * );</code>
	* 
	* @since			2.0.0
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @param			array			the field definition array.
	* <h4>Built-in Field Types</h4>
	* <ul>
	* 	<li><strong>text</strong> - a normal field to enter text input.</li>
	* 	<li><strong>text</strong> - a masked text input field.</li>
	* 	<li><strong>textarea</strong> - a text input field with multiple lines. It supports rich text editor.</li>
	* 	<li><strong>radio</strong> - a set of radio buttons that lets the user pick an option.</li>
	* 	<li><strong>checkbox</strong> - a check box that lets the user enable/disable an item.</li>
	* 	<li><strong>select</strong> - a drop-down list that lest the user pick one or more item(s) from a list.</li>
	* 	<li><strong>hidden</strong> - a hidden field that will be useful to insert invisible values.</li>
	* 	<li><strong>file</strong> - a file uploader that lets the user upload files.</li>
	* 	<li><strong>image</strong> - a custom text field with the image uploader script that lets the user set the image URL.</li>
	* 	<li><strong>media</strong> - a custom text field with the media uploader script that lets the user set the file URL.</li>
	* 	<li><strong>color</strong> - a custom text field with the color picker script.</li>
	* 	<li><strong>submit</strong> - a submit button that lets the user send the form.</li>
	* 	<li><strong>export</strong> - a custom submit field that lets the user export the stored data.</li>
	* 	<li><strong>import</strong> - a custom combination field of the file and the submit fields that let the user import data.</li>
	* 	<li><strong>posttype</strong> - a check-list of post types enabled on the site.</li>
	* 	<li><strong>taxonomy</strong> - a set of check-lists of taxonomies enabled on the site in a tabbed box.</li>
	* 	<li><strong>size</strong> - a combination field of the text and the select fields that let the user set sizes with a selectable unit.</li>
	* 	<li><strong>section_title</strong> - [3.0.0+] a text field type that will be placed in the section title so that it lets the user set the section title. Note that only one field with this field type is allowed per a section.</li>
	* </ul>
	* <h4>Field Definition Array</h4>
	* <ul>
	* 	<li><strong>field_id</strong> - ( required, string ) the field ID. Avoid using non-alphabetic characters exept underscore and numbers.</li>
	* 	<li><strong>section_id</strong> - ( required, string ) the section ID that the field belongs to.</li>
	* 	<li><strong>type</strong> - ( required, string ) the type of the field. The supported types are listed below.</li>
	* 	<li><strong>title</strong> - ( optional, string ) the title of the section.</li>
	* 	<li><strong>description</strong> - ( optional, string ) the description of the field which is inserted into the after the input field tag.</li>
	* 	<li><strong>tip</strong> - ( optional, string ) the tip for the field which is displayed when the mouse is hovered over the field title.</li>
	* 	<li><strong>capability</strong> - ( optional, string ) the http://codex.wordpress.org/Roles_and_Capabilities">access level of the section. If the page visitor does not have sufficient capability, the section will be invisible to them.</li>
	* 	<li><strong>error_message</strong> - ( optional, string ) the error message to display above the input field.</li>
	* 	<li><strong>before_field</strong> - ( optional, string ) the HTML string to insert before the input field output.</li>
	* 	<li><strong>after_field</strong> - ( optional, string ) the HTML string to insert after the input field output.</li>
	* 	<li><strong>if</strong> - ( optional, boolean ) if the passed value is false, the section will not be registered.</li>
	* 	<li><strong>order</strong> - ( optional, integer ) the order number of the section. The higher the number is, the lower the position it gets.</li>
	* 	<li><strong>label</strong> - ( optional, string ) the text label(s) associated with and displayed along with the input field. Some input types can ignore this key.</li>
	* 	<li><strong>default</strong> - ( optional, string|array ) the default value(s) assigned to the input tag's value attribute.</li>
	* 	<li><strong>value</strong> - ( optional, string|array ) the value(s) assigned to the input tag's <em>value</em> attribute to override the default and the stored value.</li>
	* 	<li><strong>delimiter</strong> - ( optional, string ) the HTML string that delimits multiple elements. This is available if the <var>label</var> key is passed as array. It will be enclosed in inline-block elements so the passed HTML string should not contain block elements.</li>
	* 	<li><strong>before_input</strong> - ( optional, string ) the HTML string inserted right before the input tag. It will be enclosed in the <code>label</code> tag so the passed HTML string should not contain block elements.</li>
	* 	<li><strong>after_input</strong> - ( optional, string ) the HTML string inserted right after the input tag. It will be enclosed in the <code>label</code> tag so the passed HTML string should not contain block elements.</li>
	* 	<li><strong>label_min_width</strong> - ( optional, string ) the inline style property of the <em>min-width</em> of the label tag for the field in pixel without the unit. Default: <code>120</code>.</li>
	*	<li><strong>help</strong> - ( optional, string ) the help description added to the contextual help tab.</li>
	*	<li><strong>help_aside</strong> - ( optional, string ) the additional help description for the side bar of the contextual help tab.</li>
	*	<li><strong>repeatable</strong> - [3.0.0+] ( optional, array|boolean ) whether the fields should be repeatable. If it yields true, the plus and the minus buttons appear next to each field that lets the user add/remove the fields. Optionally an setting array can be passed.
	*		<h5>Repeatable Fields Setting Array</h5>
	*		<ul>
	*			<li><strong>max</strong> - the allowed maximum number of fields to be repeated.</li>
	*			<li><strong>min</string> - the allowed minimum number of fields to be repeated.</li>
	*		</ul>
	*	</li>
	*	<li><strong>sortable</strong> - [3.0.0+] ( optional, boolean ) whether the fields should be sortable. If it yields true, the fields will be enclosed in a draggable box.
	*	<li><strong>attributes</strong> - [3.0.0+] ( optional, array ) holds key-value pairs representing the attribute and its property. Note that some field types have specific keys in the first dimensions. e.g.<em>array( 'class' => 'my_custom_class_selector', 'style' => 'background-color:#777', 'size' => 20, )</em></li>
	*	<li><strong>show_title_column</strong> - [3.0.0+] ( optional, boolean ) If true, the field title column will be omitted from the output.</li>
	*	<li><strong>hidden</strong> - [3.0.0+] ( optional, boolean ) If true, the entire field row output will be invisible with the inline style attribute of <em>style="display:none"</em>.</li>
	* </ul>
	* 
	* <h4>Field Type Specific Keys</h4>
	* <p>Each field type uses specific array keys.</p>
	* <ul>
	* 	<li><strong>text</strong> - a text input field which allows the user to type text.</li>
	* 	<li><strong>password</strong> - a password input field which allows the user to type text.</li>
	* 	<li><strong>number, range</strong> - HTML5 input field types. Some browsers do not support these.</li>
	* 	<li><strong>textarea</strong> - a textarea input field. The following array keys are supported.
	* 		<ul>
	* 			<li><strong>rich</strong> - [2.1.2+]( optional, array ) to make it a rich text editor pass a non-empty value. It accept a setting array of the <code>_WP_Editors</code> class defined in the core.
	* For more information, see the argument section of <a href="http://codex.wordpress.org/Function_Reference/wp_editor" target="_blank">this page</a>.
	* 			</li>
	*		</ul>
	* 	</li>
	* 	<li><strong>radio</strong> - a radio button input field.</li>
	* 	<li><strong>checkbox</strong> - a check box input field.</li>
	* 	<li><strong>select</strong> - a drop-down input field.
	* 		<ul>
	* 			<li><strong>is_multiple</strong> - ( optional, boolean ) if this is set to true, the <em>multiple</em> attribute will be inserted into the field input tag, which enables the multiple selections for the user.</li>
	* 		</ul>
	* 	</li>
	* 	<li><strong>size</strong> - a size input field. This is a combination of number and select fields.
	* 		<ul>
	* 			<li>
	* 				<strong>units</strong> - ( optional, array ) defines the units to show. e.g. <em>array( 'px' => 'px', '%' => '%', 'em' => 'em'  )</em> 
	* 				Default: <em>array( 'px' => 'px', '%' => '%', 'em' => 'em', 'ex' => 'ex', 'in' => 'in', 'cm' => 'cm', 'mm' => 'mm', 'pt' => 'pt', 'pc' => 'pc' )</em>
	* 			</li>
	* 			<li><strong>is_multiple</strong> - ( optional, boolean ) if this is set to true, the <em>multiple</em> attribute will be inserted into the field input tag, which enables the multiple selections for the user.</li>
	* 			<li><strong>attributes</strong> - [3.0.0+] ( optional, array ) The attributes array of this field type has four initial keys: size, unit, optgroup, and option and they have a regular attribute array in each.</li>
	* 		</ul>
	*	</li>
	* 	<li><strong>hidden</strong> - a hidden input field.</li>
	* 	<li><strong>file</strong> - a file upload input field.</li>
	* 	<li><strong>submit</strong> - a submit button input field.
	* 		<ul>
	* 			<li><strong>href</strong> - ( optional, string ) the url(s) linked to the submit button.</li>
	* 			<li><strong>redirect_url</strong> - ( optional, string ) the url(s) redirected to after submitting the input form.</li>
	* 			<li><strong>reset</strong> - [2.1.2+] ( optional, boolean ) the option key to delete. Set 1 for the entire option.</li>
	* 		</ul>
	* 	</li>
	* 	<li><strong>import</strong> - an import input field. This is a custom file and submit field.
	* 		<ul>
	* 			<li><strong>option_key</strong> - ( optional, string ) the option table key to save the importing data.</li>
	* 			<li><strong>format</strong> - ( optional, string ) the import format. json, or array is supported. Default: array</li>
	* 			<li><strong>is_merge</strong> - ( optional, boolean ) [2.0.5+] determines whether the imported data should be merged with the existing options.</li>
	* 		</ul>
	* 	</li>
	* 	<li><strong>export</strong> - an export input field. This is a custom submit field.
	* 		<ul>
	* 			<li><strong>file_name</strong> - ( optional, string ) the file name to download.</li>
	* 			<li><strong>format</strong> - ( optional, string ) the format type. array, json, or text is supported. Default: array.</li>
	* 			<li><strong>data</strong> - ( optional, string|array|object ) the data to export.</li>
	* 		</ul>
	* 	</li>
	* 	<li><strong>image</strong> - an image input field. This is a custom text field with an attached JavaScript script.
	* 		<ul>
	* 			<li><strong>show_preview</strong> - ( optional, boolean ) if this is set to false, the image preview will be disabled.</li>
	* 			<li><strong>attributes_to_store</strong> - [2.1.3+] ( optional, array ) the array of the attribute names of the image to save. If this is set, the field will be an array with the specified attributes. The supported attributes are, 'title', 'alt', 'width', 'height', 'caption', 'id', 'align', and 'link'. Note that for external URLs, ID will not be captured. e.g. <em>'attributes_to_store' => array( 'id', 'caption', 'description' )</em></li>
	* 			<li><strong>allow_external_source</strong> - [2.1.3+] ( optional, boolean ) whether external URL can be set via the uploader.</li>
	* 			<li><strong>attributes</strong> - [3.0.0+] ( optional, array ) The attributes array of this field type has three keys: input, button, and preview and they have a regular attribute array in each.</li>
	* 		</ul>
	* 	</li>
	* 	<li><strong>media</strong> - [2.1.3+] a media input field. This is a custom text field with an attached JavaScript script.
	* 		<ul>
	* 			<li><strong>attributes_to_store</strong> - [2.1.3+] ( optional, array ) the array of the attribute names of the image to save. If this is set, the field will be an array with the specified attributes. The supported attributes are, 'id', 'caption', and 'description'. Note that for external URLs, ID will not be captured. e.g. <em>'attributes_to_store' => array( 'id', 'caption', 'description' )</em></li>
	* 			<li><strong>allow_external_source</strong> - [2.1.3+] ( optional, boolean ) whether external URL can be set via the uploader.</li>
	* 		</ul>
	* 	</li>
	* 	<li><strong>color</strong> - a color picker input field. This is a custom text field with a JavaScript script.</li>
	* 	<li><strong>taxonomy</strong> - a taxonomy check list. This is a set of check boxes listing a specified taxonomy. This does not accept to create multiple fields by passing an array of labels.
	* 		<ul>
	*			<li><strong>taxonomy_slugs</strong> - ( optional, array ) the taxonomy slug to list.</li>
	*			<li><strong>max_width</strong> - ( optional, string ) the inline style property value of <em>max-width</em> of this element. Include the unit such as px, %. Default: 100%</li>
	*			<li><strong>height</strong> - ( optional, string ) the inline style property value of <em>height</em> of this element. Include the unit such as px, %. Default: 250px</li>
	* 		</ul>
	* 	</li>
	* 	<li><strong>posttype</strong> - a post-type check list. This is a set of check boxes listing post type slugs.
	* 		<ul>
	* 			<li><strong>slugs_to_remove</strong> - ( optional, array ) the post type slugs not to be listed. e.g.<em>array( 'revision', 'attachment', 'nav_menu_item' )</em></li>
	* 		</ul>
	* 	</li>
	* </ul>	
	* @param			array			( optional ) another field array.
	* @param			array			( optional ) add more field arrays to the next parameters as many as necessary.
	* @return			void
	*/ 
	public function addSettingFields( $aField1, $aField2=null, $_and_more=null ) {
		foreach( func_get_args() as $aField ) $this->addSettingField( $aField );
	}	
		
	/**
	* Adds the given field array items into the field array property.
	* 
	* Identical to the addSettingFields() method except that this method does not accept enumerated parameters. 
	* 
	* <h4>Examples</h4>
	* <code>
	* 		$this->addSettingField(
	* 			array(
	* 				'field_id'		=> 'metabox_text_field',
	* 				'type'			=> 'text',
	* 				'title'			=> __( 'Text Input', 'admin-page-framework-demo' ),
	* 				'description'	=> __( 'The description for the field.', 'admin-page-framework-demo' ),
	* 				'help'			=> 'This is help text.',
	* 				'help_aside'	=> 'This is additional help text which goes to the side bar of the help pane.',
	* 			)
	* 		);	
	* </code>
	* 
	* @since			2.1.2
	* @since			3.0.0			The scope changed to public to indicate the users will use.
	* @return			void
	* @remark			The $oForm property should be created in each extended class.
	*/		
	public function addSettingField( $asField ) {
		$this->oForm->addField( $asField );		
	}
	
	/**
	 * Echoes the meta box contents.
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the <em>add_meta_box()</em> method.
	 * @param			object			$oPost			The object of the post associated with the meta box.
	 * @param			array			$vArgs			The array of arguments.
	 * @return			void
	 */ 
	public function _replyToPrintMetaBoxContents( $oPost, $vArgs ) {	
		
		// Use nonce for verification
		$aOutput = array();
		$aOutput[] = wp_nonce_field( $this->oProp->sMetaBoxID, $this->oProp->sMetaBoxID, true, false );
		
		// Condition the sections and fields definition arrays.
		$this->oForm->applyConditions();	// will set $this->oForm->aConditionedFields internally
		
		// Set the option array - the framework will refer to this data when displaying the fields.
		if ( isset( $this->oProp->aOptions ) )
			$this->setOptionArray( 
				isset( $oPost->ID ) ? $oPost->ID : ( isset( $_GET['page'] ) ? $_GET['page'] : null ), 
				$this->oForm->aConditionedFields 
			);	// will set $this->oProp->aOptions
		
		// Add the repeatable section elements to the fields definition array.
		$this->oForm->setDynamicElements( $this->oProp->aOptions );	// will update $this->oForm->aConditionedFields
							
		// Get the fields output.
		$oFieldsTable = new AdminPageFramework_FormTable( $this->oProp->aFieldTypeDefinitions, $this->oMsg );
		$aOutput[] = $oFieldsTable->getFormTables( $this->oForm->aConditionedSections, $this->oForm->aConditionedFields, array( $this, '_replyToGetSectionHeaderOutput' ), array( $this, '_replyToGetFieldOutput' ) );

		/* Do action */
		$this->oUtil->addAndDoActions( $this, 'do_' . $this->oProp->sClassName );
		
		/* Render the filtered output */
		echo $this->oUtil->addAndApplyFilters( $this, 'content_' . $this->oProp->sClassName, implode( PHP_EOL, $aOutput ) );

	}
	
	/**
	 * Sets the aOptions property array in the property object. 
	 * 
	 * This array will be referred later in the getFieldOutput() method.
	 * 
	 * @since			unknown
	 * @since			3.0.0			the scope is changed to protected as the taxonomy field class redefines it.
	 */
	protected function setOptionArray( $isPostIDOrPageSlug, $aFields ) {
		
		if ( ! is_array( $aFields ) ) return;
		
		// For post meta box, the $isPostIDOrPageSlug will be an integer representing the post ID.
		if ( is_numeric( $isPostIDOrPageSlug ) && is_int( $isPostIDOrPageSlug + 0 ) ) :
			
			$_iPostID = $isPostIDOrPageSlug;
			foreach( $aFields as $_sSectionID => $_aFields ) {
				
				if ( $_sSectionID == '_default' ) {
					
					foreach( $_aFields as $_aField ) 
						$this->oProp->aOptions[ $_aField['field_id'] ] = get_post_meta( $_iPostID, $_aField['field_id'], true );	
					
				}
				
				$this->oProp->aOptions[ $_sSectionID ] = get_post_meta( $_iPostID, $_sSectionID, true );
				
			}
							
		endif;
		
		// For page meta boxes, do nothing as the class will retrieve the option array by itself.
		
	}

	/**
	 * Registers the given fields.
	 * 
	 * @remark			$oHelpPane and $oHeadTab need to be set in the extended class.
	 * @since			3.0.0
	 */
	protected function _registerFields( array $aFields ) {

		foreach( $aFields as $_sSecitonID => $_aFields ) {
			
			foreach( $_aFields as $_iSubSectionIndexOrFieldID => $_aSubSectionOrField )  {
				
				if ( is_numeric( $_iSubSectionIndexOrFieldID ) && is_int( $_iSubSectionIndexOrFieldID + 0 ) ) // if it's a sub-section, skip
					continue;
					
				$_aField = $_aSubSectionOrField;
				
				// Load head tag elements for fields.
				AdminPageFramework_FieldTypeRegistration::_setFieldHeadTagElements( $_aField, $this->oProp, $this->oHeadTag );	// Set relevant scripts and styles for the input field.

				// For the contextual help pane,
				if ( $_aField['help'] )
					$this->oHelpPane->_addHelpTextForFormFields( $_aField['title'], $_aField['help'], $_aField['help_aside'] );
			
			}
		}
		
	}
	
	/**
	 * Returns the filtered section description output.
	 * 
	 * @since			3.0.0
	 */
	public function _replyToGetSectionHeaderOutput( $sSectionDescription, $aSection ) {
			
		return $this->oUtil->addAndApplyFilters(
			$this,
			array( 'section_head_' . $this->oProp->sClassName . '_' . $aSection['section_id'] ),	// section_ + {extended class name} + _ {section id}
			$sSectionDescription
		);				
		
	}
	
	/**
	 * Returns the field output from the given field definition array.
	 * 
	 * @since			3.0.0
	 */
	public function _replyToGetFieldOutput( $aField ) {

		// Render the form field. 		
		$sFieldType = isset( $this->oProp->aFieldTypeDefinitions[ $aField['type'] ]['hfRenderField'] ) && is_callable( $this->oProp->aFieldTypeDefinitions[ $aField['type'] ]['hfRenderField'] )
			? $aField['type']
			: 'default';	// the predefined reserved field type is applied if the parsing field type is not defined(not found).

		$oField = new AdminPageFramework_FormField( $aField, $this->oProp->aOptions, array(), $this->oProp->aFieldTypeDefinitions, $this->oMsg );	// currently the error array is not supported for meta-boxes
		$sFieldOutput = $oField->_getFieldOutput();	// field output
		unset( $oField );	// release the object for PHP 5.2.x or below.
		
		return $this->oUtil->addAndApplyFilters(
			$this,
			array( 	'field_' . $this->oProp->sClassName . '_' . $aField['field_id'] ),	// field_ + {extended class name} + _ {field id}
			$sFieldOutput,
			$aField // the field array
		);		
						
	}
		
	/**
	 * Saves the meta box field data to the associated post. 
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the <em>save_post</em> hook
	 * @internal
	 */
	public function _replyToSaveMetaBoxFields( $iPostID ) {
		
		// Bail if we're doing an auto save
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		
		// If our nonce isn't there, or we can't verify it, bail
		if ( ! isset( $_POST[ $this->oProp->sMetaBoxID ] ) || ! wp_verify_nonce( $_POST[ $this->oProp->sMetaBoxID ], $this->oProp->sMetaBoxID ) ) return;
			
		// Check permissions
		if ( ! $iPostID ) return;
		if ( in_array( $_POST['post_type'], $this->oProp->aPostTypes ) && ( ! current_user_can( $this->oProp->sCapability, $iPostID ) ) ) return;

		// Retrieve the submitted data.
		$aInput = $this->getInputArray( $this->oForm->aFields, $this->oForm->aSections );	// Todo: make sure if the aFields is formatted and conditioned or not.
	
		// Prepare the saved data.
		$aSavedMeta = $this->getSavedMetaArray( $iPostID, $aInput );
					
		// Apply filters to the array of the submitted values.
		$aInput = $this->oUtil->addAndApplyFilters( $this, "validation_{$this->oProp->sClassName}", $aInput, $aSavedMeta );

		// Drop repeatable section elements from the saved meta array.
		$aSavedMeta = $this->oForm->dropRepeatableElements( $aSavedMeta );
		
		// Loop through sections/fields and save the data.
		foreach ( $aInput as $_sSectionOrFieldID => $_vValue ) {
			
			if ( is_null( $_vValue ) ) continue;
			
			$vSavedValue = isset( $aSavedMeta[ $_sSectionOrFieldID ] ) ? $aSavedMeta[ $_sSectionOrFieldID ] : null;
			
			// PHP can compare even array contents with the == operator. See http://www.php.net/manual/en/language.operators.array.php
			if ( $_vValue == $vSavedValue ) continue;	// if the input value and the saved meta value are the same, no need to update it.
		
			update_post_meta( $iPostID, $_sSectionOrFieldID, $_vValue );
			
		}
		
	}	
		/**
		 * Retrieves the user submitted values.
		 * 
		 * @since			3.0.0
		 */
		protected function getInputArray( array $aFieldDefinitionArrays, array $aSectionDefinitionArrays ) {
			
			// Compose an array consisting of the submitted registered field values.
			$aInput = array();
			foreach( $aFieldDefinitionArrays as $_sSectionID => $_aSubSectionsOrFields ) {
				
				// If a section is not set,
				if ( $_sSectionID == '_default' ) {
					$_aFields = $_aSubSectionsOrFields;
					foreach( $_aFields as $_aField ) {
						$aInput[ $_aField['field_id'] ] = isset( $_POST[ $_aField['field_id'] ] ) 
							? $_POST[ $_aField['field_id'] ] 
							: null;
					}
					continue;
				}			
	
				// At this point, the section is set
				$aInput[ $_sSectionID ] = isset( $aInput[ $_sSectionID ] ) ? $aInput[ $_sSectionID ] : array();
				
				// If the section does not contain sub sections,
				if ( ! count( $this->oUtil->getIntegerElements( $_aSubSectionsOrFields ) ) ) {
					
					$_aFields = $_aSubSectionsOrFields;
					foreach( $_aFields as $_aField ) {
						$aInput[ $_sSectionID ][ $_aField['field_id'] ] = isset( $_POST[ $_sSectionID ][ $_aField['field_id'] ] )
							? $_POST[ $_sSectionID ][ $_aField['field_id'] ]
							: null;
					}											
					continue;

				}
					
				// Otherwise, it's sub-sections. 
				// Since the registered fields don't have information how many items the user added, parse the submitted data.
				foreach( $_POST[ $_sSectionID ] as $_iIndex => $_aFields ) {		// will include the main section as well.
					$aInput[ $_sSectionID ][ $_iIndex ] = isset( $_POST[ $_sSectionID ][ $_iIndex ] ) 
						? $_POST[ $_sSectionID ][ $_iIndex ]
						: null;
					
				}
								
			}
			
			return $aInput;
			
		}
		
		/**
		 * Retrieves the saved meta data as an array.
		 * 
		 * @since			3.0.0
		 */
		protected function getSavedMetaArray( $iPostID, $aInputStructure ) {
			
			$aSavedMeta = array();
			foreach ( $aInputStructure as $_sSectionORFieldID => $_v )
				$aSavedMeta[ $_sSectionORFieldID] = get_post_meta( $iPostID, $_sSectionORFieldID, true );
			return $aSavedMeta;
			
		}
		
	/*
	 * Magic method
	*/
	function __call( $sMethodName, $aArgs=null ) {	

		// the start_ action hook.
		if ( $sMethodName == 'start_' . $this->oProp->sClassName ) return;

		// the section_{class name}_{...} filter. [3.0.0+]
		if ( substr( $sMethodName, 0, strlen( 'section_head_' . $this->oProp->sClassName . '_' ) ) == 'section_head_' . $this->oProp->sClassName . '_' ) return $aArgs[ 0 ];
		
		// the field_{class name}_{...} filter.
		if ( substr( $sMethodName, 0, strlen( 'field_' . $this->oProp->sClassName . '_' ) ) == 'field_' . $this->oProp->sClassName . '_' ) return $aArgs[ 0 ];
		
		// the field_types_ + class name filter. [2.1.5+]
		if ( substr( $sMethodName, 0, strlen( "field_types_{$this->oProp->sClassName}" ) ) == "field_types_{$this->oProp->sClassName}" ) return $aArgs[ 0 ];		

		// the script_common + class name filter. [3.0.0+]
		if ( substr( $sMethodName, 0, strlen( "script_common_{$this->oProp->sClassName}" ) ) == "script_{$this->oProp->sClassName}" ) return $aArgs[ 0 ];

		// the script_ + class name filter.
		if ( substr( $sMethodName, 0, strlen( "script_{$this->oProp->sClassName}" ) ) == "script_{$this->oProp->sClassName}" ) return $aArgs[ 0 ];

		// the style_ie_common_ + class name filter. [3.0.0+]
		if ( substr( $sMethodName, 0, strlen( "style_ie_common_{$this->oProp->sClassName}" ) ) == "style_ie_common_{$this->oProp->sClassName}" ) return $aArgs[ 0 ];
			
		// the style_common + class name filter. [3.0.0+]
		if ( substr( $sMethodName, 0, strlen( "style_common_{$this->oProp->sClassName}" ) ) == "style_common_{$this->oProp->sClassName}" ) return $aArgs[ 0 ];

		// the style_ie + class name filter.
		if ( substr( $sMethodName, 0, strlen( "style_ie_{$this->oProp->sClassName}" ) ) == "style_ie_{$this->oProp->sClassName}" ) return $aArgs[ 0 ];
					
		// the style_ + class name filter.
		if ( substr( $sMethodName, 0, strlen( "style_{$this->oProp->sClassName}" ) ) == "style_{$this->oProp->sClassName}" ) return $aArgs[ 0 ];
						
		// the validation_ + class name	filter.
		if ( substr( $sMethodName, 0, strlen( "validation_{$this->oProp->sClassName}" ) ) == "validation_{$this->oProp->sClassName}" ) return $aArgs[ 0 ];

		// the content_{metabox id} filter. [3.0.0+]
		if ( substr( $sMethodName, 0, strlen( "content_{$this->oProp->sClassName}" ) ) == "content_{$this->oProp->sClassName}" ) return $aArgs[ 0 ];
			
		// do_{meta box id} [3.0.0.+]
		if ( substr( $sMethodName, 0, strlen( "do_{$this->oProp->sClassName}" ) ) == "do_{$this->oProp->sClassName}" ) return;
		
	}
}
endif;