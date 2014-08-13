<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Setting_Base' ) ) :
/**
 * The base class of the setting class that deals with registering and rendering fields.
 * 
 * This class mainly deals with internal methods and the constructor setting the properties. 
 * 
 * @abstract
 * @since		3.0.0
 * @extends		AdminPageFramework_Menu
 * @package		AdminPageFramework
 * @subpackage	Page
 * @var			array		$aFieldErrors						stores the settings field errors.
 * @internal
 */
abstract class AdminPageFramework_Setting_Base extends AdminPageFramework_Menu {
		
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
	 * Stores the target page slug which will be applied when no page slug is specified for the addSettingSection() method.
	 * 
	 * @since			3.0.0
	 */
	protected $_sTargetPageSlug = null;
	
	/**
	 * Stores the target tab slug which will be applied when no tab slug is specified for the addSettingSection() method.
	 * 
	 * @since			3.0.0
	 */	
	protected $_sTargetTabSlug = null;

	/**
	 * Stores the target section tab slug which will be applied when no section tab slug is specified for the addSettingSection() method.
	 * 
	 * @since			3.0.0
	 */	
	protected $_sTargetSectionTabSlug = null;
	
	/**
	 * Registers necessary hooks and sets up properties.
	 * 
	 * @internal
	 */
	function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {
		
		parent::__construct( $sOptionKey, $sCallerPath, $sCapability, $sTextDomain );

		if ( $this->oProp->bIsAdminAjax ) {
			return;
		}
		
		if ( $this->oProp->bIsAdmin ) {
		
			add_action( "load_after_{$this->oProp->sClassName}", array( $this, '_replyToRegisterSettings' ), 20 );	// Have a low priority to let in-page finalization done earlier.
			add_action( "load_after_{$this->oProp->sClassName}", array( $this, '_replyToCheckRedirects' ), 21 );	// should be loaded after registering the settings.
			
		}
					
	}
		
