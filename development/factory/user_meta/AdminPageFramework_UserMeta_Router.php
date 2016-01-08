<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Handles routing of function calls and instantiation of associated classes.
 *
 * @abstract
 * @since           3.5.0
 * @package         AdminPageFramework
 * @subpackage      UserMeta
 * @internal
 */
abstract class AdminPageFramework_UserMeta_Router extends AdminPageFramework_Factory {
    
    /**
     * Sets up hooks.
     * 
     * @since       3.5.0
     */
    public function __construct( $oProp ) {
        
        parent::__construct( $oProp );
        
        if ( ! $this->oProp->bIsAdmin ) {
            return;
        }
        
        $this->oUtil->registerAction(
            'current_screen',
            array( $this, '_replyToDetermineToLoad' )
        );
        
        // 3.7.10+
        add_action( 'set_up_' . $this->oProp->sClassName, array( $this, '_replyToSetUpHooks' ) );
                        
    }
    
    /**
     * Determines whether the factory fields belong to the loading page.
     * 
     * @internal
     * @since       3.5.0
     * @return      boolean
     */
    public function _isInThePage() {
               
        if ( ! $this->oProp->bIsAdmin ) {
            return false;
        }

        return in_array( 
            $this->oProp->sPageNow,
            array( 'user-new.php', 'user-edit.php', 'profile.php' )
        );

    }    
 
    /**
     * Sets up hooks after calling the `setUp()` method.
     * 
     * @since       3.7.10
     * @callback    action      set_up_{instantiated class name}
     * @internal
     */
    public function _replyToSetUpHooks( $oFactory ) {
        
        // Hooks to display fields.
        add_action( 'show_user_profile', array( $this, '_replyToPrintFields' ) );   // profile.php
        add_action( 'edit_user_profile', array( $this, '_replyToPrintFields' ) );   // profile.php
        add_action( 'user_new_form', array( $this, '_replyToPrintFields' ) );   // user-new.php
        
        // Hooks to save field values.
        add_action( 'personal_options_update', array( $this, '_replyToSaveFieldValues' ) ); // profile.php
        add_action( 'edit_user_profile_update', array( $this, '_replyToSaveFieldValues' ) );    // profile.php
        add_action('user_register', array( $this, '_replyToSaveFieldValues' ) );    // user-new.php
                       
    }        
    
}