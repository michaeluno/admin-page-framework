<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_FormTable' ) ) :
/**
 * Provides methods to render setting sections and fields.
 * 
 * @package			AdminPageFramework
 * @subpackage		Form
 * @since			3.0.0
 * @internal
 */
class AdminPageFramework_FormTable extends AdminPageFramework_FormTable_Base {
		
	/**
	 * Returns a set of HTML table outputs consisting of form sections and fields.
	 * 
	 * @since			3.0.0
	 */
	public function getFormTables( $aSections, $aFieldsInSections, $hfSectionCallback, $hfFieldCallback ) {
		
		$_aOutput = array();
		foreach( $this->_getSectionsBySectionTabs( $aSections ) as $_sSectionTabSlug => $_aSections ) {
			$_sSectionSet = $this->_getFormTablesBySectionTab( $_sSectionTabSlug, $_aSections, $aFieldsInSections, $hfSectionCallback, $hfFieldCallback );
			if ( $_sSectionSet )
				$_aOutput[] = "<div " . $this->generateAttributes(
						array(
							'class'	=>	'admin-page-framework-sectionset',
							'id'	=>	"sectionset-{$_sSectionTabSlug}_" . md5( serialize( $_aSections ) ),
						)
					) . ">" 
						. $_sSectionSet
					. "</div>";
		}
		return implode( PHP_EOL, $_aOutput ) 
			. $this->_getSectionTabsEnablerScript();
			
	}
		
		/**
		 * Returns the JavaScript script that enables section tabs.
		 * 
		 * @since			3.0.0
		 */
		private function _getSectionTabsEnablerScript() {
			
			// Stores the flag indicating whether the method is called. PHP static variables are stored among different class instances. 
			// So this will make sure that it is done only once per a page load.
			static $bIsCalled = false;	
			if ( $bIsCalled ) return '';
			$bIsCalled = true;
			
			// wp_enqueue_script( 'jquery-ui-tabs' );
			return "<script type='text/javascript'>
				jQuery( document ).ready( function() {
					jQuery( '.admin-page-framework-section-tabs-contents' ).createTabs();	// the parent element of the ul tag; The ul element holds li tags of titles.
					// jQuery( '.admin-page-framework-section-tabs-contents' ).tabs();	// the parent element of the ul tag; The ul element holds li tags of titles.
				});
			</script>";					
			
		}
		
