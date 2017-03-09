var gulp         = require('gulp'),
//var bower        = require('gulp-bower'),
    sass         = require('gulp-sass'),
    browserSync  = require('browser-sync'),
    prefix       = require('gulp-autoprefixer'),
    plumber      = require('gulp-plumber'),
    uglify       = require('gulp-uglify'),
    rename       = require("gulp-rename"),
    imagemin     = require("gulp-imagemin"),
    pngquant     = require('imagemin-pngquant'),
    gutil        = require('gulp-util');


var srcDir       = 'src/',
    publicDir    = 'public/',
    vendorDir    = 'vendor/';
var bowerDir     = vendorDir + 'bower_components/';


var config = {
    js: {
        src: srcDir + 'js',
        pub: publicDir + 'js'
    },
    scss : {
        src: srcDir + 'scss',
        styleCss: srcDir + 'scss/style.scss',
        pub: publicDir + 'css',
        sassOpts: {
            outputStyle: 'nested', // todo: compressed for prod
            precison: 3,
            errLogToConsole: true,
            includePaths: [ bowerDir + 'bootstrap/scss']
        }
    }
};


var onError = function (error) {
    gutil.beep();
    gutil.log(gutil.colors.red('Error (' + error.plugin + '): ' + error.message));
    this.emit('end');
};


/**
 * Run bower install - execute via 'gulp bower'
 **/
/*
gulp.task('bower', function() {
    return bower()
        .pipe(gulp.dest(config.bowerDir))
});
*/


/**
 * Sass - minify, autoprefix
 **/
gulp.task('sass', function() {
    return gulp.src([
            config.scss.src + '/**/*.scss'
            //!config.scss.src + 'bootstrap/*.scss' // ignores contents of src/bootstrap dir
        ])
        .pipe(plumber({ errorHandler: onError }))
        .pipe(sass(config.scss.sassOpts))
        .pipe(prefix('last 2 versions', '> 1%', 'ie 8', 'Android 2', 'Firefox ESR'))
        .pipe(gulp.dest(config.scss.pub));
});



/**
 * BrowserSync.io - localhost:3000
 **/
gulp.task('browser-sync', function() {
    browserSync.init([publicDir + 'css/*.css', publicDir + 'js/**/*.js', 'index.html'], {
        server: {
            baseDir: publicDir
        }
    });
});


/**
 * JS - Uglify
 **/
gulp.task('scripts', function() {
    gulp.src(publicDir + 'js/*.js')
        .pipe(uglify())
        .pipe(rename({
            dirname: "min",
            suffix: ".min"
        }))
        .pipe(gulp.dest(publicDir + 'js'))
});

/**
 * Compress images
 **/
gulp.task('images', function () {
    return gulp.src(publicDir + 'images/*')
        .pipe(imagemin({
            progressive: true,
            svgoPlugins: [{removeViewBox: false}],
            use: [pngquant()]
        }))
        .pipe(gulp.dest('images'));
});





gulp.task('default', ['sass', /*'browser-sync',*/ 'scripts', 'images', 'watch']);

gulp.task('watch', function () {
    gulp.watch(config.scss.src + '**/*.scss', ['sass']);
    gulp.watch(config.js.src + '**/*.js', ['scripts']);
    gulp.watch(publicDir + 'images/*', ['images']);
});