const gulp = require( 'gulp' );
const requireDir = require('require-dir');
const tasks = requireDir('./tool/gulp/tasks/');

// gulp sass
tasks.sass.src                  = [ './development/**/*.scss', '!node_modules/**' ];
exports.sass                    = tasks.sass.callback;

// gulp js-minify
tasks[ 'js-minify' ].src        = [ './development/**/*.js', '!./development/**/*.min.js', '!node_modules/**' ];
exports[ 'js-minify' ]          = tasks[ 'js-minify' ].callback;

// gulp js-concat
tasks[ 'js-concat' ].src        = [ './development/**/{*,}.bundle/*.js', '!./development/**/*.min.js', '!./development/**/*.bundle.js' ];
exports[ 'js-concat' ]          = tasks[ 'js-concat' ].callback;

// gulp js-concat-min
tasks[ 'js-concat-min' ].src    = [ './development/**/{*,}.bundle/*.min.js', '!./development/**/*.bundle.js' ];
exports[ 'js-concat-min' ]      = tasks[ 'js-concat-min' ].callback;

// gulp watch
exports.watch   = gulp.series( function( cb ) {
    gulp.watch( tasks.sass.src, tasks.sass.callback );
    gulp.watch( tasks[ 'js-minify' ].src, tasks[ 'js-minify' ].callback );
    // watch([ '**/*.js', '!node_modules/**'], parallel(runLinter));
    gulp.watch( tasks[ 'js-concat' ].src, tasks[ 'js-concat' ].callback );
    gulp.watch( tasks[ 'js-concat-min' ].src, tasks[ 'js-concat-min' ].callback );
  }
);

// gulp
exports.default = exports.watch;