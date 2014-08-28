<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Setting_Form' ) ) :
/**
 * Handles the submitted data of the framework's form.
 *  
 * @abstract
 * @since 3.0.0
 * @extends AdminPageFramework_Menu
 * @package AdminPageFramework
 * @subpackage Page
 * @internal
 */
abstract class AdminPageFramework_Setting_Form extends AdminPageFramework_Setting_Base {
        
    /**
     * Handles the form submitted data.
     * 
     * If the form is submitted, it calls the validation callback method and reloads the page.
     * 
     * @remark  This method is triggered when the page is about to be rendered.
     * @since   3.1.0
     */
    protected function _handleSubmittedData() {
        
        /*  The $_POST array will look like this:
            array(
                [option_page] => APF_Demo
                [action] => update
                [_wpnonce] => d3f9bd2fbc
                [_wp_http_referer] => /wp39x/wp-admin/edit.php?post_type=apf_posts&page=apf_builtin_field_types&tab=textfields
                [APF_Demo] => Array (
                        [text_fields] => Array( ...)
                    )

                [page_slug] => apf_builtin_field_types
                [tab_slug] => textfields
                [_is_admin_page_framework] => ...
            )
        */
        if ( 
            ! isset( 
                // The framework specific keys
                $_POST['_is_admin_page_framework'], // holds the form nonce
                $_POST['page_slug'], 
                $_POST['tab_slug'], 
                // The settings API fields keys - deprecated
                // $_POST['option_page'], 
                // $_POST['action'], 
                // $_POST['_wpnonce'], // deprecated
                $_POST['_wp_http_referer']
            ) 
        ) {     
            return;
        }
        $_sRequestURI   = remove_query_arg( array( 'settings-updated' ), wp_unslash( $_SERVER['REQUEST_URI'] ) );
        $_sReffererURI  = remove_query_arg( array( 'settings-updated' ), $_POST['_wp_http_referer'] );
        if ( $_sRequestURI != $_sReffererURI ) { // see the function definition of wp_referer_field() in functions.php.
            return;     
        }
        
        $_sNonceTransientKey = 'form_' . md5( $this->oProp->sClassName . get_current_user_id() );
        if ( $_POST['_is_admin_page_framework'] !== $this->oUtil->getTransient( $_sNonceTransientKey ) ) {
            $this->setAdminNotice( $this->oMsg->__( 'nonce_verification_failed' ) );
            return;
        }
        $this->oUtil->deleteTransient( $_sNonceTransientKey );
        
        // If only page-meta-boxes are used, it's possible that the option key element does not exist.
        $_aInput = isset( $_POST[ $this->oProp->sOptionKey ] ) ? $_POST[ $this->oProp->sOptionKey ] : array();
        $_aInput = $this->_doValidationCall( stripslashes_deep( $_aInput ) );
        if ( ! $this->oProp->_bDisableSavingOptions ) {    
            $this->oProp->updateOption( $_aInput );
        }

        // Reload the page with the update notice.
        exit( wp_redirect( add_query_arg( array( 'settings-updated' => true ) ) ) );
        
    }
                            

}
endif;