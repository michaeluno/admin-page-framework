const { src, dest } = require( 'gulp' );
const using = require( 'gulp-using' );
const cache  = require( 'gulp-cached' );
const flatmap = require('gulp-flatmap')
const path   = require( 'path' );
const sassTask = require( __dirname + '/sass.js' );

module.exports = class GulpTaskSassUse {
  static src = '';
  static callback( cb ) {
    console.log( 'gulp sass-use called.' );
    let _processing = false;
    src( GulpTaskSassUse.src )
      .pipe( cache( 'css-use' ) )
      // @see https://stackoverflow.com/a/50655862
      .pipe( flatmap( ( stream, file ) => {
        if ( _processing ) {
          return stream;
        }
        _processing = true;
        let _pathDir  = path.dirname( file.path );
        let _baseName = path.basename( file.path, '.scss' );
        let _nameParts = _baseName.replace( /^_/, "" ).split( '-' );
        let _nameFile  = _nameParts[ 0 ] + '.scss';
        sassTask.src = [ _pathDir + '/' + _nameFile ];
        sassTask.callback( cb );
        return stream;
      }));
    cb();
  }
};