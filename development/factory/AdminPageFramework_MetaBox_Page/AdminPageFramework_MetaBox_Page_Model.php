<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_MetaBox_Page_Model' ) ) :
/**
 * Provides model methods for creating meta boxes in pages added by the framework.
 * 
 * @abstract
 * @since           3.0.4
 * @package         AdminPageFramework
 * @subpackage      PageMetaBox
 * @internal
 */
abstract class AdminPageFramework_MetaBox_Page_Model extends AdminPageFramework_MetaBox_Page_Router {

    /**
     * Defines the fields type.
     * @since       3.0.0
     * @internal
     */
    static protected $_sFieldsType = 'page_meta_box';

    /**
     * Sets up properties and hooks.
     * 
     * @since       3.0.4
     */
    function __construct( $sMetaBoxID, $sTitle, $asPageSlugs=array(), $sContext='normal', $sPriority='default', $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {     
                
        /* The property object needs to be done first */
        $this->oProp             = new AdminPageFramework_Property_MetaBox_Page( $this, get_class( $this ), $sCapability, $sTextDomain, self::$_sFieldsType );     
        $this->oProp->aPageSlugs = is_string( $asPageSlugs ) ? array( $asPageSlugs ) : $asPageSlugs; // must be set before the isInThePage() method is used.
        
        parent::__construct( $sMetaBoxID, $sTitle, $asPageSlugs, $sContext, $sPriority, $sCapability, $sTextDomain );
            
    }
        
    /**
     * Sets up validation hooks.
     * 
     * @since       3.3.0
     */
    protected function _setUpValidationHooks( $oScreen ) {

        /* Validation hooks */
        foreach( $this->oProp->aPageSlugs as $_sIndexOrPageSlug => $_asTabArrayOrPageSlug ) {
            
            if ( is_string( $_asTabArrayOrPageSlug ) ) {     
                $_sPageSlug = $_asTabArrayOrPageSlug;
                add_filter( "validation_saved_options_{$_sPageSlug}", array( $this, '_replyToFilterPageOptions' ) );
                add_filter( "validation_{$_sPageSlug}", array( $this, '_replyToValidateOptions' ), 10, 3 );
                continue;
            }
            
            // At this point, the array key is the page slug.
            $_sPageSlug = $_sIndexOrPageSlug;
            $_aTabs     = $_asTabArrayOrPageSlug;
            add_filter( "validation_{$_sPageSlug}", array( $this, '_replyToValidateOptions' ), 10, 3 );
            foreach( $_aTabs as $_sTabSlug ) {
                add_filter( "validation_saved_options_{$_sPageSlug}_{$_sTabSlug}", array( $this, '_replyToFilterPageOptions' ) );
            }
            
        }
    
    }        

    /**
     * Returns the field output.
     * 
     * @since       3.0.0
     * @internal
     */
    protected function getFieldOutput( $aField ) {
        
        /* Since meta box fields don't have the `option_key` key which is required to compose the name attribute in the regular pages. */
        $sOptionKey             = $this->_getOptionKey();
        $aField['option_key']   = $sOptionKey ? $sOptionKey : null;
        $aField['page_slug']    = isset( $_GET['page'] ) ? $_GET['page'] : ''; // set an empty string to make it yield true for isset() so that saved options will be checked.

        return parent::getFieldOutput( $aField );
        
    }

        /**
         * Returns the currently loading page's option key if the page has the admin page object.
         * @since       3.0.0
         * @internal
         */
        private function _getOptionkey() {
            return isset( $_GET['page'] ) 
                ? $this->oProp->getOptionKey( $_GET['page'] )
                : null;
        }
                    
    /**
     * Adds the defined meta box.
     * 
     * @internal
     * @since       3.0.0
     * @remark      uses `add_meta_box()`.
     * @remark      Before this method is called, the pages and in-page tabs need to be registered already.
     * @remark      A callback for the `add_meta_boxes` hook.
     * @return      void
     */ 
    public function _replyToAddMetaBox( $sPageHook='' ) {

        foreach( $this->oProp->aPageSlugs as $sKey => $asPage ) {
            
            if ( is_string( $asPage ) )  {
                $this->_addMetaBox( $asPage );
                continue;
            }
            if ( ! is_array( $asPage ) ) { continue; }
            
            $sPageSlug = $sKey;
            foreach( $asPage as $sTabSlug ) {
                
                if ( ! $this->oProp->isCurrentTab( $sTabSlug ) ) { continue; }
                
                $this->_addMetaBox( $sPageSlug );
                
            }
            
        }
                
    }    
        /**
         * Adds meta box with the given page slug.
         * 
         * @since       3.0.0
         * @internal
         */
        private function _addMetaBox( $sPageSlug ) {

            add_meta_box( 
                $this->oProp->sMetaBoxID,                       // id
                $this->oProp->sTitle,                           // title
                array( $this, '_replyToPrintMetaBoxContents' ), // callback
                $this->oProp->_getScreenIDOfPage( $sPageSlug ), // screen ID
                $this->oProp->sContext,                         // context
                $this->oProp->sPriority,                        // priority
                null                                            // argument (deprecated)
            );     
            
        }

    /**
     * Filters the page option array.
     * 
     * This is triggered from the system validation method of the main Admin Page Framework factory class with the `validation_saved_options_{page slug}` filter hook.
     * 
     * @since       3.0.0
     * @param       array       $aPageOptions
     */
    public function _replyToFilterPageOptions( $aPageOptions ) {
        return $this->oForm->dropRepeatableElements( $aPageOptions );    
    }
    
    /**
     * Validates the submitted option values.
     * 
     * This method is triggered with the `validation_{page slug}` or `validation_{page slug}_{tab slug}` method of the main Admin Page Framework factory class.
     * 
     * @internal
     * @sicne       3.0.0
     * @param       array       $aNewPageOptions        The array holing the field values of the page sent from the framework page class (the main class).
     * @param       array       $aOldPageOptions        The array holing the saved options of the page. Note that this will be empty if non of generic page fields are created.
     */
    public function _replyToValidateOptions( $aNewPageOptions, $aOldPageOptions ) {
        
        // The field values of this class will not be included in the parameter array. So get them.
        $_aFieldsModel          = $this->oForm->getFieldsModel();
        $_aNewMetaBoxInput      = $this->oUtil->castArrayContents( $_aFieldsModel, $_POST );
        $_aOldMetaBoxInput      = $this->oUtil->castArrayContents( $_aFieldsModel, $aOldPageOptions );
        $_aOtherOldMetaBoxInput = $this->oUtil->invertCastArrayContents( $aOldPageOptions, $_aFieldsModel );

        // Apply filters - third party scripts will have access to the input.
        $_aNewMetaBoxInput = stripslashes_deep( $_aNewMetaBoxInput ); // fixes magic quotes
        $_aNewMetaBoxInput = $this->oUtil->addAndApplyFilters( $this, "validation_{$this->oProp->sClassName}", $_aNewMetaBoxInput, $_aOldMetaBoxInput, $this );
    
        // Now merge the input values with the passed page options, and plus the old data to cover different in-page tab field options.
        return $this->oUtil->uniteArrays( $_aNewMetaBoxInput, $aNewPageOptions, $_aOtherOldMetaBoxInput );
                        
    }
    
    /**
     * Registers form fields and sections.
     * 
     * @since       3.0.0
     * @since       3.3.0       Changed the name from `_replyToRegisterFormElements()`. Changed the scope to `protected`.
     * @internal
     */
    public function _registerFormElements( $oScreen ) {
                
        // Schedule to add head tag elements and help pane contents.     
        if ( ! $this->_isInThePage() ) return;
        
        $this->_loadDefaultFieldTypeDefinitions();
        
        // Format the fields array.
        $this->oForm->format();
        $this->oForm->applyConditions(); // will create the conditioned elements.
        
        // Add the repeatable section elements to the fields definition array.
        $this->oForm->setDynamicElements( $this->oProp->aOptions ); // will update $this->oForm->aConditionedFields
                        
        $this->_registerFields( $this->oForm->aConditionedFields );

    }     
            
}
endif;