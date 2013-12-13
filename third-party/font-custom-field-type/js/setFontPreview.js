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

}