const { src, dest } = require( 'gulp' );

// const rename = require( 'gulp-rename' );
const concat = require( 'gulp-concat' );
const using  = require( 'gulp-using' );
const cache  = require( 'gulp-cached' );
const uglify = require( 'gulp-uglify' );
const path   = require( 'path' );
const flatmap = require( 'gulp-flatmap' );

module.exports = class GulpTaskJSConcatMin {
  static src = '';
  static callback( cb ) {

    let _processing = false;
    src( GulpTaskJSConcatMin.src )
      .pipe( flatmap( ( stream, file ) => {
        if ( _processing ) {
          return stream;
        }
        _processing = true;
        let _pathDir  = path.dirname( file.path );
        let _baseName = path.basename( _pathDir, '.bundle' );
        return src( [ _pathDir + '/*.min.js', '!' + _pathDir + '/*.bundle.js' ] )
          .pipe( using() )
          .pipe( cache( 'js-concat-min' ) )
          .pipe( concat( _baseName + '.bundle.min.js' ) )
          .pipe( uglify({
              output: {
                  comments: /^!/
              }
          }))
          .pipe( dest( path.dirname( _pathDir ) ) );
      }));

    cb();
  }
};