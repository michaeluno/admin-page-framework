<?php
/**
 * Admin Page Framework - Demo
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed GPLv2
 * 
 */

class APF_TaxonomyField extends AdminPageFramework_TaxonomyField {
        
    /*
     * ( optional ) Use the setUp() method to define settings of this meta box.
     */
    public function setUp() {

        /*
         * ( optional ) Adds setting fields into the meta box.
         */
        $this->addSettingFields(
            array(
                'field_id' => 'text_field',
                'type' => 'text',
                'title' => __( 'Text Input', 'admin-page-framework-demo' ),
                'description' => __( 'The description for the field.', 'admin-page-framework-demo' ),
                'help' => 'This is help text.',
                'help_aside' => 'This is additional help text which goes to the side bar of the help pane.',
            ),
            array(
                'field_id' => 'text_field_repeatable',
                'type' => 'text',
                'title' => __( 'Text Repeatable', 'admin-page-framework-demo' ),
                'repeatable' =>    true
            ),     
            array(
                'field_id' => 'textarea_field',
                'type' => 'textarea',
                'title' => __( 'Text Area', 'admin-page-framework-demo' ),
                'description' => __( 'The description for the field.', 'admin-page-framework-demo' ),
                'help' => __( 'This a <em>text area</em> input field, which is larger than the <em>text</em> input field.', 'admin-page-framework-demo' ),
                'default' => __( 'This is a default text value.', 'admin-page-framework-demo' ),
                'attributes' => array(
                    'cols' => 40,     
                ),
            ),
            array(
                'field_id' => 'image_upload',
                'type' => 'image',
                'title' => __( 'Image Upload', 'admin-page-framework-demo' ),
            )
        );     
    
        // Customize the sorting algorithm of the terms of a custom column.
        add_filter( 'get_terms', array( $this, 'replyToSortCustomColumn' ), 10, 3 );
    
    }
        
    /*
     * ( optional ) modify the columns of the term listing table
     */
    public function sortable_columns_APF_TaxonomyField( $aColumn ) { // sortable_column_{instantiated class name}
        
        return array( 
                'custom' => 'custom',
            ) 
            + $aColumn;
        
    }

    public function columns_APF_TaxonomyField( $aColumn ) { // column_{instantiated class name}
        
        unset( $aColumn['description'] );
        return array( 
                'cb' => $aColumn['cb'],
                'thumbnail' => __( 'Thumbnail', 'admin-page-framework-demo' ),
                'custom' => __( 'Custom Column', 'admin-page-framework-demo' ),
            ) 
            + $aColumn;
        
    }
    
    /*
     * ( optional ) output the stored option to the custom column
     */    
    public function cell_APF_TaxonomyField( $sCellHTML, $sColumnSlug, $iTermID ) { // cell_{instantiated class name}
        
        if ( ! $iTermID || $sColumnSlug != 'thumbnail' ) { return $sCellHTML; }
        
        $aOptions = get_option( 'APF_TaxonomyField', array() ); // by default the class name is the option key.
        return isset( $aOptions[ $iTermID ][ 'image_upload' ] ) && $aOptions[ $iTermID ][ 'image_upload' ]
            ? "<img src='{$aOptions[ $iTermID ][ 'image_upload' ]}' style='max-height: 72px; max-width: 120px;'/>"
            : $sCellHTML;
        
    }
    
    public function cell_APF_TaxonomyField_custom( $sCellHTML, $iTermID ) { // cell_{instantiated class name}_{cell slug}
        
        // Using AdminPageFramework::getOption() is another way to retrieve an option value.
        return AdminPageFramework::getOption( 'APF_TaxonomyField', array( $iTermID, 'text_field' ) );            
        
    }
    
    /*
     * ( optional ) Use this method to insert your custom text.
     */
    public function do_APF_TaxonomyField() { // do_{instantiated class name}
        ?>
            <p><?php _e( 'This text is inserted with the <code>do_{instantiated class name}</code> hook.', 'admin-page-framework-demo' ) ?></p>
        <?php     
    }

    /**
     * Customizes the sorting algorithm of a custom column.
     */
    public function replyToSortCustomColumn( $aTerms, $aTaxonomies, $aArgs ) {
        
        if ( 'edit-tags.php' == $GLOBALS['pagenow'] && isset( $_GET{'orderby'} ) && 'custom' == $_GET{'orderby'} ) {
            usort( $aTerms, array( $this, '_replyToSortByCustomOptionValue' ) );
        }
        return $aTerms;
        
    }
        public function _replyToSortByCustomOptionValue( $oTermA, $oTermB ) {
            
            $_sClassName = get_class( $this ); // the instantiated class name is the option key by default.
            $_sTextFieldA = AdminPageFramework::getOption( $_sClassName, array( $oTermA->term_id, 'text_field' ) );
            $_sTextFieldB = AdminPageFramework::getOption( $_sClassName, array( $oTermB->term_id, 'text_field' ) );
            return isset( $_GET['order'] ) && 'asc' == $_GET['order']
                ? strnatcmp( $_sTextFieldA, $_sTextFieldB )
                : strnatcmp( $_sTextFieldB, $_sTextFieldA );
            
        }    
    
    
    /*
     * ( optional ) Use this method to validate submitted option values.
     */
    public function validation_APF_TaxonomyField( $aNewOptions, $aOldOptions ) {

        // Do something to compare the values.
        return $aNewOptions;
    }
    
}