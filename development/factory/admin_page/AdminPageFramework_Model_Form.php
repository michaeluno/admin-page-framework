<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Interact with the database for the forms.
 *
 * @abstract
 * @since           3.3.1
 * @since           3.6.3       Chagned the name from `AdminPageFramework_Form_Model`.
 * @extends         AdminPageFramework_Router
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 */
abstract class AdminPageFramework_Model_Form extends AdminPageFramework_Router {
    
    /**
     * Stores the settings field errors. 
     * 
     * @remark      Do not set a default value here since it is checked whether it is null or not later.
     * @since       2.0.0
     * @since       3.6.3       Changed the visibility scope to public as a delegation class needs to access this property.
     * @var         array       Stores field errors.
     * @internal
     */ 
    public $aFieldErrors; 
    
    /**
     * Defines the class object structure type.
     * 
     * This is used to create a property object as well as to define the form element structure.
     * 
     * @since       3.0.0
     * @since       DEVVER      Changed the name from `$_sStructureType`.
     * @internal
     */
    static protected $_sStructureType = 'admin_page';
    
    /**
     * Stores the target page slug which will be applied when no page slug is specified for the `addSettingSection()` method.
     * 
     * @since       3.0.0
     */
    protected $_sTargetPageSlug = null;
    
    /**
     * Stores the target tab slug which will be applied when no tab slug is specified for the `addSettingSection()` method.
     * 
     * @since       3.0.0
     */    
    protected $_sTargetTabSlug = null;

    /**
     * Stores the target section tab slug which will be applied when no section tab slug is specified for the `addSettingSection()` method.
     * 
     * @since 3.0.0
     */    
    protected $_sTargetSectionTabSlug = null;
    
    /**
     * Registers necessary hooks and sets up properties.
     * 
     * @internal
     * @since       3.3.0
     * @since       3.3.1       Moved from `AdminPageFramework_Setting_Base`.
     */
    public function __construct( $sOptionKey=null, $sCallerPath=null, $sCapability='manage_options', $sTextDomain='admin-page-framework' ) {

        parent::__construct( $sOptionKey, $sCallerPath, $sCapability, $sTextDomain );
        
        if ( $this->oProp->bIsAdminAjax ) {
            return;
        }
        if ( ! $this->oProp->bIsAdmin ) {
            return;
        }

        new AdminPageFramework_Model__FormEmailHandler( $this );                
        
        // Checking the GET and POST methods.
        if ( isset( $_REQUEST[ 'apf_remote_request_test' ] ) && '_testing' === $_REQUEST[ 'apf_remote_request_test' ] ) {
            exit( 'OK' );
        }
        
    }
    
    /**
     * Validates submitted form data and saves them.
     * 
     * @since       DEVVER
     * @callback    form        handle_form_data
     * @return      void
     */
    public function _replyToHandleSubmittedFormData( $aSavedData, $aArguments, $aSectionsets, $aFieldsets ) {
        new AdminPageFramework_Model__FormSubmission( 
            $this,
            $aSavedData, 
            $aArguments, 
            $aSectionsets, 
            $aFieldsets
        );
    }
    
    /**
     * Called upon fieldset resource registration.
     * 
     * A contextual help pane item associated with this fieldset will be added.
     * 
     * @remark      Overrides the method of the factory class.
     * @since       DEVVER
     * @return      void
     */
    public function _replyToFieldsetReourceRegistration( $aFieldset ) {
        
        $aFieldset = $aFieldset + array(
            'help'          => null,
            'title'         => null,
            'help_aside'    => null,
            'page_slug'     => null,
            'tab_slug'      => null,
            'section_title' => null,
            'section_id'    => null,
        );
        if ( ! $aFieldset[ 'help' ] ) {
            return;
        }
        $this->addHelpTab( 
            array(
                'page_slug'                 => $aFieldset[ 'page_slug' ],
                'page_tab_slug'             => $aFieldset[ 'tab_slug' ],
                'help_tab_title'            => $aFieldset[ 'section_title' ],
                'help_tab_id'               => $aFieldset[ 'section_id' ],
                'help_tab_content'          => "<span class='contextual-help-tab-title'>" 
                        . $aFieldset[ 'title' ] 
                    . "</span> - " . PHP_EOL
                    . $aFieldset[ 'help' ],
                'help_tab_sidebar_content'  => $aFieldset[ 'help_aside' ] 
                    ? $aFieldset[ 'help_aside' ] 
                    : "",
            )
        );
                                  
    }
    
