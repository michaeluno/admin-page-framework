## Documentation Guideline for Admin Page Framework ##
The documentation of Admin Page Framework is directly written in the source code and a document parser is used to generate HTML files from it.

Therefore, in order to write documentation, write it right before the definition of classes, methods, properties, and functions etc.

### Syntax ###

The syntax of [DocBlock](http://en.wikipedia.org/wiki/PHPDoc) is employed.

The allowed HTML tags: `b`, `i`, `a`, `ul`, `ol`, `li`, `p`, `br`, `var`, `samp`, `kbd`, `tt`, `code`, `blockquote`, `pre`, `h4`, `h3`, `h2`, `h1`, `strong`, `em`, `span`.

### Example ###
	/**
	 * Sets the given message to be displayed in the next page load. 
	 * 
	 * This is used to inform users about the submitted input data, such as "Updated sucessfully." or "Problem occured." etc. and normally used in validation callback methods.
	 * 
	 * <strong>Example</strong>
	 * <code>if ( ! $bVerified ) {
	 *		$this->setFieldErrors( $aErrors );		
	 *		$this->setSettingNotice( 'There was an error in your input.' );
	 *		return $aOldPageOptions;
	 *	}</code>
	 * @since			2.0.0
	 * @access 			protected
	 * @remark			The user may use this method in their extended class definition.
	 * @param			string			the text message to be displayed.
	 * @param			string			( optional ) the type of the message, either "error" or "updated"  is used.
	 * @param			string			( optional ) the ID of the message. This is used in the ID attribute of the message HTML element.
	 * @return			void
	 */		
	protected function setSettingNotice( $sMsg, $sType='error', $sID=null ) {
		
		add_settings_error( 
			$this->oProp->sOptionKey, 
			isset( $sID ) ? $sID : ( isset( $_GET['page'] ) ? $_GET['page'] : $this->oProp->sOptionKey ),
			$sMsg,
			$sType
		);
					
	}

### Submit ###
Before submitting your documentation, please raise an [issue](https://github.com/michaeluno/admin-page-framework/issues?direction=desc&labels=Documentation&page=1&sort=created&state=open) first so that we can discuss whether it is appropriate or something is missing or not. 
