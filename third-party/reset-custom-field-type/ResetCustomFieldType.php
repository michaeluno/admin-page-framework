<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'ResetCustomFieldType' ) ) :
/**
 * Defines the reset field type.
 * 
 * @package     AdminPageFramework
 * @subpackage  FieldType
 * @since       3.2.2
 * @internal
 */
class ResetCustomFieldType extends AdminPageFramework_FieldType {
    
   
    /**
     * Defines the field type slugs used for this field type.
     * 
     * The slug is used for the type key in a field definition array.
     * <code>$this->addSettingFields(
     *      array(
     *          'section_id'    => '...',
     *          'type'          => 'reset',        // <--- THIS PART
     *          'field_id'      => '...',
     *          'title'         => '...',
     *      )
     *  );</code>
     */
    public $aFieldTypeSlugs = array( 'reset', );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * The keys are used for the field definition array.
     * <code>$this->addSettingFields(
     *      array(
     *          'section_id'    => '...',    
     *          'type'          => '...',
     *          'field_id'      => '...',
     *          'my_custom_key' => '...',    // <-- THIS PART
     *      )
     *  );</code>
     * @remark            $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'attributes'    => array(
            'class' => 'button button-secondary',
        ),  
    );

    /**
     * The user constructor.
     * 
     * Loaded at the end of the constructor.
     */
    protected function construct() {}
        
    /**
     * Loads the field type necessary components.
     * 
     * This method is triggered when a field definition array that calls this field type is parsed. 
     */ 
    protected function setUp() {}    

    /**
     * Returns an array holding the urls of enqueuing scripts.
     * 
     * The returning array should be composed with all numeric keys. Each element can be either a string( the url or the path of the source file) or an array of custom argument.
     * 
     * <h4>Custom Argument Array</h4>
     * <ul>
     *     <li><strong>src</strong> - ( required, string ) The url or path of the target source file</li>
     *     <li><strong>handle_id</strong> - ( optional, string ) The handle ID of the script.</li>
     *     <li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
     *     <li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
     *     <li><strong>translation</strong> - ( optional, array ) The translation array. The handle ID will be used for the object name.</li>
     *     <li><strong>in_footer</strong> - ( optional, boolean ) Whether to enqueue the script before < / head > or before < / body > Default: <code>false</code>.</li>
     * </ul>     
     */
    protected function getEnqueuingScripts() { 
        return array();
    }
    
    /**
     * Returns an array holding the urls of enqueuing styles.
     * 
     * <h4>Custom Argument Array</h4>
     * <ul>
     *     <li><strong>src</strong> - ( required, string ) The url or path of the target source file</li>
     *     <li><strong>handle_id</strong> - ( optional, string ) The handle ID of the stylesheet.</li>
     *     <li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
     *     <li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
     *     <li><strong>media</strong> - ( optional, string ) the description of the field which is inserted into the after the input field tag.</li>
     * </ul>
     */
    protected function getEnqueuingStyles() { 
        return array();
    }            


    /**
     * Returns the field type specific JavaScript script.
     */ 
    protected function getScripts() { 

        $_aJSArray = json_encode( $this->aFieldTypeSlugs );
        /*    
         * The below function will be triggered when a new repeatable field is added. 
         * 
         * Use the registerAPFCallback method to register a callback.
         * Available callbacks are:
         *     added_repeatable_field - triggered when a repeatable field gets repeated. Parameters 1. (object) the jQuery element object. 2. (string) the field type slug. 3. (string) the field tag id.
         *     removed_repeatable_field - triggered when a repeatable field gets removed. Parameters 1. (object) the jQuery element object. 2. (string) the field type slug. 3. (string) the field tag id.
         *     sorted_fields - triggered when a sortable field gets sorted. Parameters 1. (object) the jQuery element object. 2. (string) the field type slug. 3. (string) the field tag id.
         * */
        return "" . PHP_EOL;
        
    }

    /**
     * Returns IE specific CSS rules.
     */
    protected function getIEStyles() { return ''; }

    /**
     * Returns the field type specific CSS rules.
     */ 
    protected function getStyles() {
        return "";
        
    }

    
    /**
     * Returns the output of the geometry custom field type.
     * 
     */
    /**
     * Returns the output of the field type.
     */
    protected function getField( $aField ) { 

        $_aButtonAttributes = array(
            'type'    => 'reset',
        ) + $aField['attributes'];
        $_aButtonAttributes['class'] .= ' reset';

        $_aLabelContainerAttributes = array(
            'style' => $aField['label_min_width'] ? "min-width:" . $this->sanitizeLength( $aField['label_min_width'] ) . ";" : null,
            'class' => 'admin-page-framework-input-label-container admin-page-framework-input-button-container admin-page-framework-input-container',
        );
        
        return 
            $aField['before_label']
            . "<div " . $this->generateAttributes( $_aLabelContainerAttributes ) . ">"
                . "<label for='{$aField['input_id']}'>"
                    . $aField['before_input']            
                    . "<button " . $this->generateAttributes( $_aButtonAttributes ) . " >" 
                        . ( $aField['label'] ? $aField['label'] : $this->oMsg->get( 'reset' ) )
                    . "</button>"
                    . $aField['after_input']
                . "</label>"
            . "</div>"
            . $aField['after_label'];
        
    }    
         
}
endif;