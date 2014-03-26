<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Setting_Validation' ) ) :
/**
 * Deals with validating submitted options.
 * 
 * 
 * @abstract
 * @since		3.0.0
 * @extends		AdminPageFramework_Setting_Port
 * @package		AdminPageFramework
 * @subpackage	Page
 * @internal
 */
abstract class AdminPageFramework_Setting_Validation extends AdminPageFramework_Setting_Port {						
			
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
		
		/* Check if this is called from the framework's page */
		if ( ! isset( $_POST['_is_admin_page_framework'] ) ) return $aInput;
		
		/* 1-1. Set up local variables */
		$_sTabSlug =	isset( $_POST['tab_slug'] ) ? $_POST['tab_slug'] : '';	// no need to retrieve the default tab slug here because it's an embedded value that is already set in the previous page. 
		$_sPageSlug =	isset( $_POST['page_slug'] ) ? $_POST['page_slug'] : '';
		
		/* 1-2. Retrieve the pressed submit field data */
		$_sPressedFieldID =		isset( $_POST['__submit'] ) ? $this->_getPressedSubmitButtonData( $_POST['__submit'], 'field_id' ) : '';
		$_sPressedInputID =		isset( $_POST['__submit'] ) ? $this->_getPressedSubmitButtonData( $_POST['__submit'], 'input_id' ) : '';
		$_sPressedInputName =	isset( $_POST['__submit'] ) ? $this->_getPressedSubmitButtonData( $_POST['__submit'], 'name' ) : '';
		$_bIsReset =			isset( $_POST['__submit'] ) ? $this->_getPressedSubmitButtonData( $_POST['__submit'], 'is_reset' ) : '';	// if the 'reset' key in the field definition array is set, this value will be set.
		$_sKeyToReset =			isset( $_POST['__submit'] ) ? $this->_getPressedSubmitButtonData( $_POST['__submit'], 'reset_key' ) : '';	// this will be set if the user confirms the reset action.
		$_sSubmitSectionID =	isset( $_POST['__submit'] ) ? $this->_getPressedSubmitButtonData( $_POST['__submit'], 'section_id' ) : '';
		
		/* 1-3. Execute the submit_{...} actions. */
		$this->oUtil->addAndDoActions(
			$this,
			array( 
				"submit_{$this->oProp->sClassName}_{$_sPressedInputID}", 
				$_sSubmitSectionID ? "submit_{$this->oProp->sClassName}_{$_sSubmitSectionID}_{$_sPressedFieldID}" : "submit_{$this->oProp->sClassName}_{$_sPressedFieldID}",
				$_sSubmitSectionID ? "submit_{$this->oProp->sClassName}_{$_sSubmitSectionID}" : null,	// if null given, the method will ignore it
				isset( $_POST['tab_slug'] ) ? "submit_{$this->oProp->sClassName}_{$_sPageSlug}_{$_sTabSlug}" : null,	// if null given, the method will ignore it
				"submit_{$this->oProp->sClassName}_{sPageSlug}",
				"submit_{$this->oProp->sClassName}",
			)
		);                
		
		/* 2. Check if custom submit keys are set [part 1] */
		if ( isset( $_POST['__import']['submit'], $_FILES['__import'] ) ) 
			return $this->_importOptions( $this->oProp->aOptions, $_sPageSlug, $_sTabSlug );
		if ( isset( $_POST['__export']['submit'] ) ) 
			die( $this->_exportOptions( $this->oProp->aOptions, $_sPageSlug, $_sTabSlug ) );		
		if ( $_bIsReset )
			return $this->_askResetOptions( $_sPressedInputName, $_sPageSlug, $_sSubmitSectionID );
		if ( isset( $_POST['__submit'] ) && $_sLinkURL = $this->_getPressedSubmitButtonData( $_POST['__submit'], 'link_url' ) )
			die( wp_redirect( $_sLinkURL ) );	// if the associated submit button for the link is pressed, it will be redirected.
		if ( isset( $_POST['__submit'] ) && $_sRedirectURL = $this->_getPressedSubmitButtonData( $_POST['__submit'], 'redirect_url' ) )
			$this->_setRedirectTransients( $_sRedirectURL );
				
