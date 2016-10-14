<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * A taxonomy field can list terms of specified taxonomies.
 * 
 * This class defines the `taxonomy` field type.
 * 
 * <h2>Field Definition Arguments</h2>
 * <h3>Field Type Specific Arguments</h3>
 *  <ul>
 *      <li>**taxonomy_slugs** - (optional, array) the taxonomy slug to list. Default: `category`</li>
 *      <li>**max_width** - (optional, string) the inline style property value of `max-width` of this element. Include the unit such as px, %. Default: 100%</li>
 *      <li>**height** - (optional, string) the inline style property value of `height` of this element. Include the unit such as px, %. Default: 250px</li>
 *      <li>**select_all_button** - [3.3.0+] (optional, array) pass `true` to enable the `Select All` button. To set a custom label, set the text such as `__( 'Check All', 'test-domain' )`. Default: `true`.</li>
 *      <li>**select_none_button** - [3.3.0+] (optional, array) pass `true` to enable the `Select None` button. To set a custom label, set the text such as `__( 'Check All', 'test-domain' )`. Default: `true`.</li>
 *      <li>**label_no_term_found** - [3.3.2+] (optional, string) The label to display when no term is found. Default: `No Term Found`.</li>
 *      <li>**label_list_title** - [3.3.2+] (optional, string) The heading title string for a term list. Default: `''`. Insert an HTML custom string right before the list starts.</li>
 *      <li>**query** - [3.3.2+] (optional, array) an query argument array to search terms. For more details, see the argument of the [get_terms()](http://codex.wordpress.org/Function_Reference/get_terms#Parameters) function.
 *          <ul>
 *              <li>child_of - (integer) The parent term ID. All the descendant terms such as child's child term will be listed. default: `0`</li>
 *              <li>parent   - (integer) The direct parent term ID. Only the first level children will be listed. </li>
 *              <li>orderby - (string) The type of how the term list should be ordered by. Either `ID`, `term_id`, or `name` can be accepted. Default: `name`.</li>
 *              <li>order - (string) The order of the list. `ASC` or `DESC`. Default: `ASC`.</li>
 *              <li>hide_empty - (boolean) whether to show the terms with no post associated. Default: `false`.</li>
 *              <li>hierarchical - (boolean) whether to show the terms as a hierarchical tree. Default: `true`</li>
 *              <li>number - (integer) The maximum number of the terms to show. 0 for no limit. Default: `0`.</li>
 *              <li>pad_counts - (boolean) whether to sum up the post counts with the child post counts. Default: `false`</li>
 *              <li>exclude - (string|array) Comma separated term IDs or an array to exclude from the list. for example `1` will remove the 'Uncategorized' category from the list. </li>
 *              <li>exclude_tree - (integer) For more details see [get_terms()](http://codex.wordpress.org/Function_Reference/get_terms#Parameters)..</li>
 *              <li>include - (string|array) Comma separated term IDs to include in the list.</li>
 *              <li>fields - (string) Default: `all`. For more details see [get_terms()](http://codex.wordpress.org/Function_Reference/get_terms#Parameters).</li>
 *              <li>slug - (string) For more details see [get_terms()](http://codex.wordpress.org/Function_Reference/get_terms#Parameters).</li>
 *              <li>get - (string) For more details see [get_terms()](http://codex.wordpress.org/Function_Reference/get_terms#Parameters).</li>
 *              <li>name__like - (string) For more details see [get_terms()](http://codex.wordpress.org/Function_Reference/get_terms#Parameters).</li>
 *              <li>description__like - (string) For more details see [get_terms()](http://codex.wordpress.org/Function_Reference/get_terms#Parameters).</li>
 *              <li>offset - (integer) For more details see [get_terms()](http://codex.wordpress.org/Function_Reference/get_terms#Parameters).</li>
 *              <li>search - (string) The search keyword to get the term with. Default ``.</li>
 *              <li>cache_domain - (string) Default:`core`. For more details see [get_terms()](http://codex.wordpress.org/Function_Reference/get_terms#Parameters).</li>
 *          </ul>
 *      </li>
 *      <li>**queries** - [3.3.2+] (optional, array) Sets a query argument for each taxonomy. The array key must be the taxonomy slug and the value is the query argument array.</li>
 *      <li>**save_unchecked** - [3.8.8+] (optional, boolean) Decides whether to save values of unchecked terms.</li>
 *  </ul>
 * 
 * <h3>Common Field Definition Arguments</h3>
 * For common field definition arguments, see {@link AdminPageFramework_Factory_Controller::addSettingField()}.
 * 
 * <h2>Example</h2>
 * <code>
 *  array(  
 *      'field_id'              => 'taxonomy_checklist',
 *      'title'                 => __( 'Taxonomy Checklist', 'admin-page-framework-loader' ),
 *      'type'                  => 'taxonomy',
 *      'height'                => '200px', // (optional)
 *      'width'                 => '400px', // (optional)
 *      'show_post_count'       => true,    // (optional) whether to show the post count. Default: false.
 *      'taxonomy_slugs'        => array( 'category', 'post_tag', ),
 *      'select_all_button'     => false,        // 3.3.0+   to change the label, set the label here
 *      'select_none_button'    => false,        // 3.3.0+   to change the label, set the label here        
 *  )
 * </code>
 * 
 * <h3>List Taxonomies with a Custom Query</h3>
 * <code>
 * array(  
 *     'field_id'              => 'taxonomy_custom_queries',
 *     'title'                 => __( 'Custom Taxonomy Queries', 'admin-page-framework-demo' ),
 *     'type'                  => 'taxonomy',
 *     
 *     // (required)   Determines which taxonomies should be listed
 *     'taxonomy_slugs'        => $aTaxnomies = get_taxonomies( '', 'names' ),    
 *         
 *     // (optional) This defines the default query argument. For the structure and supported arguments, see http://codex.wordpress.org/Function_Reference/get_terms#Parameters
 *     'query'                 => array(
 *         'depth'     => 2,
 *         'orderby'   => 'term_id',       // accepts 'ID', 'term_id', or 'name'
 *         'order'     => 'DESC',
 *         // 'exclude'   => '1', // removes the 'Uncategorized' category.
 *         // 'search' => 'PHP',   // the search keyward
 *         // 'parent'    => 9,    // only show terms whose direct parent ID is 9.
 *         // 'child_of'  => 8,    // only show child terms of the term ID of 8.
 *     ),
 *     // (optional) This allows the user to set a query argument for each taxonomy. 
 *     // Note that each element will be merged with the above default 'query' argument array. 
 *     // So unset keys here will be overridden by the default argument array above. 
 *     'queries'               => array(
 *         // taxonomy slug => query argument array
 *         'category'  =>  array(
 *             'depth'     => 2,
 *             'orderby'   => 'term_id',  
 *             'order'     => 'DESC',
 *             'exclude'   => array( 1 ), 
 *         ),
 *         'post_tag'  => array(
 *             'orderby'   => 'name',
 *             'order'     => 'ASC',
 *             // 'include'   => array( 4, ), // term ids
 *         ),
 *     ), 
 * ),
 * </code>
 *  
 * @image           http://admin-page-framework.michaeluno.jp/image/common/form/field_type/taxonomy.png
 * @package         AdminPageFramework
 * @subpackage      Common/Form/FieldType
 * @since           2.1.5
 * @since           3.3.1       Changed to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
 */
class AdminPageFramework_FieldType_taxonomy extends AdminPageFramework_FieldType_checkbox {
    
    /**
     * Defines the field type slugs used for this field type.
     * @var     array
     */
    public $aFieldTypeSlugs = array( 'taxonomy', );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * @remark  `$_aDefaultKeys` holds shared default key-values defined in the base class.
     * @var     array
     */
    protected $aDefaultKeys = array(
        'taxonomy_slugs'        => 'category',      // (array|string) This is for the taxonomy field type.
        'height'                => '250px',         // the tab box height
        'width'                 => null,            // 3.5.7.2+ the tab box width
        'max_width'             => '100%',          // for the taxonomy checklist field type, since 2.1.1.     
        'show_post_count'       => true,            // (boolean) 3.2.0+ whether or not the post count associated with the term should be displayed or not.
        'attributes'            => array(),    
        'select_all_button'     => true,            // (boolean|string) 3.3.0+ to change the label, set the label here
        'select_none_button'    => true,            // (boolean|string) 3.3.0+ to change the label, set the label here                
        'label_no_term_found'   => null,            // (string) 3.3.2+  The label to display when no term is found. null needs to be set here as the default value will be assigned in the field output method.
        'label_list_title'      => '',              // (string) 3.3.2+ The heading title string for a term list. Default: `''`. Insert an HTML custom string right before the list starts.
        'query'                 => array(       // (array)  3.3.2+ Defines the default query argument.
            // see the arguments of the get_category() function: http://codex.wordpress.org/Function_Reference/get_categories
            // see the argument of the get_terms() function: http://codex.wordpress.org/Function_Reference/get_terms        
            'child_of'          => 0,
            'parent'            => '',
            'orderby'           => 'name',      // (string) 'ID' or 'term_id' or 'name'
            'order'             => 'ASC',       // (string) 'ASC' or 'DESC'
            'hide_empty'        => false,       // (boolean) whether to show the terms with no post associated. Default: false
            'hierarchical'      => true,        // (boolean) whether to show the terms as a hierarchical tree.
            'number'            => '',          // (integer) The maximum number of the terms to show.
            'pad_counts'        => false,       // (boolean) whether to sum up the post counts with the child post counts.
            'exclude'           => array(),     // (string) Comma separated term IDs to exclude from the list. for example `1` will remove the 'Uncategorized' category from the list.
            'exclude_tree'      => array(), 
            'include'           => array(),     // (string) Comma separated term IDs to include in the list.
            'fields'            => 'all', 
            'slug'              => '', 
            'get'               => '', 
            'name__like'        => '',
            'description__like' => '',
            'offset'            => '', 
            'search'            => '',          // (string) The search keyword to get the term with.
            'cache_domain'      => 'core',
        ),
        'queries'   => array(),         // (optional, array) 3.3.2+  Sets a query argument for each taxonomy. The array key must be the taxonomy slug and the value is the query argument array.
        'save_unchecked'        => true,        // (optional, boolean) 3.8.8+   Whether to store the values of unchecked items.
    );
    
    /**
     * Loads the field type necessary components.
     * 
     * @since       2.1.5  
     * @since       3.3.1       Changed from `_replyToFieldLoader()`.
     * @internal
     * @return      void
     */ 
    protected function setUp() {
        new AdminPageFramework_Form_View___Script_CheckboxSelector;
    }
    
    /**
     * Returns the field type specific JavaScript script.
     * 
     * Returns the JavaScript script of the taxonomy field type.
     * 
     * @since       2.1.1
     * @since       2.1.5       Moved from `AdminPageFramework_Property_Base()`.
     * @since       3.3.1       Changed from `_replyToGetScripts()`.
     * @internal
     * @return      string
     */ 
    protected function getScripts() {

        $_aJSArray = json_encode( $this->aFieldTypeSlugs );
        
        return parent::getScripts() . 
        // return 
<<<JAVASCRIPTS
/* For tabs */
var enableAdminPageFrameworkTabbedBox = function( nodeTabBoxContainer ) {
    jQuery( nodeTabBoxContainer ).each( function() {
        jQuery( this ).find( '.tab-box-tab' ).each( function( i ) {
            
            if ( 0 === i ) {
                jQuery( this ).addClass( 'active' );
            }
                
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
};        

jQuery( document ).ready( function() {
         
    enableAdminPageFrameworkTabbedBox( jQuery( '.tab-box-container' ) );

    /* The repeatable event */
    jQuery().registerAdminPageFrameworkCallbacks( {     
        /**
         * The repeatable field callback for the add event.
         * 
         * @param object node
         * @param string    the field type slug
         * @param string    the field container tag ID
         * @param integer    the caller type. 1 : repeatable sections. 0 : repeatable fields.
         */     
        added_repeatable_field: function( oCloned, sFieldType, sFieldTagID, iCallType ) {
            
            // Repeatable Sections
            if ( 1 === iCallType ) {
                var _oSectionsContainer     = jQuery( oCloned ).closest( '.admin-page-framework-sections' );
                var _iSectionIndex          = _oSectionsContainer.attr( 'data-largest_index' );
                var _sSectionIDModel        = _oSectionsContainer.attr( 'data-section_id_model' );
                jQuery( oCloned ).find( 'div, li.category-list' ).incrementAttribute(
                    'id', // attribute name
                    _iSectionIndex, // increment from
                    _sSectionIDModel // digit model
                );
                jQuery( oCloned ).find( 'label' ).incrementAttribute(
                    'for', // attribute name
                    _iSectionIndex, // increment from
                    _sSectionIDModel // digit model
                );            
                jQuery( oCloned ).find( 'li.tab-box-tab a' ).incrementAttribute(
                    'href', // attribute name
                    _iSectionIndex, // increment from
                    _sSectionIDModel // digit model
                );                
            } 
            // Repeatable fields 
            else {
                var _oFieldsContainer       = jQuery( oCloned ).closest( '.admin-page-framework-fields' );
                var _iFieldIndex            = Number( _oFieldsContainer.attr( 'data-largest_index' ) - 1 );
                var _sFieldTagIDModel       = _oFieldsContainer.attr( 'data-field_tag_id_model' );

                jQuery( oCloned ).find( 'div, li.category-list' ).incrementAttribute(
                    'id', // attribute name
                    _iFieldIndex, // increment from
                    _sFieldTagIDModel // digit model
                );
                jQuery( oCloned ).find( 'label' ).incrementAttribute(
                    'for', // attribute name
                    _iFieldIndex, // increment from
                    _sFieldTagIDModel // digit model
                );            
                jQuery( oCloned ).find( 'li.tab-box-tab a' ).incrementAttribute(
                    'href', // attribute name
                    _iFieldIndex, // increment from
                    _sFieldTagIDModel // digit model
                );
            }
            enableAdminPageFrameworkTabbedBox( jQuery( oCloned ).find( '.tab-box-container' ) );            
            
        }
    
    },
    {$_aJSArray}
    );
});     
JAVASCRIPTS;

    }
    
    /**
     * Returns the field type specific CSS rules.
     * 
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetStyles()`.
     * @internal
     * @return      string
     */ 
    protected function getStyles() {
        return <<<CSSRULES
/* Taxonomy Field Type */
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
    white-space: nowrap;     
}    
/* Tabbed box */
.admin-page-framework-field .tab-box-container.categorydiv {
    max-height: none;
}
.admin-page-framework-field .tab-box-tab-text {
    display: inline-block;
    font-size: 13px;
    font-size: smaller;
    padding: 2px;
}
.admin-page-framework-field .tab-box-tabs {
    line-height: 12px;
    margin-bottom: 0;
}
/* .admin-page-framework-field .tab-box-tab {     
    vertical-align: top;
} */
.admin-page-framework-field .tab-box-tabs .tab-box-tab.active {
    display: inline;
    border-color: #dfdfdf #dfdfdf #fff;
    margin-bottom: 0px;
    padding-bottom: 4px;
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
    padding: 0 0 0 1.8em;
    padding: 0.55em 0.5em 0.55em 1.8em;
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

    /* height: 300px; */
    display: none; 
    overflow: auto; 
    display: block; 
    position: relative; 
    overflow-x: hidden;
}
.admin-page-framework-field .tab-box-content .taxonomychecklist {
    margin-right: 3.2em;
}
.admin-page-framework-field .tab-box-content:target, 
.admin-page-framework-field .tab-box-content:target, 
.admin-page-framework-field .tab-box-content:target { 
    display: block; 
}  
/* tab-box-content */
.admin-page-framework-field .tab-box-content .select_all_button_container, 
.admin-page-framework-field .tab-box-content .select_none_button_container
{
    margin-top: 0.8em;
}
/* Nested Checkbox Items */
.admin-page-framework-field .taxonomychecklist .children {
    margin-top: 6px;
    margin-left: 1em;
}
CSSRULES;

    }
    
    /**
     * Returns the field type specific CSS rules.
     * 
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetInputIEStyles()`.
     * @internal
     * @return      string
     */ 
    protected function getIEStyles() {
        return <<<CSSRULES
.tab-box-content { display: block; }
.tab-box-contents { overflow: hidden;position: relative; }
b { position: absolute; top: 0px; right: 0px; width:1px; height: 251px; overflow: hidden; text-indent: -9999px; }
CSSRULES;

    }    
    
    /**
     * Returns the output of the field type.
     * 
     * Returns the output of taxonomy checklist check boxes.
     * 
     * @remark      Multiple fields are not supported.
     * @remark      Repeater fields are not supported.
     * @since       2.0.0
     * @since       2.1.1       The checklist boxes are rendered in a tabbed single box.
     * @since       2.1.5       Moved from AdminPageFramework_FormField.
     * @since       3.3.1       Changed from `_replyToGetField()`.
     * @internal
     * @return      string
     */
    protected function getField( $aField ) {
        
        // Format
        $aField['label_no_term_found'] = $this->getElement( 
            $aField, 
            'label_no_term_found', 
            $this->oMsg->get( 'no_term_found' ) 
        );
        
        // Parse
        $_aTabs         = array();
        $_aCheckboxes   = array();      
        foreach( $this->getAsArray( $aField['taxonomy_slugs'] ) as $sKey => $sTaxonomySlug ) {
            $_aTabs[]        = $this->_getTaxonomyTab( $aField, $sKey, $sTaxonomySlug );    
            $_aCheckboxes[]  = $this->_getTaxonomyCheckboxes( $aField, $sKey, $sTaxonomySlug );
        }

        // Output
        return "<div id='tabbox-{$aField['field_id']}' class='tab-box-container categorydiv' style='max-width:{$aField['max_width']};'>"
                . "<ul class='tab-box-tabs category-tabs'>" 
                    . implode( PHP_EOL, $_aTabs ) 
                . "</ul>" 
                . "<div class='tab-box-contents-container'>"
                    . "<div class='tab-box-contents' style='height: {$aField['height']};'>"
                        . implode( PHP_EOL, $_aCheckboxes )
                    . "</div>"
                . "</div>" 
            . "</div>"
            ;
                
    }
        /**
         * Returns the HTML output of taxonomy checkboxes.
         * 
         * @since       3.5.3
         * @return      string      the generated HTML output of taxonomy checkboxes.
         * @internal
         */
        private function _getTaxonomyCheckboxes( array $aField, $sKey, $sTaxonomySlug ) {
            
            $_aTabBoxContainerArguments = array(
                'id'    => "tab_{$aField['input_id']}_{$sKey}",
                'class' => 'tab-box-content',
                'style' => $this->generateInlineCSS(
                    array(
                        'height' => $this->getAOrB( $aField[ 'height' ], $this->getLengthSanitized( $aField[ 'height' ] ), null ),
                        'width'  => $this->getAOrB( $aField[ 'width' ], $this->getLengthSanitized( $aField[ 'width' ] ), null ),
                    )
                ),
            );
            return "<div " . $this->getAttributes( $_aTabBoxContainerArguments ) . ">"
                    . $this->getElement( $aField, array( 'before_label', $sKey ) )
                    . "<div " . $this->getAttributes( $this->_getCheckboxContainerAttributes( $aField ) ) . "></div>"
                    . "<ul class='list:category taxonomychecklist form-no-clear'>"
                        . $this->_getTaxonomyChecklist( $aField, $sKey, $sTaxonomySlug )
                    . "</ul>"     
                    . "<!--[if IE]><b>.</b><![endif]-->"
                    . $this->getElement( $aField, array( 'after_label', $sKey ) )
                . "</div><!-- tab-box-content -->";
            
        }    
            /**
             * 
             * @param       array       $aField         Field definition array,
             * @param       string      $sKey           The array key of the 'taxonomy_slugs' argument array.
             * @param       string      $sTaxonomySlug  the taxonomy slug (id) such as category and post_tag 
             * @internal
             * @return      string
             */
            private function _getTaxonomyChecklist( array $aField, $sKey, $sTaxonomySlug ) {
                return wp_list_categories( 
                    array(
                        'walker'                => new AdminPageFramework_WalkerTaxonomyChecklist, // the walker class instance
                        'taxonomy'              => $sTaxonomySlug, 
                        '_name_prefix'          => is_array( $aField[ 'taxonomy_slugs' ] ) 
                            ? "{$aField[ '_input_name' ]}[{$sTaxonomySlug}]" 
                            : $aField[ '_input_name' ],   // name prefix of the input
                        '_input_id_prefix'      => $aField[ 'input_id' ],
                        '_attributes'           => $this->getElement( 
                            $aField, 
                            array( 'attributes', $sKey ), 
                            array() 
                        ) + $aField[ 'attributes' ],                 
                        
                        // checked items ( term IDs ) e.g.  array( 6, 10, 7, 15 ), 
                        '_selected_items'       => $this->_getSelectedKeyArray( $aField['value'], $sTaxonomySlug ),
                        
                        'echo'                  => false,  // returns the output
                        'show_post_count'       => $aField[ 'show_post_count' ],      // 3.2.0+
                        'show_option_none'      => $aField[ 'label_no_term_found' ],  // 3.3.2+ 
                        'title_li'              => $aField[ 'label_list_title' ],     // 3.3.2+
                        
                        '_save_unchecked'       => $aField[ 'save_unchecked' ], // 3.8.8+ Whether to insert hidden input element for unchecked.
                    ) 
                    + $this->getAsArray( 
                        $this->getElement( 
                            $aField, 
                            array( 'queries', $sTaxonomySlug ), 
                            array() 
                        ), 
                        true 
                    )
                    + $aField[ 'query' ]  
                );
            }
     
            /**
             * Returns an array consisting of keys whose value is true.
             * 
             * A helper function for the above getTaxonomyChecklistField() method. 
             * 
             * @since   2.0.0
             * @param   array   $vValue This can be either an one-dimensional array ( for single field ) or a two-dimensional array ( for multiple fields ).
             * @param   string  $sKey     
             * @return  array   Returns an numerically indexed array holding the keys that yield true as the value.
             * @internal
             */ 
            private function _getSelectedKeyArray( $vValue, $sTaxonomySlug ) {

                $vValue = ( array ) $vValue; // cast array because the initial value (null) may not be an array.
                
                if ( ! isset( $vValue[ $sTaxonomySlug ] ) ) { 
                    return array(); 
                }
                if ( ! is_array( $vValue[ $sTaxonomySlug ] ) ) { 
                    return array(); 
                }
                
                return array_keys( $vValue[ $sTaxonomySlug ], true );
            
            }            
            
        /**
         * Returns an HTML tab list item output.
         * 
         * @since       3.5.3
         * @return      string      The generated HTML tab list item output.
         * @internal
         */
        private function _getTaxonomyTab( array $aField, $sKey, $sTaxonomySlug ) {
            return "<li class='tab-box-tab'>"
                    . "<a href='#tab_{$aField['input_id']}_{$sKey}'>"
                        . "<span class='tab-box-tab-text'>" 
                            . $this->_getLabelFromTaxonomySlug( $sTaxonomySlug )
                        . "</span>"
                    ."</a>"
                ."</li>";
        }    
            /**
             * Retrieves the label of the given taxonomy by its slug.
             * 
             * A helper function for the above getTaxonomyChecklistField() method.
             * 
             * @since 2.1.1
             */
            private function _getLabelFromTaxonomySlug( $sTaxonomySlug ) {
                $_oTaxonomy = get_taxonomy( $sTaxonomySlug );
                return isset( $_oTaxonomy->label )
                    ? $_oTaxonomy->label
                    : null;
            }       

        
}
