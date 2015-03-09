<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Handles retrieving data from the database and the submitted $_POST array.
 *
 * @abstract
 * @since           3.3.0
 * @package         AdminPageFramework
 * @subpackage      MetaBox
 * @internal
 */
abstract class AdminPageFramework_MetaBox_Model extends AdminPageFramework_MetaBox_Router {
    
    /**
     * Indicates whether the submitted data is for a new post.
     * 
     * @since       3.3.0
     */
    private $_bIsNewPost = false;    

    /**
     * A validation callback method.
     * 
     * The user may just override this method instead of defining a `validation_{...}` callback method.
     * 
     * @since       3.4.1
     * @since       3.5.3       Moved from `AdminPageFramework_Factory_Model`. or not.
     * @remark      Do not even declare this method to avoid PHP strict standard warnings.
     */
    // public function validate( $aInput, $aOldInput, $oFactory ) {
        // return $aInput;
    // }         
    
    /**
     * Sets up validation hooks.
     * 
     * @since       3.3.0
     * @internal
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
     * @uses        add_meta_box()
     * @return      void
     * @callback    action      add_meta_boxes
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
     * @internal
     * @since       3.0.0
     * @since       3.3.0       Changed the name from `_replyToRegisterFormElements()`. Changed the scope to `protected`.
     * @return      void
     */
    protected function _registerFormElements( $oScreen ) {
                
        // Schedule to add head tag elements and help pane contents. 
        if ( ! $this->oUtil->isPostDefinitionPage( $this->oProp->aPostTypes ) ) { 
            return; 
        }
    
        $this->_loadFieldTypeDefinitions();  // defined in the factory class.
    
        // Format the fields array.
        $this->oForm->format();
        $this->oForm->applyConditions(); // will set $this->oForm->aConditionedFields
        $this->oForm->applyFiltersToFields( $this, $this->oProp->sClassName );
        
        // Set the option array - the framework will refer to this data when displaying the fields.
        $this->_setOptionArray( 
            $this->_getPostID(),
            $this->oUtil->getAsArray( $this->oForm->aConditionedFields )
        ); 
        
        // Add the repeatable section elements to the fields definition array.
        $this->oForm->setDynamicElements( $this->oProp->aOptions ); // will update $this->oForm->aConditionedFields
        
        $this->_registerFields( $this->oForm->aConditionedFields );
                
    }    
        
