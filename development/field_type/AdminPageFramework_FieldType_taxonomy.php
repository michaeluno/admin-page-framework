<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_taxonomy' ) ) :
/**
 * Defines the taxonomy field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
class AdminPageFramework_FieldType_taxonomy extends AdminPageFramework_FieldType_Base {
	
	/**
	 * Defines the field type slugs used for this field type.
	 */
	public $aFieldTypeSlugs = array( 'taxonomy', );
	
	/**
	 * Defines the default key-values of this field type. 
	 * 
	 * @remark			$_aDefaultKeys holds shared default key-values defined in the base class.
	 */
	protected $aDefaultKeys = array(
		'taxonomy_slugs'				=> 'category',			// ( array|string ) This is for the taxonomy field type.
		'height'						=> '250px',				// 
		'max_width'						=> '100$',				// for the taxonomy checklist field type, since 2.1.1.		
		'attributes'	=> array(
		),	
	);
	
	/**
	 * Loads the field type necessary components.
	 */ 
	public function _replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 * 
	 * Returns the JavaScript script of the taxonomy field type.
	 * 
	 * @since			2.1.1
	 * @since			2.1.5			Moved from AdminPageFramework_Property_Base().
	 */ 
	public function _replyToGetScripts() {
		
		$aJSArray = json_encode( $this->aFieldTypeSlugs );
		return "	
			jQuery( document ).ready( function() {
				/* For tabs */
				var enableAPFTabbedBox = function( nodeTabBoxContainer ) {
					jQuery( nodeTabBoxContainer ).each( function() {
						jQuery( this ).find( '.tab-box-tab' ).each( function( i ) {
							
							if ( i == 0 )
								jQuery( this ).addClass( 'active' );
								
							jQuery( this ).click( function( e ){
									 
								// Prevents jumping to the anchor which moves the scroll bar.
								e.preventDefault();
								
								// Remove the active tab and set the clicked tab to be active.
								jQuery( this ).siblings( 'li.active' ).removeClass( 'active' );
								jQuery( this ).addClass( 'active' );
								
								// Find the element id and select the content element with it.
								var thisTab = jQuery( this ).find( 'a' ).attr( 'href' );
								active_content = jQuery( this ).closest( '.tab-box-container' ).find( thisTab ).css( 'display', 'block' ); 
								active_content.siblings().css( 'display', 'none' );
								
							});
						});		
					});
				}		
				enableAPFTabbedBox( jQuery( '.tab-box-container' ) );

				/*	The repeatable event */
				jQuery().registerAPFCallback( {				
					added_repeatable_field: function( node, sFieldType, sFieldTagID ) {
			
						/* If it is not the color field type, do nothing. */
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;
						
						var fIncrementOrDecrement = 1;
						var updateID = function( index, name ) {
							
							if ( typeof name === 'undefined' ) {
								return name;
							}
							return name.replace( /_((\d+))(?=(_|$))/, function ( fullMatch, n ) {						
								return '_' + ( Number(n) + ( fIncrementOrDecrement == 1 ? 1 : -1 ) );
							});
							
						}
						var updateName = function( index, name ) {
							
							if ( typeof name === 'undefined' ) {
								return name;
							}
							return name.replace( /\[((\d+))(?=\])/, function ( fullMatch, n ) {				
								return '[' + ( Number(n) + ( fIncrementOrDecrement == 1 ? 1 : -1 ) );
							});
							
						}
						node.find( 'div' ).attr( 'id', function( index, name ){ return updateID( index, name ) } );
						node.find( 'li.tab-box-tab a' ).attr( 'href', function( index, name ){ return updateID( index, name ) } );
						
						enableAPFTabbedBox( node.find( '.tab-box-container' ) );
						
					}
				});
			});			
		";
	}
	
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function _replyToGetStyles() {
		return 
		"/* Taxonomy Field Type */
			.admin-page-framework-field .taxonomy-checklist li { 
				margin: 8px 0 8px 20px; 
			}
			.admin-page-framework-field div.taxonomy-checklist {
				padding: 8px 0 8px 10px;
				margin-bottom: 20px;
			}
			.admin-page-framework-field .taxonomy-checklist ul {
				list-style-type: none;
				margin: 0;
			}
			.admin-page-framework-field .taxonomy-checklist ul ul {
				margin-left: 1em;
			}
			.admin-page-framework-field .taxonomy-checklist-label {
				/* margin-left: 0.5em; */
			}		
		/* Tabbed box */
			.admin-page-framework-field .tab-box-container.categorydiv {
				max-height: none;
			}
			.admin-page-framework-field .tab-box-tab-text {
				display: inline-block;
			}
			.admin-page-framework-field .tab-box-tabs {
				line-height: 12px;
				margin-bottom: 0;
			
			}
			.admin-page-framework-field .tab-box-tabs .tab-box-tab.active {
				display: inline;
				border-color: #dfdfdf #dfdfdf #fff;
				margin-bottom: 0;
				padding-bottom: 1px;
				background-color: #fff;
			}
			.admin-page-framework-field .tab-box-container { 
				position: relative; 
				width: 100%; 
				clear: both;
				margin-bottom: 1em;
			}
			.admin-page-framework-field .tab-box-tabs li a { color: #333; text-decoration: none; }
			.admin-page-framework-field .tab-box-contents-container {  
				padding: 0 2em 0 1.8em;
				border: 1px solid #dfdfdf; 
				background-color: #fff;
			}
			.admin-page-framework-field .tab-box-contents { 
				overflow: hidden; 
				overflow-x: hidden; 
				position: relative; 
				top: -1px; 
				height: 300px;  
			}
			.admin-page-framework-field .tab-box-content { 
				height: 300px;
				display: none; 
				overflow: auto; 
				display: block; 
				position: relative; 
				overflow-x: hidden;
			}
			.admin-page-framework-field .tab-box-content:target, 
			.admin-page-framework-field .tab-box-content:target, 
			.admin-page-framework-field .tab-box-content:target { 
				display: block; 
			}			
		" . PHP_EOL;
	}
	
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function _replyToGetInputIEStyles() {
		return 	".tab-box-content { display: block; }
			.tab-box-contents { overflow: hidden;position: relative; }
			b { position: absolute; top: 0px; right: 0px; width:1px; height: 251px; overflow: hidden; text-indent: -9999px; }
		";	

	}	
	
	/**
	 * Returns the output of the field type.
	 * 
	 * Returns the output of taxonomy checklist check boxes.
	 * 
	 * @remark			Multiple fields are not supported.
	 * @remark			Repeater fields are not supported.
	 * @since			2.0.0
	 * @since			2.1.1			The checklist boxes are rendered in a tabbed single box.
	 * @since			2.1.5			Moved from AdminPageFramework_InputField.
	 */
	public function _replyToGetField( $aField ) {

		$aTabs = array();
		$aCheckboxes = array();
		foreach( ( array ) $aField['taxonomy_slugs'] as $sKey => $sTaxonomySlug ) {
			
			$aInputAttributes = isset( $aField['attributes'][ $sKey ] ) && is_array( $aField['attributes'][ $sKey ] )
				? $aField['attributes'][ $sKey ] + $aField['attributes']
				: $aField['attributes'];
				
			$aTabs[] = 
				"<li class='tab-box-tab'>"
					. "<a href='#tab_{$aField['input_id']}_{$sKey}'>"
						. "<span class='tab-box-tab-text'>" 
							. $this->_getLabelFromTaxonomySlug( $sTaxonomySlug )
						. "</span>"
					."</a>"
				."</li>";
			$aCheckboxes[] = 
				"<div id='tab_{$aField['input_id']}_{$sKey}' class='tab-box-content' style='height: {$aField['height']};'>"
					. $this->getFieldElementByKey( $aField['before_label'], $sKey )
					. "<ul class='list:category taxonomychecklist form-no-clear'>"
						. wp_list_categories( array(
							'walker' => new AdminPageFramework_WalkerTaxonomyChecklist,	// the walker class instance
							'name'     => is_array( $aField['taxonomy_slugs'] ) ? "{$aField['field_name']}[{$sTaxonomySlug}]" : $aField['field_name'],   // name of the input
							'selected' => $this->_getSelectedKeyArray( $aField['value'], $sKey ), 		// checked items ( term IDs )	e.g.  array( 6, 10, 7, 15 ), 
							'title_li'	=> '',	// disable the Categories heading string 
							'hide_empty' => 0,	
							'echo'	=> false,	// returns the output
							'taxonomy' => $sTaxonomySlug,	// the taxonomy slug (id) such as category and post_tag 
							'input_id' => $aField['input_id'],
							'attributes'	=> $aInputAttributes,
						) )					
					. "</ul>"			
					. "<!--[if IE]><b>.</b><![endif]-->"
					. $this->getFieldElementByKey( $aField['after_label'], $sKey )
				. "</div>";
		}

		$sTabs = "<ul class='tab-box-tabs category-tabs'>" . implode( PHP_EOL, $aTabs ) . "</ul>";
		$sContents = 
			"<div class='tab-box-contents-container'>"
				. "<div class='tab-box-contents' style='height: {$aField['height']};'>"
					. implode( PHP_EOL, $aCheckboxes )
				. "</div>"
			. "</div>";
			
		return ''
			// ( is_array( $aField['before_label'] ) ? '' : ( string ) $aField['before_label'] )
			. "<div id='tabbox-{$aField['field_id']}' class='tab-box-container categorydiv' style='max-width:{$aField['max_width']};'>"
				. $sTabs . PHP_EOL
				. $sContents . PHP_EOL
			. "</div>"
			// . ( is_array( $aField['after_label'] ) ? '' : ( string ) $aField['after_label'] )
			;
				
	}
	
		/**
		 * Returns an array consisting of keys whose value is true.
		 * 
		 * A helper function for the above getTaxonomyChecklistField() method. 
		 * 
		 * @since			2.0.0
		 * @param			array			$vValue			This can be either an one-dimensional array ( for single field ) or a two-dimensional array ( for multiple fields ).
		 * @param			string			$sKey			
		 * @return			array			Returns an array consisting of keys whose value is true.
		 */ 
		private function _getSelectedKeyArray( $vValue, $sKey ) {
					
			$vValue = ( array ) $vValue;	// cast array because the initial value (null) may not be an array.
			$iArrayDimension = $this->getArrayDimension( $vValue );
					
			if ( $iArrayDimension == 1 )
				$aKeys = $vValue;
			else if ( $iArrayDimension == 2 )
				$aKeys = ( array ) $this->getCorrespondingArrayValue( $vValue, $sKey, false );

			return array_keys( $aKeys, true );
		
		}
	
		/**
		 * Retrieves the label of the given taxonomy by its slug.
		 * 
		 * A helper function for the above getTaxonomyChecklistField() method.
		 * 
		 * @since			2.1.1
		 * 
		 */
		private function _getLabelFromTaxonomySlug( $sTaxonomySlug ) {
			
			$oTaxonomy = get_taxonomy( $sTaxonomySlug );
			return isset( $oTaxonomy->label )
				? $oTaxonomy->label
				: null;
			
		}
	
}
endif;