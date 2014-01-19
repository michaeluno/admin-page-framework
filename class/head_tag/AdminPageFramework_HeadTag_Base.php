<?php 
if ( ! class_exists( 'AdminPageFramework_HeadTag_Base' ) ) :
/**
 * Provides methods to enqueue or insert head tag elements into the head tag.
 * 
 * @abstract
 * @since			2.1.5
 * @use				AdminPageFramework_Utility
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Head Tag
 */
abstract class AdminPageFramework_HeadTag_Base {
	
	/**
	 * Represents the structure of the array for enqueuing scripts and styles.
	 * @since			2.1.2
	 * @since			2.1.5			Moved to the base class.
	 * @since			3.0.0			Moved from the property class.
	 * @internal
	 */
	protected static $_aStructure_EnqueuingScriptsAndStyles = array(
	
		/* The system internal keys. */
		'sSRC'			=> null,
		'aPostTypes' => array(),		// for meta box class
		'sPageSlug' => null,	
		'sTabSlug' => null,
		'sType' => null,		// script or style
		
		/* The below keys are for users. */
		'handle_id' => null,
		'dependencies' => array(),
        'version' => false,		// although the type should be string, the wp_enqueue_...() functions want false as the default value.
        'translation' => array(),	// only for scripts
        'in_footer' => false,	// only for scripts
		'media' => 'all',	// only for styles		
		
	);	
	
	function __construct( $oProp ) {
		
		$this->oProp = $oProp;
		$this->oUtil = new AdminPageFramework_WPUtility;
				
		// Hook the admin header to insert custom admin stylesheet.
		add_action( 'admin_head', array( $this, '_replyToAddStyle' ), 999 );
		add_action( 'admin_head', array( $this, '_replyToAddScript' ), 999 );
		add_action( 'admin_enqueue_scripts', array( $this, '_replyToEnqueueScripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, '_replyToEnqueueStyles' ) );
		
	}	
	
	/*
	 * Methods that should be overridden in extended classes.
	 */
	public function _replyToAddStyle() {}	// no parameter
	public function _replyToAddScript() {}	// no parameter
	protected function _enqueueSRCByConditoin( $aEnqueueItem ) {}
 	
	/*
	 * Shared methods
	 */
		
	/**
	 * Performs actual enqueuing items. 
	 * 
	 * @since			2.1.2
	 * @since			2.1.5			Moved from the main class.
	 * @internal
	 */
	protected function _enqueueSRC( $aEnqueueItem ) {
		
		// For styles
		if ( $aEnqueueItem['sType'] == 'style' ) {
			wp_enqueue_style( $aEnqueueItem['handle_id'], $aEnqueueItem['sSRC'], $aEnqueueItem['dependencies'], $aEnqueueItem['version'], $aEnqueueItem['media'] );
			return;
		}
		
		// For scripts
		wp_enqueue_script( $aEnqueueItem['handle_id'], $aEnqueueItem['sSRC'], $aEnqueueItem['dependencies'], $aEnqueueItem['version'], $aEnqueueItem['in_footer'] );
		if ( $aEnqueueItem['translation'] ) 
			wp_localize_script( $aEnqueueItem['handle_id'], $aEnqueueItem['handle_id'], $aEnqueueItem['translation'] );
		
	}
	
	/**
	 * Takes care of added enqueuing scripts by page slug and tab slug.
	 * 
	 * @remark			A callback for the admin_enqueue_scripts hook.
	 * @since			2.1.2
	 * @since			2.1.5			Moved from the main class. Changed the name from enqueueStylesCalback to replyToEnqueueStyles().
	 * @since			3.0.0			Changed the name to _replyToEnqueueStyles().
	 * @internal
	 */	
	public function _replyToEnqueueStyles() {	
		foreach( $this->oProp->aEnqueuingStyles as $sKey => $aEnqueuingStyle ) 
			$this->_enqueueSRCByConditoin( $aEnqueuingStyle );
	}
	
	/**
	 * Takes care of added enqueuing scripts by page slug and tab slug.
	 * 
	 * @remark			A callback for the admin_enqueue_scripts hook.
	 * @since			2.1.2
	 * @since			2.1.5			Moved from the main class. Changed the name from enqueueScriptsCallback to callbackEnqueueScripts().
	 * @since			3.0.0			Changed the name to _replyToEnqueueScripts().
	 * @internal
	 */
	public function _replyToEnqueueScripts() {							
		foreach( $this->oProp->aEnqueuingScripts as $sKey => $aEnqueuingScript ) 
			$this->_enqueueSRCByConditoin( $aEnqueuingScript );				
	}
	
}
endif;