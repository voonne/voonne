/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

var gulp = require('gulp');
var less = require('gulp-less');
var cleanCSS = require('gulp-clean-css');
var autoprefixer = require('autoprefixer');
var livereload = require('gulp-livereload');
var postcss = require('gulp-postcss');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var plumber = require('gulp-plumber');
var sourcemaps = require('gulp-sourcemaps');
var runSequence = require('run-sequence');

gulp.task('watch', function() {
	livereload.listen();
	gulp.watch('./styles/**/*.less', ['styles']);
	gulp.watch('./scripts/admin.js', ['admin-script']);
	gulp.watch('./scripts/sign-in.js', ['sign-in-script']);
});

gulp.task('styles', function () {
	return gulp.src('./styles/*.less')
		.pipe(plumber({
			errorHandler: function (err) {
				console.log(err);
				this.emit('end');
			}
		}))
		.pipe(sourcemaps.init())
		.pipe(less())
		.pipe(cleanCSS())
		.pipe(postcss([autoprefixer()]))
		.pipe(sourcemaps.write('.'))
		.pipe(gulp.dest('./dist/styles'))
		.pipe(livereload());
});

gulp.task('admin-script', function() {
	return gulp.src([
		'./bower_components/jquery/dist/jquery.js',
		'./bower_components/moment/min/moment.min.js',
		'./bower_components/moment/min/locales.min.js',
		'./bower_components/bootstrap/dist/js/bootstrap.js',
		'./bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
		'./scripts/admin.js'])
		.pipe(concat('admin.js'))
		.pipe(gulp.dest('./dist/scripts'))
		.pipe(livereload());
});

gulp.task('sign-in-script', function() {
	return gulp.src([
		'./bower_components/jquery/dist/jquery.js',
		'./scripts/sign-in.js'])
		.pipe(concat('sign-in.js'))
		.pipe(gulp.dest('./dist/scripts'))
		.pipe(livereload());
});

gulp.task('minify-scripts', function() {
	return gulp.src('./dist/scripts/*.js')
		.pipe(uglify())
		.pipe(gulp.dest('./dist/scripts'));
});

gulp.task('build', function(callback) {
	runSequence(
		['admin-script', 'sign-in-script', 'styles'],
		['minify-scripts'],
		callback);
});

gulp.task('copy-fonts', function() {
	return gulp.src([
		'./bower_components/fontawesome/fonts/*.{ttf,woff,woff2,eof,svg}',
		'./bower_components/bootstrap/fonts/*.{ttf,woff,woff2,eof,svg}'])
		.pipe(gulp.dest('./dist/fonts'));
});

gulp.task('default', ['watch'], function() {});
