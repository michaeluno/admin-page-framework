<?php 
if ( ! class_exists( 'AdminPageFramework_HeadTag_Base' ) ) :
/**
 * Provides methods to enqueue or insert head tag elements into the head tag.
 * 
 * @since			2.1.5
 * 
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
	public function _replyToAddStyle() {}
	public function _replyToAddScript() {}
	protected function _enqueueSRCByConditoin() {}
 	
	/*
	 * Shared methods
	 */
	/**
	 *	Prints the inline stylesheet of this class stored in this class property.
	 *	@since			3.0.0
	 */
	protected function _printClassSpecificStyles( $sIDPrefix ) {
			
		$oCaller = $this->oProp->_getParentObject();		

		// Print out the filtered styles.
		$sStyle = $this->oUtil->addAndApplyFilters( $oCaller, "style_{$this->oProp->sClassName}", $this->oProp->sStyle );
		if ( $sStyle )
			echo "<style type='text/css' id='{$sIDPrefix}-{$this->oProp->sClassName}'>{$sStyle}</style>";
			
		$sStyleIE = $this->oUtil->addAndApplyFilters( $oCaller, "style_ie_{$this->oProp->sClassName}", $this->oProp->sStyleIE );
		if ( $sStyleIE )
			echo  "<!--[if IE]><style type='text/css' id='{$sIDPrefix}-ie-{$this->oProp->sClassName}'>{$sStyleIE}</style><![endif]-->";
	
	}
	/**
	 * Prints the inline stylesheet of the meta-box common CSS rules with the style tag.
	 * 
	 * @since			3.0.0
	 * @remark			The meta box class may be instantiated multiple times so use a global flag.
	 * @parametr		string			$sIDPrefix			The id selector embedded in the script tag.
	 * @parametr		string			$sClassName			The class name that identify the call group. This is important for the meta-box class because it can be instantiated multiple times in one particular page.
	 */
	protected function _printCommonStyles( $sIDPrefix, $sClassName ) {
		
		if ( isset( $GLOBALS[ "{$sClassName}_StyleLoaded" ] ) && $GLOBALS[ "{$sClassName}_StyleLoaded" ] ) return;
		$GLOBALS[ "{$sClassName}_StyleLoaded" ] = true;			
		
		$oCaller = $this->oProp->_getParentObject();				
		$sStyle = $this->oUtil->addAndApplyFilters( $oCaller, "style_common_{$this->oProp->sClassName}", AdminPageFramework_Property_Base::$_sDefaultStyle );
		if ( $sStyle )
			echo "<style type='text/css' id='{$sIDPrefix}'>{$sStyle}</style>";

		$sStyleIE = $this->oUtil->addAndApplyFilters( $oCaller, "style_ie_common_{$this->oProp->sClassName}", AdminPageFramework_Property_Base::$_sDefaultStyleIE );
		if ( $sStyleIE )
			echo "<!--[if IE]><style type='text/css' id='{$sIDPrefix}-ie'>{$sStyleIE}</style><![endif]-->";
			
	}		
	/**
	 *	Prints the inline scripts of this class stored in this class property.
	 *	@since			3.0.0
	 */
	protected function _printClassSpecificScripts( $sIDPrefix ) {
			
		$sScript = $this->oUtil->addAndApplyFilters( $this->oProp->_getParentObject(), "script_{$this->oProp->sClassName}", $this->oProp->sScript );
		if ( $sScript )
			echo "<script type='text/javascript' id='{$sIDPrefix}-{$this->oProp->sClassName}'>{$sScript}</script>";				

	}
	/**
	 * Prints the inline scripts of the meta-box common scripts.
	 * 
	 * @remark			The meta box class may be instantiated multiple times so use a global flag.
	 * @parametr		string			$sIDPrefix			The id selector embedded in the script tag.
	 * @parametr		string			$sClassName			The class name that identify the call group. This is important for the meta-box class because it can be instantiated multiple times in one particular page.
	 * @since			3.0.0
	 */
	protected function _printCommonScripts( $sIDPrefix, $sClassName ) {
		
		if ( isset( $GLOBALS[ "{$sClassName}_ScriptLoaded" ] ) && $GLOBALS[ "{$sClassName}_ScriptLoaded" ] ) return;
		$GLOBALS[ "{$sClassName}_ScriptLoaded" ] = true;
		
		$sScript = $this->oUtil->addAndApplyFilters( $this->oProp->_getParentObject(), "script_common_{$this->oProp->sClassName}", AdminPageFramework_Property_Base::$_sDefaultScript );
		if ( $sScript )
			echo "<script type='text/javascript' id='{$sIDPrefix}'>{$sScript}</script>";
	
	}			
		
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