	/**
	 * Check if a redirect transient is set and if so it redirects to the set page.
	 * 
	 * @remark			A callback method for the admin_init hook.
	 * @internal
	 */
	public function _replyToCheckRedirects() {

		// Check if it's one of the plugin's added page. If not, do nothing.
		// if ( ! ( isset( $_GET['page'] ) ) || ! $this->oProp->isPageAdded( $_GET['page'] ) ) return; 
		if ( ! $this->_isInThePage() ) {
			return;
		}

		// If the settings have not updated the options, do nothing.
		if ( ! ( isset( $_GET['settings-updated'] ) && ! empty( $_GET['settings-updated'] ) ) ) {
			return;
		}
		
		// The redirect transient key.
		$_sTransient = md5( trim( "redirect_{$this->oProp->sClassName}_{$_GET['page']}" ) );
		
		// Check the settings error transient.
		$_aError = $this->_getFieldErrors( $_GET['page'], false );
		if ( ! empty( $_aError ) ) {
			delete_transient( $_sTransient );	// we don't need it any more.
			return;
		}
		
		// Okay, it seems the submitted data have been updated successfully.
		$_sURL = get_transient( $_sTransient );
		if ( false === $_sURL ) {
			return;
		}
		
		// The redirect URL seems to be set.
		delete_transient( $_sTransient );	// we don't need it any more.
					
		// Go to the page.
		die( wp_redirect( $_sURL ) );
		
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
	 * @since			3.1.2			Changed the hook from the <em>admin_menu</em> to <em>current_screen</em> so that the user can add forms in <em>load_{...}</em> callback methods.
	 * @since			3.1.3			Removed the Settings API related functions entirely.
	 * @remark			This method is not intended to be used by the user.
	 * @remark			The callback method for the <em>admin_init</em> hook.
	 * @return			void
	 * @internal
	 */ 
	public function _replyToRegisterSettings() {

		if ( ! $this->_isInThePage() ) { 
			return;
		}

		/* 1. Apply filters to added sections and fields */
		$this->oForm->aSections = $this->oUtil->addAndApplyFilter( $this, "sections_{$this->oProp->sClassName}", $this->oForm->aSections );
		foreach( $this->oForm->aFields as $_sSectionID => &$_aFields ) {
			$_aFields = $this->oUtil->addAndApplyFilter(	// Parameters: $oCallerObject, $aFilters, $vInput, $vArgs...
				$this,
				"fields_{$this->oProp->sClassName}_{$_sSectionID}",
				$_aFields
			); 
			unset( $_aFields );	// to be safe in PHP especially the same variable name is used in the scope.
		}
		$this->oForm->aFields = $this->oUtil->addAndApplyFilter(	// Parameters: $oCallerObject, $aFilters, $vInput, $vArgs...
			$this,
			"fields_{$this->oProp->sClassName}",
			$this->oForm->aFields
		); 		
		
		/* 2. Format ( sanitize ) the section and field arrays and apply conditions to the sections and fields and drop unnecessary items. */
		// 2-1. Set required properties for formatting.
		$this->oForm->setDefaultPageSlug( $this->oProp->sDefaultPageSlug );	
		$this->oForm->setOptionKey( $this->oProp->sOptionKey );
		$this->oForm->setCallerClassName( $this->oProp->sClassName );
		
		// 2-2. Do format internally stored sections and fields definition arrays.
		$this->oForm->format();

		// 2-3. Now set required properties for conditioning.
		$this->oForm->setCurrentPageSlug( isset( $_GET['page'] ) && $_GET['page'] ? $_GET['page'] : '' );
		$this->oForm->setCurrentTabSlug( $this->oProp->getCurrentTab() );
		
		// 2-4. Do conditioning.
		$this->oForm->applyConditions();
		$this->oForm->setDynamicElements( $this->oProp->aOptions );	// will update $this->oForm->aConditionedFields
		
		/* 3. Define field types. This class adds filters for the field type definitions so that framework's built-in field types will be added. */
		$this->oProp->aFieldTypeDefinitions = AdminPageFramework_FieldTypeRegistration::register( $this->oProp->aFieldTypeDefinitions, $this->oProp->sClassName, $this->oMsg );
		$this->oProp->aFieldTypeDefinitions = $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $sFilter, $vInput, $vArgs...
			$this,
			'field_types_' . $this->oProp->sClassName,	// 'field_types_' . {extended class name}
			$this->oProp->aFieldTypeDefinitions
		);		

		/* 4. Register settings sections */ 
		foreach( $this->oForm->aConditionedSections as $_aSection ) {
									
			/* For the contextual help pane */
			if ( ! empty( $_aSection['help'] ) )
				$this->addHelpTab( 
					array(
						'page_slug'					=> $_aSection['page_slug'],
						'page_tab_slug'				=> $_aSection['tab_slug'],
						'help_tab_title'			=> $_aSection['title'],
						'help_tab_id'				=> $_aSection['section_id'],
						'help_tab_content'			=> $_aSection['help'],
						'help_tab_sidebar_content'	=> $_aSection['help_aside'] ? $_aSection['help_aside'] : "",
					)
				);
				
		}

		/* 5. Set head tag and help pane elements */
		foreach( $this->oForm->aConditionedFields as $_sSectionID => $_aSubSectionOrFields ) {
			
			foreach( $_aSubSectionOrFields as $_sSubSectionIndexOrFieldID => $_aSubSectionOrField ) {
				
				// If the iterating item is a sub-section array.
				if ( is_numeric( $_sSubSectionIndexOrFieldID ) && is_int( $_sSubSectionIndexOrFieldID + 0 ) ) {
					
					$_iSubSectionIndex = $_sSubSectionIndexOrFieldID;
					$_aSubSection = $_aSubSectionOrField;
					foreach( $_aSubSection as $__sFieldID => $__aField ) {																		
						AdminPageFramework_FieldTypeRegistration::_setFieldHeadTagElements( $__aField, $this->oProp, $this->oHeadTag );	// Set relevant scripts and styles for the input field.
					}
					continue;
					
				}
					
				/* 5-1. Add the given field. */
				$aField = $_aSubSectionOrField;

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
		
		/* 6. Enable the form - Set the form enabling flag so that the <form></form> tag will be inserted in the page. */
		$this->oProp->bEnableForm = true;	
		
		/* 7. Handle submitted data. */
		$this->_handleSubmittedData();	
		
	}
		
	/**
	 * Returns the output of the filtered section description.
	 * 
	 * @remark			An alternative to _renderSectionDescription().
	 * @since			3.0.0
	 * @internal
	 */
	public function _replyToGetSectionHeaderOutput( $sSectionDescription, $aSection ) {

		// $_sCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;	
		// $_sSectionID = $aSection['section_id'];
		// if ( ! isset( $this->oForm->aSections[ $_sSectionID ] ) ) return '';	// if it is not added
		// if ( ! $this->oForm->isPageAdded( $_sCurrentPageSlug ) ) return '';
		
		return $this->oUtil->addAndApplyFilters(
			$this,
			array( 'section_head_' . $this->oProp->sClassName . '_' . $aSection['section_id'] ),	// section_{instantiated class name}_{section id}
			$sSectionDescription
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

		$_oField = new AdminPageFramework_FormField( $aField, $this->oProp->aOptions, $this->aFieldErrors, $this->oProp->aFieldTypeDefinitions, $this->oMsg );
		$_sFieldOutput = $_oField->_getFieldOutput();	// field output
		unset( $_oField );	// release the object for PHP 5.2.x or below.

		return $this->oUtil->addAndApplyFilters(
			$this,
			array( 
				isset( $aField['section_id'] ) && $aField['section_id'] != '_default' 
					? 'field_' . $this->oProp->sClassName . '_' . $aField['section_id'] . '_' . $_sFieldID
					: 'field_' . $this->oProp->sClassName . '_' . $_sFieldID,
			),
			$_sFieldOutput,
			$aField // the field array
		);		
		
	}
}
endif;