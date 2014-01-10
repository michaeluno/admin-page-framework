<?php
if ( ! class_exists( 'AdminPageFramework_Setting' ) ) :
/**
 * Provides methods to add form elements with WordPress Settings API. 
 *
 * @abstract
 * @since		2.0.0
 * @extends		AdminPageFramework_Menu
 * @package		Admin Page Framework
 * @subpackage	Admin Page Framework - Page
 * @staticvar	array		$_aStructure_Section				represents the structure of the form section array.
 * @staticvar	array		$_aStructure_Field					represents the structure of the form field array.
 * @var			array		$aFieldErrors						stores the settings field errors.
 */
abstract class AdminPageFramework_Setting extends AdminPageFramework_Menu {
	
	/**
	 * Represents the structure of the form section array.
	 * 
	 * @since			2.0.0
	 * @remark			Not for the user.
	 * @var				array			Holds array structure of form section.
	 * @static
	 * @internal
	 */ 	
	protected static $_aStructure_Section = array(	
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
	protected static $_aStructure_Field = array(
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
		'name'				=> null,		// the name attribute of the input field.
		'error_message'		=> null,		// error message for the field
		'before_label'		=> null,
		'after_label'		=> null,
		'if' 				=> true,
		'order'				=> null,		// do not set the default number here for this key.		
		'help'				=> null,		// since 2.1.0
		'help_aside'		=> null,		// since 2.1.0
		'repeatable'		=> null,		// since 2.1.3
		'sortable'		=> null,		// since 2.1.3
		'attributes'		=> null,		// since 3.0.0 - the array represents the attributes of input tag
	);	
	
	/**
	 * Stores the settings field errors. 
	 * 
	 * @since			2.0.0
	 * @var				array			Stores field errors.
	 * @internal
	 */ 
	protected $aFieldErrors;		// Do not set a value here since it is checked to see it's null.
							
	function __construct() {
		
		add_action( 'admin_menu', array( $this, '_replyToRegisterSettings' ), 100 );	// registers the settings
		add_action( 'admin_init', array( $this, '_replyToCheckRedirects' ) );	// redirects
		
		// Call the parent constructor.
		$aArgs = func_get_args();
		call_user_func_array( array( $this, "parent::__construct" ), $aArgs );
		
	}
							
	/**
	* Sets the given message to be displayed in the next page load. 
	* 
	* This is used to inform users about the submitted input data, such as "Updated successfully." or "Problem occurred." etc. and normally used in validation callback methods.
	* 
	* <h4>Example</h4>
	* <code>if ( ! $bVerified ) {
	*		$this->setFieldErrors( $aErrors );		
	*		$this->setSettingNotice( 'There was an error in your input.' );
	*		return $aOldPageOptions;
	*	}</code>
	*
	* @since			2.0.0
	* @since			2.1.2			Added a check to prevent duplicate items.
	* @since			2.1.5			Added the $bOverride parameter.
	* @since			3.0.0			Changed the scope to public from protected.
	* @access 			public
	* @remark			The user may use this method in their extended class definition.
	* @param			string			$sMsg					the text message to be displayed.
	* @param			string			$sType				( optional ) the type of the message, either "error" or "updated"  is used.
	* @param			string			$sID					( optional ) the ID of the message. This is used in the ID attribute of the message HTML element.
	* @param			integer		$bOverride				( optional ) false: do not override when there is a message of the same id. true: override the previous one.
	* @return			void
	*/		
	public function setSettingNotice( $sMsg, $sType='error', $sID=null, $bOverride=true ) {
		
		// Check if the same message has been added already.
		$aWPSettingsErrors = isset( $GLOBALS['wp_settings_errors'] ) ? ( array ) $GLOBALS['wp_settings_errors'] : array();
		$sID = isset( $sID ) ? $sID : $this->oProp->sOptionKey; 	// the id attribute for the message div element.

		foreach( $aWPSettingsErrors as $iIndex => $aSettingsError ) {
			
			if ( $aSettingsError['setting'] != $this->oProp->sOptionKey ) continue;
						
			// If the same message is added, no need to add another.
			if ( $aSettingsError['message'] == $sMsg ) return;
				
			// Prevent duplicated ids.
			if ( $aSettingsError['code'] === $sID ) {
				if ( ! $bOverride ) 
					return;
				else	// remove the item with the same id  
					unset( $aWPSettingsErrors[ $iIndex ] );
			}
							
		}

		add_settings_error( 
			$this->oProp->sOptionKey, // the script specific ID so the other settings error won't be displayed with the settings_errors() function.
			$sID, 
			$sMsg,	// error or updated
			$sType
		);
					
	}

	/**
	* Adds the given form section items into the property. 
	* 
	* The passed section array must consist of the following keys.
	* 
	* <strong>Section Array</strong>
	* <ul>
	* <li><strong>section_id</strong> - ( string ) the section ID. Avoid using non-alphabetic characters exept underscore and numbers.</li>
	* <li><strong>page_slug</strong> - (  string ) the page slug that the section belongs to.</li>
	* <li><strong>tab_slug</strong> - ( optional, string ) the tab slug that the section belongs to.</li>
	* <li><strong>title</strong> - ( optional, string ) the title of the section.</li>
	* <li><strong>capability</strong> - ( optional, string ) the <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> of the section. If the page visitor does not have sufficient capability, the section will be invisible to them.</li>
	* <li><strong>if</strong> - ( optional, boolean ) if the passed value is false, the section will not be registered.</li>
	* <li><strong>order</strong> - ( optional, integer ) the order number of the section. The higher the number is, the lower the position it gets.</li>
	* <li><strong>help</strong> - ( optional, string ) the help description added to the contextual help tab.</li>
	* <li><strong>help_aside</strong> - ( optional, string ) the additional help description for the side bar of the contextual help tab.</li>
	* </ul>
	* 
	* <h4>Example</h4>
	* <code>$this->addSettingSections(
	*		array(
	*			'section_id'		=> 'text_fields',
	*			'page_slug'		=> 'first_page',
	*			'tab_slug'		=> 'textfields',
	*			'title'			=> 'Text Fields',
	*			'description'	=> 'These are text type fields.',
	*			'order'			=> 10,
	*		),	
	*		array(
	*			'section_id'		=> 'selectors',
	*			'page_slug'		=> 'first_page',
	*			'tab_slug'		=> 'selectors',
	*			'title'			=> 'Selectors and Checkboxes',
	*			'description'	=> 'These are selector type options such as dropdown lists, radio buttons, and checkboxes',
	*		)</code>
	*
	* @since			2.0.0
	* @since			3.0.0			Changed the scope to public from protected.
	* @access 			public
	* @remark			The user may use this method in their extended class definition.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @remark			The actual registration will be performed in the <em>_replyToRegisterSettings()</em> method with the <em>admin_menu</em> hook.
	* @param			array		$aSection1				the section array.
	* @param			array		$aSection2				( optional ) another section array.
	* @param			array		$_and_more					( optional ) add more section array to the next parameters as many as necessary.
	* @return			void
	*/		
	public function addSettingSections( $aSection1, $aSection2=null, $_and_more=null ) {	
		foreach( func_get_args() as $aSection )  $this->addSettingSection( $aSection );
	}
	
	/**
	 * A singular form of the adSettingSections() method which takes only a single parameter.
	 * 
	 * This is useful when adding section arrays in loops.
	 * 
	 * @since			2.1.2
	 * @since			3.0.0			Changed the scope to public from protected.
	 * @access			public
	 * @param			array		$aSection				the section array.
	 * @remark			The user may use this method in their extended class definition.
	 * @remark			The actual registration will be performed in the <em>_replyToRegisterSettings()</em> method with the <em>admin_menu</em> hook.
	 */
	public function addSettingSection( $aSection ) {
		
		$sCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;		
		
		if ( ! is_array( $aSection ) ) return;
		if ( ! isset( $aSection['section_id'], $aSection['page_slug'] ) ) return;	// these keys are necessary.

		$aSection = $aSection + self::$_aStructure_Section;	// avoid undefined index warnings.
		
		// Sanitize the IDs since they are used as a callback method name, the slugs as well.
		$aSection['section_id'] = $this->oUtil->sanitizeSlug( $aSection['section_id'] );
		$aSection['page_slug'] = $this->oUtil->sanitizeSlug( $aSection['page_slug'] );
		$aSection['tab_slug'] = $this->oUtil->sanitizeSlug( $aSection['tab_slug'] );
		
		$this->oProp->aSections[ $aSection['section_id'] ] = $aSection;	

	}
	
	/**
	* Removes the given section(s) by section ID.
	* 
	* This accesses the property storing the added section arrays and removes the specified ones.
	* 
	* <h4>Example</h4>
	* <code>$this->removeSettingSections( 'text_fields', 'selectors', 'another_section', 'yet_another_section' );</code>
	* 
	* @since			2.0.0
	* @since			3.0.0			Changed the scope to public from protected.
	* @access 			public
	* @remark			The user may use this method in their extended class definition.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @remark			The actual registration will be performed in the <em>_replyToRegisterSettings()</em> method with the <em>admin_menu</em> hook.
	* @param			string			$sSectionID1			the section ID to remove.
	* @param			string			$sSectionID2			( optional ) another section ID to remove.
	* @param			string			$_and_more				( optional ) add more section IDs to the next parameters as many as necessary.
	* @return			void
	*/	
	public function removeSettingSections( $sSectionID1=null, $sSectionID2=null, $_and_more=null ) {	
		
		foreach( func_get_args() as $sSectionID ) 
			if ( isset( $this->oProp->aSections[ $sSectionID ] ) )
				unset( $this->oProp->aSections[ $sSectionID ] );
		
	}
	
	/**
	* Adds the given field array items into the field array property.
	* 
	* The passed field array must consist of the following keys. 
	* 
	* <h4>Field Array</h4>
	* <ul>
	* 	<li><strong>field_id</strong> - ( string ) the field ID. Avoid using non-alphabetic characters exept underscore and numbers.</li>
	* 	<li><strong>section_id</strong> - ( string ) the section ID that the field belongs to.</li>
	* 	<li><strong>type</strong> - ( string ) the type of the field. The supported types are listed below.</li>
	* 	<li><strong>title</strong> - ( optional, string ) the title of the section.</li>
	* 	<li><strong>description</strong> - ( optional, string ) the description of the field which is inserted into the after the input field tag.</li>
	* 	<li><strong>tip</strong> - ( optional, string ) the tip for the field which is displayed when the mouse is hovered over the field title.</li>
	* 	<li><strong>capability</strong> - ( optional, string ) the http://codex.wordpress.org/Roles_and_Capabilities">access level of the section. If the page visitor does not have sufficient capability, the section will be invisible to them.</li>
	* 	<li><strong>name</strong> - ( optional, string ) the name attribute value of the input tag instead of automatically generated one.</li>
	* 	<li><strong>error_message</strong> - ( optional, string ) the error message to display above the input field.</li>
	* 	<li><strong>before_field</strong> - ( optional, string ) the HTML string to insert before the input field output.</li>
	* 	<li><strong>after_field</strong> - ( optional, string ) the HTML string to insert after the input field output.</li>
	* 	<li><strong>if</strong> - ( optional, boolean ) if the passed value is false, the section will not be registered.</li>
	* 	<li><strong>order</strong> - ( optional, integer ) the order number of the section. The higher the number is, the lower the position it gets.</li>
	* 	<li><strong>label</strong> - ( optional|mandatory, string|array ) the text label(s) associated with and displayed along with the input field. Some input types can ignore this key while some require it.</li>
	* 	<li><strong>default</strong> - ( optional, string|array ) the default value(s) assigned to the input tag's value attribute.</li>
	* 	<li><strong>value</strong> - ( optional, string|array ) the value(s) assigned to the input tag's <em>value</em> attribute to override the default or stored value.</li>
	* 	<li><strong>delimiter</strong> - ( optional, string|array ) the HTML string that delimits multiple elements. This is available if the <var>label</var> key is passed as array. It will be enclosed in inline-block elements so the passed HTML string should not contain block elements.</li>
	* 	<li><strong>before_input</strong> - ( optional, string|array ) the HTML string inserted right before the input tag. It will be enclosed in the <code>label</code> tag so the passed HTML string should not contain block elements.</li>
	* 	<li><strong>after_input</strong> - ( optional, string|array ) the HTML string inserted right after the input tag. It will be enclosed in the <code>label</code> tag so the passed HTML string should not contain block elements.</li>
	* 	<li><strong>class_attribute</strong> - ( optional, string|array ) the value(s) assigned to the input tag's <em>class</em>.</li>
	* 	<li><strong>label_min_width</strong> - ( optional, string|array ) the inline style property of the <em>min-width</em> of the label tag for the field in pixel without the unit. Default: <code>120</code>.</li>
	* 	<li><strong>disable</strong> - ( optional, boolean|array ) if this is set to true, the <em>disabled</em> attribute will be inserted into the field input tag.</li>
	*	<li><strong>help</strong> - ( optional, string ) the help description added to the contextual help tab.</li>
	*	<li><strong>help_aside</strong> - ( optional, string ) the additional help description for the side bar of the contextual help tab.</li>
	*	<li><strong>repeatable</strong> - [3.0.0+] ( optional, array|boolean ) whether the fields should be repeatable. If it yields true, the plus and the minus buttons appear next to each field that lets the user add/remove the fields. Optionally an setting array can be passed.
	*		<h4>Repeatable Fields Setting Array</h4>
	*		<ul>
	*			<li><code>max</code> - the allowed maximum number of fields to be repeated.</li>
	*			<li><code>min</code> - the allowed minimum number of fields to be repeated.</li>
	*		</ul>
	*	</li>
	*	<li><strong>attributes</strong> - [3.0.0+] ( optional, array ) holds key-value pairs representing the attribute and its property. Note that some field types have specific keys in the first dimensions. e.g.<code>array( 'class' => 'my_custom_class_selector', 'style' => 'background-color:#777', 'size' => 20, )</code></li>
	* </ul>
	* <h4>Field Types</h4>
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
	* 	<li><strong>select</strong> - a dropdown input field.</li>
	* 		<ul>
	* 			<li><strong>is_multiple</strong> - ( optional, boolean ) if this is set to true, the <em>multiple</em> attribute will be inserted into the field input tag, which enables the multiple selections for the user.</li>
	* 		</ul>
	* 	<li><strong>size</strong> - a size input field. This is a combination of number and select fields.</li>
	* 		<ul>
	* 			<li>
	* 				<strong>units</strong> - ( optional, array ) defines the units to show. e.g. <code>array( 'px' => 'px', '%' => '%', 'em' => 'em'  )</code> 
	* 				Default: <code>array( 'px' => 'px', '%' => '%', 'em' => 'em', 'ex' => 'ex', 'in' => 'in', 'cm' => 'cm', 'mm' => 'mm', 'pt' => 'pt', 'pc' => 'pc' )</code>
	* 			</li>
	* 			<li><strong>is_multiple</strong> - ( optional, boolean ) if this is set to true, the <em>multiple</em> attribute will be inserted into the field input tag, which enables the multiple selections for the user.</li>
	* 			<li><strong>attributes</strong> - ( optional, array ) The attributes array of this field type has four initial keys: size, unit, optgroup, and option and they have a regular attribute array in each.</li>
	* 	</ul>
	* 	<li><strong>hidden</strong> - a hidden input field.</li>
	* 	<li><strong>file</strong> - a file upload input field.</li>
	* 	<li><strong>submit</strong> - a submit button input field.</li>
	* 		<ul>
	* 			<li><strong>href</strong> - ( optional, string ) the url(s) linked to the submit button.</li>
	* 			<li><strong>redirect_url</strong> - ( optional, string ) the url(s) redirected to after submitting the input form.</li>
	* 			<li><strong>is_reset</strong> - [2.1.2+] ( optional, boolean ) the option key to delete. Set 1 for the entire option.</li>
	* 		</ul>
	* 	<li><strong>import</strong> - an inport input field. This is a custom file and submit field.</li>
	* 		<ul>
	* 			<li><strong>option_key</strong> - ( optional, string ) the option table key to save the importing data.</li>
	* 			<li><strong>format</strong> - ( optional, string ) the import format. json, or array is supported. Default: array</li>
	* 			<li><strong>is_merge</strong> - ( optional, boolean ) [2.0.5+] determines whether the imported data should be merged with the existing options.</li>
	* 		</ul>
	* 	<li><strong>export</strong> - an export input field. This is a custom submit field.</li>
	* 		<ul>
	* 			<li><strong>file_name</strong> - ( optional, string ) the file name to download.</li>
	* 			<li><strong>format</strong> - ( optional, string ) the format type. array, json, or text is supported. Default: array.</li>
	* 			<li><strong>data</strong> - ( optional, string|array|object ) the data to export.</li>
	* 		</ul>
	* 	<li><strong>image</strong> - an image input field. This is a custom text field with an attached JavaScript script.</li>
	* 		<ul>
	* 			<li><strong>show_preview</strong> - ( optional, boolean ) if this is set to false, the image preview will be disabled.</li>
	* 			<li><strong>attributes_to_store</strong> - [2.1.3+] ( optional, array ) the array of the attribute names of the image to save. If this is set, the field will be an array with the specified attributes. The supported attributes are, 'title', 'alt', 'width', 'height', 'caption', 'id', 'align', and 'link'. Note that for external URLs, ID will not be captured. e.g. <code>'attributes_to_store' => array( 'id', 'caption', 'description' )</code></li>
	* 			<li><strong>allow_external_source</strong> - [2.1.3+] ( optional, boolean ) whether external URL can be set via the uploader.</li>
	* 			<li><strong>attributes</strong> - ( optional, array ) The attributes array of this field type has three keys: input, button, and preview and they have a regular attribute array in each.</li>
	* 		</ul>
	* 	<li><strong>media</strong> - [2.1.3+] a media input field. This is a custom text field with an attached JavaScript script.</li>
	* 		<ul>
	* 			<li><strong>attributes_to_store</strong> - [2.1.3+] ( optional, array ) the array of the attribute names of the image to save. If this is set, the field will be an array with the specified attributes. The supported attributes are, 'id', 'caption', and 'description'. Note that for external URLs, ID will not be captured. e.g. <code>'attributes_to_store' => array( 'id', 'caption', 'description' )</code></li>
	* 			<li><strong>allow_external_source</strong> - [2.1.3+] ( optional, boolean ) whether external URL can be set via the uploader.</li>
	* 		</ul>
	* 	<li><strong>color</strong> - a color picker input field. This is a custom text field with a JavaScript script.</li>
	* 	<li><strong>taxonomy</strong> - a taxonomy check list. This is a set of check boxes listing a specified taxonomy. This does not accept to create multiple fields by passing an array of labels.</li>
	* 		<ul>
	*			<li><strong>taxonomy_slugs</strong> - ( optional, array ) the taxonomy slug to list.</li>
	*			<li><strong>max_width</strong> - ( optional, string ) the inline style property value of <em>max-width</em> of this element. Include the unit such as px, %. Default: 100%</li>
	*			<li><strong>height</strong> - ( optional, string ) the inline style property value of <em>height</em> of this element. Include the unit such as px, %. Default: 250px</li>
	* 		</ul>
	* 	<li><strong>posttype</strong> - a posttype check list. This is a set of check boxes listing post type slugs.</li>
	* 		<ul>
	* 			<li><strong>slugs_to_remove</strong> - ( optional, array ) the post type slugs not to be listed. e.g.<code>array( 'revision', 'attachment', 'nav_menu_item' )</code></li>
	* 		</ul>

	* </ul>	
	* 
	* <h4>Example</h4>
	* <code>$this->addSettingFields(
	*		array(
	*			'field_id' => 'text',
	*			'section_id' => 'text_fields',
	*			'title' => __( 'Text', 'admin-page-framework-demo' ),
	*			'description' => __( 'Type something here.', 'admin-page-framework-demo' ),
	*			'type' => 'text',
	*			'order' => 1,
	*			'default' => 123456,
	*			'size' => 40,
	*		),	
	*		array(
	*			'field_id' => 'text_multiple',
	*			'section_id' => 'text_fields',
	*			'title' => 'Multiple Text Fields',
	*			'description' => 'These are multiple text fields.',
	*			'type' => 'text',
	*			'order' => 2,
	*			'default' => 'Hello World',
	*			'label'	=>	'First Item',
	*			'attributes'	=> array(
	*				'size'	=> 30
	*			),
	*			array(
	*				'label'		=>	'Second Item',
	*				'default'	=> 'Foo bar',
	*				'attributes'	=>	array(
	*					'size'	=>	60,
	*				),
	*			),
	*			array(
	*				'label'		=>	'Third Item',
	*				'default'	=> 'Yes, we can.',
	*				'attributes'	=>	array(
	*					'size'	=>	90,
	*				),
	*			),
	*		)
	*	);</code> 
	* 
	* @since			2.0.0
	* @since			3.0.0			Changed the scope to public from protected.
	* @access 			public
	* @remark			The user may use this method in their extended class definition.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @remark			The actual registration will be performed in the <em>_replyToRegisterSettings()</em> method with the <em>admin_menu</em> hook.
	* @param			array			$aField1			the field array.
	* @param			array			$aField2			( optional ) another field array.
	* @param			array			$_and_more			( optional ) add more field arrays to the next parameters as many as necessary.
	* @return			void
	*/		
	public function addSettingFields( $aField1, $aField2=null, $_and_more=null ) {	
		foreach( func_get_args() as $aField ) $this->addSettingField( $aField );
	}
	/**
	* Adds the given field array items into the field array property.
	* 
	* Itentical to the addSettingFields() method except that this method does not accept enumerated parameters. 
	* 
	* @since			2.1.2
	* @since			3.0.0			Changed the scope to public from protected.
	* @access			public
	* @return			void
	* @remark			The user may use this method in their extended class definition.
	*/	
	public function addSettingField( $aField ) {
		
		if ( ! is_array( $aField ) ) return;

		// Check the required keys
		if ( ! isset( $aField['field_id'], $aField['section_id'], $aField['type'] ) ) return;	// these keys are necessary.
		
		$aField = $aField + self::$_aStructure_Field;	// avoid undefined index warnings.
		
		// Sanitize the IDs since they are used as a callback method name.
		$aField['field_id'] = $this->oUtil->sanitizeSlug( $aField['field_id'] );
		$aField['section_id'] = $this->oUtil->sanitizeSlug( $aField['section_id'] );
										
		$this->oProp->aFields[ $aField['field_id'] ] = $aField;		
		
	}
	
	/**
	* Removes the given field(s) by field ID.
	* 
	* This accesses the property storing the added field arrays and removes the specified ones.
	* 
	* <h4>Example</h4>
	* <code>$this->removeSettingFields( 'fieldID_A', 'fieldID_B', 'fieldID_C', 'fieldID_D' );</code>
	* 
	* @since			2.0.0
	* @since			3.0.0			Changed the scope to public from protected.
	* @access 			public
	* @remark			The user may use this method in their extended class definition.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @remark			The actual registration will be performed in the <em>_replyToRegisterSettings()</em> method with the <em>admin_menu</em> hook.
	* @param			string			$sFieldID1				the field ID to remove.
	* @param			string			$sFieldID2				( optional ) another field ID to remove.
	* @param			string			$_and_more					( optional ) add more field IDs to the next parameters as many as necessary.
	* @return			void
	*/	
	public function removeSettingFields( $sFieldID1, $sFieldID2=null, $_and_more ) {
				
		foreach( func_get_args() as $sFieldID ) 
			if ( isset( $this->oProp->aFields[ $sFieldID ] ) )
				unset( $this->oProp->aFields[ $sFieldID ] );

	}	
			
	/**
	 * Sets the field error array. 
	 * 
	 * This is normally used in validation callback methods when the submitted user's input data have an issue.
	 * This method saves the given array in a temporary area( transient ) of the options database table.
	 * 
	 * <h4>Example</h4>
	 * <code>public function validation_first_page_verification( $aInput, $aOldPageOptions ) {	// valication_ + page slug + _ + tab slug			
	 *		$bVerified = true;
	 *		$aErrors = array();
	 *		// Check if the submitted value meets your criteria. As an example, here a numeric value is expected.
	 *		if ( isset( $aInput['first_page']['verification']['verify_text_field'] ) && ! is_numeric( $aInput['first_page']['verification']['verify_text_field'] ) ) {
	 *			// Start with the section key in $aErrors, not the key of page slug.
	 *			$aErrors['verification']['verify_text_field'] = 'The value must be numeric: ' . $aInput['first_page']['verification']['verify_text_field'];	
	 *			$bVerified = false;
	 *		}
	 *		// An invalid value is found.
	 *		if ( ! $bVerified ) {
	 *			// Set the error array for the input fields.
	 *			$this->setFieldErrors( $aErrors );		
	 *			$this->setSettingNotice( 'There was an error in your input.' );
	 *			return $aOldPageOptions;
	 *		}
	 *		return $aInput;
	 *	}</code>
	 * 
	 * @since			2.0.0
	 * @since			3.0.0			Changed the scope to public from protected.
	 * @remark			the user may use this method.
	 * @remark			the transient name is a MD5 hash of the extended class name + _ + page slug ( the passed ID )
	 * @param			array			$aErrors			the field error array. The structure should follow the one contained in the submitted $_POST array.
	 * @param			string			$sID				this should be the page slug of the page that has the dealing form field.
	 * @param			integer			$nSavingDuration	the transient's lifetime. 300 seconds means 5 minutes.
	 */ 
	public function setFieldErrors( $aErrors, $sID=null, $nSavingDuration=300 ) {
		
		$sID = isset( $sID ) ? $sID : ( isset( $_POST['page_slug'] ) ? $_POST['page_slug'] : ( isset( $_GET['page'] ) ? $_GET['page'] : $this->oProp->sClassName ) );	
		set_transient( md5( $this->oProp->sClassName . '_' . $sID ), $aErrors, $nSavingDuration );	// store it for 5 minutes ( 60 seconds * 5 )
	
	}

	/**
	 * Retrieves the specified field value stored in the options.
	 * 
	 * Useful when you don't know the section name but it's a bit slower than accessing the property value by specifying the section name.
	 * 
	 * @since			2.1.2
	 * @since			3.0.0			Changed the scope to public from protected. Dropped the sections. Made it return a default value even if it's not saved in the database.
	 * @access			public
	 */
	public function getFieldValue( $sFieldID ) {
		
		/* If it's saved, return it */
		if ( array_key_exists( $sFieldID, $this->oProp->aOptions ) )
			return $this->oProp->aOptions[ $sFieldID ];
	
		/* Otherwise, search the default value */
		$_aDefaultOptions = $this->oProp->getDefaultOptions();
		if ( array_key_exists( $sFieldID, $_aDefaultOptions ) )
			return $_aDefaultOptions[ $sFieldID ];
			
		return null;
					
	}
			
	/**
	 * Validates the submitted user input.
	 * 
	 * @since			2.0.0
	 * @access			protected
	 * @remark			This method is not intended for the users to use.
	 * @remark			the scope must be protected to be accessed from the extended class. The <em>AdminPageFramework</em> class uses this method in the overloading <em>__call()</em> method.
	 * @return			array			Return the input array merged with the original saved options so that other page's data will not be lost.
	 * @internal
	 */ 
	protected function _doValidationCall( $sMethodName, $aInput ) {
		
		/* 1-1. Set up variables */
		$sTabSlug = isset( $_POST['tab_slug'] ) ? $_POST['tab_slug'] : '';	// no need to retrieve the default tab slug here because it's an embedded value that is already set in the previous page. 
		$sPageSlug = isset( $_POST['page_slug'] ) ? $_POST['page_slug'] : '';
		
		/* 1-2. Retrieve the submit field ID(the container that holds submit input tags) and the input ID(this determines exactly which submit button is pressed). */
		$sPressedFieldID = isset( $_POST['__submit'] ) ? $this->_getPressedCustomSubmitButtonSiblingValue( $_POST['__submit'], 'field_id' ) : '';
		$sPressedInputID = isset( $_POST['__submit'] ) ? $this->_getPressedCustomSubmitButtonSiblingValue( $_POST['__submit'], 'input_id' ) : '';
		
		/* 2. Check if custom submit keys are set [part 1] */
		if ( isset( $_POST['__import']['submit'], $_FILES['__import'] ) ) 
			return $this->_importOptions( $this->oProp->aOptions, $sPageSlug, $sTabSlug );
		if ( isset( $_POST['__export']['submit'] ) ) 
			die( $this->_exportOptions( $this->oProp->aOptions, $sPageSlug, $sTabSlug ) );		
		if ( isset( $_POST['__reset_confirm'] ) && $sPressedFieldName = $this->_getPressedCustomSubmitButtonSiblingValue( $_POST['__reset_confirm'], 'key' ) )
			return $this->_askResetOptions( $sPressedFieldName, $sPageSlug );			
		if ( isset( $_POST['__link'] ) && $sLinkURL = $this->_getPressedCustomSubmitButtonSiblingValue( $_POST['__link'], 'url' ) )
			die( wp_redirect( $sLinkURL ) );	// if the associated submit button for the link is pressed, the will be redirected.
		if ( isset( $_POST['__redirect'] ) && $sRedirectURL = $this->_getPressedCustomSubmitButtonSiblingValue( $_POST['__redirect'], 'url' ) )
			$this->_setRedirectTransients( $sRedirectURL );
				
		/* 3. Apply validation filters - validation_{page slug}_{tab slug}, validation_{page slug}, validation_{instantiated class name} */
		$aInput = $this->_getFilteredOptions( $aInput, $sPageSlug, $sTabSlug, $sPressedFieldID, $sPressedInputID );
		
		/* 4. Check if custom submit keys are set [part 2] - these should be done after applying the filters. */
		if ( isset( $_POST['__reset'] ) && $sKeyToReset = $this->_getPressedCustomSubmitButtonSiblingValue( $_POST['__reset'], 'key' ) )
			$aInput = $this->_resetOptions( $sKeyToReset, $aInput );
		
		/* 5. Set the update notice */
		$bEmpty = empty( $aInput );
		$this->setSettingNotice( 
			$bEmpty ? $this->oMsg->__( 'option_cleared' ) : $this->oMsg->__( 'option_updated' ), 
			$bEmpty ? 'error' : 'updated', 
			$this->oProp->sOptionKey,	// the id
			false	// do not override
		);
		
		return $aInput;	
		
	}
	
		/**
		 * Displays a confirmation message to the user when a reset button is pressed.
		 * 
		 * @since			2.1.2
		 */
		private function _askResetOptions( $sPressedFieldName, $sPageSlug ) {
			
			// Retrieve the pressed button's associated submit field ID.
			$aNameKeys = explode( '|', $sPressedFieldName );	// e.g. OptionKey|field_id
			$sFieldID = $aNameKeys[ 1 ];	
			
			// Set up the field error array.
			$aErrors = array();
			$aErrors[ $sFieldID ] = $this->oMsg->__( 'reset_options' );
			$this->setFieldErrors( $aErrors );
			
			// Set a flag that the confirmation is displayed
			set_transient( md5( "reset_confirm_" . $sPressedFieldName ), $sPressedFieldName, 60*2 );
			
			$this->setSettingNotice( $this->oMsg->__( 'confirm_perform_task' ) );
			
			return $this->_getPageOptions( $this->oProp->aOptions, $sPageSlug ); 			
			
		}
		
		/**
		 * Performs reset options.
		 * 
		 * @since			2.1.2
		 * @remark			$aInput has only the page elements that called the validation callback. In other words, it does not hold other pages' option keys.
		 */
		private function _resetOptions( $sKeyToReset, $aInput ) {
			
			if ( $sKeyToReset == 1 || $sKeyToReset === true ) {
				delete_option( $this->oProp->sOptionKey );
				$this->setSettingNotice( $this->oMsg->__( 'option_been_reset' ) );
				return array();
			}
			
			unset( $this->oProp->aOptions[ trim( $sKeyToReset ) ], $aInput[ trim( $sKeyToReset ) ] );
			update_option( $this->oProp->sOptionKey, $this->oProp->aOptions );
			$this->setSettingNotice( $this->oMsg->__( 'specified_option_been_deleted' ) );
		
			return $aInput;	// the returned array will be saved with the Settings API.
		}
		
		/**
		 * Sets the given URL's transient.
		 */
		private function _setRedirectTransients( $sURL ) {
			if ( empty( $sURL ) ) return;
			$sTransient = md5( trim( "redirect_{$this->oProp->sClassName}_{$_POST['page_slug']}" ) );
			return set_transient( $sTransient, $sURL , 60*2 );
		}
		
		/**
		 * Retrieves the target key's value associated with the given data to a custom submit button.
		 * 
		 * This method checks if the associated submit button is pressed with the input fields.
		 * 
		 * @since			2.0.0
		 * @return			mixed			Returns null if no button is found and the associated link url if found. Otherwise, the URL associated with the button.
		 */ 
		private function _getPressedCustomSubmitButtonSiblingValue( $aPostElements, $sTargetKey='url' ) {	
		
			foreach( $aPostElements as $field_name => $aSubElements ) {
				
				/*
				 * $aSubElements['name']	- the input field name property of the submit button, delimited by pipe (|) e.g. APF_GettingStarted|first_page|submit_buttons|submit_button_link
				 * $aSubElements['url']	- the URL to redirect to. e.g. http://www.somedomain.com
				 * */
				$aNameKeys = explode( '|', $aSubElements[ 'name' ] );		// the 'name' key must be set.
				
				// Count of 4 means it's a single element. Count of 5 means it's one of multiple elements.
				// The isset() checks if the associated button is actually pressed or not.
				if ( count( $aNameKeys ) == 2 && isset( $_POST[ $aNameKeys[0] ][ $aNameKeys[1] ] ) )
					return $aSubElements[ $sTargetKey ];
				if ( count( $aNameKeys ) == 3 && isset( $_POST[ $aNameKeys[0] ][ $aNameKeys[1] ][ $aNameKeys[2] ] ) )
					return $aSubElements[ $sTargetKey ];
				if ( count( $aNameKeys ) == 4 && isset( $_POST[ $aNameKeys[0] ][ $aNameKeys[1] ][ $aNameKeys[2] ][ $aNameKeys[3] ] ) )
					return $aSubElements[ $sTargetKey ];
					
			}
			
			return null;	// not found
			
		}

		/**
		 * Processes the imported data.
		 * 
		 * @since			2.0.0
		 * @since			2.1.5			Added additional filters with field id and input id.
		 */
		private function _importOptions( $aStoredOptions, $sPageSlug, $sTabSlug ) {
			
			$oImport = new AdminPageFramework_ImportOptions( $_FILES['__import'], $_POST['__import'] );	
			$sPressedFieldID = $oImport->getSiblingValue( 'field_id' );
			$sPressedInputID = $oImport->getSiblingValue( 'input_id' );
			$bMerge = $oImport->getSiblingValue( 'is_merge' );
			
			// Check if there is an upload error.
			if ( $oImport->getError() > 0 ) {
				$this->setSettingNotice( $this->oMsg->__( 'import_error' ) );	
				return $aStoredOptions;	// do not change the framework's options.
			}

			// Apply filters to the uploaded file's MIME type.
			$aMIMEType = $this->oUtil->addAndApplyFilters(
				$this,
				array( "import_mime_types_{$sPageSlug}_{$sTabSlug}", "import_mime_types_{$sPageSlug}", "import_mime_types_{$this->oProp->sClassName}_{$sPressedInputID}", "import_mime_types_{$this->oProp->sClassName}_{$sPressedFieldID}", "import_mime_types_{$this->oProp->sClassName}" ),
				array( 'text/plain', 'application/octet-stream' ),        // .json file is dealt as a binary file.
				$sPressedFieldID,
				$sPressedInputID
			);                

			// Check the uploaded file MIME type.
			if ( ! in_array( $oImport->getType(), $aMIMEType ) ) {        
				$this->setSettingNotice( $this->oMsg->___( 'uploaded_file_type_not_supported' ) );
				return $aStoredOptions;        // do not change the framework's options.
			}
			
			// Check the uploaded file type.
			if ( ! in_array( $oImport->getType(), array( 'text/plain', 'application/octet-stream' ) ) ) {	// .json file is dealt as binary file.
				$this->setSettingNotice( $this->oMsg->__( 'uploaded_file_type_not_supported' ) );		
				return $aStoredOptions;	// do not change the framework's options.
			}
			
			// Retrieve the importing data.
			$vData = $oImport->getImportData();
			if ( $vData === false ) {
				$this->setSettingNotice( $this->oMsg->__( 'could_not_load_importing_data' ) );		
				return $aStoredOptions;	// do not change the framework's options.
			}
			
			// Apply filters to the data format type.
			$sFormatType = $this->oUtil->addAndApplyFilters(
				$this,
				array( "import_format_{$sPageSlug}_{$sTabSlug}", "import_format_{$sPageSlug}", "import_format_{$this->oProp->sClassName}_{$sPressedInputID}", "import_format_{$this->oProp->sClassName}_{$sPressedFieldID}", "import_format_{$this->oProp->sClassName}" ),
				$oImport->getFormatType(),	// the set format type, array, json, or text.
				$sPressedFieldID,
				$sPressedInputID
			);	// import_format_{$sPageSlug}_{$sTabSlug}, import_format_{$sPageSlug}, import_format_{$sClassName}_{pressed input id}, import_format_{$sClassName}_{pressed field id}, import_format_{$sClassName}		

			// Format it.
			$oImport->formatImportData( $vData, $sFormatType );	// it is passed as reference.	
			
			// If a custom option key is set,
			// Apply filters to the importing option key.
			$sImportOptionKey = $this->oUtil->addAndApplyFilters(
				$this,
				array( "import_option_key_{$sPageSlug}_{$sTabSlug}", "import_option_key_{$sPageSlug}", "import_option_key_{$this->oProp->sClassName}_{$sPressedInputID}", "import_option_key_{$this->oProp->sClassName}_{$sPressedFieldID}", "import_option_key_{$this->oProp->sClassName}" ),
				$oImport->getSiblingValue( 'option_key' ),	
				$sPressedFieldID,
				$sPressedInputID
			);	// import_option_key_{$sPageSlug}_{$sTabSlug}, import_option_key_{$sPageSlug}, import_option_key_{$sClassName}_{pressed input id}, import_option_key_{$sClassName}_{pressed field id}, import_option_key_{$sClassName}			
			
			// Apply filters to the importing data.
			$vData = $this->oUtil->addAndApplyFilters(
				$this,
				array( "import_{$sPageSlug}_{$sTabSlug}", "import_{$sPageSlug}", "import_{$this->oProp->sClassName}_{$sPressedInputID}", "import_{$this->oProp->sClassName}_{$sPressedFieldID}", "import_{$this->oProp->sClassName}" ),
				$vData,
				$aStoredOptions,
				$sPressedFieldID,
				$sPressedInputID,
				$sFormatType,
				$sImportOptionKey,
				$bMerge
			);

			// Set the update notice
			$bEmpty = empty( $vData );
			$this->setSettingNotice( 
				$bEmpty ? $this->oMsg->__( 'not_imported_data' ) : $this->oMsg->__( 'imported_data' ), 
				$bEmpty ? 'error' : 'updated',
				$this->oProp->sOptionKey,	// message id
				false	// do not override 
			);
					
			if ( $sImportOptionKey != $this->oProp->sOptionKey ) {
				update_option( $sImportOptionKey, $vData );
				return $aStoredOptions;	// do not change the framework's options.
			}
		
			// The option data to be saved will be returned.
			return $bMerge ?
				$this->oUtil->unitArrays( $vData, $aStoredOptions )
				: $vData;
							
		}
		
		private function _exportOptions( $vData, $sPageSlug, $sTabSlug ) {

			$oExport = new AdminPageFramework_ExportOptions( $_POST['__export'], $this->oProp->sClassName );
			$sPressedFieldID = $oExport->getSiblingValue( 'field_id' );
			$sPressedInputID = $oExport->getSiblingValue( 'input_id' );
			
			// If the data is set in transient,
			$vData = $oExport->getTransientIfSet( $vData );
		
			// Get the field ID.
			$sFieldID = $oExport->getFieldID();
		
			// Add and apply filters. - adding filters must be done in this class because the callback method belongs to this class 
			// and the magic method should be triggered.			
			$vData = $this->oUtil->addAndApplyFilters(
				$this,
				array( "export_{$sPageSlug}_{$sTabSlug}", "export_{$sPageSlug}", "export_{$this->oProp->sClassName}_{$sPressedInputID}", "export_{$this->oProp->sClassName}_{$sPressedFieldID}", "export_{$this->oProp->sClassName}" ),
				$vData,
				$sPressedFieldID,
				$sPressedInputID
			);	// export_{$sPageSlug}_{$sTabSlug}, export_{$sPageSlug}, export_{$sClassName}_{pressed input id}, export_{$sClassName}_{pressed field id}, export_{$sClassName}	
			
			$sFileName = $this->oUtil->addAndApplyFilters(
				$this,
				array( "export_name_{$sPageSlug}_{$sTabSlug}", "export_name_{$sPageSlug}", "export_name_{$this->oProp->sClassName}_{$sPressedInputID}", "export_name_{$this->oProp->sClassName}_{$sPressedFieldID}", "export_name_{$this->oProp->sClassName}" ),
				$oExport->getFileName(),
				$sPressedFieldID,
				$sPressedInputID
			);	// export_name_{$sPageSlug}_{$sTabSlug}, export_name_{$sPageSlug}, export_name_{$sClassName}_{pressed input id}, export_name_{$sClassName}_{pressed field id}, export_name_{$sClassName}	
	
			$sFormatType = $this->oUtil->addAndApplyFilters(
				$this,
				array( "export_format_{$sPageSlug}_{$sTabSlug}", "export_format_{$sPageSlug}", "export_format_{$this->oProp->sClassName}_{$sPressedInputID}", "export_format_{$this->oProp->sClassName}_{$sPressedFieldID}", "export_format_{$this->oProp->sClassName}" ),
				$oExport->getFormat(),
				$sPressedFieldID,
				$sPressedInputID
			);	// export_format_{$sPageSlug}_{$sTabSlug}, export_format_{$sPageSlug}, export_format_{$sClassName}_{pressed input id}, export_format_{$sClassName}_{pressed field id}, export_format_{$sClassName}	
								
			$oExport->doExport( $vData, $sFileName, $sFormatType );
			exit;
			
		}
	
		/**
		 * Apples validation filters to the submitted input data.
		 * 
		 * @since			2.0.0
		 * @since			2.1.5			Added the $sPressedFieldID and $sPressedInputID parameters.
		 * @return			array			The filtered input array.
		 */
		private function _getFilteredOptions( $aInput, $sPageSlug, $sTabSlug, $sPressedFieldID, $sPressedInputID ) {

			$_aOptions = $this->oUtil->uniteArrays( $this->oProp->aOptions, $this->oProp->getDefaultOptions() );

			// for the input ID
			if ( $sPressedInputID )
				$aInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$this->oProp->sClassName}_{$sPressedInputID}", $aInput, $_aOptions );
			
			// for the field ID
			if ( $sPressedFieldID )
				$aInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$this->oProp->sClassName}_{$sPressedFieldID}", $aInput, $_aOptions );
							
