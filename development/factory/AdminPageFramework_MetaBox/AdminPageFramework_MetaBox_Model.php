<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Handles retrieving data from the database and the submitted $_POST array.
 *
 * @abstract
 * @since           3.3.0
 * @package         AdminPageFramework
 * @subpackage      MetaBox
 */
abstract class AdminPageFramework_MetaBox_Model extends AdminPageFramework_MetaBox_Router {
    
    /**
     * Indicates whether the submitted data is for a new post.
     * 
     * @since       3.3.0
     */
    private $_bIsNewPost = false;    

    /**
     * Sets up validation hooks.
     * 
     * @since       3.3.0
     */
    protected function _setUpValidationHooks( $oScreen ) {

        if ( 'attachment' === $oScreen->post_type && in_array( 'attachment', $this->oProp->aPostTypes ) ) {
            add_filter( 'wp_insert_attachment_data', array( $this, '_replyToFilterSavingData' ), 10, 2 );
        } else {
            add_filter( 'wp_insert_post_data', array( $this, '_replyToFilterSavingData' ), 10, 2 );
        }
    
    }
     
    
    /**
     * Adds the defined meta box.
     * 
     * @since       2.0.0
     * @internal
     * @remark      uses `add_meta_box()`.
     * @remark      A callback for the `add_meta_boxes` hook.
     * @return      void
     */ 
    public function _replyToAddMetaBox() {

        foreach( $this->oProp->aPostTypes as $sPostType ) {
            add_meta_box( 
                $this->oProp->sMetaBoxID,                       // id
                $this->oProp->sTitle,                           // title
                array( $this, '_replyToPrintMetaBoxContents' ), // callback
                $sPostType,                                     // post type
                $this->oProp->sContext,                         // context
                $this->oProp->sPriority,                        // priority
                null                                            // argument - deprecated $this->oForm->aFields
            );
        }
            
    }     
    
    /**
     * Registers form fields and sections.
     * 
     * @since       3.0.0
     * @since       3.3.0       Changed the name from `_replyToRegisterFormElements()`. Changed the scope to `protected`.
     * @internal
     */
    protected function _registerFormElements( $oScreen ) {
                
        // Schedule to add head tag elements and help pane contents. 
        if ( ! $this->oUtil->isPostDefinitionPage( $this->oProp->aPostTypes ) ) { return; }
    
        $this->_loadDefaultFieldTypeDefinitions();  // defined in the factory class.
    
        // Format the fields array.
        $this->oForm->format();
        $this->oForm->applyConditions(); // will set $this->oForm->aConditionedFields
        $this->oForm->applyFiltersToFields( $this, $this->oProp->sClassName );
        
        // Set the option array - the framework will refer to this data when displaying the fields.
        $this->_setOptionArray( 
            $this->_getPostID(),
            $this->oForm->aConditionedFields 
        ); 
        
        // Add the repeatable section elements to the fields definition array.
        $this->oForm->setDynamicElements( $this->oProp->aOptions ); // will update $this->oForm->aConditionedFields
        
        $this->_registerFields( $this->oForm->aConditionedFields );
                
    }    
        
        /**
         * Returns the post ID associated with the loading page.
         * @since   3.4.1
         */
        private function _getPostID()  {
            
            // for an editing post page.
            if ( isset( $GLOBALS['post']->ID ) ) {
                return $GLOBALS['post']->ID;
            }
            if ( isset( $_GET['post'] ) ) {
                return $_GET['post'];
            }
            // for post.php without any query key-values.
            if ( isset( $_POST['post_ID'] ) ) {
                return $_POST['post_ID'];
            }
            return null;
            
        }

