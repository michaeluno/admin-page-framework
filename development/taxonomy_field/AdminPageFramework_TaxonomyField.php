<?php
if ( ! class_exists( 'AdminPageFramework_TaxonomyField' ) ) :
/**
 * Provides methods for creating fields in the taxonomy page (edit-tags.php).
 *
 * @abstract
 * @since			3.0.0
 * @use				AdminPageFramework_Utility
 * @use				AdminPageFramework_Message
 * @use				AdminPageFramework_Debug
 * @use				AdminPageFramework_Property_MetaBox
 * @package			AdminPageFramework
 * @subpackage		TaxonomyField
 * @extends			AdminPageFramework_MetaBox_Base
 */
abstract class AdminPageFramework_TaxonomyField extends AdminPageFramework_MetaBox_Base {
	
	/**
	 * Stores the property object.
	 * @since			3.0.0
	 */
	protected $oProp;
	
	/**
	 * Stores the head tag object.
	 * @since			3.0.0
	 * @internal
	 */
	protected $oHeadTag;
	
	/**
	 * Stores the contextual help pane object.
	 * @since			3.0.0
	 * @internal
	 */
	protected $oHelpPane;
	
	/**
	 * Constructs the class object instance of AdminPageFramework_TaxonomyField.
	 * 
	 * <h4>Examples</h4>
	 * <code>
	 * new APF_TaxonomyField( 'apf_sample_taxonomy' );		// taxonomy slug
	 * </code>
	 * 
	 * @since			3.0.0
	 * @param			array|string			The taxonomy slug(s). If multiple slugs need to be passed, enclose them in an array and pass the array.
	 * @param			string					The option key used for the options table to save the data. By default, the extended class name will be applied.
	 * @param			string					The access rights. Default: <em>manage_options</em>.
	 * @param			string					The text domain. Default: <em>admin-page-framework</em>.
	 * @return			void
	 */ 
	function __construct( $asTaxonomySlug, $sOptionKey='', $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {
		
		if ( empty( $asTaxonomySlug ) ) return;
		
		/* Objects */
		$this->oProp = new AdminPageFramework_Property_TaxonomyField( $this, get_class( $this ), $sCapability );
		$this->oUtil = new AdminPageFramework_WPUtility;
		$this->oMsg = AdminPageFramework_Message::instantiate( $sTextDomain );
		$this->oDebug = new AdminPageFramework_Debug;

		$this->oHeadTag = new AdminPageFramework_HeadTag_TaxonomyField( $this->oProp );
		$this->oHelpPane = new AdminPageFramework_HelpPane_TaxonomyField( $this->oProp );		
				
		/* Properties */
		$this->oProp->aTaxonomySlugs = ( array ) $asTaxonomySlug;
		$this->oProp->sOptionKey = $sOptionKey ? $sOptionKey : $this->oProp->sClassName;
		
		if ( $this->oProp->bIsAdmin ) {
			
			add_action( 'wp_loaded', array( $this, '_replyToLoadDefaultFieldTypeDefinitions' ), 10 );	// should be loaded before the setUp() method. The method is defined in the meta box base class.
			add_action( 'wp_loaded', array( $this, 'setUp' ), 11 );
			
			foreach( $this->oProp->aTaxonomySlugs as $sTaxonomySlug ) {				
				
				/* Validation callbacks need to be set regardless of the current page is edit-tags.php or not */
				add_action( "created_{$sTaxonomySlug}", array( $this, '_replyToValidateOptions' ), 10, 2 );
				add_action( "edited_{$sTaxonomySlug}", array( $this, '_replyToValidateOptions' ), 10, 2 );

				if ( $GLOBALS['pagenow'] != 'edit-tags.php' ) continue;
				add_action( "{$sTaxonomySlug}_add_form_fields", array( $this, '_replyToAddFieldsWOTableRows' ) );
				add_action( "{$sTaxonomySlug}_edit_form_fields", array( $this, '_replyToAddFieldsWithTableRows' ) );
				
			}
			
		}
		
		$this->oUtil->addAndDoAction( $this, "start_{$this->oProp->sClassName}" );		
		
	}
	
	/**
	 * The set up method.
	 * 
	 * @remark			should be overridden by the user definition class. 
	 * @since			3.0.0
	 */
	public function setUp() {}
	
	/**
	* Adds the given field array items into the field array property.
	* 
	* Identical to the addSettingFields() method except that this method does not accept enumerated parameters. 
	* 
	* <h4>Examples</h4>
	* <code>
	* 		$this->addSettingField(		
	* 			array(
	* 				'field_id'		=> 'textarea_field',
	* 				'type'			=> 'textarea',
	* 				'title'			=> __( 'Text Area', 'admin-page-framework-demo' ),
	* 				'description'	=> __( 'The description for the field.', 'admin-page-framework-demo' ),
	* 				'help'			=> __( 'This a <em>text area</em> input field, which is larger than the <em>text</em> input field.', 'admin-page-framework-demo' ),
	* 				'default'		=> __( 'This is a default text.', 'admin-page-framework-demo' ),
	* 				'attributes'	=>	array(
	* 					'cols'	=>	40,				
	* 				),
	* 			)
	* 		);		
	* </code>
	* 
	* @since			3.0.0			The scope changed to public to indicate the users will use.
	* @return			void
	* @remark			Do not check the 'if' key to skip the field registration because the added field IDs need to be retrieved later on when determining submitted values in the $_POST array.
	*/		
	public function addSettingField( array $aField ) {
		
		$aField = $aField + AdminPageFramework_Property_MetaBox::$_aStructure_Field;	// avoid undefined index warnings.
		
		// Sanitize the IDs since they are used as a callback method name.
		$aField['field_id'] = $this->oUtil->sanitizeSlug( $aField['field_id'] );
		
		// Check the mandatory keys are set.
		if ( ! isset( $aField['field_id'], $aField['type'] ) ) return;	// these keys are necessary.
													
		// Load head tag elements for fields.
		AdminPageFramework_FieldTypeRegistration::_setFieldHeadTagElements( $aField, $this->oProp, $this->oHeadTag );	// Set relevant scripts and styles for the input field.

		// For the contextual help pane,
		if ( $aField['help'] )
			$this->oHelpPane->_addHelpTextForFormFields( $aField['title'], $aField['help'], $aField['help_aside'] );
				
		$this->oProp->aFields[ $aField['field_id'] ] = $aField;
	
	}
	
	/**
	 * Sets the <var>$aOptions<var> property array in the property object. 
	 * 
	 * This array will be referred later in the getFieldOutput() method.
	 * 
	 * @since			unknown
	 * @since			3.0.0			the scope is changed to protected as the taxonomy field class redefines it.
	 * #internal
	 */
	protected function setOptionArray( $iTermID=null, $sOptionKey ) {
				
		$aOptions = get_option( $sOptionKey );
		$this->oProp->aOptions = isset( $iTermID, $aOptions[ $iTermID ] ) ? $aOptions[ $iTermID ] : array();

	}	
	
	/**
	 * Adds input fields
	 * 
	 * @internal
	 * @since			3.0.0
	 */	
	public function _replyToAddFieldsWOTableRows( $oTerm ) {
		echo $this->_getFieldsOutput( isset( $oTerm->term_id ) ? $oTerm->term_id : null, false );
	}
		
	/**
	 * Adds input fields with table rows.
	 * 
	 * @remark			Used for the Edit Category(taxonomy) page.
	 * @internal
	 * @since			3.0.0
	 */
	public function _replyToAddFieldsWithTableRows( $oTerm ) {
		echo $this->_getFieldsOutput( isset( $oTerm->term_id ) ? $oTerm->term_id : null, true );
	}
	
	/**
	 * Retrieves the fields output.
	 * 
	 * @since			3.0.0
	 * @internal
	 */
	private function _getFieldsOutput( $iTermID, $bRenderTableRow ) {
	
		$aOutput = array();
		
		/* Set nonce. */
		$aOutput[] = wp_nonce_field( $this->oProp->sClassHash, $this->oProp->sClassHash, true, false );
		
		/* Set the option property array */
		$this->setOptionArray( $iTermID, $this->oProp->sOptionKey );

		foreach ( $this->oProp->aFields as $aField ) {
			
			// Avoid undefined index warnings
			$aField = array( '_field_type' => 'taxonomy' ) + $aField + AdminPageFramework_Property_MetaBox::$_aStructure_Field;
			
			// Check capability. If the access level is not sufficient, skip.
			$aField['capability'] = isset( $aField['capability'] ) ? $aField['capability'] : $this->oProp->sCapability;
			if ( ! current_user_can( $aField['capability'] ) ) continue; 			

			// If a custom condition is set and it's not true, skip.
			if ( ! $aField['if'] ) continue;
		
			if ( $bRenderTableRow ) :
				$aOutput[] = "<tr>";	// Begin a table row. 
					if ( $aField['show_title_column'] )
						$aOutput[] = 
							"<th>"
								."<label for='{$aField['field_id']}'>"
									. "<a id='{$aField['field_id']}'></a>"
										. "<span title='" . strip_tags( isset( $aField['tip'] ) ? $aField['tip'] : $aField['description'] ) . "'>"
											. $aField['title'] 
										. "</span>"
								. "</label>"
							. "</th>";		
					$aOutput[] = "<td>";
						$aOutput[] = $this->getFieldOutput( $aField );
					$aOutput[] = "</td>";
				$aOutput[] = "</tr>";
			else :				
				if ( $aField['show_title_column'] )
					$aOutput[] = 
						"<label for='{$aField['field_id']}'>"
							. "<a id='{$aField['field_id']}'></a>"
								. "<span title='" . strip_tags( isset( $aField['tip'] ) ? $aField['tip'] : $aField['description'] ) . "'>"
									. $aField['title'] 
								. "</span>"
						. "</label>";					
				$aOutput[] = $this->getFieldOutput( $aField );
			endif;

			
		} // end foreach
		
		/* Filter the output */
		$sOutput = $this->oUtil->addAndApplyFilters( $this, 'content_' . $this->oProp->sClassName, implode( PHP_EOL, $aOutput ) );
		
		/* Do action */
		$this->oUtil->addAndDoActions( $this, 'do_' . $this->oProp->sClassName );
			
		return $sOutput;	
	
	}
	
	/**
	 * Validates the given option array.
	 * 
	 * @since			3.0.0
	 * @internal
	 */
	public function _replyToValidateOptions( $iTermID ) {
		
		if ( ! wp_verify_nonce( $_POST[ $this->oProp->sClassHash ], $this->oProp->sClassHash ) ) return;
		
		$aTaxonomyFieldOptions = get_option( $this->oProp->sOptionKey );
		$aOldOptions = isset( $aTaxonomyFieldOptions[ $iTermID ] ) ? $aTaxonomyFieldOptions[ $iTermID ] : array();
		$aSubmittedOptions = array();
		foreach( array_keys( $this->oProp->aFields ) as $sFieldID ) 
			if ( isset( $_POST[ $sFieldID ] ) ) $aSubmittedOptions[ $sFieldID ] = $_POST[ $sFieldID ];

		/* Apply validation filters to the submitted option array. */
		$aSubmittedOptions = $this->oUtil->addAndApplyFilters( $this, 'validation_' . $this->oProp->sClassName, $aSubmittedOptions, $aOldOptions );
		
		$aTaxonomyFieldOptions[ $iTermID ] = $this->oUtil->uniteArrays( $aSubmittedOptions, $aOldOptions );
		update_option( $this->oProp->sOptionKey, $aTaxonomyFieldOptions );
		
	}

}
endif;