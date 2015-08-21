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
 */
class APF_Demo_ManageOptions_Property {

    public function __construct( $oFactory, $sPageSlug, $sTabSlug ) {
    
        $this->oFactory     = $oFactory;
        $this->sClassName   = $oFactory->oProp->sClassName;
        $this->sPageSlug    = $sPageSlug; 
        $this->sTabSlug     = $sTabSlug;
        $this->sSectionID   = $this->sTabSlug;
        
        $this->_addTab();
    
    }
    
    private function _addTab() {
        
        $this->oFactory->addInPageTabs(    
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Properties', 'admin-page-framework-loader' ),
            )
        );  
        
        // load + page slug + tab slug
        add_action( 'load_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToLoadTab' ) );
  
    }
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {
        
        add_action( 'do_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToDoTab' ) );

    }
    
    public function replyToDoTab() {
   
        ?>
        <h3><?php _e( 'Framework Properties', 'admin-page-framework-demo' ); ?></h3>
        <p><?php _e( 'These are the property values stored in the framework. Advanced users may change the property values by directly modifying the <code>$this->oProp</code> object.', 'admin-page-framework-demo' ); ?></p>
        <pre class="dump-array"><code>$this-&gt;oDebug-&gt;get( get_object_vars( $this-&gt;oProp ) );</code></pre>
        <?php
            $this->oFactory->oDebug->dump( get_object_vars( $this->oFactory->oProp ) );   
   
     
    }
    
}