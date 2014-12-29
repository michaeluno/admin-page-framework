<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Defines the section_tab field type.
 * 
 * When a field is defined with this field type, the section title will be replaced with this field. This is used for repeatable tabbed sections.
 * 
 * @package     AdminPageFramework
 * @subpackage  FieldType
 * @since       3.0.0
 * @since       3.3.1       Changed to extend `AdminPageFramework_FieldType` from `AdminPageFramework_FieldType_Base`.
 * @internal
 */
class AdminPageFramework_FieldType_section_title extends AdminPageFramework_FieldType {
    
    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'section_title', );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * @remark $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'label_min_width'   => 30,
        'attributes'        => array(
            'size'      => 20,
            'maxlength' => 100,
        ),    
    );

    /**
     * Returns the field type specific CSS rules.
     * 
     * @since       3.0.0
     * @since       3.3.1       Changed from `_replyToGetStyles()`.
     */ 
    protected function getStyles() {
        return <<<CSSRULES
/* Section Tab Field Type */
.admin-page-framework-section-tab .admin-page-framework-field-section_title {
    padding: 0.5em;
}
 .admin-page-framework-section-tab .admin-page-framework-field-section_title .admin-page-framework-input-label-string {     
    vertical-align: middle; 
} 
 .admin-page-framework-section-tab .admin-page-framework-fields {
    display: inline-block;
} 
.admin-page-framework-field.admin-page-framework-field-section_title {
    float: none;
} 
.admin-page-framework-field.admin-page-framework-field-section_title input {
    background-color: #fff;
    color: #333;
    border-color: #ddd;
    box-shadow: inset 0 1px 2px rgba(0,0,0,.07);
    border-width: 1px;
    border-style: solid;
    outline: 0;
    box-sizing: border-box;
    vertical-align: middle;
}
CSSRULES;
   
    }    
    
    /**
     * Returns the output of the text input field.
     * 
     * @since       2.1.5
     * @since       3.0.0     Removed unnecessary parameters.
     * @since       3.3.1     Changed from `_replyToGetField()`.
     */
    protected function getField( $aField ) {

        return 
            $aField['before_label']
            . "<div class='admin-page-framework-input-label-container'>"
                . "<label for='{$aField['input_id']}'>"
                    . $aField['before_input']
                    . ( $aField['label'] && ! $aField['repeatable']
                        ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>" . $aField['label'] . "</span>"
                        : "" 
                    )
                    . "<input " . $this->generateAttributes( array( 'type' => 'text' ) + $aField['attributes'] ) . " />" // this method is defined in the base class
                    . $aField['after_input']
                    . "<div class='repeatable-field-buttons'></div>" // the repeatable field buttons will be replaced with this element.
                . "</label>"
            . "</div>"
            . $aField['after_label'];
        
    }
            
}