'use strict';

/* TOO SLOW! */

import gulp from 'gulp';
import sass from 'gulp-sass';
import uglify from 'gulp-uglify';

const staticPath = {
    src: 'public/static/src/',
    dest: 'public/static/dist/'
};

const sassPaths = {
    src: staticPath.src + 'scss/**/*.scss',
    dest: staticPath.dest + 'css/'
};

const jsPaths = {
    src: staticPath.src + 'js/**/*.js',
    dest: staticPath.dest + 'js/'
};

gulp.task('sass', () => {
    return gulp.src(sassPaths.src)
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(gulp.dest(sassPaths.dest));
});

gulp.task('js', () => {
    return gulp.src(jsPaths.src)
        .pipe(uglify())
        .pipe(gulp.dest(jsPaths.dest));
});

gulp.task('default', ['sass', 'js']);

gulp.task('watch', () => {
    gulp.watch(sassPaths.src,['sass']);
    gulp.watch(jsPaths.src + 'js/**/*.js',['js']);
});

//
//gulp.task('styles', () => {
//    return gulp.src(paths.src)
//        .pipe(sourcemaps.init())
//        .pipe(sass.sync().on('error', plugins.sass.logError))
//        .pipe(autoprefixer())
//        .pipe(sourcemaps.write('.'))
//        .pipe(gulp.dest(paths.dest));
//});

//'use strict';
//
//var gulp = require('gulp'),
//    sass = require('gulp-sass'),
//    uglify = require('gulp-uglify'),
//    staticPathSrc = 'public/static/src/',
//    staticPathDist = 'public/static/dist/';
//
//gulp.task('sass', function() {
//    gulp.src(staticPathSrc + 'scss/**/*.scss')
//        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
//        .pipe(gulp.dest(staticPathDist + 'css/'))
//});
//
//gulp.task('js', function() {
//    gulp.src(staticPathSrc + 'js/**/*.js')
//        .pipe(uglify())
//        .pipe(gulp.dest(staticPathDist + 'js/'))
//});
//
//gulp.task('default', ['sass', 'js']);
//
//gulp.task('watch',function() {
//    gulp.watch(staticPathSrc + 'scss/**/*.scss',['sass']);
//    gulp.watch(staticPathSrc + 'js/**/*.js',['js']);
//});