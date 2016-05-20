<?php
/**
 * Admin Page Framework - Demo
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds a section in a tab.
 * 
 * @package     AdminPageFramework
 * @subpackage  Example
 */
class APF_Demo_AdvancedUsage_Mixed_Inline {
    
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_advanced_usage';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'mixed_types';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'mixed';
        
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
                'title'         => __( 'Inline Mixed', 'admin-page-framework-loader' ),
                'description'   => __( 'As of v3.8, it is possible to mix fields with different field types as an inline element without using sub-fields.', 'admin-page-framework-loader' ),
            )
        );   

        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID       
            array(
                'field_id'      => 'inline_mixed',
                'title'         => __( 'Inline Mixed', 'admin-page-framework-loader' ),            
                'content'       => array(
                    '%1$s %2$s %3$s %4$s',
                    array(
                        'field_id'        => 'enable',
                        'type'            => 'checkbox',
                        'label'           => __( 'Do something in', 'admin-page-framework-loader' ),
                    ),                
                    array(
                        'field_id'        => 'interval',
                        'type'            => 'number',
                        'label_min_width' => '',
                        'default'         => 3,
                        'attributes'      => array(
                            'style' => 'width: 80px;',
                        ),
                    ),    
                    array(
                        'field_id'        => 'interval_unit',
                        'type'            => 'select',
                        'label_min_width' => '',
                        'label'           => array(
                            'hour'    => __( 'hours', 'admin-page-framework-loader' ),
                            'day'     => __( 'days', 'admin-page-framework-loader' ),
                            'week'    => __( 'weeks', 'admin-page-framework-loader' ),
                        ),                    
                        'default'         => 'day',
                    ),
                    array(
                        'field_id'      => '_text',
                        'content'       => __( 'to do something else.', 'admin-page-framework-loader' ),
                    ),                                 
                ),
            ),
            array(
                'field_id'      => 'shipping_address',
                'title'         => __( 'Shipping Information', 'admin-page-framework-loader' ),
                'repeatable'    => true,
                'sortable'      => true,
                'content'       => array(
                    '%1$s%2$s%3$s%4$s%5$s%6$s%7$s%8$s%9$s',
                    array(
                        'field_id'      => 'first_name',
                        'type'          => 'text',
                        'title'         => __( 'First Name', 'admin-page-framework-loader' ),
                        'attributes'    => array(
                            'fieldset'  => array(
                                'style'  => 'width: 50%',
                            ),
                        ),                        
                    ),
                    array(
                        'field_id'      => 'last_name',
                        'type'          => 'text',
                        'title'         => __( 'Last Name', 'admin-page-framework-loader' ),
                        'attributes'    => array(
                            'fieldset'  => array(
                                'style'  => 'width: 50%',
                            ),
                      
                        ),
                    ),
                    array(
                        'field_id'      => 'mailing_address',
                        'type'          => 'text',
                        'title'         => __( 'Street Address', 'admin-page-framework-loader' ),
                        'attributes'    => array(
                            'fieldset'  => array(
                                'style'  => 'width: 100%'
                            ),
                        ),                                        
                    ),
                    array(
                        'field_id'      => 'city',
                        'type'          => 'text',
                        'title'         => __( 'City/Town', 'admin-page-framework-loader' ),
                        'attributes'    => array(
                            'fieldset'  => array(
                                'style'  => 'width: 38%'
                            ),
                        ),
                    ),
                    array(
                        'field_id'      => 'state',
                        'type'          => 'text',
                        'title'         => __( 'State/Province', 'admin-page-framework-loader' ),
                        'attributes'    => array(
                            'fieldset'  => array(
                                'style'  => 'width: 38%'
                            ),
                        ),                
                    ),
                    array(
                        'field_id'      => 'zip',
                        'type'          => 'text',
                        'title'         => __( 'Zip/Postal Code', 'admin-page-framework-loader' ),
                        'attributes'    => array(
                            'fieldset'  => array(
                                'style'  => 'width: 24%; clear:right;'
                            ),
                        ),  
                    ),                    
                    array(
                        'field_id'      => 'telephone',
                        'type'          => 'text',
                        'title'         => __( 'Tel. No.', 'admin-page-framework-loader' ),
                        'attributes'    => array(
                            'fieldset'  => array(
                                'style'  => 'width: 33%'
                            ),
                        ),
                    ),
                    array(
                        'field_id'      => 'fax',
                        'type'          => 'text',
                        'title'         => __( 'Fax No.', 'admin-page-framework-loader' ),
                        'attributes'    => array(
                            'fieldset'  => array(
                                'style'  => 'width: 33%'
                            ),
                        ),            
                    ),
                    array(
                        'field_id'      => 'email',
                        'type'          => 'text',
                        'title'         => __( 'Email', 'admin-page-framework-loader' ),
                        'attributes'    => array(
                            'fieldset'  => array(
                                'style'  => 'width: 34%'
                            ),
                        ),                  
                    ),                         
                ),
                
                                
            )
        );

    }

}
