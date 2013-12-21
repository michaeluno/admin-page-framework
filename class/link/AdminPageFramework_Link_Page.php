<?php
if ( ! class_exists( 'AdminPageFramework_Link_Page' ) ) :
/**
 * Provides methods for HTML link elements for admin pages created by the framework, except the pages of custom post types.
 *
 * Embeds links in the footer and plugin's listing table etc.
 * 
 * @since			2.0.0
 * @since			3.0.0			Changed the name to AdminPageFramework_Link_Page_Page from AdminPageFramework_Link_Page.
 * @extends			AdminPageFramework_Link_Base
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Link
 */
class AdminPageFramework_Link_Page extends AdminPageFramework_Link_Base {
	
	/**
	 * Stores the caller script path.
	 * @since			2.0.0
	 */ 
	private $sCallerPath;
	
	/**
	 * The property object, commonly shared.
	 * @since			2.0.0
	 */ 
	private $oProp;
	
	public function __construct( &$oProp, $sCallerPath=null, $oMsg=null ) {
		
		if ( ! is_admin() ) return;
		
		$this->oProp = $oProp;
		$this->sCallerPath = file_exists( $sCallerPath ) ? $sCallerPath : $this->getCallerScriptPath();
		$this->oMsg = $oMsg;
		
		// Add script info into the footer 
		add_filter( 'update_footer', array( $this, '_replyToAddInfoInFooterRight' ), 11 );
		add_filter( 'admin_footer_text' , array( $this, '_replyToAddInfoInFooterLeft' ) );	
		$this->_setFooterInfoLeft( $this->oProp->aScriptInfo, $this->oProp->aFooterInfo['sLeft'] );
		$this->_setFooterInfoRight( AdminPageFramework_Property_Base::_getLibraryData(), $this->oProp->aFooterInfo['sRight'] );
	
		if ( $this->oProp->aScriptInfo['sType'] == 'plugin' )
			add_filter( 'plugin_action_links_' . plugin_basename( $this->oProp->aScriptInfo['sPath'] ) , array( $this, '_replyToAddSettingsLinkInPluginListingPage' ) );

	}

	
	/**	
	 * 
	 * @since			2.0.0
	 * @since			2.1.4			Changed to be static since it is used from multiple classes.
	 * @remark			The scope is public because this is accessed from an extended class.
	 */ 
	public static $_aStructure_SubMenuLink = array(		
		'title' => null,
		'href' => null,
		'sCapability' => null,
		'order' => null,
		'type' => 'link',
		'fShowPageHeadingTab' => true,
		'fShowInMenu' => true,
	);

	public function addSubMenuLink( $sMenuTitle, $sURL, $sCapability=null, $nOrder=null, $bShowPageHeadingTab=true, $bShowInMenu=true ) {
		
		$iCount = count( $this->oProp->aPages );
		$this->oProp->aPages[ $sURL ] = array(  
			'title'		=> $sMenuTitle,
			'title'		=> $sMenuTitle,	// used for the page heading tabs.
			'href'			=> $sURL,
			'type'			=> 'link',	// this is used to compare with the 'page' type.
			'sCapability'		=> isset( $sCapability ) ? $sCapability : $this->oProp->sCapability,
			'order'			=> is_numeric( $nOrder ) ? $nOrder : $iCount + 10,
			'fShowPageHeadingTab'	=> $bShowPageHeadingTab,
			'fShowInMenu'		=> $bShowInMenu,
		);	
			
	}
			
	/*
	 * Methods for embedding links 
	 */ 	
	public function addLinkToPluginDescription( $linkss ) {
		
		if ( !is_array( $linkss ) )
			$this->oProp->aPluginDescriptionLinks[] = $linkss;
		else
			$this->oProp->aPluginDescriptionLinks = array_merge( $this->oProp->aPluginDescriptionLinks , $linkss );
	
		add_filter( 'plugin_row_meta', array( $this, 'addLinkToPluginDescription_Callback' ), 10, 2 );

	}
	public function addLinkToPluginTitle( $linkss ) {
		
		if ( !is_array( $linkss ) )
			$this->oProp->aPluginTitleLinks[] = $linkss;
		else
			$this->oProp->aPluginTitleLinks = array_merge( $this->oProp->aPluginTitleLinks, $linkss );
		
		add_filter( 'plugin_action_links_' . plugin_basename( $this->oProp->aScriptInfo['sPath'] ), array( $this, 'AddLinkToPluginTitle_Callback' ) );

	}
	
	/*
	 * Callback methods
	 */ 
	
	/**
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the filter hook, <em>admin_footer_text</em>.
	 */ 
	public function _replyToAddInfoInFooterLeft( $sLinkHTML='' ) {

		if ( ! isset( $_GET['page'] ) || ! $this->oProp->isPageAdded( $_GET['page'] )  ) 
			return $sLinkHTML;	// $sLinkHTML is given by the hook.
		
		if ( empty( $this->oProp->aScriptInfo['sName'] ) ) return $sLinkHTML;
		
		return $this->oProp->aFooterInfo['sLeft'];

	}
	public function _replyToAddInfoInFooterRight( $sLinkHTML='' ) {

		if ( ! isset( $_GET['page'] ) || ! $this->oProp->isPageAdded( $_GET['page'] )  ) 
			return $sLinkHTML;	// $sLinkTHML is given by the hook.
			
		return $this->oProp->aFooterInfo['sRight'];
			
	}
	
	public function _replyToAddSettingsLinkInPluginListingPage( $aLinks ) {
		
		// For a custom root slug,
		$sLinkURL = preg_match( '/^.+\.php/', $this->oProp->aRootMenu['sPageSlug'] ) 
			? add_query_arg( array( 'page' => $this->oProp->sDefaultPageSlug ), admin_url( $this->oProp->aRootMenu['sPageSlug'] ) )
			: "admin.php?page={$this->oProp->sDefaultPageSlug}";
		
		array_unshift(	
			$aLinks,
			'<a href="' . $sLinkURL . '">' . $this->oMsg->__( 'settings' ) . '</a>'
		); 
		return $aLinks;
		
	}	
	
	public function addLinkToPluginDescription_Callback( $aLinks, $sFile ) {

		if ( $sFile != plugin_basename( $this->oProp->aScriptInfo['sPath'] ) ) return $aLinks;
		
		// Backward compatibility sanitization.
		$aAddingLinks = array();
		foreach( $this->oProp->aPluginDescriptionLinks as $linksHTML )
			if ( is_array( $linksHTML ) )	// should not be an array
				$aAddingLinks = array_merge( $linksHTML, $aAddingLinks );
			else
				$aAddingLinks[] = ( string ) $linksHTML;
		
		return array_merge( $aLinks, $aAddingLinks );
		
	}			
	public function addLinkToPluginTitle_Callback( $aLinks ) {

		// Backward compatibility sanitization.
		$aAddingLinks = array();
		foreach( $this->oProp->aPluginTitleLinks as $linksHTML )
			if ( is_array( $linksHTML ) )	// should not be an array
				$aAddingLinks = array_merge( $linksHTML, $aAddingLinks );
			else
				$aAddingLinks[] = ( string ) $linksHTML;
		
		return array_merge( $aLinks, $aAddingLinks );
		
	}		
}
endif;