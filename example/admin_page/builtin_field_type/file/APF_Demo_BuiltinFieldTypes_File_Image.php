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
class APF_Demo_BuiltinFieldTypes_File_Image {

    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'apf_builtin_field_types';

    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'files';

    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'image_select';

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
                'title'         => __( 'Image Selector', 'admin-page-framework-loader' ),
                'description'   => __( 'Set an image url with jQuwey based image selector.', 'admin-page-framework-loader' ),
                'tip'           => __( 'The <code>image</code> field type allows your users to submit their images.', 'admin-page-framework-loader' ),
            )
        );

        // Fields
        $oFactory->addSettingFields(
            $this->sSectionID,
            array(
                'field_id'      => 'image_select_field',
                'title'         => __( 'Select an Image', 'admin-page-framework-loader' ),
                'type'          => 'image',
                'label'         => __( 'First', 'admin-page-framework-loader' ),
                'default'       => plugins_url( 'asset/image/demo/wordpress-logo-2x.png', AdminPageFrameworkLoader_Registry::$sFilePath ),
                'allow_external_source' => false,
                'attributes'    => array(
                    'preview' => array(
                        'style' => 'max-width:300px;' // determines the size of the preview image. // margin-left: auto; margin-right: auto; will make the image in the center.
                    ),
                ),
                array(
                    'label'         => __( 'Second', 'admin-page-framework-loader' ),
                    'default'       => '',
                    'allow_external_source' => true,
                    'attributes'    => array(
                        'input'     => array(
                            'style' => 'background-color: #F5FFDF',
                        ),
                        'button'    => array(
                            'style' => 'background-color: #E1FCD2',
                        ),
                        'remove_button'    => array(
                            'style' => 'background-color: #E1FCD2',
                        ),
                    ),
                ),
                array(
                    'label'         => __( 'Third', 'admin-page-framework-loader' ),
                    'default'       => '',
                ),
                'description'   => array(
                    __( 'See the button and the input colors of the second item are different. This is done by setting the attributes individually.', 'admin-page-framework-loader' ),
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'          => 'image',
    'allow_external_source' => false,
    'attributes'    => array(
        'preview' => array(
            'style' => 'max-width:300px;',
        ),
    ),
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'              => 'image_with_attributes',
                'title'                 => __( 'Save Image Attributes', 'admin-page-framework-loader' ),
                'type'                  => 'image',
                'attributes_to_store'   => array( 'alt', 'id', 'title', 'caption', 'width', 'height', 'align', 'link' ), // some attributes cannot be captured with external URLs and the old media uploader.
                'attributes'            => array(
                    // To use a custom text label, pass the label to the 'data-label' attribute.
                    'button'        => array(
                        'data-label' => __( 'Select Image', 'admin-page-framework-loader' ),
                    ),
                    'remove_button' => array(      // 3.2.0+
                        'data-label' => __( 'Remove', 'admin-page-framework-loader' ), // will set the Remove button label instead of the dashicon
                    ),
                    'preview' => array(
                        'style' => 'max-width: 300px;'
                    ),
                ),
                'description'   => array(
                    __( 'Capturing additional attributes is supported.', 'admin-page-framework-loader' ),
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'                => 'image',
    'attributes_to_store' => array( 
        'alt', 'id', 'title', 'caption', 
        'width', 'height', 'align', 'link',
    ),
    'attributes'            => array(
        // To use a custom text label, pass the label to the 'data-label' attribute.
        'button'        => array(
            'data-label' => 'Select Image',
        ),
        'remove_button' => array(
            'data-label' => 'Remove',
        ),
        'preview' => array(
            'style' => 'max-width: 300px;'
        ),   
    ),
)
EOD
                        )
                        . "</pre>",
                ),
            ),
            array(
                'field_id'              => 'image_select_field_repeatable_and_sortable',
                'title'                 => __( 'Repeatable & Sortable', 'admin-page-framework-loader' ),
                'type'                  => 'image',
                'repeatable'            => true,
                'sortable'              => true,
                'attributes'            => array(
                    'preview' => array(
                        'style' => 'max-width: 200px;'
                    ),
                ),
                'description'   => array(
                    __( 'Image fields can be repeatable and sortable.', 'admin-page-framework-loader' ),
                    "<pre class='field-argument-example'>"
                        . $oFactory->oWPRMParser->getSyntaxHighlightedPHPCode(
<<<EOD
array(
    'type'                  => 'image',
    'repeatable'            => true,
    'sortable'              => true,
    'attributes'            => array(
        'preview' => array(
            'style' => 'max-width: 200px;'
        ),
    ),    
)
EOD
                        )
                        . "</pre>",
                ),
            )
        );

    }

}
