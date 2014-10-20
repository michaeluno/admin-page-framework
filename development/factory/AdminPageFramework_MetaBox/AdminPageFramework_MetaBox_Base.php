<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_MetaBox_Base' ) ) :
/**
 * The base class of meta box classes.
 * 
 * @abstract
 * @since       2.0.0
 * @package     AdminPageFramework
 * @extends     AdminPageFramework_Factory
 * @subpackage  MetaBox
 * @internal
 */
abstract class AdminPageFramework_MetaBox_Base extends AdminPageFramework_Factory {
    
    /**
     * Defines the fields type.
     * 
     * @since       3.0.0
     * @internal
     */
    static protected $_sFieldsType;
    
    /**
     * Stores the target section tab slug for the addSettingSection() method.
     * @internal
     */
    protected $_sTargetSectionTabSlug;    
    
    /**
     * Constructs the class object instance of AdminPageFramework_MetaBox.
     * 
     * Mainly sets up properties and hooks.
     * 
     * @see         http://codex.wordpress.org/Function_Reference/add_meta_box#Parameters
     * @since       2.0.0
     * @param       string          $sMetaBoxID             The meta box ID. [3.3.0+] If an empty value is passed, the ID will be automatically generated and the lower-cased class name will be used.
     * @param       string          $sTitle                 The meta box title.
     * @param       string|array    $asPostTypeOrScreenID   (optional) The post type(s) or screen ID that the meta box is associated with.
     * @param       string          $sContext               (optional) The part of the page where the edit screen section should be shown ('normal', 'advanced', or 'side') Default: `normal`.
     * @param       string          $sPriority              (optional) The priority within the context where the boxes should show ('high', 'core', 'default' or 'low') Default: `default`.
     * @param       string          $sCapability            (optional) The <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> to the meta box. Default: `edit_posts`.
     * @param       string          $sTextDomain            (optional) The text domain applied to the displayed text messages. Default: `admin-page-framework`.
     * @return      void
     */ 
    function __construct( $sMetaBoxID, $sTitle, $asPostTypeOrScreenID=array( 'post' ), $sContext='normal', $sPriority='default', $sCapability='edit_posts', $sTextDomain='admin-page-framework' ) {
        
        if ( empty( $asPostTypeOrScreenID ) ) { return; }
                
        // Properties
        $_sClassName = get_class( $this );
        parent::__construct( 
            isset( $this->oProp )? $this->oProp : new AdminPageFramework_Property_MetaBox( $this, $_sClassName, $sCapability )
        );
        
        $this->oProp->sMetaBoxID    = $sMetaBoxID ? $this->oUtil->sanitizeSlug( $sMetaBoxID ) : strtolower( $_sClassName );
        $this->oProp->sTitle        = $sTitle;
        $this->oProp->sContext      = $sContext;    // 'normal', 'advanced', or 'side' 
        $this->oProp->sPriority     = $sPriority;   // 'high', 'core', 'default' or 'low'    

        if ( $this->oProp->bIsAdmin ) {
            add_action( 'current_screen', array( $this, '_replyToDetermineToLoad' ) );    
        }    

    }

    /*
     * Internal methods that should be extended.
     */
    public function _replyToAddMetaBox() {}
    public function _replyToRegisterFormElements( $oScreen ) {}

    /**
     * Determines whether the meta box class components should be loaded in the currently loading page.
     * @since       3.1.3    
     * @internal
     */
    protected  function _isInstantiatable() {
        
        // Disable in admin-ajax.php
        if ( isset( $GLOBALS['pagenow'] ) && 'admin-ajax.php' === $GLOBALS['pagenow'] ) {
            return false;
        }
        return true;
        
    }
    /**
     * Determines whether the meta box should be loaded in the currently loading page.
     * 
     * @since       3.0.3
     * @since       3.1.5      Changed the hook to 'current_screen' from 'wp_loaded'.
     * @internal    
     */
    public function _replyToDetermineToLoad( $oScreen ) {
 
        if ( ! $this->_isInThePage() ) { 
            return; 
        }
        
        $this->_setUp();
        $this->oUtil->addAndDoAction( $this, "set_up_{$this->oProp->sClassName}", $this );
        $this->oProp->_bSetupLoaded = true;
        
        // The screen object should be established to detect the loaded page. 
        // @since   3.1.5   No longer called as a callback.
        $this->_replyToRegisterFormElements( $oScreen ); 

        add_action( 'add_meta_boxes', array( $this, '_replyToAddMetaBox' ) );
        add_action( 'save_post', array( $this, '_replyToSaveMetaBoxFields' ) );
          
    }    
            
