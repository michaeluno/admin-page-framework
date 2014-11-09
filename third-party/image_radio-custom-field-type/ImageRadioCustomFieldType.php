<?php
if ( ! class_exists( 'ImageRadioCustomFieldType' ) ) :
/**
 * Defines the image_radio field type.
 * 
 * @package     AdminPageFramework
 * @subpackage  FieldType
 * @since       3.2.1
 * @internal
 */
class ImageRadioCustomFieldType extends AdminPageFramework_FieldType {
        
    /**
     * Defines the field type slugs used for this field type.
     * 
     * The slug is used for the type key in a field definition array.
     * <code>$this->addSettingFields(
     *       array(
     *           'section_id'    => ...,
     *           'type'          => 'image_radio',        // <--- THIS PART
     *           'field_id'      => ...,
     *           'title'         => ...,
     *       )
     *   );</code>
     */
    public $aFieldTypeSlugs = array( 'image_radio', );
    
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

        /* The below JavaScript function will be triggered when a new repeatable field is added. Since the APF repeater script does not
            renew the color piker element (while it does on the input tag value), the renewal task must be dealt here separately. */    
        $aJSArray = json_encode( $this->aFieldTypeSlugs );
        return "     
            jQuery( document ).ready( function(){
                jQuery().registerAPFCallback( {     
                    added_repeatable_field: function( nodeField, sFieldType, sFieldTagID, sCallType ) {
            
                        /* If it is not the color field type, do nothing. */
                        if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;
                                                    
                        /* the checked state of radio buttons somehow lose their values so re-check them again */    
                        nodeField.closest( '.admin-page-framework-fields' )
                            .find( 'input[type=radio][checked=checked]' )
                            .attr( 'checked', 'checked' );
                            
                        /* Rebind the checked attribute updater */
                        nodeField.find( 'input[type=radio]' ).change( function() {
                            jQuery( this ).closest( '.admin-page-framework-field' )
                                .find( 'input[type=radio]' )
                                .attr( 'checked', false );
                            jQuery( this ).attr( 'checked', 'checked' );
                        });

                    }
                });
            });
        ";      
    
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
.admin-page-framework-field-image_radio .admin-page-framework-input-label-string {
    display: none;
}   
.admin-page-framework-field-image_radio input[type='radio'] {
    display: none;
}

.admin-page-framework-field-image_radio .admin-page-framework-input-label-container label input[type='radio'] + .image_radio_item {
    border: 2px solid #DDD;
    display:inline-block;
    padding: 0 0 0 0px;
    margin: 0;
}
.admin-page-framework-field-image_radio .admin-page-framework-input-label-container label input[type='radio']:checked + .image_radio_item {
    border: 2px solid #0080FF;
    background: #0080FF;
    display:inline-block;
    padding: 0 0 0 0px;
    
}        
.admin-page-framework-section .admin-page-framework-field-image_radio .admin-page-framework-input-label-container label {
    display: inline-block;
}
.admin-page-framework-field-image_radio .admin-page-framework-input-label-container {
    margin-bottom: 1em;
}
        ";
    }
    
    /**
     * Returns the output of the geometry custom field type.
     * 
     */
    /**
     * Returns the output of the field type.
     */
    protected function getField( $aField ) { 
            
        $_aOutput   = array();
        $_sValue    = $aField['attributes']['value'];
        
        foreach( $this->getAsArray( $aField['label'] ) as $_sKey => $_sLabel ) {

            $_sLabel            = $this->resolveSRC( $_sLabel );
            
            /* Prepare attributes */
            $_aInputAttributes  = array(
                    'type'          => 'radio',
                    'checked'       => $_sValue == $_sKey ? 'checked' : null,
                    'value'         => $_sKey,
                    'id'            => $aField['input_id'] . '_' . $_sKey,
                    'data-default'  => $aField['default'],      
                ) 
                + $this->getFieldElementByKey( $aField['attributes'], $_sKey, $aField['attributes'] )
                + $aField['attributes'];
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
                'class'     => 'image_radio_item',
                'style'     => $this->generateInlineCSS(
                    array(
                        'width'               => $this->sanitizeLength( $aField['width'] + 4 ),
                        'height'              => $this->sanitizeLength( $aField['height'] + 4 ),
                        'background-image'    => "url('{$_sLabel}')",
                        'background-repeat'   => 'no-repeat',                        
                        'background-position' => 'center',           
                        'background-size'     => $this->sanitizeLength( $aField['width'] ) . ' ' . $this->sanitizeLength( $aField['height'] ),
                        'backgroundSize'      => "cover",   // for IE8
                    )
                ),               
            );
            $_aImageAttributesIE8OrBelow = array(
                'class'     => 'image_radio_item',
                'style'     => $this->generateInlineCSS(
                    array(
                        'width'               => $this->sanitizeLength( $aField['width'] + 4 ),
                        'height'              => $this->sanitizeLength( $aField['height'] + 4 ),
                        'background'          => 'no-repeat center center fixed',                        
                        'filter'              => "progid:DXImageTransform.Microsoft.AlphaImageLoader( src='{$_sLabel}', sizingMethod='scale')",    
                    )
                ),                           
            );
            /* Insert the output */
            $_aOutput[] = 
                $this->getFieldElementByKey( $aField['before_label'], $_sKey )
                . "<div class='admin-page-framework-input-label-container admin-page-framework-radio-label' style='min-width: " . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>"
                    . "<label " . $this->generateAttributes( $_aLabelAttributes ) . ">"
                        . $this->getFieldElementByKey( $aField['before_input'], $_sKey )
                        . "<span class='admin-page-framework-input-container'>"
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
                . $this->getFieldElementByKey( $aField['after_label'], $_sKey )
                ;
                
        }
        $_aOutput[] = $this->_getUpdateCheckedScript( $aField['_field_container_id'] );
        return implode( PHP_EOL, $_aOutput );
            
    }    
  
        /**
         * Returns the JavaScript script that updates the checked attribute of radio buttons when the user select one.
         * This helps repeatable field script that duplicate the last checked item.
         * @sinec 3.2.1
         */
        private function _getUpdateCheckedScript( $sFieldContainerID ) {
            return 
                "<script type='text/javascript' class='radio-button-checked-attribute-updater'>
                    jQuery( document ).ready( function(){
                        jQuery( '#{$sFieldContainerID} input[type=radio]' ).change( function() {
                            jQuery( this ).closest( '.admin-page-framework-field' ).find( 'input[type=radio]' ).attr( 'checked', false );
                            jQuery( this ).attr( 'checked', 'checked' );
                        });
                    });     
                </script>";     
            
        }    
}
endif;