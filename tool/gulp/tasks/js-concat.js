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

    let _processing = false;
    src( GulpTaskJSConcat.src )
      // @see https://stackoverflow.com/a/50655862
      .pipe( flatmap( ( stream, file ) => {
        if ( _processing ) {
          return stream;
        }
        _processing = true;
        let _pathDir  = path.dirname( file.path );
        let _baseName = path.basename( _pathDir, '.bundle' );
        return src( [ _pathDir + '/*.js', '!' + _pathDir + '/*.min.js', '!' + _pathDir + '/*.bundle.js' ] )
          .pipe( using() )
          .pipe( concat( _baseName + '.bundle.js' ) )
          .pipe( dest( path.dirname( _pathDir ) ) );
      }));

    cb();
  }
};