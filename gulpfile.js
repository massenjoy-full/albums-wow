const gulp = require('gulp');
const uglify = require('gulp-uglify');
const cleanCSS = require('gulp-clean-css');
const concat = require('gulp-concat');

// Minify Filters JS.
gulp.task('scripts', function () {
	return gulp.src('assets/js/filters.js')
		.pipe(concat('filters.min.js'))
		.pipe(uglify())
		.on('error', function (error) {
			console.error('Error in scripts task', error.toString());
		})
		.pipe(gulp.dest('assets/js'));
});

// Minify Filters CSS.
gulp.task('style-filters', function () {
	return gulp.src('assets/css/filters.css')
		.pipe(concat('filters.min.css'))
		.pipe(cleanCSS())
		.on('error', function (error) {
			console.error('Error in style-filters task', error.toString());
		})
		.pipe(gulp.dest('assets/css'));
});

// Minify Styles CSS.
gulp.task('styles', function () {
	return gulp.src('assets/css/style.css')
		.pipe(concat('style.min.css'))
		.pipe(cleanCSS())
		.on('error', function (error) {
			console.error('Error in styles task', error.toString());
		})
		.pipe(gulp.dest('assets/css'));
});

// Default Task.
gulp.task('default', gulp.parallel('scripts', 'style-filters', 'styles'));
