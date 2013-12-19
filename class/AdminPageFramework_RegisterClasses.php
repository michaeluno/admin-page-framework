<?php
class AdminPageFramework_RegisterClasses {
	
	function __construct( $sClassDirPath, & $aClassPaths=array() ) {
		
		$this->aClassPaths = $aClassPaths;	// the link to the array storing registered classes outside this object.
		$this->sClassDirPath = trailingslashit( $sClassDirPath );	
		$this->aClassFileNames = array_map( array( $this, 'getBaseName' ), glob( $this->sClassDirPath . '*.php' ) );
		$this->setUpClassArray();
				
	}
	public function getBaseName( $sPath ) {
		return basename( $sPath );
	}	
	
	/**
	 * Sets up the array consisting of class paths with the key of file base name.
	 * 
	 * This array is referred by the auto-loader callback and pass the stored file path. 
	 * So the plugin can be extended by modifying this path locations so that the plugin loads the modified classes
	 * instead of the built-in ones.
	 * 
	 * An example of the structure of $this->aClassPath 
	 * 
		Array (
			[AdminPageFramework_APIRequestTransient.php] => ...\wp36x\wp-content\plugins\amazon-auto-links/class/AdminPageFramework_APIRequestTransient.php
			[AdminPageFramework_AdminPage.php] => ...\wp36x\wp-content\plugins\amazon-auto-links/class/AdminPageFramework_AdminPage.php
			[AdminPageFramework_AdminPage_.php] => ...\wp36x\wp-content\plugins\amazon-auto-links/class/AdminPageFramework_AdminPage_.php
			...
			...
		)
	 * 
	 */
	function setUpClassArray() {
				
		foreach( $this->aClassFileNames as $sClassFileName ) {
			
			// if it's set, do not register ( add it to the array ).
			if ( isset( $this->aClassPaths[ $sClassFileName ] ) ) continue;
			
			$this->aClassPaths[ $sClassFileName ] = $this->sClassDirPath . $sClassFileName;	
		
		}

	}
	
	/**
	 * Performs registration of the callback.
	 * 
	 * This registers the method to be triggered when an unknown class is instantiated. 
	 * 
	 * @remark			The front-end method. 
	 */
	public function registerClasses() {
		
		spl_autoload_register( array( $this, 'callBackFromAutoLoader' ) );
		
	}
	
	public function callBackFromAutoLoader( $sClassName ) {
		
		$sBaseName = $sClassName . '.php';
		
		if ( ! in_array( $sBaseName, $this->aClassFileNames ) ) return;
		
		if ( file_exists( $this->aClassPaths[ $sBaseName ] ) ) 
			include_once( $this->aClassPaths[ $sBaseName ] );
		
	}
	
}