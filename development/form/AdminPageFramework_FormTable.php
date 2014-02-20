<?php
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
			
			wp_enqueue_script( 'jquery-ui-tabs' );
			return "<script type='text/javascript'>
				jQuery( document ).ready( function() {
					jQuery( '.admin-page-framework-section-tabs-contents' ).tabs();
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
				
				$_sSectionTitile = "<h4>" . $aSections[ $_sSectionID ]['title'] . "</h4>";
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
							$_aSectionTabList[] = "<li class='admin-page-framework-section-tab nav-tab' id='section_tab-{$_sSectionTagID}'><a href='#{$_sSectionTagID}'>{$_sSectionTitile}</a></li>";
					
						$aOutput[] = $this->getFormTable( $_sSectionTagID, $_iIndex, $aSections[ $_sSectionID ], $_aFields, $hfSectionCallback, $hfFieldCallback );
						
					}
					
				} else {
				// The normal section
					$_sSectionTagID = 'section-' . $_sSectionID . '__' . '0';
					
					// For tabbed sections,
					if ( $aSections[ $_sSectionID ]['section_tab_slug'] )
						$_aSectionTabList[] = "<li class='admin-page-framework-section-tab nav-tab' id='section_tab-{$_sSectionTagID}'><a href='#{$_sSectionTagID}'>{$_sSectionTitile}</a></li>";
					
					$_aFields = $aSubSectionsOrFields;
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
		 * Returns an array holding section definition array by section tab.
		 * 
		 * @since			3.0.0
		 */
		private function _getSectionsBySectionTabs( array $aSections ) {

			$_aSectionsBySectionTab = array( '_default' => array() );
			foreach( $aSections as $_aSection ) {
				
				if ( ! $_aSection['section_tab_slug'] ) {
					$_aSectionsBySectionTab[ '_default' ][ $_aSection['section_id'] ] = $_aSection;
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
				
		$aOutput = array();
		$aOutput[] = "<table "
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
								? "<h3 class='admin-page-framework-section-title' {$_sDisplayNone}>" . $aSection['title'] . "</h3>"
								: ""
							)					
							. ( $aSection['description']	// admin-page-framework-section-description is referred by the repeatable section buttons
								? "<div class='admin-page-framework-section-description'>" . call_user_func_array( $hfSectionCallback, array( '<p>' . $aSection['description'] . '</p>', $aSection ) ) . "</div>"								
								: ""
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
				. implode( PHP_EOL, $aOutput )
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
			$aOutput[] = $this->getFieldRow( $aField, $hfCallback );
		return implode( PHP_EOL, $aOutput );
		
	}
		
		/**
		 * Returns the field output enclosed in a table row.
		 * 
		 * @since			3.0.0
		 */
		protected function getFieldRow( $aField, $hfCallback ) {
			
			$aOutput = array();
			$_sAttributes = $this->getAttributes( 
				$aField,
				array( 
					'id' => 'fieldrow-' . AdminPageFramework_FormField::_getInputTagID( $aField ),
					'valign' => 'top',
					'class' => 'admin-page-framework-fieldrow',
				)
			);
			$aOutput[] = "<tr {$_sAttributes}>";
				if ( $aField['show_title_column'] )
					$aOutput[] = "<th>" . $this->getFieldTitle( $aField ) . "</th>";
				$aOutput[] = "<td>" . call_user_func_array( $hfCallback, array( $aField ) ) . "</td>";
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
			$aOutput[] = $this->getField( $aField, $hfCallback );
		return implode( PHP_EOL, $aOutput );
		
	}
	
		/**
		 * Returns the given field output without a table row tag.
		 * @since			3.0.0
		 */
		protected function getField( $aField, $hfCallback )  {
			
			$aOutput = array();
			$aOutput[] = "<div " . $this->getAttributes( $aField ) . ">";
			if ( $aField['show_title_column'] )
				$aOutput[] = $this->getFieldTitle( $aField );
			$aOutput[] = call_user_func_array( $hfCallback, array( $aField ) );
			$aOutput[] = "</div>";
			return implode( PHP_EOL, $aOutput );		
			
		}
	
		/**
		 * Generates attributes of the field container tag.
		 * 
		 * @since			3.0.0
		 */
		protected function getAttributes( $aField, $aAttributes=array() ) {
			
			$_aAttributes = $aAttributes + ( isset( $aField['attributes']['fieldrow'] ) ? $aField['attributes']['fieldrow'] : array() );
			
			if ( $aField['hidden'] )	// Prepend the visibility CSS property.
				$_aAttributes['style'] = 'display:none;' . ( isset( $_aAttributes['style'] ) ? $_aAttributes['style'] : '' );
			
			return $this->generateAttributes( $_aAttributes );
			
		}
		
		/**
		 * Returns the title part of the field output.
		 * 
		 * @since			3.0.0
		 */
		protected function getFieldTitle( $aField ) {
			
			return "<label for='{$aField['field_id']}'>"
				. "<a id='{$aField['field_id']}'></a>"
					. "<span title='" . ( strip_tags( isset( $aField['tip'] ) ? $aField['tip'] : $aField['description'] ) ) . "'>"
						. $aField['title'] 
					. "</span>"
				. "</label>";
		
			
		}
			
}
endif;