    /**
     * Echoes the meta box contents.
     * 
     * @since       2.0.0
     * @remark      A callback for the `add_meta_box()` method.
     * @param       object      $oPost      The object of the post associated with the meta box.
     * @param       array       $vArgs      The array of arguments.
     * @return      void
     * @internal    
     */ 
    public function _replyToPrintMetaBoxContents( $oPost, $vArgs ) {    

        // Use nonce for verification
        $_aOutput   = array();
        $_aOutput[] = wp_nonce_field( $this->oProp->sMetaBoxID, $this->oProp->sMetaBoxID, true, false );
        
        // Condition the sections and fields definition arrays.
        $this->oForm->applyConditions(); // will set $this->oForm->aConditionedFields internally
        $this->oForm->applyFiltersToFields( $this, $this->oProp->sClassName );
        
        // Set the option array - the framework will refer to this data when displaying the fields.
        if ( isset( $this->oProp->aOptions ) ) {
            $this->_setOptionArray( 
                isset( $oPost->ID ) ? $oPost->ID : ( isset( $_GET['page'] ) ? $_GET['page'] : null ), 
                $this->oForm->aConditionedFields 
            ); // will set $this->oProp->aOptions
        }
        
        // Add the repeatable section elements to the fields definition array.
        $this->oForm->setDynamicElements( $this->oProp->aOptions ); // will update $this->oForm->aConditionedFields
                            
        // Get the fields output.
        $_oFieldsTable  = new AdminPageFramework_FormTable( $this->oProp->aFieldTypeDefinitions, $this->_getFieldErrors(), $this->oMsg );
        $_aOutput[]     = $_oFieldsTable->getFormTables( $this->oForm->aConditionedSections, $this->oForm->aConditionedFields, array( $this, '_replyToGetSectionHeaderOutput' ), array( $this, '_replyToGetFieldOutput' ) );

        /* Do action */
        $this->oUtil->addAndDoActions( $this, 'do_' . $this->oProp->sClassName, $this );
        
        /* Render the filtered output */
        echo $this->oUtil->addAndApplyFilters( $this, 'content_' . $this->oProp->sClassName, implode( PHP_EOL, $_aOutput ) );

    }
    
    /**
     * Sets the aOptions property array in the property object. 
     * 
     * This array will be referred later in the getFieldOutput() method.
     * 
     * @since       unknown
     * @since       3.0.0     the scope is changed to protected as the taxonomy field class redefines it.
     * @internal    
     */
    protected function _setOptionArray( $isPostIDOrPageSlug, $aFields ) {
        
        if ( ! is_array( $aFields ) ) { return; }
        
        // For post meta box, the $isPostIDOrPageSlug will be an integer representing the post ID.
        if ( is_numeric( $isPostIDOrPageSlug ) && is_int( $isPostIDOrPageSlug + 0 ) ) :
            
            $_iPostID = $isPostIDOrPageSlug;
            foreach( $aFields as $_sSectionID => $_aFields ) {
                
                if ( '_default' == $_sSectionID  ) {
                    foreach( $_aFields as $_aField ) {
                        $this->oProp->aOptions[ $_aField['field_id'] ] = get_post_meta( $_iPostID, $_aField['field_id'], true );    
                    }
                }
                $this->oProp->aOptions[ $_sSectionID ] = get_post_meta( $_iPostID, $_sSectionID, true );
                
            }
                            
        endif;
        
        // For page meta boxes, do nothing as the class will retrieve the option array by itself.
        
    }
    
    /**
     * Returns the filtered section description output.
     * 
     * @internal
     * @since       3.0.0
     */
    public function _replyToGetSectionHeaderOutput( $sSectionDescription, $aSection ) {
            
        return $this->oUtil->addAndApplyFilters(
            $this,
            array( 'section_head_' . $this->oProp->sClassName . '_' . $aSection['section_id'] ), // section_ + {extended class name} + _ {section id}
            $sSectionDescription
        );     
        
    }

