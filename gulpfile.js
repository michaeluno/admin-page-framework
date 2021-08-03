const gulp = require( 'gulp' );
const requireDir = require('require-dir');
const tasks = requireDir('./tool/gulp/tasks/');

// command: gulp sass
tasks.sass.src                  = [ './development/**/*.scss', '!./development/**/_*.scss', '!node_modules/**' ];
exports.sass                    = tasks.sass.callback;

// command: gulp sass-use
tasks[ 'sass-use' ].src         = [ './development/**/_*.scss', '!node_modules/**' ];
exports[ 'sass-use' ]           = tasks[ 'sass-use' ].callback;

// command: gulp js-minify
tasks[ 'js-minify' ].src        = [ './development/**/*.js', '!./development/**/*.min.js', '!node_modules/**' ];
exports[ 'js-minify' ]          = tasks[ 'js-minify' ].callback;

// command: gulp js-concat
tasks[ 'js-concat' ].src        = [ './development/**/{*,}.bundle/*.js', '!./development/**/*.min.js', '!./development/**/*.bundle.js' ];
exports[ 'js-concat' ]          = tasks[ 'js-concat' ].callback;

// command: gulp js-concat-min
tasks[ 'js-concat-min' ].src    = [ './development/**/{*,}.bundle/*.min.js', '!./development/**/*.bundle.js' ];
exports[ 'js-concat-min' ]      = tasks[ 'js-concat-min' ].callback;

// command: gulp watch
exports.watch   = gulp.series( function( cb ) {
    gulp.watch( tasks.sass.src, tasks.sass.callback );
    gulp.watch( tasks[ 'sass-use' ].src, tasks[ 'sass-use' ].callback );
    gulp.watch( tasks[ 'js-minify' ].src, tasks[ 'js-minify' ].callback );
    // watch([ '**/*.js', '!node_modules/**'], parallel(runLinter));
    gulp.watch( tasks[ 'js-concat' ].src, tasks[ 'js-concat' ].callback );
    gulp.watch( tasks[ 'js-concat-min' ].src, tasks[ 'js-concat-min' ].callback );
  }
);

// gulp
exports.default = exports.watch;