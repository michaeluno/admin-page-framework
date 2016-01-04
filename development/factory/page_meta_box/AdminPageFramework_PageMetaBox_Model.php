<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 */

/**
 * Provides methods which mainly deal with the stored data for creating meta boxes in admin pages added by the framework.
 * 
 * @abstract
 * @since           3.0.4
 * @package         AdminPageFramework
 * @subpackage      PageMetaBox
 * @internal
 */
abstract class AdminPageFramework_PageMetaBox_Model extends AdminPageFramework_PageMetaBox_Router {

    /**
     * Defines the class object structure type.
     * 
     * This is used to create a property object as well as to define the form element structure.
     * 
     * @since       3.0.0
     * @since       3.7.0      Changed the name from `$_sStructureType`.
     * @internal
     */
    static protected $_sStructureType = 'page_meta_box';

    /**
     * Sets up properties and hooks.
     * 
     * @since       3.0.4
     */
    public function __construct( $sMetaBoxID, $sTitle, $asPageSlugs=array(), $sContext='normal', $sPriority='default', $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {     
                
        // The property object needs to be done first before the parent constructor.
        $this->oProp             = new AdminPageFramework_Property_MetaBox_Page( 
            $this, 
            get_class( $this ), 
            $sCapability, 
            $sTextDomain, 
            self::$_sStructureType 
        );
        
        // This property item must be set before the isInThePage() method is used.
        $this->oProp->aPageSlugs = is_string( $asPageSlugs ) 
            ? array( $asPageSlugs ) 
            : $asPageSlugs; 
        
        parent::__construct( $sMetaBoxID, $sTitle, $asPageSlugs, $sContext, $sPriority, $sCapability, $sTextDomain );
        
    }
    
    /**
     * A validation callback method.
     * 
     * The user may just override this method instead of defining a `validation_{...}` callback method.
     * 
     * @since       3.4.1
     * @since       3.5.3       Moved from `AdminPageFramework_Factory_Model`.
     * @todo        Examine if the forth parameter of submit info can be added or not.
     * @param       array       $aInput         The submit form data.
     * @param       array       $aOldInput      The previously submit form data stored in the database.
     * @param       object      $oFactory       The page meta box factory object.
     * @param       array       $aSubmitInfo    [3.5.3+] An array holding submit information such as which submit field is pressed.
     * @remark      Not defining this method for backward compatibility to avoid strict standard warnings of PHP.
     */
    // public function validate( $aInput, $aOldInput, $oFactory, $aSubmitInfo ) {
        // return $aInput;
    // }              
        
    /**
     * Sets up validation hooks.
     * 
     * @internal
     * @since       3.3.0
     * @return      void
     */
    protected function _setUpValidationHooks( $oScreen ) {

        // Validation hooks
        foreach( $this->oProp->aPageSlugs as $_sIndexOrPageSlug => $_asTabArrayOrPageSlug ) {
            
            if ( is_string( $_asTabArrayOrPageSlug ) ) {     
                $_sPageSlug = $_asTabArrayOrPageSlug;
                add_filter( "validation_saved_options_without_dynamic_elements_{$_sPageSlug}", array( $this, '_replyToFilterPageOptionsWODynamicElements' ), 10, 2 );  // 3.4.1+
                add_filter( "validation_{$_sPageSlug}", array( $this, '_replyToValidateOptions' ), 10, 4 );
                add_filter( "options_update_status_{$_sPageSlug}", array( $this, '_replyToModifyOptionsUpdateStatus' ) );
                continue;
            }
            
            // At this point, the array key is the page slug. It means the user specified the tab(s).
            $_sPageSlug = $_sIndexOrPageSlug;
            $_aTabs     = $_asTabArrayOrPageSlug;
            foreach( $_aTabs as $_sTabSlug ) {
                add_filter( "validation_{$_sPageSlug}_{$_sTabSlug}", array( $this, '_replyToValidateOptions' ), 10, 4 );
                add_filter( "validation_saved_options_without_dynamic_elements_{$_sPageSlug}_{$_sTabSlug}", array( $this, '_replyToFilterPageOptionsWODynamicElements' ), 10, 2 ); // 3.4.1+
                add_filter( "options_update_status_{$_sPageSlug}_{$_sTabSlug}", array( $this, '_replyToModifyOptionsUpdateStatus' ) );
            }
            
        }
    
    }        
                    
    /**
     * Adds the defined meta box.
     * 
     * @internal
     * @since       3.0.0
     * @remark      Before this method is called, the pages and in-page tabs need to be registered already.
     * @return      void
     * @callback    action      add_meta_boxes
     */ 
    public function _replyToAddMetaBox( $sPageHook='' ) {
        foreach( $this->oProp->aPageSlugs as $sKey => $_asPage ) {
            if ( is_string( $_asPage ) )  {
                $this->_addMetaBox( $_asPage );
                continue;
            }            
            $this->_addMetaBoxes( $sKey, $_asPage );            
        }
    }    
        /**
         * Adds meta boxes.
         * 
         * @since       3.7.0
         * @internal
         * @return      void
         */
        private function _addMetaBoxes( $sPageSlug, $asPage ) {
            foreach( $this->oUtil->getAsArray( $asPage ) as $_sTabSlug ) {
                if ( ! $this->oProp->isCurrentTab( $_sTabSlug ) ) { 
                    continue; 
                }
                $this->_addMetaBox( $sPageSlug );
            }         
        }
        /**
         * Adds meta box with the given page slug.
         * 
         * @since       3.0.0
         * @internal
         * @uses        add_meta_box()
         * @return      void
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
     * @deprecated  3.4.1
     * @internal
     */
    public function _replyToFilterPageOptions( $aPageOptions ) {
        return $aPageOptions;
    }
    /**
     * Filters the array of the options without dynamic elements.
     * 
     * @since       3.4.1       Deprecated `_replyToFilterPageOptions()`.
     * @callback    filter      validation_saved_options_without_dynamic_elements_{$_sPageSlug}
     * @callback    filter      validation_saved_options_without_dynamic_elements_{page slug}_{tab slug}
     * @internal
     */
    public function _replyToFilterPageOptionsWODynamicElements( $aOptionsWODynamicElements, $oFactory ) {
        return $this->oForm->dropRepeatableElements( $aOptionsWODynamicElements );
    }
    
    /**
     * Validates the submitted option values.
     * 
     * This method is triggered with the `validation_{page slug}` or `validation_{page slug}_{tab slug}` method of the main Admin Page Framework factory class.
     * 
     * @internal
     * @callback    filter      validation_{page slug}, validation_{page slug}_{tab slug}
     * @sicne       3.0.0       
     * @param       array       $aNewPageOptions        The array holing the field values of the page sent from the framework page class (the main class).
     * @param       array       $aOldPageOptions        The array holing the saved options of the page. Note that this will be empty if non of generic page fields are created.
     * @param       object      $oAdminPage             The admin page factory class object.
     * @param       array       $aSubmitInfo            An array containing submit information such as a pressed submit field ID.
     * @return      array       The validated form input data.
     */
    public function _replyToValidateOptions( $aNewPageOptions, $aOldPageOptions, $oAdminPage, $aSubmitInfo ) {
        
        $_aNewMetaBoxInputs      = $this->oForm->getSubmittedData( $_POST );
        $_aOldMetaBoxInputs      = $this->oUtil->castArrayContents( 
            $this->oForm->getDataStructureFromAddedFieldsets(),   // model
            $aOldPageOptions        // data source
        );

        // Apply filters - third party scripts will have access to the input.
        $_aNewMetaBoxInputsRaw   = $_aNewMetaBoxInputs; // copy one for validation errors.
        $_aNewMetaBoxInputs      = call_user_func_array( 
            array( $this, 'validate' ),     // triggers __call()
            array( $_aNewMetaBoxInputs, $_aOldMetaBoxInputs, $this, $aSubmitInfo ) 
        ); // 3.5.3+
        $_aNewMetaBoxInputs      = $this->oUtil->addAndApplyFilters( 
            $this, 
            "validation_{$this->oProp->sClassName}", 
            $_aNewMetaBoxInputs, 
            $_aOldMetaBoxInputs, 
            $this,
            $aSubmitInfo
        );
    
        // If there are validation errors. set the last input.
        if ( $this->hasFieldError() ) {
            $this->setLastInputs( $_aNewMetaBoxInputsRaw );           
        }    
    
        // Now merge the input values with the passed page options, and plus the old data to cover different in-page tab field options.
        return $this->oUtil->uniteArrays( 
            $_aNewMetaBoxInputs, 
            $aNewPageOptions
        );       
                        
    }

    /**
     * Modifies the options update status array.
     * 
     * This is to insert the 'field_errors' key into the options update status array when there is a field error.
     * 
     * @internal
     * @since       3.4.1
     * @callback    filter      options_update_status_{page slug}
     * @callback    filter      options_update_status_{page slug}_{tab slug}
     * @return      array
     */
    public function _replyToModifyOptionsUpdateStatus( $aStatus ) {
        
        if ( ! $this->hasFieldError() ) {
            return $aStatus;
        }
        return array( 
                'field_errors' => true 
            ) 
            + $this->oUtil->getAsArray( $aStatus );
        
    }
    
    /**
     * Called when the form object tries to set the form data from the database.
     * 
     * @callback    form        `saved_data`    
     * @remark      The `oOptions` property will be automatically set with the overload method.
     * @remark      Redefine (override) this method completely because the parent meta box factory class
     * has this method and its own way to retrieve form data.
     * @return      array       The saved form data.
     * @since       3.7.0
     */
    public function _replyToGetSavedFormData() {
        $_aPageOptions = $this->oUtil->addAndApplyFilter(
            $this, // the caller factory object
            'options_' . $this->oProp->sClassName,
            $this->oProp->oAdminPage->oProp->aOptions  
        );
        return $this->oUtil->castArrayContents( 
            $this->oForm->getDataStructureFromAddedFieldsets(),   // model
            $_aPageOptions        // data source
        );        
    }    
    
       /**
         * Extracts meta box form fields options array from the given options array of an admin page.
         * 
         * @since       3.5.6
         * @return      array       The extracted options array.
         * @internal
         * @deprecated  3.7.0
         */
        private function _getPageMetaBoxOptionsFromPageOptions( array $aPageOptions, array $aFieldsets ) {
     
            $_aOptions = array();
            foreach( $aFieldsets as $_sSectionID => $_aFieldsets ) {
                if ( '_default' === $_sSectionID  ) {
                    foreach( $_aFieldsets as $_aField ) {
                        if ( array_key_exists( $_aField[ 'field_id' ], $aPageOptions ) ) {
                            $_aOptions[ $_aField[ 'field_id' ] ] = $aPageOptions[ $_aField[ 'field_id' ] ];
                        }
                    }
                }
                if ( array_key_exists( $_sSectionID, $aPageOptions ) ) {
                    $_aOptions[ $_sSectionID ] = $aPageOptions[ $_sSectionID ];
                }
            }       
            return $_aOptions;
        
        }
            
}