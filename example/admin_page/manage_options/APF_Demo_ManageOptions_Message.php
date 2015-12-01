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
class APF_Demo_ManageOptions_Message {

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
                'title'         => __( 'Messages', 'admin-page-framework-loader' ),
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
        <h3><?php _e( 'Framework System Messages', 'admin-page-framework-loader' ); ?></h3>
        <p><?php _e( 'You can change the framework\'s defined internal messages by using the <code>setMessage()</code> method.', 'admin-page-framework-loader' ); // ' syntax fixer ?>
            <?php _e( 'The keys and the default values can be obtained with the <code>getMessage()</code> method.', 'admin-page-framework-loader' ); ?>            
        </p>
        <h4><?php _e( 'Check the Original Message', 'admin-page-framework-loader' ); ?></h4>
        <pre class="dump-array"><code>$this-&gt;getMessage( 'option_updated' );</code></pre>
        <?php $this->oFactory->oDebug->dump( $this->oFactory->getMessage( 'option_updated' ) ); ?>

        <h4><?php _e( 'Modify a Message', 'admin-page-framework-loader' ); ?></h4>
        <pre class="dump-array"><code>$this-&gt;setMessage( 'option_updated', 'This is a modified message.' );</code></pre>
        <?php 
            $this->oFactory->setMessage( 
                'option_updated', 
                __( 'This is a modified message', 'admin-page-framework-loader' ) 
            ); 
        ?>
        
        <h4><?php _e( 'List All the Messages', 'admin-page-framework-loader' ); ?></h4>
        <pre class="dump-array"><code>$this-&gt;getMessage();</code></pre>
        <?php
        $this->oFactory->oDebug->dump( $this->oFactory->getMessage() );
     
    }
    
}