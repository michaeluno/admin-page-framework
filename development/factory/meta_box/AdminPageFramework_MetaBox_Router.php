<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Handles routing of function calls and instantiation of associated classes.
 *
 * @abstract
 * @since           3.3.0
 * @package         AdminPageFramework/Factory/MetaBox
 * @internal
 */
abstract class AdminPageFramework_MetaBox_Router extends AdminPageFramework_Factory {
  
    /**
     * Constructs the class object instance of AdminPageFramework_MetaBox.
     * 
     * Mainly sets up properties and hooks.
     * 
     * @see         http://codex.wordpress.org/Function_Reference/add_meta_box#Parameters
     * @since       2.0.0
     * @param       string          $sMetaBoxID             The meta box ID. [3.3.0+] If an empty value is passed, the ID will be automatically generated and the lower-cased class name will be used.
     * @param       string          $sTitle                 The meta box title.
     * @param       string|array    $asPostTypeOrScreenID   (optional) The post type(s) or screen ID that the meta box is associated with.
     * @param       string          $sContext               (optional) The part of the page where the edit screen section should be shown ('normal', 'advanced', or 'side') Default: `normal`.
     * @param       string          $sPriority              (optional) The priority within the context where the boxes should show ('high', 'core', 'default' or 'low') Default: `default`.
     * @param       string          $sCapability            (optional) The <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> to the meta box. Default: `edit_posts`.
     * @param       string          $sTextDomain            (optional) The text domain applied to the displayed text messages. Default: `admin-page-framework`.
     * @return      void
     */ 
    public function __construct( $sMetaBoxID, $sTitle, $asPostTypeOrScreenID=array( 'post' ), $sContext='normal', $sPriority='default', $sCapability='edit_posts', $sTextDomain='admin-page-framework' ) {
             
        parent::__construct( $this->oProp );
        
        $this->oProp->sMetaBoxID    = $sMetaBoxID 
            ? $this->oUtil->sanitizeSlug( $sMetaBoxID ) 
            : strtolower( $this->oProp->sClassName );
        $this->oProp->sTitle        = $sTitle;
        $this->oProp->sContext      = $sContext;    // 'normal', 'advanced', or 'side' 
        $this->oProp->sPriority     = $sPriority;   // 'high', 'core', 'default' or 'low'    

        if ( ! $this->oProp->bIsAdmin ) {
            return;
        }
        
        $this->oUtil->registerAction(
            'current_screen', 
            array( $this, '_replyToDetermineToLoad' )
        );                    

    }
  
    /**
     * Determines whether the meta box belongs to the loading page.
     * 
     * @since       3.0.3
     * @since       3.2.0       Changed the scope to `public` from `protected` as the head tag object will access it.
     * @since       3.3.0       Moved from `AdminPageFramework_MetaBox`.
     * @since       3.8.14      Changed the visibility scope to `protected` from `public` as there is the `isInThePage()` public method.
     * @internal
     */
    protected function _isInThePage() {

        if ( ! in_array( $this->oProp->sPageNow, array( 'post.php', 'post-new.php' ) ) ) {
            return false;
        }
        
        if ( ! in_array( $this->oUtil->getCurrentPostType(), $this->oProp->aPostTypes ) ) {     
            return false;    
        }    

        return true;
        
    }        
    
    /**
     * Determines whether the meta box class components should be loaded in the currently loading page.
     * @since       3.1.3    
     * @internal
     */
    protected  function _isInstantiatable() {
        
        // Disable in admin-ajax.php
        if ( isset( $GLOBALS[ 'pagenow' ] ) && 'admin-ajax.php' === $GLOBALS[ 'pagenow' ] ) {
            return false;
        }
        return true;
        
    }
   
}