    /**
     * Modifies registered sectionsets definition array.
     * @since       DEVVER
     * @remark      Overrides the method of the factory class.
     * @return      array       The modified sectionsets definition array.
     */    
    public function _replyToModifySectionsets( $aSectionsets ) {

        // Help pane elements must be added before the head tag gets rendered.
        $this->_registerHelpPaneItemsOfFormSections( $aSectionsets );
        
        return parent::_replyToModifySectionsets( $aSectionsets );

    }    
        /**
         * Parse the definition array and add help pane items.
         * 
         * Help pane elements must be added before the head tag gets rendered.
         * @return      void
         * @since       DEVVER
         */
        public function _registerHelpPaneItemsOfFormSections( $aSectionsets ) {            
// @todo Test if help pane item gets displayed        

            foreach( $aSectionsets as $_aSectionset ) {
// @todo check capability and conditions                
                $_aSectionset = $_aSectionset + array(
                    'help'          => null,
                    'page_slug'     => null,
                    'tab_slug'      => null,
                    'title'         => null,
                    'section_id'    => null,
                    'help'          => null,
                    'help_aside'    => null,
                );
                if ( empty( $_aSectionset[ 'help' ] ) ) {
                    continue;
                }
                $this->addHelpTab( 
                    array(
                        'page_slug'                 => $_aSectionset[ 'page_slug' ],
                        'page_tab_slug'             => $_aSectionset[ 'tab_slug' ],
                        'help_tab_title'            => $_aSectionset[ 'title' ],
                        'help_tab_id'               => $_aSectionset[ 'section_id' ],
                        'help_tab_content'          => $_aSectionset[ 'help' ],
                        'help_tab_sidebar_content'  => $this->getElement( $_aSectionset, 'help_aside', '' ),
                    )
                );            
            }
        }
            
    /**
     * Determines whether the passed field should be visible or not.
     * @since       DEVVER
     * @return      boolean
     */
    public function _replyToDetermineSectionsetVisibility( $bVisible, $aSectionset ) {

        if ( ! current_user_can( $aSectionset[ 'capability' ] ) ) {
            return false;
        }
        if ( ! $aSectionset[ 'if' ] ) { 
            return false;
        }
        if ( ! $this->_isSectionOfCurrentPage( $aSectionset ) ) { 
            return false;
        }
        return $bVisible;
        
    }
     /**
         * Checks if the given section belongs to the currently loading tab.
         * 
         * @since       2.0.0
         * @since       3.0.0       Moved from the setting class.
         * @since       DEVVER      Moved from `AdminPageFramework_FormDefinition_Page`.
         * Renamed from `_isSectionOfCurrentPage()`.
         * @remark      Assumes the given section definition array is already formatted.
         * @return      boolean     Returns true if the section belongs to the current tab page. Otherwise, false.
         */     
        private function _isSectionOfCurrentPage( array $aSectionset ) {
        
            // Make sure the value type is string so that when the page_slug is not set, it won't match.
            $_sCurrentPageSlug  = ( string ) $this->oProp->getCurrentPageSlug();
            
            // Make sure if it's in the loading page.
            if ( $aSectionset[ 'page_slug' ] !== $_sCurrentPageSlug  ) { 
                return false; 
            }
                                        
            // If the checking tab slug and the current loading tab slug is the same, it should be registered.
            return  ( $aSectionset[ 'tab_slug' ] === $this->oProp->getCurrentTabSlug( $_sCurrentPageSlug ) );
            
        }        
    
    /**
     * Determines whether the passed field should be visible or not.
     * @since       DEVVER
     * @return      boolean
     */
    public function _replyToDetermineFieldsetVisibility( $bVisible, $aFieldset ) {
        
        $_sCurrentPageSlug  = $this->oProp->getCurrentPageSlug();
        
        // If the specified field does not exist, do nothing.
        if ( $aFieldset[ 'page_slug' ] !== $_sCurrentPageSlug ) { 
            return false; 
        }        
        return parent::_replyToDetermineFieldsetVisibility( $bVisible, $aFieldset );
        
    }
    
