<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_FieldType_posttype' ) ) :
/**
 * Defines the posttype field type.
 * 
 * @package     AdminPageFramework
 * @subpackage  FieldType
 * @since       2.1.5
 * @internal
 */
class AdminPageFramework_FieldType_posttype extends AdminPageFramework_FieldType_checkbox {

    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'posttype', );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * @remark $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'slugs_to_remove'   => null, // the default array will be assigned in the rendering method.
        'attributes'        => array(
            'size'      => 30,
            'maxlength' => 400,
        ),    
    );
    protected $aDefaultRemovingPostTypeSlugs = array(
        'revision', 
        'attachment', 
        'nav_menu_item',
    );
    /**
     * Loads the field type necessary components.
     */ 
    public function _replyToFieldLoader() {
    }    
    
    /**
     * Returns the field type specific JavaScript script.
     */ 
    public function _replyToGetScripts() {
        return "";     
    }    

    /**
     * Returns the field type specific CSS rules.
     */ 
    public function _replyToGetStyles() {
        return "/* Posttype Field Type */
            .admin-page-framework-field input[type='checkbox'] {
                margin-right: 0.5em;
            }     
            .admin-page-framework-field-posttype .admin-page-framework-input-label-container {
                padding-right: 1em;
            }    
        ";     
    }
    
    /**
     * Returns the output of the field type.
     * 
     * Returns the output of post type checklist check boxes.
     * 
     * @remark  the posttype checklist field does not support multiple elements by passing an array of labels.
     * @since   2.0.0
     * @since   2.1.5 Moved from AdminPageFramework_FormField.
     * @since   3.0.0 Reconstructed entirely.
     */
    public function _replyToGetField( $aField ) {
        
        $this->_sCheckboxClassSelector = '';    // disable the checkbox class selector.
        $aField['label'] = $this->_getPostTypeArrayForChecklist( isset( $aField['slugs_to_remove'] ) ? $aField['slugs_to_remove'] : $this->aDefaultRemovingPostTypeSlugs );
        return parent::_replyToGetField( $aField );
            
    }    
    
        /**
         * A helper function for the above getPosttypeChecklistField method.
         * 
         * @since 2.0.0
         * @since 2.1.1 Changed the returning array to have the labels in its element values.
         * @since 2.1.5 Moved from AdminPageFramework_InputTag.
         * @return array The array holding the elements of installed post types' labels and their slugs except the specified expluding post types.
         */ 
        private function _getPostTypeArrayForChecklist( $aRemoveNames, $aPostTypes=array() ) {
            
            foreach( get_post_types( '','objects' ) as $oPostType ) {
                if (  isset( $oPostType->name, $oPostType->label ) ) {
                    $aPostTypes[ $oPostType->name ] = $oPostType->label;
                }
            }

            return array_diff_key( $aPostTypes, array_flip( $aRemoveNames ) );    

        }     
    
}
endif;