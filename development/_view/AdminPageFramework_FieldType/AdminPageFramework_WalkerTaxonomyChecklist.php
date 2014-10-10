<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_WalkerTaxonomyChecklist' ) ) :
/**
 * Provides methods for rendering taxonomy check lists.
 * 
 * Used for the wp_list_categories() function to render category hierarchical checklist.
 * 
 * @see Walker : wp-includes/class-wp-walker.php
 * @see Walker_Category : wp-includes/category-template.php
 * @since 2.0.0
 * @since 2.1.5 Added the tag_id key to the argument array. Changed the format of 'id' and 'for' attribute of the input and label tags.
 * @extends Walker_Category
 * @package AdminPageFramework
 * @subpackage FieldType
 * @internal
 */
class AdminPageFramework_WalkerTaxonomyChecklist extends Walker_Category {
    
    /**
     * Modifies the variable stirng the opening 'li' tag of the list.
     */
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
            'taxonomy' => 'category' // 'post_tag' or any other registered taxonomy slug will work.

            [class] => categories
            [has_children] => 1
        */
        
        $aArgs = $aArgs + array(
            'name'              => null,
            'disabled'          => null,
            'selected'          => array(),
            'input_id'          => null,
            'attributes'        => array(),
            'taxonomy'          => null,
            'show_post_count'   => false,    // 3.2.0+
        );
        
        // Local variables
        $_iID            = $oCategory->term_id;
        $_sTaxonomySlug  = empty( $aArgs['taxonomy'] ) ? 'category' : $aArgs['taxonomy'];
        $_sID            = "{$aArgs['input_id']}_{$_sTaxonomySlug}_{$_iID}";

        // Post count
        $_sPostCount     = $aArgs['show_post_count'] ? " <span class='font-lighter'>(" . $oCategory->count . ")</span>" : '';
        
        // Attributes
        $_aInputAttributes = isset( $_aInputAttributes[ $_iID ] ) 
            ? $_aInputAttributes[ $_iID ] + $aArgs['attributes']
            : $aArgs['attributes'];

        $_aInputAttributes = array(
            'id'        => $_sID,
            'value'     => 1, // must be 1
            'type'      => 'checkbox',
            'name'      => "{$aArgs['name']}[{$_iID}]",
            'checked'   => in_array( $_iID, ( array ) $aArgs['selected'] ) ? 'checked' : '',
        ) + $_aInputAttributes;
        $_aInputAttributes['class'] .= ' apf_checkbox';
        
        // Output
        $sOutput .= "\n" // the variable is by reference so the modification takes effect
            . "<li id='list-{$_sID}' class='category-list'>" 
                . "<label for='{$_sID}' class='taxonomy-checklist-label'>"
                    . "<input value='0' type='hidden' name='{$aArgs['name']}[{$_iID}]' class='apf_checkbox' />"
                    . "<input " . AdminPageFramework_WPUtility::generateAttributes( $_aInputAttributes ) . " />"
                    . esc_html( apply_filters( 'the_category', $oCategory->name ) ) 
                    . $_sPostCount
                . "</label>";    
            /* no need to close the </li> tag since it is dealt in the end_el() method. */
            
    }
}
endif;