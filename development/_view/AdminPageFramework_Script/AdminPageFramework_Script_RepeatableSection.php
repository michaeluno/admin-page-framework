<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides JavaScript utility scripts.
 * 
 * @since       3.0.0     
 * @since       3.3.0      Extends `AdminPageFramework_Script_Base`.
 * @package     AdminPageFramework
 * @subpackage  JavaScript
 * @internal
 */
class AdminPageFramework_Script_RepeatableSection extends AdminPageFramework_Script_Base {

    /**
     * Returns the script.
     * 
     * @since       3.0.0
     * @since       3.3.0       Changed the name from `getjQueryPlugin()`.
     */
    static public function getScript() {
        
        $_aParams           = func_get_args() + array( null );
        $_oMsg              = $_aParams[ 0 ];        
        $sCannotAddMore     = $_oMsg->get( 'allowed_maximum_number_of_sections' );
        $sCannotRemoveMore  = $_oMsg->get( 'allowed_minimum_number_of_sections' );
        
        return <<<JAVASCRIPTS
( function( $ ) {

    $.fn.updateAPFRepeatableSections = function( aSettings ) {
        
        var nodeThis = this; // it can be from a sections container or a cloned section container.
        var sSectionsContainerID = nodeThis.find( '.repeatable-section-add' ).first().closest( '.admin-page-framework-sectionset' ).attr( 'id' );

        /* Store the sections specific options in an array  */
        if ( ! $.fn.aAPFRepeatableSectionsOptions ) $.fn.aAPFRepeatableSectionsOptions = [];
        if ( ! $.fn.aAPFRepeatableSectionsOptions.hasOwnProperty( sSectionsContainerID ) ) {     
            $.fn.aAPFRepeatableSectionsOptions[ sSectionsContainerID ] = $.extend({    
                max: 0, // These are the defaults.
                min: 0,
                }, aSettings );
        }
        var aOptions = $.fn.aAPFRepeatableSectionsOptions[ sSectionsContainerID ];

        /* The Add button behavior - if the tag id is given, multiple buttons will be selected. 
         * Otherwise, a section node is given and single button will be selected. */
        $( nodeThis ).find( '.repeatable-section-add' ).click( function() {
            $( this ).addAPFRepeatableSection();
            return false; // will not click after that
        });
        
        /* The Remove button behavior */
        $( nodeThis ).find( '.repeatable-section-remove' ).click( function() {
            $( this ).removeAPFRepeatableSection();
            return false; // will not click after that
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
        var nodeNewSection = nodeSectionContainer.clone(); // clone without bind events.
        var nodeSectionsContainer = nodeSectionContainer.closest( '.admin-page-framework-sectionset' );
        var sSectionsContainerID = nodeSectionsContainer.attr( 'id' );
        var nodeTabsContainer = $( '#' + sSectionContainerID ).closest( '.admin-page-framework-sectionset' ).find( '.admin-page-framework-section-tabs' );

        /* If the set maximum number of sections already exists, do not add */
        var sMaxNumberOfSections = $.fn.aAPFRepeatableSectionsOptions[ sSectionsContainerID ]['max'];
        if ( sMaxNumberOfSections != 0 && nodeSectionsContainer.find( '.admin-page-framework-section' ).length >= sMaxNumberOfSections ) {
            var nodeLastRepeaterButtons = nodeSectionContainer.find( '.admin-page-framework-repeatable-section-buttons' ).last();
            var sMessage = $( this ).formatPrintText( '{$sCannotAddMore}', sMaxNumberOfSections );
            var nodeMessage = $( '<span class=\"repeatable-section-error\" id=\"repeatable-section-error-' + sSectionsContainerID + '\">' + sMessage + '</span>' );
            if ( nodeSectionsContainer.find( '#repeatable-section-error-' + sSectionsContainerID ).length > 0 )
                nodeSectionsContainer.find( '#repeatable-section-error-' + sSectionsContainerID ).replaceWith( nodeMessage );
            else
                nodeLastRepeaterButtons.before( nodeMessage );
            nodeMessage.delay( 2000 ).fadeOut( 1000 );
            return;     
        }
        
        nodeNewSection.find( 'input:not([type=radio], [type=checkbox], [type=submit], [type=hidden]),textarea' ).val( '' ); // empty the value     
        nodeNewSection.find( '.repeatable-section-error' ).remove(); // remove error messages.
        
        /* If this is not for tabbed sections, do not show the title */
        var sSectionTabSlug = nodeNewSection.find( '.admin-page-framework-section-caption' ).first().attr( 'data-section_tab' );
        if ( ! sSectionTabSlug || sSectionTabSlug === '_default' ) {
            nodeNewSection.find( '.admin-page-framework-section-title' ).not( '.admin-page-framework-collapsible-section-title' ).hide();
        }
        // Bind the click event to the collapsible section(s) bar. If a collapsible section is not added, the jQuery plugin is not added.
        if( 'function' === typeof nodeNewSection.enableAPFCollapsibleButton ){ 
            nodeNewSection.find( '.admin-page-framework-collapsible-sections-title, .admin-page-framework-collapsible-section-title' ).enableAPFCollapsibleButton();
        }
                        
        /* Add the cloned new field element */        
        nodeNewSection.insertAfter( nodeSectionContainer );    

        /* It seems radio buttons of the original field need to be reassigned. Otherwise, the checked items will be gone. */
        nodeSectionContainer.find( 'input[type=radio][checked=checked]' ).attr( 'checked', 'checked' );    
        
        /* Iterate each section and increment the names and ids of the next following siblings. */
        nodeSectionContainer.nextAll().each( function( iSectionIndex ) {
            
            incrementAttributes( this );
            
            /* Iterate each field one by one */
            $( this ).find( '.admin-page-framework-field' ).each( function( iFieldIndex ) {    
            
                /* Rebind the click event to the repeatable field buttons - important to update AFTER inserting the clone to the document node since the update method need to count fields. */
                $( this ).updateAPFRepeatableFields();
                                            
                /* Call the registered callback functions */
                $( this ).callBackAddRepeatableField( $( this ).data( 'type' ), $( this ).attr( 'id' ), 1, iSectionIndex, iFieldIndex );
                
            });     
            
        });
    
        /* Rebind the click event to the repeatable sections buttons - important to update AFTER inserting the clone to the document node since the update method need to count sections. 
         * Also do this after updating the attributes since the script needs to check the last added id for repeatable section options such as 'min'
         * */
        nodeNewSection.updateAPFRepeatableSections();    
        
        /* Rebind sortable fields - iterate sortable fields containers */
        nodeNewSection.find( '.admin-page-framework-fields.sortable' ).each( function() {
            $( this ).enableAPFSortable();
        });
        
        /* For tabbed sections - add the title tab list */
        if ( nodeTabsContainer.length > 0 && ! nodeSectionContainer.hasClass( 'is_subsection_collapsible' ) ) {
            
            /* The clicked(copy source) section tab */
            var nodeTab = nodeTabsContainer.find( '#section_tab-' + sSectionContainerID );
            var nodeNewTab = nodeTab.clone();
            
            nodeNewTab.removeClass( 'active' );
            nodeNewTab.find( 'input:not([type=radio], [type=checkbox], [type=submit], [type=hidden]),textarea' ).val( '' ); // empty the value
        
            /* Add the cloned new field tab */
            nodeNewTab.insertAfter( nodeTab );    
            
            /* Increment the names and ids of the next following siblings. */
            nodeTab.nextAll().each( function() {
                incrementAttributes( this );
                $( this ).find( 'a.anchor' ).incrementIDAttribute( 'href' );
            });     
            
            nodeTabsContainer.closest( '.admin-page-framework-section-tabs-contents' ).createTabs( 'refresh' );
        }     
        
        /* If more than one sections are created, show the Remove button */
        var nodeRemoveButtons =  nodeSectionsContainer.find( '.repeatable-section-remove' );
        if ( nodeRemoveButtons.length > 1 ) nodeRemoveButtons.show();     
                            
        /* Return the newly created element */
        return nodeNewSection;    
        
    };    
    // Local function literal
    var incrementAttributes = function( oElement, iOccurrence ) {
        
        var iOccurrence = 'undefined' !== typeof iOccurrence ? iOccurrence : 1;
        $( oElement ).incrementIDAttribute( 'id', iOccurrence ); // passing 1 in the second parameter means to apply the change to the first occurrence.
        $( oElement ).find( 'tr.admin-page-framework-fieldrow' ).incrementIDAttribute( 'id', iOccurrence );
        $( oElement ).find( '.admin-page-framework-fieldset' ).incrementIDAttribute( 'id', iOccurrence );
        $( oElement ).find( '.admin-page-framework-fieldset' ).incrementIDAttribute( 'data-field_id', iOccurrence ); // I don't remember what this data attribute was for...
        $( oElement ).find( '.admin-page-framework-fields' ).incrementIDAttribute( 'id', iOccurrence );
        $( oElement ).find( '.admin-page-framework-field' ).incrementIDAttribute( 'id', iOccurrence );
        $( oElement ).find( 'table.form-table' ).incrementIDAttribute( 'id', iOccurrence );
        $( oElement ).find( '.repeatable-field-add' ).incrementIDAttribute( 'data-id', iOccurrence ); // holds the fields container ID referred by the repeater field script.
        $( oElement ).find( 'label' ).incrementIDAttribute( 'for', iOccurrence );    
        $( oElement ).find( 'input,textarea,select' ).incrementIDAttribute( 'id', iOccurrence );
        $( oElement ).find( 'input,textarea,select' ).incrementNameAttribute( 'name', iOccurrence );     
        
    }     
        
    $.fn.removeAPFRepeatableSection = function() {
        
        /* Need to remove the element: the secitons container */
        var nodeSectionContainer = $( this ).closest( '.admin-page-framework-section' );
        var sSectionConteinrID = nodeSectionContainer.attr( 'id' );
        var nodeSectionsContainer = $( this ).closest( '.admin-page-framework-sectionset' );
        var sSectionsContainerID = nodeSectionsContainer.attr( 'id' );
        var nodeTabsContainer = nodeSectionsContainer.find( '.admin-page-framework-section-tabs' );
        var nodeTabs = nodeTabsContainer.find( '.admin-page-framework-section-tab' );
        
        /* If the set minimum number of sections already exists, do not remove */
        var sMinNumberOfSections = $.fn.aAPFRepeatableSectionsOptions[ sSectionsContainerID ]['min'];
        if ( sMinNumberOfSections != 0 && nodeSectionsContainer.find( '.admin-page-framework-section' ).length <= sMinNumberOfSections ) {
            var nodeLastRepeaterButtons = nodeSectionContainer.find( '.admin-page-framework-repeatable-section-buttons' ).last();
            var sMessage = $( this ).formatPrintText( '{$sCannotRemoveMore}', sMinNumberOfSections );
            var nodeMessage = $( '<span class=\"repeatable-section-error\" id=\"repeatable-section-error-' + sSectionsContainerID + '\">' + sMessage + '</span>' );
            if ( nodeSectionsContainer.find( '#repeatable-section-error-' + sSectionsContainerID ).length > 0 )
                nodeSectionsContainer.find( '#repeatable-section-error-' + sSectionsContainerID ).replaceWith( nodeMessage );
            else
                nodeLastRepeaterButtons.before( nodeMessage );
            nodeMessage.delay( 2000 ).fadeOut( 1000 );
            return;     
        }     
        
        /** 
         * Call the registered callback functions
         * 
         * @since 3.0.0
         * @since 3.1.6 Changed it to do after removing the element.
         */                
        var oNextAllSections = nodeSectionContainer.nextAll();
        var _bIsSubsectionCollapsible  = nodeSectionContainer.hasClass( 'is_subsection_collapsible' );
        
        /* Remove the section */
        nodeSectionContainer.remove();
        
        /* Decrement the names and ids of the next following siblings. */
        oNextAllSections.each( function( iSectionIndex ) {
            
            decrementAttributes( this );
            
            /* Call the registered callback functions */
            $( this ).find( '.admin-page-framework-field' ).each( function( iFieldIndex ) {    
                $( this ).callBackRemoveRepeatableField( $( this ).data( 'type' ), $( this ).attr( 'id' ), 1, iSectionIndex, iFieldIndex );
            });     
            
        });
        
        /* For tabbed sections - remove the title tab list */
        if ( nodeTabsContainer.length > 0 && nodeTabs.length > 1 && ! _bIsSubsectionCollapsible ) {
            nodeSelectionTab = nodeTabsContainer.find( '#section_tab-' + sSectionConteinrID );
            nodeSelectionTab.nextAll().each( function() {
                $( this ).find( 'a.anchor' ).decrementIDAttribute( 'href' );
                decrementAttributes( this );
            });    
            
            if (  nodeSelectionTab.prev().length )
                nodeSelectionTab.prev().addClass( 'active' );
            else
                nodeSelectionTab.next().addClass( 'active' );
                
            nodeSelectionTab.remove();
            nodeTabsContainer.closest( '.admin-page-framework-section-tabs-contents' ).createTabs( 'refresh' );
        }     
        
        /* Count the remaining Remove buttons and if it is one, disable the visibility of it */
        var nodeRemoveButtons = nodeSectionsContainer.find( '.repeatable-section-remove' );
        if ( 1 === nodeRemoveButtons.length ) {
            nodeRemoveButtons.css( 'display', 'none' );
            
            /* Also if this is not for tabbed sections, do show the title */
            var sSectionTabSlug = nodeSectionsContainer.find( '.admin-page-framework-section-caption' ).first().attr( 'data-section_tab' );
            if ( ! sSectionTabSlug || sSectionTabSlug === '_default' ) 
                nodeSectionsContainer.find( '.admin-page-framework-section-title' ).first().show();
            
        }
            
    };
    // Local function literal
    var decrementAttributes = function( oElement, iOccurrence ) {
        
        var iOccurrence = 'undefined' !== typeof iOccurrence ? iOccurrence : 1;
        $( oElement ).decrementIDAttribute( 'id' );     
        $( oElement ).find( 'tr.admin-page-framework-fieldrow' ).decrementIDAttribute( 'id', iOccurrence );
        $( oElement ).find( '.admin-page-framework-fieldset' ).decrementIDAttribute( 'id', iOccurrence );
        $( oElement ).find( '.admin-page-framework-fieldset' ).decrementIDAttribute( 'data-field_id', iOccurrence ); // I don't remember what this data attribute was for...
        $( oElement ).find( '.admin-page-framework-fields' ).decrementIDAttribute( 'id', iOccurrence );
        $( oElement ).find( '.admin-page-framework-field' ).decrementIDAttribute( 'id', iOccurrence );
        $( oElement ).find( 'table.form-table' ).decrementIDAttribute( 'id', iOccurrence );
        $( oElement ).find( '.repeatable-field-add' ).decrementIDAttribute( 'data-id', iOccurrence ); // holds the fields container ID referred by the repeater field script.
        $( oElement ).find( 'label' ).decrementIDAttribute( 'for', iOccurrence );
        $( oElement ).find( 'input,textarea,select' ).decrementIDAttribute( 'id', iOccurrence );
        $( oElement ).find( 'input,textarea,select' ).decrementNameAttribute( 'name', iOccurrence );     
        
    }    
    
}( jQuery ));
JAVASCRIPTS;
    }
}