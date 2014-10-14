<?php
if ( ! class_exists( 'DateTimeCustomFieldType' ) ) :
class DateTimeCustomFieldType extends AdminPageFramework_FieldType {
    
    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'date_time', );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * @remark            $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'date_format'    => 'yy/mm/dd',
        'time_format'    => 'H:mm',
        'attributes'     => array(
            'size'       => 16,
            'maxlength'  => 400,
        ),
        'options'        => array(
            'showButtonPanel'    =>    false,
        ),
    );
    
    /**
     * Loads the field type necessary components.
     */ 
    protected function setUp() {
        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_script( 'jquery-ui-slider' );
    }    

    /**
     * Returns an array holding the urls of enqueuing scripts.
     */
    protected function getEnqueuingScripts() { 
        return array(
            array( 'src'    => dirname( __FILE__ ) . '/js/jquery-ui-timepicker-addon.min.js', 'dependencies'    => array( 'jquery-ui-datepicker' ) ),
            array( 'src'    => dirname( __FILE__ ) . '/js/datetimepicker-option-handler.js', ),            
        );
    }    
    
    /**
     * Returns an array holding the urls of enqueuing styles.
     */
    protected function getEnqueuingStyles() { 
        return array(
            dirname( __FILE__ ) . '/css/jquery-ui-1.10.3.min.css',
            dirname( __FILE__ ) . '/css/jquery-ui-timepicker-addon.min.css',
        ); 
    }    
        
    /**
     * Returns the field type specific JavaScript script.
     */ 
    protected function getScripts() { 

        $aJSArray = json_encode( $this->aFieldTypeSlugs );
        /*    The below function will be triggered when a new repeatable field is added. */
        return "
            jQuery( document ).ready( function(){
                jQuery().registerAPFCallback( {            
                    /**
                     * The repeatable field callback for the add event.
                     * 
                     * @param    object    oCopiedNode
                     * @param    string    the field type slug
                     * @param    string    the field container tag ID
                     * @param    integer    the caller type. 1 : repeatable sections. 0 : repeatable fields.
                     */                
                    added_repeatable_field: function( oCopiedNode, sFieldType, sFieldTagID, iCallerType ) {
            
                        /* If it is not this field type, do nothing. */
                        if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;

                        /* If the input tag is not found, do nothing  */
                        if ( oCopiedNode.find( 'input.datetime_picker' ).length <= 0 ) return;
                        
                        /* Update the date-time input tag of all the next fields including the passed field. 
                         * This is because the datetimepicker jQuery plugin looses its bind when the attribute is updated(incremented).
                         * */
                        var oFieldContainer = oCopiedNode.closest( '.admin-page-framework-field' );
                        oFieldContainer.nextAll().andSelf().each( function( iIndex ) {

                            var oDateTimePickerInput = jQuery( this ).find( 'input.datetime_picker' );    
                            if( oDateTimePickerInput.length <= 0 ) { return true; }
                            
                            /* (Re)bind the date picker script */
                            oDateTimePickerInput.removeClass( 'hasDatepicker' );
                            var sOptionID = jQuery( this ).closest( '.admin-page-framework-sections' ).attr( 'id' ) 
                                + '_' 
                                + jQuery( this ).closest( '.admin-page-framework-fields' ).attr( 'id' );    // sections id + _ + fields id 
                            var aOptions = jQuery( '#' + oDateTimePickerInput.attr( 'id' ) ).getDateTimePickerOptions( sOptionID );
                            oDateTimePickerInput.datetimepicker( aOptions );
                        
                        });
                        
                    },
                    /**
                     * The repeatable field callback for the remove event.
                     * 
                     * @param    object    the field container element next to the removed field container.
                     * @param    string    the field type slug
                     * @param    string    the field container tag ID
                     * @param    integer    the caller type. 1 : repeatable sections. 0 : repeatable fields.
                     */            
                    removed_repeatable_field: function( oNextFieldConainer, sFieldType, sFieldTagID, iCallType ) {
                        
                        /* If it is not the color field type, do nothing. */
                        if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;
                                            
                        /* If the uploader buttons are not found, do nothing */
                        if ( oNextFieldConainer.find( 'input.datetime_picker' ).length <= 0 )  return;                        
                        
                        /* Update the next all (including this one) fields */
                        oNextFieldConainer.nextAll().andSelf().each( function( iIndex ) {
                            
                            var oDateTimePickerInput = jQuery( this ).find( 'input.datetime_picker' );    
                            if( oDateTimePickerInput.length <= 0 ) { return true; }
                            
                            /* (Re)bind the date picker script */
                            oDateTimePickerInput.removeClass( 'hasDatepicker' );
                            var sOptionID = jQuery( this ).closest( '.admin-page-framework-sections' ).attr( 'id' ) 
                                + '_' 
                                + jQuery( this ).closest( '.admin-page-framework-fields' ).attr( 'id' );    // sections id + _ + fields id 
                            var aOptions = jQuery( '#' + oDateTimePickerInput.attr( 'id' ) ).getDateTimePickerOptions( sOptionID );
                            oDateTimePickerInput.datetimepicker( aOptions );                            

                        });
                        
                    },                    
                    
                    sorted_fields : function( node, sFieldType, sFieldsTagID ) {    // on contrary to repeatable callbacks, the _fields_ container node and its ID will be passed.

                        /* Return if it is not the type. */
                        if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;    /* If it is not the color field type, do nothing. */                        
                        
                        /* Bind the date picker script */
                        node.children( '.admin-page-framework-field' ).each( function() {
                            var oInput = jQuery( this ).find( 'input.datetime_picker' );
                            var sOptionID = oInput.closest( '.admin-page-framework-sections' ).attr( 'id' ) + '_' + oInput.closest( '.admin-page-framework-fields' ).attr( 'id' );    // sections id + _ + fields id 
                            var aOptions = jQuery( '#' + oInput.attr( 'id' ) ).getDateTimePickerOptions( sOptionID );                            
                            oInput.removeClass( 'hasDatepicker' );
                            oInput.datetimepicker( aOptions );                                                    
                        });
                    },                    
                });
            });        
        
        " . PHP_EOL;
        
    } 
    
    /**
     * Returns IE specific CSS rules.
     */
    protected function getIEStyles() { return ''; }

    /**
     * Returns the field type specific CSS rules.
     */ 
    protected function getStyles() { return ""; }    
        
    /**
     * Returns the output of this field type.
     */
    protected function getField( $aField ) { 
            
        $aInputAttributes = array(
            'type'              =>    'text',
            'data-date_format'  => $aField['date_format'],
            'data-time_format'  => $aField['time_format'],
        ) + $aField['attributes'];
        $aInputAttributes['class'] .= ' datetime_picker';
        return 
            $aField['before_label']
            . "<div class='admin-page-framework-input-label-container'>"
                . "<label for='{$aField['input_id']}'>"
                    . $aField['before_input']
                    . ( $aField['label'] && ! $aField['repeatable']
                        ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>" . $aField['label'] . "</span>"
                        : "" 
                    )
                    . "<input " . $this->generateAttributes( $aInputAttributes ) . " />"    // this method is defined in the base class
                    . $aField['after_input']
                    . "<div class='repeatable-field-buttons'></div>"    // the repeatable field buttons will be replaced with this element.
                . "</label>"
            . "</div>"
            . $this->_getDateTimePickerEnablerScript( $aField['input_id'], $aField['date_format'], $aField['time_format'], $aField['options'] )
            . $aField['after_label'];
        
    }    

        /**
         * A helper function for the above getDateField() method.
         * 
         */
        protected function _getDateTimePickerEnablerScript( $sInputID, $sDateFormat, $sTimeFormat, $asOptions ) {
            
            if ( is_array( $asOptions ) ) {                
                $aOptions = $asOptions;
                $aOptions['dateFormat'] = isset( $aOptions['dateFormat'] ) ? $aOptions['dateFormat'] : $sDateFormat;
                $aOptions['timeFormat'] = isset( $aOptions['timeFormat'] ) ? $aOptions['timeFormat'] : $sTimeFormat;
                $_sOptions = json_encode( ( array ) $aOptions );    
            } else {
                $_sOptions = ( string ) $asOptions;    
            }
            return 
                "<script type='text/javascript' class='date-time-picker-enabler-script'>
                    jQuery( document ).ready( function() {
                        jQuery( document ).on( 'focus', 'input#{$sInputID}:not(.hasDatepicker)', function() {
                            jQuery( this ).datetimepicker( {$_sOptions} );
                        });                                               
                        var sOptionID = jQuery( '#{$sInputID}' ).closest( '.admin-page-framework-sections' ).attr( 'id' ) + '_' + jQuery( '#{$sInputID}' ).closest( '.admin-page-framework-fields' ).attr( 'id' );
                        jQuery( '#{$sInputID}' ).setDateTimePickerOptions( sOptionID, {$_sOptions});                                
                    });
                </script>";            
        }
    
}
endif;