<?php
if ( ! class_exists( 'AdminPageFramework_FormTable_Base' ) ) :
/**
 * The base class of the form table class that provides methods to render setting sections and fields.
 * 
 * @package			AdminPageFramework
 * @subpackage		Form
 * @since			3.0.0
 * @internal
 */
class AdminPageFramework_FormTable_Base extends AdminPageFramework_WPUtility {
	
	public function __construct( $oMsg ) {
		
		$this->oMsg = $oMsg ? $oMsg: AdminPageFramework_Message::instantiate( '' );
		
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