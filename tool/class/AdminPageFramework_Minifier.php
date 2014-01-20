<?php
class AdminPageFramework_Minifier {

	function __construct( $sSourceFilePath, $sOutputFilePath, $aAllowedExtensions=array( 'php', 'inc' ) ) {
		
		$this->sSourceFilePath = $sSourceFilePath;
		$this->sSourceFileNameWOExt = pathinfo( $sSourceFilePath, PATHINFO_FILENAME );
		$this->sOutputFielPath = $sOutputFilePath;
		$this->sOutputFileNameWOExt = pathinfo( $sOutputFilePath, PATHINFO_FILENAME );
		$this->aAllowedExtensions = $aAllowedExtensions;
		
		$this->store( $this->sSourceFilePath, $this->aAllowedExtensions );		
		$this->sort();
		
	}
		
		/**
		 * Stores the found file into array.
		 * return array multi-dimensional array storing all the found files
		 */
		private function store( $sSourceFilePath, $aAllowedExtensions ) {
			$this->aFiles = $this->composeFileArray( dirname( $sSourceFilePath ), $aAllowedExtensions );
		}
		
		/**
		 * Sets up the array consisting of class paths with the key of file name w/o extension.
		 */
		private function composeFileArray( $sClassDirPath, $aAllowedExtensions ) {
			
			$sClassDirPath = rtrim( $sClassDirPath, '\\/' ) . DIRECTORY_SEPARATOR;	// ensures the trailing (back/)slash exists.
			$aFilePaths = $this->doRecursiveGlob( $sClassDirPath . '*.' . $this->getGlobPatternExtensionPart( $aAllowedExtensions ), GLOB_BRACE );

			/*
			 * Now the structure of $aFilePaths looks like:
				array
				  0 => string '.../class/MyClass.php'
				  1 => string '.../class/MyClass2.php'
				  2 => string '.../class/MyClass3.php'
				  ...
			 * 
			 */		 
			$aFiles = array(	// the file array structure.
				'path'	=>	null,
				'content'	=>	null,
				'code'	=>	null,
				'docblock'	=>	null,
				'dependency' => null,
			);
			foreach( $aFilePaths as $sFilePath ) {
				
				$sFileNameWOExt = pathinfo( $sFilePath, PATHINFO_FILENAME );	
				$sPHPCode = $this->getPHPCode( $sFilePath );
				$aFiles[ $sFileNameWOExt ] = array(	// the file name without extension will be assigned to the key
					'path'	=>	$sFilePath,	
					// 'content'	=> $this->getFileContents( $sFilePath ),
					'code'	=>	$sPHPCode ? trim( $sPHPCode ) : '',
					'docblock' => ( $sFileNameWOExt == $this->sSourceFileNameWOExt ) ? $this->getDocBlockOfBootstrap( $sFilePath ) : null,
					'dependency' => $this->getDependentClass( $sPHPCode ),
				); 

			}
			unset( $aFiles[ $this->sOutputFileNameWOExt ] );	// it's possible that the minified file also gets loaded but we don't want it.
			return $aFiles;
				
		}
			private function getPHPCode( $sFilePath ) {
				$sCode = php_strip_whitespace ( $sFilePath );
				$sCode = preg_replace( '/^<\?php/', '', $sCode );
				$sCode = preg_replace( '/\?>\s+?$/', '', $sCode );
				return $sCode;
			}
		
			/**
			 * Composes the file pattern of the file extension part used for the glob() function with the given file extensions.
			 */
			private function getGlobPatternExtensionPart( $aExtensions=array( 'php', 'inc' ) ) {
				return empty( $aExtensions ) ? '*' : '{' . implode( ',', $aExtensions ) . '}';
			}
			/**
			 * The recursive version of the glob() function.
			 */
			private function doRecursiveGlob( $sPathPatten, $iFlags=0 ) {

				$aFiles = glob( $sPathPatten, $iFlags );
				foreach ( glob( dirname( $sPathPatten ) . '/*', GLOB_ONLYDIR|GLOB_NOSORT ) as $sDirPath )
					$aFiles = array_merge( $aFiles, $this->doRecursiveGlob( $sDirPath . '/' . basename( $sPathPatten ), $iFlags ) );
			
				return $aFiles;
				
			}	
			
