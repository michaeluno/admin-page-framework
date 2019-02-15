<?php
/**
 * Admin Page Framework Loader
 *
 * Demonstrates the usage of Admin Page Framework.
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed GPLv2
 *
 */

/**
 * Adds a tab of the set page to the loader plugin.
 *
 * @since       3.5.0
 */
class AdminPageFrameworkLoader_AdminPage_Help_Debug extends AdminPageFrameworkLoader_AdminPage_Tab_Base {

    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {

        $oAdminPage->addSettingFIeld(
            array(
                'field_id'  => 'reset',
                'type'      => 'submit',
                'reset'     => true,
                'show_title_column' => false,
                'value'     => __( 'Reset', 'admin-page-framework-loader' ),
            )
        );
    }

    public function replyToDoTab( $oFactory ) {

        echo "<h3>" . __( 'Saved Options', 'admin-page-framework-loader' ) . "</h3>";
        $oFactory->oDebug->dump( $oFactory->oProp->aOptions );

    }

}
