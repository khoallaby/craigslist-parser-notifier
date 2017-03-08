
var gulp         = require('gulp');
//var bower        = require('gulp-bower');
var sass         = require('gulp-sass');
var browserSync  = require('browser-sync');
var prefix       = require('gulp-autoprefixer');
var plumber      = require('gulp-plumber');
var uglify       = require('gulp-uglify');
var rename       = require("gulp-rename");
var imagemin     = require("gulp-imagemin");
var pngquant     = require('imagemin-pngquant');


var srcDir       = './src',
    publicDir    = './public',
    vendorDir    = './vendor';


var config = {
    jsDir: srcDir + '/js',
    sassDir: srcDir + '/sass',
    bowerDir: vendorDir + '/bower_components'
};


/**
 jsDir: './src/js',
 *
 * Basically runs bower install
 * - execute via 'gulp bower'
 *
 **/
/*
gulp.task('bower', function() {
    return bower()
        .pipe(gulp.dest(config.bowerDir))
});
*/


/**
 *
 * Styles
 * - Compile
 * - Compress/Minify
 * - Catch errors (gulp-plumber)
 * - Autoprefixer
 *
 **/


gulp.task('sass', function() {
    gulp.src( config.sassDir + '/**/*.scss')
        .pipe(sass({outputStyle: 'compressed'}))
        .pipe(prefix('last 2 versions', '> 1%', 'ie 8', 'Android 2', 'Firefox ESR'))
        .pipe(plumber())
        .pipe(gulp.dest('css'));
});






/**
 *
 * BrowserSync.io
 * - Watch CSS, JS & HTML for changes
 * - View project at: localhost:3000
 *
 **/
gulp.task('browser-sync', function() {
    browserSync.init([publicDir + '/css/*.css', publicDir + '/js/**/*.js', 'index.html'], {
        server: {
            baseDir: './'
        }
    });
});


/**
 *
 * Javascript
 * - Uglify
 *
 **/
gulp.task('scripts', function() {
    gulp.src(publicDir + 'js/*.js')
        .pipe(uglify())
        .pipe(rename({
            dirname: "min",
            suffix: ".min",
        }))
        .pipe(gulp.dest('js'))
});

/**
 *
 * Images
 * - Compress them!
 *
 **/
gulp.task('images', function () {
    return gulp.src('images/*')
        .pipe(imagemin({
            progressive: true,
            svgoPlugins: [{removeViewBox: false}],
            use: [pngquant()]
        }))
        .pipe(gulp.dest('images'));
});


/**
 *
 * Default task
 * - Runs sass, browser-sync, scripts and image tasks
 * - Watchs for file changes for images, scripts and sass/css
 *
 **/
gulp.task('default', ['sass', 'browser-sync', 'scripts', 'images']);

gulp.task('watch', ['sass', 'browser-sync', 'scripts', 'images'], function () {
    gulp.watch(config.sassDir + '/**/*.scss', ['sass']);
    gulp.watch(config.jsDir + 'js/**/*.js', ['scripts']);
    gulp.watch('images/*', ['images']);
});