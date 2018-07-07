<?php
/**
 * Admin Page Framework
 * 
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2018, Michael Uno; Licensed MIT
 * 
 */

/**
 * Handles routing of function calls and instantiation of associated classes.
 *
 * @abstract
 * @since           3.5.0
 * @package         AdminPageFramework/Factory/TaxonomyField
 * @internal
 */
abstract class AdminPageFramework_TaxonomyField_Router extends AdminPageFramework_Factory {

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
        
        // this class need be loaded in ajax.php and ajax.php does not trigger current_screen action hook.
        $this->oUtil->registerAction(
            'wp_loaded', 
            array( $this, '_replyToDetermineToLoad' )
        );
        
        add_action( 
            'set_up_' . $this->oProp->sClassName,
            array( $this, '_replyToSetUpHooks' )
        );
        
    }
  
    /**
     * Determines whether the taxonomy fields belong to the loading page.
     * 
     * @internal
     * @since       3.0.3
     * @since       3.2.0       Changed the scope to `public` from `protected` as the head tag object will access it.
     * @since       3.5.0       Moved from `AdminPageFramework_TaxonomyField`.
     * @since       3.8.14      Changed the visibility scope to `protected` from `public` as there is the `isInThePage()` public method.
     */
    protected function _isInThePage() {

        if ( $this->oProp->bIsAdminAjax ) {
            return true;
        }
        
        if ( ! in_array( $this->oProp->sPageNow, array( 'edit-tags.php', 'term.php' ) ) ) {
            return false;
        }
        
        if ( isset( $_GET[ 'taxonomy' ] ) && ! in_array( $_GET[ 'taxonomy' ], $this->oProp->aTaxonomySlugs ) ) {
            return false;
        }        
        
        return true;
  
    }    
   
    /**
     * Sets up hooks after calling the `setUp()` method.
     * 
     * @since       3.7.10
     * @callback    action      set_up_{instantiated class name}
     * @internal
     */
    public function _replyToSetUpHooks( $oFactory ) {
        
        foreach( $this->oProp->aTaxonomySlugs as $_sTaxonomySlug ) {     
            
            // Validation callbacks need to be set regardless of whether the current page is edit-tags.php or not.
            add_action( "created_{$_sTaxonomySlug}", array( $this, '_replyToValidateOptions' ), 10, 2 );
            add_action( "edited_{$_sTaxonomySlug}", array( $this, '_replyToValidateOptions' ), 10, 2 );

            add_action( "{$_sTaxonomySlug}_add_form_fields", array( $this, '_replyToPrintFieldsWOTableRows' ) );
            add_action( "{$_sTaxonomySlug}_edit_form_fields", array( $this, '_replyToPrintFieldsWithTableRows' ) );
            
            add_filter( "manage_edit-{$_sTaxonomySlug}_columns", array( $this, '_replyToManageColumns' ), 10, 1 );
            add_filter( "manage_edit-{$_sTaxonomySlug}_sortable_columns", array( $this, '_replyToSetSortableColumns' ) );
            add_action( "manage_{$_sTaxonomySlug}_custom_column", array( $this, '_replyToPrintColumnCell' ), 10, 3 );
            
        }

        // 3.8.14+
        $this->_load();

    }    
  
}
