<?php
/**
 * Admin Page Framework - Demo
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds a section in a tab.
 * 
 * @package     AdminPageFramework
 * @subpackage  Example
 */
class APF_Demo_BuiltinFieldTypes_MISC_Hidden {
    
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_builtin_field_types';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'misc';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'hidden_field';
        
    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {
    
        // Section
        $oFactory->addSettingSections(
            $this->sPageSlug, // the target page slug                
            array(
                'tab_slug'          => $this->sTabSlug,
                'section_id'        => $this->sSectionID,
                'title'             => __( 'Hidden Fields', 'admin-page-framework-loader' ),
                'description'       => __( 'These are hidden fields.', 'admin-page-framework-loader' ),
            )
        );

        // Fields
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID        
            array( // Single Hidden Field
                'field_id'      => 'hidden_single',
                'title'         => __( 'Hidden Field', 'admin-page-framework-loader' ),
                'type'          => 'hidden',
                // 'hidden' =>    true // <-- the field row can be hidden with this option.
                'default'       => __( 'Test value', 'admin-page-framework-loader' ),
                'label'         => __( 'Test label', 'admin-page-framework-loader' ),
            ),
            array( // Single Hidden Field
                'field_id'      => 'hidden_repeatable',
                'title'         => __( 'Repeatable', 'admin-page-framework-loader' ),
                'type'          => 'hidden',
                'value'         => 'HIIDDENVALUE',
                'label'         => __( 'Repeat Me', 'admin-page-framework-loader' ),
                'repeatable'    => true,
            ),
            array( // Multiple Hidden Fields
                'field_id'      => 'hidden_miltiple',
                'title'         => __( 'Multiple', 'admin-page-framework-loader' ),
                'type'          => 'hidden',
                'label'         => $this->_getLabelByValue(
                    $oFactory->getValue( array( 'hidden_field', 'hidden_miltiple', 0 ), 'a' )
                ), // __( 'First Item', 'admin-page-framework-loader' ),
                'default'       => 'a',
                array(
                    // 'label'     => __( 'Second Item', 'admin-page-framework-loader' ),
                    'label'     => $this->_getLabelByValue(
                        $oFactory->getValue( array( 'hidden_field', 'hidden_miltiple', 1 ), 'b' )
                    ),
                    'default'   => 'b',
                ),
                array(
                    // 'label'     => __( 'Third Item', 'admin-page-framework-loader' ),
                    'label'     => $this->_getLabelByValue(
                        $oFactory->getValue( array( 'hidden_field', 'hidden_miltiple', 2 ), 'c' )
                    ),
                    'default'   => 'c',
                ),
                'sortable'      => true,
            )
        );
      
    }
    
        /**
         * 
         * @return      string
         */
        private function _getLabelByValue( $sValue ) {
            switch( $sValue ) {
                case 'a':
                    return 'Apple';
                case 'b':
                    return 'Banana';
                case 'c':
                    return 'Cherry';
                default:
                    return $sValue;
            }
        }

}
