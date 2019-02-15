<?php
/**
 * Admin Page Framework Loader
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 */

/**
 * Adds the 'Generator' section to the 'Generator' tab.
 *
 * @since       3.5.4           Moved some methods from `AdminPageFrameworkLoader_AdminPage_Tool_Minifier`.
 * @deprecated  3.5.4           As the component generator was introduced.
 */
class AdminPageFrameworkLoader_AdminPage_Tool_Minifier_Minifier extends AdminPageFrameworkLoader_AdminPage_Section_Base {

    /**
     * A user constructor.
     */
    protected function construct( $oFactory ) {

        add_action(
            "export_{$oFactory->oProp->sClassName}_{$this->sSectionID}_download",
            array( $this, 'replyToDownloadMinifiedVersion' ),
            10,
            4
        );
        add_action(
            'export_name_' . $this->sPageSlug . '_' . $this->sTabSlug,
            array( $this, 'replyToFilterFileName' ), 10, 5
        );

    }

    public function addFields( $oFactory, $sSectionID ) {

        $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array(
                'field_id'          => 'class_prefix',
                'title'             => __( 'Class Prefix', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'description'       => __( 'Set alphanumeric characters for the class names', 'admin-page-framework-loader' )
                    . ' ' .  __( 'For example, if you set here <code>MyPluginName_</code>, you will need to extend the class named <code>MyClassName_AdminPageFramework</code> instead of <code>AdminPageFramework</code>.', 'admin-page-framework-loader' )
                    . ' e.g.<code>MyPluginName_</code>',
                'attributes'        => array(
                    'size'          => 30,
                    // 'required' => 'required',
                    'placeholder'   => __( 'Type a prefix.', 'admin-page-framework-loader' ),
                ),
            ),
            array(
                'field_id'          => 'minified_script_name',
                'title'             => __( 'File Name', 'admin-page-framework-loader' ),
                'type'              => 'text',
                'description'       => __( 'The file name of the minified script.', 'admin-page-framework-loader' )
                    . ' e.g.<code>my-plugin-admin-page-framework.min.php</code>',
                'default'   => 'admin-page-framework.min.php',
                'attributes'        => array(
                    'size'          => 40,
                    // 'required' => 'required',
                    'placeholder'   => __( 'Type a prefix.', 'admin-page-framework-loader' ),
                ),
            ),
            array(
                'field_id'          => 'download',
                'title'             => __( 'Download', 'admin-page-framework-loader' ),
                'type'              => 'export',
                'label_min_width'   => 0,
                'value'             => __( 'Download', 'adimn-page-framework-demo' ),
                'file_name'         => 'admin-page-framework.min.php',  // the default file name. This will be modified by the filter.
                'format'            => 'text',  // 'json', 'text', 'array'
                'description'       => __( 'Download the minified version.', 'admin-page-framework-loader' ),
            )
        );

    }

    /**
     * Validates the submitted form data.
     *
     * @since       3.4.6
     * @since       3.5.4       Moved from `AdminPageFrameworkLoader_AdminPage_Tool_Minifier`.
     */
    public function validate( $aInput, $aOldInput, $oAdminPage, $aSubmitInfo ) {

        $_bVerified = true;
        $_aErrors   = array();

        // Sanitize the file name.
        $aInput[ 'minified_script_name' ] = $oAdminPage->oUtil->sanitizeFileName(
            $aInput[ 'minified_script_name' ],
            '-'
        );

        // the class prefix must not contain white spaces and some other characters not supported in PHP class names.
        $aInput[ 'class_prefix' ] = isset( $aInput[ 'class_prefix' ] )
            ? trim( $aInput[ 'class_prefix' ] )
            : '';
        preg_match( '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $aInput[ 'class_prefix' ], $_aMatches );
        if ( $aInput[ 'class_prefix' ] && empty( $_aMatches ) ) {

            // $variable[ 'sectioni_id' ]['field_id']
            $_aErrors['class_prefix'] = __( 'The prefix must consist of alphanumeric with underscores.', 'admin-page-framework-loader' );
            $_bVerified = false;

        }

        /* 4. An invalid value is found. */
        if ( ! $_bVerified ) {

            /* 4-1. Set the error array for the input fields. */
            $oAdminPage->setFieldErrors( $_aErrors );
            $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'admin-page-framework-loader' ) );

            return $aOldInput;

        }

        return $aInput;

    }

    /**
     * Lets the user download the minified version of Admin Page Framework.
     *
     * @since   3.4.6
     */
    public function replyToDownloadMinifiedVersion( $aSavedData, $sSubmittedFieldID, $sSubmittedInputID, $oAdminPage ) {

        $_sMinifiedVersionPath = AdminPageFrameworkLoader_Registry::$sDirPath . '/library/admin-page-framework.min.php';
        if ( file_exists( $_sMinifiedVersionPath ) ) {
            return $this->_modifyClassNames( file_get_contents( $_sMinifiedVersionPath ) );
        }
        return $aSavedData;

    }
        /**
         * Modifies the class names of the minified script.
         *
         * @since       3.4.6
         */
        private function _modifyClassNames( $sCode ) {

            $_sPrefix = isset( $_POST[ $this->oFactory->oProp->sOptionKey ][ $this->sSectionID ][ 'class_prefix' ] ) && $_POST[ $this->oFactory->oProp->sOptionKey ][ $this->sSectionID ][ 'class_prefix' ]
                ? $_POST[ $this->oFactory->oProp->sOptionKey ][ $this->sSectionID ][ 'class_prefix' ]
                : '';

            return str_replace(
                'AdminPageFramework',         // search
                $_sPrefix . 'AdminPageFramework',         // replace
                $sCode // subject
            );


        }
    /**
     * Filters the file name.
     *
     * @remark      The callback method for the "export_name_{page slug}_{tab slug}" filter.
     * Inside `$_POST`
     * <code>
     * [APF_Demo_Tool] => Array (
     *   [minifier] => Array (
     *       [class_prefix] =>
     *       [minified_script_name] => admin-page-framework.min.php
     *   )
     * )
     * </code>
     */
    public function replyToFilterFileName( $sFileName, $sFieldID, $sInputID, $vExportingData, $oAdminPage ) {

        return isset(
                $_POST[ $this->oFactory->oProp->sOptionKey ][ $this->sSectionID ][ 'minified_script_name' ]
            )
            && $_POST[ $this->oFactory->oProp->sOptionKey ][ $this->sSectionID ][ 'minified_script_name' ]
                ? $_POST[ $this->oFactory->oProp->sOptionKey ][ $this->sSectionID ][ 'minified_script_name' ]
                : $sFileName;

    }

}