		/**
		 * Returns an output string of form tables.
		 * 
		 * @since			3.0.0
		 */
		private function _getFormTablesBySectionTab( $sSectionTabSlug, $aSections, $aFieldsInSections, $hfSectionCallback, $hfFieldCallback ) {

			if ( empty( $aSections ) ) return '';	// if empty, return a blank string.
		
			/* <ul>
				<li><a href="#tabs-1">Nunc tincidunt</a></li>
				<li><a href="#tabs-2">Proin dolor</a></li>
				<li><a href="#tabs-3">Aenean lacinia</a></li>
			</ul>		 */			
			$_aSectionTabList = array();

			$aOutput = array();
			foreach( $aFieldsInSections as $_sSectionID => $aSubSectionsOrFields ) {
				
				if ( ! isset( $aSections[ $_sSectionID ] ) ) continue;
				
				$_sSectionTabSlug = $aSections[ $_sSectionID ]['section_tab_slug'];	// will be referred outside the loop.
													
				// For repeatable sections
				$_aSubSections = $aSubSectionsOrFields;
				$_aSubSections = $this->getIntegerElements( $_aSubSections );
				$_iCountSubSections = count( $_aSubSections );	// Check sub-sections.
				if ( $_iCountSubSections ) {

					// Add the repeatable sections enabler script.
					if ( $aSections[ $_sSectionID ]['repeatable'] )
						$aOutput[] = $this->getRepeatableSectionsEnablerScript( 'sections-' .  md5( serialize( $aSections ) ), $_iCountSubSections, $aSections[ $_sSectionID ]['repeatable'] );	
					
					// Get the section tables.
					foreach( $this->numerizeElements( $_aSubSections ) as $_iIndex => $_aFields ) {		// will include the main section as well.
					
						$_sSectionTagID = 'section-' . $_sSectionID . '__' . $_iIndex;
						
						// For tabbed sections,
						if ( $aSections[ $_sSectionID ]['section_tab_slug'] )
							$_aSectionTabList[] = "<li class='admin-page-framework-section-tab nav-tab' id='section_tab-{$_sSectionTagID}'><a href='#{$_sSectionTagID}'>"
									. $this->_getSectionTitle( $aSections[ $_sSectionID ]['title'], 'h4', $_aFields, $hfFieldCallback )
								."</a></li>";
					
						$aOutput[] = $this->getFormTable( $_sSectionTagID, $_iIndex, $aSections[ $_sSectionID ], $_aFields, $hfSectionCallback, $hfFieldCallback );
						
					}
					
				} else {
				// The normal section
					$_sSectionTagID = 'section-' . $_sSectionID . '__' . '0';
					$_aFields = $aSubSectionsOrFields;
					
					// For tabbed sections,
					if ( $aSections[ $_sSectionID ]['section_tab_slug'] )
						$_aSectionTabList[] = "<li class='admin-page-framework-section-tab nav-tab' id='section_tab-{$_sSectionTagID}'><a href='#{$_sSectionTagID}'>"
								. $this->_getSectionTitle( $aSections[ $_sSectionID ]['title'], 'h4', $_aFields, $hfFieldCallback )	
							. "</a></li>";
					
					$aOutput[] = $this->getFormTable( $_sSectionTagID, 0, $aSections[ $_sSectionID ], $_aFields, $hfSectionCallback, $hfFieldCallback );
				}
					
			}

			// Return
			if ( empty( $aOutput ) ) return '';	// if empty, return a blank string.
			return 
				"<div " . $this->generateAttributes(
						array(
							'class'	=>	'admin-page-framework-sections'
								. ( ! $_sSectionTabSlug || $_sSectionTabSlug == '_default' ? '' : ' admin-page-framework-section-tabs-contents' ),
							'id'	=>	"sections-" . md5( serialize( $aSections ) ),
						)
					) . ">" 				
					. ( $_sSectionTabSlug	// if the section tab slug yields true, insert the section tab list
						? "<ul class='admin-page-framework-section-tabs nav-tab-wrapper'>" . implode( PHP_EOL, $_aSectionTabList ) . "</ul>"
						: ''
					)	
					. implode( PHP_EOL, $aOutput )
				. "</div>";
			
		}
		
		/**
		 * Returns the section title output.
		 * 
		 * @since			3.0.0
		 */
		private function _getSectionTitle( $sTitle, $sTag, $aFields, $hfFieldCallback ) {
			
			$aSectionTitleField = $this->_getSectionTitleField( $aFields );
			return $aSectionTitleField
				? call_user_func_array( $hfFieldCallback, array( $aSectionTitleField ) )
				: "<{$sTag}>" . $sTitle . "</{$sTag}>";
			
		}
		
		/**
		 * Returns the first found section_title field.
		 * 
		 * @since			3.0.0
		 */
		private function _getSectionTitleField( $aFields ) {
			
			foreach( $aFields as $aField )
				if ( $aField['type'] == 'section_title' )
					return $aField;	// will return the first found one.
			
		}
		
		/**
		 * Returns an array holding section definition array by section tab.
		 * 
		 * @since			3.0.0
		 */
		private function _getSectionsBySectionTabs( array $aSections ) {

			$_aSectionsBySectionTab = array();
			$iIndex = 0;
			// $_aSectionsBySectionTab = array( '_default' => array() );
			foreach( $aSections as $_aSection ) {
				
				if ( ! $_aSection['section_tab_slug'] ) {
					$_aSectionsBySectionTab[ '_default_' . $iIndex ][ $_aSection['section_id'] ] = $_aSection;
					$iIndex++;
					continue;
				}
					
				$_sSectionTaqbSlug = $_aSection['section_tab_slug'];
				$_aSectionsBySectionTab[ $_sSectionTaqbSlug ] = isset( $_aSectionsBySectionTab[ $_sSectionTaqbSlug ] ) && is_array( $_aSectionsBySectionTab[ $_sSectionTaqbSlug ] )
					? $_aSectionsBySectionTab[ $_sSectionTaqbSlug ]
					: array();
				
				$_aSectionsBySectionTab[ $_sSectionTaqbSlug ][ $_aSection['section_id'] ] = $_aSection;
				
			}
			return $_aSectionsBySectionTab;
			
		}

