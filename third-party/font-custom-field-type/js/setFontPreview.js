setFontPreview = function( strURL, strInputID ) {
						
	// Remove the previous style element for the preview
	jQuery( '#font_preview_style_' + strInputID ).remove();
	
	// Hide the preview element 
	jQuery( '#font_preview_' + strInputID ).css( 'opacity', 0 );
	
	var strExtension = strURL.split( '.' ).pop().toLowerCase();
	switch ( strExtension ) {
		case 'eot':
			strFormat = 'embedded-opentype';
			break;
		case 'ttf':
			strFormat = 'truetype';
			break;
		case 'otf':
			strFormat = 'opentype';
			break;		
		default:
			strFormat = strExtension;	// woff, svg
			break;
	} 			
	
	// Set the new url for the preview 
	var strCSS = '@font-face { font-family: \"' + strInputID + '\"; src: url( ' + strURL + ' ) format( \"' + strExtension + '\" ) }';
	jQuery( 'head' ).append( '<style id=\"font_preview_style_' + strInputID + '\" type=\"text/css\">' +  strCSS + '</style>' );
	
	// Refresh the preview element	
	jQuery( '#font_preview_' + strInputID ).animate({
		width: '100%',
		height: '100%',
		opacity: 1,	
	}, 1000 );					

};

 /**
 * Removes the set values to the input tags.
 * 
 * @since   3.2.0
 */
removeInputValuesForFont = function( oElem ) {

    var _oImageInput = jQuery( oElem ).closest( '.admin-page-framework-field' ).find( '.font-field input' );
    if ( _oImageInput.length <= 0 )  {
        return;
    }
    
    // Find the input tag.
    var _sInputID = _oImageInput.first().attr( 'id' );
    
    // Remove the associated values.
    setFontPreviewElement( _sInputID, {} );
    
};

/**
 * Sets the preview element.
 * 
 * @since   3.2.0   Changed the scope to global.
 */                
setFontPreviewElement = function( sInputID, oFont ) {
    
    // If the user want the attributes to be saved, set them in the input tags.
    jQuery( 'input#' + sInputID ).val( oFont.url );        // the url field is mandatory so it does not have the suffix.

    // Change the font-face
    setFontPreview( oFont.url ? oFont.url : '', sInputID );

};