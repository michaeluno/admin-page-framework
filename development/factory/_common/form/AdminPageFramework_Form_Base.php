<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides shared methods for the form class.
 * 
 * @package     AdminPageFramework
 * @subpackage  Common/Form
 * @since       3.7.0
 * @internal
 */
abstract class AdminPageFramework_Form_Base extends AdminPageFramework_Form_Utility {
    
    /**
     * Stores resource items. 
     * 
     * @internal
     */
    static public $_aResources = array(
        'internal_styles'    => array(),
        'internal_styles_ie' => array(),
        'internal_scripts'   => array(),
        'src_styles'         => array(),
        'src_scripts'        => array(),
    );    
    
    /**
     * Checks if a given array holds fieldsets or not.
     * 
     * @todo        It seems this method is not used. If so deprecate it.
     * @return      boolean
     */
    // public function isFieldsets( array $aItems ) {
        // $_aItem = $this->getFirstElement( $aItems );
        // return isset( $_aItem[ 'field_id' ], $_aItem[ 'section_id' ] );
    // }
    
    /**
     * Determines whether the given ID is of a registered form section.
     * 
     * Consider the possibility that the given ID may be used both for a section and a field.
     * 
     * 1. Check if the given ID is not a section.
     * 2. Parse stored fields and check their ID. If one matches, return false.
     * 
     * @since       3.0.0
     * @since       3.7.0      Moved from `AdminPageFramework_FormDefinition_Base`.
     */
    public function isSection( $sID ) {
// @todo Find a way for nested sections.        
        // Integer IDs are not accepted as they are reserved for sub-sections.
        if ( $this->isNumericInteger( $sID ) ) {
            return false;
        }
        
        // If the section ID is not registered, return false.
        if ( ! array_key_exists( $sID, $this->aSectionsets ) ) { 
            return false; 
        }
        
        // the fields array's first dimension is also filled with the keys of section ids.
        if ( ! array_key_exists( $sID, $this->aFieldsets ) ) { 
            return false; 
        }
        
        // Since numeric IDs are denied at the beginning of the method, the elements will not be sub-sections.
        $_bIsSeciton = false;
        foreach( $this->aFieldsets as $_sSectionID => $_aFields ) {    
        
            if ( $_sSectionID == $sID ) { 
                $_bIsSeciton = true; 
            }
            
            // a field using the ID is found, and it precedes a section match.     
            if ( array_key_exists( $sID, $_aFields ) ) { 
                return false; 
            }
            
        }
        
        return $_bIsSeciton;
        
    }        
    
    /**
     * Decides whether the current user including guests can view the form or not.
     * 
     * To allow guests to view the form set an empty value to it.
     * 
     * @since       3.7.0
     * @return      boolean
     */
    public function canUserView( $sCapability ) {
        
        if ( ! $sCapability  ) {
            return true;
        }
        
        return ( boolean ) current_user_can( $sCapability );
        
    }

    /**
     * Decides whether the form elements should be registered or not.
     * 
     * @access      public      A delegation class accesses this method so it must be public.
     * @since       3.7.0
     * @return      boolean
     */
    public function isInThePage() {
        return $this->callBack(
            $this->aCallbacks[ 'is_in_the_page' ], 
            true
        );
    }    
    
    /**
     * Prevents the output from getting too long when the object is dumped.
     *
     * Field definition arrays contain the factory object reference and when the debug log method tries to dump it, the output gets too long.
     * So shorten it here.
     * 
     * @remark      Called when the object is called as a string.
     * @since       3.7.0
     */   
    public function __toString() {
        return $this->getObjectInfo( $this );        
    }
    
}
