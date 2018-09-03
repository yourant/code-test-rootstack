//Gruntfile
module.exports = function(grunt) {

    //Initializing the configuration object
    grunt.initConfig({
        // Task configuration
        less: {
            inspinia: {
                options: {
                    compress: true  //minifying the result
                },
                files: {
                    "./public/assets/stylesheets/theme.css": [
                        "./resources/assets/packages/inspinia/less/style.less",
                        "./resources/assets/less/inspinia.theme.less"
                    ]
                }
            },
            application: {
                options: {
                    compress: true
                },
                files: {
                    "./public/assets/stylesheets/application.css": [
                        "./resources/assets/less/modules/*.less"
                    ],
                    "./public/assets/stylesheets/front.css": [
                        "./resources/assets/less/front/**/*.less"
                    ]
                }
            }
        },
        cssmin: {
            combine: {
                files: {
                    './public/assets/stylesheets/base.css': [
                        './bower_components/bootstrap/dist/css/bootstrap.css',
                        './bower_components/font-awesome/css/font-awesome.css',
                        './resources/assets/packages/inspinia/css/animate.css',
                        './bower_components/bootstrap-select/dist/css/bootstrap-select.css',
                        './bower_components/bootstrap-datepicker/css/datepicker3.css',
                        './bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
                        './bower_components/select2/dist/css/select2.css',
                        './bower_components/handsontable/dist/handsontable.full.css',
                        './resources/assets/packages/formvalidation/dist/css/formValidation.css',
                        './bower_components/pace/themes/blue/pace-theme-flat-top.css'
                    ]
                }
            },
            minify: {
                expand: true,
                cwd: './public/assets/stylesheets/',
                src: ['*.css', '!*.min.css'],
                dest: './public/assets/stylesheets/',
                ext: '.min.css'
            }
        },
        concat: {
            options: {
                separator: ';'
            },
            js_top: {
                src: ['./bower_components/pace/pace.js', './bower_components/jquery/dist/jquery.js'],
                dest: './public/assets/javascript/admin-top.js'
            },
            js_front_top: {
                src: ['./bower_components/jquery/dist/jquery.js'],
                dest: './public/assets/javascript/front-top.js'
            },
            js_front_bottom: {
                src: [
                    './bower_components/bootstrap/dist/js/bootstrap.min.js',
                    './bower_components/metisMenu/dist/metisMenu.min.js',
                    './bower_components/jquery-slimscroll/jquery.slimscroll.js',
                    './bower_components/moment/min/moment.min.js',
                    './resources/assets/packages/inspinia/js/inspinia-front.js',
                    './resources/assets/javascript/shared/**/*.js',
                ],
                dest: './public/assets/javascript/front-bottom.js'
            },
            js_admin_bottom: {
                src: [
                    './bower_components/bootstrap/dist/js/bootstrap.min.js',
                    './bower_components/metisMenu/dist/metisMenu.min.js',
                    './bower_components/jquery-color/jquery.color.js',
                    './bower_components/jquery-slimscroll/jquery.slimscroll.js',
                    './bower_components/bootstrap-select/dist/js/bootstrap-select.js',
                    './bower_components/bootstrap-datepicker/js/bootstrap-datepicker.js',
                    './bower_components/moment/min/moment.min.js',
                    './bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
                    './bower_components/select2/dist/js/select2.js',
                    './bower_components/handsontable/dist/handsontable.full.js',
                    './bower_components/bootbox/bootbox.js',
                    './bower_components/spin.js/spin.js',
                    './bower_components/spin.js/jquery.spin.js',
                    './resources/assets/packages/formvalidation/dist/js/formValidation.js',
                    './resources/assets/packages/formvalidation/dist/js/framework/bootstrap.js',
                    './resources/assets/packages/inspinia/js/plugins/sparkline/jquery.sparkline.min.js',
                    './resources/assets/packages/inspinia/js/inspinia.js',
                    './bower_components/Chart.js/dist/Chart.min.js',
                    './resources/assets/javascript/shared/**/*.js',
                    './resources/assets/javascript/modules/**/*.js'
                ],
                dest: './public/assets/javascript/admin-bottom.js'
            }
        },
        uglify: {
            options: {
                mangle: false  // Use if you want the names of your functions and variables unchanged
            },
            frontend: {
                files: {
                    './public/assets/javascript/admin-top.min.js': './public/assets/javascript/admin-top.js',
                    './public/assets/javascript/admin-bottom.min.js': './public/assets/javascript/admin-bottom.js',
                    './public/assets/javascript/front-top.min.js': './public/assets/javascript/front-top.js',
                    './public/assets/javascript/front-bottom.min.js': './public/assets/javascript/front-bottom.js',
                }
            }
        },
        copy: {
            font_awesome: {
                files: [{
                    // includes files within path
                    expand: true,
                    flatten: true,
                    src: [
                        './bower_components/font-awesome/fonts/*webfont*',
                        './bower_components/bootstrap/dist/fonts/*',
                        './resources/assets/fonts/*'
                    ],
                    dest: './public/assets/fonts/',
                    filter: 'isFile'
                }]
            }
        },
        watch: {
            less: {
                files: ['./resources/assets/**/*.less'],
                tasks: ['less','cssmin'],
                options: { livereload: true }
            },
            css: {
                files: ['./resources/assets/**/*.css'],
                tasks: ['cssmin'],
                options: { livereload: true }
            },
            js: {
                files: ['./resources/assets/**/*.js'],
                tasks: ['concat'],
                options: { livereload: true }
            }
        }
    });

    // Plugin loading
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-watch');

    // Task definition
    grunt.registerTask('default', ['less','concat', 'uglify', 'cssmin','copy']);
};