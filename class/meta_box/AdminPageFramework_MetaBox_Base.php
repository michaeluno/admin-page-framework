<?php
if ( ! class_exists( 'AdminPageFramework_MetaBox' ) ) :
/**
 * @abstract
 * @since			2.0.0
 * @use				AdminPageFramework_Utility
 * @use				AdminPageFramework_Message
 * @use				AdminPageFramework_Debug
 * @use				AdminPageFramework_Property_MetaBox
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Meta Box
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
		
		$this->oProp = isset( $this->oProp )
			? $this->oProp
			: new AdminPageFramework_Property_MetaBox( $this, get_class( $this ), $sCapability );
			
		// Properties
		$this->oProp->sMetaBoxID = $this->oUtil->sanitizeSlug( $sMetaBoxID );
		$this->oProp->sTitle = $sTitle;
		$this->oProp->sContext = $sContext;	//  'normal', 'advanced', or 'side' 
		$this->oProp->sPriority = $sPriority;	// 	'high', 'core', 'default' or 'low'
		
		
		if ( $this->oProp->bIsAdmin ) {
			
			add_action( 'wp_loaded', array( $this, '_replyToLoadDefaultFieldTypeDefinitions' ), 10 );	// should be loaded before the setUp() method.
			add_action( 'wp_loaded', array( $this, 'setUp' ), 11 );
			
			add_action( 'add_meta_boxes', array( $this, '_replyToAddMetaBox' ) );
			add_action( 'save_post', array( $this, '_replyToSaveMetaBoxFields' ) );
							
			// if ( in_array( $GLOBALS['pagenow'], array( 'media-upload.php', 'async-upload.php', ) ) ) 
				// add_filter( 'gettext', array( $this, '_replyToReplaceThickBoxText' ) , 1, 2 );		
	
		}
		
		// Hooks
		$this->oUtil->addAndDoAction( $this, "start_{$this->oProp->sClassName}" );
		
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
	 * @remark			The user may use this method to add contextual help text.
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
	public function addSettingField( array $aField ) {}
			
	/*
	 * Internal methods that should be extended.
	 */
	public function _replyToAddMetaBox() {}
	
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
		$sOut = wp_nonce_field( $this->oProp->sMetaBoxID, $this->oProp->sMetaBoxID, true, false );
		
		// Begin the field table and loop
		$sOut .= '<table class="form-table">';
		$iPostID = isset( $oPost->ID ) ? $oPost->ID : ( isset( $_GET['page'] ) ? $_GET['page'] : null );
		$this->setOptionArray( $iPostID, $vArgs['args'] );

		foreach ( ( array ) $vArgs['args'] as $aField ) {
			
			// Avoid undefined index warnings
			$aField = $aField + AdminPageFramework_Property_MetaBox::$_aStructure_Field;
			
			// Check capability. If the access level is not sufficient, skip.
			$aField['capability'] = isset( $aField['capability'] ) ? $aField['capability'] : $this->oProp->sCapability;
			if ( ! current_user_can( $aField['capability'] ) ) continue; 			
		
			// Begin a table row. 
			$sOut .= "<tr>";
			if ( $aField['show_title_column'] )
				$sOut .= 
					"<th>"
						."<label for='{$aField['field_id']}'>"
							. "<a id='{$aField['field_id']}'></a>"
								. "<span title='" . strip_tags( isset( $aField['tip'] ) ? $aField['tip'] : $aField['description'] ) . "'>"
									. $aField['title'] 
								. "</span>"
						. "</label>"
					. "</th>";		
			$sOut .= "<td>";
			$sOut .= $this->getFieldOutput( $aField );
			$sOut .= "</td>";
			$sOut .= "</tr>";
			
		} // end foreach
		$sOut .= '</table>'; // end table
		
		/* Filter the output */
		$sOut = $this->oUtil->addAndApplyFilters( $this, 'content_' . $this->oProp->sMetaBoxID, $sOut );
		
		/* Do action */
		$this->oUtil->addAndDoActions( $this, 'do_' . $this->oProp->sMetaBoxID );
			
		echo $sOut;
		
	}
	
	/**
	 * 
	 * @since			unknown
	 * @since			3.0.0			the scope is changed to protected as the taxonomy field class redefines it.
	 */
	protected function setOptionArray( $isPostIDOrPageSlug, $aFields ) {
		
		if ( ! is_array( $aFields ) ) return;
		
		if ( is_numeric( $isPostIDOrPageSlug ) ) :
			
			$iPostID = $isPostIDOrPageSlug;
			foreach( $aFields as $iIndex => $aField ) {
				
				// Avoid undefined index warnings
				$aField = $aField + AdminPageFramework_Property_MetaBox::$_aStructure_Field;

				$this->oProp->aOptions[ $iIndex ] = get_post_meta( $iPostID, $aField['field_id'], true );
				
			}
			
		endif;
		
		// For page meta boxes, do nothing as the class will retrieve the option array by itself.
		
	}	
	protected function getFieldOutput( $aField ) {

		// Set the input field name which becomes the option key of the custom meta field of the post.
		// $aField['name'] = isset( $aField['name'] ) ? $aField['name'] : $aField['field_id'];	// deprecated as the attributes key can support custom name 

		// Render the form field. 		
		$sFieldType = isset( $this->oProp->aFieldTypeDefinitions[ $aField['type'] ]['hfRenderField'] ) && is_callable( $this->oProp->aFieldTypeDefinitions[ $aField['type'] ]['hfRenderField'] )
			? $aField['type']
			: 'default';	// the predefined reserved field type is applied if the parsing field type is not defined(not found).

		$oField = new AdminPageFramework_InputField( $aField, $this->oProp->aOptions, array(), $this->oProp->aFieldTypeDefinitions, $this->oMsg );	// currently the error array is not supported for meta-boxes
		$oField->isMetaBox( true );
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
		if ( in_array( $_POST['post_type'], $this->oProp->aPostTypes )   
			&& ( ( ! current_user_can( $this->oProp->sCapability, $iPostID ) ) || ( ! current_user_can( $this->oProp->sCapability, $iPostID ) ) )
		) return;

		// Compose an array consisting of the submitted registered field values.
		$aInput = array();
		foreach( $this->oProp->aFields as $aField ) 
			$aInput[ $aField['field_id'] ] = isset( $_POST[ $aField['field_id'] ] ) ? $_POST[ $aField['field_id'] ] : null;
			
		// Prepare the old value array.
		$aOriginal = array();
		foreach ( $aInput as $sFieldID => $v )
			$aOriginal[ $sFieldID ] = get_post_meta( $iPostID, $sFieldID, true );
					
		// Apply filters to the array of the submitted values.
		$aInput = $this->oUtil->addAndApplyFilters( $this, "validation_{$this->oProp->sClassName}", $aInput, $aOriginal );

		// Loop through fields and save the data.
		foreach ( $aInput as $sFieldID => $vValue ) {
			
			// $sOldValue = get_post_meta( $iPostID, $sFieldID, true );			
			$sOldValue = isset( $aOriginal[ $sFieldID ] ) ? $aOriginal[ $sFieldID ] : null;
			if ( ! is_null( $vValue ) && $vValue != $sOldValue ) {
				update_post_meta( $iPostID, $sFieldID, $vValue );
				continue;
			} 
			// if ( '' == $sNewValue && $sOldValue ) 
				// delete_post_meta( $iPostID, $aField['field_id'], $sOldValue );
			
		} // end foreach
		
	}	
	
	/*
	 * Magic method
	*/
	function __call( $sMethodName, $aArgs=null ) {	

		// the start_ action hook.
		if ( $sMethodName == 'start_' . $this->oProp->sClassName ) return;

		// the field_{class name}_{...} filter.
		if ( substr( $sMethodName, 0, strlen( 'field_' . $this->oProp->sClassName . '_' ) ) == 'field_' . $this->oProp->sClassName . '_' ) return $aArgs[ 0 ];
		
		// the field_types_ + class name filter. [2.1.5+]
		if ( substr( $sMethodName, 0, strlen( "field_types_{$this->oProp->sClassName}" ) ) == "field_types_{$this->oProp->sClassName}" ) return $aArgs[ 0 ];		

		// the script_common + class name filter.	[3.0.0+]
		if ( substr( $sMethodName, 0, strlen( "script_common_{$this->oProp->sClassName}" ) ) == "script_{$this->oProp->sClassName}" ) return $aArgs[ 0 ];

		// the script_ + class name filter.
		if ( substr( $sMethodName, 0, strlen( "script_{$this->oProp->sClassName}" ) ) == "script_{$this->oProp->sClassName}" ) return $aArgs[ 0 ];

		// the style_ie_common_ + class name filter.	[3.0.0+]
		if ( substr( $sMethodName, 0, strlen( "style_ie_common_{$this->oProp->sClassName}" ) ) == "style_ie_common_{$this->oProp->sClassName}" ) return $aArgs[ 0 ];
			
		// the style_common + class name filter.	[3.0.0+]
		if ( substr( $sMethodName, 0, strlen( "style_common_{$this->oProp->sClassName}" ) ) == "style_common_{$this->oProp->sClassName}" ) return $aArgs[ 0 ];

		// the style_ie + class name filter.
		if ( substr( $sMethodName, 0, strlen( "style_ie_{$this->oProp->sClassName}" ) ) == "style_ie_{$this->oProp->sClassName}" ) return $aArgs[ 0 ];
					
		// the style_ + class name filter.
		if ( substr( $sMethodName, 0, strlen( "style_{$this->oProp->sClassName}" ) ) == "style_{$this->oProp->sClassName}" ) return $aArgs[ 0 ];
						
		// the validation_ + class name	filter.
		if ( substr( $sMethodName, 0, strlen( "validation_{$this->oProp->sClassName}" ) ) == "validation_{$this->oProp->sClassName}" ) return $aArgs[ 0 ];

		// the content_{metabox id} filter. [3.0.0+]
		if ( substr( $sMethodName, 0, strlen( "content_{$this->oProp->sMetaBoxID}" ) ) == "content_{$this->oProp->sMetaBoxID}" ) return $aArgs[ 0 ];
			
		// do_{meta box id} [3.0.0.+]
		if ( substr( $sMethodName, 0, strlen( "do_{$this->oProp->sMetaBoxID}" ) ) == "do_{$this->oProp->sMetaBoxID}" ) return;
		
	}
}
endif;