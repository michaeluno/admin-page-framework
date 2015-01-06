<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides helper methods that deal with custom submit fields and retrieve custom key elements.
 *
 * @abstract
 * @since       2.0.0
 * @remark      The classes that extend this include ExportOptions, ImportOptions, and Redirect.
 * @package     AdminPageFramework
 * @subpackage  Setting
 * @internal
 */
abstract class AdminPageFramework_CustomSubmitFields extends AdminPageFramework_WPUtility {
     
    public function __construct( $aPostElement ) {
            
        $this->aPost = $aPostElement;
        $this->sInputID = $this->getInputID( $aPostElement['submit'] ); // the submit element must be set by the field type.
    
    }
    
    /**
     * Retrieves the value of the specified element key.
     * 
     * The element key is either a single key or two keys. The two keys means that the value is stored in the second dimension.
     * 
     * @since   2.0.0
     * @since   3.4.0   Changed the name from `getElement()`.
     */ 
    protected function getSubmitValueByType( $aElement, $sInputID, $sElementKey='format' ) {
            
        return ( isset( $aElement[ $sInputID ][ $sElementKey ] ) )
            ? $aElement[ $sInputID ][ $sElementKey ]
            : null;
        
    }     
    
    /**
     * Returns the specified sibling value.
     * 
     * @since 2.1.5
     */
    public function getSiblingValue( $sKey ) {    
        return $this->getSubmitValueByType( $this->aPost, $this->sInputID, $sKey );
    }
    
    /**
     * Retrieves the submitted export/import button’s input id.
     * 
     * The input id should be consist of ({section id}_){field id}_{index}.
     * 
     * The getFieldID() is deprecated as multiple same field IDs will be possible in near updates.
     * 
     * @since 3.0.0
     */
    public function getInputID( $aSubmitElement ) {
            
        // Only the pressed element will be stored in the array.
        // The input tag: name="__import[submit][my_section_my_import_field_the_index]" value="Import Button"
        // The array structure:  array( 'my_section_my_import_field_the_index' => 'Import Button' )
        foreach( $aSubmitElement as $sInputID => $v ) { // $aSubmitElement should have been set in the constructor.
            $this->sInputID = $sInputID;
            return $this->sInputID;
        }     
        
    }
        
}