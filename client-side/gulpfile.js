var gulp = require('gulp');
var browserify = require('browserify');
var tsify = require('tsify');
var source = require('vinyl-source-stream')
var sass = require('gulp-sass');

gulp.task('ts', function () {
    var bundleStream = browserify('src/ts/inline.ts')
        .plugin(tsify)
        .bundle()
        .on('error', function (error) {
            console.error(error.toString());
        })

    return bundleStream
        .pipe(source('inline.js'))
        .pipe(gulp.dest('dist'))
});

gulp.task('sass', function () {
    return gulp.src('src/sass/inline.scss')
        .pipe(sass())
        .pipe(gulp.dest('dist'));
});

gulp.task('default', ['ts', 'sass']);