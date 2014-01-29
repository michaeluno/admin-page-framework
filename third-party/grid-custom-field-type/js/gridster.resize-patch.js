(function($) {
    $.Gridster.generate_stylesheet = function(opts) {
        var styles = '';
        var max_size_x = this.options.max_size_x;
		var max_size_x = this.options.max_size_x || this.cols;
        var max_rows = 0;
        var max_cols = 0;
        var i;
        var rules;
 
        opts || (opts = {});
        opts.cols || (opts.cols = this.cols);
        opts.rows || (opts.rows = this.rows);
        opts.namespace || (opts.namespace = this.options.namespace);
        opts.widget_base_dimensions || (opts.widget_base_dimensions = this.options.widget_base_dimensions);
        opts.widget_margins || (opts.widget_margins = this.options.widget_margins);
        opts.min_widget_width = (opts.widget_margins[0] * 2) +
            opts.widget_base_dimensions[0];
        opts.min_widget_height = (opts.widget_margins[1] * 2) +
            opts.widget_base_dimensions[1];
 		
        /* generate CSS styles for cols */
        for (i = opts.cols; i >= 0; i--) {
            styles += (opts.namespace + ' [data-col="'+ (i + 1) + '"] { left:' +
                ((i * opts.widget_base_dimensions[0]) +
                (i * opts.widget_margins[0]) +
                ((i + 1) * opts.widget_margins[0])) + 'px;} ');
        }
 
        /* generate CSS styles for rows */
        for (i = opts.rows; i >= 0; i--) {
            styles += (opts.namespace + ' [data-row="' + (i + 1) + '"] { top:' +
                ((i * opts.widget_base_dimensions[1]) +
                (i * opts.widget_margins[1]) +
                ((i + 1) * opts.widget_margins[1]) ) + 'px;} ');
        }
 
        for (var y = 1; y <= opts.rows; y++) {
            styles += (opts.namespace + ' [data-sizey="' + y + '"] { height:' +
                (y * opts.widget_base_dimensions[1] +
                (y - 1) * (opts.widget_margins[1] * 2)) + 'px;}');
        }
 
        for (var x = 1; x <= max_size_x; x++) {
            styles += (opts.namespace + ' [data-sizex="' + x + '"] { width:' +
                (x * opts.widget_base_dimensions[0] +
                (x - 1) * (opts.widget_margins[0] * 2)) + 'px;}');
        }
 
        return this.add_style_tag(styles);
    };
 
    $.Gridster.add_style_tag = function(css) {
        var d = document;
        var tag = d.createElement('style');
 
        tag.setAttribute('generated-from', 'gridster');
 
        d.getElementsByTagName('head')[0].appendChild(tag);
        tag.setAttribute('type', 'text/css');
 
        if (tag.styleSheet) {
            tag.styleSheet.cssText = css;
        } else {
            tag.appendChild(document.createTextNode(css));
        }
        return this;
    };
 
    $.Gridster.resize_widget_dimensions = function(options) {
        if (options.widget_margins) {
            this.options.widget_margins = options.widget_margins;
        }
 
        if (options.widget_base_dimensions) {
             this.options.widget_base_dimensions = options.widget_base_dimensions;
        }

// if ( options.max_cols ) {
	// this.options.max_cols = options.max_cols;
// }
// if ( options.min_cols ) {
	// this.options.min_cols = options.min_cols;
// }
// if ( options.extra_cols ) {
	// this.options.extra_cols = options.extra_cols;
// }
			
        this.min_widget_width  = (this.options.widget_margins[0] * 2) + this.options.widget_base_dimensions[0];
        this.min_widget_height = (this.options.widget_margins[1] * 2) + this.options.widget_base_dimensions[1];

        // var serializedGrid = this.serialize();
        this.$widgets.each($.proxy(function(i, widget) {
            var $widget = $(widget);
            this.resize_widget($widget);
			
        }, this));
 
        this.generate_grid_and_stylesheet();
        this.get_widgets_from_DOM();
        this.set_dom_grid_height();
 
		$('head [generated-from="gridster"]:not(:last)').remove();

        return false;
    };
	$.Gridster.get_widgets_from_DOM = function() {
		this.$widgets.each( $.proxy( function( i, widget ) {
			// this.remove_widget( widget, true, null );
			this.register_widget($(widget));
		}, this ) );
		return this;
	};	
		
	$.Gridster.isStuckOut = function( iMaxCols ) {
		
		var bIsStuckOut = false;
		this.$widgets.each( $.proxy( function( i, widget ) {
					
			if ( widget.hasOwnProperty('dataset') && iMaxCols < parseInt( widget['dataset']['col'] ) + parseInt( widget['dataset']['sizex'] ) - 1 ) {
				bIsStuckOut = true;
				return false;	// break;
			}
			
		}, this  ));
// console.log( { 'max cols': iMaxCols, 'is stuck out': bIsStuckOut, } );
		return bIsStuckOut;
		
	};
	
	$.fn.redraw = function(){
		$(this).each(function(){
			var redraw = this.offsetHeight;
		});
	};	
})(jQuery);