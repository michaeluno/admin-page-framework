<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods for rendering taxonomy check lists.
 * 
 * Used for the wp_list_categories() function to render category hierarchical checklist.
 * 
 * @see             Walker : wp-includes/class-wp-walker.php
 * @see             Walker_Category : wp-includes/category-template.php
 * @since           2.0.0
 * @since           2.1.5     Added the tag_id key to the argument array. Changed the format of 'id' and 'for' attribute of the input and label tags.
 * @extends         Walker_Category
 * @package         AdminPageFramework
 * @subpackage      FieldType
 * @internal
 */
class AdminPageFramework_WalkerTaxonomyChecklist extends Walker_Category {
    
    /**
     * Modifies the variable string the opening 'li' tag of the list.
     * 
     * @param       string      $sOutput        
     * @param       object      $oTerm        
     * @param       integer     $iDepth
     * @param       array       $aArgs          The argument passed from the field output.
     * <h4>Arguments</h4>
     *  - show_option_all       (string)    Text to display for showing all categories. default: ``
     *  - show_option_none      (string)    Text to display for showing no categories. e.g. `__( 'No categories' )`
     *  - orderby               (string)    Accepts 'name' or 'ID'. What column to use for ordering the terms. e.g. `name`. default: `ID`
     *  - order                 (string)    What direction to order categories. Accepts 'ASC' (ascending) or 'DESC' (descending). default: `ASC`
     *  - title_li              (string)    The string that is inserted before the list starts. Default: __( 'Categories' ),
     *  - echo                  (boolean|integer)   Whether to echo the output or return the output string value.
     *  - hierarchical          (boolean)   Whether to show the terms in a hierarchical structure. 
     *  - depth                 (integer)   The max level to display the hierarchical depth. Default: 0.
     *  - hide_empty            (boolean|integer) Whether to hide terms that have no post associated.
     *  - pad_counts            (boolean|integer) Whether to sum up the post count with the child post counts.
     *  - number                (integer)   The maximum number of terms to display. Default 0.
     *  - exclude               (string)    Comma separated term ID(s) to exclude from the list.
     *  - include               (string)    Comma separated term ID(s) to include in the list.
     *  - child_of              (integer)   Term ID to retrieve child terms of. If multiple taxonomies are passed, $child_of is ignored. Default 0.
     * 
     *  <h4>Known Existing Arguments</h4>
     *  - feed                 => '', 
     *  - feed_type             => '',
     *  - feed_image            => '', 
     *  - exclude_tree          => '',  
     *  - current_category      => 0,
     *  - class                 => categories,
     * 
     * <h4>Unknown Arguments</h4>
     *  - taxonomy              => 'category', // 'post_tag' or any other registered taxonomy slug will work. side note: the framework option will be used
     *  - has_children          => 1,
     *  - option_none_value     (mixed)     Value to use when no taxonomy term is selected.     
     *  - show_count            (bool|int)  Whether to show how many posts are associated with the term. default: `0`  side note: did not take effect
     *  - style                 (string)    'list', side note: Could not confirm whether there are other option besides 'list'.
     *  - use_desc_for_title    (boolean|int) default is 1 - Whether to use the category description as the title attribute. side note: the framework enables this by default.
     * @param       integer     $iCurrentObjectID
     */
    function start_el( &$sOutput, $oTerm, $iDepth=0, $aArgs=array(), $iCurrentObjectID=0 ) {
       
        $aArgs = $aArgs + array(
            '_name_prefix'      => null,
            '_input_id_prefix'  => null,
            '_attributes'       => array(),
            '_selected_items'   => array(),
            'taxonomy'          => null,    // parsed by the core function to perform the database query.
            'disabled'          => null,    // not sure what this was for
        );
        
        // Local variables
        $_iID            = $oTerm->term_id;
        $_sTaxonomySlug  = empty( $aArgs[ 'taxonomy_slug' ] ) 
            ? 'category' 
            : $aArgs[ 'taxonomy_slug' ];
        $_sID            = "{$aArgs['_input_id_prefix']}_{$_sTaxonomySlug}_{$_iID}";

        // Post count
        $_sPostCount     = $aArgs['show_post_count'] 
            ? " <span class='font-lighter'>(" . $oTerm->count . ")</span>" 
            : '';
        
        // Attributes
        $_aInputAttributes = isset( $_aInputAttributes[ $_iID ] ) 
            ? $_aInputAttributes[ $_iID ] + $aArgs['_attributes']
            : $aArgs['_attributes'];
        $_aInputAttributes = array(
            'id'        => $_sID,
            'value'     => 1, // must be 1 beacause the index of zero exists so the index value cannot be assigined here.
            'type'      => 'checkbox',
            'name'      => "{$aArgs['_name_prefix']}[{$_iID}]",
            'checked'   => in_array( $_iID, ( array ) $aArgs[ '_selected_items' ] )
                ? 'checked' 
                : null,
        ) + $_aInputAttributes
          + array(
            'class'     => null,
        );
        $_aInputAttributes['class'] .= ' apf_checkbox';
        
        $_aLiTagAttributes = array(
            'id'        => "list-{$_sID}",
            'class'     => 'category-list',
            'title'     => $oTerm->description,
        );
        
        // Output - the variable is by reference so the modification takes effect
        $sOutput .= "\n"
            . "<li " . AdminPageFramework_WPUtility::generateAttributes( $_aLiTagAttributes ) . ">" 
                . "<label for='{$_sID}' class='taxonomy-checklist-label'>"
                    . "<input value='0' type='hidden' name='" . $_aInputAttributes[ 'name' ] . "' class='apf_checkbox' />"
                    . "<input " . AdminPageFramework_WPUtility::generateAttributes( $_aInputAttributes ) . " />"
                    . esc_html( apply_filters( 'the_category', $oTerm->name ) ) 
                    . $_sPostCount
                . "</label>";    
            /* no need to close the </li> tag since it is dealt in the end_el() method. */
            
    }
}