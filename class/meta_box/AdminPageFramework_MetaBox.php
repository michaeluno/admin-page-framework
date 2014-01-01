<?php
if ( ! class_exists( 'AdminPageFramework_MetaBox' ) ) :
/**
 * Provides methods for creating meta boxes.
 *
 * <h2>Hooks</h2>
 * <p>The class automatically creates WordPress action and filter hooks associated with the class methods.
 * The class methods corresponding to the name of the below actions and filters can be extended to modify the page output. Those methods are the callbacks of the filters and actions.</p>
 * <h3>Methods and Action Hooks</h3>
 * <ul>
 * 	<li><code>start_ + extended class name</code> – triggered at the end of the class constructor.</li>
 * </ul>
 * <h3>Methods and Filter Hooks</h3>
 * <ul>
 * 	<li><code>extended class name + _ + field_ + field ID</code> – receives the form input field output of the given input field ID. The first parameter: output string. The second parameter: the array of option.</li>
 * 	<li><code>style_ + extended class name</code> –  receives the output of the CSS rules applied to the pages of the associated post types with the meta box.</li>
 * 	<li><code>script_ + extended class name</code> – receives the output of the JavaScript scripts applied to the pages of the associated post types with the meta box.</li>
 * 	<li><code>validation_ + extended class name</code> – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 * </ul>
 * <h3>Remarks</h3>
 * <p>The slugs must not contain a dot(.) or a hyphen(-) since it is used in the callback method name.</p>  
 * 
 * @abstract
 * @since			2.0.0
 * @use				AdminPageFramework_Utility
 * @use				AdminPageFramework_Message
 * @use				AdminPageFramework_Debug
 * @use				AdminPageFramework_Property_Page
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Meta Box
 */
abstract class AdminPageFramework_MetaBox {
	
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
	 * @param			string|array	$vPostTypes				( optional ) The post type(s) that the meta box is associated with.
	 * @param			string			$sContext				( optional ) The part of the page where the edit screen section should be shown ('normal', 'advanced', or 'side') Default: normal.
	 * @param			string			$sPriority			( optional ) The priority within the context where the boxes should show ('high', 'core', 'default' or 'low') Default: default.
	 * @param			string			$sCapability			( optional ) The <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> to the meta box. Default: edit_posts.
	 * @param			string			$sTextDomain			( optional ) The text domain applied to the displayed text messages. Default: admin-page-framework.
	 * @return			void
	 */ 
	function __construct( $sMetaBoxID, $sTitle, $vPostTypes=array( 'post' ), $sContext='normal', $sPriority='default', $sCapability='edit_posts', $sTextDomain='admin-page-framework' ) {
		
		// Objects
		$this->oUtil = new AdminPageFramework_Utility;
		$this->oMsg = AdminPageFramework_Message::instantiate( $sTextDomain );
		$this->oDebug = new AdminPageFramework_Debug;
		$this->oProp = new AdminPageFramework_Property_MetaBox( $this, get_class( $this ), $sCapability );
		$this->oHeadTag = new AdminPageFramework_HeadTag_MetaBox( $this->oProp );
		$this->oHelpPane = new AdminPageFramework_HelpPane_MetaBox( $this->oProp );
			
		// Properties
		$this->oProp->sMetaBoxID = $this->oUtil->sanitizeSlug( $sMetaBoxID );
		$this->oProp->sTitle = $sTitle;
		$this->oProp->aPostTypes = is_string( $vPostTypes ) ? array( $vPostTypes ) : $vPostTypes;	
		$this->oProp->sContext = $sContext;	//  'normal', 'advanced', or 'side' 
		$this->oProp->sPriority = $sPriority;	// 	'high', 'core', 'default' or 'low'
				
		if ( is_admin() ) {
			
			add_action( 'wp_loaded', array( $this, 'replyToLoadDefaultFieldTypeDefinitions' ), 10 );	// should be loaded before the setUp() method.
			add_action( 'wp_loaded', array( $this, 'setUp' ), 11 );
			
			add_action( 'add_meta_boxes', array( $this, 'addMetaBox' ) );
			add_action( 'save_post', array( $this, 'saveMetaBoxFields' ) );
							
			if ( in_array( $GLOBALS['pagenow'], array( 'media-upload.php', 'async-upload.php', ) ) ) 
				add_filter( 'gettext', array( $this, 'replaceThickBoxText' ) , 1, 2 );		
	
		}
		
		// Hooks
		$this->oUtil->addAndDoAction( $this, "{$this->oProp->sPrefixStart}{$this->oProp->sClassName}" );
		
	}

