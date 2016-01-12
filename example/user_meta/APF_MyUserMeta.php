<?php
/**
 * Admin Page Framework - Loader
 * 
 * Loads Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed GPLv2
 */

/**
 * Demonstrates usage of the user meta factory class of Admin Page Framework.
 * 
 * @since       3.5.3
 */
class APF_Demo_MyUserMeta extends AdminPageFramework_UserMeta {
	
    public function setUp() {
                   
        $this->addSettingFields(
            array(    
                'field_id'      => 'text_field',
                'type'          => 'text',
                'title'         => __( 'Text', 'admin-page-framework-loader' ),
                'repeatable'    => true,
                'sortable'      => true,
                'description'   => 'Type something here.',   
            ),        
            array(    
                'field_id'      => 'text_area',
                'type'          => 'textarea',
                'title'         => __( 'Text Area', 'admin-page-framework-loader' ),
                'default'       => 'Hi there!',   
            ),    
            array(
                'field_id'      => 'image',
                'type'          => 'image',
                'title'         => __( 'Image', 'admin-page-framework-loader' ),
                'attributes'    => array(
                    'preview' => array(
                        'style' => 'max-width: 200px;',
                    ),
                ),
            ),
            array(
                'field_id'      => 'color',
                'type'          => 'color',
                'title'         => __( 'Color', 'admin-page-framework-loader' ),
            ),
            array(    
                'field_id'      => 'radio_buttons',
                'type'          => 'radio',
                'title'         => __( 'Radio', 'admin-page-framework-loader' ),
                'label'         => array(
                    'a' => 'A',
                    'b' => 'B',
                    'c' => 'C',
                ),
                'default'       => 'b',
            )          
        );      
        
    }
    
    /**
     * A pre-defined validation callback method.
     * @return      array
     */
    public function validate( $aInputs, $aOldInputs, $oFactory ) {
        return $aInputs;
    }
    
}

new APF_Demo_MyUserMeta; 
