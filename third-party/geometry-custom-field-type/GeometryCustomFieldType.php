<?php
if ( ! class_exists( 'GeometryCustomFieldType' ) ) :
class GeometryCustomFieldType extends AdminPageFramework_FieldType {
        
    /**
     * Defines the field type slugs used for this field type.
     */        
    public $aFieldTypeSlugs = array( 'geometry' );    
        
    /**
     * Defines the default key-values of this field type. 
     * 
     * @remark            $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'attributes'        => array(
            'value'         => array(
                'latitude'      => 20,
                'longitude'     => 20,
                'elevation'     => null,
                'location_name' => null,
            ),
            'latitude'      => array(),
            'longitude'     => array(),
            'elevation'     => array(),
            'location_name' => array(),
            'button'        => array(),
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
            "http://maps.googleapis.com/maps/api/js?sensor=false",    // load this first
            dirname( __FILE__ ) . '/js/jquery-gmaps-latlon-picker.js',    // load this next - a file path can be passed, ( as well as a url )
        );
    }    

    /**
     * Returns an array holding the urls of enqueuing styles.
     */
    protected function getEnqueuingStyles() { 
        return array(
            dirname( __FILE__ ) . '/css/jquery-gmaps-latlon-picker.css',    // a file path can be passed, ( as well as a url )
        ); 
    }    
    
    /**
     * Returns the field type specific JavaScript script.
     */ 
    protected function getScripts() { return ''; } 

    /**
     * Returns IE specific CSS rules.
     */
    protected function getIEStyles() { return ''; }

    /**
     * Returns the field type specific CSS rules.
     */ 
    protected function getStyles() {
        return "/* Geometry Custom Field Type */
            .admin-page-framework-field .gllpMap {width: 100%}
            .admin-page-framework-section .form-table td .gllpLatlonPicker label {
                display: inline-block;
            }
        ";
    }
    
    /**
     * Returns the output of this field type.
     */
    protected function getField( $aField ) { 
            
        return 
            $aField['before_label']
            . "<div class='admin-page-framework-input-label-container'>"
                    . $aField['before_input']
                    . ( $aField['label'] && ! $aField['repeatable']
                        ? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>" . $aField['label'] . "</span>"
                        : "" 
                    )
                    . $this->_getInputs( $aField )
                    . $aField['after_input']
            . "</div>"
            . $aField['after_label'];
        
    }
        protected function _getInputs( &$aField ) {
            
            /* Set up attributes */
            $aBaseAttributes = $aField['attributes'];
            unset( $aBaseAttributes['latitude'], $aBaseAttributes['longitude'], $aBaseAttributes['elevation'], $aBaseAttributes['location_name'], $aBaseAttributes['button'] );

            $aButtonAttributes = array(
                'type'    => 'button',
                'id'      => "{$aField['input_id']}_button",
            ) + $aField['attributes']['button'] + $aBaseAttributes;
            $aButtonAttributes['class']    .= ' gllpUpdateButton button button-small';
            
            $aLattitudeAttributes = array(
                'type'    => 'text',
                'id'      => "{$aField['input_id']}_latitude",
                'value'   => isset( $aField['attributes']['value']['latitude'] ) ? $aField['attributes']['value']['latitude'] : 20,
                'name'    => "{$aField['_input_name']}[latitude]",                        
            ) + $aField['attributes']['latitude'] + $aBaseAttributes;
            $aLattitudeAttributes['class'] .= ' gllpLatitude';
            
            $aLongitudeAttributes = array(
                'type'    => 'text',
                'id'      => "{$aField['input_id']}_longitude",
                'value'   => isset( $aField['attributes']['value']['longitude'] ) ? $aField['attributes']['value']['longitude'] : 20,
                'name'    => "{$aField['_input_name']}[longitude]",
            ) + $aField['attributes']['longitude'] + $aBaseAttributes;            
            $aLongitudeAttributes['class'] .= ' gllpLongitude';

            $aElevationAttributes = array(
                'type'    => 'text',
                'id'      => "{$aField['input_id']}_elevation",
                'value'   => isset( $aField['attributes']['value']['elevation'] ) ? $aField['attributes']['value']['elevation'] : null,
                'name'    => "{$aField['_input_name']}[elevation]",
            ) + $aField['attributes']['elevation'] + $aBaseAttributes;            
            $aElevationAttributes['class'] .= ' gllpElevation';        
            
            $aLocationNameAttributes = array(
                'type'    => 'text',
                'id'      => "{$aField['input_id']}_name",
                'value'   => isset( $aField['attributes']['value']['localtion_name'] ) ? $aField['attributes']['value']['localtion_name'] : null,
                'name'    => "{$aField['_input_name']}[localtion_name]",
            ) + $aField['attributes']['location_name'] + $aBaseAttributes;            
            $aLocationNameAttributes['class'] .= ' gllpLocationName';
            
            /* Return the output */
            return
                "<div class='gllpLatlonPicker'>"
                    . "<div class='gllpMap map'>" . __( 'Google Maps', 'admin-page-framework-demo' ) . "</div>"
                    . "<label for='{$aField['input_id']}_button' class='update-button'>"
                        . "<a " . $this->generateAttributes( $aButtonAttributes ) . ">" . __( 'Update Map', 'admin-page-framework-demo' ) . "</a>"
                    . "</label>"                    
                    . "<label for='{$aField['input_id']}_latitude'>"
                        . "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>" . __( 'Latitude', 'admin-page-framework-demo' ) . "</span>"
                        . "<input " . $this->generateAttributes( $aLattitudeAttributes ) . " />"                
                    . "</label><br />"
                    . "<label for='{$aField['input_id']}_longitude'>"
                        . "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>" . __( 'Longitude', 'admin-page-framework-demo' ) . "</span>"
                        . "<input " . $this->generateAttributes( $aLongitudeAttributes ) . " />"    
                    . "</label><br />"
                    . "<label for='{$aField['input_id']}_elevation'>"
                        . "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>" . __( 'Elevation', 'admin-page-framework-demo' ) . "</span>"                    
                        . "<input " . $this->generateAttributes( $aElevationAttributes ) . " />"
                        . ' ' . __( "metres", "admin-page-framework-demo" )
                    . "</label><br />"                                
                    . "<label for='{$aField['input_id']}_name'>"
                        . "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>" . __( 'Location Name', 'admin-page-framework-demo' ) . "</span>"
                        . "<input " . $this->generateAttributes( $aLocationNameAttributes ) . " />"
                    . "</label><br />"
                    . "<label for='{$aField['input_id']}_zoom'>"
                        . "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->sanitizeLength( $aField['label_min_width'] ) . ";'>" . __( 'zoom', 'admin-page-framework-demo' ) . "</span>"    
                        . "<input type='number' class='gllpZoom' id='{$aField['input_id']}_zoom' min='1' value='3'/>"
                    . "</label><br />"
                . "</div>";    
            
        }
    
}
endif;