	/**
	* The method for all necessary set-ups.
	* 
	* <h4>Example</h4>
	* <code>	public function setUp() {		
	* 	$this->addSettingFields(
	* 		array(
	* 			'field_id'		=> 'sample_metabox_text_field',
	* 			'title'			=> 'Text Input',
	* 			'description'	=> 'The description for the field.',
	* 			'type'			=> 'text',
	* 		),
	* 		array(
	* 			'field_id'		=> 'sample_metabox_textarea_field',
	* 			'title'			=> 'Textarea',
	* 			'description'	=> 'The description for the field.',
	* 			'type'			=> 'textarea',
	* 			'default'			=> 'This is a default text.',
	* 		)
	* 	);		
	* }</code>
	* 
	* @abstract
	* @since			2.0.0
	* @remark			The user may override this method.
	* @return			void
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
	 * Head Tag Methods
	 */
	/**
	 * Enqueues styles by page slug and tab slug.
	 * 
	 * @since			3.0.0
	 * @remark			The user may use this method.
	 */
	public function enqueueStyles( $aSRCs, $aPostTypes=array(), $aCustomArgs=array() ) {
		return $this->oHeadTag->_enqueueStyles( $aSRCs, $aPostTypes, $aCustomArgs );
	}
	/**
	 * Enqueues a style by page slug and tab slug.
	 * 	
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the stylesheet.</li>
	 * 	<li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
	 * 	<li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>media</strong> - ( optional, string ) the description of the field which is inserted into after the input field tag.</li>
	 * </ul>
	 * 
	 * @remark			The user may use this method.
	 * @since			3.0.0
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_style
	 * @param			string			$sSRC				The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
	 * @param			array			$aPostTypes		(optional) The post type slugs that the stylesheet should be added to. If not set, it applies to all the pages of the post types.
	 * @param 			array			$aCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */	
	public function enqueueStyle( $sSRC, $aPostTypes=array(), $aCustomArgs=array() ) {
		return $this->oHeadTag->_enqueueStyle( $sSRC, $aPostTypes, $aCustomArgs );		
	}
	/**
	 * Enqueues scripts by page slug and tab slug.
	 * 
	 * @since			3.0.0
	 */
	public function enqueueScripts( $aSRCs, $aPostTypes=array(), $aCustomArgs=array() ) {
		return $this->oHeadTag->_enqueueScripts( $aSRCs, $aPostTypes, $aCustomArgs );
	}	
	/**
	 * Enqueues a script by page slug and tab slug.
	 * 
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the script.</li>
	 * 	<li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
	 * 	<li><strong>version/strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>translation</strong> - ( optional, array ) The translation array. The handle ID will be used for the object name.</li>
	 * 	<li><strong>in_footer</strong> - ( optional, boolean ) Whether to enqueue the script before < / head > or before < / body > Default: <code>false</code>.</li>
	 * </ul>	 
	 * 
	 * <h4>Example</h4>
	 * <code>$this->enqueueScript(  
	 *		plugins_url( 'asset/js/test.js' , __FILE__ ),	// source url or path
	 *		array( 'my_post_type_slug' ),
	 *		array(
	 *			'handle_id' => 'my_script',	// this handle ID also is used as the object name for the translation array below.
	 *			'translation' => array( 
	 *				'a' => 'hello world!',
	 *				'style_handle_id' => $sStyleHandle,	// check the enqueued style handle ID here.
	 *			),
	 *		)
	 *	);</code>
	 * 
	 * @remark			The user may use this method.
	 * @since			2.1.2
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	 * @param			string			$sSRC				The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
	 * @param			string			$sPageSlug		(optional) The page slug that the script should be added to. If not set, it applies to all the pages created by the framework.
	 * @param			string			$sTabSlug			(optional) The tab slug that the script should be added to. If not set, it applies to all the in-page tabs in the page.
	 * @param 			array			$aCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */
	public function enqueueScript( $sSRC, $aPostTypes=array(), $aCustomArgs=array() ) {	
		return $this->oHeadTag->_enqueueScript( $sSRC, $aPostTypes, $aCustomArgs );
	}	
		
