<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Factory_Controller' ) ) :
/**
 * Provides methods for models.
 * 
 * @abstract
 * @since			3.0.4
 * @subpackage		Factory
 * @internal
 */
abstract class AdminPageFramework_Factory_Controller extends AdminPageFramework_Factory_View {
			
	/*
	 * Should be extended
	 */
	public function setUp() {}
			
	/*
	 * Head Tag Methods - should be extended.
	 */
	public function enqueueStyles( $aSRCs, $_vArg2=null ) {}	// the number of arguments depend on the extended class
	public function enqueueStyle( $sSRC, $_vArg2=null ) {}
	public function enqueueScripts( $aSRCs, $_vArg2=null ) {}
	public function enqueueScript( $sSRC, $_vArg2=null ) {}	
	
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
	 * @remark			The target section tab slug will be reset once the method returns.
	 * @param			array|string			the section array or the target page slug. If the target page slug is set, the next section array can omit the page slug key.
	 * <strong>Section Array</strong>
	 * <ul>
	 * <li><strong>section_id</strong> - ( string ) the section ID. Avoid using non-alphabetic characters except underscore and numbers.</li>
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
		
		// reset the stored target tab slug and the target section tab slug
		$this->_sTargetSectionTabSlug = null;		
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
	* 	<li><strong>field_id</strong> - ( required, string ) the field ID. Avoid using non-alphabetic characters except underscore and numbers.</li>
	* 	<li><strong>type</strong> - ( required, string ) the type of the field. The supported types are listed below.</li>
	* 	<li><strong>section_id</strong> - ( optional, string ) the section ID that the field belongs to. If not set, the internal <em>_default</em> section ID will be assigned.</li>
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
	 * @since			3.0.4			
	 * @param			array			$aErrors			the field error array. The structure should follow the one contained in the submitted $_POST array.
	 * @param			string			$sID				deprecated
	 * @param			integer			$iLifeSpan			the transient's lifetime. 300 seconds means 5 minutes.
	 */ 
	public function setFieldErrors( $aErrors, $sID='', $iLifeSpan=300 ) {
		
		set_transient( md5( $this->oProp->sClassName ), $aErrors, $iLifeSpan );	// store it for 5 minutes ( 60 seconds * 5 )
	
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
	* @since			3.0.4			
	* @access 			public
	* @param			string			$sMessage			the text message to be displayed.
	* @param			string			$sType				( optional ) the type of the message, either "error" or "updated"  is used.
	* @param			array			$asAttributes		( optional ) the tag attribute array applied to the message container HTML element. If a string is given, it is used as the ID attribute value.
	* @param			boolean			$bOverride			( optional ) false: do not override when there is a message of the same id. true: override the previous one.
	* @return			void
	*/		
	public function setSettingNotice( $sMessage, $sType='error', $asAttributes=array(), $bOverride=true ) {
		
		// The framework user set notification messages will be stored in this global array element.
		$GLOBALS['aAdminPageFramework']['aNotices'] = ! isset( $GLOBALS['aAdminPageFramework']['aNotices'] ) ? $GLOBALS['aAdminPageFramework']['aNotices'] : array();
		
		// If the array is empty, save the array at shutdown.
		if ( empty( $GLOBALS['aAdminPageFramework']['aNotices'] ) ) {
			add_action( 'shutdown', array( $this, '_replyToSaveNotices' ) );	// the method is defined in the controller class.
		}
		
		// Set up local variables
		$_sID = md5( trim( $sMessage ) );
			
		// If the override options is true, or if the message is set,
		if ( $bOverride || ! isset( $GLOBALS['aAdminPageFramework']['aNotices'][ $_sID ] )  ) {
			
			$GLOBALS['aAdminPageFramework']['aNotices'][ $_sID ] = array(
				'sMessage' => $sMessage,
				'aAttributes' => ( is_array( $asAttributes ) ? $asAttributes : array( 'id' => $asAttributes )  )
					+ array(
						'class'	=>	$sType,
						'id'	=>	$this->oProp->sClassName . '_' . $_sID,
					),
					
			
			);
		}
							
	}
	

}
endif;