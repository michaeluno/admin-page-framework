<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides routing methods for the post type factory class.
 * 
 * @abstract
 * @since           3.0.4
 * @package         AdminPageFramework/Factory/PostType
 * @internal
 */
abstract class AdminPageFramework_PostType_Router extends AdminPageFramework_Factory {    
  
    /**
     * Sets up hooks and properties.
     * 
     * @internal
     * @remark      Make sure to call the parent construct first as the factory router need to set up sub-class objects.
     * @since       3.7.10
     */
    public function __construct( $oProp ) {
                
        parent::__construct( $oProp );

        $this->oUtil->registerAction( 'init', array( $this, '_replyToDetermineToLoad' ) );
        $this->oUtil->registerAction( 'current_screen', array( $this, '_replyToDetermineToLoadAdmin' ) );
        
    }
    
    /**
     * Determines whether to load the admin components of the post type.
     * 
     * @internal   
     * @since       3.7.10
     * @return      void
     * @callback    action      current_screen
     */
    public function _replyToDetermineToLoadAdmin( /* $oScreen */ ) {

        if ( ! $this->_isInThePage() ) {
            return;
        }
        
        $this->load();
        $this->oUtil->addAndDoAction( 
            $this, 
            "load_{$this->oProp->sPostType}", 
            $this 
        );
    
    }
    
    /**
     * Calls the setUp() method.
     * 
     * In this method, unlike the other factory classes, _isInThePage() is not used to check whether to load the `setUp()`.
     * This is because a registration of a post type should be done in any page. 
     * For example, a post type with UI enabled is not registered in an admin page, the top-level menu will not be added in the page.
     * Also a post type should be accessible from the front-end. So the check is not necessary.
     * 
     * @internal
     * @return      void
     * @since       3.7.10     
     */
    public function _replyToDetermineToLoad() {
        
        $this->setUp();
        
        // This action hook must be called AFTER the _setUp() method as there are callback methods that hook into this hook and assumes required configurations have been made.
        $this->oUtil->addAndDoAction( 
            $this, 
            "set_up_{$this->oProp->sClassName}", 
            $this 
        );
    
    }     

    /**
     * Instantiates a link object based on the type.
     * 
     * @since       3.7.10
     * @internal
     * @return      null|object
     */
    protected function _getLinkObject() {
        $_sClassName = $this->aSubClassNames[ 'oLink' ];
        return new $_sClassName( $this->oProp, $this->oMsg );        
    }          

    /**
     * Instantiates a link object based on the type.
     * 
     * @since       3.7.10
     * @internal
     * @return      null|object
     */    
    protected function _getPageLoadObject() {
        $_sClassName = $this->aSubClassNames[ 'oPageLoadInfo' ];
        return new $_sClassName( $this->oProp, $this->oMsg );
    }
  
    /**
     * Determines whether the currently loaded page is of the post type page.
     * 
     * @internal
     * @since       3.0.4
     * @since       3.2.0       Changed the scope to public from protected as the head tag object will access it.
     * @since       3.8.14      Changed the visibility scope to `protected` from `public` as there is the `isInThePage()` public method.
     * @return      boolean
     */
    protected function _isInThePage() {

        // If it's not in one of the post type's pages
        if ( ! $this->oProp->bIsAdmin ) {
            return false;
        }

        // Post table columns use ajax to update when the user modifies the post meta via quick edit.
        if ( $this->oProp->bIsAdminAjax && $this->oUtil->getElement( $this->oProp->aPostTypeArgs, 'public', true ) ) {
            return true;
        }        
        
        if ( ! in_array( $this->oProp->sPageNow, array( 'edit.php', 'edit-tags.php', 'term.php', 'post.php', 'post-new.php' ) ) ) {
            return false;
        }
                
        // 3.7.9+  Limitation: If the `page` argument is set in the query url, 
        // this factory will not be loaded to make the overall responses lighter.
        if ( isset( $_GET[ 'page' ] ) ) {
            return false;
        }

        return $this->oUtil->getCurrentPostType() === $this->oProp->sPostType;

    }
    
    /**
     * Determines whether the class component classes should be instantiated or not.
     * 
     * @internal
     * @callback    action      current_screen
     * @return      void
     * @since       3.7.0
     */
    public function _replyToLoadComponents( /* $oScreen */ ) {

        if ( 'plugins.php' === $this->oProp->sPageNow ) {
            $this->oLink = $this->_replyTpSetAndGetInstance_oLink();
        }
        
        parent::_replyToLoadComponents();
        
    }
  
}
