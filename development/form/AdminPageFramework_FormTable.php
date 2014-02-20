<?php
if ( ! class_exists( 'AdminPageFramework_FormTable' ) ) :
/**
 * Provides methods to render setting fields.
 * 
 * @package			AdminPageFramework
 * @subpackage		Form
 * @since			3.0.0
 * @internal
 */
class AdminPageFramework_FormTable extends AdminPageFramework_WPUtility {
	
	public function __construct( $oMsg ) {
		
		$this->oMsg = $oMsg ? $oMsg: AdminPageFramework_Message::instantiate( '' );
		
	}
	
	/**
	 * Returns a set of HTML table outputs consisting of form sections and fields.
	 * 
	 * @since			3.0.0
	 */
	public function getFormTables( $aSections, $aFieldsInSections, $hfSectionCallback, $hfFieldCallback ) {
		
		$_aOutput = array();
		foreach( $this->_getSectionsBySectionTabs( $aSections ) as $_sSectionTabSlug => $_aSections ) {
			$_sSectionSet = $this->_getFormTablesBySectionTab( $_sSectionTabSlug, $_aSections, $aFieldsInSections, $hfFieldCallback );
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
		private function _getFormTablesBySectionTab( $sSectionTabSlug, $aSections, $aFieldsInSections, $hfFieldCallback ) {

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
					
						$aOutput[] = $this->getFormTable( $_sSectionTagID, $_iIndex, $aSections[ $_sSectionID ], $_aFields, $hfFieldCallback );
						
					}
					
				} else {
				// The normal section
					$_sSectionTagID = 'section-' . $_sSectionID . '__' . '0';
					
					// For tabbed sections,
					if ( $aSections[ $_sSectionID ]['section_tab_slug'] )
						$_aSectionTabList[] = "<li class='admin-page-framework-section-tab nav-tab' id='section_tab-{$_sSectionTagID}'><a href='#{$_sSectionTagID}'>{$_sSectionTitile}</a></li>";
					
					$_aFields = $aSubSectionsOrFields;
					$aOutput[] = $this->getFormTable( $_sSectionTagID, 0, $aSections[ $_sSectionID ], $_aFields, $hfFieldCallback );
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
		 * Returns the output of the head part of the section (title and the description)
		 * 
		 * @since			3.0.0
		 * @deprecated
		 */
		private function _getSectionHeader( $aSection ) {
						
			$aOutput = array();
			
			// If a section tab is specified
			if ( $aSection['section_tab_slug'] ) :
			
			
				return implode( PHP_EOL, $aOutput );
			endif;
			
			// Otherwise,
			$aOutput[] = $aSections['title'] ? "<h3 class='admin-page-framework-section-title'>" . $aSections['title'] . "</h3>" : '';
			$aOutput[] = $aSections['description'] ? "<p class='admin-page-framework-section-description'>" . $aSections['description'] . "</p>" : '';
			return implode( PHP_EOL, $aOutput );
			
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
	public function getFormTable( $sSectionTagID, $iSectionIndex, $aSection, $aFields, $hfFieldCallback ) {

		if ( count( $aFields ) <= 0 ) return '';
		
		$_bIsFirstItemOfRepeatableSection = ( $aSection['repeatable'] && $iSectionIndex == 0 );
		$_bIsFirstItemOfRepeatableSectionOfNonSectionTab = ( $_bIsFirstItemOfRepeatableSection && ! $aSection['section_tab_slug'] );
		
		$aOutput = array();
		$aOutput[] = "<table "
			. $this->generateAttributes(  
					array( 
						'id' => 'section_table-' . $sSectionTagID,
						'class' => 'form-table',	// temporarily deprecated: admin-page-framework-section-table
					)
				)
			. ">"
				. ( $aSection['description'] && $aSection['title'] && ( $aSection['section_tab_slug'] || $_bIsFirstItemOfRepeatableSectionOfNonSectionTab )
					? "<caption class='admin-page-framework-section-caption'>"
							. ( $aSection['title'] && ! $aSection['section_tab_slug']
								? "<h3 class='admin-page-framework-section-title'>" . $aSection['title'] . "</h3>"
								: ""
							)					
							. ( $aSection['description']	// admin-page-framework-section-description is referred by the repeatable section buttons
								? "<p class='admin-page-framework-section-description'>" . $aSection['description'] . "</p>"
								: ""
							)
						. "</caption>"
					: "<caption class='admin-page-framework-section-caption style='display:none;'></caption>"
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
					'id' => 'fieldrow-' . AdminPageFramework_InputField::_getInputTagID( $aField ),
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
		
		
	/*
	* Scripts etc.
	*/ 

	/**
	 * Returns the framework's repeatable field jQuery plugin.
	 * 
	 * @since			3.0.0
	 */
	public function _replyToAddRepeatableSectionjQueryPlugin() {
		
		static $bIsCalled = false;	// the static variable value will take effect even in other instances of the same class.
		
		if ( $bIsCalled ) return;
		$bIsCalled = true;
		
		$sCannotAddMore = $this->oMsg->__( 'allowed_maximum_number_of_sections' );
		$sCannotRemoveMore =  $this->oMsg->__( 'allowed_minimum_number_of_sections' );
		
		$sScript = "		
		(function ( $ ) {
		
			$.fn.updateAPFRepeatableSections = function( aSettings ) {
				
				var nodeThis = this;	// it can be from a sections container or a cloned section container.
				var sSectionsContainerID = nodeThis.find( '.repeatable-section-add' ).first().closest( '.admin-page-framework-sectionset' ).attr( 'id' );

				/* Store the sections specific options in an array  */
				if ( ! $.fn.aAPFRepeatableSectionsOptions ) $.fn.aAPFRepeatableSectionsOptions = [];
				if ( ! $.fn.aAPFRepeatableSectionsOptions.hasOwnProperty( sSectionsContainerID ) ) {		
					$.fn.aAPFRepeatableSectionsOptions[ sSectionsContainerID ] = $.extend({	
						max: 0,	// These are the defaults.
						min: 0,
						}, aSettings );
				}
				var aOptions = $.fn.aAPFRepeatableSectionsOptions[ sSectionsContainerID ];

				/* The Add button behavior - if the tag id is given, multiple buttons will be selected. 
				 * Otherwise, a section node is given and single button will be selected. */
				$( nodeThis ).find( '.repeatable-section-add' ).click( function() {
					$( this ).addAPFRepeatableSection();
					return false;	// will not click after that
				});
				
				/* The Remove button behavior */
				$( nodeThis ).find( '.repeatable-section-remove' ).click( function() {
					$( this ).removeAPFRepeatableSection();
					return false;	// will not click after that
				});		
				
				/* If the number of sections is less than the set minimum value, add sections. */
				var sSectionID = nodeThis.find( '.repeatable-section-add' ).first().closest( '.admin-page-framework-section' ).attr( 'id' );
				var nCurrentSectionCount = jQuery( '#' + sSectionsContainerID ).find( '.admin-page-framework-section' ).length;
				if ( aOptions['min'] > 0 && nCurrentSectionCount > 0 ) {
					if ( ( aOptions['min'] - nCurrentSectionCount ) > 0 ) {					
						$( '#' + sSectionID ).addAPFRepeatableSection( sSectionID );				 
					}
				}
				
			};
			
			/**
			 * Adds a repeatable section.
			 */
			$.fn.addAPFRepeatableSection = function( sSectionContainerID ) {
				if ( typeof sSectionContainerID === 'undefined' ) {
					var sSectionContainerID = $( this ).closest( '.admin-page-framework-section' ).attr( 'id' );	
				}

				var nodeSectionContainer = $( '#' + sSectionContainerID );
				var nodeNewSection = nodeSectionContainer.clone();	// clone without bind events.
				var nodeSectionsContainer = nodeSectionContainer.closest( '.admin-page-framework-sectionset' );
				var sSectionsContainerID = nodeSectionsContainer.attr( 'id' );
				var nodeTabs = $( '#' + sSectionContainerID ).closest( '.admin-page-framework-sectionset' ).find( '.admin-page-framework-section-tabs' );
				
				/* If the set maximum number of sections already exists, do not add */
				var sMaxNumberOfSections = $.fn.aAPFRepeatableSectionsOptions[ sSectionsContainerID ]['max'];
				if ( sMaxNumberOfSections != 0 && nodeSectionsContainer.find( '.admin-page-framework-section' ).length >= sMaxNumberOfSections ) {
					var nodeLastRepeaterButtons = nodeSectionContainer.find( '.admin-page-framework-repeatable-section-buttons' ).last();
					var sMessage = $( this ).formatPrintText( '{$sCannotAddMore}', sMaxNumberOfSections );
					var nodeMessage = $( '<span class=\"repeatable-section-error\" id=\"repeatable-section-error-' + sSectionsContainerID + '\" style=\"float:right;color:red;margin-left:1em;\">' + sMessage + '</span>' );
					if ( nodeSectionsContainer.find( '#repeatable-section-error-' + sSectionsContainerID ).length > 0 )
						nodeSectionsContainer.find( '#repeatable-section-error-' + sSectionsContainerID ).replaceWith( nodeMessage );
					else
						nodeLastRepeaterButtons.before( nodeMessage );
					nodeMessage.delay( 2000 ).fadeOut( 1000 );
					return;		
				}
				
				nodeNewSection.find( 'input:not([type=radio], [type=checkbox], [type=submit], [type=hidden]),textarea' ).val( '' );	// empty the value		
				nodeNewSection.find( '.repeatable-section-error' ).remove();	// remove error messages.
								
				/* Add the cloned new field element */
				nodeNewSection.insertAfter( nodeSectionContainer );	
				
				/* Increment the names and ids of the next following siblings. */
				nodeSectionContainer.nextAll().each( function() {
					$( this ).incrementIDAttribute( 'id', true );	// passing true in the second parameter means to apply the change to the first occurrence.
					$( this ).find( 'tr.admin-page-framework-fieldrow' ).incrementIDAttribute( 'id', true );
					$( this ).find( '.admin-page-framework-fieldset' ).incrementIDAttribute( 'id', true );
					$( this ).find( '.admin-page-framework-fieldset' ).incrementIDAttribute( 'data-field_id', true );	// don't remember what this data attribute was for
					$( this ).find( '.admin-page-framework-fields' ).incrementIDAttribute( 'id', true );
					$( this ).find( '.admin-page-framework-field' ).incrementIDAttribute( 'id', true );
					$( this ).find( '.repeatable-field-add' ).incrementIDAttribute( 'data-id', true );	// holds the fields container ID referred by the repeater field script.
					$( this ).find( 'label' ).incrementIDAttribute( 'for', true );	// passing true changes the first occurrence
					$( this ).find( 'input,textarea,select' ).incrementIDAttribute( 'id', true );
					$( this ).find( 'input,textarea,select' ).incrementNameAttribute( 'name', true );
				});
			
				/* Rebind the click event to the repeatable sections buttons - important to update AFTER inserting the clone to the document node since the update method need to count sections. 
				 * Also do this after updating the attributes since the script needs to check the last added id for repeatable section options such as 'min'
				 * */
				nodeNewSection.updateAPFRepeatableSections();	
				
				/* It seems radio buttons of the original field need to be reassigned. Otherwise, the checked items will be gone. */
				nodeSectionContainer.find( 'input[type=radio][checked=checked]' ).attr( 'checked', 'Checked' );	
	
				/* Iterate each field one by one */
				nodeNewSection.find( '.admin-page-framework-field' ).each( function() {	

					/* Rebind the click event to the repeatable field buttons - important to update AFTER inserting the clone to the document node since the update method need to count fields. */
					$( this ).updateAPFRepeatableFields();
												
					/* Call the registered callback functions */
					$( this ).callBackAddRepeatableField( $( this ).data( 'type' ), $( this ).attr( 'id' ) );
					
				});
				
				/* For tabbed sections - add the title tab list */
				if ( nodeTabs.length > 0 ) {
					var nodeNewTab = nodeTabs.find( '.admin-page-framework-section-tab' ).last().clone();
					nodeNewTab.removeClass( 'ui-state-active' );
					nodeNewTab.incrementIDAttribute( 'id' );
					nodeNewTab.find( 'a.ui-tabs-anchor' ).incrementIDAttribute( 'href' );
					nodeTabs.append( nodeNewTab );
					nodeTabs.closest( '.admin-page-framework-section-tabs-contents' ).tabs( 'refresh' );
				}				
				
				/* If more than one sections are created, show the Remove button */
				var nodeRemoveButtons =  nodeSectionsContainer.find( '.repeatable-section-remove' );
				if ( nodeRemoveButtons.length > 1 ) nodeRemoveButtons.show();				
									
				/* Return the newly created element */
				return nodeNewSection;	
				
			};
				
			$.fn.removeAPFRepeatableSection = function() {
				
				/* Need to remove the element: the secitons container */
				var nodeSectionContainer = $( this ).closest( '.admin-page-framework-section' );
				var sSectionConteinrID = nodeSectionContainer.attr( 'id' );
				var nodeSectionsContainer = $( this ).closest( '.admin-page-framework-sectionset' );
				var sSectionsContainerID = nodeSectionsContainer.attr( 'id' );
				var nodeTabs = nodeSectionsContainer.find( '.admin-page-framework-section-tabs' );
				
				/* If the set minimum number of sections already exists, do not remove */
				var sMinNumberOfSections = $.fn.aAPFRepeatableSectionsOptions[ sSectionsContainerID ]['min'];
				if ( sMinNumberOfSections != 0 && nodeSectionsContainer.find( '.admin-page-framework-section' ).length <= sMinNumberOfSections ) {
					var nodeLastRepeaterButtons = nodeSectionContainer.find( '.admin-page-framework-repeatable-section-buttons' ).last();
					var sMessage = $( this ).formatPrintText( '{$sCannotRemoveMore}', sMinNumberOfSections );
					var nodeMessage = $( '<span class=\"repeatable-section-error\" id=\"repeatable-section-error-' + sSectionsContainerID + '\" style=\"float:right;color:red;margin-left:1em;\">' + sMessage + '</span>' );
					if ( nodeSectionsContainer.find( '#repeatable-section-error-' + sSectionsContainerID ).length > 0 )
						nodeSectionsContainer.find( '#repeatable-section-error-' + sSectionsContainerID ).replaceWith( nodeMessage );
					else
						nodeLastRepeaterButtons.before( nodeMessage );
					nodeMessage.delay( 2000 ).fadeOut( 1000 );
					return;		
				}				
				
				/* Decrement the names and ids of the next following siblings. */
				nodeSectionContainer.nextAll().each( function() {
					$( this ).decrementIDAttribute( 'id' );					
					$( this ).find( 'tr.admin-page-framework-fieldrow' ).decrementIDAttribute( 'id', true );
					$( this ).find( '.admin-page-framework-fieldset' ).decrementIDAttribute( 'id', true );
					$( this ).find( '.admin-page-framework-fieldset' ).decrementIDAttribute( 'data-field_id', true );	// don't remember what this data attribute was for
					$( this ).find( '.admin-page-framework-fields' ).decrementIDAttribute( 'id', true );
					$( this ).find( '.admin-page-framework-field' ).decrementIDAttribute( 'id', true );
					$( this ).find( '.repeatable-field-add' ).decrementIDAttribute( 'data-id', true );	// holds the fields container ID referred by the repeater field script.
					$( this ).find( 'label' ).decrementIDAttribute( 'for', true );
					$( this ).find( 'input,textarea,select' ).decrementIDAttribute( 'id', true );
					$( this ).find( 'input,textarea,select' ).decrementNameAttribute( 'name', true );			
				});

				/* Call the registered callback functions */
				nodeSectionContainer.find( '.admin-page-framework-field' ).each( function() {	
					$( this ).callBackRemoveRepeatableField( $( this ).data( 'type' ), $( this ).attr( 'id' ) );
				});
			
				/* Remove the field */
				nodeSectionContainer.remove();
				
				/* For tabbed sections - remove the title tab list */
				if ( nodeTabs.length > 0 ) {
					nodeSelectionTab = nodeTabs.find( '#section_tab-' + sSectionConteinrID );
					nodeSelectionTab.nextAll().each( function() {
						$( this ).find( 'a.ui-tabs-anchor' ).decrementIDAttribute( 'href' );
					});					
					nodeSelectionTab.remove();
					nodeTabs.closest( '.admin-page-framework-section-tabs-contents' ).tabs( 'refresh' );
				}						
				
				/* Count the remaining Remove buttons and if it is one, disable the visibility of it */
				var nodeRemoveButtons = nodeSectionsContainer.find( '.repeatable-section-remove' );
				if ( nodeRemoveButtons.length == 1 ) nodeRemoveButtons.css( 'display', 'none' );
					
			};
				
		}( jQuery ));	
		";
		
		echo "<script type='text/javascript' class='admin-page-framework-repeatable-sections-plugin'>{$sScript}</script>";
	
	}		
	
}
endif;