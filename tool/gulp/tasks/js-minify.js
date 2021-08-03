const { src, dest } = require( 'gulp' );

const rename = require( 'gulp-rename' );
const uglify = require( 'gulp-uglify' );
const using  = require( 'gulp-using' );
const cache  = require( 'gulp-cached' );

module.exports = class GulpTaskJS {
  static src = '';
  static callback( cb ) {
    console.log( 'js-minify src:', GulpTaskJS.src );
    src( GulpTaskJS.src )
      .pipe( cache('js-minify' ) )
      .pipe( using() )
      .pipe( uglify({
          output: {
              comments: /^!/
          }
      }))
      .pipe( rename( { extname: '.min.js' } ) )
      .pipe( dest( function( file ) {
          return file.base;
      } ) );

    cb();
  }
};
