<?php
if ( ! class_exists( 'AdminPageFramework_Setting_Base' ) ) :
/**
 * The base class of the setting class. 
 * 
 * This class mainly deals with internal methods and the constructor setting the properties.
 * 
 * @abstract
 * @since		3.0.0
 * @extends		AdminPageFramework_Menu
 * @package		AdminPageFramework
 * @subpackage	Page
 * @var			array		$aFieldErrors						stores the settings field errors.
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
	function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability=null, $sTextDomain='admin-page-framework' ) {
		
		add_action( 'admin_menu', array( $this, '_replyToRegisterSettings' ), 100 );	// registers the settings
		add_action( 'admin_init', array( $this, '_replyToCheckRedirects' ) );	// redirects
		
		parent::__construct( $sOptionKey, $sCallerPath, $sCapability, $sTextDomain );

		$this->oProp->sFieldsType = self::$_sFieldsType;
		$this->oForm = new AdminPageFramework_FormElement_Page( $this->oProp->sFieldsType, $this->oProp->sCapability );
		
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

		/* 1-1. Set up local variables */
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
			
			return $this->oForm->getPageOptions( $this->oProp->aOptions, $sPageSlug ); 			
			
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
			
			// Prepare the saved options 
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
										
				$aInput[ $sID ] = $this->oUtil->addAndApplyFilter( 
					$this, 
					"validation_{$this->oProp->sClassName}_{$sID}", 
					$aInput[ $sID ], 
					isset( $_aOptions[ $sID ] ) ? $_aOptions[ $sID ] : null 
				);
				
			}
						
			// Prepare the saved page option array.
			$_aPageOptions = $this->oForm->getPageOptions( $_aOptions, $sPageSlug );	// this method respects injected elements into the page ( page meta box fields )
			$_aPageOptions = $this->oUtil->addAndApplyFilter( $this, "validation_saved_options_{$sPageSlug}", $_aPageOptions );
			$_aTabOnlyOptions = array();
			$_aTabOptions = array();
						
			// For tabs
			if ( $sTabSlug && $sPageSlug )	{	
				$_aTabOnlyOptions = $this->oForm->getTabOnlyOptions( $_aOptions, $sPageSlug, $sTabSlug );		// does not respect page meta box fields
				$_aTabOptions = $this->oForm->getTabOptions( $_aOptions, $sPageSlug, $sTabSlug );		// respects page meta box fields
				$_aTabOptions = $this->oUtil->addAndApplyFilter( $this, "validation_saved_options_{$sPageSlug}_{$sTabSlug}", $_aTabOptions );
				$aInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$sPageSlug}_{$sTabSlug}", $aInput, $_aTabOptions );
				$aInput = $this->oUtil->uniteArrays( 
					$aInput, 
					$this->oUtil->invertCastArrayContents( $_aTabOptions, $_aTabOnlyOptions ),	// will only consist of page meta box fields
					$this->oForm->getOtherTabOptions( $_aOptions, $sPageSlug, $sTabSlug )
				);
			}
			
			// For pages	
			if ( $sPageSlug )	{
				
				$aInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$sPageSlug}", $aInput, $_aPageOptions ); // $aInput: new values, $aStoredPageOptions: old values	

				// If it's in a tab-page, drop the elements which belong to the tab so that arrayed-options will not be merged such as multiple select options.
				$_aPageOptions = $sTabSlug && ! empty( $_aTabOptions )? $this->oUtil->invertCastArrayContents( $_aPageOptions, $_aTabOptions ) : $_aPageOptions;
				$aInput = $this->oUtil->uniteArrays( 
					$aInput, 
					$_aPageOptions,	// repeatable elements have been dropped
					$this->oUtil->invertCastArrayContents( $this->oForm->getOtherPageOptions( $_aOptions, $sPageSlug ), $_aPageOptions )
				);	
				
			}

			// For the class
			$aInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$this->oProp->sClassName}", $aInput, $_aOptions );
			return $aInput;
		
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
		if (  $GLOBALS['pagenow'] != 'options.php' && ( count( $this->oForm->aConditionedFields ) == 0 ) ) return;

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
			$this->oProp->sOptionKey,	// the option key name that will be stored in the option table in the database.
			array( $this, 'validation_pre_' . $this->oProp->sClassName )	// the validation callback method
		); 
		
	}
		
	/**
	 * Returns the output of the filtered section description.
	 * 
	 * @remark			An alternative to _renderSectionDescription().
	 * @since			3.0.0
	 * @internal
	 */
	public function _replyToGetSectionHeaderOutput( $sSectionID ) {

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