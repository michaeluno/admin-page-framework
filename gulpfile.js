const gulp = require( 'gulp' );
const requireDir = require('require-dir');
const tasks = requireDir('./tool/gulp/tasks/');

// grunt sass
tasks.sass.src  = [ './development/**/*.scss', '!node_modules/**' ];
exports.sass    = tasks.sass.callback;

// grunt js-minify
tasks[ 'js-minify' ].src    = [ './development/**/*.js', '!./development/**/*.min.js', '!node_modules/**' ];
exports[ 'js-minify' ]      = tasks[ 'js-minify' ].callback;

// grunt js-concat
tasks[ 'js-concat' ].src    = [ './development/{*,}/', './development/**/{*,}/' ];
exports[ 'js-concat' ]      = tasks[ 'js-concat' ].callback;

// grunt watch
exports.watch   = function( cb ) {
    gulp.watch( tasks.sass.src, tasks.sass.callback );
    gulp.watch( tasks[ 'js-minify' ].src, tasks[ 'js-minify' ].callback );
    // watch([ '**/*.js', '!node_modules/**'], parallel(runLinter));
};

// grunt
exports.default = gulp.series( exports.watch );