		/* 3. Apply validation filters - validation_{page slug}_{tab slug}, validation_{page slug}, validation_{instantiated class name} */
		$aInput = $this->_getFilteredOptions( $aInput, $_sPageSlug, $_sTabSlug );
		
		/* 4. Check if custom submit keys are set [part 2] - these should be done after applying the filters. */
		if ( $_sKeyToReset ) {
			$aInput = $this->_resetOptions( $_sKeyToReset, $aInput );
		}
		
		/* 5. Set the update notice */
		$_bEmpty = empty( $aInput );
		$this->setSettingNotice( 
			$_bEmpty ? $this->oMsg->__( 'option_cleared' ) : $this->oMsg->__( 'option_updated' ), 
			$_bEmpty ? 'error' : 'updated', 
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
		 * Applies validation filters to the submitted input data.
		 * 
		 * @since			2.0.0
		 * @since			2.1.5			Added the $sPressedFieldID and $sPressedInputID parameters.
		 * @since			3.0.0			Removed the $sPressedFieldID and $sPressedInputID parameters.
		 * @return			array			The filtered input array.
		 */
		private function _getFilteredOptions( $aInput, $sPageSlug, $sTabSlug ) {

			$aInput = is_array( $aInput ) ? $aInput : array();
			$_aInputToParse = $aInput;	// copy one for parsing
			
			// Prepare the saved options 
			$_aDefaultOptions = $this->oProp->getDefaultOptions( $this->oForm->aFields );		
			$_aOptions = $this->oUtil->uniteArrays( $this->oProp->aOptions, $_aDefaultOptions );
			$_aTabOptions = array();	// stores options of the belongning in-page tab.
			
			// Merge the user input with the user-set default values.
			$_aDefaultOptions = $this->_removePageElements( $_aDefaultOptions, $sPageSlug, $sTabSlug );	// do not include the default values of the submitted page's elements as they merge recursively
			$aInput = $this->oUtil->uniteArrays( $aInput, $this->oUtil->castArrayContents( $aInput, $_aDefaultOptions ) );
			unset( $_aDefaultOptions ); // to be clear that we don't use this any more
			
			// For each submitted element
			$aInput = $this->_validateEachField( $aInput, $_aOptions, $_aInputToParse );
			unset( $_aInputToParse ); // to be clear that we don't use this any more
											
			// For tabs			
			$aInput = $this->_validateTabFields( $aInput, $_aOptions, $_aTabOptions, $sPageSlug, $sTabSlug );
			
			// For pages
			$aInput = $this->_validatePageFields( $aInput, $_aOptions, $_aTabOptions, $sPageSlug, $sTabSlug );
			
			// For the class
			return $this->oUtil->addAndApplyFilter( $this, "validation_{$this->oProp->sClassName}", $aInput, $_aOptions );
		
		}	
			
			/**
			 * Validates each field or section.
			 * 
			 * @since			3.0.2
			 */
			private function _validateEachField( $aInput, $aOptions, $aInputToParse ) {
				
				foreach( $aInputToParse as $sID => $aSectionOrFields ) {	// $sID is either a section id or a field id
					
					if ( $this->oForm->isSection( $sID ) ) {
						foreach( $aSectionOrFields as $sFieldID => $aFields )	// For fields
							$aInput[ $sID ][ $sFieldID ] = $this->oUtil->addAndApplyFilter( 
								$this, 
								"validation_{$this->oProp->sClassName}_{$sID}_{$sFieldID}", 
								$aInput[ $sID ][ $sFieldID ], 
								isset( $aOptions[ $sID ][ $sFieldID ] ) ? $aOptions[ $sID ][ $sFieldID ] : null 
							);
					}
											
					$aInput[ $sID ] = $this->oUtil->addAndApplyFilter( 
						$this, 
						"validation_{$this->oProp->sClassName}_{$sID}", 
						$aInput[ $sID ], 
						isset( $aOptions[ $sID ] ) ? $aOptions[ $sID ] : null 
					);
					
				}
				
				return $aInput;
				
			}	
			
			/**
			 * Validates field options which belong to the given in-page tab.
			 * 
			 * @since			3.0.2
			 */
			private function _validateTabFields( $aInput, $aOptions, & $aTabOptions, $sPageSlug, $sTabSlug ) {
				
				if ( ! ( $sTabSlug && $sPageSlug ) ) {
					return $aInput;
				}
								
				$_aTabOnlyOptions = $this->oForm->getTabOnlyOptions( $aOptions, $sPageSlug, $sTabSlug );		// does not respect page meta box fields
				$aTabOptions = $this->oForm->getTabOptions( $aOptions, $sPageSlug, $sTabSlug );		// respects page meta box fields
				$aTabOptions = $this->oUtil->addAndApplyFilter( $this, "validation_saved_options_{$sPageSlug}_{$sTabSlug}", $aTabOptions );

				return $this->oUtil->uniteArrays( 
					$this->oUtil->addAndApplyFilter( $this, "validation_{$sPageSlug}_{$sTabSlug}", $aInput, $aTabOptions ), 
					$this->oUtil->invertCastArrayContents( $aTabOptions, $_aTabOnlyOptions ),	// will only consist of page meta box fields
					$this->oForm->getOtherTabOptions( $aOptions, $sPageSlug, $sTabSlug )
				);
				
			}			
			
			/**
			 * Validates field options which belong to the given page.
			 * 
			 * @since			3.0.2
			 */
			private function _validatePageFields( $aInput, $aOptions, $aTabOptions, $sPageSlug, $sTabSlug ) {
				
				if ( ! $sPageSlug ) {
					return $aInput;
				}

				// Prepare the saved page option array.
				$_aPageOptions = $this->oForm->getPageOptions( $aOptions, $sPageSlug );	// this method respects injected elements into the page ( page meta box fields )
				$_aPageOptions = $this->oUtil->addAndApplyFilter( $this, "validation_saved_options_{$sPageSlug}", $_aPageOptions );
				
				
				$aInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$sPageSlug}", $aInput, $_aPageOptions ); // $aInput: new values, $aStoredPageOptions: old values	

				// If it's in a tab-page, drop the elements which belong to the tab so that arrayed-options will not be merged such as multiple select options.
				$_aPageOptions = $sTabSlug && ! empty( $aTabOptions ) 
					? $this->oUtil->invertCastArrayContents( $_aPageOptions, $aTabOptions ) 
					: ( ! $sTabSlug		// if the tab is not specified, do not merge the input array with the page options as the input array already includes the page options. This is for dynamic elements(repeatable sections).
						? array()
						: $_aPageOptions
					);
					
				return $this->oUtil->uniteArrays( 
					$aInput, 
					$_aPageOptions,	// repeatable elements have been dropped
					$this->oUtil->invertCastArrayContents( $this->oForm->getOtherPageOptions( $aOptions, $sPageSlug ), $_aPageOptions )
				);	
								
			}			
			
			/**
			 * Removes option array elements that belongs to the given page/tab by their slug.
			 * 
			 * This is used when merging options and avoiding merging options that have an array structure as the framework uses the recursive merge
			 * and if an option is not a string but an array, the default array of such a structure will merge with the user input of the corresponding structure. 
			 * This problem will occur with the select field type with multiple attribute enabled. 
			 * 
			 * @since			3.0.0
			 */
			private function _removePageElements( $aOptions, $sPageSlug, $sTabSlug ) {
				
				if ( ! $sPageSlug && ! $sTabSlug ) return $aOptions;
				
				// If the tab is given
				if ( $sTabSlug && $sPageSlug ) {
					return $this->oForm->getOtherTabOptions( $aOptions, $sPageSlug, $sTabSlug );
				}
				
				// If only the page is given 
				return $this->oForm->getOtherPageOptions( $aOptions, $sPageSlug );
				
			}
			
}
endif;