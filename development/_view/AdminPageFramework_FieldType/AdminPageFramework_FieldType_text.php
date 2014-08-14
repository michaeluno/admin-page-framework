<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_FieldType_text' ) ) :
/**
 * Defines the text field type.
 * 
 * Also the field types of 'password', 'datetime', 'datetime-local', 'email', 'month', 'search', 'tel', 'url', and 'week' are defeined.
 * 
 * @package AdminPageFramework
 * @subpackage FieldType
 * @since 2.1.5
 * @internal
 */
class AdminPageFramework_FieldType_text extends AdminPageFramework_FieldType_Base {
    
    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'text', 'password', 'date', 'datetime', 'datetime-local', 'email', 'month', 'search', 'tel', 'url', 'week', );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * @remark $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'attributes' => array(
            'size' => 30,
            'maxlength' => 400,
        ),    
    );

    /**
     * Returns the field type specific CSS rules.
     */ 
    public function _replyToGetStyles() {
        return "/* Text Field Type */
                .admin-page-framework-field-text .admin-page-framework-field .admin-page-framework-input-label-container {
                    vertical-align: top; 
                }
            " . PHP_EOL;     
    }    
    
    /**
     * Returns the output of the text input field.
     * 
     * @since 2.1.5
     * @since 3.0.0 Removed unnecessary parameters.
     */
    public function _replyToGetField( $aField ) {

        return 
            $aField['before_label']
            . "<div class='admin-page-framework-input-label-container'>"
                . "<label for='{$aField['input_id']}'>"
                    . $aField['before_input']
                    . ( $aField['label'] && ! $aField['repeatable']
                        ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>" . $aField['label'] . "</span>"
                        : "" 
                    )
                    . "<input " . $this->generateAttributes( $aField['attributes'] ) . " />" // this method is defined in the base class
                    . $aField['after_input']
                    . "<div class='repeatable-field-buttons'></div>" // the repeatable field buttons will be replaced with this element.
                . "</label>"
            . "</div>"
            . $aField['after_label'];
        
    }
            
}
endif;