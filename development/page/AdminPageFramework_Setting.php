<?php
if ( ! class_exists( 'AdminPageFramework_Setting' ) ) :
/**
 * Provides methods to add form elements with WordPress Settings API. 
 *
 * @abstract
 * @since		2.0.0
 * @extends		AdminPageFramework_Menu
 * @package		AdminPageFramework
 * @subpackage	Page
 * @var			array		$aFieldErrors						stores the settings field errors.
 */
abstract class AdminPageFramework_Setting extends AdminPageFramework_Menu {
		
	/**
	 * Stores the settings field errors. 
	 * 
	 * @since			2.0.0
	 * @var				array			Stores field errors.
	 * @internal
	 */ 
	protected $aFieldErrors;		// Do not set a value here since it is checked to see it's null.
	
	/**
	 * Defines the fields type.
	 * @since			3.0.0
	 * @internal
	 */
	static protected $_sFieldsType = 'page';
	
	/**
	 * Registers necessary hooks and sets up properties.
	 * 
	 * @internal
	 */
	function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability=null, $sTextDomain='admin-page-framework' ) {
		
		add_action( 'admin_menu', array( $this, '_replyToRegisterSettings' ), 100 );	// registers the settings
		add_action( 'admin_init', array( $this, '_replyToCheckRedirects' ) );	// redirects
		
		parent::__construct( $sOptionKey, $sCallerPath, $sCapability, $sTextDomain );
		
		$this->oProp->sFieldsType = self::$_sFieldsType;
		$this->oForm = new AdminPageFramework_FormElement;
		
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
	* @param			string			the text message to be displayed.
	* @param			string			( optional ) the type of the message, either "error" or "updated"  is used.
	* @param			string			( optional ) the ID of the message. This is used in the ID attribute of the message HTML element.
	* @param			integer			( optional ) false: do not override when there is a message of the same id. true: override the previous one.
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
	 * @since			2.0.0
	 * @since			3.0.0			Changed the scope to public from protected.
	 * @access 			public
	 * @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	 * @remark			The actual registration will be performed in the <em>_replyToRegisterSettings()</em> method with the <em>admin_menu</em> hook.
	 * @param			array|string			the section array or the target page slug. If the target page slug is set, the next section array can omit the page slug key.
	 * <strong>Section Array</strong>
	 * <ul>
	 * <li><strong>page_slug</strong> - (  required, string ) the page slug that the section belongs to.</li>
	 * <li><strong>section_id</strong> - ( optional, string ) the section ID. Avoid using non-alphabetic characters except underscore and numbers. If not set the internal section ID <em>_default</em> will be assigned.</li>
	 * <li><strong>tab_slug</strong> - ( optional, string ) the tab slug that the section belongs to.</li>
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
	 * @since			2.1.2
	 * @since			3.0.0			Changed the scope to public from protected.
	 * @access			public
	 * @remark			The actual registration will be performed in the <em>_replyToRegisterSettings()</em> method with the <em>admin_menu</em> hook.
	 * @param			array|string			the section array. If a string is passed, it is considered as a target page slug that will be used as a page slug element from the next call so that the element can be ommited.
	 * @return			void
	 */
	public function addSettingSection( $asSection ) {
				
		static $__sTargetPageSlug = null;	// stores the target page slug which will be applied when no page slug is specified.
		static $__sTargetTabSlug = null;	// stores the target tab slug which will be applied when no tab slug is specified.
		if ( ! is_array( $asSection ) ) {
			$__sTargetPageSlug = is_string( $asSection ) ? $asSection : $__sTargetPageSlug;
			return;
		} 
		
		$aSection = $asSection;
		$__sTargetPageSlug = isset( $aSection['page_slug'] ) ? $aSection['page_slug'] : $__sTargetPageSlug;
		$__sTargetTabSlug = isset( $aSection['tab_slug'] ) ? $aSection['tab_slug'] : $__sTargetTabSlug;		
		$aSection = $this->oUtil->uniteArrays( $aSection, array( 'page_slug' => $__sTargetPageSlug, 'tab_slug' => $__sTargetTabSlug ) );	// avoid undefined index warnings.
		
		$aSection['page_slug'] = $aSection['page_slug'] ? $this->oUtil->sanitizeSlug( $aSection['page_slug'] ) : ( $this->oProp->sDefaultPageSlug ? $this->oProp->sDefaultPageSlug : null );
		$aSection['tab_slug'] = $this->oUtil->sanitizeSlug( $aSection['tab_slug'] );
		
		if ( ! $aSection['page_slug'] ) return;	// The page slug is necessary.
		$this->oForm->addSection( $aSection );
		
	}
	
	/**
	* Removes the given section(s) by section ID.
	* 
	* This accesses the property storing the added section arrays and removes the specified ones.
	* 
	* <h4>Example</h4>
	* <code>$this->removeSettingSections( 'text_fields', 'selectors', 'another_section', 'yet_another_section' );
	* </code>
	* 
	* @since			2.0.0
	* @since			3.0.0			Changed the scope to public from protected.
	* @access 			public
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @remark			The actual registration will be performed in the <em>_replyToRegisterSettings()</em> method with the <em>admin_menu</em> hook.
	* @param			string			the section ID to remove.
	* @param			string			( optional ) another section ID to remove.
	* @param			string			( optional ) add more section IDs to the next parameters as many as necessary.
	* @return			void
	*/	
	public function removeSettingSections( $sSectionID1=null, $sSectionID2=null, $_and_more=null ) {	
		
		foreach( func_get_args() as $_sSectionID ) 
			$this->oForm->removeSection( $_sSectionID );
		
	}
	
	/**
	* Adds the given field array items into the field array property.
	* 
	* The passed field array must consist of the following keys. 
	*  
	* <h4>Example</h4>
	* <code>$this->addSettingFields(
	*		array(
	*			'field_id'	=>	'text',
	*			'section_id'	=>	'text_fields',
	*			'title'	=>	__( 'Text', 'admin-page-framework-demo' ),
	*			'description'	=>	__( 'Type something here.', 'admin-page-framework-demo' ),
	*			'type'	=>	'text',
	*			'order'	=>	1,
	*			'default'	=>	123456,
	*			'size'	=>	40,
	*		),	
	*		array(
	*			'field_id'	=>	'text_multiple',
	*			'section_id'	=>	'text_fields',
	*			'title'	=>	'Multiple Text Fields',
	*			'description'	=>	'These are multiple text fields.',
	*			'type'	=>	'text',
	*			'order'	=>	2,
	*			'default'	=>	'Hello World',
	*			'label'	=>	'First Item',
	*			'attributes'	=>	array(
	*				'size'	=>	30
	*			),
	*			array(
	*				'label'	=>	'Second Item',
	*				'default'	=>	'Foo bar',
	*				'attributes'	=>	array(
	*					'size'	=>	60,
	*				),
	*			),
	*			array(
	*				'label'	=>	'Third Item',
	*				'default'	=>	'Yes, we can.',
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
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @remark			The actual registration will be performed in the <em>_replyToRegisterSettings()</em> method with the <em>admin_menu</em> hook.
	* @param			array			the field definition array.
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
	* @since			2.1.2
	* @since			3.0.0			Changed the scope to public from protected.
	* @access			public
	* @param			array|string	the field array or the target section ID. If the target section ID is set, the section_id key can be omitted from the next passing field array.
	* @return			void
	*/	
	public function addSettingField( $asField ) {
		$this->oForm->addField( $asField );	
	}	
	
	/**
	* Removes the given field(s) by field ID.
	* 
	* This accesses the property storing the added field arrays and removes the specified ones.
	* 
	* <h4>Example</h4>
	* <code>$this->removeSettingFields( 'fieldID_A', 'fieldID_B', 'fieldID_C', 'fieldID_D' );
	* </code>
	* 
	* @since			2.0.0
	* @since			3.0.0			Changed the scope to public from protected.
	* @access 			public
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @remark			The actual registration will be performed in the <em>_replyToRegisterSettings()</em> method with the <em>admin_menu</em> hook.
	* @param			string			the field ID to remove.
	* @param			string			( optional ) another field ID to remove.
	* @param			string			( optional ) add more field IDs to the next parameters as many as necessary.
	* @return			void
	*/	
	public function removeSettingFields( $sFieldID1, $sFieldID2=null, $_and_more ) {
				
		foreach( func_get_args() as $_sFieldID ) $this->oForm->removeField( $_sFieldID );

	}	
			
	/**
	 * Sets the field error array. 
	 * 
	 * This is normally used in validation callback methods when the submitted user's input data have an issue.
	 * This method saves the given array in a temporary area( transient ) of the options database table.
	 * 
	 * <h4>Example</h4>
	 * <code>
	 *	public function validation_APF_Demo_verify_text_field_submit( $aNewInput, $aOldOptions ) {
	 *
	 *		// 1. Set a flag.
	 *		$bVerified = true;
	 *		
	 *		// 2. Prepare an error array. 
	 *		$aErrors = array();
	 *
	 *		// 3. Check if the submitted value meets your criteria.
	 *		if ( ! is_numeric( $aNewInput['verify_text_field'] ) ) {
	 *			$aErrors['verify_text_field'] = __( 'The value must be numeric:', 'admin-page-framework-demo' ) 
	 *				. $aNewInput['verify_text_field'];
	 *			$bVerified = false;
	 *		}
	 *	
	 *		// 4. An invalid value is found.
	 *		if ( ! $bVerified ) {
	 *			// 4-1. Set the error array for the input fields.
	 *			$this->setFieldErrors( $aErrors );		
	 *			$this->setSettingNotice( 'There was an error in your input.' );
	 *			return $aOldOptions;
	 *		}
	 *					
	 *		return $aNewInput;		
	 *
	 *	}
	 * </code>
	 * 
	 * @since			2.0.0
	 * @since			3.0.0			Changed the scope to public from protected.
	 * @remark			the user may use this method.
	 * @remark			the transient name is a MD5 hash of the extended class name + _ + page slug ( the passed ID )
	 * @param			array			the field error array. The structure should follow the one contained in the submitted $_POST array.
	 * @param			string			this should be the page slug of the page that has the dealing form field.
	 * @param			integer			the transient's lifetime. 300 seconds means 5 minutes.
	 */ 
	public function setFieldErrors( $aErrors, $sID=null, $nSavingDuration=300 ) {
		
		$sID = isset( $sID ) ? $sID : ( isset( $_POST['page_slug'] ) ? $_POST['page_slug'] : ( isset( $_GET['page'] ) ? $_GET['page'] : $this->oProp->sClassName ) );	
		set_transient( md5( $this->oProp->sClassName . '_' . $sID ), $aErrors, $nSavingDuration );	// store it for 5 minutes ( 60 seconds * 5 )
	
	}

	/**
	 * Retrieves the specified field value stored in the options by field ID.
	 *  
	 * @since			2.1.2
	 * @since			3.0.0			Changed the scope to public from protected. Dropped the sections. Made it return a default value even if it's not saved in the database.
	 * @access			public
	 * @param			string			The field ID.
	 * @return			array|string|null		If the field ID is not set in the saved option array, it will return null. Otherwise, the set value. 
	 * If the user has not submitted the form, the framework will try to return the default value set in the field definition array.
	 */
	public function getFieldValue( $sFieldID, $sSectionID='' ) {
		
		$_aOptions = $this->oUtil->uniteArrays( $this->oProp->aOptions, $this->oProp->getDefaultOptions( $this->oForm->aFields ) );
		
		/* If it's saved, return it */
		if ( ! $sSectionID ) {
			if ( array_key_exists( $sFieldID, $_aOptions ) )
				return $_aOptions[ $sFieldID ];
				
			// loop through section elements
			foreach( $_aOptions as $aOptions ) {
				if ( array_key_exists( $sFieldID, $aOptions ) )
					return $aOptions[ $sFieldID ];
			}
				
		}
		if ( $sSectionID )
			if ( array_key_exists( $sSectionID, $_aOptions ) && array_key_exists( $sFieldID, $_aOptions[ $sSectionID ] ) )
				return $_aOptions[ $sSectionID ][ $sFieldID ];
	
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
		
		/* 1-2. Retrieve the pressed submit field data */
		$sPressedFieldID = isset( $_POST['__submit'] ) ? $this->_getPressedSubmitButtonData( $_POST['__submit'], 'field_id' ) : '';
		$sPressedInputID = isset( $_POST['__submit'] ) ? $this->_getPressedSubmitButtonData( $_POST['__submit'], 'input_id' ) : '';
		$sPressedInputName = isset( $_POST['__submit'] ) ? $this->_getPressedSubmitButtonData( $_POST['__submit'], 'name' ) : '';
		$bIsReset = isset( $_POST['__submit'] ) ? $this->_getPressedSubmitButtonData( $_POST['__submit'], 'is_reset' ) : '';		// if the 'reset' key in the field definition array is set, this value will be set.
		$sKeyToReset = isset( $_POST['__submit'] ) ? $this->_getPressedSubmitButtonData( $_POST['__submit'], 'reset_key' ) : '';	// this will be set if the user confirms the reset action.
		$sSubmitSectionID = isset( $_POST['__submit'] ) ? $this->_getPressedSubmitButtonData( $_POST['__submit'], 'section_id' ) : '';
		
		/* 1-3. Execute the submit_{...} actions. */
		$this->oUtil->addAndDoActions(
			$this,
			array( 
				"submit_{$this->oProp->sClassName}_{$sPressedInputID}", 
				$sSubmitSectionID ? "submit_{$this->oProp->sClassName}_{$sSubmitSectionID}_{$sPressedFieldID}" : "submit_{$this->oProp->sClassName}_{$sPressedFieldID}",
				$sSubmitSectionID ? "submit_{$this->oProp->sClassName}_{$sSubmitSectionID}" : null,	// if null given, the method will ignore it
				isset( $_POST['tab_slug'] ) ? "submit_{$this->oProp->sClassName}_{$sPageSlug}_{$sTabSlug}" : null,	// if null given, the method will ignore it
				"submit_{$this->oProp->sClassName}_{sPageSlug}",
				"submit_{$this->oProp->sClassName}",
			)
		);                
		
		/* 2. Check if custom submit keys are set [part 1] */
		if ( isset( $_POST['__import']['submit'], $_FILES['__import'] ) ) 
			return $this->_importOptions( $this->oProp->aOptions, $sPageSlug, $sTabSlug );
		if ( isset( $_POST['__export']['submit'] ) ) 
			die( $this->_exportOptions( $this->oProp->aOptions, $sPageSlug, $sTabSlug ) );		
		if ( $bIsReset )
			return $this->_askResetOptions( $sPressedInputName, $sPageSlug, $sSubmitSectionID );
		if ( isset( $_POST['__submit'] ) && $sLinkURL = $this->_getPressedSubmitButtonData( $_POST['__submit'], 'link_url' ) )
			die( wp_redirect( $sLinkURL ) );	// if the associated submit button for the link is pressed, it will be redirected.
		if ( isset( $_POST['__submit'] ) && $sRedirectURL = $this->_getPressedSubmitButtonData( $_POST['__submit'], 'redirect_url' ) )
			$this->_setRedirectTransients( $sRedirectURL );
				
		/* 3. Apply validation filters - validation_{page slug}_{tab slug}, validation_{page slug}, validation_{instantiated class name} */
		$aInput = $this->_getFilteredOptions( $aInput, $sPageSlug, $sTabSlug );
		/* 4. Check if custom submit keys are set [part 2] - these should be done after applying the filters. */
		if ( $sKeyToReset )
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
		private function _askResetOptions( $sPressedInputName, $sPageSlug, $sSectionID ) {
			
			// Retrieve the pressed button's associated submit field ID.
			$aNameKeys = explode( '|', $sPressedInputName );	
			$sFieldID = $sSectionID 
				? $aNameKeys[ 2 ]	// Optionkey|section_id|field_id
				: $aNameKeys[ 1 ];	// OptionKey|field_id
			
			// Set up the field error array.
			$aErrors = array();
			if ( $sSectionID )
				$aErrors[ $sSectionID ][ $sFieldID ] = $this->oMsg->__( 'reset_options' );
			else
				$aErrors[ $sFieldID ] = $this->oMsg->__( 'reset_options' );
			$this->setFieldErrors( $aErrors );

				
			// Set a flag that the confirmation is displayed
			set_transient( md5( "reset_confirm_" . $sPressedInputName ), $sPressedInputName, 60*2 );
			
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
		 * @return			null|string			Returns null if no button is found and the associated link url if found. Otherwise, the URL associated with the button.
		 */ 
		private function _getPressedSubmitButtonData( $aPostElements, $sTargetKey='field_id' ) {	

			/* The structure of the $aPostElements array looks like this:
				[submit_buttons_submit_button_field_0] => Array
					(
						[input_id] => submit_buttons_submit_button_field_0
						[field_id] => submit_button_field
						[name] => APF_Demo|submit_buttons|submit_button_field
						[section_id] => submit_buttons
					)

				[submit_buttons_submit_button_link_0] => Array
					(
						[input_id] => submit_buttons_submit_button_link_0
						[field_id] => submit_button_link
						[name] => APF_Demo|submit_buttons|submit_button_link|0
						[section_id] => submit_buttons
					)
			 * The keys are the input id.
			 */
			foreach( $aPostElements as $sInputID => $aSubElements ) {
				
				$aNameKeys = explode( '|', $aSubElements[ 'name' ] );		// the 'name' key must be set.
				
				// The count of 4 means it's a single element. Count of 5 means it's one of multiple elements.
				// The isset() checks if the associated button is actually pressed or not.
				if ( count( $aNameKeys ) == 2 && isset( $_POST[ $aNameKeys[0] ][ $aNameKeys[1] ] ) )
					return isset( $aSubElements[ $sTargetKey ] ) ? $aSubElements[ $sTargetKey ] :null;
				if ( count( $aNameKeys ) == 3 && isset( $_POST[ $aNameKeys[0] ][ $aNameKeys[1] ][ $aNameKeys[2] ] ) )
					return isset( $aSubElements[ $sTargetKey ] ) ? $aSubElements[ $sTargetKey ] :null;
				if ( count( $aNameKeys ) == 4 && isset( $_POST[ $aNameKeys[0] ][ $aNameKeys[1] ][ $aNameKeys[2] ][ $aNameKeys[3] ] ) )
					return isset( $aSubElements[ $sTargetKey ] ) ? $aSubElements[ $sTargetKey ] :null;
					
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
			$sSectionID = $oImport->getSiblingValue( 'section_id' );
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
				array( 
					"import_mime_types_{$this->oProp->sClassName}_{$sPressedInputID}", 
					$sSectionID ? "import_mime_types_{$this->oProp->sClassName}_{$sSectionID}_{$sPressedFieldID}" : "import_mime_types_{$this->oProp->sClassName}_{$sPressedFieldID}", 
					$sSectionID ? "import_mime_types_{$this->oProp->sClassName}_{$sSectionID}" : null, 
					$sTabSlug ? "import_mime_types_{$sPageSlug}_{$sTabSlug}" : null, 
					"import_mime_types_{$sPageSlug}", 
					"import_mime_types_{$this->oProp->sClassName}" ),
				array( 'text/plain', 'application/octet-stream' ),        // .json file is dealt as a binary file.
				$sPressedFieldID,
				$sPressedInputID
			);                

			// Check the uploaded file MIME type.
			$_sType = $oImport->getType();
			if ( ! in_array( $oImport->getType(), $aMIMEType ) ) {        
				$this->setSettingNotice( sprintf( $this->oMsg->__( 'uploaded_file_type_not_supported' ), $_sType ) );
				return $aStoredOptions;        // do not change the framework's options.
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
				array( 
					"import_format_{$this->oProp->sClassName}_{$sPressedInputID}",
					$sSectionID ? "import_format_{$this->oProp->sClassName}_{$sSectionID}_{$sPressedFieldID}" : "import_format_{$this->oProp->sClassName}_{$sPressedFieldID}",
					$sSectionID ? "import_format_{$this->oProp->sClassName}_{$sSectionID}" : null,
					$sTabSlug ? "import_format_{$sPageSlug}_{$sTabSlug}" : null,
					"import_format_{$sPageSlug}",
					"import_format_{$this->oProp->sClassName}"
				),
				$oImport->getFormatType(),	// the set format type, array, json, or text.
				$sPressedFieldID,
				$sPressedInputID
			);	

			// Format it.
			$oImport->formatImportData( $vData, $sFormatType );	// it is passed as reference.	
			
			// Apply filters to the importing option key.
			$sImportOptionKey = $this->oUtil->addAndApplyFilters(
				$this,
				array(
					"import_option_key_{$this->oProp->sClassName}_{$sPressedInputID}",
					$sSectionID ? "import_option_key_{$this->oProp->sClassName}_{$sSectionID}_{$sPressedFieldID}" : "import_option_key_{$this->oProp->sClassName}_{$sPressedFieldID}",
					$sSectionID ? "import_option_key_{$this->oProp->sClassName}_{$sSectionID}" : null,
					$sTabSlug ? "import_option_key_{$sPageSlug}_{$sTabSlug}" : null,
					"import_option_key_{$sPageSlug}",
					"import_option_key_{$this->oProp->sClassName}"
				),
				$oImport->getSiblingValue( 'option_key' ),	
				$sPressedFieldID,
				$sPressedInputID
			);
			
			// Apply filters to the importing data.
			$vData = $this->oUtil->addAndApplyFilters(
				$this,
				array(
					"import_{$this->oProp->sClassName}_{$sPressedInputID}",
					$sSectionID ? "import_{$this->oProp->sClassName}_{$sSectionID}_{$sPressedFieldID}" : "import_{$this->oProp->sClassName}_{$sPressedFieldID}",
					$sSectionID ? "import_{$this->oProp->sClassName}_{$sSectionID}" : null,
					$sTabSlug ? "import_{$sPageSlug}_{$sTabSlug}" : null,
					"import_{$sPageSlug}",
					"import_{$this->oProp->sClassName}"
				),
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
			$sSectionID = $oExport->getSiblingValue( 'section_id' );
			$sPressedFieldID = $oExport->getSiblingValue( 'field_id' );
			$sPressedInputID = $oExport->getSiblingValue( 'input_id' );
			
			// If the data is set in transient,
			$vData = $oExport->getTransientIfSet( $vData );

			// Add and apply filters. - adding filters must be done in this class because the callback method belongs to this class 
			// and the magic method should be triggered.			
			$vData = $this->oUtil->addAndApplyFilters(
				$this,
				array( 
					"export_{$this->oProp->sClassName}_{$sPressedInputID}", 
					$sSectionID ? "export_{$this->oProp->sClassName}_{$sSectionID}_{$sPressedFieldID}" : "export_{$this->oProp->sClassName}_{$sPressedFieldID}", 	
					$sTabSlug ? "export_{$sPageSlug}_{$sTabSlug}" : null, 	// null will be skipped in the method
					"export_{$sPageSlug}", 
					"export_{$this->oProp->sClassName}" 
				),
				$vData,
				$sPressedFieldID,
				$sPressedInputID
			);	
			
			$sFileName = $this->oUtil->addAndApplyFilters(
				$this,
				array( 
					"export_name_{$this->oProp->sClassName}_{$sPressedInputID}",
					"export_name_{$this->oProp->sClassName}_{$sPressedFieldID}",
					$sSectionID ? "export_name_{$this->oProp->sClassName}_{$sSectionID}_{$sPressedFieldID}" : "export_name_{$this->oProp->sClassName}_{$sPressedFieldID}",
					$sTabSlug ? "export_name_{$sPageSlug}_{$sTabSlug}" : null,
					"export_name_{$sPageSlug}",
					"export_name_{$this->oProp->sClassName}" 
				),
				$oExport->getFileName(),
				$sPressedFieldID,
				$sPressedInputID
			);	
			
			$sFormatType = $this->oUtil->addAndApplyFilters(
				$this,
				array( 
					"export_format_{$this->oProp->sClassName}_{$sPressedInputID}",
					"export_format_{$this->oProp->sClassName}_{$sPressedFieldID}",
					$sSectionID ? "export_format_{$this->oProp->sClassName}_{$sSectionID}_{$sPressedFieldID}" : "export_format_{$this->oProp->sClassName}_{$sPressedFieldID}",
					$sTabSlug ? "export_format_{$sPageSlug}_{$sTabSlug}" : null,
					"export_format_{$sPageSlug}",
					"export_format_{$this->oProp->sClassName}" 
				),
				$oExport->getFormat(),
				$sPressedFieldID,
				$sPressedInputID
			);	
			$oExport->doExport( $vData, $sFileName, $sFormatType );
			exit;
			
		}
	
		/**
		 * Applies validation filters to the submitted input data.
		 * 
		 * @since			2.0.0
		 * @since			2.1.5			Added the $sPressedFieldID and $sPressedInputID parameters.
		 * @since			3.0.0			Removed the $sPressedFieldID and $sPressedInputID parameters.
		 * @return			array			The filtered input array.
		 */
		private function _getFilteredOptions( $aInput, $sPageSlug, $sTabSlug ) {

			$aInput = is_array( $aInput ) ? $aInput : array();
			$_aDefaultOptions = $this->oProp->getDefaultOptions( $this->oForm->aFields );
			$_aOptions = $this->oUtil->uniteArrays( $this->oProp->aOptions, $_aDefaultOptions );
			$_aInput = $aInput;	// copy one for parsing
			$aInput = $this->oUtil->uniteArrays( $aInput, $this->oUtil->castArrayContents( $aInput, $_aDefaultOptions ) );
			
			// For each submitted element
			foreach( $_aInput as $sID => $aSectionOrFields ) {	// $sID is either a section id or a field id
				
				if ( $this->oForm->isSection( $sID ) ) 
					foreach( $aSectionOrFields as $sFieldID => $aFields )	// For fields
						$aInput[ $sID ][ $sFieldID ] = $this->oUtil->addAndApplyFilter( 
							$this, 
							"validation_{$this->oProp->sClassName}_{$sID}_{$sFieldID}", 
							$aInput[ $sID ][ $sFieldID ], 
							isset( $_aOptions[ $sID ][ $sFieldID ] ) ? $_aOptions[ $sID ][ $sFieldID ] : null 
						);
										
				$aInput[ $sID ] = $this->oUtil->addAndApplyFilter( $this, "validation_{$this->oProp->sClassName}_{$sID}", $aInput[ $sID ], isset( $_aOptions[ $sID ] ) ? $_aOptions[ $sID ] : null );
				
			}
							
			// for tabs
			if ( $sTabSlug && $sPageSlug )	{	
				$aInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$sPageSlug}_{$sTabSlug}", $aInput, $this->_getTabOptions( $_aOptions, $sPageSlug, $sTabSlug ) );	// $aInput: new values, $aStoredPageOptions: old values
				$aInput = $this->oUtil->uniteArrays( $aInput, $this->_getOtherTabOptions( $_aOptions, $sPageSlug, $sTabSlug ) );
			}
			
			// for pages	
			if ( $sPageSlug )	{
				
				$aInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$sPageSlug}", $aInput, $this->_getPageOptions( $_aOptions, $sPageSlug ) ); // $aInput: new values, $aStoredPageOptions: old values			
				
				// Respect page meta box field values.
				$aInput = $this->oUtil->uniteArrays( $aInput, $_aOptions );	// $aInput = $this->oUtil->uniteArrays( $aInput, $this->_getOtherPageOptions( $_aOptions, $sPageSlug ) );
				
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
				foreach( $this->oForm->aFields as $_sSectionID => $_aFields  ) {
					
					foreach( $_aFields as $_sFieldID => $_aField ) {
						
						if ( ! isset( $_aField['page_slug'], $_aField['tab_slug'] ) ) continue;
						if ( $_aField['page_slug'] != $sPageSlug ) continue;
						if ( $_aField['tab_slug'] != $sTabSlug ) continue;
						if ( is_numeric( $_sFieldID ) && is_int( $_sFieldID + 0 ) ) continue;	// it's a sub-section array.
						
						// if a section is set,
						if ( isset( $_aField['section_id'] ) && $_aField['section_id'] != '_default' ) {
							if ( array_key_exists( $_aField['section_id'], $aOptions ) )
								$_aStoredOptionsOfTheTab[ $_aField['section_id'] ] = $aOptions[ $_aField['section_id'] ];
							continue;
						}
						
						// It does not have a section so set the field id as its key.
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
				foreach( $this->oForm->aFields as $_sSectionID => $_aFields ) {
					
					foreach( $_aFields as $_sFieldID => $_aField ) {
					
						if ( ! isset( $_aField['page_slug'] ) || $_aField['page_slug'] != $sPageSlug ) continue;
						if ( is_numeric( $_sFieldID ) && is_int( $_sFieldID + 0 ) ) continue;	// it's a sub-section array.
						
						// If a section is set,
						if ( isset( $_aField['section_id'] ) && $_aField['section_id'] != '_default' ) {
							if ( array_key_exists( $_aField['section_id'], $aOptions ) )
								$_aStoredOptionsOfThePage[ $_aField['section_id'] ] = $aOptions[ $_aField['section_id'] ];
							continue;
						}
						
						// It does not have a section so set the field id as its key.
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
				foreach( $this->oForm->aFields as $_sSectionID => $_aFields ) {
					
					foreach( $_aFields as $_sFieldID => $_aField ) {
						
						if ( ! isset( $_aField['page_slug'], $_aField['tab_slug'] ) ) continue;
						if ( $_aField['page_slug'] != $sPageSlug ) continue;
						if ( $_aField['tab_slug'] == $sTabSlug ) continue;
						if ( is_numeric( $_sFieldID ) && is_int( $_sFieldID + 0 ) ) continue;	// it's a sub-section array.
						
						// If a section is set,
						if ( isset( $_aField['section_id'] ) && $_aField['section_id'] != '_default' ) {
							if ( array_key_exists( $_aField['section_id'], $aOptions ) )
								$_aStoredOptionsNotOfTheTab[ $_aField['section_id'] ] = $aOptions[ $_aField['section_id'] ];
							continue;
						}
						// It does not have a section
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
				foreach( $this->oForm->aFields as $_sSectionID => $_aFields ) {
					
					foreach( $_aFields as $_sFieldID => $_aField ) {
						
						if ( ! isset( $_aField['page_slug'] ) ) continue;
						if ( $_aField['page_slug'] == $sPageSlug ) continue;
						if ( is_numeric( $_sFieldID ) && is_int( $_sFieldID + 0 ) ) continue;	// it's a sub-section array.
						
						// If a section is set,
						if ( isset( $_aField['section_id'] ) && $_aField['section_id'] != '_default' ) {
							if ( array_key_exists( $_aField['section_id'], $aOptions ) )
								$_aStoredOptionsNotOfThePage[ $_aField['section_id'] ] = $aOptions[ $_aField['section_id'] ];
							continue;
						}
						// It does not have a section
						if ( array_key_exists( $_aField['field_id'], $aOptions ) )
							$_aStoredOptionsNotOfThePage[ $_aField['field_id'] ] = $aOptions[ $_aField['field_id'] ];
							
					}
				
				}						
				return $_aStoredOptionsNotOfThePage;
				
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
		 * Note that we use local variables for the applying(registering) items. This allows the framework to refer to the added sections and fields for later use by keeping them intact.
		 * */
		$this->_formatSectionArrays( $this->oForm->aSections );	// passed by reference.
		$this->_formatFieldArrays( $this->oForm->aFields, $this->oForm->aSections );
		$_aSections = $this->_applyConditionsForSections( $this->oForm->aSections );
		$_aFields = $this->_applyConditionsForFields( $this->oForm->aFields, $_aSections );
		
// $this->_composeFormArray( $_aSections, $_aFields );	// deprecated

		/* 2. If there is no section or field to add, do nothing. */
		if (  $GLOBALS['pagenow'] != 'options.php' && ( count( $_aFields ) == 0 ) ) return;

		/* 3. Define field types. This class adds filters for the field type definitions so that framework's built-in field types will be added. */
		new AdminPageFramework_FieldTypeRegistration( $this->oProp->aFieldTypeDefinitions, $this->oProp->sClassName, $this->oMsg );
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
		foreach( $_aFields as $_sSectionID => $__aFields ) {
			
			uasort(  $__aFields, array( $this, '_sortByOrder' ) ); // Todo: check if it affects the sub-section keys
			foreach( $__aFields as $_sFieldID => $_aSubSectionOrField ) {
				
				// If the iterating item is a sub-section array.
				if ( is_numeric( $_sFieldID ) && is_int( $_sFieldID + 0 ) ) {
					
					$_iIndex = $_sFieldID;
					$_aSubSection = $_aSubSectionOrField;
					foreach( $_aSubSection as $__sFieldID => $__aField ) {					
						add_settings_field(
							$__aField['section_id'] . '_' . $_iIndex . '_' . $__aField['field_id'],	// id
							"<a id='{$__aField['section_id']}_{$_iIndex}_{$__aField['field_id']}'></a><span title='{$__aField['tip']}'>{$__aField['title']}</span>",
							null,	// callback function - no longer used by the framework
							$this->oForm->getPageSlugBySectionID( $__aField['section_id'] ), // page slug
							$__aField['section_id']	// section
						);							
					}
					continue;
					
				}
					
				/* 5-1. Add the given field. */
				$aField = $_aSubSectionOrField;
				add_settings_field(
					$aField['section_id'] . '_' . $aField['field_id'],	// id
					"<a id='{$aField['section_id']}_{$aField['field_id']}'></a><span title='{$aField['tip']}'>{$aField['title']}</span>",
					null,	// callback function - no longer used by the framewok
					$this->oForm->getPageSlugBySectionID( $aField['section_id'] ), // page slug
					$aField['section_id']	// section
				);	
				
				/* 5-2. Set relevant scripts and styles for the input field. */
				AdminPageFramework_FieldTypeRegistration::_setFieldHeadTagElements( $aField, $this->oProp, $this->oHeadTag );	// Set relevant scripts and styles for the input field.
				
				/* 5-3. For the contextual help pane, */
				if ( ! empty( $aField['help'] ) ) {
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
				
			}
			
		}
		
		/* 6. Register the settings. */
		$this->oProp->bEnableForm = true;	// Set the form enabling flag so that the <form></form> tag will be inserted in the page.
		register_setting(	
			$this->oProp->sOptionKey,	// the option group name.	
			$this->oProp->sOptionKey,	// the option key name that will be stored in the option table in the database.
			array( $this, 'validation_pre_' . $this->oProp->sClassName )	// the validation callback method
		); 
		
	}
	
		/**
		 * Formats the given section arrays.
		 * 
		 * @since			2.0.0
		 */ 
		private function _formatSectionArrays( &$aSections ) {

			// Set the default section.
			$aSections = $this->oUtil->uniteArrays( 
				$aSections, 
				array( 
					'_default' =>	array( 				
						'page_slug'	=>  $this->oProp->sDefaultPageSlug,		
						'capability' => $this->oProp->sCapability,
						'section_id'	=>	'_default',
					),
				)
			);
		
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
			
				$aSection = $this->oUtil->uniteArrays(
					$aSection,
					array( 'capability' => $this->oProp->sCapability ),
					AdminPageFramework_FormElement::$_aStructure_Section	
				);	// avoid undefined index warnings.
				
				// Check the mandatory keys' values.
				$aSection['page_slug'] = isset( $aSection['page_slug'] ) ? $aSection['page_slug'] : ( isset( $_GET['page'] ) ? $_GET['page'] : null );
				if ( ! isset( $aSection['page_slug'] ) ) continue;	// these keys are necessary.
				
				// Sanitize the IDs since they are used as a callback method name, the slugs as well.
				$aSection['section_id'] = $this->oUtil->sanitizeSlug( $aSection['section_id'] );
				$aSection['page_slug'] = $this->oUtil->sanitizeSlug( $aSection['page_slug'] );
				$aSection['tab_slug'] = $this->oUtil->sanitizeSlug( $aSection['tab_slug'] );
			
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
				if ( ! $this->_isSectionOfCurrentTab( $_aSection ) )  continue;
				
				// If the access level is set and it is not sufficient, skip.
				if ( ! current_user_can( $_aSection['capability'] ) ) continue;	// since 1.0.2.1
			
				// If a custom condition is set and it's not true, skip,
				if ( ! $_aSection['if'] ) continue;		
				
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
			private function _isSectionOfCurrentTab( $aSection ) {

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
		private function _formatFieldArrays( &$aFields, $aSections ) {
			
			// Apply filters to let other scripts to add fields.
			foreach( $aFields as $_sSectionID => &$_aFields ) {
				$_aFields = $this->oUtil->addAndApplyFilter(	// Parameters: $oCallerObject, $aFilters, $vInput, $vArgs...
					$this,
					"fields_{$this->oProp->sClassName}_{$_sSectionID}",
					$_aFields
				); 
				unset( $_aFields );	// to be safe in PHP especially the same variable name is used in the scope.
			}
			$aFields = $this->oUtil->addAndApplyFilter(	// Parameters: $oCallerObject, $aFilters, $vInput, $vArgs...
				$this,
				"fields_{$this->oProp->sClassName}",
				$aFields
			); 
// TODO:
// - retrieve the saved option array and inject sub-sections into the formatting section array.

			// Apply the conditions to remove unnecessary elements and put new orders.
			$_aNewFieldArrays = array();
			foreach( $aFields as $_sSectionID => $_aFields ) {
					
				$_aNewFieldArrays[ $_sSectionID ] = isset( $_aNewFieldArrays[ $_sSectionID ] ) ? $_aNewFieldArrays[ $_sSectionID ] : array();
				foreach( $_aFields as $_aField ) {
				
					if ( ! is_array( $_aField ) ) continue;		// the element must be an array.
					
					$_aField = $this->oUtil->uniteArrays(
						array( '_fields_type' => $this->oProp->sFieldsType ),
						$_aField,
						array( 'capability' => $this->oProp->sCapability ),
						AdminPageFramework_FormElement::$_aStructure_Field	// avoid undefined index warnings.
					);

					// Sanitize the IDs since they are used as a callback method name.
					$_aField['field_id'] = $this->oUtil->sanitizeSlug( $_aField['field_id'] );
					$_aField['section_id'] = $this->oUtil->sanitizeSlug( $_aField['section_id'] );
							
					// Check the mandatory keys' values.
					if ( ! isset( $_aField['field_id'], $_aField['type'] ) ) continue;	// these keys are necessary.
					
					// Set the order.
					$_aField['order']	= is_numeric( $_aField['order'] ) ? $_aField['order'] : count( $_aNewFieldArrays[ $_sSectionID ] ) + 10;
					
					// Set the tip, option key, instantiated class name, and page slug elements.
					$_aField['tip'] = strip_tags( isset( $_aField['tip'] ) ? $_aField['tip'] : $_aField['description'] );
					$_aField['option_key'] = $this->oProp->sOptionKey;
					$_aField['class_name'] = $this->oProp->sClassName;
					$_aField['page_slug'] = isset( $aSections[ $_aField['section_id'] ]['page_slug'] ) ? $aSections[ $_aField['section_id'] ]['page_slug'] : null;
					$_aField['tab_slug'] = isset( $aSections[ $_aField['section_id'] ]['tab_slug'] ) ? $aSections[ $_aField['section_id'] ]['tab_slug'] : null;
					$_aField['section_title'] = isset( $aSections[ $_aField['section_id'] ]['title'] ) ? $aSections[ $_aField['section_id'] ]['title'] : null;	// used for the contextual help pane.
					
					// Add the element to the new returning array.
					$_aNewFieldArrays[ $_sSectionID ][ $_aField['field_id'] ] = $_aField;
				
				}
				
			}
			
			$aFields = $_aNewFieldArrays;

		}
		/**
		 * Applies conditions to the given fields.
		 * 
		 * @remark			This must be done after performing the _formatFieldArrays() method and the _applyConditionsForSections() method.
		 * @since	
		 * @param			array				The formatted field definition arrays. 
		 * @param			array				The formatted section definition arrays. The arrays should be already filtered with conditions. In other words, the sections the sections will be displayed in the loading page.
		 */
		private function _applyConditionsForFields( $aFields, $aSections ) {

			$_aNewFieldArrays = array();
			foreach( $aFields as $_sSectionID => $_aFields ) {
				
				// Check if the parsing section id exists in the given section array.
				if ( ! array_key_exists( $_sSectionID, $aSections ) ) continue;
				
				foreach( $_aFields as $_aField ) {					
									
					// If the access level is not sufficient, skip.
					if ( ! current_user_can( $_aField['capability'] ) ) continue; 
								
					// If the condition is not met, skip.
					if ( ! $_aField['if'] ) continue;		
									
					// Add the element to the new returning array.
					$_aNewFieldArrays[ $_sSectionID ][ $_aField['field_id'] ] = $_aField;
					
				}
					
			}
			return $_aNewFieldArrays;
			
		}
	
	/**
	 * Returns the output of the filtered section description.
	 * 
	 * @remark			An alternative to _renderSectionDescription().
	 * @since			3.0.0
	 * @internal
	 */
	public function _replyToGetSectionOutput( $sSectionID ) {

		$_sCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;	
		if ( ! isset( $this->oForm->aSections[ $sSectionID ] ) ) return '';	// if it is not added
		if ( ! $this->oForm->isPageAdded( $_sCurrentPageSlug ) ) return '';
		
		return $this->oUtil->addAndApplyFilters(
			$this,
			array( 'section_head_' . $this->oProp->sClassName . '_' . $sSectionID ),	// section_{instantiated class name}_{section id}
			$this->oForm->getSectionHeader( $sSectionID )
		);				
		
	}
	
	/**
	 * Returns the output of the given field.
	 * 
	 * @since			3.0.0
	 * @internal
	 */	 
	public function _replyToGetFieldOutput( $aField ) {
		
		$_sCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;	
		$_sSectionID = isset( $aField['section_id'] ) ? $aField['section_id'] : '_default';
		$_sFieldID = $aField['field_id'];
		
		// If the specified field does not exist, do nothing.
		if ( $aField['page_slug'] != $_sCurrentPageSlug ) return '';

		// Retrieve the field error array.
		$this->aFieldErrors = isset( $this->aFieldErrors ) ? $this->aFieldErrors : $this->_getFieldErrors( $_sCurrentPageSlug ); 

		// Render the form field. 		
		$sFieldType = isset( $this->oProp->aFieldTypeDefinitions[ $aField['type'] ]['hfRenderField'] ) && is_callable( $this->oProp->aFieldTypeDefinitions[ $aField['type'] ]['hfRenderField'] )
			? $aField['type']
			: 'default';	// the predefined reserved field type is applied if the parsing field type is not defined(not found).

// TODO: Change the class name to AdminPageFramework_FieldOutput as the class mainly deals with the field outputs.
		$oField = new AdminPageFramework_InputField( $aField, $this->oProp->aOptions, $this->aFieldErrors, $this->oProp->aFieldTypeDefinitions, $this->oMsg );
// TODO: Change the method name to _getFieldOutput() as the Input does not imply neither the input tag nor the user input.
		$sFieldOutput = $oField->_getInputFieldOutput();	// field output
		unset( $oField );	// release the object for PHP 5.2.x or below.

		return $this->oUtil->addAndApplyFilters(
			$this,
			array( 
				isset( $aField['section_id'] ) && $aField['section_id'] != '_default' 
					? 'field_' . $this->oProp->sClassName . '_' . $aField['section_id'] . '_' . $_sFieldID
					: 'field_' . $this->oProp->sClassName . '_' . $_sFieldID,
			),
			$sFieldOutput,
			$aField // the field array
		);		
		
	}
}
endif;