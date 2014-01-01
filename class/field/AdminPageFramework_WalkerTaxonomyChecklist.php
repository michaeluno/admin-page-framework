<?php
if ( ! class_exists( 'AdminPageFramework_WalkerTaxonomyChecklist' ) ) :
/**
 * Provides methods for rendering taxonomy check lists.
 * 
 * Used for the wp_list_categories() function to render category hierarchical checklist.
 * 
 * @see				Walker : wp-includes/class-wp-walker.php
 * @see				Walker_Category : wp-includes/category-template.php
 * @since			2.0.0
 * @since			2.1.5			Added the tag_id key to the argument array. Changed the format of 'id' and 'for' attribute of the input and label tags.
 * @extends			Walker_Category
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 */
class AdminPageFramework_WalkerTaxonomyChecklist extends Walker_Category {
		
	function start_el( &$sOutput, $oCategory, $iDepth=0, $aArgs=array(), $iCurrentObjectID=0 ) {
		
		/*	
		 	$aArgs keys:
			'show_option_all' => '', 
			'show_option_none' => __('No categories'),
			'orderby' => 'name', 
			'order' => 'ASC',
			'style' => 'list',
			'show_count' => 0, 
			'hide_empty' => 1,
			'use_desc_for_title' => 1, 
			'child_of' => 0,
			'feed' => '', 
			'feed_type' => '',
			'feed_image' => '', 
			'exclude' => '',
			'exclude_tree' => '', 
			'current_category' => 0,
			'hierarchical' => true, 
			'title_li' => __( 'Categories' ),
			'echo' => 1, 
			'depth' => 0,
			'taxonomy' => 'category'	// 'post_tag' or any other registered taxonomy slug will work.

			[class] => categories
			[has_children] => 1
		*/
		
		$aArgs = $aArgs + array(
			'name' 		=>	null,
			'disabled'	=>	null,
			'selected'	=>	array(),
			'input_id'	=>	null,
			'attributes'	=>	array(),
			'taxonomy'	=>	null,
		);
		
		$iID = $oCategory->term_id;
		$sTaxonomy = empty( $aArgs['taxonomy'] ) ? 'category' : $aArgs['taxonomy'];
		
		// $sDisabled = $aArgs['disabled'] ? 'disabled="Disabled"' : '';
		// $sClass = 'category-list';
		$sID = "{$aArgs['input_id']}_{$sTaxonomy}_{$iID}";
		// $sChecked = in_array( $iID, ( array ) $aArgs['selected'] )  ? 'Checked' : '';
		
		$aInputAttributes = isset( $aInputAttributes[ $iID ] ) 
			? $aInputAttributes[ $iID ] + $aArgs['attributes']
			: $aArgs['attributes'];
		
		$aInputAttributes = array(
			'id'	=>	$sID,
			'value'	=>	1,	// must be 1
			'type'	=>	'checkbox',
			'name'	=>	"{$aArgs['name']}[{$iID}]",
			'checked'	=>	in_array( $iID, ( array ) $aArgs['selected'] )  ? 'Checked' : '',
		) + $aInputAttributes;
		$sOutput .= "\n"
			. "<li id='list-{$sID}' class='category-list'>" 
				. "<label for='{$sID}' class='taxonomy-checklist-label'>"
					. "<input value='0' type='hidden' name='{$aArgs['name']}[{$iID}]' />"
					// . "<input id='{$sID}' value='1' type='checkbox' name='{$aArgs['name']}[{$iID}]' {$sChecked} {$sDisabled} />"
					. "<input " . AdminPageFramework_Utility::generateAttributes( $aInputAttributes ) . " />"
					. esc_html( apply_filters( 'the_category', $oCategory->name ) ) 
				. "</label>";	
			// no need to close </li> since it is dealt in end_el().
			
	}
}
endif;