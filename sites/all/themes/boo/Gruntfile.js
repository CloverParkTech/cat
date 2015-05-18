module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        sass: {
            dist: {
                options: {
                    style: 'compressed'
                },
                files: {
                    'style.css': 'sass/style.scss'
                }
            }
        },


        concat: {
            global: {
                src: [
                    'js/**/*.js',
                ],
                dest: 'js/global.js',
            },



        },


        uglify: {
            dist: {
                files: {
                    'js/global.min.js': 'js/global.js',

                }
            }
        },

        autoprefixer: {
            options: {
                browsers: ['last 2 versions', 'ie 9']
            },
            your_target: {
                src: 'style.css'
            },
        },

        watch: {
            css: {
                files: ['sass/**/*.scss', 'sass/**/*.scss'],
                tasks: ['sass:dist'],
                options: {

                },
            },

            autoprefixer: {
                files: ['style.css'],
                tasks: ['autoprefixer']
            },


            scripts: {
                files: ['js/**/*.js'],
                tasks: ['concat', 'uglify'],
                options: {
                    spawn: false,
                    livereload: true,
                },
            }

        },


    });

    // 3. Where we tell Grunt we plan to use this plug-in.

    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-autoprefixer');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');


    // 4. Where we tell Grunt what to do when we type "grunt" into the terminal.
    grunt.registerTask('default', ['sass', 'autoprefixer', 'concat', 'uglify', 'watch']);

};