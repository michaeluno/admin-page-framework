<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
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
        
        if ( $this->oProp->bIsAdmin ) {
            add_action( 'wp_loaded', array( $this, '_replyToDetermineToLoad' ) );
        }        
                
    }
    
    /**
     * Determines whether the factory fields belong to the loading page.
     * 
     * @internal
     * @since       3.5.0
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
     * Determines whether the meta box should be loaded in the currently loading page.
     * 
     * @since       3.5.0
     * @internal
     */
    public function _replyToDetermineToLoad( /* $oScreen */ ) {
        
        if ( ! $this->_isInThePage() ) { return; }

        // @todo introduce "set_up_pre_{ class name }" action hook.        
        $this->_setUp();
        
        // This action hook must be called AFTER the _setUp() method as there are callback methods that hook into this hook and assumes required configurations have been made.
        $this->oUtil->addAndDoAction( $this, "set_up_{$this->oProp->sClassName}", $this );
        
        $this->oProp->_bSetupLoaded = true;
        
        // the screen object should be established to detect the loaded page. 
        add_action( 'current_screen', array( $this, '_replyToRegisterFormElements' ), 20 ); 
        
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