var gulp = require('gulp');
var components = 'public/js/components/';
var js = [
	'bower_components/jquery-legacy/jquery.js',
	'bower_components/chosen/chosen.jquery.js',
	'bower_components/codemirror/lib/codemirror.js',
	'bower_components/mention/bootstrap-typeahead.js',
	'bower_components/mention/mention.js',
	components+'validator.js',
	components+'accordion.js',
	components+'tabs.js',
	components+'page.js',
	components+'post.js',
	components+'async.js',
	'public/js/script.js'
];

gulp.task('default', ['browser-sync', 'copy'], function() {
	gulp.watch(['public/scss/style.scss', 'public/scss/partials/**/*.scss'], ['sass', 'reload']);
	gulp.watch(js, ['js', 'reload']);
	gulp.watch('resources/views/**/*', ['reload']);
	gulp.watch('resources/themes/**/*', ['sami']);
});

gulp.task('copy', function() {
	gulp.src(['bower_components/font-awesome/fonts/*'])
		.pipe(gulp.dest('public/fonts/'));
	gulp.src(['bower_components/chosen/chosen-sprite.png'])
		.pipe(gulp.dest('public/css/'));
});

gulp.task('deploy', ['js'], function(){
	var sass = require('gulp-sass');
	return gulp.src(['public/scss/style.scss', 'public/scss/partials/**/*.scss'])
		.pipe(sass({outputStyle: 'compressed'})).pipe(gulp.dest('public/css'));
})

gulp.task('sass', ['copy'], function () {
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

var browserSync = require('browser-sync');
gulp.task('reload', function(){
	browserSync.reload();
});

gulp.task('browser-sync', function() {
	browserSync({
		proxy: "codeblock.dev"
	});
});

gulp.task('sami', function(){
	return gulp.src(['resources/themes/codeblock/style.css']).pipe(gulp.dest('storage/doc/build/css'));
});