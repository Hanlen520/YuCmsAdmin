module.exports = function(grunt) {
  grunt.initConfig({
     pkg: grunt.file.readJSON('package.json'),

     	less: {
			options: {
	          compress: false,
	          yuicompress: false
	        },
			development: {
				files: {
					"styles/base-debug.css": "./less/base/base.less",
					"styles/main-debug.css": "./less/main.less"
				}
			}
		},

		// 压缩 CSS 文件
		cssmin: {
			options: {
				report: 'gzip'
			},
			combine: {
				files: {
					"styles/base.css": "./styles/base-debug.css",
					"styles/main.css": "./styles/main-debug.css"
				}
			}
		},

		// grunt-contrib-imagemin，能够快速的压缩工程内的图片
	   // imagemin: {
    //     /* 压缩图片大小 */
	   //      dist: {
	   //          options: {
	   //              optimizationLevel: 3 //定义 PNG 图片优化水平
	   //          },
	   //          files: [{
	   //              expand: true,
	   //              cwd: 'images/',
	   //              src: ['**/*.{png,jpg,jpeg}'], // 优化 img 目录下所有 png/jpg/jpeg 图片
	   //              dest: 'images/' // 优化后的图片保存位置，覆盖旧图片，并且不作提示
	   //          }]
	   //      }
    //     },


		 // uglify: {
   //          options: {
   //              banner: '\n'
   //          },
   //          bulid: {
   //              src: 'java_js/chatprocess.js',
   //              dest: 'java_js/chatprocess.js'
   //          }
   //      },

		// 监控LESS
		watch: {
			//	 options: {
		 	//      spawn: false
		 	//    },
		 	//    scripts: {
		 	//        files: [ '<%= pkg.version %>/**/*.coffee' ],
		 	//        tasks: [ 'coffee']
		 	//    }

			// files：监听哪些文件的修改
			files: ["less/**/*.less"],

			// tasks：文件修改后触发哪些任务
			tasks: ["less", "cssmin"]
		}

  });

 	grunt.loadNpmTasks("grunt-contrib-less");
	grunt.loadNpmTasks("grunt-contrib-cssmin");
	grunt.loadNpmTasks("grunt-contrib-watch");
	// grunt.loadNpmTasks('grunt-contrib-imagemin');
	// grunt.loadNpmTasks("grunt-contrib-uglify");

  	grunt.registerTask("build", ["less", "cssmin", "watch"]);
  	// grunt.registerTask('img', ['imagemin']);
  	// grunt.registerTask("js", [ "uglify" ]);
};