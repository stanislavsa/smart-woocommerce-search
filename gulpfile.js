var gulp = require('gulp');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');
var pump = require('pump');

gulp.task('default', function (cb) {
	pump([
			gulp.src(['assets/js/*.js', '!assets/js/ysm-admin.js']),
			uglify(),
			concat('main.min.js'),
			gulp.dest('assets/js/min/')
		],
		cb
	);
});
