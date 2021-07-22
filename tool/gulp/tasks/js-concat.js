const { src, dest } = require( 'gulp' );

// const rename = require( 'gulp-rename' );
const concat = require( 'gulp-concat' );
const using  = require( 'gulp-using' );
const cache  = require( 'gulp-cached' );
const uglify = require( 'gulp-uglify' );
const path   = require( 'path' );


const flatmap = require( 'gulp-flatmap' );

module.exports = class GulpTaskJSConcat {
  static src = '';
  static callback( cb ) {

    // Concatenate JS files
    src( GulpTaskJSConcat.src )
      .pipe( flatmap(function( stream, dir ) {
        let _basename = path.basename( dir.path );
        return src( [ dir.path + '/*.js', '!' + dir.path + '/*.bundle.js', '!' + dir.path + '/*.min.js' ] )
          .pipe( using() )
          .pipe( cache( 'js-concat' ) )
          .pipe( concat( _basename + '.bundle.js' ) )
          .pipe( dest( path.dirname( dir.path ) ) );
    }));

    // Concatenate min JS files
    src( GulpTaskJSConcat.src )
      .pipe( flatmap(function( stream, dir ) {
        let _basename = path.basename( dir.path );
        return src( [ dir.path + '/*.min.js', '!' + dir.path + '/*.bundle.min.js' ] )
          .pipe( cache( 'js-concat-min' ) )
          .pipe( using() )
          .pipe( concat( _basename + '.bundle.min.js' ) )
          .pipe( uglify({
              output: {
                  comments: /^!/
              }
          }))
          .pipe( dest( path.dirname( dir.path ) ) );
    }));

    cb();
  }
};