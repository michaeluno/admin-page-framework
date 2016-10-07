<?php
/**
 * Admin Page Framework Loader
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds a tab that displays the `no_ui_slider` field examples.
 * 
 * @since       3.8.4
 */
class APF_Demo_CustomFieldType_NoUISlider {

    public $oFactory;
    
    public $sClassName;
    
    public $sPageSlug;
    
    public $sTabSlug = 'no_ui_slider';

    public function __construct( $oFactory, $sPageSlug ) {
    
        $this->oFactory     = $oFactory;
        $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = $sPageSlug; 
        $this->sSectionID   = $this->sTabSlug;
                        
        $this->oFactory->addInPageTabs(    
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Slider', 'admin-page-framework-loader' ),
            )
        );  
        
        // Register the field type.
        new NoUISliderCustomFieldType( $this->sClassName );
        
        // load_{page slug}_{tab slug}
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToLoadTab' ) );
          
    }
    
    /**
     * Triggered when the tab starts loading.
     * 
     * @callback        action      load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oAdminPage ) {                
        
        add_action( 'do_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToDoTab' ) );
        
        // validation_{page slug}_{tab slug}
        add_filter( 'validation_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'validate' ), 10, 4 );
        
         // Section
        $oAdminPage->addSettingSections(    
            $this->sPageSlug, // the target page slug                
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Range Slider', 'admin-page-framework-loader' ),
                'description'   => array( 
                    __( 'This field type lets the user set numbers by using a slider.', 'admin-page-framework-loader' ),
                    sprintf(
                        __( 'For the specifications of the <code>options</code> argument, see <a href="%1$s" target="blank">here</a>.', 'admin-page-framework-loader' ),
                        'https://refreshless.com/nouislider/slider-options/'
                    ),
                ),                
            )            
        );        
                    
        // Fields   
        $oAdminPage->addSettingFields(
            $this->sSectionID,
            array(
                'field_id'      => 'no_ui_slider_default',
                'type'          => 'no_ui_slider',
                'title'         => __( 'Slider', 'admin-page-framework-loader' ),
                'description'   => array(
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'no_ui_slider',
)
EOD
                        )
                        . "</pre>",                       
                ),                   
            ),      
            array(
                'field_id'      => 'no_ui_slider_two_handles',
                'type'          => 'no_ui_slider',
                'title'         => __( 'Two Handles', 'admin-page-framework-loader' ),
                'options'       => array(
                    'start' => array(
                        10, 60
                    ),
                
                ),                                
                'description'   => array(
                    __( 'To add more handles, add positions to the <code>start</code> array argument', 'admin-page-framework-loader' ),
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'no_ui_slider',
    'options'       => array(
        'start' => array(
            10, 20
        ),    
    ),                    
)
EOD
                        )
                        . "</pre>",                       
                ),                   
            ),     
            array(
                'field_id'      => 'no_ui_slider_multiple_handles',
                'type'          => 'no_ui_slider',
                'title'         => __( 'Multiple Handles', 'admin-page-framework-loader' ),
                'options'       => array(
                    'start'   => array(
                        10, 30, 70,
                    ),             
                    'connect' => array(
                        true, false, true, false
                    ),
                ),                                
                'description'   => array(
                    __( 'Use the <code>connect</code> array argument to set a colored bar between handles.', 'admin-page-framework-loader' ),
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'no_ui_slider',
    'options'       => array(
        'start' => array(
            10, 30, 70,
        ),    
        'connect' => array(
            true, false, true, false
        ),        
    ),                    
)
EOD
                        )
                        . "</pre>",                       
                ),                   
            ),     
            array(
                'field_id'      => 'no_ui_slider_margin',
                'type'          => 'no_ui_slider',
                'title'         => __( 'Margin', 'admin-page-framework-loader' ),
                'options'       => array(
                    'start' => array(
                        30, 80
                    ),
                    'margin'  => 20,
                    'connect' => array(
                        true, false, true
                    ),                
                ),                                
                'description'   => array(
                    __( 'To set a fixed margin between two handles, use the <code>margin</code> argument', 'admin-page-framework-loader' ),
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'no_ui_slider',
    'options'       => array(
        'start' => array(
            30, 80
        ),
        'margin'  => 20,
        'connect' => array(
            true, false, true
        ),
    ),                    
)
EOD
                        )
                        . "</pre>",                       
                ),                   
            ),    
            array(
                'field_id'      => 'no_ui_slider_limit',
                'type'          => 'no_ui_slider',
                'title'         => __( 'limit', 'admin-page-framework-loader' ),
                'options'       => array(
                    'start' => array(
                        40, 60
                    ),
                    'limit'  => 50,
                    'connect' => array(
                        false, true, false
                    ),                
                ),                
                'description'   => array(
                    __( 'The <code>limit</code> argument is the opposite of <code>margin</code> that sets the maximum length of two handles.', 'admin-page-framework-loader' ),
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'no_ui_slider',
    'options'       => array(
        'start' => array(
            40, 60
        ),
        'limit'  => 50,
        'connect' => array(
            false, true, false
        ),                
    ),                
)
EOD
                        )
                        . "</pre>",                       
                ),                   
            ),               
            array(
                'field_id'      => 'no_ui_slider_range',
                'type'          => 'no_ui_slider',                
                'title'         => __( 'Range', 'admin-page-framework-loader' ),
                'options'       => array(
                    'start' => array(
                        120, 250,
                    ),
                    'range' => array(
                        'min'   => 100,
                        'max'   => 300,
                    ),
                ),
                'description'   => array(
                    __( 'Set minimum and maximum values with the <code>range</code> argument and set values to the <code>min</code> and <code>max</code> inner arguments.', 'admin-page-framework-loader' ),
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'no_ui_slider',
    'options'       => array(
        'start' => array(
            120, 250,
        ),
        'range' => array(
            'min'   => 100,
            'max'   => 300,
        ),
    ),
)
EOD
                        )
                        . "</pre>"
                ),
            ),            
            array(
                'field_id'      => 'no_ui_slider_step',
                'type'          => 'no_ui_slider',
                'title'         => __( 'Step', 'admin-page-framework-loader' ),
                'options'       => array(
                    'step'  => 0.01,
                    'round' => 2,
                    'start' => array(
                        10,
                    ),
                ),
                'description'   => array(
                    __( 'To set a step, use the <code>step</code> argument. The minimum step is <code>0.01</code>.', 'admin-page-framework-loader' ),
                    __( 'To specify the number of digits to round the value, use the <code>round</code> argument. The default value is <code>0</code>.', 'admin-page-framework-loader' ),
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'no_ui_slider',
    'default'       => 10,
    'options'       => array(
        'step'  => 0.01,
        'round' => 2,
        'start' => array(
            10,
        ),
    ),    
)
EOD
                        )
                        . "</pre>",                       
                ),                   
            ),
            array(
                'field_id'      => 'no_ui_slider_orientation',
                'type'          => 'no_ui_slider',                
                'title'         => __( 'Orientation', 'admin-page-framework-loader' ),
                'options'       => array(
                    'orientation' => 'vertical',
                ),
                'attributes'    => array(
                    'slider' => array(
                        'style' => 'height: 100px;',
                    ),
                ),
                'description'   => array(
                    __( 'To have a vertical slider, use the <code>orientation</code> argument and set it to <code>vertical</code>.', 'admin-page-framework-loader' ),
                    __( 'To set a height of the slider element, set an inline CSS to the <code>slider</code> inner argument of the <code>attributes</code> argument.', 'admin-page-framework-loader' ),
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'no_ui_slider',
    'options'       => array(
        'orientation' => 'vertical',
    ),
    'attributes'    => array(
        'slider' => array(
            'style' => 'height: 100px;',
        ),
    ),
)
EOD
                        )
                        . "</pre>"
                ),
            ),     
            array(
                'field_id'      => 'no_ui_slider_direction',
                'type'          => 'no_ui_slider',                
                'title'         => __( 'Direction', 'admin-page-framework-loader' ),
                'options'       => array(
                    'direction' => 'rtl',   // or ltr
                ),
                'description'   => array(
                    __( 'To change the direction that the value increases or decreases, use the <code>direction</code> argument.', 'admin-page-framework-loader' ),
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'no_ui_slider',
    'options'       => array(
        'direction' => 'rtl',   // or ltr
    ),
)
EOD
                        )
                        . "</pre>"
                ),
            ),
            array(
                'field_id'      => 'no_ui_slider_tooltips',
                'type'          => 'no_ui_slider',                
                'title'         => __( 'Tool-tips', 'admin-page-framework-loader' ),
                'options'       => array(
                    'tooltips' => array( true, false, true ),
                    'round'    => 2,
                    'step'     => 0.1,
                    'start'    => array(
                        12, 34, 78,
                    ),
                ),
                'description'   => array(
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'no_ui_slider',
    'options'       => array(
        'tooltips' => array( true, false, true ),
        'round'    => 2,
        'step'     => 0.1,
        'start'    => array(
            12, 34, 78,
        ),
    ),
)
EOD
                        )
                        . "</pre>"
                ),
            ),    
            array(
                'field_id'      => 'no_ui_slider_pips',
                'type'          => 'no_ui_slider',                
                'title'         => __( 'Scale Marks', 'admin-page-framework-loader' ),
                'options'       => array(
                    'pips' => array(
                        'mode'   => 'range',
                        'density'=> 3
                    )
                ),
                'description'   => array(
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'no_ui_slider',
    'options'       => array(
        'pips' => array(
            'mode'   => 'range',
            'density'=> 3
        )
    ),
)
EOD
                        )
                        . "</pre>"
                ),
            ),              
            array(
                'field_id'      => 'no_ui_slider_repeatable_sortable',
                'type'          => 'no_ui_slider',
                'title'         => __( 'Repeatable & Sortable', 'admin-page-framework-loader' ),
                'repeatable'    => true,
                'sortable'      => true,
                'options'       => array(
                    'start'     => array( 10, ),
                    'connect'   => array( true, false ),
                ),
                'description'   => array(
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'no_ui_slider',
    'repeatable'    => true,
    'sortable'      => true,
    'options'       => array(
        'start'     => array( 10, ),
        'connect'   => array( true, false ),
    ),
)
EOD
                        )
                        . "</pre>",                       
                ),                   
            ),            
            array(
                'field_id'      => 'no_ui_slider_repeatable_sortable_range',
                'type'          => 'no_ui_slider',
                'title'         => __( 'Repeatable & Sortable Range', 'admin-page-framework-loader' ),
                'repeatable'    => true,
                'sortable'      => true,
                'options'       => array( 
                    'start' => array(
                        10, 200,                        
                    ),
                    'range' => array( 
                        'min'   => 10, 
                        'max'   => 200, 
                    ),            
                    'connect'       => array( true, false, true ),
                ), 
                'description'   => array(
                    "<pre>"
                        . $oAdminPage->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'no_ui_slider',
    'repeatable'    => true,
    'sortable'      => true,
    'options'       => array( 
        'start'    => array(
            10, 200,
        ),
        'range'    => array( 
            'min'   => 10, 
            'max'   => 200, 
        ),            
        'connect'  => array( true, false, true ),
    ),
)
EOD
                        )
                        . "</pre>",                       
                ),                   
            ),               
            array()
        );  
 
    }            
    
    public function replyToDoTab() {        
        submit_button();
    }

    /**
     * @return      array
     */
    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
        return $aInputs;
    }
    
    
}
