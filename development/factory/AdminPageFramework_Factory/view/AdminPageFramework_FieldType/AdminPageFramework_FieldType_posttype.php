<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Defines the posttype field type.
 * 
 * @package         AdminPageFramework
 * @subpackage      FieldType
 * @since           2.1.5
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
        'slugs_to_remove'       => null,    // the default array will be assigned in the rendering method.
        /** 
         * Accepts query arguments. For the specification, see the arg parameter of get_post_types() function.
         * See: http://codex.wordpress.org/Function_Reference/get_post_types#Parameters
         */
        'query'                 => array(),  // 3.2.1+
        'operator'              => 'and',    // 3.2.1+ either 'and' or 'or'
        'attributes'            => array(
            'size'      => 30,
            'maxlength' => 400,
        ),    
        'select_all_button'     => true,     // 3.3.0+   to change the label, set the label here
        'select_none_button'    => true,     // 3.3.0+   to change the label, set the label here        
    );
    protected $aDefaultRemovingPostTypeSlugs = array(
        'revision', 
        'attachment', 
        'nav_menu_item',
    );
    /**
     * Returns the field type specific CSS rules.
     * 
     * @since       2.1.5
     * @since       3.3.1       Changed from `_replyToGetStyles()`.
     */ 
    protected function getStyles() {
        $_sParentStyles = parent::getStyles();
        return $_sParentStyles . <<<CSSRULES
/* Posttype Field Type */
.admin-page-framework-field input[type='checkbox'] {
    margin-right: 0.5em;
}     
.admin-page-framework-field-posttype .admin-page-framework-input-label-container {
    padding-right: 1em;
}    
CSSRULES;

    }
    
    /**
     * Returns the output of the field type.
     * 
     * Returns the output of post type checklist check boxes.
     * 
     * @remark      the posttype checklist field does not support multiple elements by passing an array of labels.
     * @since       2.0.0
     * @since       2.1.5       Moved from AdminPageFramework_FormField.
     * @since       3.0.0       Reconstructed entirely.
     * @since       3.3.1       Changed from `_replyToGetField()`.
     */
    protected function getField( $aField ) {
        
        $this->_sCheckboxClassSelector = '';    // disable the checkbox class selector.
        $aField['label'] = $this->_getPostTypeArrayForChecklist( 
            isset( $aField['slugs_to_remove'] ) ? $this->getAsArray( $aField['slugs_to_remove'] ) : $this->aDefaultRemovingPostTypeSlugs,    // slugs to remove
            $aField['query'],
            $aField['operator']
        );
        return parent::getField( $aField );
            
    }    
    
        /**
         * A helper function for the above getPosttypeChecklistField method.
         * 
         * @since   2.0.0
         * @since   2.1.1   Changed the returning array to have the labels in its element values.
         * @since   2.1.5   Moved from AdminPageFramework_InputTag.
         * @since   3.2.1   Added the $asQueryArgs and $sOperator parameters.
         * @param   $aSlugsToRemove     array   The slugs to remove from the result.
         * @param   $asQueryArgs        array   The query argument.
         * @param   $sOperator          array   The query operator.
         * @return  array   The array holding the elements of installed post types' labels and their slugs except the specified expluding post types.
         */ 
        private function _getPostTypeArrayForChecklist( $aSlugsToRemove, $asQueryArgs=array(), $sOperator='and' ) {
            
            $_aPostTypes = array();
            foreach( get_post_types( $asQueryArgs, 'objects' ) as $_oPostType ) {
                if (  isset( $_oPostType->name, $_oPostType->label ) ) {
                    $_aPostTypes[ $_oPostType->name ] = $_oPostType->label;
                }
            }
            return array_diff_key( $_aPostTypes, array_flip( $aSlugsToRemove ) );    

        }     
    
}