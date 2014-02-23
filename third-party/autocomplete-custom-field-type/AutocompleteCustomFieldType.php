<?php
class AutoCompleteCustomFieldType extends AdminPageFramework_FieldType {
		
	/**
	 * Defines the field type slugs used for this field type.
	 * 
	 * The slug is used for the type key in a field definition array.
	 * 	$this->addSettingFields(
			array(
				'section_id'	=>	'...',
				'type'	=>	'autocomplete',		// <--- THIS PART
				'field_id'	=>	'...',
				'title'		=>	'...',
			)
		);
	 */
	public $aFieldTypeSlugs = array( 'autocomplete', );
	
	/**
	 * Defines the default key-values of this field type. 
	 * 
	 * The keys are used for the field definition array.
	 * 	$this->addSettingFields(
			array(
				'section_id'	=>	'...',	
				'type'	=>	'...',
				'field_id'	=>	'...',
				'my_custom_key' => '...',	// <-- THIS PART
			)
		);
	 * @remark			$_aDefaultKeys holds shared default key-values defined in the base class.
	 */
	protected $aDefaultKeys = array(
		'settings'	=>	null,	// will be set in the constructor.
		'settings2'	=>	null,	// will be set in the constructor.
		'attributes'	=>	array(
			'size'	=>	10,
			'maxlength'	=>	400,
		),	
	);

	public function __construct( $asClassName, $asFieldTypeSlug=null, $oMsg=null, $bAutoRegister=true ) {
		
		$_aGet = $_GET;
		unset( $_aGet['post_type'], $_aGet['request'], $_aGet['page'], $_aGet['tab'], $_aGet['settings-updated'] );
		$this->aDefaultKeys['settings'] = add_query_arg( array( 'request' => 'autocomplete' ) + $_aGet, admin_url( $GLOBALS['pagenow'] ) );
		$this->aDefaultKeys['settings2'] = array(
			'hintText'	=>	__( 'Type the title of posts.', 'admin-page-framework-demo' ),
		);
		parent::__construct( $asClassName, $asFieldTypeSlug, $oMsg, $bAutoRegister );
		
		/*
		 * If the request key is set in the url and it yields 'autocomplete', return a JSON output and exit.
		 */
		if ( isset( $_GET['request'] ) && $_GET['request'] == 'autocomplete' ) {
			
			if ( ! function_exists( 'is_user_logged_in' ) ) 
				include( ABSPATH . "wp-includes/pluggable.php" ); 			
			if ( is_user_logged_in() ) :
			
				$_aGet = $_GET;
				unset( $_aGet['request'], $_aGet['page'], $_aGet['tab'], $_aGet['settings-updated'] );
				
				// Compose the argument.
				$aArgs = $_aGet + array(
					'post_type' => 'post',
				);
				$oResults = new WP_Query( $aArgs );
				$aData = array();
				foreach( $oResults->posts as $iIndex => $oPost ) {
					$aData[ $iIndex ] = array(
						'id'	=>	$oPost->ID,
						'name'	=>	$oPost->post_title,
					);
				}
				die( json_encode( $aData ) );
			endif;
			
		}		
		
	}
	
	/**
	 * Loads the field type necessary components.
	 * 
	 * This method is triggered when a field definition array that calls this field type is parsed. 
	 */ 
	public function setUp() {}	

	/**
	 * Returns an array holding the urls of enqueuing scripts.
	 * 
	 * The returning array should be composed with all numeric keys. Each element can be either a string( the url or the path of the source file) or an array of custom argument.
	 * 
	 * <h4>Custom Argument Array</h4>
	 * <ul>
	 * 	<li><strong>src</strong> - ( required, string ) The url or path of the target source file</li>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the script.</li>
	 * 	<li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
	 * 	<li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>translation</strong> - ( optional, array ) The translation array. The handle ID will be used for the object name.</li>
	 * 	<li><strong>in_footer</strong> - ( optional, boolean ) Whether to enqueue the script before < / head > or before < / body > Default: <code>false</code>.</li>
	 * </ul>	 
	 */
	protected function getEnqueuingScripts() { 
		return array(
			array( 	// if you need to set a dependency, pass as a custom argument array. 
				'src'	=> dirname( __FILE__ ) . '/asset/jquery.tokeninput.js', 	// path or url
				'dependencies'	=> array( 'jquery' ) 
			),
			dirname( __FILE__ ) . '/asset/tokeninput.options-hander.js',	// a string value of the target path or url will work as well.
		);
	}
	
