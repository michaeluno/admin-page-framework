<?php
/**
 * Admin Page Framework
 *
 * Facilitates WordPress plugin and theme development.
 *
 * @author      Michael Uno <michael@michaeluno.jp>
 * @copyright   2013-2019 (c) Michael Uno
 * @license     MIT <http://opensource.org/licenses/MIT>
 * @package     AdminPageFramework
 */

if ( ! class_exists( 'PostTypeTaxonomyCustomFieldType' ) ) :
/**
 * A field type that lets the user select taxonomy terms of selected post types.
 *
 * @since       3.8.8
 * @version     0.0.1b
 * @requires    Admin Page Framework 3.8.8 or above.
 */
class PostTypeTaxonomyCustomFieldType extends AdminPageFramework_FieldType_taxonomy {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'post_type_taxonomy', );

    /**
     * Defines the default key-values of this field type settings.
     *
     * @remark   `$_aDefaultKeys` holds shared default key-values defined in the base class.
     * @see      http://codex.wordpress.org/Function_Reference/get_post_types#Parameters
     */
    protected $aDefaultKeys = array(
        /**
         * Accepts query arguments. For the argument specification, see the arg parameter of get_post_types() function.
         * See: http://codex.wordpress.org/Function_Reference/get_post_types#Parameters
         */
        'post_type' => array(
            'query'                 => array(
                'public'                => true,
            ),
            'operator'              => 'and',
            'attributes'            => array(
                'size'      => 30,
                'maxlength' => 400,
            ),
            'slugs_to_remove'       => array(),    // the default array will be assigned in the rendering method.
            'select_all_button'     => true,
            'select_none_button'    => true,
        ),

        /**
         * For taxonomy terms
         */
        'taxonomy'              => array(
            // 'taxonomy_slugs'        => 'category',      // (array|string) This is for the taxonomy field type.
            'height'                => '250px',         // the tab box height
            'width'                 => null,            // the tab box width
            'max_width'             => '100%',          // for the taxonomy checklist field type, since 2.1.1.
            'show_post_count'       => true,            // (boolean) whether or not the post count associated with the term should be displayed or not.
            'attributes'            => array(),
            'select_all_button'     => true,            // (boolean|string) to change the label, set the label here
            'select_none_button'    => true,            // (boolean|string) to change the label, set the label here
            'label_no_term_found'   => null,            // (string)  The label to display when no term is found. null needs to be set here as the default value will be assigned in the field output method.
            'label_list_title'      => '',              // (string) The heading title string for a term list. Default: `''`. Insert an HTML custom string right before the list starts.
            'query'                 => array(       // (array) Defines the default query argument.
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
            'queries'   => array(),         // (optional, array) Sets a query argument for each taxonomy. The array key must be the taxonomy slug and the value is the query argument array.
            'save_unchecked'        => false,        // (optional, boolean) Whether to store the values of unchecked items.
        ),

    );

    protected $aDefaultRemovingPostTypeSlugs = array(
        // 'revision',
        // 'attachment',
        // 'nav_menu_item',
    );


    // protected function construct() {
    // }

    /**
     * Loads the field type necessary components.
     */
    // public function setUp() {
    // }



    /**
     * Returns an array holding the urls of enqueuing scripts.
     * @return      array
     */
    protected function getEnqueuingScripts() {
        return array();
    }

    /**
     * @return      array
     */
    protected function getEnqueuingStyles() {
        return array();
    }

    /**
     * Returns the field type specific JavaScript script.
     */
    protected function getScripts() {
        $_aJSArray  = json_encode( $this->aFieldTypeSlugs );
        $_sScript   =  parent::getScripts();
        $_sScript .= <<<JAVASCRIPTS
jQuery( document ).ready( function(){
    
    // Add the select all and none buttons.
    jQuery( '.admin-page-framework-checkbox-container-posttype[data-select_all_button]' ).each( function( iIndex ){
        jQuery( this ).before( '<div class=\"select_all_button_container\" onclick=\"jQuery( this ).selectAllAdminPageFrameworkCheckboxes(); return false;\"><a class=\"select_all_button button button-small\">' + jQuery( this ).data( 'select_all_button' ) + '</a></div>' );
    });            
    jQuery( '.admin-page-framework-checkbox-container-posttype[data-select_none_button]' ).each( function( iIndex ){
        jQuery( this ).before( '<div class=\"select_none_button_container\" onclick=\"jQuery( this ).deselectAllAdminPageFrameworkCheckboxes(); return false;\"><a class=\"select_all_button button button-small\">' + jQuery( this ).data( 'select_none_button' ) + '</a></div>' );
    });
    
    // When the post type check-boxes are clicked, show/hide the corresponding taxonomy elements.
    jQuery( document ).on( 'change', '.admin-page-framework-field-post_type_taxonomy .admin-page-framework-field-posttype input[type="checkbox"]', function() {
        var _sPostTypeSlug       = jQuery( this ).data( 'key' );
        var _sTargetTabsSelector = '.tab-box-container li.tab-box-tab[data-associated-post-types*="' + _sPostTypeSlug + ',"]';
        var _sTargetCBContainers = '.tab-box-content[data-associated-post-types*="' + _sPostTypeSlug + ',"]';
        var _sTabsBoxSelector    = '.tab-box-container';
        var _oField              = jQuery( this ).closest( '.admin-page-framework-field-post_type_taxonomy' );
        var _oTargetTabs         = _oField.find( _sTargetTabsSelector );                
        var _oTargetCBContainers = _oField.find( _sTargetCBContainers );
        if ( jQuery( this ).is( ':checked' ) ) {            
            
            // Show the associated taxonomy tabs.
            // Check the number of showing tabs. 
            // Note that there are post types which do not have any taxonomy.
            if ( _oTargetTabs.length ) {                
                _oTargetTabs.show()
                    .trigger( 'click' );    // need to activate a tab.
                
                // Enable the check-boxes as they will be disabled when the post type check-box is unchecked.
                _oTargetCBContainers.find( 'input[type=checkbox]' ).removeAttr( 'disabled' );                
                
                // If at least one item which is associated with a taxonomy is checked, the tabs-container box should be displayed.
                _oField.find( _sTabsBoxSelector ).show();

            }
            
        } else {
            
            var _sVisibleTabsSelector = '.tab-box-container li.tab-box-tab:visible';
            
            // Hide the associated taxonomy tabs.                        
            if ( _oTargetTabs.length ) {                            
                _oTargetTabs.hide();
                
                // Disable the check-boxes so that the values won't be sent.
                _oTargetCBContainers.find( 'input[type=checkbox]' ).attr( 'disabled','disabled' );
                
                // Activate the first visible tab item.
                _oField.find( _sVisibleTabsSelector ).first().trigger( 'click' );
                
            }
                
            // If none of the tabs is shown, hide the check-box container box.
            if ( ! _oField.find( _sVisibleTabsSelector ).length ){
                _oField.find( _sTabsBoxSelector ).hide();
            }
            
        }
    });
    
    // Hide the unchecked elements (tabs and check-box containers).
    jQuery( '.admin-page-framework-field-post_type_taxonomy .admin-page-framework-field-posttype input[type="checkbox"]:not(:checked)' ).each( function(){
        jQuery( this ).trigger( 'change' );
    } );
    

    jQuery().registerAdminPageFrameworkCallbacks( {
        /**
         * Called when a field gets repeated.
         */
        repeated_field: function( oCloned, aModel ) {

            // Uncheck all the items and hide the associated elements (tabs and check-box containers).
            oCloned.find( '.admin-page-framework-field-posttype input[type="checkbox"]' )
                .prop( 'checked', false )
                .trigger( 'change' );         
                        
        },            
    },
    {$_aJSArray}
    );    
    
});
JAVASCRIPTS;
        return $_sScript;


    }

    /**
     * Returns the field type specific CSS rules.
     */
    protected function getStyles() {
        return parent::getStyles()
            . " /* Select All and None Buttons */
.admin-page-framework-field-post_type_taxonomy .select_all_button_container, 
.admin-page-framework-field-post_type_taxonomy .select_none_button_container
{
    display: inline-block;  /* 3.8.8+ For post_type_taxonomy field type */
    margin-bottom: 1em;
}        
.tab-box-content .select_all_button_container,
.tab-box-content .select_none_button_container
{
    margin-bottom: 0;
}

.admin-page-framework-field-post_type_taxonomy .select-taxonomy-terms-checkbox-container {
    display: inline-block;
    margin: 1em 0;
}
";
    }

    /**
     * Returns the output of the field type.
     */
    public function getField( $aField ) {

        $_sOutput  = '';

        // Post type check boxes.
        $_aField   = $this->_getPostTypeFieldArguments( $aField );
        $_sOutput .= $this->getFieldOutput( $_aField );

        // Taxonomy term check-boxes
        $_aField   = $this->_getTaxonomyFieldArguments( $aField );
        $_sOutput .= parent::getField( $_aField );

        // Remove the repeatable button place holders embedded with the parent methods.
        $_sOutput  = str_replace(
            "<div class='repeatable-field-buttons'></div>", // search
            '', // replacement
            $_sOutput   // subject
        );

        return "<div class='repeatable-field-buttons'></div>"
            . $_sOutput;

    }

        /**
         * @return      array
         */
        private function _getPostTypeFieldArguments( $aField ) {

            // Add the `post_type` dimension.
            $aField[ 'attributes' ] = array(
                'name'  => $aField[ 'attributes' ][ 'name' ] . '[post_type]',
                'id'    => $aField[ 'attributes' ][ 'id' ] . '_post_type',
            ) + $aField[ 'attributes' ];

            $aField[ 'input_id' ] = $aField[ 'input_id' ] . '_post_type';
            $aField[ 'tag_id' ]   = $aField[ 'tag_id' ] . '_post_type';

            $_aField = $aField[ 'post_type' ] + $aField;
            $_aField[ 'type' ] = 'posttype';

            $_aField[ 'attributes' ] = $aField[ 'attributes' ] + $_aField[ 'attributes' ];

            unset(
                $_aField[ 'title' ],
                $_aField[ 'description' ],
                $_aField[ 'repeatable' ],
                $_aField[ 'sortable' ]
            );

            $_aField[ 'value' ]       = $this->getElementAsArray(
                $aField,
                array( 'value', 'post_type' )
            );

            return $_aField;

        }


        /**
         * @return      array
         */
        private function _getTaxonomyFieldArguments( $aField ) {

            $aField[ 'taxonomy_slugs' ] = $this->_getTaxonomySlugs( $aField );
            $_aField = $aField[ 'taxonomy' ] + $aField;

            // Add the `taxonomy` dimension.
            $_aField[ '_input_name' ] = $aField[ '_input_name' ] . '[taxonomy]';
            $_aField[ 'input_id' ]    = $aField[ 'input_id' ] . '_taxonomy';

            $_aField[ 'value' ]       = $this->getElementAsArray(
                $aField,
                array( 'value', 'taxonomy' )
            );
            return $_aField;

        }
            /**
             * @return      array
             */
            private function _getTaxonomySlugs( $aField ) {

                $_aPostTypeFieldArguments = $this->getElementAsArray( $aField, array( 'post_type' ) );
                $_aPostTypes              = $this->_getPostTypeArrayForChecklist(
                    $this->getElement( $_aPostTypeFieldArguments, array( 'query' ), array( '_builtin' => true, 'public' => true ) ),
                    $this->getElement( $_aPostTypeFieldArguments, array( 'operator' ), 'and' ),
                    $this->getElementAsArray( $_aPostTypeFieldArguments, array( 'slugs_to_remove' ), array() )
                );
                $_aTaxonomyNames          = array();
                foreach ( $_aPostTypes as $_sPostTypeSlug => $_sLabel ) {
                    $_aTaxonomyNames = array_merge(
                        $_aTaxonomyNames,
                        get_object_taxonomies( $_sPostTypeSlug, 'names' )
                    );
                }
                $_aTaxonomyNames = array_unique( $_aTaxonomyNames );
                return $_aTaxonomyNames;

            }
                /**
                 * A helper function for the above getPosttypeChecklistField method.
                 *
                 * @since   3.8.8
                 * @param   $asQueryArgs        array   The query argument.
                 * @param   $sOperator          array   The query operator.
                 * @param   $aSlugsToRemove     array   The slugs to remove from the result.
                 * @return  array   The array holding the elements of installed post types' labels and their slugs except the specified expluding post types.
                 * @internal
                 */
                private function _getPostTypeArrayForChecklist( $asQueryArgs=array(), $sOperator='and', $aSlugsToRemove ) {

                    $_aPostTypes = array();
                    foreach( get_post_types( $asQueryArgs, 'objects' ) as $_oPostType ) {
                        if (  isset( $_oPostType->name, $_oPostType->label ) ) {
                            $_aPostTypes[ $_oPostType->name ] = $_oPostType->label;
                        }
                    }
                    return array_diff_key( $_aPostTypes, array_flip( $aSlugsToRemove ) );

                }

        /**
         * @return      string
         * @deprecated
         */
     /*    private function _getTaxonomyToggleCheckbox( $aField ) {

            $_aCheckboxAttributes = array(
                'type'  => 'checkbox',
                'value' => 1,
                'name'  => $aField[ 'attributes' ][ 'name' ] . '[select_terms]',
                'id'    => $aField[ 'attributes' ][ 'id' ] . '_select_terms',
            );
            $_aHiddenAttributes = array(
                'type'   => 'hidden',
                'value'  => '0',
            ) + $_aCheckboxAttributes;
            return "<div class='select-taxonomy-terms-checkbox-container'>"
                    . "<label class='select-taxonomy-terms'>"
                        . "<input " . $this->getAttributes( $_aHiddenAttributes ) . " />"
                        . "<input " . $this->getAttributes( $_aCheckboxAttributes ) . " />"
                            . __( 'Select taxonomy terms.', 'admin-page-framework-loader' )
                    . "</label>"
                . "</div>";

        } */

}
endif;
