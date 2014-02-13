<?php
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
		
		if ( is_array( $aSection ) )
			$this->oForm->addSection( $aSection );
			
	}		
		
	/**
	* Adds the given field array items into the field array property. 
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
	* @param			array			$aField1			The field array.
	* @param			array			$aField2			Another field array.
	* @param			array			$_and_more			Add more fields arrays as many as necessary to the next parameters.
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
		$oFieldsTable = new AdminPageFramework_FormTable( $this->oMsg );
		$aOutput[] = $oFieldsTable->getFormTables( $this->oForm->aConditionedSections, $this->oForm->aConditionedFields, array( $this, '_replyToGetSectionOutput' ), array( $this, '_replyToGetFieldOutput' ) );

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
	public function _replyToGetSectionOutput( $sSectionID ) {
			
		return $this->oUtil->addAndApplyFilters(
			$this,
			array( 'section_head_' . $this->oProp->sClassName . '_' . $sSectionID ),	// section_ + {extended class name} + _ {section id}
			$this->oForm->getSectionHeader( $sSectionID )
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

		$oField = new AdminPageFramework_InputField( $aField, $this->oProp->aOptions, array(), $this->oProp->aFieldTypeDefinitions, $this->oMsg );	// currently the error array is not supported for meta-boxes
		$sFieldOutput = $oField->_getInputFieldOutput();	// field output
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
		$aInput = $this->getInputArray( $this->oForm->aFields );
	
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
		protected function getInputArray( array $aFieldDefinitionArrays ) {
			
			// Compose an array consisting of the submitted registered field values.
			$aInput = array();
			foreach( $aFieldDefinitionArrays as $_sSectionID => $_aFields ) {
				
				// If a section is not set,
				if ( $_sSectionID == '_default' ) {
					foreach( $_aFields as $_aField ) {
						$aInput[ $_aField['field_id'] ] = isset( $_POST[ $_aField['field_id'] ] ) 
							? $_POST[ $_aField['field_id'] ] 
							: null;
					}
					continue;
				}			
					
				// If a section is set,
				$aInput[ $_sSectionID ] = isset( $aInput[ $_sSectionID ] ) ? $aInput[ $_sSectionID ] : array();
				foreach( $_aFields as $_aField ) {
					$aInput[ $_sSectionID ][ $_aField['field_id'] ] = isset( $_POST[ $_sSectionID ][ $_aField['field_id'] ] )
						? $_POST[ $_sSectionID ][ $_aField['field_id'] ]
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