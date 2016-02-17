<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to format form each section definition arrays.
 * 
 * @remark      It is assumed the passed section definition array is already formatted with the .AdminPageFramework_Form_Model___FormatSectionset.
 * 
 * @package     AdminPageFramework
 * @subpackage  Format
 * @since       3.6.0
 * @internal
 * @extends     AdminPageFramework_FrameworkUtility
 */
class AdminPageFramework_Form_Model___Format_EachSection extends AdminPageFramework_FrameworkUtility {
    
    /**
     * Represents the structure of the form section definition array.
     * 
     * @since       3.6.0       Moved from `AdminPageFramework_FormDefinition`.
     * @var         array       Represents the array structure of form section definition.
     * @static
     */
    static public $aStructure = array(
        '_count_subsections' => 0,
        '_is_first_index'    => false,
        '_is_last_index'     => false,
        '_index'             => null,
        
        '_is_collapsible'    => false, // (boolean)

        '_tag_id'            => '',
        '_tag_id_model'      => '',
        
        '_sections_id'       => '',
    );
    
    /**
     * Stores the section definition.
     */
    public $aSection            = array();
    
    /**
     * Indicates the sub section index.
     * 
     * @remark      set 'null' as default as no index will be set for sections without sub-sections.
     */
    public $iIndex              = null;
    
    public $aSubSections        = array();
    
    public $sSectionsID         = '';
    
    /**
     * Sets up properties.
     */
    public function __construct( /* array $aSection, $iIndex, $sSectionsID */ ) {
        
        $_aParameters = func_get_args() + array(
            $this->aSection,
            $this->iIndex,
            $this->aSubSections,
            $this->sSectionsID,
        );
        $this->aSection             = $_aParameters[ 0 ];
        $this->iIndex               = $_aParameters[ 1 ];
        $this->aSubSections         = $_aParameters[ 2 ];
        $this->sSectionsID          = $_aParameters[ 3 ];


    }
    
    /**
     * Returns an formatted definition array.
     * 
     * @return      array       The formatted definition array.
     */
    public function get() {
        
        $_aSection = $this->aSection + self::$aStructure;
            
        $_aSection[ '_index' ]             = $this->iIndex;
        $_aSection[ '_count_subsections' ] = count( $this->aSubSections );
        $_aSection[ '_is_first_index' ]    = $this->isFirstElement( $this->aSubSections, $this->iIndex );
        $_aSection[ '_is_last_index' ]     = $this->isLastElement( $this->aSubSections, $this->iIndex );
        
        $_aSection[ '_is_collapsible' ]    = $_aSection[ 'collapsible' ] && 'section' === $_aSection[ 'collapsible' ][ 'container' ];
        
        $_aSection[ '_tag_id' ]            = 'section-' . $_aSection[ 'section_id' ] . '__' . $this->iIndex;
        $_aSection[ '_tag_id_model' ]      = 'section-' . $_aSection[ 'section_id' ] . '__' . '___i___';
        
        return $_aSection;
        
    }
           
}
