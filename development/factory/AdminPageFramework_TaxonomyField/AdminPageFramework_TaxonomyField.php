<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_TaxonomyField' ) ) :
/**
 * Provides methods for creating fields in the taxonomy page (edit-tags.php).
 *
 * <h2>Hooks</h2>
 * <p>The class automatically creates WordPress action and filter hooks associated with the class methods.
 * The class methods corresponding to the name of the below actions and filters can be extended to modify the page output. Those methods are the callbacks of the filters and actions.</p>
 * <h3>Methods and Action Hooks</h3>
 * <ul>
 * 	<li><strong>start_{instantiated class name}</strong> – triggered at the end of the class constructor.</li>
 * 	<li><strong>do_{instantiated class name}</strong> – triggered when the meta box gets rendered.</li>
 * </ul>
 * <h3>Methods and Filter Hooks</h3>
 * <ul>
 * 	<li><strong>field_types_{instantiated class name}</strong> – receives the field type definition array. The first parameter: the field type definition array.</li>
 * 	<li><strong>field_{instantiated class name}_{field ID}</strong> – receives the form input field output of the given input field ID. The first parameter: output string. The second parameter: the array of option.</li>
 * 	<li><strong>content_{instantiated class name}</strong> – receives the entire output of the meta box. The first parameter: the output HTML string.</li>
 * 	<li><strong>style_common_{instantiated class name}</strong> –  receives the output of the base CSS rules applied to the pages of the associated post types with the meta box.</li>
 * 	<li><strong>style_ie_common_{instantiated class name}</strong> –  receives the output of the base CSS rules for Internet Explorer applied to the pages of the associated post types with the meta box.</li>
 * 	<li><strong>style_{instantiated class name}</strong> –  receives the output of the CSS rules applied to the pages of the associated post types with the meta box.</li>
 * 	<li><strong>style_ie_{instantiated class name}</strong> –  receives the output of the CSS rules for Internet Explorer applied to the pages of the associated post types with the meta box.</li>
 * 	<li><strong>script_common_{instantiated class name}</strong> – receives the output of the base JavaScript scripts applied to the pages of the associated post types with the meta box.</li>
 * 	<li><strong>script_{instantiated class name}</strong> – receives the output of the JavaScript scripts applied to the pages of the associated post types with the meta box.</li>
 * 	<li><strong>validation_{instantiated class name}</strong> – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 * 	<li><strong>columns_{taxonomy slug}</strong> – receives the header columns array. The first parameter: the header columns array.</li>
 * 	<li><strong>columns_{instantiated class name}</strong> – receives the header sortable columns array. The first parameter: the header columns array.</li>
 * 	<li><strong>sortable_columns_{taxonomy slug}</strong> – receives the header sortable columns array. The first parameter: the header columns array.</li>
 * 	<li><strong>sortable_columns_{instantiated class name}</strong> – receives the header columns array. The first parameter: the header columns array.</li>
 * 	<li><strong>cell_{taxonomy slug}</strong> – receives the cell output of the term listing table. The first parameter: the output string. The second parameter: the column slug. The third parameter: the term ID.</li>
 * 	<li><strong>cell_{instantiated class name}</strong> – receives the cell output of the term listing table. The first parameter: the output string. The second parameter: the column slug. The third parameter: the term ID.</li>
 * 	<li><strong>cell_{instantiated class name}_{column slug}</strong> – receives the cell output of the term listing table. The first parameter: the output string. The second parameter: the term ID.</li>
 * </ul> 
 * @abstract
 * @since			3.0.0
 * @use				AdminPageFramework_Utility
 * @use				AdminPageFramework_Message
 * @use				AdminPageFramework_Debug
 * @use				AdminPageFramework_Property_MetaBox
 * @package			AdminPageFramework
 * @subpackage		TaxonomyField
 * @extends			AdminPageFramework_Factory
 
 * @todo			Fix issues caused by extending the factory class and provide missing elements which were present in the meta box base class.
 */
abstract class AdminPageFramework_TaxonomyField extends AdminPageFramework_Factory {
	
