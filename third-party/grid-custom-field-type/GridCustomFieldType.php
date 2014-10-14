<?php
if ( ! class_exists( 'GridCustomFieldType' ) ) :
class GridCustomFieldType extends AdminPageFramework_FieldType {
        
    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'grid', );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * @remark  $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'grid_options'  => array(
            'widget_margins'            => array( 5, 5 ),
            'widget_base_dimensions'    => array( 50, 50 ),
        ),
        'attributes'    => array(
            'size'      => 10,
            'maxlength' => 400,
        ),    
    );

    /**
     * Loads the field type necessary components.
     */ 
    protected function setUp() {}    

    /**
     * Returns an array holding the urls of enqueuing scripts.
     */
    protected function getEnqueuingScripts() { 
        return array(
            array( 'src' => dirname( __FILE__ ) . '/js/jquery.gridster.js', 'dependencies' => array( 'jquery' ) ),
            array( 'src' => dirname( __FILE__ ) . '/js/gridster.custom.js', 'dependencies' => array( 'jquery' ) ),
            
        );
    }
    
    /**
     * Returns an array holding the urls of enqueuing styles.
     */
    protected function getEnqueuingStyles() { 
        return array(
            dirname( __FILE__ ) . '/css/jquery.gridster.css',
            dirname( __FILE__ ) . '/css/gridster.demo.mod.css',
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
                    added_repeatable_field: function( node, sFieldType, sFieldTagID ) {
            
                        /* If it is not this field type, do nothing. */
                        if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;

                        /* If the input tag is not found, do nothing  */
                        var nodeGridInput = node.find( 'input.grid' );
                        if ( nodeGridInput.length <= 0 ) return;
                        
                        /* Remove unnecessary elements */
                        // nodeGridInput.closest( '.admin-page-framework-field' ).find( 'canvas' ).remove();
                        
                        /* Bind the knob script */
                        // nodeGridInput.knob();
                        
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
    protected function getStyles() {
        return "
        .admin-page-framework-field-grid {
            width: 100%;
        }
        .admin-page-framework-field-grid .admin-page-framework-input-label-container {
            width: 100%;
        }
        .remove_gridster_widget {
            float: right;
            padding: 0.1em 0.4em;
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
            
        $aInputAttributes = array(
            'type'    => 'hidden',
            'value'   => is_array( $aField['attributes']['value'] ) 
                ? json_encode( $aField['attributes']['value'] )    // convert to json string.
                : $aField['attributes']['value'],    
        ) + $aField['attributes'];
        $aInputAttributes['class'] .= ' grid';

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
                . "</label>"
                . $this->getGridContainer( $aField['input_id'] )
            . "</div>"
            . $this->getGridEnablerScript( $aField['input_id'], $aField['grid_options'] )
            . $aField['after_label']
            . $this->getAddWidgetButton( $aField['input_id'] )
            ;
        
    }    
        private function getAddWidgetButton( $sInputID ) {
            return "<a id='add_widget-{$sInputID}' class='add_gridster_widget button secondary small' href='#'>" . __( 'Add Widget', 'admin-page-framework-demo' ) . "</a>";
        }
        private function getGridContainer( $sInputID ) {
            return                
                "<div class='gridster'>"
                    . "<ul id='grid_container-{$sInputID}'>"
                    . "</ul>"
                . "</div>";
            
        }
        private function getGridEnablerScript( $sInputID, $aSettings=array() ) {
            $aJSArray = json_encode( $aSettings );
            return 
                "<script type='text/javascript' class='gridster-enabler-script'>
                    jQuery( document ).ready( function() {
                        var aOptions = {$aJSArray};
                        jQuery.extend( true, aOptions, {    // recursive merge
                            namespace: '#grid_container-{$sInputID}',    // Set the container element selector. This is important as the gridster script will generate styles with it.
                            resize: {
                                stop: function( event, ui ){                             
                                    setTimeout( function() {    // give a delay for Firefox
                                        var oGridster = jQuery( '#grid_container-{$sInputID}' ).gridster().data('gridster');
                                        oGridster.saveGrid( '#{$sInputID}' );
                                    }, 500 );    
                                },
                            },
                            draggable: {
                                stop: function( event, ui ){ 
                                    setTimeout( function() {    // give a delay for Firefox
                                        var oGridster = jQuery( '#grid_container-{$sInputID}' ).gridster().data('gridster');
                                        oGridster.saveGrid( '#{$sInputID}' );
                                    }, 500 );    
                                },
                            },
                            serialize_params: function( w, wgd ) { 
                                return ( wgd )
                                    ? { 
                                        id: wgd.el[0].id, 
                                        col: wgd.col, 
                                        row: wgd.row,
                                        size_y: wgd.size_y,
                                        size_x: wgd.size_x,
                                    } 
                                    : {};
                            },
                        });

                        // Set container width - must be done before creating the grid; otherwise, it gets locked. 
                        var nDimension = 50;
                        var iMargin = 5;
                        var iLargestColumnNumber = 3;
                        // jQuery( '#grid_container-{$sInputID}' ).width( ( iLargestColumnNumber - 1 ) * ( iMargin * 2 + nDimension ) );
                        // jQuery( '#grid_container-{$sInputID}' ).parent().width( ( iLargestColumnNumber ) * ( iMargin * 2 + nDimension ) );
                        
                        jQuery( '#grid_container-{$sInputID}' ).parent().css( 'width', '100%' );
                        jQuery( '#grid_container-{$sInputID}' ).css( 'width', '100%' );
                        
                        // Create a gridster grid
                        jQuery( '#grid_container-{$sInputID}' ).gridster( aOptions );
                        var oGridster = jQuery( '#grid_container-{$sInputID}' ).gridster().data( 'gridster' );
                        
                        // Import the saved data
                        var json = jQuery.parseJSON( jQuery( '#{$sInputID}' ).val() );      
                        for(i=0; i<json.length; i++) {
                            var joWidget = oGridster.add_widget(
                                '<li class=\"gridster_widget\"><div class=\"remove_gridster_widget\"><a>x</a></div></li>',     // '<div id=\"' + json[i]['id'] + '\"></div>', 
                                json[i]['size_x'], 
                                json[i]['size_y'], 
                                json[i]['col'], 
                                json[i]['row'] 
                            );
                            oGridster.setRemoveButton( joWidget.find( '.remove_gridster_widget' ), '#{$sInputID}' );
                        }                        

                                
                    // Add widget
                    jQuery( '#add_widget-{$sInputID}' ).click( function() { 
    
                        var oGridster = jQuery( '#grid_container-{$sInputID}' ).gridster().data( 'gridster' );
                        var aLargestPosition = oGridster.getLargestColumnPosition();
                        var joWidget = oGridster.add_widget(
                            '<li class=\"gridster_widget\"><div class=\"remove_gridster_widget\"><a>x</a></div></li>',    // html
                            1,    // x-colspan
                            1,    // y-colspan
                            aLargestPosition['x'],    // col index
                            1 === aLargestPosition['y'] ? aLargestPosition['y'] : aLargestPosition['y'] + 1,    // row index
                            null    // max colspan
                        );
                        oGridster.setRemoveButton( joWidget.find( '.remove_gridster_widget' ), '#{$sInputID}' );
                        oGridster.saveGrid( '#{$sInputID}' );
                        return false;
                        
                    });    
                });                
                </script>";        
            
        }
    
}
endif;