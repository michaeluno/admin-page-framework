<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Handles form registration.
 *
 * @since           3.6.3
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 */
class AdminPageFramework_Model_FormRegistration extends AdminPageFramework_WPUtility {
    
    /**
     * Stores the factory object.
     */
    public $oFactory;

    /**
     * Sets up properties and hooks.
     * @since       3.6.3
     */
    public function __construct( $oFactory ) {
       
        $this->oFactory         = $oFactory;        
        
        add_action( 
            "load_after_{$this->oFactory->oProp->sClassName}", 
            array( $this, '_replyToRegisterSettings' ), 
            20  // lower priority - this must be called after form validation is done. 20: field registration, 21: validation handling 22: handle redirects
        ); 
        
    }   
    
    /**
     * Registers the setting sections and fields.
     * 
     * This methods passes the stored section and field array contents to the `add_settings_section()` and `add_settings_fields()` functions.
     * Then perform `register_setting()`.
     * 
     * The filters will be applied to the section and field arrays; that means that third-party scripts can modify the arrays.
     * Also they get sorted before being registered based on the set order.
     * 
     * @since       2.0.0
     * @since       2.1.5       Added the ability to define custom field types.
     * @since       3.1.2       Changed the hook from the `admin_menu` to `current_screen` so that the user can add forms in `load_{...}` callback methods.
     * @since       3.1.3       Removed the Settings API related functions entirely.
     * @since       3.3.1       Moved from `AdminPageFramework_Setting_Base`.
     * @since       3.6.3       Moved from `AdminPageFramework_Model`.
     * @remark      This method is not intended to be used by the user.
     * @remark      The callback method for the `load_after_{instantiated class name}` hook.
     * @return      void
     * @internal
     */ 
    public function _replyToRegisterSettings() {

        if ( ! $this->oFactory->_isInThePage() ) { 
            return;
        }

        /* 1. Apply filters to added sections and fields */
        $this->oFactory->oForm->aSections = $this->addAndApplyFilter( 
            $this->oFactory, 
            "sections_{$this->oFactory->oProp->sClassName}", 
            $this->oFactory->oForm->aSections 
        );
        foreach( $this->oFactory->oForm->aFields as $_sSectionID => &$_aFields ) {
            $_aFields = $this->addAndApplyFilter( // Parameters: $oCallerObject, $aFilters, $vInput, $vArgs...
                $this->oFactory,
                "fields_{$this->oFactory->oProp->sClassName}_{$_sSectionID}",
                $_aFields
            ); 
            unset( $_aFields ); // to be safe in PHP especially the same variable name is used in the scope.
        }
        $this->oFactory->oForm->aFields = $this->addAndApplyFilter( 
            // Parameters: $oCallerObject, $aFilters, $vInput, $vArgs...
            $this->oFactory,
            "fields_{$this->oFactory->oProp->sClassName}",
            $this->oFactory->oForm->aFields
        );         
        
        /* 2. Format ( sanitize ) the section and field arrays and apply conditions to the sections and fields and drop unnecessary items. */
        // 2-1. Set required properties for formatting.
        $this->oFactory->oForm->setDefaultPageSlug( $this->oFactory->oProp->sDefaultPageSlug );    
        $this->oFactory->oForm->setOptionKey( $this->oFactory->oProp->sOptionKey );
        $this->oFactory->oForm->setCallerClassName( $this->oFactory->oProp->sClassName );
        
        // 2-2. Do format internally stored sections and fields definition arrays.
        $this->oFactory->oForm->format();

        // 2-3. Now set required properties for conditioning.
        $_sCurrentPageSlug = $this->oFactory->oProp->getCurrentPageSlug();
        $this->oFactory->oForm->setCurrentPageSlug( $_sCurrentPageSlug );
        $this->oFactory->oForm->setCurrentTabSlug( $this->oFactory->oProp->getCurrentTabSlug( $_sCurrentPageSlug ) );

        // 2-4. Do conditioning.
        $this->oFactory->oForm->applyConditions();
        $this->oFactory->oForm->applyFiltersToFields( 
            $this->oFactory, 
            $this->oFactory->oProp->sClassName 
        ); // applies filters to the conditioned field definition arrays.
        $this->oFactory->oForm->setDynamicElements( $this->oFactory->oProp->aOptions ); // will update $this->oFactory->oForm->aConditionedFields
        
        /* 3. Define field types. This class adds filters for the field type definitions so that framework's built-in field types will be added. */
        $this->oFactory->loadFieldTypeDefinitions();

        /* 4. Set up the contextual help pane for sections. */ 
        foreach( $this->oFactory->oForm->aConditionedSections as $_aSection ) {
                                    
            if ( empty( $_aSection[ 'help' ] ) ) {
                continue;
            }
            
            $this->oFactory->addHelpTab( 
                array(
                    'page_slug'                 => $_aSection[ 'page_slug' ],
                    'page_tab_slug'             => $_aSection[ 'tab_slug' ],
                    'help_tab_title'            => $_aSection[ 'title' ],
                    'help_tab_id'               => $_aSection[ 'section_id' ],
                    'help_tab_content'          => $_aSection[ 'help' ],
                    'help_tab_sidebar_content'  => $_aSection[ 'help_aside' ] 
                        ? $_aSection[ 'help_aside' ] 
                        : "",
                )
            );
                
        }

        /* 5. Register fields - set head tag and help pane elements */
        $this->oFactory->registerFields( $this->oFactory->oForm->aConditionedFields );

        /* 6. Enable the form - Set the form enabling flag so that the <form></form> tag will be inserted in the page. */
        $this->oFactory->oProp->bEnableForm = true;    
        
        /* 7. Handle submitted data. */
        $this->oFactory->_handleSubmittedData();    
        
    }
    
}