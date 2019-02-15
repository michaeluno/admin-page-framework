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
 * @since       3.5.3       Extends `AdminPageFrameworkLoader_AdminPage_Tab_ReadMeBase`.
 * @extends     AdminPageFrameworkLoader_AdminPage_Tab_ReadMeBase
 */
class AdminPageFrameworkLoader_AdminPageWelcome_Welcome extends AdminPageFrameworkLoader_AdminPage_Tab_ReadMeBase {

    public function replyToLoadTab( $oFactory ) {

        add_action( "style_{$this->sPageSlug}_{$this->sTabSlug}", array( $this, 'replyToAddInlineCSS' ) );

        $oFactory->addSettingSections(
            $this->sPageSlug, // the target page slug
            array(
                'tab_slug'          => $this->sTabSlug,
                'section_id'        => $this->sTabSlug,
                'section_tab_slug'  => 'welcome',
                'title'             => __( "What's New", 'admin-page-framework-loader' ),   // '
                'content'           => $this->_getReadmeContents(
                    AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/text/about.txt',
                    '', // TOC title
                    array( 'New Features' ) // section
                )
            ),
            array(
                'tab_slug'          => $this->sTabSlug,
                'section_tab_slug'  => $this->sTabSlug,
                'section_id'        => 'getting_started',
                'title'             => __( "Getting Started", 'admin-page-framework-loader' ),   // '
                'content'           => $this->_getReadmeContents(
                    AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/text/about.txt',
                    "<h3>" . __( 'Contents', 'admin-page-framework-loader' ) . "</h3>",
                    array( 'Getting Started', 'Tutorials' )
                ),
            ),
            array(
                'tab_slug'          => $this->sTabSlug,
                'section_tab_slug'  => $this->sTabSlug,
                'section_id'        => 'change_log',
                'title'             => __( "Change Log", 'admin-page-framework-loader' ),   // '
                'content'           => $this->_getChangeLog(),
            )
        );

        new GitHubCustomFieldType( 'admin_page_framework' );

        $oFactory->addSettingSections(
            $this->sPageSlug, // the target page slug
            array(
                'section_id'        => 'credit',
                'tab_slug'          => $this->sTabSlug,
                'section_tab_slug'  => $this->sTabSlug,
                'title'         => __( 'Credit', 'admin-page-framework-loader' ),
                // 'description'   => __( 'Admin Page Framework uses GitHub.', 'admin-page-framework-loader' ),
            )
        );
        $oFactory->addSettingFields(
            'credit', // the target section id
            array(
                'field_id'  => '_message',
                'type'      => '_message',
                'show_title_column' => false,
                'content'   => "<p class='about-description'>"
                    . __( 'Admin Page Framework is created by the following contributors.', 'admin-page-framework-loader' )
                . "</p>"
            ),
            array(
                'field_id'  => '_contributors',
                'type'      => '_contributors',
                'show_title_column' => false,
                'content'   => $this->_getContributors(),
            ),
            array(
                'field_id'      => 'github_star',
                'type'          => 'github',
                'user_name'     => 'michaeluno',    // the GitHub account ID
                'button_type'   => 'star',          // either of the followings: follow, star, watch, fork, issue
                'count'         => false,
                'repository'    => 'admin-page-framework',
                'size'          => 'mega',
                'attributes'    => array(
                    'data-text' => ' ' . AdminPageFramework_Registry::NAME . ' ' . AdminPageFramework_Registry::getVersion() . ' ',
                    // 'data-icon' => 'octicon-mark-github',
                ),
                'description'   => __( 'Star the repository and get Involved!', 'admin-page-framework-loader' ),
                'show_title_column' => false,
            )
        );

    }
        /**
         *
         * @since       3.5.0
         * @return      void
         */
        public function replyToAddInlineCSS( $sCSSRules ) {
            return $sCSSRules
. ".changelog h4 {
    /* margin: 0; */
}
.form-table td p {
    margin: 1em 0;
}
.admin-page-framework-section-tab h4 {
    padding: 10px 16px 12px;
    font-size: 1.6em;
    font-weight: 400;
}
.admin-page-framework-content .toc ul li {
    margin-left: 2em;
}
.admin-page-framework-content ul li {
    margin-left: 0;
}";
        }

        /**
         * Retrieves contents of a change log section of a readme file.
         * @since       3.6.1
         * @return      void
         */
        private function _getChangeLog( $sSection='Changelog' ) {

            $_aReplacements   = array(
                '%PLUGIN_DIR_URL%'  => AdminPageFrameworkLoader_Registry::getPluginURL(),
                '%WP_ADMIN_URL%'    => admin_url(),
            );
            $_oWPReadmeParser = new AdminPageFramework_WPReadmeParser(
                AdminPageFrameworkLoader_Registry::$sDirPath . '/readme.txt',
                $_aReplacements
            );
            $_sChangeLog = $_oWPReadmeParser->getSection( $sSection );
            $_oWPReadmeParser = new AdminPageFramework_WPReadmeParser(
                AdminPageFrameworkLoader_Registry::$sDirPath . '/changelog.md',
                $_aReplacements
            );
            $_sChangeLog .= $_oWPReadmeParser->getSection( $sSection );

            $_sChangeLog = $_sChangeLog
                ? $_sChangeLog
                : '<p>' . __( 'No valid changlog was found.', 'admin-page-framework-loader' ) . '</p>';
            return "<div class='changelog'>"
                . $_sChangeLog
                . "</div>";

        }

    // public function replyToDoTab() {

        // echo $this->_getReadmeContents(
            // AdminPageFrameworkLoader_Registry::$sDirPath . '/asset/text/about.txt',
            // '', // TOC title
            // array( 'New Features' ) // section
        // );

    // }


        /**
         * Render Contributors List
         *
         * @since   3.5.0
         * @return  string      An HTML formatted list of all the contributors of Admin Page Framework.
         */
        private function _getContributors() {

            $_aContributors = $this->_getContributorsFromGitHub( 'https://api.github.com/repos/michaeluno/admin-page-framework' );
            if ( empty( $_aContributors ) ) {
                return '';
            }

            $_aOutput   = array();
            foreach ( $_aContributors as $_oContributor ) {
                $_aOutput[] = '<li class="wp-person">';
                $_aOutput[] .= sprintf( '<a href="%s" title="%s">',
                    esc_url( 'https://github.com/' . $_oContributor->login ),
                    esc_html( sprintf( __( 'View %s', 'admin-page-framework-loader' ), $_oContributor->login ) )
                );
                $_aOutput[] .= sprintf( '<img src="%s" width="64" height="64" class="gravatar" alt="%s" />', esc_url( $_oContributor->avatar_url ), esc_html( $_oContributor->login ) );
                $_aOutput[] .= '</a>';
                $_aOutput[] .= sprintf( '<a class="web" href="%s">%s</a>', esc_url( 'https://github.com/' . $_oContributor->login ), esc_html( $_oContributor->login ) );
                $_aOutput[] .= '</a>';
                $_aOutput[] .= '</li>';
            }

            return '<ul class="wp-people-group">'
                    . implode( PHP_EOL, $_aOutput )
                . '</ul>';

        }

        /**
         * Retrieve a list of contributors from GitHub.
         *
         * @access      private
         * @since       3.5.0
         * @return      array       A list of contributors
         */
        private function _getContributorsFromGitHub( $sRepositoryURL ) {

            $_aContributors = get_transient( 'apfl_contributors' );

            if ( false !== $_aContributors ) {
                return $_aContributors;
            }

            $_mResponse = wp_remote_get( $sRepositoryURL . '/contributors', array( 'sslverify' => false ) );

            if ( is_wp_error( $_mResponse ) || 200 != wp_remote_retrieve_response_code( $_mResponse ) ) {
                return array();
            }

            $_aContributors = json_decode( wp_remote_retrieve_body( $_mResponse ) );

            if ( ! is_array( $_aContributors ) )
                return array();

            set_transient( 'apfl_contributors', $_aContributors, 3600 );

            return $_aContributors;
        }

}

