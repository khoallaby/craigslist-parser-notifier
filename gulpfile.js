var gulp        = require('gulp'),
//var bower       = require('gulp-bower'),
    sass        = require('gulp-sass'),
    prefix      = require('gulp-autoprefixer'),
    plumber     = require('gulp-plumber'),
    uglify      = require('gulp-uglify'),
    concat      = require('gulp-concat'),
    minify      = require('gulp-clean-css'),
    sourcemaps  = require('gulp-sourcemaps'),
    merge       = require('merge-stream'),
    rename      = require("gulp-rename"),
    imagemin    = require("gulp-imagemin"),
    pngquant    = require('imagemin-pngquant'),
    php         = require('gulp-connect-php'),
    livereload  = require('gulp-livereload'),
    gutil       = require('gulp-util');


var srcDir      = 'src/',
    publicDir   = 'public/',
    vendorDir   = 'vendor/';
var bowerDir    = vendorDir + 'bower_components/',
    assetsDir   = publicDir + 'assets/';


var config = {
    js: {
        src: srcDir + 'js',
        pub: assetsDir + 'js'
    },
    scss : {
        src: srcDir + 'scss/',
        pub: assetsDir + 'css/',
        sassOpts: {
            outputStyle: 'nested',
            precison: 3,
            errLogToConsole: true,
            includePaths: [bowerDir + 'bootstrap/scss']
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
 * PHP server @ localhost:8000
 */
gulp.task('php', function() {
    // remove trailing slash so it doesnt explode
    var dir = publicDir.substring(0, publicDir.length - 1);
    php.server({
        base: dir,
        hostname: 'localhost',
        open: true,
        port: 8000,
        keepalive: true,
        livereload: true
    });
});


/**
 * livereload
 */
gulp.task('livereload', ['php'], function() {

    gulp.watch([
        //config.scss.src + '**/*.scss',
        config.scss.pub + '**/*.css',
        config.js.src + '**/*.js',
        publicDir + '**/*.php',
        srcDir + '**/*.php'
    ], function (event) {
        gulp.src(event.path)
            .pipe(livereload());
    });

});


/**
 * Sass - autoprefix, concat, minify, merge with css files
 * @todo: run this on deploy
 **/
gulp.task('sass', function() {
    var scssStream = gulp.src([
            config.scss.src + '**/*.scss'
            //!config.scss.src + 'bootstrap/*.scss' // ignores contents of src/bootstrap dir
        ])
        .pipe(plumber({ errorHandler: onError }))
        .pipe(sass(config.scss.sassOpts))
        .pipe(prefix('last 2 versions', '> 1%', 'ie 8', 'Android 2', 'Firefox ESR'))
        .pipe(concat('scss-files.css'));
        //.pipe(gulp.dest(config.scss.pub));


    var cssStream = gulp.src([
            bowerDir + 'font-awesome/css/font-awesome.css'
        ])
        .pipe(concat('css-files.css'));

    return merge(scssStream, cssStream)
        .pipe(sourcemaps.init())
        .pipe(concat('style.css'))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(assetsDir + 'css/'))
        .pipe(concat('style.min.css'))
        .pipe(minify())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(assetsDir + 'css/'));

});




/**
 * JS - Uglify
 **/
gulp.task('scripts', function() {
    gulp.src(assetsDir + '*.js')
        .pipe(uglify())
        .pipe(rename({
            dirname: "min",
            suffix: ".min"
        }))
        .pipe(gulp.dest(assetsDir + 'js'))
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

gulp.task('move-files',function(){
    gulp.src(bowerDir + 'font-awesome/fonts/**/*')
        .pipe(gulp.dest(assetsDir + 'fonts/'));
});



gulp.task('default', ['sass', 'scripts', 'images', 'move-files', 'php', 'livereload', 'watch']);


gulp.task('watch', function () {

    livereload.listen();
    gulp.watch(config.scss.src + '**/*.scss', ['sass']);
    gulp.watch(config.js.src + '**/*.js', ['scripts']);
    gulp.watch(publicDir + 'images/*', ['images']);

});