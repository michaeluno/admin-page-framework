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
		
		add_action( 'admin_menu', array( $this, '_replyToRegisterSettings' ), 100 );	// registers the settings
		add_action( 'admin_init', array( $this, '_replyToCheckRedirects' ) );	// redirects
		
		parent::__construct( $sOptionKey, $sCallerPath, $sCapability, $sTextDomain );

		// $this->oForm = new AdminPageFramework_FormElement_Page( $this->oProp->sFieldsType, $this->oProp->sCapability );
				
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
		if ( ! empty( $aError ) ) {
			return;
		}
		
		// Okay, it seems the submitted data have been updated successfully.
		$sTransient = md5( trim( "redirect_{$this->oProp->sClassName}_{$_GET['page']}" ) );
		$sURL = get_transient( $sTransient );
		if ( false === $sURL  ) {
			return;
		}
		
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
		
		if ( ! $this->_isInThePage() ) return;
		
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
		
		/* 2-5. If there is no section or field to add, do nothing. */
		// if ( 'options.php' != $this->oProp->sPageNow && ( count( $this->oForm->aConditionedFields ) == 0 ) ) return;

		/* 3. Define field types. This class adds filters for the field type definitions so that framework's built-in field types will be added. */
		new AdminPageFramework_FieldTypeRegistration( $this->oProp->aFieldTypeDefinitions, $this->oProp->sClassName, $this->oMsg );
		$this->oProp->aFieldTypeDefinitions = $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $sFilter, $vInput, $vArgs...
			$this,
			'field_types_' . $this->oProp->sClassName,	// 'field_types_' . {extended class name}
			$this->oProp->aFieldTypeDefinitions
		);		

		/* 4. Register settings sections */ 
		foreach( $this->oForm->aConditionedSections as $_aSection ) {
			
			/* 4-1. Add the given section */
			add_settings_section(
				$_aSection['section_id'],	//  section ID
				"<a id='{$_aSection['section_id']}'></a>" . $_aSection['title'],	// title - place the anchor in front of the title.
				array( $this, 'section_pre_' . $_aSection['section_id'] ), 		// callback function -  this will trigger the __call() magic method.
				$_aSection['page_slug']	// page
			);
						
			/* 4-2. For the contextual help pane */
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
		
		/* 5. Register settings fields	*/
		foreach( $this->oForm->aConditionedFields as $_sSectionID => $_aSubSectionOrFields ) {
			
			foreach( $_aSubSectionOrFields as $_sSubSectionIndexOrFieldID => $_aSubSectionOrField ) {
				
				// If the iterating item is a sub-section array.
				if ( is_numeric( $_sSubSectionIndexOrFieldID ) && is_int( $_sSubSectionIndexOrFieldID + 0 ) ) {
					
					$_iSubSectionIndex = $_sSubSectionIndexOrFieldID;
					$_aSubSection = $_aSubSectionOrField;
					foreach( $_aSubSection as $__sFieldID => $__aField ) {												
						add_settings_field(
							$__aField['section_id'] . '_' . $_iSubSectionIndex . '_' . $__aField['field_id'],	// id
							"<a id='{$__aField['section_id']}_{$_iSubSectionIndex}_{$__aField['field_id']}'></a><span title='{$__aField['tip']}'>{$__aField['title']}</span>",
							null,	// callback function - no longer used by the framework
							$this->oForm->getPageSlugBySectionID( $__aField['section_id'] ), // page slug
							$__aField['section_id']	// section
						);							
						AdminPageFramework_FieldTypeRegistration::_setFieldHeadTagElements( $__aField, $this->oProp, $this->oHeadTag );	// Set relevant scripts and styles for the input field.
					}
					continue;
					
				}
					
				/* 5-1. Add the given field. */
				$aField = $_aSubSectionOrField;
				add_settings_field(
					$aField['section_id'] . '_' . $aField['field_id'],	// id
					"<a id='{$aField['section_id']}_{$aField['field_id']}'></a><span title='{$aField['tip']}'>{$aField['title']}</span>",
					null,	// callback function - no longer used by the framework
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
			$this->oProp->sOptionKey	// the option key name that will be stored in the option table in the database.
			// array( $this, 'validation_pre_' . $this->oProp->sClassName )	// the validation callback method
		); 
		
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

		$oField = new AdminPageFramework_FormField( $aField, $this->oProp->aOptions, $this->aFieldErrors, $this->oProp->aFieldTypeDefinitions, $this->oMsg );
		$sFieldOutput = $oField->_getFieldOutput();	// field output
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