<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_FieldType_taxonomy' ) ) :
/**
 * Defines the taxonomy field type.
 * 
 * @package         AdminPageFramework
 * @subpackage      FieldType
 * @since           2.1.5
 * @since           3.3.1       Changed to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
 */
class AdminPageFramework_FieldType_taxonomy extends AdminPageFramework_FieldType {
    
    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'taxonomy', );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * @remark  $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'taxonomy_slugs'        => 'category',      // (array|string) This is for the taxonomy field type.
        'height'                => '250px',         // 
        'max_width'             => '100%',          // for the taxonomy checklist field type, since 2.1.1.     
        'show_post_count'       => true,            // (boolean) 3.2.0+ whether or not the post count associated with the term should be displayed or not.
        'attributes'            => array(),    
        'select_all_button'     => true,            // (boolean|string) 3.3.0+ to change the label, set the label here
        'select_none_button'    => true,            // (boolean|string) 3.3.0+ to change the label, set the label here                
    );
    
    /**
     * Loads the field type necessary components.
     * 
     * @since       2.1.5  
     * @since       3.3.1       Changed from `_replyToFieldLoader()`.
     */ 
    protected function setUp() {
        new AdminPageFramework_Script_CheckboxSelector;
    }
    
    /**
     * Returns the field type specific JavaScript script.
     * 
     * Returns the JavaScript script of the taxonomy field type.
     * 
     * @since       2.1.1
     * @since       2.1.5       Moved from `AdminPageFramework_Property_Base()`.
     * @since       3.3.1       Changed from `_replyToGetScripts()`.
     */ 
    protected function getScripts() {
        
        $aJSArray = json_encode( $this->aFieldTypeSlugs );
        return "
            /* For tabs */
            var enableAPFTabbedBox = function( nodeTabBoxContainer ) {
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
                     
                enableAPFTabbedBox( jQuery( '.tab-box-container' ) );

                /* The repeatable event */
                jQuery().registerAPFCallback( {     
                    /**
                     * The repeatable field callback for the add event.
                     * 
                     * @param object node
                     * @param string    the field type slug
                     * @param string    the field container tag ID
                     * @param integer    the caller type. 1 : repeatable sections. 0 : repeatable fields.
                     */     
                    added_repeatable_field: function( oClonedField, sFieldType, sFieldTagID, iCallType ) {
            
                        /* If it is not the color field type, do nothing. */
                        if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) { return; }

                        oClonedField.nextAll().andSelf().each( function() {     
                            jQuery( this ).find( 'div' ).incrementIDAttribute( 'id' );
                            jQuery( this ).find( 'li.tab-box-tab a' ).incrementIDAttribute( 'href' );
                            jQuery( this ).find( 'li.category-list' ).incrementIDAttribute( 'id' );
                            enableAPFTabbedBox( jQuery( this ).find( '.tab-box-container' ) );
                        });     
                        
                    },
                    /**
                     * The repeatable field callback for the remove event.
                     * 
                     * @param object    the field container element next to the removed field container.
                     * @param string    the field type slug
                     * @param string    the field container tag ID
                     * @param integer    the caller type. 1 : repeatable sections. 0 : repeatable fields.
                     */     
                    removed_repeatable_field: function( oNextFieldConainer, sFieldType, sFieldTagID, iCallType ) {
            
                        /* If it is not the color field type, do nothing. */
                        if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) { return; }
    
                        oNextFieldConainer.nextAll().andSelf().each( function() {
                            jQuery( this ).find( 'div' ).decrementIDAttribute( 'id' );
                            jQuery( this ).find( 'li.tab-box-tab a' ).decrementIDAttribute( 'href' );
                            jQuery( this ).find( 'li.category-list' ).decrementIDAttribute( 'id' );
                        });    
                                                
                    },     
                });
            });     
        ";
    }
    
    /**
     * Returns the field type specific CSS rules.
     * 
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetStyles()`.
     */ 
    protected function getStyles() {
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
                white-space: nowrap;     
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
            /* .admin-page-framework-field .tab-box-tab {     
                vertical-align: top;
            } */
            .admin-page-framework-field .tab-box-tabs .tab-box-tab.active {
                display: inline;
                border-color: #dfdfdf #dfdfdf #fff;
                margin-bottom: 0px;
                padding-bottom: 2px;
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
        " . PHP_EOL;
    }
    
    /**
     * Returns the field type specific CSS rules.
     * 
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetInputIEStyles()`.
     */ 
    protected function getIEStyles() {
        return     ".tab-box-content { display: block; }
            .tab-box-contents { overflow: hidden;position: relative; }
            b { position: absolute; top: 0px; right: 0px; width:1px; height: 251px; overflow: hidden; text-indent: -9999px; }
        ";    

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
     */
    protected function getField( $aField ) {

        $aTabs          = array();
        $aCheckboxes    = array();
        
        $_aCheckboxContainerAttributes = array(
            'class'                     => 'admin-page-framework-checkbox-container',
            'data-select_all_button'    => $aField['select_all_button'] 
                ? ( ! is_string( $aField['select_all_button'] ) ? $this->oMsg->get( 'select_all' ) : $aField['select_all_button'] )
                : null,
            'data-select_none_button'   => $aField['select_none_button'] 
                ? ( ! is_string( $aField['select_none_button'] ) ? $this->oMsg->get( 'select_none' ) : $aField['select_none_button'] )
                : null,
        );        
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
                    . "<div " . $this->generateAttributes( $_aCheckboxContainerAttributes ) . "></div>"
                    . "<ul class='list:category taxonomychecklist form-no-clear'>"
                        . wp_list_categories( 
                            array(
                                'walker'            => new AdminPageFramework_WalkerTaxonomyChecklist, // the walker class instance
                                'name'              => is_array( $aField['taxonomy_slugs'] ) ? "{$aField['_input_name']}[{$sTaxonomySlug}]" : $aField['_input_name'],   // name of the input
                                'selected'          => $this->_getSelectedKeyArray( $aField['value'], $sTaxonomySlug ),         // checked items ( term IDs ) e.g.  array( 6, 10, 7, 15 ), 
                                'title_li'          => '',                          // disable the Categories heading string 
                                'hide_empty'        => 0,    
                                'echo'              => false,                       // returns the output
                                'taxonomy'          => $sTaxonomySlug,              // the taxonomy slug (id) such as category and post_tag 
                                'input_id'          => $aField['input_id'],
                                'attributes'        => $aInputAttributes,
                                'show_post_count'   => $aField['show_post_count'],  // 3.2.0+
                            ) 
                        )     
                    . "</ul>"     
                    . "<!--[if IE]><b>.</b><![endif]-->"
                    . $this->getFieldElementByKey( $aField['after_label'], $sKey )
                . "</div>";
        }

        $sTabs      = "<ul class='tab-box-tabs category-tabs'>" . implode( PHP_EOL, $aTabs ) . "</ul>";
        $sContents  = 
            "<div class='tab-box-contents-container'>"
                . "<div class='tab-box-contents' style='height: {$aField['height']};'>"
                    . implode( PHP_EOL, $aCheckboxes )
                . "</div>"
            . "</div>";
            
        return ''
            . "<div id='tabbox-{$aField['field_id']}' class='tab-box-container categorydiv' style='max-width:{$aField['max_width']};'>"
                . $sTabs . PHP_EOL
                . $sContents . PHP_EOL
            . "</div>"
            ;
                
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
         */ 
        private function _getSelectedKeyArray( $vValue, $sTaxonomySlug ) {

            $vValue = ( array ) $vValue; // cast array because the initial value (null) may not be an array.
            
            if ( ! isset( $vValue[ $sTaxonomySlug ] ) ) { return array(); }
            if ( ! is_array( $vValue[ $sTaxonomySlug ] ) ) { return array(); }
            
            return array_keys( $vValue[ $sTaxonomySlug ], true );
        
        }
    
        /**
         * Retrieves the label of the given taxonomy by its slug.
         * 
         * A helper function for the above getTaxonomyChecklistField() method.
         * 
         * @since 2.1.1
         */
        private function _getLabelFromTaxonomySlug( $sTaxonomySlug ) {
            
            $oTaxonomy = get_taxonomy( $sTaxonomySlug );
            return isset( $oTaxonomy->label )
                ? $oTaxonomy->label
                : null;
            
        }
    
}
endif;