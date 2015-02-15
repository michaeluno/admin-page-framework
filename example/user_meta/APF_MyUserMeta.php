<?php
/**
 * Admin Page Framework - Loader
 * 
 * Loads Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
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
                'title'         => __( 'Text', 'admin-page-framework-demo' ),
                'repeatable'    => true,
                'sortable'      => true,
                'description'   => 'Type something here.',   
            ),        
            array(    
                'field_id'      => 'text_area',
                'type'          => 'textarea',
                'title'         => __( 'Text Area', 'admin-page-framework-demo' ),
                'default'       => 'Hi there!',   
            ),        
            array(    
                'field_id'      => 'radio_buttons',
                'type'          => 'radio',
                'title'         => __( 'Radio', 'admin-page-framework-demo' ),
                'label'         => array(
                    'a' => 'A',
                    'b' => 'B',
                    'c' => 'C',
                ),
                'default'       => 'a',
            )          
        );      
        
    }
    
    /**
     * A pre-defined validation callback method.
     */
    public function validate( $aInput, $aOldInput, $oFactory ) {
        return $aInput;        
    }
    
}