    /**
     * Extracts the user submitted values from the $_POST array.
     * 
     * @since       3.0.0
     * @internal
     */
    protected function _getInputArray( array $aFieldDefinitionArrays, array $aSectionDefinitionArrays ) {
        
        // Construct an array consisting of the submitted registered field values.
        $_aInput = array();
        foreach( $aFieldDefinitionArrays as $_sSectionID => $_aSubSectionsOrFields ) {
            
            // If a section is not set,
            if ( '_default' == $_sSectionID ) {
                $_aFields = $_aSubSectionsOrFields;
                foreach( $_aFields as $_aField ) {
                    $_aInput[ $_aField['field_id'] ] = isset( $_POST[ $_aField['field_id'] ] ) 
                        ? $_POST[ $_aField['field_id'] ] 
                        : null;
                }
                continue;
            }     

            // At this point, the section is set
            $_aInput[ $_sSectionID ] = isset( $_aInput[ $_sSectionID ] ) ? $_aInput[ $_sSectionID ] : array();
            
            // If the section does not contain sub sections,
            if ( ! count( $this->oUtil->getIntegerElements( $_aSubSectionsOrFields ) ) ) {
                
                $_aFields = $_aSubSectionsOrFields;
                foreach( $_aFields as $_aField ) {
                    $_aInput[ $_sSectionID ][ $_aField['field_id'] ] = isset( $_POST[ $_sSectionID ][ $_aField['field_id'] ] )
                        ? $_POST[ $_sSectionID ][ $_aField['field_id'] ]
                        : null;
                }     
                continue;

            }
                
            // Otherwise, it's sub-sections. 
            // Since the registered fields don't have information how many items the user added, parse the submitted data.
            foreach( $_POST[ $_sSectionID ] as $_iIndex => $_aFields ) { // will include the main section as well.
                $_aInput[ $_sSectionID ][ $_iIndex ] = isset( $_POST[ $_sSectionID ][ $_iIndex ] ) 
                    ? $_POST[ $_sSectionID ][ $_iIndex ]
                    : null;
            }
                            
        }
    
        return $_aInput;
        
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
    
    /**
     * Sets the aOptions property array in the property object. 
     * 
     * This array will be referred later in the getFieldOutput() method.
     * 
     * @since       unknown
     * @since       3.0.0     the scope is changed to protected as the taxonomy field class redefines it.
     * @internal    
     * @todo        Add the `options_{instantiated class name}` filter.
     */
    protected function _setOptionArray( $iPostID, $aFields ) {
        
        if ( ! is_array( $aFields ) ) { 
            return; 
        }        
        if ( ! is_numeric( $iPostID ) || ! is_int( $iPostID + 0 ) ) { 
            return; 
        }
        
        $this->oProp->aOptions = is_array( $this->oProp->aOptions ) ? $this->oProp->aOptions : array();
        foreach( $aFields as $_sSectionID => $_aFields ) {
            
            if ( '_default' == $_sSectionID  ) {
                foreach( $_aFields as $_aField ) {
                    $this->oProp->aOptions[ $_aField['field_id'] ] = get_post_meta( $iPostID, $_aField['field_id'], true );    
                }
            }
            $this->oProp->aOptions[ $_sSectionID ] = get_post_meta( $iPostID, $_sSectionID, true );
            
        }
        
        // Apply the filter to let third party scripts to set own options array.
        $this->oProp->aOptions = AdminPageFramework_WPUtility::addAndApplyFilter( // Parameters: $oCallerObject, $sFilter, $vInput, $vArgs...
            $this, // the caller object
            'options_' . $this->oProp->sClassName, // options_{instantiated class name}
            $this->oProp->aOptions
        );
        
        $_aLastInput = isset( $_GET['field_errors'] ) && $_GET['field_errors'] ? $this->oProp->aLastInput : array();
        $this->oProp->aOptions = empty( $this->oProp->aOptions ) ? array() : AdminPageFramework_WPUtility::getAsArray( $this->oProp->aOptions );
        $this->oProp->aOptions = $_aLastInput + $this->oProp->aOptions;

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
     * The submitted data for a new post gets passed. 
     * 
     * The filter is either 'wp_insert_attachment_data' or 'wp_insert_post_data' and is triggered when a post has not been created so no post id is assigned.
     * 
     * @since       3.3.0
	 *
	 * @param       array       $aPostData      An array of slashed post data.
     * @param       array       $aUnmodified    An array of sanitized, but otherwise unmodified post data.
     */
    public function _replyToFilterSavingData( $aPostData, $aUnmodified ) {

        // Perform initial checks.
        if ( 'auto-draft' === $aUnmodified['post_status'] ) { return $aPostData; }
        if ( ! $this->_validateCall() ) { return $aPostData; }
        if ( ! in_array( $aUnmodified['post_type'], $this->oProp->aPostTypes ) ) {
            return $aPostData;
        }  
        
        // Determine the post ID.
        $_iPostID = $aUnmodified['ID'];
        if ( ! current_user_can( $this->oProp->sCapability, $_iPostID ) ) {
            return $aPostData;
        }
        
        // Retrieve the submitted data. 
        $_aInput        = $this->_getInputArray( $this->oForm->aConditionedFields, $this->oForm->aConditionedSections );
        $_aInputRaw     = $_aInput; // store one for the last input array.
        
        // Prepare the saved data. For a new post, the id is set to 0.
        $_aSavedMeta    = $_iPostID 
            ? $this->oUtil->getSavedMetaArray( $_iPostID, array_keys( $_aInput ) )
            : array();
        
        // Apply filters to the array of the submitted values.
        $_aInput = $this->oUtil->addAndApplyFilters( 
            $this, 
            "validation_{$this->oProp->sClassName}",
            $this->validate( $_aInput, $_aSavedMeta, $this ),
            $_aSavedMeta, 
            $this 
        ); 
 
        // If there are validation errors. Change the post status to 'pending'.
        if ( $this->hasFieldError() ) {
            $this->_setLastInput( $_aInputRaw );
            $aPostData['post_status'] = 'pending';
            add_filter( 'redirect_post_location', array( $this, '_replyToModifyRedirectPostLocation' ) );
        }
                    
        $this->_updatePostMeta( 
            $_iPostID, 
            $_aInput, 
            $this->oForm->dropRepeatableElements( $_aSavedMeta ) // Drop repeatable section elements from the saved meta array.
        );        
        
        return $aPostData;
        
    }

        /**
         * Modifies the 'message' query value in the redirect url of the post publish.
         * 
         * This method is called when a publishing post contains a field error of meta boxes added by the framework.
         * And the query url gets modified to disable the WordPress default admin notice, "Post published.".
         * 
         * @since       3.3.0
         * @return      string      The modified url to be redirected after publishing the post.
         */
        public function _replyToModifyRedirectPostLocation( $sLocation ) {

            remove_filter( 'redirect_post_location', array( $this, __FUNCTION__ ) );
            return add_query_arg( array( 'message' => 'apf_field_error', 'field_errors' => true ), $sLocation );
            
        }    
        

        /**
         * Saves the post with the given data and the post ID.
         * 
         * @since       3.0.4
         * @internal
         * @return      void
         */
        private function _updatePostMeta( $iPostID, array $aInput, array $aSavedMeta ) {
            
            if ( ! $iPostID ) {
                return;
            }
            
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
         * Checks whether the function call of processing submitted field values is valid or not.
         * 
         * @since       3.3.0
         */
        private function _validateCall() {
            
            // Bail if we're doing an auto save
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { 
                return false;
            }
     
            // If our nonce isn't there, or we can't verify it, bail
            if ( ! isset( $_POST[ $this->oProp->sMetaBoxID ] ) || ! wp_verify_nonce( $_POST[ $this->oProp->sMetaBoxID ], $this->oProp->sMetaBoxID ) ) { 
                return false;
            }
            
            return true;
            
        }            
    
}