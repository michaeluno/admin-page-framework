<?php
/**
 * Admin Page Framework - Demo
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds a section in a tab.
 * 
 * @package     AdminPageFramework/Example
 */
class APF_Demo_BuiltinFieldTypes_Selector_Radio {
    
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_builtin_field_types';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'selectors';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'radio';
        
    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {
    
        // Section
        $oFactory->addSettingSections(    
            $this->sPageSlug, // the target page slug                
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Radio Buttons', 'admin-page-framework-loader' ),
                'tip'           => __( 'These are radio buttons.', 'admin-page-framework-loader' ),
            )
        );   
      
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID       
            array( 
                'field_id'      => 'radio',
                'title'         => __( 'Radio Button', 'admin-page-framework-loader' ),
                'type'          => 'radio',
                'label'         => array(
                    'a' => 'Apple',
                    'b' => 'Banana ( this option is disabled. )',
                    'c' => 'Cherry' 
                ),
                'default'       => 'c', // yields Cherry; its key is specified.
                'after_label'   => '<br />',
                'attributes'    => array(
                    'b' => array(
                        'disabled' => 'disabled',
                    ),
                ),
                'tip'           => __( 'Use the <code>after_input</code> argument to insert <code>&lt;br /&gt;</code> after each sub-field.', 'admin-page-framework-loader' )
                    . ' ' . __( 'To disable elements (or apply different attributes) on an individual element basis, use the <code>attributes</code> argument and create the element whose key name is the radio input element value.', 'admin-page-framework-loader' ),
                'description'   => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'radio',
    'label'         => array(
        'a' => 'Apple',
        'b' => 'Banana ( this option is disabled. )',
        'c' => 'Cherry' 
    ),
    'default'       => 'c', 
    'attributes'    => array(
        'b' => array(
            'disabled' => 'disabled',
        ),
    ),    
)
EOD
                        )
                        . "</pre>",
                ),                    
                
            ),
            array(
                'field_id'      => 'radio_multiple',
                'title'         => __( 'Multiple Sets', 'admin-page-framework-loader' ),
                'tip'           => __( 'Multiple sets of radio buttons. The horizontal line is set with the <code>delimiter</code> argument.', 'admin-page-framework-loader' ),
                'type'          => 'radio',
                'delimiter'     => '<hr />',
                'default'       => 1,
                'label'         => array( 
                    1 => 'one',
                    2 => 'two',
                ),
                'attributes'    => array(
                    'field' => array(
                        'style' => 'width: 100%;',
                    ),
                ),
                array(
                    'default'   => 5,
                    'label'     => array( 
                        3 => 'three',
                        4 => 'four',    
                        5 => 'five' 
                    ),
                ),
                array(
                    'default'   => 7,
                    'label'     => array( 
                        6 => 'six',
                        7 => 'seven',
                        8 => 'eight',
                        9 => 'nine' 
                    ),
                ),
                'description'   => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'radio',
    'default'       => 1,
    'label'         => array( 
        1 => 'one',
        2 => 'two',
    ),
    array(
        'default'   => 5,
        'label'     => array( 
            3 => 'three',
            4 => 'four',    
            5 => 'five' 
        ),
    ),
    array(
        'default'   => 7,
        'label'     => array( 
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine' 
        ),
    ),
)
EOD
                        )
                        . "</pre>",
                ),                 
            ),
            array( 
                'field_id'      => 'radio_repeatable',
                'title'         => __( 'Repeatable', 'admin-page-framework-loader' ),
                'type'          => 'radio',
                'label'         => array( 
                    1 => __( 'On', 'admin-page-framework-loader' ),
                    0 => __( 'Off', 'admin-page-framework-loader' ),
                ),
                'default'       => 0, // set the key of the label array
                'repeatable'    => true,
                'description'   => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'radio',
    'label'         => array( 
        1 => __( 'On', 'admin-page-framework-loader' ),
        0 => __( 'Off', 'admin-page-framework-loader' ),
    ),
    'default'       => 0,
    'repeatable'    => true,
)
EOD
                        )
                        . "</pre>",
                ),            
            ),    
            array(
                'field_id'      => 'radio_sortable',
                'title'         => __( 'Sortable', 'admin-page-framework-loader' ),
                'type'          => 'radio',
                'label'         => array( 
                    1 => __( 'One', 'admin-page-framework-loader' ),
                    2 => __( 'Two', 'admin-page-framework-loader' ),
                    3 => __( 'Three', 'admin-page-framework-loader' ),
                ),
                'default'       => 2, // set the key of the label array
                'sortable'      => true,
                array(), // the second item
                array(), // the third item
                array(), // the fourth item
                'description'   => array(
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'radio',
    'label'         => array( 
        1 => __( 'One', 'admin-page-framework-loader' ),
        2 => __( 'Two', 'admin-page-framework-loader' ),
        3 => __( 'Three', 'admin-page-framework-loader' ),
    ),
    'default'       => 2,
    'sortable'      => true,
    array(), // the second item
    array(), // the third item
    array(), // the fourth item
)
EOD
                        )
                        . "</pre>",
                ),               
            )
        );      
        
    }

}