    /**
     * @since       DEVVER
     * @return      array
     */
    public function _replyToFormatFieldsetDefinition( $aFieldset, $aSectionsets ) {

        // 3.6.0+ Inherit the capability value from the tab.
        
        if ( empty( $aFieldset ) ) { 
            return $aFieldset; 
        }
        
        $_sSectionID = $aFieldset[ 'section_id' ];
        
        $aFieldset[ 'option_key' ]      = $this->oProp->sOptionKey;
        $aFieldset[ 'class_name' ]      = $this->oProp->sClassName;
        $aFieldset[ 'page_slug' ]       = $this->oUtil->getElement( 
            $aSectionsets, 
            array( $_sSectionID, 'page_slug' ), 
            null 
        );
        $aFieldset[ 'tab_slug' ]        = $this->oUtil->getElement( 
            $aSectionsets, 
            array( $_sSectionID, 'tab_slug' ), 
            null 
        );
        
        // used for the contextual help pane.
        $_aSectionset = $this->oUtil->getElementAsArray(
            $aSectionsets,
            $_sSectionID
        );
        $aFieldset[ 'section_title' ]   = $this->oUtil->getElement( 
            $_aSectionset, 
            'title'
        );
        $aFieldset[ 'capability' ]   = $aFieldset[ 'capability' ]
            ? $aFieldset[ 'capability' ]
            : $this->_replyToGetCapabilityForForm( 
                $this->oUtil->getElement( $_aSectionset, 'capability' ),
                $aSectionset[ 'page_slug' ], 
                $aSectionset[ 'tab_slug' ] 
            );
 
        return parent::_replyToFormatFieldsetDefinition( $aFieldset, $aSectionsets );
    
    }
    
    /**
     * @since       DEVVER
     * @return      array
     */
    public function _replyToFormatSectionsetDefinition( $aSectionset ) {
        
        if ( empty( $aSectionset ) ) {
            return $aSectionset;
        }

        $aSectionset = $aSectionset + array(
            'page_slug'     => null,
            'tab_slug'      => null,
            'capability'    => null,
        );
        
        $aSectionset[ 'page_slug' ] = $aSectionset[ 'page_slug' ]
            ? $aSectionset[ 'page_slug' ]
            : $this->oProp->sDefaultPageSlug;
            
        // 3.6.0+ Inherit the capability value from the page.
        $aSectionset[ 'capability' ] = $this->_replyToGetCapabilityForForm( 
            $aSectionset[ 'capability' ], 
            $aSectionset[ 'page_slug' ], 
            $aSectionset[ 'tab_slug' ] 
        );
       
        return parent::_replyToFormatSectionsetDefinition( $aSectionset );
    }
    
    /**
     * @since       DEVVER
     * @return      boolean     Whether or not the form registration should be allowed in the current screen.
     */
    public function _replyToDetermineWhetherToProcessFormRegistration( $bAllowed ) {
        $_sPageSlug = $this->oProp->getCurrentPageSlug();
        return $this->oProp->isPageAdded( $_sPageSlug );
    }
    /**
     * Returns the inherited capability value from the page and in-page tab for form elements.
     * 
     * @since       3.6.0
     * @since       DEVVER      Moved from `AdminPageFramework_FormDefinition_Page`.
     * @return      string
     */    
    public function _replyToGetCapabilityForForm( $sCapability /*, $sPageSlug, $sTabSlug */ ) {
        
        $_aParameters     = func_get_args() + array( '', '', '' );
        $_sPageSlug       = $this->oUtil->getAOrB( $_aParameters[ 1 ], $_aParameters[ 1 ], $this->oProp->getCurrentPageSlug() );
        $_sTabSlug        = $this->oUtil->getAOrB( $_aParameters[ 2 ], $_aParameters[ 2 ], $this->oProp->getCurrentTabSlug( $_sPageSlug ) );
        
        // Note that the passed capability value to the method is same as the one set to the factory class constructor.
        $_sTabCapability  = $this->_getInPageTabCapability( $_sTabSlug, $_sPageSlug );
        $_sPageCapability = $this->_getPageCapability( $_sPageSlug );
        $_aCapabilities   = array_filter( array( $_sTabCapability, $_sPageCapability ) )
            + array( $this->oProp->sCapability );
        return $_aCapabilities[ 0 ];
        
    }

         
}