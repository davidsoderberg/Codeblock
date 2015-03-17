var gulp = require('gulp');
var vendor = 'public/js/vendor/';
var js = [
	vendor+'jquery.js',
	vendor+'chosen.js',
	vendor+'codemirror.js',
	vendor+'validator.js',
	vendor+'bootstrap-typeahead.js',
	vendor+'mention.js',
	'public/js/script.js'
];

gulp.task('default', function() {
	gulp.watch(['public/scss/style.scss', 'public/scss/partials/**/*.scss'], ['sass']);
	gulp.watch(js, ['js']);
});

gulp.task('deploy', ['js'], function(){
	var sass = require('gulp-sass');
	return gulp.src(['public/scss/style.scss', 'public/scss/partials/**/*.scss'])
		.pipe(sass({outputStyle: 'compressed'})).pipe(gulp.dest('public/css'));
})

gulp.task('sass', function () {
	var sass = require('gulp-sass');
	var sourcemaps = require('gulp-sourcemaps');
	return gulp.src(['public/scss/style.scss', 'public/scss/partials/**/*.scss'])
		.pipe(sourcemaps.init())
		.pipe(sass({outputStyle: 'compressed'}))
		.pipe(sourcemaps.write('./'))
		.pipe(gulp.dest('public/css'));
});

gulp.task('js', function () {
	var uglify = require('gulp-uglifyjs');
	var rename = require("gulp-rename");
	return gulp.src(js)
		.pipe(uglify({mangle: false}))
		.pipe(rename('script.min.js'))
		.pipe(gulp.dest('public/js'));
});