	/**
	 * Returns an array holding the urls of enqueuing styles.
	 * 
	 * <h4>Custom Argument Array</h4>
	 * <ul>
	 * 	<li><strong>src</strong> - ( required, string ) The url or path of the target source file</li>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the stylesheet.</li>
	 * 	<li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
	 * 	<li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>media</strong> - ( optional, string ) the description of the field which is inserted into the after the input field tag.</li>
	 * </ul>
	 */
	protected function getEnqueuingStyles() { 
		return array(
			dirname( __FILE__ ) . '/asset/token-input.css',
			dirname( __FILE__ ) . '/asset/token-input-facebook.css',
			dirname( __FILE__ ) . '/asset/token-input-mac.css',		
		);
	}			


	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	protected function getScripts() { 

		$aJSArray = json_encode( $this->aFieldTypeSlugs );
		/*	
		 * The below function will be triggered when a new repeatable field is added. 
		 * 
		 * Use the registerAPFCallback method to register a callback.
		 * Available callbacks are:
		 * 	added_repeatable_field - triggered when a repeatable field gets repeated. Parameters 1. (object) the jQuery element object. 2. (string) the field type slug. 3. (string) the field tag id.
		 * 	removed_repeatable_field - triggered when a repeatable field gets removed. Parameters 1. (object) the jQuery element object. 2. (string) the field type slug. 3. (string) the field tag id.
		 * 	sorted_fields - triggered when a sortable field gets sorted. Parameters 1. (object) the jQuery element object. 2. (string) the field type slug. 3. (string) the field tag id.
		 * */
		return "
			jQuery( document ).ready( function(){
				jQuery().registerAPFCallback( {				
					added_repeatable_field: function( node, sFieldType, sFieldTagID ) {
			
						/* If it is not this field type, do nothing. */
						if ( jQuery.inArray( sFieldType, {$aJSArray} ) <= -1 ) return;

						/* If the input tag is not found, do nothing  */
						var nodeNewAutoComplete = node.find( 'input.autocomplete' );
						if ( nodeNewAutoComplete.length <= 0 ) return;
						
						/* Remove unnecessary elements */
						node.find( 'ul.token-input-list' ).remove();
						
						/* Bind the knob script */
						var sFieldsID = node.closest( '.admin-page-framework-fields' ).attr( 'id' );
						var aOptions = jQuery( '#' + nodeNewAutoComplete.attr( 'id' ) ).getTokenInputOptions( sFieldsID );
						jQuery( nodeNewAutoComplete ).tokenInput( 
							aOptions[0], 
							jQuery.extend( true, aOptions[1], {
								onAdd: function ( item ) {
									jQuery( nodeNewAutoComplete ).attr( 'value', JSON.stringify( jQuery( nodeNewAutoComplete ).tokenInput( 'get' ) ) );
								},
								onDelete: function ( item ) {
									jQuery( nodeNewAutoComplete ).attr( 'value', JSON.stringify( jQuery( nodeNewAutoComplete ).tokenInput( 'get' ) ) );
								},
							})
						);
					},
					
				});
			});		
		
		" . PHP_EOL;
		
	}

	/**
	 * Returns IE specific CSS rules.
	 */
	protected function getIEStyles() { return ''; }

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	protected function getStyles() {
		return "
		.admin-page-framework-field-autocomplete {
			width: 100%;
		}
		.admin-page-framework-input-label-container {
			min-width: 200px;
		}
		";
	}

	
	/**
	 * Returns the output of the geometry custom field type.
	 * 
	 */
	/**
	 * Returns the output of the field type.
	 */
	protected function getField( $aField ) { 
			
		$aInputAttributes = array(
			'type'	=>	'text',
		) + $aField['attributes'];
		$aInputAttributes['class']	.= ' autocomplete';

		return 
			$aField['before_label']
			. "<div class='admin-page-framework-input-label-container'>"
				. "<label for='{$aField['input_id']}'>"
					. $aField['before_input']
					. ( $aField['label'] && ! $aField['repeatable']
						? "<span class='admin-page-framework-input-label-string' style='min-width:" .  $aField['label_min_width'] . "px;'>" . $aField['label'] . "</span>"
						: "" 
					)
					. "<input " . $this->generateAttributes( $aInputAttributes ) . " />"	// this method is defined in the base class
					. $aField['after_input']
				. "</label>"
			. "</div>"
			. $this->getAutocompletenablerScript( $aField['input_id'], $aField['settings'], $aField['settings2'] )
			. $aField['after_label'];
		
	}	
		
		private function getAutocompletenablerScript( $sInputID, $asParam1, $aParam2 ) {
			$asParam1 = is_array( $asParam1 ) ? json_encode( $asParam1 ) : "'" . $asParam1 . "'";
			$aParam2 = json_encode( ( array ) $aParam2 );
			return 
				"<script type='text/javascript' class='autocomplete-enabler-script'>
					jQuery( document ).ready( function() {

						var oSavedValues = jQuery.parseJSON( jQuery( '#{$sInputID}' ).attr( 'value' ) );						
						jQuery( '#{$sInputID}' ).tokenInput( 
							{$asParam1}, 
							jQuery.extend( true, {$aParam2}, {
								onAdd: function ( item ) {
									jQuery( '#{$sInputID}' ).attr( 'value', JSON.stringify( jQuery( '#{$sInputID}' ).tokenInput( 'get' ) ) );
								},
								onDelete: function ( item ) {
									jQuery( '#{$sInputID}' ).attr( 'value', JSON.stringify( jQuery( '#{$sInputID}' ).tokenInput( 'get' ) ) );
								},
							})
						);
						jQuery( oSavedValues ).each( function ( index, value) {
							jQuery( '#{$sInputID}' ).tokenInput( 'add', value );
						}); 
						jQuery( '#{$sInputID}' ).storeTokenInputOptions( jQuery( '#{$sInputID}' ).closest( '.admin-page-framework-fields' ).attr( 'id' ), {$asParam1}, {$aParam2} );
					});
				</script>";		
		}
	
}