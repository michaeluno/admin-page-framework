<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods of views for the post type factory class.
 * 
 * Those methods are public and provides means for users to set property values.
 * 
 * @abstract
 * @since       3.0.4
 * @package     AdminPageFramework
 * @subpackage  PostType
 */
abstract class AdminPageFramework_PostType_Controller extends AdminPageFramework_PostType_View {    

    /**
     * Sets up hooks and properties.
     * 
     * @internal
     */
    function __construct( $oProp ) {
        
        // 3.4.2+ Changed the hook to init from wp_loaded.
        if ( did_action( 'init' ) ) {  // For the activation hook.
            $this->setup_pre();
        } {
            add_action( 'init', array( $this, 'setup_pre' ) );     
        }
        
        // Parent classes includes the model class and it registers the post type with the init hook.
        // For the case this class is instniated after the init hook, the setUp() method should be called earlier than that.
        // Thus, the parent constructor must be called after the call of setup_pre() above.        
        parent::__construct( $oProp );
        
    }

    /**
    * The method for necessary set-ups.
    * 
    * <h4>Example</h4>
    * <code>public function setUp() {
    *         $this->setAutoSave( false );
    *         $this->setAuthorTableFilter( true );
    *         $this->addTaxonomy( 
    *             'sample_taxonomy', // taxonomy slug
    *             array( // argument - for the argument array keys, refer to : http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
    *                 'labels'              => array(
    *                     'name'            => 'Genre',
    *                     'add_new_item'    => 'Add New Genre',
    *                     'new_item_name'   => "New Genre"
    *                 ),
    *                 'show_ui'                 => true,
    *                 'show_tagcloud'           => false,
    *                 'hierarchical'            => true,
    *                 'show_admin_column'       => true,
    *                 'show_in_nav_menus'       => true,
    *                 'show_table_filter'       => true, // framework specific key
    *                 'show_in_sidebar_menus'   => false, // framework specific key
    *             )
    *         );
    *     }</code>
    * 
    * @abstract
    * @since        2.0.0
    * @remark       The user should override this method in their class definition.
    * @remark       A callback for the `wp_loaded` hook.
    */
    public function setUp() {}    
        
    /*
     * Head Tag Methods
     */
    /**
     * {@inheritdoc}
     * 
     * {@inheritdoc}
     * 
     * @since       3.0.0
     * @return      array       An array holding the handle IDs of queued items.
     */
    public function enqueueStyles( $aSRCs, $aCustomArgs=array() ) {     
        if ( method_exists( $this->oResource, '_enqueueStyles' ) ) {
            return $this->oResource->_enqueueStyles( $aSRCs, array( $this->oProp->sPostType ), $aCustomArgs );
        }
    }
    /**
     * {@inheritdoc}
     * 
     * {@inheritdoc}
     * 
     */    
    public function enqueueStyle( $sSRC, $aCustomArgs=array() ) {
        if ( method_exists( $this->oResource, '_enqueueStyle' ) ) {
            return $this->oResource->_enqueueStyle( $sSRC, array( $this->oProp->sPostType ), $aCustomArgs );     
        }
    }
    /**
     * {@inheritdoc}
     * 
     * {@inheritdoc}
     * 
     * @return      array       An array holding the handle IDs of queued items.
     */
    public function enqueueScripts( $aSRCs, $aCustomArgs=array() ) {
        if ( method_exists( $this->oResource, '_enqueueScripts' ) ) {
            return $this->oResource->_enqueueScripts( $aSRCs, array( $this->oProp->sPostType ), $aCustomArgs );
        }
    }    
    /**
     * {@inheritdoc}
     * 
     * {@inheritdoc}
     *  
     * @since       3.0.0
     */
    public function enqueueScript( $sSRC, $aCustomArgs=array() ) {    
        if ( method_exists( $this->oResource, '_enqueueScript' ) ) {
            return $this->oResource->_enqueueScript( $sSRC, array( $this->oProp->sPostType ), $aCustomArgs );
        }
    }     
    
    /*
     * Front-end methods
     */
    /**
    * Enables or disables the auto-save feature in the custom post type's post submission page.
    * 
    * <h4>Example</h4>
    * <code>$this->setAutoSave( false );
    * </code>
    * 
    * @since        2.0.0
    * @param        boolean         If true, it enables the auto-save; otherwise, it disables it.
    * return        void
    */ 
    protected function setAutoSave( $bEnableAutoSave=True ) {
        $this->oProp->bEnableAutoSave = $bEnableAutoSave;     
    }
    
