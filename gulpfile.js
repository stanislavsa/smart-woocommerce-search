'use strict';

global.$ = {
	gulp: require('gulp'),
	sass: require('gulp-sass')(require('sass')),
	concat: require('gulp-concat'),
	uglify: require('gulp-uglify'),
	del: require('del'),
	distPath: './assets/dist'
};

$.gulp.task('sass', function() {
	return $.gulp.src( './assets/src/scss/**/*.scss' )
		.pipe( $.sass( {outputStyle: 'compressed'} ).on('error', $.sass.logError ) )
		.pipe($.gulp.dest($.distPath + '/css'));
});

$.gulp.task('css:select2', function() {
	return $.gulp.src( './inc/app/assets/src/scss/**/select2.min.css' )
		.pipe($.gulp.dest($.distPath + '/css'));
});

$.gulp.task('js:front', function() {
	return $.gulp.src( [ './inc/app/assets/src/js/**/*.js', './assets/src/js/**/*.js', '!./inc/app/assets/src/js/**/admin.js', '!./inc/app/assets/src/js/**/select2.min.js', '!./assets/src/js/**/admin.js' ] )
		.pipe($.concat('main.js'))
		.pipe($.uglify())
		.pipe($.gulp.dest($.distPath + '/js'));
});

$.gulp.task('js:admin', function() {
	return $.gulp.src( ['./inc/app/assets/src/js/**/admin.js', './assets/src/js/**/admin.js'] )
		.pipe($.concat('admin.js'))
		.pipe($.gulp.dest($.distPath + '/js'));
});

$.gulp.task('js:select2', function() {
	return $.gulp.src( ['./inc/app/assets/src/js/**/select2.min.js', './assets/src/js/**/select2.min.js'] )
		.pipe($.gulp.dest($.distPath + '/js'));
});

$.gulp.task('clean', function() {
	return $.del([$.distPath]);
});

$.gulp.task('js:clean', function() {
	return $.del([$.distPath + '/js']);
});

$.gulp.task('watch', function() {
	$.gulp.watch(['./inc/app/assets/src/js/**/*.js', './assets/src/js/**/*.js'], $.gulp.series('js:front'));
	$.gulp.watch(['./inc/app/assets/src/js/**/*.js', './assets/src/js/**/*.js'], $.gulp.series('js:admin'));
	$.gulp.watch(['./inc/app/assets/src/scss/**/*.scss', './assets/src/scss/**/*.scss'], $.gulp.series('sass'));
});

// Default task
$.gulp.task('js:process', $.gulp.series(
	'js:clean',
	$.gulp.parallel(
		'js:front',
		'js:admin',
		'js:select2',
		'css:select2'
	)
));

// Default task
$.gulp.task('default', $.gulp.series(
	'clean',
	$.gulp.parallel(
		'sass',
		'js:front',
		'js:admin',
		'js:select2',
		'css:select2'
	)
));