<?php
if ( ! class_exists( 'AdminPageFramework_Page_MetaBox' ) ) :
/**
 * Provides methods to insert meta box in pages added by the framework.
 *
 * @abstract
 * @since			3.0.0
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Page
 */
abstract class AdminPageFramework_Page_MetaBox extends AdminPageFramework_Base {

	function __construct() {	
			
		add_action( 'admin_head', array( $this, '_replyToEnableMetaBox' ) );	// since the screen object needs to be established, some hooks are too early like admin_init or admin_menu.
		add_action( 'add_meta_boxes', array( $this, '_replyToAddMetaBox' ) );
		
		/* Call the parent constructor.	*/
		$aArgs = func_get_args();
		call_user_func_array( array( $this, "parent::__construct" ), $aArgs );
					
	}
		
		
	/**
	 * Renders the registered meta boxes for the side position.
	 * 
	 * @remark			Called in the _renderPage() method.
	 * @remark			If no meta box is registered, nothing will be printed.
	 * @param			string			$sContext			'side', 'normal', or 'advanced'
	 * @since			3.0.0
	 * @internal
	 */
	protected function _printMetaBox( $sContext, $iContainerID ) {
			
		/* If nothing is registered do not render even the container */
		if ( ! isset( $GLOBALS['wp_meta_boxes'][ $GLOBALS['page_hook'] ][ $sContext ] ) 
			|| count( $GLOBALS['wp_meta_boxes'][ $GLOBALS['page_hook'] ][ $sContext ] ) <= 0 ) return;
		
		echo "<div id='postbox-container-{$iContainerID}' class='postbox-container'>";
			do_meta_boxes( '', $sContext, null ); 
		echo "</div>";

	}
						
		
	/**
	 * Enables meta boxes for the currently loading page 
	 * 
	 * @remark			In order to enable the Screen Option tab, this must be called at earlier point of the page load. The admin_head hooks seems to be sufficient.
	 * @since			3.0.0
	 * @internal
	 */
	public function _replyToEnableMetaBox() {
		
		$oScreen = get_current_screen();
		$sScreenID = $oScreen->id;

// echo '<pre>hook_suffix: ' . $GLOBALS['hook_suffix'] . '</pre>';
// echo '<pre>page_hook: ' . $GLOBALS['page_hook'] . '</pre>';
// $this->oScreen = get_current_screen();	// store it for later use	
// echo '<pre>screen id: ' . $this->oScreen->id . '</pre>';
// echo '<pre>columns: ' . ( 1 == get_current_screen()->get_columns() ? '1' : '2' ) . '</pre>';

		/* Trigger the add_meta_boxes hooks to allow meta boxes to be added */
		do_action( "add_meta_boxes_{$sScreenID}", null );
		do_action( 'add_meta_boxes', $sScreenID, null );
		wp_enqueue_script( 'postbox' );
		add_action( "admin_footer-{$sScreenID}", array( $this, '_replyToAddMetaboxScript' ) );
		
	}
	/**
	 * Adds meta box script.
	 * @remark			This method may be called multiple times if the main class is instantiated multiple times. But it is only enough to perform once.
	 * @since			3.0.0
	 * @internal
	 */
	public function _replyToAddMetaboxScript() {
			
		if ( isset( $GLOBALS['aAdminPageFramework']['bAddedMetaBoxScript'] ) ) return;
		$GLOBALS['aAdminPageFramework']['bAddedMetaBoxScript'] = true;
		
		?>
		<script class="admin-page-framework-insert-metabox-script">
			jQuery( document).ready( function(){ postboxes.add_postbox_toggles( pagenow ); });
		</script>
		<?php
	}
	
	/**
	 * Registers meta boxes with conditions.
	 * @since			3.0.0
	 * @internal
	 */
	public function _replyToAddMetaBox() {
		
		// foreach( $this->oProp->aMetaBoxes as $aMetaBox ) 
			// add_meta_box( 
				// $aMetaBox['id'], 		// id
				// $aMetaBox['title'], 	// title
// array( $this, '_replyToPrintMetaBoxContents' ), 	// callback
// $sPostType,		// screen id
				// $aMetaBox['context'], 	// context
				// $aMetaBox['priority'],	// priority
				// $aMetaBox['arguments']	// arguments
			// );
			
	}
}
endif;