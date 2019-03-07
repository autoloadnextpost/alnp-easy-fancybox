module.exports = function(grunt) {
	'use strict';

	require('load-grunt-tasks')(grunt);

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		// Minify JavaScript
		uglify: {
			options: {
				compress: {
					global_defs: {
						"EO_SCRIPT_DEBUG": false
					},
					dead_code: true
				},
				banner: '/*! <%= pkg.title %> <%= pkg.version %> <%= grunt.template.today("yyyy-mm-dd HH:MM") %> */\n'
			},
			build: {
				files: [{
					expand: true, // Enable dynamic expansion.
					src: [
						'assets/js/*.js',
						'!assets/js/*.min.js',
					],
					ext: '.min.js', // Dest filepaths will have this extension.
				}]
			}
		},

		// Check for Javascript errors
		jshint: {
			options: {
				reporter: require('jshint-stylish'),
				globals: {
					"EO_SCRIPT_DEBUG": false,
				},
				'-W099': true, // Mixed spaces and tabs
				'-W083': true, // Fix functions within loop
				'-W082': true, // Declarations should not be placed in blocks
				'-W020': true, // Read only - error when assigning EO_SCRIPT_DEBUG a value.
			},
			all: [
				'assets/js/*.js',
				'!assets/js/*.min.js',
			]
		},

		// Generate .pot file
		makepot: {
			target: {
				options: {
					cwd: '',
					domainPath: 'languages',                                  // Where to save the POT file.
					exclude: [
						'releases',
						'node_modules',
					],
					mainFile: '<%= pkg.name %>.php',                          // Main project file.
					potComments: '# Copyright (c) {{year}} Sébastien Dumont', // The copyright at the beginning of the POT file.
					potFilename: '<%= pkg.name %>.pot',                       // Name of the POT file.
					potHeaders: {
						'poedit': true,                                       // Includes common Poedit headers.
						'x-poedit-keywordslist': true,                        // Include a list of all possible gettext functions.
						'Report-Msgid-Bugs-To': 'https://github.com/autoloadnextpost/<%=pkg.name %>/issues',
						'language-team': 'Sébastien Dumont <mailme@sebastiendumont.com>',
						'language': 'en_US'
					},
					type: 'wp-plugin',                                        // Type of project.
					updateTimestamp: true,                                    // Whether the POT-Creation-Date should be updated without other changes.
				}
			}
		},

		// Check strings for localization issues
		checktextdomain: {
			options:{
				text_domain: '<%= pkg.name %>', // Project text domain.
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				]
			},
			files: {
				src:  [
					'*.php',
					'**/*.php', // Include all files
					'!node_modules/**' // Exclude node_modules/
				],
				expand: true // Enable dynamic expansion.
			},
		},

		potomo: {
			dist: {
				options: {
					poDel: false
				},
				files: [{
					expand: true, // Enable dynamic expansion.
					cwd: 'languages',
					src: ['*.po'],
					dest: 'languages',
					ext: '.mo',
					nonull: false
				}]
			}
		},

		// Bump version numbers (replace with version in package.json)
		replace: {
			Version: {
				src: [
					'*.md',
					'*.txt',
					'assets/js/*.js',
					'<%= pkg.name %>.php',
					'includes/admin/*.php',
					'includes/admin/views/*.php'
				],
				overwrite: true,
				replacements: [
					{
						from: /Stable tag:.*$/m,
						to: "Stable tag: <%= pkg.version %>"
					},
					{
						from: /Version:.*$/m,
						to: "Version: <%= pkg.version %>"
					},
					{
						from: /public static \$version = \'.*.'/m,
						to: "public static $version = '<%= pkg.version %>'"
					},
					{
						from: /public static \$required_alnp = \'.*.'/m,
						to: "public static $required_alnp = '<%= pkg.required_alnp %>'"
					},
					{
						from: '@@textdomain',
						to: "<%= pkg.name %>"
					},
					{
						from: '@@title',
						to: "<%= pkg.title %>"
					},
					{
						from: '@@name',
						to: "<%= pkg.name %>"
					},
					{
						from: '@@description',
						to: "<%= pkg.description %>"
					},
					{
						from: 'ALNP_ADDON',
						to: "<%= pkg.constant %>"
					},
					{
						from: 'ALNP_Addon_Name',
						to: '<%= pkg.class_name %>'
					},
					{
						from: 'alnp_addon_name',
						to: '<%= pkg.localize_script %>'
					}
				]
			}
		},

		rename: {
			main: {
				files: [
					{
						src: [
							'alnp-addon-boilerplate.php',
						],
						dest: '<%= pkg.name %>.php',
					},
					{
						src: [
							'assets/js/alnp-addon-boilerplate.js',
						],
						dest: 'assets/js/<%= pkg.name %>.js',
					},
					{
						src: [
							'includes/admin/alnp-addon-boilerplate-admin.php',
						],
						dest: 'includes/admin/<%= pkg.name %>-admin.php',
					},
					{
						src: [
							'includes/admin/class-alnp-addon-boilerplate-check.php',
						],
						dest: 'includes/admin/class-<%= pkg.name %>-check.php',
					},
					{
						src: [
							'includes/admin/class-alnp-addon-boilerplate-feedback.php'
						],
						dest: 'includes/admin/class-<%= pkg.name %>-feedback.php',
					},
				]
			}
		},

		  // Copies the plugin to create deployable plugin.
		copy: {
			deploy: {
				src: [
					'**',
					'!.*',
					'!*.md',
					'!.*/**',
					'.htaccess',
					'!Gruntfile.js',
					'!package.json',
					'!package-lock.json',
					'!releases/**',
					'!node_modules/**',
					'!.DS_Store',
					'!npm-debug.log',
					'!*.sh',
					'!*.zip',
					'!*.jpg',
					'!*.jpeg',
					'!*.gif',
					'!*.png'
				],
				dest: '<%= pkg.name %>',
				expand: true, // Enable dynamic expansion.
				dot: true
			}
		},

		// Compresses the deployable plugin folder.
		compress: {
			zip: {
				options: {
					archive: './releases/<%= pkg.name %>-v<%= pkg.version %>.zip',
					mode: 'zip'
				},
				files: [
					{
						expand: true, // Enable dynamic expansion.
						cwd: './<%= pkg.name %>/',
						src: '**',
						dest: '<%= pkg.name %>'
					}
				]
			}
		},

		// Deletes the deployable plugin folder once zipped up.
		clean: [ '<%= pkg.name %>' ]
	});

	// Set the default grunt command to run test cases.
	grunt.registerTask( 'default', [ 'test' ] );

	// Prepares the boilerplate for developing a new add-on.
	grunt.registerTask( 'prepare', [ 'rename' ] );

	// Checks for errors with the javascript and text domain.
	grunt.registerTask( 'test', [ 'jshint', 'checktextdomain' ]);

	// Updates version and minify javascript and finaly runs i18n tasks.
	grunt.registerTask( 'dev', [ 'replace', 'newer:uglify', 'makepot' ]);

	/**
	 * Run i18n related tasks.
	 *
	 * This includes extracting translatable strings, updating the master pot file.
	 * If this is part of a deploy process, it should come before zipping everything up.
	 */
	grunt.registerTask( 'update-pot', [ 'checktextdomain', 'makepot' ]);

	/**
	 * Creates a deployable plugin zipped up ready to upload
	 * and install on a WordPress installation.
	 */
	grunt.registerTask( 'zip', [ 'copy', 'compress', 'clean' ]);
};
