var gulp = require('gulp');
var scss = ['public/scss/style.scss', 'public/scss/partials/**/*.scss'];

gulp.task('default', ['scss', 'js', 'browser-sync', 'copy'], function () {
	gulp.watch(scss, ['scss', 'reload']);
	gulp.watch(['public/js/components/*.js', 'public/js/script.js'], ['js', 'reload']);
	gulp.watch('resources/views/**/*', ['reload']);
});

gulp.task('copy', function () {
	gulp.src(['bower_components/font-awesome/fonts/*'])
		.pipe(gulp.dest('public/fonts/'));
	gulp.src(['bower_components/chosen/chosen-sprite.png'])
		.pipe(gulp.dest('public/css/'));
	gulp.src(['bower_components/codemirror/addon/**/*'])
		.pipe(gulp.dest('public/js/codemirror/addon/'))
	gulp.src(['bower_components/codemirror/keymap/**/*'])
		.pipe(gulp.dest('public/js/codemirror/keymap/'))
	gulp.src(['bower_components/codemirror/mode/**/*'])
		.pipe(gulp.dest('bower_components/codemirror/mode/'));
});

gulp.task('deploy', ['js'], function () {
	var sass = require('gulp-sass');
	return gulp.src(scss)
		.pipe(sass({outputStyle: 'compressed'})).pipe(gulp.dest('public/css'));
})

gulp.task('scss', ['copy'], function () {
	var sass = require('gulp-sass');
	var sourcemaps = require('gulp-sourcemaps');
	return gulp.src(scss)
		.pipe(sourcemaps.init())
		.pipe(sass({outputStyle: 'compressed'}))
		.pipe(sourcemaps.write('./'))
		.pipe(gulp.dest('public/css'));
});

gulp.task('js', function () {
	var source = require('vinyl-source-stream');
	var browserify = require('browserify');
	var uglify = require('gulp-uglify');
	var buffer = require('vinyl-buffer');
	var babelify = require('babelify');
	return browserify('public/js/script.js')
		.transform(babelify, {presets: ["es2015"], ignore: '/bower_components/'})
		.bundle()
		.pipe(source('script.min.js'))
		.pipe(buffer())
		.pipe(uglify())
		.pipe(gulp.dest('public/js'));
});

var browserSync = require('browser-sync');
gulp.task('reload', function () {
	browserSync.reload();
});

gulp.task('browser-sync', function () {
	browserSync({
		proxy: "codeblock.dev"
	});
});

gulp.task('lint', function () {
	var jshint = require('gulp-jshint');
	var jscs = require('gulp-jscs');

	return gulp.src(['public/js/components/*.js'])
		.pipe(jshint('.jshintrc'))
		.pipe(jscs())
		.pipe(jscs.reporter())
		.pipe(jshint.reporter('jshint-stylish'));
});