	/**
	 * Loads the default field type definition.
	 * 
	 * @since			2.1.5
	 */
	public function replyToLoadDefaultFieldTypeDefinitions() {
		
		// This class adds filters for the field type definitions so that framework's default field types will be added.
		new AdminPageFramework_RegisterBuiltinFieldTypes( $this->oProp->aFieldTypeDefinitions, $this->oProp->sClassName, $this->oMsg );		
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
	protected function addSettingFields( $aField1, $aField2=null, $_and_more=null ) {

		foreach( func_get_args() as $aField ) 
			$this->addSettingField( $aField );
		
	}	
	/**
	* Adds the given field array items into the field array property.
	* 
	* Itentical to the addSettingFields() method except that this method does not accept enumerated parameters. 
	* 
	* @since			2.1.2
	* @return			void
	* @remark			The user may use this method in their extended class definition.
	*/		
	protected function addSettingField( $aField ) {

		if ( ! is_array( $aField ) ) return;
		
		$aField = $aField + AdminPageFramework_Property_MetaBox::$_aStructure_Field;	// avoid undefined index warnings.
		
		// Sanitize the IDs since they are used as a callback method name.
		$aField['field_id'] = $this->oUtil->sanitizeSlug( $aField['field_id'] );
		
		// Check the mandatory keys' values are set.
		if ( ! isset( $aField['field_id'], $aField['type'] ) ) return;	// these keys are necessary.
						
		// If a custom condition is set and it's not true, skip.
		if ( ! $aField['if'] ) return;
							
		// Load head tag elements for fields.
		if ( 
			in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', ) ) 
			&& ( 
				( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], $this->oProp->aPostTypes ) )
				|| ( isset( $_GET['post'], $_GET['action'] ) && in_array( get_post_type( $_GET['post'] ), $this->oProp->aPostTypes ) )		// edit post page
			)
		) {
			// Set relevant scripts and styles for the input field.
			$this->_setFieldHeadTagElements( $aField );

		}
		
