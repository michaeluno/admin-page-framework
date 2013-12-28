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
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'taxonomy_slugs'					=> 'category',			// ( string ) This is for the taxonomy field type.
			'height'						=> '250px',				// for the taxonomy checklist field type, since 2.1.1.
			'sWidth'						=> '100%',				// for the taxonomy checklist field type, since 2.1.1.		
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 * 
	 * Returns the JavaScript script of the taxonomy field type.
	 * 
	 * @since			2.1.1
	 * @since			2.1.5			Moved from AdminPageFramework_Property_Base().
	 */ 
	public function replyToGetScripts() {
		return "
			jQuery( document ).ready( function() {
				jQuery( '.tab-box-container' ).each( function() {
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
			});
		";
	}
	
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetStyles() {
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
				position: relative; width: 100%; 

			}
			.admin-page-framework-field .tab-box-tabs li a { color: #333; text-decoration: none; }
			.admin-page-framework-field .tab-box-contents-container {  
				padding: 0 0 0 20px; 
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
	public function replyToGetInputIEStyles() {
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
	public function replyToGetField( $vValue, $aField, $aOptions, $aErrors, $aFieldDefinition ) {

		$aOutput = array();
		$field_name = $aField['field_name'];
		$tag_id = $aField['tag_id'];
		$field_class_selector = $aField['field_class_selector'];
		$_aDefaultKeys = $aFieldDefinition['aDefaultKeys'];	
		
		// $aFields = $aField['repeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $aField['label'];		
		
		$aTabs = array();
		$aCheckboxes = array();
		foreach( ( array ) $aField['taxonomy_slugs'] as $sKey => $sTaxonomySlug ) {
			$sActive = isset( $sActive ) ? '' : 'active';	// inserts the active class selector into the first element.
			$aTabs[] = 
				"<li class='tab-box-tab'>"
					. "<a href='#tab-{$sKey}'>"
						. "<span class='tab-box-tab-text'>" 
							. $this->getCorrespondingArrayValue( empty( $aField['label'] ) ? null : $aField['label'], $sKey, $this->getLabelFromTaxonomySlug( $sTaxonomySlug ) )
						. "</span>"
					."</a>"
				."</li>";
			$aCheckboxes[] = 
				"<div id='tab-{$sKey}' class='tab-box-content' style='height: {$aField['height']};'>"
					. "<ul class='list:category taxonomychecklist form-no-clear'>"
						. wp_list_categories( array(
							'walker' => new AdminPageFramework_WalkerTaxonomyChecklist,	// the walker class instance
							'name'     => is_array( $aField['taxonomy_slugs'] ) ? "{$field_name}[{$sKey}]" : "{$field_name}",   // name of the input
							'selected' => $this->getSelectedKeyArray( $vValue, $sKey ), 		// checked items ( term IDs )	e.g.  array( 6, 10, 7, 15 ), 
							'title_li'	=> '',	// disable the Categories heading string 
							'hide_empty' => 0,	
							'echo'	=> false,	// returns the output
							'taxonomy' => $sTaxonomySlug,	// the taxonomy slug (id) such as category and post_tag 
							'tag_id' => $tag_id,
						) )					
					. "</ul>"			
					. "<!--[if IE]><b>.</b><![endif]-->"
				. "</div>";
		}
		$sTabs = "<ul class='tab-box-tabs category-tabs'>" . implode( '', $aTabs ) . "</ul>";
		$sContents = 
			"<div class='tab-box-contents-container'>"
				. "<div class='tab-box-contents' style='height: {$aField['height']};'>"
					. implode( '', $aCheckboxes )
				. "</div>"
			. "</div>";
			
		$sOutput = 
			"<div id='{$tag_id}' class='{$field_class_selector} admin-page-framework-field-taxonomy tab-box-container categorydiv' style='max-width:{$aField['sWidth']};'>"
				. $sTabs . PHP_EOL
				. $sContents . PHP_EOL
			. "</div>";

		return $sOutput;

	}	
	
		/**
		 * A helper function for the above getTaxonomyChecklistField() method. 
		 * 
		 * @since			2.0.0
		 * @param			array			$vValue			This can be either an one-dimensional array ( for single field ) or a two-dimensional array ( for multiple fields ).
		 * @param			string			$sKey			
		 * @return			array			Returns an array consisting of keys whose value is true.
		 */ 
		private function getSelectedKeyArray( $vValue, $sKey ) {
					
			$vValue = ( array ) $vValue;	// cast array because the initial value (null) may not be an array.
			$iArrayDimension = $this->getArrayDimension( ( array ) $vValue );
					
			if ( $iArrayDimension == 1 )
				$aKeys = $vValue;
			else if ( $iArrayDimension == 2 )
				$aKeys = ( array ) $this->getCorrespondingArrayValue( $vValue, $sKey, false );
				
			return array_keys( $aKeys, true );
		
		}
	
		/**
		 * A helper function for the above getTaxonomyChecklistField() method.
		 * 
		 * @since			2.1.1
		 * 
		 */
		private function getLabelFromTaxonomySlug( $sTaxonomySlug ) {
			
			$oTaxonomy = get_taxonomy( $sTaxonomySlug );
			return isset( $oTaxonomy->label )
				? $oTaxonomy->label
				: null;
			
		}
	
}
endif;