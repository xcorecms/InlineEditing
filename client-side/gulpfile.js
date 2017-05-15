var gulp = require('gulp');
var less = require('gulp-less');
var ts = require('gulp-typescript');
var tsProject = ts.createProject('src/ts/tsconfig.json');

gulp.task('ts', function () {
    return tsProject.src()
        .pipe(tsProject())
        .js.pipe(gulp.dest('dist'));
});

gulp.task('less', function () {
    return gulp.src('src/less/inline.less')
        .pipe(less())
        .pipe(gulp.dest('dist'));
});

gulp.task('default', ['ts', 'less']);