		// For the contextual help pane,
		if ( 
			in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', ) ) 
			&& ( 
				( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], $this->oProp->aPostTypes ) )
				|| ( isset( $_GET['post'], $_GET['action'] ) && in_array( get_post_type( $_GET['post'] ), $this->oProp->aPostTypes ) )		// edit post page
			)
			&& $aField['help']
		) 			
			$this->oHelpPane->_addHelpTextForFormFields( $aField['title'], $aField['help'], $aField['help_aside'] );
				
		$this->oProp->aFields[ $aField['field_id'] ] = $aField;
	
	}
		/**
		 * Sets the given field type's enqueuing scripts and styles.
		 * 
		 * A helper function for the above addSettingField() method.
		 * 
		 * @since			2.1.5
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

			$this->oHeadTag->_enqueueStyles( $this->oProp->aFieldTypeDefinitions[ $sFieldType ]['aEnqueueStyles'] );
			$this->oHeadTag->_enqueueScripts( $this->oProp->aFieldTypeDefinitions[ $sFieldType ]['aEnqueueScripts'] );
					
		}		

	/**
	 * 
	 * since			2.1.3
	 */
	public function removeMediaLibraryTab( $aTabs ) {
		
		if ( ! isset( $_REQUEST['enable_external_source'] ) ) return $aTabs;
		
		if ( ! $_REQUEST['enable_external_source'] )
			unset( $aTabs['type_url'] );	// removes the From URL tab in the thick box.
		
		return $aTabs;
		
	}

	/**
 	 * Replaces the label text of a button used in the media uploader.
	 * @since			2.0.0
	 * @remark			A callback for the <em>gettext</em> hook.
	 */ 
	public function replaceThickBoxText( $sTranslated, $sText ) {

		// Replace the button label in the media thick box.
		if ( ! in_array( $GLOBALS['pagenow'], array( 'media-upload.php', 'async-upload.php' ) ) ) return $sTranslated;
		if ( $sText != 'Insert into Post' ) return $sTranslated;
		if ( $this->oUtil->getQueryValueInURLByKey( wp_get_referer(), 'referrer' ) != 'admin_page_framework' ) return $sTranslated;
		
		if ( isset( $_GET['button_label'] ) ) return $_GET['button_label'];

		return $this->oProp->sThickBoxButtonUseThis ?  $this->oProp->sThickBoxButtonUseThis : $this->oMsg->__( 'use_this_image' );
		
	}
	
	/**
	 * Adds the defined meta box.
	 * 
	 * @since			2.0.0
	 * @remark			uses <em>add_meta_box()</em>.
	 * @remark			A callback for the <em>add_meta_boxes</em> hook.
	 * @return			void
	 */ 
	public function addMetaBox() {
		
		foreach( $this->oProp->aPostTypes as $sPostType ) 
			add_meta_box( 
				$this->oProp->sMetaBoxID, 		// id
				$this->oProp->sTitle, 	// title
				array( $this, 'echoMetaBoxContents' ), 	// callback
				$sPostType,		// post type
				$this->oProp->sContext, 	// context
				$this->oProp->sPriority,	// priority
				$this->oProp->aFields	// argument
			);
			
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
	public function echoMetaBoxContents( $oPost, $vArgs ) {	
		
		// Use nonce for verification
		$sOut = wp_nonce_field( $this->oProp->sMetaBoxID, $this->oProp->sMetaBoxID, true, false );
		
		// Begin the field table and loop
		$sOut .= '<table class="form-table">';
		$this->setOptionArray( $oPost->ID, $vArgs['args'] );
		
		foreach ( ( array ) $vArgs['args'] as $aField ) {
			
			// Avoid undefined index warnings
			$aField = $aField + AdminPageFramework_Property_MetaBox::$_aStructure_Field;
			
			// get value of this field if it exists for this post
			$sStoredValue = get_post_meta( $oPost->ID, $aField['field_id'], true );
			$aField['value'] = $sStoredValue ? $sStoredValue : $aField['value'];
			
			// Check capability. If the access level is not sufficient, skip.
			$aField['capability'] = isset( $aField['capability'] ) ? $aField['capability'] : $this->oProp->sCapability;
			if ( ! current_user_can( $aField['capability'] ) ) continue; 			
			
			// Begin a table row. 
			
			// If it's a hidden input type, do now draw a table row
			if ( $aField['type'] == 'hidden' ) {
				$sOut .= "<tr><td style='height: 0; padding: 0; margin: 0; line-height: 0;'>"
					. $this->getFieldOutput( $aField )
					. "</td></tr>";
				continue;
			}
			$sOut .= "<tr>";
			if ( ! $aField['show_in_page_tabTitleColumn'] )
				$sOut .= "<th><label for='{$aField['field_id']}'>"
						. "<a id='{$aField['field_id']}'></a>"
						. "<span title='" . strip_tags( isset( $aField['tip'] ) ? $aField['tip'] : $aField['description'] ) . "'>"
						. $aField['title'] 
						. "</span>"
						. "</label></th>";		
			$sOut .= "<td>";
			$sOut .= $this->getFieldOutput( $aField );
			$sOut .= "</td>";
			$sOut .= "</tr>";
			
		} // end foreach
		$sOut .= '</table>'; // end table
		echo $sOut;
		
	}
	private function setOptionArray( $iPostID, $aFields ) {
		
		if ( ! is_array( $aFields ) ) return;
		
		foreach( $aFields as $iIndex => $aField ) {
			
			// Avoid undefined index warnings
			$aField = $aField + AdminPageFramework_Property_MetaBox::$_aStructure_Field;

			$this->oProp->aOptions[ $iIndex ] = get_post_meta( $iPostID, $aField['field_id'], true );
			
		}
	}	
	private function getFieldOutput( $aField ) {

		// Set the input field name which becomes the option key of the custom meta field of the post.
		$aField['sName'] = isset( $aField['sName'] ) ? $aField['sName'] : $aField['field_id'];

		// Render the form field. 		
		$sFieldType = isset( $this->oProp->aFieldTypeDefinitions[ $aField['type'] ]['hfRenderField'] ) && is_callable( $this->oProp->aFieldTypeDefinitions[ $aField['type'] ]['hfRenderField'] )
			? $aField['type']
			: 'default';	// the predefined reserved field type is applied if the parsing field type is not defined(not found).
		$oField = new AdminPageFramework_InputField( $aField, $this->oProp->aOptions, array(), $this->oProp->aFieldTypeDefinitions[ $sFieldType ], $this->oMsg );	// currently the error array is not supported for meta-boxes
		$oField->isMetaBox( true );
		$sFieldOutput = $oField->_getInputFieldOutput();	// field output
		unset( $oField );	// release the object for PHP 5.2.x or below.
		
		return $this->oUtil->addAndApplyFilters(
			$this,
			array( 
				$this->oProp->sClassName . '_' . 'field_' . $aField['field_id'],	// this filter will be deprecated
				'field_' . $this->oProp->sClassName . '_' . $aField['field_id']	// field_ + {extended class name} + _ {field id}
			),
			$sFieldOutput,
			$aField // the field array
		);		
						
	}
		
	/**
	 * Saves the meta box field data to the associated post. 
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the <em>save_post</em> hook
	 */
	public function saveMetaBoxFields( $iPostID ) {
		
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
		if ( $sMethodName == $this->oProp->sPrefixStart . $this->oProp->sClassName ) return;

		// the class name + field_ field ID filter.
		if ( substr( $sMethodName, 0, strlen( 'field_' . $this->oProp->sClassName . '_' ) ) == 'field_' . $this->oProp->sClassName . '_' )
			return $aArgs[ 0 ];
		
		// the class name + field_ field ID filter.
		if ( substr( $sMethodName, 0, strlen( $this->oProp->sClassName . '_' . 'field_' ) ) == $this->oProp->sClassName . '_' . 'field_' )
			return $aArgs[ 0 ];

		// the field_types_ + class name filter. [2.1.5+]
		if ( substr( $sMethodName, 0, strlen( "field_types_{$this->oProp->sClassName}" ) ) == "field_types_{$this->oProp->sClassName}" )
			return $aArgs[ 0 ];		
			
		// the script_ + class name	filter.
		if ( substr( $sMethodName, 0, strlen( "script_{$this->oProp->sClassName}" ) ) == "script_{$this->oProp->sClassName}" )
			return $aArgs[ 0 ];		
	
		// the style_ + class name	filter.
		if ( substr( $sMethodName, 0, strlen( "style_{$this->oProp->sClassName}" ) ) == "style_{$this->oProp->sClassName}" )
			return $aArgs[ 0 ];		

		// the validation_ + class name	filter.
		if ( substr( $sMethodName, 0, strlen( "validation_{$this->oProp->sClassName}" ) ) == "validation_{$this->oProp->sClassName}" )
			return $aArgs[ 0 ];				
			
	}
}
endif;