			// for tabs
			if ( $sTabSlug && $sPageSlug )	{	
				$aInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$sPageSlug}_{$sTabSlug}", $aInput, $this->_getTabOptions( $_aOptions, $sPageSlug, $sTabSlug ) );	// $aInput: new values, $aStoredPageOptions: old values
				$aInput = $this->oUtil->uniteArrays( $aInput, $this->_getOtherTabOptions( $_aOptions, $sPageSlug, $sTabSlug ) );
			}
			
			// for pages	
			if ( $sPageSlug )	{
				$aInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$sPageSlug}", $aInput, $this->_getPageOptions( $_aOptions, $sPageSlug ) ); // $aInput: new values, $aStoredPageOptions: old values
				$aInput = $this->oUtil->uniteArrays( $aInput, $this->_getOtherPageOptions( $_aOptions, $sPageSlug ) );
			}
		
			// for the class
			$aInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$this->oProp->sClassName}", $aInput, $_aOptions );

			return $aInput;
		
		}	
			
			/**
			 * Retrieves the stored options of the given tab slug.
			 * @since			3.0.0
			 */
			private function _getTabOptions( $aOptions, $sPageSlug, $sTabSlug='' ) {
				
				$_aStoredOptionsOfTheTab = array();
				if ( ! $sTabSlug ) return $_aStoredOptionsOfTheTab;
				foreach( $this->oProp->aFields as $_aField ) {
					if ( isset( $_aField['page_slug'], $_aField['tab_slug'] ) && $_aField['page_slug'] == $sPageSlug && $_aField['tab_slug'] == $sTabSlug ) {
						if ( array_key_exists( $_aField['field_id'], $aOptions ) )
							$_aStoredOptionsOfTheTab[ $_aField['field_id'] ] = $aOptions[ $_aField['field_id'] ];
					}
				}		
				return $_aStoredOptionsOfTheTab;
				
			}
	
			/**
			 * Retrieves the stored options of the given page slug.
			 * 
			 * The other pages' option data will not be contained in the returning array.
			 * This is used to pass the old option array to the validation callback method.
			 * 
			 * @since			2.0.0
			 * @return			array			the stored options of the given page slug. If not found, an empty array will be returned.
			 */ 
			private function _getPageOptions( $aOptions, $sPageSlug ) {
						
				$_aStoredOptionsOfThePage = array();
				foreach( $this->oProp->aFields as $_aField ) {
					if ( isset( $_aField['page_slug'] ) && $_aField['page_slug'] == $sPageSlug ) {
						if ( array_key_exists( $_aField['field_id'], $aOptions ) )
							$_aStoredOptionsOfThePage[ $_aField['field_id'] ] = $aOptions[ $_aField['field_id'] ];
					}
				}
				return $_aStoredOptionsOfThePage;
				
			}
				
			/**
			 * Retrieves the stored options excluding the currently specified tab's sections and their fields.
			 * 
			 * This is used to merge the submitted form data with the previously stored option data of the form elements 
			 * that belong to the in-page tab of the given page.
			 * 
			 * @since			2.0.0
			 * @since			3.0.0			The second parameter was changed to a tab slug.
			 * @return			array			the stored options excluding the currently specified tab's sections and their fields.
			 * 	 If not found, an empty array will be returned.
			 */ 
			private function _getOtherTabOptions( $aOptions, $sPageSlug, $sTabSlug ) {

				$_aStoredOptionsNotOfTheTab = array();
				foreach( $this->oProp->aFields as $_aField ) {
					if ( isset( $_aField['page_slug'], $_aField['tab_slug'] ) && $_aField['page_slug'] == $sPageSlug && $_aField['tab_slug'] != $sTabSlug ) {
						if ( array_key_exists( $_aField['field_id'], $aOptions ) )
							$_aStoredOptionsNotOfTheTab[ $_aField['field_id'] ] = $aOptions[ $_aField['field_id'] ];
					}
				}					
				return $_aStoredOptionsNotOfTheTab;
				
			}
				
			/**
			 * Retrieves the stored options excluding the key of the given page slug.
			 * 
			 * This is used to merge the submitted form input data with the previously stored option data except the given page.
			 * 
			 * @since			2.0.0
			 * @return			array			the array storing the options excluding the key of the given page slug. 
			 */ 
			private function _getOtherPageOptions( $aOptions, $sPageSlug ) {

				$_aStoredOptionsNotOfThePage = array();
				foreach( $this->oProp->aFields as $_aField ) {
					if ( isset( $_aField['page_slug'] ) && $_aField['page_slug'] != $sPageSlug ) {
						if ( array_key_exists( $_aField['field_id'], $aOptions ) )
							$_aStoredOptionsNotOfThePage[ $_aField['field_id'] ] = $aOptions[ $_aField['field_id'] ];
					}
				}			
				return $_aStoredOptionsNotOfThePage;
				
			}
	
	/**
	 * Renders the registered setting fields.
	 * 
	 * @internal
	 * @since			2.0.0
	 * @remark			the protected scope is used because it's called from an extended class.
	 * @return			void
	 */ 
	protected function _renderSettingField( $sFieldID, $sPageSlug ) {
			
		// If the specified field does not exist, do nothing.
		if ( ! isset( $this->oProp->aFields[ $sFieldID ] ) ) return;	// if it is not added, return
		$aField = $this->oProp->aFields[ $sFieldID ];
		
		// Retrieve the field error array.
		$this->aFieldErrors = isset( $this->aFieldErrors ) ? $this->aFieldErrors : $this->_getFieldErrors( $sPageSlug ); 

		// Render the form field. 		
		$sFieldType = isset( $this->oProp->aFieldTypeDefinitions[ $aField['type'] ]['hfRenderField'] ) && is_callable( $this->oProp->aFieldTypeDefinitions[ $aField['type'] ]['hfRenderField'] )
			? $aField['type']
			: 'default';	// the predefined reserved field type is applied if the parsing field type is not defined(not found).

		$oField = new AdminPageFramework_InputField( $aField, $this->oProp->aOptions, $this->aFieldErrors, $this->oProp->aFieldTypeDefinitions, $this->oMsg );
		$sFieldOutput = $oField->_getInputFieldOutput();	// field output
		unset( $oField );	// release the object for PHP 5.2.x or below.

		echo $this->oUtil->addAndApplyFilters(
			$this,
			array( 
				'field_' . $this->oProp->sClassName . '_' . $sFieldID	// field_ + {extended class name} + _ {field id}
			),
			$sFieldOutput,
			$aField // the field array
		);
	
	}
	
	/**
	 * Retrieves the settings error array set by the user in the validation callback.
	 * 
	 * @since				2.0.0
	 * @since				2.1.2			Added the second parameter. 
	 * @since				3.0.0			Changed the scope to private from protected sicne it is only used in this class.
	 * @access				private.
	 */
	private function _getFieldErrors( $sPageSlug, $bDelete=true ) {
		
		// If a form submit button is not pressed, there is no need to set the setting errors.
		if ( ! isset( $_GET['settings-updated'] ) ) return null;
		
		// Find the transient.
		$sTransient = md5( $this->oProp->sClassName . '_' . $sPageSlug );
		$aFieldErrors = get_transient( $sTransient );
		if ( $bDelete )
			delete_transient( $sTransient );	
		return $aFieldErrors;

	}
	
	/**
	 * Renders the filtered section description.
	 * 
	 * @internal
	 * @since			2.0.0
	 * @remark			the protected scope is used because it's called from an extended class.
	 * @remark			This is the redirected callback for the section description method from __call().
	 * @return			void
	 */ 	
	protected function _renderSectionDescription( $sMethodName ) {		

		$sSectionID = substr( $sMethodName, strlen( 'section_pre_' ) );	// X will be the section ID in section_pre_X
		
		if ( ! isset( $this->oProp->aSections[ $sSectionID ] ) ) return;	// if it is not added
		
		echo $this->oUtil->addAndApplyFilters(
			$this,
			array( 'section_' . $this->oProp->sClassName . '_' . $sSectionID ),	// section_ + {extended class name} + _ {section id}
			'<p>' . $this->oProp->aSections[ $sSectionID ]['description'] . '</p>',	 // the p-tagged description string
			$this->oProp->aSections[ $sSectionID ]['description']	// the original description
		);		
			
	}
	
	/**
	 * Check if a redirect transient is set and if so it redirects to the set page.
	 * 
	 * @remark			A callback method for the admin_init hook.
	 * @internal
	 */
	public function _replyToCheckRedirects() {

		// So it's not options.php. Now check if it's one of the plugin's added page. If not, do nothing.
		if ( ! ( isset( $_GET['page'] ) ) || ! $this->oProp->isPageAdded( $_GET['page'] ) ) return; 
		
		// If the Settings API has not updated the options, do nothing.
		if ( ! ( isset( $_GET['settings-updated'] ) && ! empty( $_GET['settings-updated'] ) ) ) return;

		// Check the settings error transient.
		$aError = $this->_getFieldErrors( $_GET['page'], false );
		if ( ! empty( $aError ) ) return;
		
		// Okay, it seems the submitted data have been updated successfully.
		$sTransient = md5( trim( "redirect_{$this->oProp->sClassName}_{$_GET['page']}" ) );
		$sURL = get_transient( $sTransient );
		if ( $sURL === false ) return;
		
		// The redirect URL seems to be set.
		delete_transient( $sTransient );	// we don't need it any more.
					
		// Go to the page.
		die( wp_redirect( $sURL ) );
		
	}
	
	/**
	 * Registers the setting sections and fields.
	 * 
	 * This methods passes the stored section and field array contents to the <em>add_settings_section()</em> and <em>add_settings_fields()</em> functions.
	 * Then perform <em>register_setting()</em>.
	 * 
	 * The filters will be applied to the section and field arrays; that means that third-party scripts can modify the arrays.
	 * Also they get sorted before being registered based on the set order.
	 * 
	 * @since			2.0.0
	 * @since			2.1.5			Added the ability to define custom field types.
	 * @remark			This method is not intended to be used by the user.
	 * @remark			The callback method for the <em>admin_menu</em> hook.
	 * @return			void
	 * @internal
	 */ 
	public function _replyToRegisterSettings() {
		
		/* 1. Format ( sanitize ) the section and field arrays and apply conditions to the sections and fields and drop unnecessary items. 
		 * Note that we use local variables for the applying items. This allows the framework to refer the added sections and fields for later use. 
		 * */
		$this->_formatSectionArrays( $this->oProp->aSections );	// passed by reference.
		$this->_formatFieldArrays( $this->oProp->aFields, $this->oProp->aSections );	
		$_aSections = $this->_applyConditionsForSections( $this->oProp->aSections );
		$_aFields = $this->_applyConditionsForFields( $this->oProp->aFields, $_aSections );

		/* 2. If there is no section or field to add, do nothing. */
		if (  $GLOBALS['pagenow'] != 'options.php' && ( count( $_aSections ) == 0 || count( $_aFields ) == 0 ) ) return;

		/* 3. Define field types. This class adds filters for the field type definitions so that framework's built-in field types will be added. */
		new AdminPageFramework_RegisterBuiltinFieldTypes( $this->oProp->aFieldTypeDefinitions, $this->oProp->sClassName, $this->oMsg );
		$this->oProp->aFieldTypeDefinitions = $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $sFilter, $vInput, $vArgs...
			$this,
			'field_types_' . $this->oProp->sClassName,	// 'field_types_' . {extended class name}
			$this->oProp->aFieldTypeDefinitions
		);		

		/* 4. Register settings sections */
		uasort( $_aSections, array( $this, '_sortByOrder' ) ); 
		foreach( $_aSections as $aSection ) {
			
			/* 4-1. Add the given section */
			add_settings_section(
				$aSection['section_id'],	//  section ID
				"<a id='{$aSection['section_id']}'></a>" . $aSection['title'],	// title - place the anchor in front of the title.
				array( $this, 'section_pre_' . $aSection['section_id'] ), 		// callback function -  this will trigger the __call() magic method.
				$aSection['page_slug']	// page
			);
			
			/* 4-2. For the contextual help pane */
			if ( ! empty( $aSection['help'] ) )
				$this->addHelpTab( 
					array(
						'page_slug'					=> $aSection['page_slug'],
						'page_tab_slug'				=> $aSection['tab_slug'],
						'help_tab_title'			=> $aSection['title'],
						'help_tab_id'				=> $aSection['section_id'],
						'help_tab_content'			=> $aSection['help'],
						'help_tab_sidebar_content'	=> $aSection['help_aside'] ? $aSection['help_aside'] : "",
					)
				);
				
		}
		
		/* 5. Register settings fields	*/
		uasort( $_aFields, array( $this, '_sortByOrder' ) ); 
		foreach( $_aFields as $aField ) {

			/* 5-1. Add the given field. */
			add_settings_field(
				$aField['field_id'],
				"<a id='{$aField['field_id']}'></a><span title='{$aField['tip']}'>{$aField['title']}</span>",
				array( $this, 'field_pre_' . $aField['field_id'] ),	// callback function - will trigger the __call() magic method.
				$this->_getPageSlugBySectionID( $aField['section_id'] ), // page slug
				$aField['section_id'],	// section
				$aField['field_id']		// arguments - pass the field ID to the callback function
			);	

			/* 5-2. Set relevant scripts and styles for the input field. */
			$this->_setFieldHeadTagElements( $aField );
			
			/* 5-3. For the contextual help pane, */
			if ( ! empty( $aField['help'] ) )
				$this->addHelpTab( 
					array(
						'page_slug'					=> $aField['page_slug'],
						'page_tab_slug'				=> $aField['tab_slug'],
						'help_tab_title'			=> $aField['section_title'],
						'help_tab_id'				=> $aField['section_id'],
						'help_tab_content'			=> "<span class='contextual-help-tab-title'>" . $aField['title'] . "</span> - " . PHP_EOL
														. $aField['help'],
						'help_tab_sidebar_content'	=> $aField['help_aside'] ? $aField['help_aside'] : "",
					)
				);

		}
		
		/* 6. Register the settings. */
		$this->oProp->bEnableForm = true;	// Set the form enabling flag so that the <form></form> tag will be inserted in the page.
		register_setting(	
			$this->oProp->sOptionKey,	// the option group name.	
			$this->oProp->sOptionKey,	// the option key name that will be stored in the option table in the database.
			array( $this, 'validation_pre_' . $this->oProp->sClassName )	// validation method
		); 
		
	}
		/**
		 * Retrieves the page slug that the settings section belongs to.		
		 * 
		 * @since			2.0.0
		 * @return			string|null
		 * @internal
		 */ 
		private function _getPageSlugBySectionID( $sSectionID ) {
			return isset( $this->oProp->aSections[ $sSectionID ]['page_slug'] )
				? $this->oProp->aSections[ $sSectionID ]['page_slug']
				: null;			
		}
		/**
		 * Sets the given field type's enqueuing scripts and styles.
		 * 
		 * A helper function for the above _replyToRegisterSettings() method.
		 * 
		 * @since			2.1.5
		 * @internal
		 */
		private function _setFieldHeadTagElements( $aField ) {
			
			$sFieldType = $aField['type'];
			
			// Set the global flag to indicate whether the elements are already added and enqueued.
			if ( isset( $GLOBALS['aAdminPageFramework']['aFieldFlags'][ $sFieldType ] ) && $GLOBALS['aAdminPageFramework']['aFieldFlags'][ $sFieldType ] ) return;
			$GLOBALS['aAdminPageFramework']['aFieldFlags'][ $sFieldType ] = true;

			// If the field type is not defined, return.
			if ( ! isset( $this->oProp->aFieldTypeDefinitions[ $sFieldType ] ) ) return;

			if ( is_callable( $this->oProp->aFieldTypeDefinitions[ $sFieldType ]['hfFieldLoader'] ) )
				call_user_func_array( $this->oProp->aFieldTypeDefinitions[ $sFieldType ]['hfFieldLoader'], array() );		
			
			if ( is_callable( $this->oProp->aFieldTypeDefinitions[ $sFieldType ]['hfGetScripts'] ) )
				$this->oProp->sScript .= call_user_func_array( $this->oProp->aFieldTypeDefinitions[ $sFieldType ]['hfGetScripts'], array() );
				
			if ( is_callable( $this->oProp->aFieldTypeDefinitions[ $sFieldType ]['hfGetStyles'] ) )
				$this->oProp->sStyle .= call_user_func_array( $this->oProp->aFieldTypeDefinitions[ $sFieldType ]['hfGetStyles'], array() );
				
			if ( is_callable( $this->oProp->aFieldTypeDefinitions[ $sFieldType ]['hfGetIEStyles'] ) )
				$this->oProp->sStyleIE .= call_user_func_array( $this->oProp->aFieldTypeDefinitions[ $sFieldType ]['hfGetIEStyles'], array() );					
				
			foreach( $this->oProp->aFieldTypeDefinitions[ $sFieldType ]['aEnqueueStyles'] as $asSource ) {
				if ( is_string( $asSource ) )
					$this->oHeadTag->_enqueueStyle( $asSource );
				else if ( is_array( $asSource ) && isset( $asSource[ 'src' ] ) )
					$this->oHeadTag->_enqueueStyle( $asSource[ 'src' ], '', '', $asSource );
			}
			foreach( $this->oProp->aFieldTypeDefinitions[ $sFieldType ]['aEnqueueScripts'] as $asSource ) {
				if ( is_string( $asSource ) )
					$this->oHeadTag->_enqueueScript( $asSource );
				else if ( is_array( $asSource ) && isset( $asSource[ 'src' ] ) )
					$this->oHeadTag->_enqueueScript( $asSource[ 'src' ], '', '', $asSource );
			}			
				
		}
	
		/**
		 * Formats the given section arrays.
		 * 
		 * @since			2.0.0
		 */ 
		private function _formatSectionArrays( &$aSections ) {

			// Apply filters to let other scripts to add sections.
			$aSections = $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $sFilter, $vInput, $vArgs...
				$this,
				"sections_{$this->oProp->sClassName}",
				$aSections
			);
						
			// Since the section array may have been modified by filters, sanitize the elements and 
			// apply the conditions to remove unnecessary elements and put new orders.
			$_aNewSectionArray = array();
			foreach( $aSections as $aSection ) {
			
				if ( ! is_array( $aSection ) ) continue;
			
				$aSection = $aSection + self::$_aStructure_Section;	// avoid undefined index warnings.
				
				// Sanitize the IDs since they are used as a callback method name, the slugs as well.
				$aSection['section_id'] = $this->oUtil->sanitizeSlug( $aSection['section_id'] );
				$aSection['page_slug'] = $this->oUtil->sanitizeSlug( $aSection['page_slug'] );
				$aSection['tab_slug'] = $this->oUtil->sanitizeSlug( $aSection['tab_slug'] );
				
				// Check the mandatory keys' values.
				if ( ! isset( $aSection['section_id'], $aSection['page_slug'] ) ) continue;	// these keys are necessary.
			
				// Set the order.
				$aSection['order']	= is_numeric( $aSection['order'] ) ? $aSection['order'] : count( $_aNewSectionArray ) + 10;
			
				// Add the section array to the returning array.
				$_aNewSectionArray[ $aSection['section_id'] ] = $aSection;
				
			}
			$aSections = $_aNewSectionArray;
			
		}
		
		/**
		 * Applies conditions to the given sections.
		 * 
		 * @remark			This must be done after performing the _formatSectionArrays() method.
		 * @since			3.0.0
		 */
		private function _applyConditionsForSections( $aSections ) {

			$_sCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;
			$_aNewSectionArray = array();
			foreach( $aSections as $_aSection ) {

				// If the page slug does not match the current loading page, there is no need to register form sections and fields.
				if ( $GLOBALS['pagenow'] != 'options.php' && ! $_sCurrentPageSlug || $_sCurrentPageSlug !=  $_aSection['page_slug'] ) continue;

				// If this section does not belong to the currently loading page tab, skip.
				if ( ! $this->_isSettingSectionOfCurrentTab( $_aSection ) )  continue;
				
				// If the access level is set and it is not sufficient, skip.
				$_aSection['capability'] = isset( $_aSection['capability'] ) ? $_aSection['capability'] : $this->oProp->sCapability;
				if ( ! current_user_can( $_aSection['capability'] ) ) continue;	// since 1.0.2.1
			
				// If a custom condition is set and it's not true, skip,
				if ( $_aSection['if'] !== true ) continue;		
				
				$_aNewSectionArray[ $_aSection['section_id'] ] = $_aSection;
				
			}
			return $_aNewSectionArray;
			
		}
			/**
			 * Checks if the given section belongs to the currently loading tab.
			 * 
			 * @since			2.0.0
			 * @return			boolean			Returns true if the section belongs to the current tab page. Otherwise, false.
			 */ 	
			private function _isSettingSectionOfCurrentTab( $aSection ) {

				// Determine: 
				// 1. if the current tab matches the given tab slug. Yes -> the section should be registered.
				// 2. if the current page is the default tab. Yes -> the section should be registered.

				// If the tab slug is not specified, it means that the user wants the section to be visible in the page regardless of tabs.
				if ( ! isset( $aSection['tab_slug'] ) ) return true;
				
				// 1. If the checking tab slug and the current loading tab slug is the same, it should be registered.
				$sCurrentTab =  isset( $_GET['tab'] ) ? $_GET['tab'] : null;
				if ( $aSection['tab_slug'] == $sCurrentTab )  return true;

				// 2. If $_GET['tab'] is not set and the page slug is stored in the tab array, 
				// consider the default tab which should be loaded without the tab query value in the url
				$sPageSlug = $aSection['page_slug'];
				if ( ! isset( $_GET['tab'] ) && isset( $this->oProp->aInPageTabs[ $sPageSlug ] ) ) {
				
					$sDefaultTabSlug = isset( $this->oProp->aDefaultInPageTabs[ $sPageSlug ] ) ? $this->oProp->aDefaultInPageTabs[ $sPageSlug ] : '';
					if ( $sDefaultTabSlug  == $aSection['tab_slug'] ) return true;		// should be registered.			
						
				}
						
				// Otherwise, false.
				return false;
				
			}	
			
		/**
		 * Formats the given field arrays.
		 * 
		 * @since			2.0.0
		 */ 
		private function _formatFieldArrays( &$aFields, &$aSections ) {
			
			// Apply filters to let other scripts to add fields.
			$aFields = $this->oUtil->addAndApplyFilter(	// Parameters: $oCallerObject, $aFilters, $vInput, $vArgs...
				$this,
				"fields_{$this->oProp->sClassName}",
				$aFields
			); 
			
			// Apply the conditions to remove unnecessary elements and put new orders.
			$_aNewFieldArrays = array();
			foreach( $aFields as $_aField ) {
			
				if ( ! is_array( $_aField ) ) continue;		// the element must be an array.
				
				$_aField = $_aField + self::$_aStructure_Field;	// avoid undefined index warnings.
				
				// Sanitize the IDs since they are used as a callback method name.
				$_aField['field_id'] = $this->oUtil->sanitizeSlug( $_aField['field_id'] );
				$_aField['section_id'] = $this->oUtil->sanitizeSlug( $_aField['section_id'] );
						
				// Check the mandatory keys' values.
				if ( ! isset( $_aField['field_id'], $_aField['section_id'], $_aField['type'] ) ) continue;	// these keys are necessary.
				
				// Set the order.
				$_aField['order']	= is_numeric( $_aField['order'] ) ? $_aField['order'] : count( $_aNewFieldArrays ) + 10;
				
				// Set the tip, option key, instantiated class name, and page slug elements.
				$_aField['tip'] = strip_tags( isset( $_aField['tip'] ) ? $_aField['tip'] : $_aField['description'] );
				$_aField['option_key'] = $this->oProp->sOptionKey;
				$_aField['class_name'] = $this->oProp->sClassName;
				$_aField['page_slug'] = isset( $aSections[ $_aField['section_id'] ]['page_slug'] ) ? $aSections[ $_aField['section_id'] ]['page_slug'] : null;
				$_aField['tab_slug'] = isset( $aSections[ $_aField['section_id'] ]['tab_slug'] ) ? $aSections[ $_aField['section_id'] ]['tab_slug'] : null;
				$_aField['section_title'] = isset( $aSections[ $_aField['section_id'] ]['title'] ) ? $aSections[ $_aField['section_id'] ]['title'] : null;	// used for the contextual help pane.
				
				// Add the element to the new returning array.
				$_aNewFieldArrays[ $_aField['field_id'] ] = $_aField;
					
			}
			$aFields = $_aNewFieldArrays;
			
		}
		/**
		 * Applies conditions to the given fields.
		 * 
		 * @remark			This must be done after performing the _formatFieldArrays() method.
		 * @since	
		 */
		private function _applyConditionsForFields( $aFields, $aSections ) {
			
			$_aNewFieldArrays = array();
			foreach( $aFields as $_aField ) {
							
				// If the section that this field belongs to is not set, no need to register this field.
				// The $aSection property must be formatted prior to perform this method.
				if ( ! isset( $aSections[ $_aField['section_id'] ] ) ) continue;		
		
				// If the access level is not sufficient, skip.
				$_aField['capability'] = isset( $_aField['capability'] ) ? $_aField['capability'] : $this->oProp->sCapability;
				if ( ! current_user_can( $_aField['capability'] ) ) continue; 
							
				// If the condition is not met, skip.
				if ( $_aField['if'] !== true ) continue;		
			
				// Add the element to the new returning array.
				$_aNewFieldArrays[ $_aField['field_id'] ] = $_aField;
					
			}
			return $_aNewFieldArrays;
			
		}
}
endif;