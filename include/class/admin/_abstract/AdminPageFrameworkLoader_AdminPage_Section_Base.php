<?php
/**
 * Admin Page Framework Loader
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 */

/**
 * Provides an abstract base for adding form sections.
 * 
 * @since       3.5.3
 */
abstract class AdminPageFrameworkLoader_AdminPage_Section_Base extends AdminPageFrameworkLoader_AdminPage_RootBase {

    /**
     * Stores the factory object.
     */
    public $oFactory;

    /**
     * Stores the associated page slug with the adding section.
     */
    public $sPageSlug;    

    /**
     * Stores the associated tab slug with the adding section.
     */
    public $sTabSlug;    

    /**
     * Stores the section ID.
     */
    public $sSectionID;    
    
    /**
     * Sets up hooks and properties.
     */
    public function __construct( $oFactory, $sPageSlug, array $aSectionDefinition ) {
        
        $this->oFactory     = $oFactory;
        $this->sPageSlug    = $sPageSlug;
        $aSectionDefinition = $aSectionDefinition + array(
            'tab_slug'      => '',
            'section_id'    => '',
        );
        $this->sTabSlug     = $aSectionDefinition['tab_slug'];
        $this->sSectionID   = $aSectionDefinition['section_id'];
        
        if ( ! $this->sSectionID ) {
            return;
        }
        $this->_addSection( $oFactory, $sPageSlug, $aSectionDefinition );
        
        $this->construct( $oFactory );
        
    }
    
    private function _addSection( $oFactory, $sPageSlug, array $aSectionDefinition ) {
        
        add_action( 'validation_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'validate' ), 10, 4 );
        
        $oFactory->addSettingSections(
            $sPageSlug,    // target page slug
            $aSectionDefinition
        );        
        
        // Set the target section id
        $oFactory->addSettingFields(
            $this->sSectionID
        );
        
        // Call the user method
        $this->addFields( $oFactory, $this->sSectionID );

    }

    /**
     * Called when adding fields.
     * @remark      This method should be overridden in each extended class.
     */
    public function addFields( $oFactory, $sSectionID ) {}
 
    /**
     * Called upon form validation.
     */
    public function validate( $aSubmit, $aOldInput, $oFactory, $aSubmitInfo ) {}
 
}