        /**
         * Returns the post ID associated with the loading page.
         * @since   3.4.1
         * @internal
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
     * Retrieves the saved meta data as an array.
     * 
     * @since       3.0.0
     * @internal
     * @uses        get_post_meta()
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
     * @internal    
     * @since       unknown
     * @since       3.0.0       the scope is changed to protected as the taxonomy field class redefines it.
     * @since       3.5.3       Removed a type check at the beginning of the method and added a type hint to the parameter. 
     * This change enables an empty value to be parsed and triggers `options_{class name}` filter hook. Before this change if the option is empty, the hook did not get triggered.
     */
    protected function _setOptionArray( $iPostID, array $aFields ) {
        
        if ( ! $this->oUtil->isNumericInteger( $iPostID ) ) {
            return; 
        }
        
        $this->oProp->aOptions = $this->oUtil->getAsArray( $this->oProp->aOptions );        
        $this->_fillOptionsArrayFromPostMeta( 
            $this->oProp->aOptions, 
            $iPostID, 
            $aFields
        );
          
        // Apply the filter to let third party scripts to set own options array.
        $this->oProp->aOptions = $this->oUtil->addAndApplyFilter( 
            $this, // the caller object
            'options_' . $this->oProp->sClassName, 
            $this->oProp->aOptions
        );
        
        $_aLastInput = isset( $_GET['field_errors'] ) && $_GET['field_errors'] 
            ? $this->oProp->aLastInput 
            : array();
        $this->oProp->aOptions = $_aLastInput + $this->oUtil->getAsArray( $this->oProp->aOptions );

    }
        /**
         * Updates the first parameter of the options array with the post meta data associated with the given post ID.
         * 
         * @since       3.5.3
         * @return      void
         * @uses        get_post_meta()
         * @internal
         */
        private function _fillOptionsArrayFromPostMeta( array &$aOptions, $iPostID, array $aFields ) {
      
            foreach( $aFields as $_sSectionID => $_aFields ) {
                
                if ( '_default' == $_sSectionID  ) {
                    foreach( $_aFields as $_aField ) {
                        $aOptions[ $_aField['field_id'] ] = get_post_meta( 
                            $iPostID, 
                            $_aField['field_id'], 
                            true 
                        );    
                    }
                }
                $aOptions[ $_sSectionID ] = get_post_meta( 
                    $iPostID, 
                    $_sSectionID, 
                    true 
                );
                
            }
      
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
     * The submitted data for a new post being passed. 
     * 
     * Triggered when a post has not been created so no post id is assigned.
     * 
	 * @internal
     * @since       3.3.0
     * @callback    filter      wp_insert_attachment_data
     * @callback    filter      wp_insert_post_data
	 * @param       array       $aPostData      An array of slashed post data.
     * @param       array       $aUnmodified    An array of sanitized, but otherwise unmodified post data.
     */
    public function _replyToFilterSavingData( $aPostData, $aUnmodified ) {

        // Perform initial checks.
        if ( 'auto-draft' === $aUnmodified['post_status'] ) { 
            return $aPostData; 
        }
        if ( ! $this->_validateCall() ) { 
            return $aPostData; 
        }
        if ( ! in_array( $aUnmodified['post_type'], $this->oProp->aPostTypes ) ) {
            return $aPostData;
        }  
        
        // Determine the post ID.
        $_iPostID = $aUnmodified['ID'];
        if ( ! current_user_can( $this->oProp->sCapability, $_iPostID ) ) {
            return $aPostData;
        }
        
        // Retrieve the submitted data. 
        $_aInput        = $this->oForm->getUserSubmitDataFromPOST( $this->oForm->aConditionedFields, $this->oForm->aConditionedSections );
        $_aInputRaw     = $_aInput; // store one for the last input array.
        
        // Prepare the saved data. For a new post, the id is set to 0.
        $_aSavedMeta    = $_iPostID 
            ? $this->oUtil->getSavedMetaArray( $_iPostID, array_keys( $_aInput ) )
            : array();
        
        // Apply filters to the array of the submitted values.
        $_aInput = $this->oUtil->addAndApplyFilters( 
            $this, 
            "validation_{$this->oProp->sClassName}",
            call_user_func_array( 
                array( $this, 'validate' ), // triggers __call()
                array( $_aInput, $_aSavedMeta, $this ) 
            ), // 3.5.3+            
            $_aSavedMeta, 
            $this 
        ); 
 
        // If there are validation errors. Change the post status to 'pending'.
        if ( $this->hasFieldError() ) {
            $this->_setLastInput( $_aInputRaw );
            $aPostData['post_status'] = 'pending';
            add_filter( 'redirect_post_location', array( $this, '_replyToModifyRedirectPostLocation' ) );
        }
                    
        $this->oForm->updateMetaDataByType( 
            $_iPostID,  // object id
            $_aInput,   // user submit form data
            $this->oForm->dropRepeatableElements( $_aSavedMeta ), // Drop repeatable section elements from the saved meta array.
            $this->oForm->sFieldsType   // fields type
        );        
        
        return $aPostData;
        
    }

        /**
         * Modifies the 'message' query value in the redirect url of the post publish.
         * 
         * This method is called when a publishing post contains a field error of meta boxes added by the framework.
         * And the query url gets modified to disable the WordPress default admin notice, "Post published.".
         * 
         * @internal
         * @callback    filter      redirect_post_location
         * @since       3.3.0
         * @return      string      The modified url to be redirected after publishing the post.
         */
        public function _replyToModifyRedirectPostLocation( $sLocation ) {

            remove_filter( 'redirect_post_location', array( $this, __FUNCTION__ ) );
            return add_query_arg( array( 'message' => 'apf_field_error', 'field_errors' => true ), $sLocation );
            
        }        
            
        /**
         * Checks whether the function call of processing submitted field values is valid or not.
         * 
         * @since       3.3.0
         * @internal
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