    /**
    * Adds a custom taxonomy to the class post type.
    * <h4>Example</h4>
    * <code>$this->addTaxonomy( 
    *   'sample_taxonomy', // taxonomy slug
    *   array( // argument
    *       'labels'        => array(
    *       'name'          => 'Genre',
    *       'add_new_item'  => 'Add New Genre',
    *       'new_item_name' => "New Genre"
    *   ),
    *   'show_ui'               => true,
    *   'show_tagcloud'         => false,
    *   'hierarchical'          => true,
    *   'show_admin_column'     => true,
    *   'show_in_nav_menus'     => true,
    *   'show_table_filter'     => true,  // framework specific key
    *   'show_in_sidebar_menus' => false, // framework specific key
    *   )
    * );</code>
    * 
    * @see      http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
    * @since    2.0.0
    * @since    3.1.1       Added the third parameter.
    * @param    string      $sTaxonomySlug              The taxonomy slug.
    * @param    array       $aArgs                      The taxonomy argument array passed to the second parameter of the <a href="http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments">register_taxonomy()</a> function.
    * @param    array       $aAdditionalObjectTypes     Additional object types (post types) besides the caller post type.
    * @return   void
    */ 
    protected function addTaxonomy( $sTaxonomySlug, array $aArgs, array $aAdditionalObjectTypes=array() ) {
        
        $sTaxonomySlug = $this->oUtil->sanitizeSlug( $sTaxonomySlug );
        $this->oProp->aTaxonomies[ $sTaxonomySlug ] = $aArgs;    
        if ( isset( $aArgs['show_table_filter'] ) && $aArgs['show_table_filter'] ) {
            $this->oProp->aTaxonomyTableFilters[] = $sTaxonomySlug;
        }
        if ( isset( $aArgs['show_in_sidebar_menus'] ) && ! $aArgs['show_in_sidebar_menus'] ) {
            $this->oProp->aTaxonomyRemoveSubmenuPages[ "edit-tags.php?taxonomy={$sTaxonomySlug}&amp;post_type={$this->oProp->sPostType}" ] = "edit.php?post_type={$this->oProp->sPostType}";
        }
        
        $_aExistingObjectTypes = isset( $this->oProp->aTaxonomyObjectTypes[ $sTaxonomySlug ] ) && is_array( $this->oProp->aTaxonomyObjectTypes[ $sTaxonomySlug ] )
            ? $this->oProp->aTaxonomyObjectTypes[ $sTaxonomySlug ] 
            : array();
        $aAdditionalObjectTypes = array_merge( $_aExistingObjectTypes, $aAdditionalObjectTypes );
        $this->oProp->aTaxonomyObjectTypes[ $sTaxonomySlug ] = array_unique( $aAdditionalObjectTypes );

        // Set up hooks. If the 'init' hook is already done, register it now.
        if ( did_action( 'init' ) ) {
            $this->_registerTaxonomy( $sTaxonomySlug, $aAdditionalObjectTypes, $aArgs );
        } else {
            if ( 1 == count( $this->oProp->aTaxonomies ) ) {
                add_action( 'init', array( $this, '_replyToRegisterTaxonomies' ) ); // the hook should not be admin_init because taxonomies need to be accessed in regular pages.
            }
        }
        
        if ( did_action( 'admin_menu' ) ) {
            $this->_replyToRemoveTexonomySubmenuPages();
        } else {
            if ( 1 == count( $this->oProp->aTaxonomyRemoveSubmenuPages ) ) {
                add_action( 'admin_menu', array( $this, '_replyToRemoveTexonomySubmenuPages' ), 999 ); 
            }
        }
    
    }    

    /**
    * Sets whether the author drop-down filter is enabled/disabled in the post type post list table.
    * 
    * <h4>Example</h4>
    * <code>$this->setAuthorTableFilter( true );
    * </code>
    * 
    * @since        2.0.0
    * @param        boolean     $bEnableAuthorTableFileter      If true, it enables the author filter; otherwise, it disables it.
    * @return       void
    */ 
    protected function setAuthorTableFilter( $bEnableAuthorTableFileter=false ) {
        $this->oProp->bEnableAuthorTableFileter = $bEnableAuthorTableFileter;
    }
    
    /**
     * Sets the post type arguments.
     * 
     * This is only necessary if it is not set in the constructor.
     * 
     * @since           2.0.0
     * @deprecated      3.2.0           Use the setArguments() method instead.
     * @return          void
     */ 
    protected function setPostTypeArgs( $aArgs ) {
        $this->setArguments( ( array ) $aArgs );
    }
    
    /**
     * Sets the post type arguments.
     * 
     * @remark      The alias of `setPostTypeArgs()`.
     * @see         http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
     * @param       array       $aArguments     The <a href="http://codex.wordpress.org/Function_Reference/register_post_type#Arguments">array of arguments</a> to be passed to the second parameter of the `register_post_type()` function.
     * @since       3.2.0
     */
    protected function setArguments( array $aArguments=array() ) {
        $this->oProp->aPostTypeArgs = $aArguments;
    }
    
    /**
     * Sets the given HTML text into the footer on the left hand side.
     * 
     * <h4>Example</h4>
     * <code>$this->setFooterInfoLeft( '<br />Custom Text on the left hand side.' );
     * </code>
     * 
     * @since       2.0.0
     * @param       string      $sHTML      The HTML code to insert.
     * @param       boolean     $bAppend    If true, the text will be appended; otherwise, it will replace the default text.
     * @return      void
     */    
    protected function setFooterInfoLeft( $sHTML, $bAppend=true ) {
        if ( isset( $this->oLink ) ) // check if the object is set to ensure it won't trigger a warning message in non-admin pages.
            $this->oLink->aFooterInfo['sLeft'] = $bAppend 
                ? $this->oLink->aFooterInfo['sLeft'] . $sHTML
                : $sHTML;
    }
    
    /**
     * Sets the given HTML text into the footer on the right hand side.
     * 
     * <h4>Example</h4>
     * <code>$this->setFooterInfoRight( '<br />Custom Text on the right hand side.' );
     * </code>
     * 
     * @since       2.0.0
     * @param       string      $sHTML      The HTML code to insert.
     * @param       boolean     $bAppend    If true, the text will be appended; otherwise, it will replace the default text.
     * @return      void
     */     
    protected function setFooterInfoRight( $sHTML, $bAppend=true ) {
        if ( isset( $this->oLink ) ) // check if the object is set to ensure it won't trigger a warning message in non-admin pages.    
            $this->oLink->aFooterInfo['sRight'] = $bAppend 
                ? $this->oLink->aFooterInfo['sRight'] . $sHTML
                : $sHTML;
    }
    
}