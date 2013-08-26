## Documentation Guideline for Admin Page Framework ##
The documentation of Admin Page Framework is directly written in the source code and a document parser is used to generate HTML files from it.

Therefore, in order to write documentation, write it right before the definition of classes, methods, properties, and functions etcs.

### Syntax ###

The syntax of [DocBlock](http://en.wikipedia.org/wiki/PHPDoc) is employed.

### Example ###
`	/**
	* Sets the given message to be displayed in the next page load. 
	* 
	* This is used to inform users about the submitted input data, such as "Updated sucessfully." or "Problem occured." etc. and normally used in validation callback methods.
	* 
	* <strong>Example</strong>
	* <code>if ( ! $fVerified ) {
	*		$this->setFieldErrors( $arrErrors );		
	*		$this->setSettingNotice( 'There was an error in your input.' );
	*		return $arrOldPageOptions;
	*	}</code>
	* @since			2.0.0
	* @access 			protected
	* @remark			The user may use this method in their extended class definition.
	* @param			string		$strMsg					the text message to be displayed.
	* @param			string		$strType				( optional ) the type of the message, either "error" or "updated"  is used.
	* @param			string		$strID					( optional ) the ID of the message. This is used in the ID attribute of the message HTML element.
	* @return			void
	*/		
	protected function setSettingNotice( $strMsg, $strType='error', $strID=null ) {
		
		add_settings_error( 
			$this->oProps->strOptionKey, // the script specific ID so the other settings error won't be displayed with the settings_errors() function.
			isset( $strID ) ? $strID : ( isset( $_GET['page'] ) ? $_GET['page'] : $this->oProps->strOptionKey ), 	// the id attribute for the message div element.
			$strMsg,	// error or updated
			$strType
		);
					
	}`

### Submit ###
Before submitting your documentation, please raise an [issue](https://github.com/michaeluno/admin-page-framework/issues?direction=desc&labels=Documentation&page=1&sort=created&state=open) first so that we can discuss whether it is appropriate or something is missing or not. 