    /**
     * Saves the meta box field data to the associated post. 
     * 
     * @since       2.0.0
     * @remark      A callback for the <em>save_post</em> hook
     * @internal
     */
    public function _replyToSaveMetaBoxFields( $iPostID ) {
        
        // Bail if we're doing an auto save
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
 
        // If our nonce isn't there, or we can't verify it, bail
        if ( ! isset( $_POST[ $this->oProp->sMetaBoxID ] ) || ! wp_verify_nonce( $_POST[ $this->oProp->sMetaBoxID ], $this->oProp->sMetaBoxID ) ) { return; }

        // Check permissions
        if ( ! $iPostID ) { return; }
        if ( in_array( $_POST['post_type'], $this->oProp->aPostTypes ) && ( ! current_user_can( $this->oProp->sCapability, $iPostID ) ) ) { return; }

        // Retrieve the submitted data.
        $_aInput        = $this->_getInputArray( $this->oForm->aFields, $this->oForm->aSections );
    
        // Prepare the saved data.
        $_aSavedMeta    = $this->oUtil->getSavedMetaArray( $iPostID, array_keys( $_aInput ) );
                    
        // Apply filters to the array of the submitted values.
        $_aInput        = $this->oUtil->addAndApplyFilters( $this, "validation_{$this->oProp->sClassName}", $_aInput, $_aSavedMeta, $this );

        // If there are validation errors.
        if ( $this->hasFieldError() ) {
            
            // unhook this function to prevent indefinite loops.
            remove_action( 'save_post', array( $this, '_replyToSaveMetaBoxFields' ) );

            // Revert the post status.
            // @todo: this does not do the job if it is a new post. (somehow it gets published)
            // $_oPost = get_post( $iPostID );    
            // $_sPreviousPostStatus = is_object( $_oPost ) && isset( $_oPost->post_status ) ? $_oPost->post_status : 'draft';
            $_sPreviousPostStatus = get_post_status( $iPostID );
            wp_update_post( array( 'ID' => $iPostID, 'post_status' => $_sPreviousPostStatus ) );

            // re-hook this function again
            add_action( 'save_post', array( $this, '_replyToSaveMetaBoxFields' ) );
            
            return;
            
        }
        
        $this->_updatePostMeta( 
            $iPostID, 
            $_aInput, 
            $this->oForm->dropRepeatableElements( $_aSavedMeta ) // Drop repeatable section elements from the saved meta array.
        );
                
    }    
        /**
         * Saves the post with the given data and the post ID.
         * 
         * @since       3.0.4
         * @internal
         * @return      void
         */
        private function _updatePostMeta( $iPostID, array $aInput, array $aSavedMeta ) {
            
            // Loop through sections/fields and save the data.
            foreach ( $aInput as $_sSectionOrFieldID => $_vValue ) {
                
                if ( is_null( $_vValue ) ) { continue; }
                
                $_vSavedValue = isset( $aSavedMeta[ $_sSectionOrFieldID ] ) ? $aSavedMeta[ $_sSectionOrFieldID ] : null;
                
                // PHP can compare even array contents with the == operator. See http://www.php.net/manual/en/language.operators.array.php
                if ( $_vValue == $_vSavedValue ) { continue; } // if the input value and the saved meta value are the same, no need to update it.
            
                update_post_meta( $iPostID, $_sSectionOrFieldID, $_vValue );
                
            }     
            
        }
            
        /**
         * Retrieves the user submitted values.
         * 
         * @since       3.0.0
         * @internal
         */
        protected function _getInputArray( array $aFieldDefinitionArrays, array $aSectionDefinitionArrays ) {
            
            // Compose an array consisting of the submitted registered field values.
            $aInput = array();
            foreach( $aFieldDefinitionArrays as $_sSectionID => $_aSubSectionsOrFields ) {
                
                // If a section is not set,
                if ( '_default' == $_sSectionID ) {
                    $_aFields = $_aSubSectionsOrFields;
                    foreach( $_aFields as $_aField ) {
                        $aInput[ $_aField['field_id'] ] = isset( $_POST[ $_aField['field_id'] ] ) 
                            ? $_POST[ $_aField['field_id'] ] 
                            : null;
                    }
                    continue;
                }     
    
                // At this point, the section is set
                $aInput[ $_sSectionID ] = isset( $aInput[ $_sSectionID ] ) ? $aInput[ $_sSectionID ] : array();
                
                // If the section does not contain sub sections,
                if ( ! count( $this->oUtil->getIntegerElements( $_aSubSectionsOrFields ) ) ) {
                    
                    $_aFields = $_aSubSectionsOrFields;
                    foreach( $_aFields as $_aField ) {
                        $aInput[ $_sSectionID ][ $_aField['field_id'] ] = isset( $_POST[ $_sSectionID ][ $_aField['field_id'] ] )
                            ? $_POST[ $_sSectionID ][ $_aField['field_id'] ]
                            : null;
                    }     
                    continue;

                }
                    
                // Otherwise, it's sub-sections. 
                // Since the registered fields don't have information how many items the user added, parse the submitted data.
                foreach( $_POST[ $_sSectionID ] as $_iIndex => $_aFields ) { // will include the main section as well.
                    $aInput[ $_sSectionID ][ $_iIndex ] = isset( $_POST[ $_sSectionID ][ $_iIndex ] ) 
                        ? $_POST[ $_sSectionID ][ $_iIndex ]
                        : null;
                }
                                
            }
            
            return $aInput;
            
        }
        
        /**
         * Retrieves the saved meta data as an array.
         * 
         * @since       3.0.0
         * @internal
         * @deprecated
         */
        protected function _getSavedMetaArray( $iPostID, $aInputStructure ) {
            $_aSavedMeta = array();
            foreach ( $aInputStructure as $_sSectionORFieldID => $_v ) {
                $_aSavedMeta[ $_sSectionORFieldID ] = get_post_meta( $iPostID, $_sSectionORFieldID, true );
            }
            return $_aSavedMeta;
        }

}
endif;