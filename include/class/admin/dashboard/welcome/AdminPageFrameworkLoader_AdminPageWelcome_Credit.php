<?php
/**
 * Admin Page Framework Loader
 * 
 * Demonstrates the usage of Admin Page Framework.
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds a tab of the set page to the loader plugin.
 * 
 * @since       3.5.0    
 * @sicne       3.5.3       Extends `AdminPageFrameworkLoader_AdminPage_Tab_Base`.
 * @extends     AdminPageFrameworkLoader_AdminPage_Tab_Base
 */
class AdminPageFrameworkLoader_AdminPageWelcome_Credit extends AdminPageFrameworkLoader_AdminPage_Tab_Base {
    
    /**
     * Stores a seciton ID.
     */
    public $sSectionID;
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {
         
        new GitHubCustomFieldType( 'admin_page_framework' ); 
        
        $oAdminPage->addSettingSections(    
            $this->sPageSlug, // the target page slug                
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                // 'title'         => __( 'GitHub Repository', 'admin-page-framework-loader' ),
                // 'description'   => __( 'Admin Page Framework uses GitHub.', 'admin-page-framework-loader' ),
            )
        );
        $oAdminPage->addSettingFields(
            $this->sSectionID, // the target section id
            array(
                'field_id'      => 'github_star',
                'type'          => 'github',     
                'user_name'     => 'michaeluno',    // the GitHub account ID
                'button_type'   => 'star',          // either of the followings: follow, star, watch, fork, issue
                'count'         => false,
                'repository'    => 'admin-page-framework',
                'size'          => 'mega',
                'attributes'    => array(
                    'data-text' => ' ' . AdminPageFrameworkLoader_Registry::Name . ' ' . AdminPageFrameworkLoader_Registry::Version . ' ',
                    // 'data-icon' => 'octicon-mark-github',
                ),
                'description'   => __( 'Star the repository and get Involved!', 'admin-page-framework-demo' ),
                'show_title_column' => false,
            )
        );
        
        add_action( 'do_form_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToDoTabBeforeForm' ) );
        
    }
    
    public function replyToDoTabBeforeForm() {
        
        $_aOutput   = array();	
        $_aOutput[] = "<p class='about-description'>"
                . __( 'Admin Page Framework is created by the following contributors.', 'admin-page-framework-loader' )
            . "</p>";
		
        echo implode( PHP_EOL, $_aOutput ); 
        echo $this->_getContributors(); 
        
	}    
    
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