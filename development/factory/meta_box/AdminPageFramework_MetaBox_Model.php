<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed MIT
 *
 */

/**
 * Handles retrieving data from the database and the submitted $_POST array.
 *
 * @abstract
 * @since           3.3.0
 * @package         AdminPageFramework/Factory/MetaBox
 * @internal
 */
abstract class AdminPageFramework_MetaBox_Model extends AdminPageFramework_MetaBox_Router {

    /**
     * Sets up hooks.
     * @since       3.7.9
     */
    public function __construct( $sMetaBoxID, $sTitle, $asPostTypeOrScreenID=array( 'post' ), $sContext='normal', $sPriority='default', $sCapability='edit_posts', $sTextDomain='admin-page-framework' ) {

        // This is important to set the hooks before the parent constructor
        // as the setUp wil be called in there if the default action hook (current_screen) is already triggered.
        add_action( 'set_up_' . $this->oProp->sClassName, array( $this, '_replyToSetUpHooks' ) );
        add_action( 'set_up_' . $this->oProp->sClassName, array( $this, '_replyToSetUpValidationHooks' ) );

        parent::__construct(
            $sMetaBoxID,
            $sTitle,
            $asPostTypeOrScreenID,
            $sContext,
            $sPriority,
            $sCapability,
            $sTextDomain
        );

    }

    /**
     * Sets up hooks after calling the `setUp()` method.
     *
     * @since       3.7.9
     * @callback    action      set_up_{instantiated class name}
     * @internal
     */
    public function _replyToSetUpHooks( $oFactory ) {
        $this->oUtil->registerAction(
            'add_meta_boxes',
            array( $this, '_replyToRegisterMetaBoxes' )
        );
    }

    /**
     * Sets up validation hooks.
     *
     * @since       3.3.0
     * @since       3.7.9       Renamed from `_setUpValidationHooks`. Changed the scope to public from protected.
     * @callback    action      set_up_{instantiated class name}
     * @internal
     */
    public function _replyToSetUpValidationHooks( $oScreen ) {

        if ( 'attachment' === $oScreen->post_type && in_array( 'attachment', $this->oProp->aPostTypes ) ) {
            add_filter(
                'wp_insert_attachment_data',
                array( $this, '_replyToFilterSavingData' ),
                10,
                2
            );
        } else {
            add_filter(
                'wp_insert_post_data',
                array( $this, '_replyToFilterSavingData' ),
                10,
                2
            );
        }

    }

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
     * Adds the defined meta box.
     *
     * @since       2.0.0
     * @since       3.7.10      Changed the name from `_replyToAddMetaBox()`.
     * @internal
     * @uses        add_meta_box()
     * @return      void
     * @callback    add_meta_boxes
     */
    public function _replyToRegisterMetaBoxes() {
        foreach( $this->oProp->aPostTypes as $_sPostType ) {
            add_meta_box(
                $this->oProp->sMetaBoxID,                       // id
                $this->oProp->sTitle,                           // title
                array( $this, '_replyToPrintMetaBoxContents' ), // callback
                $_sPostType,                                    // post type
                $this->oProp->sContext,                         // context
                $this->oProp->sPriority,                        // priority
                null                                            // argument - deprecated $this->oForm->aFields
            );
        }
    }

    /**
     * Called when the form object tries to set the form data from the database.
     *
     * @callback    form        `saved_data`
     * @remark      The `oOptions` property will be automatically set with the overload method.
     * @return      array       The saved form data.
     * @since       3.7.0
     */
    public function _replyToGetSavedFormData() {

        $_oMetaData = new AdminPageFramework_MetaBox_Model___PostMeta(
            $this->_getPostID(),
            $this->oForm->aFieldsets
        );
        $this->oProp->aOptions = $_oMetaData->get();

        // The parent method will handle applying filters with the set property object.
        return parent::_replyToGetSavedFormData();

    }
        /**
         * Returns the post ID associated with the loading page.
         * @since       3.4.1
         * @internal
         * @return      integer     The found post ID. `0` if not found.
         */
        private function _getPostID()  {

            // for an editing post page.
            if ( isset( $GLOBALS[ 'post' ]->ID ) ) {
                return $GLOBALS[ 'post' ]->ID;
            }
            if ( isset( $_GET[ 'post' ] ) ) {
                return $_GET[ 'post' ];
            }
            // for post.php without any query key-values.
            if ( isset( $_POST[ 'post_ID' ] ) ) {
                return $_POST[ 'post_ID' ];
            }
            return 0;

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
        if ( ! $this->_shouldProceedValidation( $aUnmodified ) ) {
            return $aPostData;
        }

        // Retrieve the submitted data.
        $_aInputs       = $this->oForm->getSubmittedData(
            $_POST,     // subject data to be parsed
            true,       // extract data with the fieldset structure
            false       // strip slashes
        );
        $_aInputsRaw    = $_aInputs; // store one for the last input array.

        // Prepare the saved data. For a new post, the id is set to 0.
        $_iPostID       = $aUnmodified[ 'ID' ];
        $_aSavedMeta    = $this->oUtil->getSavedPostMetaArray(
            $_iPostID,
            array_keys( $_aInputs )
        );

        // Apply filters to the array of the submitted values.
        $_aInputs = $this->oUtil->addAndApplyFilters(
            $this,
            "validation_{$this->oProp->sClassName}",
            call_user_func_array(
                array( $this, 'validate' ), // triggers __call()
                array( $_aInputs, $_aSavedMeta, $this )
            ), // 3.5.3+
            $_aSavedMeta,
            $this
        );

        // If there are validation errors. Change the post status to 'pending'.
        if ( $this->hasFieldError() ) {
            $this->setLastInputs( $_aInputsRaw );
            $aPostData[ 'post_status' ] = 'pending';
            add_filter(
                'redirect_post_location',
                array( $this, '_replyToModifyRedirectPostLocation' )
            );
        }

        $this->oForm->updateMetaDataByType(
            $_iPostID,   // object id
            $_aInputs,   // user submit form data
            $this->oForm->dropRepeatableElements( $_aSavedMeta ), // Drop repeatable section elements from the saved meta array.
            $this->oForm->sStructureType   // fields type
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

            remove_filter(
                'redirect_post_location',
                array( $this, __FUNCTION__ )
            );
            return add_query_arg(
                array(
                    'message'       => 'apf_field_error',
                    'field_errors'  => true
                ),
                $sLocation
            );

        }

        /**
         * Checks whether the function call of processing submitted field values is valid or not.
         *
         * @since       3.3.0
         * @since       3.6.0       Added the `$aUnmodified` parameter.
         * @since       3.7.0      Renamed from `_validateCall()`.
         * @internal
         * @return      boolean
         */
        private function _shouldProceedValidation( array $aUnmodified ) {

            if ( 'auto-draft' === $aUnmodified[ 'post_status' ] ) {
                return false;
            }

            // Bail if we're doing an auto save
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return false;
            }

            // If our nonce isn't there, or we can't verify it, bail
            if ( ! isset( $_POST[ $this->oProp->sMetaBoxID ] ) ) {
                return false;
            }
            if ( ! wp_verify_nonce( $_POST[ $this->oProp->sMetaBoxID ], $this->oProp->sMetaBoxID ) ) {
                return false;
            }

            if ( ! in_array( $aUnmodified[ 'post_type' ], $this->oProp->aPostTypes ) ) {
                return false;
            }

            return current_user_can( $this->oProp->sCapability, $aUnmodified[ 'ID' ] );

        }

}