			/**
			 * Returns the file contents.
			 */
			private function getFileContents( $sFilePath ) {
				
				$hFile = fopen( $sFilePath, "r" );
				$sContents = fread( $hFile, filesize( $sFilePath ) );
				fclose( $hFile );				
				return $sContents;
				
			}
			/**
			 * Returns the docblock of the specified class
			 */
			private function getDocBlock( $sClassName ) {
				$oRC = new ReflectionClass( $sClassName );
				return trim( $oRC->getDocComment() );
			}
			/**
			 * Returns the docblock of the bootstrap script of Admin Page Framework.
			 * 
			 */
			private function getDocBlockOfBootstrap( $sFilePath ) {
				
				include_once( $sFilePath );
				$aClasses = get_declared_classes();
				foreach( $aClasses as $sClassName ) 
					if ( preg_match( '/(.+?)_Bootstrap$/', $sClassName, $aMatch ) )
						return $this->getDocBlock( $sClassName );
				return null;
				
			}
			
			/**
			 * Returns the depending class
			 */
			private function getDependentClass( $sPHPCode ) {
				
				if ( ! preg_match( '/class\s+(.+?)\s+extends\s+(.+?)\s{/i', $sPHPCode, $aMatch ) ) return null;
				
				return $aMatch[ 2 ];
				
			}
	
	public function sort() {

		$this->resolveDependant();

	}
		private function resolveDependant() {
		
			/* Append the dependant code to the dependant one and remove the dependant. */
			$this->aFiles = $this->moveDependant( $this->aFiles );
			
			/* Unset the dependant element */
			foreach ( $this->aFiles as $sClassName => $aFile ) 
				if ( ! $aFile['code'] )
					unset( $this->aFiles[ $sClassName ] );
			
			/* Make sure dependant elements no longer exist.*/
			$iDependency = 0;
			foreach ( $this->aFiles as $sClassName => $aFile ) 
				if ( $aFile['dependency'] && isset( $this->aFiles[ $aFile['dependency'] ] ) )
					$iDependency++;
			if ( $iDependency )
				$this->resolveDependant();
			
		}
		private function moveDependant( $aFiles ) {
			
			$iMoved = 0;
			foreach( $aFiles as $sClassName => &$aFile ) {
			
				if ( ! $aFile['dependency'] ) continue;
				if ( ! isset( $aFiles[ $aFile['dependency'] ] ) ) continue;	// it can be an external components.
				if ( ! $aFile['code'] ) continue;
				$aFiles[ $aFile['dependency'] ]['code'] .= $aFile['code'];
				$aFile['code'] = '';
				$iMoved++;
				
			}
			if ( $iMoved )
				$aFiles = $this->moveDependant( $aFiles );
			
			return $aFiles;
		}
		
	public function write() {
		
		$aData = array();
		$sFirstBlock = '<?php ' . PHP_EOL
			. $this->aFiles[ $this->sSourceFileNameWOExt ]['docblock'] . ' ';
		unset( $this->aFiles[ $this->sSourceFileNameWOExt ] );
		$aData[] = mb_convert_encoding( $sFirstBlock, 'UTF-8', 'auto' );
		
		foreach( $this->aFiles as $aFile ) 
			$aData[] = mb_convert_encoding( $aFile['code'], 'UTF-8', 'auto' );	// $aData[] = $aFile['code'];
		
		if ( file_exists( $this->sOutputFielPath ) )
			unlink( $this->sOutputFielPath );
		file_put_contents( $this->sOutputFielPath, implode( '', $aData ), FILE_APPEND | LOCK_EX );
		
	}
		private function writeStringToFile( $file, $string){
			$f=fopen($file, "wb");
			$file="\xEF\xBB\xBF".$file; // this is what makes the magic
			fputs($f, $string);
			fclose($f);
		}	

}