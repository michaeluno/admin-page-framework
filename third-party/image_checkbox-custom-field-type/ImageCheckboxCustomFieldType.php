<?php
if ( ! class_exists( 'ImageCheckboxCustomFieldType' ) ) :
/**
 * Defines the image_checkbox field type.
 * 
 * @package     AdminPageFramework
 * @subpackage  FieldType
 * @since       3.2.1
 * @internal
 */
class ImageCheckboxCustomFieldType extends AdminPageFramework_FieldType {
        
    /**
     * Defines the field type slugs used for this field type.
     * 
     * The slug is used for the type key in a field definition array.
     * <code>$this->addSettingFields(
     *      array(
     *          'section_id'    => ...,
     *          'type'          => 'image_checkbox',        // <--- THIS PART
     *          'field_id'      => ...,
     *          'title'         => ...,
     *      )
     *  );</code>
     */
    public $aFieldTypeSlugs = array( 'image_checkbox', );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * The keys are used for the field definition array.
     * <code>$this->addSettingFields(
     *      array(
     *          'section_id'    => ...,
     *          'type'          => ...,
     *          'field_id'      => ...,
     *          'my_custom_key' => ...,    // <-- THIS PART
     *      )
     *  );</code>
     * @remark            $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'label_min_width'   => null,
        'width'             => 64,
        'height'            => 64,
        'attributes'        => array(),    
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

        $aJSArray = json_encode( $this->aFieldTypeSlugs );
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
        return "
.admin-page-framework-field-image_checkbox input[type='checkbox'] {
    display: none;
} 
.admin-page-framework-field-image_checkbox .admin-page-framework-input-label-string {
    display: none;
}   
.admin-page-framework-field-image_checkbox .admin-page-framework-input-label-container label input[type='checkbox'] + .image_checkbox_item {
    border: 2px solid #DDD;
    display:inline-block;
    padding: 0 0 0 0px;
}
.admin-page-framework-field-image_checkbox .admin-page-framework-input-label-container label input[type='checkbox']:checked + .image_checkbox_item {
    border: 2px solid #0080FF;
    background: #0080FF;
    display:inline-block;
    padding: 0 0 0 0px;
}        
.admin-page-framework-field-image_checkbox .admin-page-framework-input-label-container {
    margin-bottom: 1em;
}
        ";
    }

    /**
     * The class selector to indicate that the input tag is a admin page framework checkbox.
     * 
     * This selector is used for the repeatable and sortable field scripts.
     * @since   3.1.7
     */
    protected $_sCheckboxClassSelector = 'apf_checkbox';
    
    /**
     * Returns the output of the geometry custom field type.
     * 
     */
    /**
     * Returns the output of the field type.
     */
    protected function getField( $aField ) { 
            
        $_aOutput = array();
        $_asValue = $aField['attributes']['value'];

        foreach( $this->getAsArray( $aField['label'] ) as $_sKey => $_sLabel ) {
            
            $_sLabel            = $this->resolveSRC( $_sLabel );
            $_aInputAttributes  = array(
                'type'      => 'checkbox', // needs to be specified since the postytpe field type extends this class. If not set, the 'posttype' will be passed to the type attribute.
                'id'        => $aField['input_id'] . '_' . $_sKey,
                'checked'   => $this->getCorrespondingArrayValue( $_asValue, $_sKey, null ) == 1 ? 'checked' : null,
                'value'     => 1, // must be always 1 for the checkbox type; the actual saved value will be reflected with the above 'checked' attribute.
                'name'      => is_array( $aField['label'] ) ? "{$aField['attributes']['name']}[{$_sKey}]" : $aField['attributes']['name'],
            ) 
                + $this->getFieldElementByKey( $aField['attributes'], $_sKey, $aField['attributes'] )
                + $aField['attributes'];
            $_aInputAttributes['class'] .= ' ' . $this->_sCheckboxClassSelector;
            $_aInputAttributesIE8OrBelow = $_aInputAttributes + array(
                'style'     => $this->generateInlineCSS(
                    array(
                        'display' => 'inline-block',
                    )
                ),       
            );
            
            $_aLabelAttributes = array(
                'for'   => $_aInputAttributes['id'],
                'class' => $_aInputAttributes['disabled'] ? 'disabled' : null,            
            );
            
            $_aImageAttributes = array(
                'class'     => 'image_checkbox_item',
                'style'     => $this->generateInlineCSS(
                    array(
                        'width'               => $this->sanitizeLength( $aField['width'] + 4 ),
                        'height'              => $this->sanitizeLength( $aField['height'] + 4 ),
                        'background-image'    => "url('{$_sLabel}')",
                        'background-repeat'   => 'no-repeat',                        
                        'background-position' => 'center',           
                        'background-size'     => $this->sanitizeLength( $aField['width'] ) . ' ' . $this->sanitizeLength( $aField['height'] ),
    
                    )
                ),               
            );
            $_aImageAttributesIE8OrBelow = array(
                'class'     => 'image_checkbox_item',
                'style'     => $this->generateInlineCSS(
                    array(
                        'width'      => $this->sanitizeLength( $aField['width'] + 4 ),
                        'height'     => $this->sanitizeLength( $aField['height'] + 4 ),
                        'background' => 'no-repeat center center fixed',                        
                        'filter'     => "progid:DXImageTransform.Microsoft.AlphaImageLoader( src='{$_sLabel}', sizingMethod='scale')",    
                    )
                ),                           
            );
            
            $_aOutput[] =
                $this->getFieldElementByKey( $aField['before_label'], $_sKey )
                . "<div class='admin-page-framework-input-label-container admin-page-framework-checkbox-label' style='min-width: " . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>"         
                    . "<label " . $this->generateAttributes( $_aLabelAttributes ) . ">"
                        . $this->getFieldElementByKey( $aField['before_input'], $_sKey )
                        . "<span class='admin-page-framework-input-container'>"
                            . "<input type='hidden' class='{$this->_sCheckboxClassSelector}' name='{$_aInputAttributes['name']}' value='0' />" // the unchecked value must be set prior to the checkbox input field.
                            // For IE 8 or below
                            . "<!--[if lte IE 8]>"
                                . "<input " . $this->generateAttributes( $_aInputAttributesIE8OrBelow ) . " />" 
                                . "<span " . $this->generateAttributes( $_aImageAttributesIE8OrBelow )  . " ></span>"
                            . "<!--<![endif]-->"
                            // For IE 9 or greater and other browsers like Firefox and Chrome
                            . "<!--[if gte IE 9]><!-->"
                                . "<input " . $this->generateAttributes( $_aInputAttributes ) . " />" 
                                . "<span " . $this->generateAttributes( $_aImageAttributes )  . " ></span>"
                            . "<!--<![endif]-->"                            
                        . "</span>"
                        . "<span class='admin-page-framework-input-label-string'>"
                            . $_sLabel
                        . "</span>"
                        . $this->getFieldElementByKey( $aField['after_input'], $_sKey )
                    . "</label>"                       
                . "</div>"
                . $this->getFieldElementByKey( $aField['after_label'], $_sKey );
                
        }    
        return implode( PHP_EOL, $_aOutput );
        
    }    
     
}
endif;