		/**
		 * Returns the enabler script for repeatable sections.
		 * @since			3.0.0
		 */
		private function getRepeatableSectionsEnablerScript( $sContainerTagID, $iSectionCount, $aSettings ) {
			
			add_action( 'admin_footer', array( $this, '_replyToAddRepeatableSectionjQueryPlugin' ) );
			
			if ( empty( $aSettings ) ) return '';			
			$aSettings = ( is_array( $aSettings ) ? $aSettings : array() ) + array( 'min' => 0, 'max' => 0 );	// do not cast array since it creates a zero key for an empty variable.
			
			
			$_sAdd = $this->oMsg->__( 'add_section' );
			$_sRemove = $this->oMsg->__( 'remove_section' );
			$_sVisibility = $iSectionCount <= 1 ? " style='display:none;'" : "";
			$_sSettingsAttributes = $this->generateDataAttributes( $aSettings );
			$_sButtons = 
				"<div class='admin-page-framework-repeatable-section-buttons' {$_sSettingsAttributes} >"
					. "<a class='repeatable-section-add button-secondary repeatable-section-button button button-large' href='#' title='{$_sAdd}' data-id='{$sContainerTagID}'>+</a>"
					. "<a class='repeatable-section-remove button-secondary repeatable-section-button button button-large' href='#' title='{$_sRemove}' {$_sVisibility} data-id='{$sContainerTagID}'>-</a>"
				. "</div>";
			$aJSArray = json_encode( $aSettings );
			return
				"<script type='text/javascript'>
					jQuery( document ).ready( function() {
						jQuery( '#{$sContainerTagID} .admin-page-framework-section-caption' ).show().prepend( \"{$_sButtons}\" );	// Adds the buttons
						jQuery( '#{$sContainerTagID}' ).updateAPFRepeatableSections( {$aJSArray} );	// Update the fields			
					});
				</script>";			
			
		}

		
	/**
	 * Returns a single HTML table output of a set of fields generated from the given field definition arrays.
	 * 
	 * @since			3.0.0
	 */
	public function getFormTable( $sSectionTagID, $iSectionIndex, $aSection, $aFields, $hfSectionCallback, $hfFieldCallback ) {

		if ( count( $aFields ) <= 0 ) return '';
		
		// For regular repeatable fields, the title should be omitted exept the first item.
		$_sDisplayNone = ( $aSection['repeatable'] && $iSectionIndex != 0 && ! $aSection['section_tab_slug'] )
			? " style='display:none;'"
			: '';
				
		$_sSectionError = isset( $this->aFieldErrors[ $aSection['section_id'] ] ) && is_string( $this->aFieldErrors[ $aSection['section_id'] ] )
			? $this->aFieldErrors[ $aSection['section_id'] ]
			: '';
				
		$_aOutput = array();
		$_aOutput[] = "<table "
			. $this->generateAttributes(  
					array( 
						'id' => 'section_table-' . $sSectionTagID,
						'class' => 'form-table',	// temporarily deprecated: admin-page-framework-section-table
					)
				)
			. ">"
				. ( $aSection['description'] || $aSection['title'] 
					? "<caption class='admin-page-framework-section-caption' data-section_tab='{$aSection['section_tab_slug']}'>"	// data-section_tab is referred by the repeater script to hide/show the title and the description
							. ( $aSection['title'] && ! $aSection['section_tab_slug']
								? "<div class='admin-page-framework-section-title' {$_sDisplayNone}>" 
										.  $this->_getSectionTitle( $aSection['title'], 'h3', $aFields, $hfFieldCallback )	
									. "</div>"
								: ""
							)					
							. ( is_callable( $hfSectionCallback )
								? "<div class='admin-page-framework-section-description'>" 	// admin-page-framework-section-description is referred by the repeatable section buttons
										. call_user_func_array( $hfSectionCallback, array( '<p>' . $aSection['description'] . '</p>', $aSection ) )
									. "</div>"
								: ""
							)
							. ( $_sSectionError  
								? "<div class='admin-page-framework-error'><span style='color:red;'>* " . $_sSectionError .  "</span></div>"
								: ''
							)
						. "</caption>"
					: "<caption class='admin-page-framework-section-caption' style='display:none;'></caption>"
				)
				. $this->getFieldRows( $aFields, $hfFieldCallback )
			. "</table>";
		return "<div "
			. $this->generateAttributes(
					array( 
						'id' => $sSectionTagID,	// section-{section id}__{index}
						'class' => 'admin-page-framework-section'
							. ( $aSection['section_tab_slug'] ? ' admin-page-framework-tab-content' : '' ),
					)				
				)
			. ">"
				. implode( PHP_EOL, $_aOutput )
			. "</div>";
		
	}

	/**
	 * Returns the output of a set of fields generated from the given field definition arrays enclosed in a table row tag for each.
	 * 
	 * @since			3.0.0	
	 */
	public function getFieldRows( $aFields, $hfCallback ) {
		
		if ( ! is_callable( $hfCallback ) ) return '';
		$aOutput = array();
		foreach( $aFields as $aField ) 
			$aOutput[] = $this->_getFieldRow( $aField, $hfCallback );
		return implode( PHP_EOL, $aOutput );
		
	}
		
		/**
		 * Returns the field output enclosed in a table row.
		 * 
		 * @since			3.0.0
		 */
		protected function _getFieldRow( $aField, $hfCallback ) {
			
			if ( $aField['type'] == 'section_title' ) return '';
			
			$aOutput = array();
			$_aField = $this->_mergeDefault( $aField );
			$_sAttributes_TR = $this->_getAttributes( 
				$_aField,
				array( 
					'id' => 'fieldrow-' . AdminPageFramework_FormField::_getInputTagID( $_aField ),
					'valign' => 'top',
					'class' => 'admin-page-framework-fieldrow',
				)
			);
			$_sAttributes_TD = $this->generateAttributes( 
				array(
					'colspan'	=>	$_aField['show_title_column'] ? 1 : 2,
					'class'		=>	$_aField['show_title_column'] ?	'' : 'admin-page-framework-field-td-no-title',
				)
			);
			$aOutput[] = "<tr {$_sAttributes_TR}>";
				if ( $_aField['show_title_column'] ) {
					$aOutput[] = "<th>" . $this->_getFieldTitle( $_aField ) . "</th>";
				}
				$aOutput[] = "<td {$_sAttributes_TD}>" . call_user_func_array( $hfCallback, array( $aField ) ) . "</td>";	// $aField is passed, not $_aField as $_aField do not respect subfields.
			$aOutput[] = "</tr>";
			return implode( PHP_EOL, $aOutput );
				
		}
	
	/**
	 * Returns a set of fields output from the given field definition array.
	 * 
	 * @remark			This is similar to getFieldRows() but without the enclosing table row tag. Used for taxonomy fields.
	 * @since			3.0.0
	 */
	public function getFields( $aFields, $hfCallback ) {
		
		if ( ! is_callable( $hfCallback ) ) return '';
		$aOutput = array();
		foreach( $aFields as $aField ) 
			$aOutput[] = $this->_getField( $aField, $hfCallback );
		return implode( PHP_EOL, $aOutput );
		
	}
	
		/**
		 * Returns the given field output without a table row tag.
		 * 
		 * @internal
		 * @since			3.0.0
		 */
		protected function _getField( $aField, $hfCallback )  {
			
			if ( $aField['type'] == 'section_title' ) return '';
			$aOutput = array();
			$_aField = $this->_mergeDefault( $aField );
			$aOutput[] = "<div " . $this->_getAttributes( $_aField ) . ">";
			if ( $_aField['show_title_column'] ) {
				$aOutput[] = $this->_getFieldTitle( $_aField );
			}
			$aOutput[] = call_user_func_array( $hfCallback, array( $aField ) );	// $aField is passed, not $_aField as $_aField do not respect subfields.
			$aOutput[] = "</div>";
			return implode( PHP_EOL, $aOutput );		
			
		}
			
}
endif;