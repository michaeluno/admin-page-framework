const fs = require('fs');

module.exports = function(grunt) {

  const sass = require('dart-sass');
  require('load-grunt-tasks')(grunt);

  // Project configuration
  grunt.initConfig({

    // optionally read package.json
    pkg: grunt.file.readJSON('package.json'),

    // Metadata
    meta: {
      pathSrc: 'development',
    },

    // info banner
    banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
            '<%= grunt.template.today("yyyy-mm-dd") %>\n' +
            ' * Copyright (c) <%= grunt.template.today("yyyy") %> */',

    usebanner: {
      minifiedHeader: {
        options: {
          position: 'top',
          // banner: '<%= banner %>',
          linebreak: false,
          replace: function( fileContents, newBanner, insertPositionMarker, src, options ) {
            console.log( 'Banner Replacement Callback called.', insertPositionMarker );
            // This task is for minified files.
            // So extract the banner (header comment) from the unminified file.

            /// Get the original (unminified) file path.
            let _pathOriginal = src.replace( /\.min(\.(js|css))$/i, '$1' );
            if ( ! fs.existsSync( _pathOriginal ) ) {
              console.log( 'Not inserting a banner. The unminified file for ' + src + ' does not exist:', _pathOriginal );
              return fileContents.trim();
            }

            // A regex pattern that matches a first comment enclosed in /* */.
            const _regex = /\/(\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+)\//;

            let _textOriginal = grunt.file.read( _pathOriginal );
            let _match = _regex.exec( _textOriginal );
            if ( ! _match ) {
              console.log( 'Non-found match: ', _match );
              console.log( 'Original file path: ', _pathOriginal );
              console.log( 'Original file text: ', _textOriginal );
            }
            if ( _match && _match.hasOwnProperty( 1 ) && 'undefined' === typeof _match[ 1 ] ) {
              console.log( 'No banner detected: ', src );
              return fileContents.trim();
            }
            let _bannerOriginal = _match[ 1 ];
            _bannerOriginal = _bannerOriginal.replace( /^(\s+)?(\*+)?\s+/gm, '' ); // remove the starting ' *' in each line
            _bannerOriginal = _bannerOriginal.replace( /\r?\n|\r/g, ' ' );     // remove line breaks
            _bannerOriginal = _bannerOriginal.trim();
            _bannerOriginal = _bannerOriginal.replace( /(^(\s+)?(\*+)?(\s+)?)|(\*+?[\s\S]+?$)/, '' );          // remove the ending *

            console.log( 'SRC', src );
            console.log( 'New banner: ', _bannerOriginal );
            return '/* ' + _bannerOriginal + '*/\n' + fileContents.replace( _regex, '' ).trim();

          },
        },
        files: [{
          expand: true,
          cwd: './',
          src: [
            '<%= meta.pathSrc %>/**/*.min.js',
            '<%= meta.pathSrc %>/**/*.min.css',
          ],
          dest: './',
          ext: '.min.js',
        }]
      }
    },

    uglify: {
      minify: {
        files: [{
          expand: true,
          cwd: './',
          src: [ '<%= meta.pathSrc %>/**/*.js' ],
          dest: './',
          ext: '.min.js',
        }]
      }
    },

    sass: {
      options: {
        implementation: sass,
        sourceMap: false,
      },
      compile: {
        files: [
          {
            expand: true,
            cwd: './',
            src: [ '<%= meta.pathSrc %>/**/*.scss' ],
            dest: './',
            ext: '.css'
          },
        ]
      },
      minify: {
        options: {
          outputStyle: 'compressed',
        },
        files: [{
          expand: true,
          cwd: './',
          src: [ '<%= meta.pathSrc %>/**/*.scss' ],
          dest: './',
          ext: '.min.css'
        }]
      },
    },

    // watch all .scss files under the srcPath
    watch: {
      sass: {
        options: {
          spawn: false,
        },
        files: [
          '<%= meta.pathSrc %>/**/*.scss',
          // '!<%= meta.pathSrc %>/**/_*.scss'
        ],
        tasks: [ 'sass:compile', 'sass:minify', ]
      },
      uglify: {
        options: {
          spawn: false,
        },
        files: [
          '<%= meta.pathSrc %>/**/*.js',
          '!<%= meta.pathSrc %>/**/*.min.js',
        ],
        tasks: [ 'uglify:minify', ]
      },
      usebanner: {
        options: {
          spawn: false,
        },
        files: [
          '<%= meta.pathSrc %>/**/*.min.css',
          '<%= meta.pathSrc %>/**/*.min.js',
        ],
        tasks: [ 'usebanner:minifiedHeader', ]
      }
    },

    concurrent: {
      tasks: [ 'watch:sass', 'watch:uglify', 'watch:usebanner' ],
      options: {
          logConcurrentOutput: true
      }
    }

  });

  // Load plugin tasks
  grunt.loadNpmTasks('grunt-sass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-banner');
  grunt.loadNpmTasks('grunt-concurrent');

  // Default task
  grunt.registerTask( 'default', [ 'concurrent:tasks' ] );

  // On watch events configure sass to only run on changed file
  // @see https://github.com/gruntjs/grunt-contrib-watch#compiling-files-as-needed
  grunt.event.on( 'watch', function( action, filepath ) {
    console.log( 'action: ', action, ', path:', filepath );
    grunt.config( 'sass.compile.files.0.src', filepath );
    grunt.config( 'sass.minify.files.0.src', filepath );
    grunt.config( 'uglify.minify.files.0.src', filepath );
    grunt.config( 'usebanner.minifiedHeader.files.0.src', filepath );
  });

};