	/**
	 * Stores the property object.
	 * 
	 * @since			3.0.0
	 * @acess			public			Inherited from the ancestor class.
	 */
	public $oProp;
	
	/**
	 * Stores the head tag object.
	 * 
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
	 * Defines the fields type.
	 * @since			3.0.0
	 * @internal
	 */
	static protected $_sFieldsType = 'taxonomy';
	
	/**
	 * Constructs the class object instance of AdminPageFramework_TaxonomyField.
	 * 
	 * Handles setting up properties and hooks.
	 * 
	 * <h4>Examples</h4>
	 * <code>
	 * new APF_TaxonomyField( 'apf_sample_taxonomy' );		// taxonomy slug
	 * </code>
	 * 
	 * @since			3.0.0
	 * @param			array|string			The taxonomy slug(s). If multiple slugs need to be passed, enclose them in an array and pass the array.
	 * @param			string					The option key used for the options table to save the data. By default, the instantiated class name will be applied.
	 * @param			string					The access rights. Default: <em>manage_options</em>.
	 * @param			string					The text domain. Default: <em>admin-page-framework</em>.
	 * @return			void
	 */ 
	function __construct( $asTaxonomySlug, $sOptionKey='', $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {
		
		if ( empty( $asTaxonomySlug ) ) return;
		
		/* Properties */
		$this->oProp = new AdminPageFramework_Property_TaxonomyField( $this, get_class( $this ), $sCapability, $sTextDomain, self::$_sFieldsType );				
		$this->oProp->aTaxonomySlugs = ( array ) $asTaxonomySlug;
		$this->oProp->sOptionKey = $sOptionKey ? $sOptionKey : $this->oProp->sClassName;
		
		parent::__construct( $this->oProp );
		
		if ( $this->oProp->bIsAdmin ) {
			
			add_action( 'wp_loaded', array( $this, '_replyToDetermineToLoad' ) );
			
		}
		
		$this->oUtil->addAndDoAction( $this, "start_{$this->oProp->sClassName}" );		
		
	}
	
	/**
	 * Determines whether the meta box belongs to the loading page.
	 * 
	 * @since			3.0.3
	 * @internal
	 */
	protected function _isInThePage() {
		
		return ( in_array( $GLOBALS['pagenow'], array( 'edit-tags.php', 'admin-ajax.php' ) ) );
		
	}	
	
	/**
	 * Determines whether the meta box should be loaded in the currently loading page.
	 * 
	 * @since			3.0.3
	 * @internal
	 */
	public function _replyToDetermineToLoad() {
		
		if ( ! $this->_isInThePage() ) return;
		
		$this->_loadDefaultFieldTypeDefinitions();
		$this->setUp();
		add_action( 'current_screen', array( $this, '_replyToRegisterFormElements' ) );	// the screen object should be established to detect the loaded page. 
	
		foreach( $this->oProp->aTaxonomySlugs as $__sTaxonomySlug ) {				
			
			/* Validation callbacks need to be set regardless of whether the current page is edit-tags.php or not */
			add_action( "created_{$__sTaxonomySlug}", array( $this, '_replyToValidateOptions' ), 10, 2 );
			add_action( "edited_{$__sTaxonomySlug}", array( $this, '_replyToValidateOptions' ), 10, 2 );

			// if ( $GLOBALS['pagenow'] != 'admin-ajax.php' && $GLOBALS['pagenow'] != 'edit-tags.php' ) continue;
			add_action( "{$__sTaxonomySlug}_add_form_fields", array( $this, '_replyToAddFieldsWOTableRows' ) );
			add_action( "{$__sTaxonomySlug}_edit_form_fields", array( $this, '_replyToAddFieldsWithTableRows' ) );
			
			add_filter( "manage_edit-{$__sTaxonomySlug}_columns", array( $this, '_replyToManageColumns' ), 10, 1 );
			add_filter( "manage_edit-{$__sTaxonomySlug}_sortable_columns", array( $this, '_replyToSetSortableColumns' ) );
			add_action( "manage_{$__sTaxonomySlug}_custom_column", array( $this, '_replyToSetColumnCell' ), 10, 3 );
			
		}		
		
	}	
	
	/**
	 * The set up method.
	 * 
	 * @remark			should be overridden by the user definition class. 
	 * @since			3.0.0
	 */
	public function setUp() {}
		
	/**
	 * Sets the <var>$aOptions<var> property array in the property object. 
	 * 
	 * This array will be referred later in the getFieldOutput() method.
	 * 
	 * @since			unknown
	 * @since			3.0.0			the scope is changed to protected as the taxonomy field class redefines it.
	 * #internal
	 */
	protected function _setOptionArray( $iTermID=null, $sOptionKey ) {
				
		$aOptions = get_option( $sOptionKey, array() );
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
	 * Modifies the columns of the term listing table in the edit-tags.php page.
	 * 
	 * @internal
	 * @since			3.0.0
	 */
	public function _replyToManageColumns( $aColumns ) {

		/* By default something like this is passed.
			Array (
				[cb] => <input type="checkbox" />
				[name] => Name
				[description] => Description
				[slug] => Slug
				[posts] => Admin Page Framework
			) 
		 */
		if ( isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] )
			$aColumns = $this->oUtil->addAndApplyFilter( $this, "columns_{$_GET['taxonomy']}", $aColumns );
		$aColumns = $this->oUtil->addAndApplyFilter( $this, "columns_{$this->oProp->sClassName}", $aColumns );	
		return $aColumns;
		
	}
	
	/**
	 * 
	 * @internal
	 * @since			3.0.0
	 */
	public function _replyToSetSortableColumns( $aSortableColumns ) {

		if ( isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] )
			$aSortableColumns = $this->oUtil->addAndApplyFilter( $this, "sortable_columns_{$_GET['taxonomy']}", $aSortableColumns );
		$aSortableColumns = $this->oUtil->addAndApplyFilter( $this, "sortable_columns_{$this->oProp->sClassName}", $aSortableColumns );
		return $aSortableColumns;
		
	}
	/**
	 * 
	 * @internal
	 * @since			3.0.0
	 */
	public function _replyToSetColumnCell( $vValue, $sColumnSlug, $sTermID ) {
		
		$sCellHTML = '';
		if ( isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] )
			$sCellHTML = $this->oUtil->addAndApplyFilter( $this, "cell_{$_GET['taxonomy']}", $vValue, $sColumnSlug, $sTermID );
		$sCellHTML = $this->oUtil->addAndApplyFilter( $this, "cell_{$this->oProp->sClassName}", $sCellHTML, $sColumnSlug, $sTermID );
		$sCellHTML = $this->oUtil->addAndApplyFilter( $this, "cell_{$this->oProp->sClassName}_{$sColumnSlug}", $sCellHTML, $sTermID );	// 3.0.2+
		echo $sCellHTML;
				
	}
	
	/**
	 * Retrieves the fields output.
	 * 
	 * @since			3.0.0
	 * @internal
	 */
	private function _getFieldsOutput( $iTermID, $bRenderTableRow ) {
	
		$_aOutput = array();
		
		/* Set nonce. */
		$_aOutput[] = wp_nonce_field( $this->oProp->sClassHash, $this->oProp->sClassHash, true, false );
		
		/* Set the option property array */
		$this->_setOptionArray( $iTermID, $this->oProp->sOptionKey );
		
		/* Format the fields arrays - taxonomy fields do not support sections */
		$this->oForm->format();
		
		/* Get the field outputs */
		$_oFieldsTable = new AdminPageFramework_FormTable( $this->oProp->aFieldTypeDefinitions, $this->_getFieldErrors(), $this->oMsg );
		$_aOutput[] = $bRenderTableRow 
			? $_oFieldsTable->getFieldRows( $this->oForm->aFields['_default'], array( $this, '_replyToGetFieldOutput' ) )
			: $_oFieldsTable->getFields( $this->oForm->aFields['_default'], array( $this, '_replyToGetFieldOutput' ) );
				
		/* Filter the output */
		$_sOutput = $this->oUtil->addAndApplyFilters( $this, 'content_' . $this->oProp->sClassName, implode( PHP_EOL, $_aOutput ) );
		
		/* Do action */
		$this->oUtil->addAndDoActions( $this, 'do_' . $this->oProp->sClassName );
			
		return $_sOutput;
	
	}
	
	/**
	 * Validates the given option array.
	 * 
	 * @since			3.0.0
	 * @internal
	 */
	public function _replyToValidateOptions( $iTermID ) {
		
		if ( ! wp_verify_nonce( $_POST[ $this->oProp->sClassHash ], $this->oProp->sClassHash ) ) return;
		
		$aTaxonomyFieldOptions = get_option( $this->oProp->sOptionKey, array() );
		$aOldOptions = isset( $aTaxonomyFieldOptions[ $iTermID ] ) ? $aTaxonomyFieldOptions[ $iTermID ] : array();
		$aSubmittedOptions = array();
		foreach( $this->oForm->aFields as $_sSectionID => $_aFields ) 
			foreach( $_aFields as $_sFieldID => $_aField ) 
				if ( isset( $_POST[ $_sFieldID ] ) ) 
					$aSubmittedOptions[ $_sFieldID ] = $_POST[ $_sFieldID ];
			
		/* Apply validation filters to the submitted option array. */
		$aSubmittedOptions = $this->oUtil->addAndApplyFilters( $this, 'validation_' . $this->oProp->sClassName, $aSubmittedOptions, $aOldOptions );
		
		$aTaxonomyFieldOptions[ $iTermID ] = $this->oUtil->uniteArrays( $aSubmittedOptions, $aOldOptions );
		update_option( $this->oProp->sOptionKey, $aTaxonomyFieldOptions );
		
	}

	/**
	 * Registers form fields and sections.
	 * 
	 * @since			3.0.0
	 * @internal
	 */
	public function _replyToRegisterFormElements() {
	
		// Schedule to add head tag elements and help pane contents.
		if ( $GLOBALS['pagenow'] != 'edit-tags.php' ) return;
		
		// Format the fields array.
		$this->oForm->format();
		$this->oForm->applyConditions();
		// $this->oForm->applyFiltersToFields( $this, $this->oProp->sClassName );
		
		// not sure if setDynamicElements() should be performed or not...
		
		$this->_registerFields( $this->oForm->aConditionedFields );
		
	}	
	
	/**
	 * Redirects undefined callback methods.
	 * @internal
	 * @since			3.0.0
	 */
	
	function __call( $sMethodName, $aArgs=null ) {		
	
		if ( isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] ) :
			if ( substr( $sMethodName, 0, strlen( 'columns_' . $_GET['taxonomy'] ) ) == 'columns_' . $_GET['taxonomy'] ) return $aArgs[ 0 ];
			if ( substr( $sMethodName, 0, strlen( 'sortable_columns_' . $_GET['taxonomy'] ) ) == 'sortable_columns_' . $_GET['taxonomy'] ) return $aArgs[ 0 ];
			if ( substr( $sMethodName, 0, strlen( 'cell_' . $_GET['taxonomy'] ) ) == 'cell_' . $_GET['taxonomy'] ) return $aArgs[ 0 ];
		endif;
	
		if ( substr( $sMethodName, 0, strlen( 'columns_' . $this->oProp->sClassName ) ) == 'columns_' . $this->oProp->sClassName ) return $aArgs[ 0 ];
		if ( substr( $sMethodName, 0, strlen( 'sortable_columns_' . $this->oProp->sClassName ) ) == 'sortable_columns_' . $this->oProp->sClassName ) return $aArgs[ 0 ];
		if ( substr( $sMethodName, 0, strlen( 'cell_' . $this->oProp->sClassName ) ) == 'cell_' . $this->oProp->sClassName ) return $aArgs[ 0 ];

		return parent::__call( $sMethodName, $aArgs );
		
	}
}
endif;