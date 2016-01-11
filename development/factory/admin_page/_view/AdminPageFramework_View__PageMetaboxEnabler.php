<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Enqueues page resources set with the `style` and `script` arguments.
 *
 * @abstract
 * @since           3.6.3
 * @package         AdminPageFramework
 * @subpackage      AdminPage
 * @internal
 * @extends         AdminPageFramework_FrameworkUtility
 */
class AdminPageFramework_View__PageMetaboxEnabler extends AdminPageFramework_FrameworkUtility {
    
    /**
     * Stores the admin page factory object.
     * @since       3.6.3
     */
    public $oFactory;

    /**
     * Sets up properties and hooks.
     * @since       3.6.3
     */
    public function __construct( $oFactory ) {
                       
        $this->oFactory = $oFactory;
        
        // Since the screen object needs to be established, some hooks are too early like admin_init or admin_menu.
        add_action( 
            'admin_head', 
            array( $this, '_replyToEnableMetaBox' )
        );                 
                
    }   
        /**
         * Enables meta boxes for the currently loading page 
         * 
         * @remark      In order to enable the Screen Option tab, this must be called at earlier point of the page load. The admin_head hooks seems to be sufficient.
         * @since       3.0.0
         * @since       3.6.3       Moved from `AdminPageFramework_Page_View_MetaBox`.
         * @internal
         * @callback    action      admin_head
         */
        public function _replyToEnableMetaBox() {

            if ( ! $this->_isMetaBoxAdded() ) {
                return;
            }
            
            $_sCurrentScreenID =  $this->getCurrentScreenID();

            // Trigger the add_meta_boxes hooks to allow meta boxes to be added.
            do_action( "add_meta_boxes_{$_sCurrentScreenID}", null );
            do_action( 'add_meta_boxes', $_sCurrentScreenID, null );

            // Resources
            wp_enqueue_script( 'postbox' );
            
            // Screen options
            $_iColumns = $this->getAOrB(
                $this->doesMetaBoxExist( 'side' ),
                2,
                1
            );
            add_screen_option(
                'layout_columns', 
                array(
                    'max'       => $_iColumns, 
                    'default'   => $_iColumns,
                )
            );
            
            // Used to save screen options.
            wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
            wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
                    
            // the network admin adds '-network' in the screen ID and the hooks with that id won't be triggered so use the 'page_hook' global variable.
            if ( isset( $GLOBALS[ 'page_hook' ] ) ) {
                add_action( 
                    "admin_footer-{$GLOBALS['page_hook']}", 
                    array( $this, '_replyToAddMetaboxScript' ) 
                );    
            }

        }
            /**
             * Checks if there are meta boxes added to the given slug of the page.
             * @internal
             * @since       3.0.0 
             * @since       3.6.3       Moved from `AdminPageFramework_Page_View_MetaBox`.
             * @return      boolean
             */
            private function _isMetaBoxAdded() {
                
                $_aPageMetaBoxClasses = $this->getElementAsArray(
                    $GLOBALS,
                    array( 'aAdminPageFramework', 'aMetaBoxForPagesClasses' )
                );
                if ( empty( $_aPageMetaBoxClasses ) ) {
                    return false;
                }
                
                $_sPageSlug = $this->getElement( $_GET, 'page', '' );
                if ( ! $_sPageSlug ) {     
                    return false;
                }
                
                foreach( $_aPageMetaBoxClasses as $_sClassName => $_oMetaBox ) {
                    if ( $this->_isPageOfMetaBox( $_sPageSlug, $_oMetaBox ) ) { 
                        return true; 
                    }
                }
                return false;
                
            }
                /**
                 * Checks if the given page slug belongs to the meta box of the given meta box object.
                 * @internal
                 * @remark      Checks the tab array in the aPageSlugs array and if the loading page tab does not match the tab elements, it yields false.
                 * @since       3.0.0
                 * @since       3.6.3       Moved from `AdminPageFramework_Page_View_MetaBox`.
                 * @return      boolean
                 */
                private function _isPageOfMetaBox( $sPageSlug, $oMetaBox ) {
                    
                    if ( in_array( $sPageSlug , $oMetaBox->oProp->aPageSlugs ) ) {
                        return true; // for numeric keys with a string value.
                    }
                    if ( ! array_key_exists( $sPageSlug , $oMetaBox->oProp->aPageSlugs ) ) {
                        return false; // for keys of page slugs, the key does not exist, it means not added.
                    }
                    
                    /* So the page slug key and its tab array is set. This means the user want to specify the meta box visibility per a tab basis. */     
                    $_aTabs             = $oMetaBox->oProp->aPageSlugs[ $sPageSlug ];
                    $_sCurrentTabSlug   = $this->oFactory->oProp->getCurrentTabSlug();
                    return ( $_sCurrentTabSlug && in_array( $_sCurrentTabSlug, $_aTabs ) );
                        
                }    
            
            /**
             * Adds meta box script.
             * @remark      This method may be called multiple times if the main class is instantiated multiple times. But it is only enough to perform once.
             * @since       3.0.0
             * @since       3.6.3       Moved from `AdminPageFramework_Page_View_MetaBox`.
             * @internal
             * @callback    action      admin_footer-{$GLOBALS[ 'page_hook' ]}
             */
            public function _replyToAddMetaboxScript() {

                // Check and set the flag which indicates whether the script is loaded or not.
                $_bLoaded = $this->getElement(
                    $GLOBALS, // subject
                    array( 'aAdminPageFramework', 'bAddedMetaBoxScript' ), // dimensional keys
                    false // default
                );
                if ( $_bLoaded ) {
                    return;
                }
                $GLOBALS[ 'aAdminPageFramework' ][ 'bAddedMetaBoxScript' ] = true;
                
                // Insert the script.
                $_sScript = <<<JAVASCRIPTS
jQuery( document).ready( function(){ 
    postboxes.add_postbox_toggles( pagenow ); 
});
JAVASCRIPTS;
                echo '<script class="admin-page-framework-insert-metabox-script">'
                        . '/* <![CDATA[ */'
                        . $_sScript
                        . '/* ]]> */'
                    . '</script>';

            }    
                
}