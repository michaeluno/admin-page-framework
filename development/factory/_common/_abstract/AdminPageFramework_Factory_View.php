<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods for views.
 * 
 * @abstract
 * @since       3.0.4
 * @package     AdminPageFramework
 * @subpackage  Factory
 * @internal
 */
abstract class AdminPageFramework_Factory_View extends AdminPageFramework_Factory_Model {
    
    /**
     * Sets up hooks and properties.
     * 
     * @internal
     */
    public function __construct( $oProp ) {
        
        parent::__construct( $oProp );

        new AdminPageFramework_Factory_View__SettingNotice( 
            $this,
            $this->oProp->sSettingNoticeActionHook
        );
        
    }     

        /**
         * Returns the name attribute value of form sections.
         * @internal    
         * @since       3.6.0
         * @return      string      the input id attribute
         */    
        public function _replyToGetSectionName( /* $sSectionName, $aSectionset */ ) {
            $_aParams = func_get_args() + array( null, null, );
            return $_aParams[ 0 ];
        }
        
        /**
         * @internal    
         * @since       3.5.7
         * @return      string      the input id attribute
         */    
        public function _replyToGetInputID( /* $sInputIDAttribute, $aField, $sKey, $iSectionIndex */ ) {
            $_aParams = func_get_args() + array( null, null, null, null );
            return $_aParams[ 0 ];
        }
        /**
         * @internal    
         * @since       3.5.7
         * @return      string      the fields & fieldset & field row container id attribute
         */    
        public function _replyToGetInputTagIDAttribute( /* $sTagIDAttribute, $aFiel, $sKey, $iSectionIndex */ ) {
            $_aParams = func_get_args() + array( null, null, null, null );
            return $_aParams[ 0 ];
        }
        
        /**
         * @internal
         * @since       3.6.0
         * @return      string
         */
        public function _replyToGetFieldNameAttribute( /* $sFieldName, $aFieldset */ )  {            
            $_aParams = func_get_args() + array( null, null, );
            return $_aParams[ 0 ];                        
        }
        /**
         * 
         * @internal
         * @since       3.6.0
         * @return      string
         */        
        public function _replyToGetFlatFieldName( /* $sFieldName, $aFieldset */ ) {            
            $_aParams = func_get_args() + array( null, null, );
            return $_aParams[ 0 ];            
        }
        
        /**
         * Generates a name attribute value for a form input element.
         * @internal    
         * @since       3.5.7
         * @return      string      the input name attribute
         */    
        public function _replyToGetInputNameAttribute( /* $sNameAttribute, $aField, $sKey */ ) {
            $_aParams = func_get_args() + array( null, null, null );
            return $_aParams[ 0 ];
        }
        
        /**
         * Generates a flat input name whose dimensional element keys are delimited by the pipe (|) character.
         * @internal    
         * @since       3.5.7
         * @return      string      the flat input name attribute
         */    
        public function _replyToGetFlatInputName( /* $sFlatNameAttribute, $aField, $sKey */ ) {
            $_aParams   = func_get_args() + array( null, null, null );
            return $_aParams[ 0 ];
        }

        /**
         * 
         * @internal    
         * @since       3.5.7
         * @return      string      the input class attribute.
         */
        public function _replyToGetInputClassAttribute( /* $sClsssAttribute, $aField, $sKey, $iSectionIndex */ ) {
            $_aParams = func_get_args() + array( null, null, null, null );
            return $_aParams[ 0 ];
        }
            
            
    /**
     * Determines whether the passed field should be visible or not.
     * @since       3.7.0
     * @return      boolean
     */
    public function _replyToDetermineSectionsetVisibility( $bVisible, $aSectionset ) {
        return $this->_isElementVisible( $aSectionset, $bVisible );       
    }    
    /**
     * Determines whether the passed field should be visible or not.
     * @since       3.7.0
     * @return      boolean
     */
    public function _replyToDetermineFieldsetVisibility( $bVisible, $aFieldset ) {
        return $this->_isElementVisible( $aFieldset, $bVisible );        
    }     
        /**
         * @since       3.7.0
         * @return      boolean
         */
        private function _isElementVisible( $aElementDefinition, $bDefault ) {
            
            $aElementDefinition = $aElementDefinition + array(
                'if'            => true,
                'capability'    => '',
            );
            if ( ! $aElementDefinition[ 'if' ] ) {
                return false;
            }
            // For front-end forms that allow guests, the capability level can be empty. In that case, return true.
            if ( ! $aElementDefinition[ 'capability' ] ) {
                return true;
            }
            if ( ! current_user_can( $aElementDefinition[ 'capability' ] ) ) {
                return false;
            }            
            return $bDefault;
            
        }
            
    /**
     * Checks whether a section is set.
     * @internal
     * @since       3.5.7       Moved from `AdminPageFramework_FormField`.
     * @param       array       $aFieldset     a fieldset definition array.
     * @return      boolean
     */
    public function isSectionSet( array $aFieldset ) {
        $aFieldset = $aFieldset + array(
            'section_id'  => null,
        );
        return $aFieldset[ 'section_id' ] && '_default' !== $aFieldset[ 'section_id' ];
    }

    /**
     * Returns the output of the filtered section description.
     * 
     * @remark      An alternative to `_renderSectionDescription()`.
     * @since       3.0.0
     * @since       3.3.1       Moved from `AdminPageFramework_Setting_Base`.
     * @since       3.7.0      Moved from extended factory classes.
     * @callback    form        `section_head_output`
     * @internal
     */
    public function _replyToGetSectionHeaderOutput( $sSectionDescription, $aSectionset ) {
        return $this->oUtil->addAndApplyFilters(
            $this,
            array( 
                // section_{instantiated class name}_{section id}
                'section_head_' . $this->oProp->sClassName . '_' . $aSectionset[ 'section_id' ] 
            ), 
            $sSectionDescription
        );
    }            
    
    /**
     * Returns the field output from the given field definition array.
     * 
     * @remark      This method will be called multiple times in a single page load depending on how many fields have been registered.
     * @since       3.0.0
     * @since       3.7.0      Changed the pamater strcucture. The first parametr no longer receives a fieldset definition array but the generated output string.
     * @callback    form        `fieldset_output`
     * @internal
     */
    public function _replyToGetFieldOutput( $sFieldOutput, $aFieldset ) {

        $_sSectionPart  = $this->oUtil->getAOrB(
            isset( $aFieldset[ 'section_id' ] ) && '_default' !== $aFieldset[ 'section_id' ],
            '_' . $aFieldset[ 'section_id' ],
            ''
        );
        return $this->oUtil->addAndApplyFilters(
            $this,
            array( 
                'field_' . $this->oProp->sClassName . $_sSectionPart . '_' . $aFieldset[ 'field_id' ]
            ),
            $sFieldOutput,
            $aFieldset // the field array
        );             
        
    }    
        
    /**
     * The content filter method,
     * 
     * The user may just override this method instead of defining a `content_{...}` callback method.
     * 
     * @since       3.4.1
     * @remark      Declare this method in each factory class as the form of parameters varies and if parameters are different, it triggers PHP strict standard warnings.
     * @param       string      $sContent       The filtering content string.
     */
    // public function content( $sContent ) {
        // return $sContent;
    // }            
    
}
