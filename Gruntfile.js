module.exports = function(grunt) {
    const sass = require('node-sass');
    
    require('load-grunt-tasks')(grunt);
    
    grunt.initConfig({
      pkg: grunt.file.readJSON('package.json'),
      sass: {
        development: {
          options: {
            implementation: sass,
            compress: false, //true after dev
            yuicompress: false, //true after dev
            trace: true,
            sourcemap: true,
            style: 'expanded',
            sourceMap: true,
            sourceMapFilename: 'wp-content/themes/masterstudy-child/assets/sourcemap/styles-child.css.map', // where file is generated and located
            sourceMapURL:      'assets/sourcemap/styles-child.css.map', // the complete url and filename put in the compiled css file
            sourceMapBasepath: 'wp-content/themes/masterstudy-child', // Sets sourcemap base path, defaults to current working directory.
            sourceMapRootpath: '/', // adds this path onto the sourcemap filename and less file paths
          },
          files: {
            "wp-content/themes/masterstudy-child/assets/css/styles.css": "wp-content/themes/masterstudy-child/assets/scss/styles.scss" // destination file and source file
          }
        },
        compressed: {
          options: {
            implementation: sass,
            compress: true,
            yuicompress: true,
            optimization: 3,
          },
          files: {
            "wp-content/themes/masterstudy-child/assets/css/styles.css": "wp-content/themes/masterstudy-child/assets/scss/styles.scss" // destination file and source file
          }
        }
      },
      uglify: {
        compressed: {
            options: {
                banner: '/*! <%= pkg.name %> <%= pkg.version %> javascript.js <%= grunt.template.today("yyyy-mm-dd h:MM:ss TT") %> */\n',
                report: 'gzip',
                beautify: false,
                compress: true,
                mangle: false
            },
            files: {
                'wp-content/themes/masterstudy-child/assets/js/child-custom.js' : [
                  'wp-content/themes/masterstudy-child/assets/js/attached/**/*.js'
                ]
            }
        },
        development: {
            options: {
                banner: '/*! <%= pkg.name %> <%= pkg.version %> javascript.js <%= grunt.template.today("yyyy-mm-dd h:MM:ss TT") %> */\n',
                beautify        : {
                  beautify     : true,
                  indent_level : 4,
                  indent_start : 0,
                  quote_keys   : false,
                  ascii_only   : false,
                  inline_script: false,
                  width        : 80,
                  max_line_len : 32000,
                  semicolons   : true,
                  preamble     : null,
                  quote_style  : 0,
                  // documented here http://lisperator.net/uglifyjs/codegen 
                  // but not documented on GitHub Uglify/UglifyJS2 or grunt-contrib-uglify. 
                },
                output : {
                  comments: 'all'
                },
                sourceMap: true,
                sourceMapName: 'wp-content/themes/masterstudy-child/assets/js/child-custom.js.map',
                compress: {
                  drop_console: false, // <- true - removes console.log 
                  hoist_funs: false,
                  drop_debugger: false
                },
                mangle: false
            },
            files: {
                'wp-content/themes/masterstudy-child/assets/js/child-custom.js' : [
                    'wp-content/themes/masterstudy-child/assets/js/attached/**/*.js'
                ]
            }
        }
      },
      cssmin: {
        css: {
          src: 'wp-content/themes/masterstudy-child/assets/css/styles.css',
          dest: 'wp-content/themes/masterstudy-child/assets/css/styles.css'
        }
      },
      watch: {
        styles: {
          files: ['wp-content/themes/masterstudy-child/**/*.scss'], // which files to watch
          tasks: ['sass:development'],
          options: {
            nospawn: true
          }
        },
        scripts: {
          files: ['wp-content/themes/masterstudy-child/assets/js/attached/**/*.js'], // which files to watch
          tasks: ['uglify:development'],
          options: {
            nospawn: true
          }
        }
      },
    });
  
    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    // grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.registerTask('development', ['sass:development', 'uglify:development']);
    grunt.registerTask('compressed', ['sass:compressed', 'uglify:compressed', 'cssmin']);
    grunt.registerTask('default', ['development', 'watch']);
  };