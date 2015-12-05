<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods for creating fields in the taxonomy page (edit-tags.php).
 * 
 * @abstract
 * @since       3.0.0
 * @package     AdminPageFramework
 * @subpackage  TaxonomyField
 * @extends     AdminPageFramework_TaxonomyField_Controller
 */
abstract class AdminPageFramework_TaxonomyField extends AdminPageFramework_TaxonomyField_Controller {
    
    /**
     * Defines the class object structure type.
     * 
     * This is used to create a property object as well as to define the form element structure.
     * 
     * @since       3.0.0
     * @since       3.7.0      Changed the name from `$_sStructureType`. Changed the default value from `taxonomy`.
     * @internal
     */
    static protected $_sStructureType = 'taxonomy_field';
    
    /**
     * Constructs the class object instance of AdminPageFramework_TaxonomyField.
     * 
     * Handles setting up properties and hooks.
     * 
     * <h4>Examples</h4>
     * <code>
     * new APF_TaxonomyField( 'apf_sample_taxonomy' ); // taxonomy slug
     * </code>
     * 
     * @since       3.0.0
     * @param       array|string    The taxonomy slug(s). If multiple slugs need to be passed, enclose them in an array and pass the array.
     * @param       string          The option key used for the options table to save the data. By default, the instantiated class name will be applied.
     * @param       string          The access rights. Default: `manage_options`.
     * @param       string          The text domain. Default: `admin-page-framework`.
     * @return      void
     */ 
    function __construct( $asTaxonomySlug, $sOptionKey='', $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {
        
        if ( empty( $asTaxonomySlug ) ) { 
            // @todo trigger a PHP warning
            return; 
        }
        
        // Properties 
        $this->oProp                    = new AdminPageFramework_Property_TaxonomyField( 
            $this, 
            get_class( $this ), 
            $sCapability, 
            $sTextDomain, 
            self::$_sStructureType 
        );     
        $this->oProp->aTaxonomySlugs    = ( array ) $asTaxonomySlug;
        $this->oProp->sOptionKey        = $sOptionKey 
            ? $sOptionKey 
            : $this->oProp->sClassName;
        
        parent::__construct( $this->oProp );
                
        $this->oUtil->addAndDoAction( $this, "start_{$this->oProp->sClassName}" );     
        
    }

}