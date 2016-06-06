// Requis
var gulp = require('gulp');

// Include plugins
var plugins = require('gulp-load-plugins')(); // Load all plugins from package.json
var urlAdjuster = require('gulp-css-url-adjuster');

// Path files
var srcJsPath = './public/assets/js';
var srcCssPath = './public/assets/css';

var distJsPath = srcJsPath + '/min';
var distCssPath = srcCssPath + '/min';

/**
 * "build_css" task = autoprefixer + CSScomb + beautify (source -> destination)
 *
 * @returns {*}
 */
var beautifyCssTask = function () {
    return gulp.src(srcCssPath + '/*.css')
        .pipe(plugins.cached('building_css'))
        .pipe(plugins.csscomb())
        .pipe(plugins.cssbeautify({indent: '    '}))
        .pipe(plugins.autoprefixer())
        .pipe(gulp.dest(srcCssPath + '/'));
};

/**
 * "minify_css" task = Minify CSS + rename to *.min.css (destination -> destination)
 * @param src
 * @param dest
 * @returns {*}
 */
var minifyCssTask = function (src, dest) {
    return gulp.src(src)
        .pipe(plugins.cached('minifying_css'))
        .pipe(urlAdjuster({
            prepend: '../' //Change it if distCssPath has been changed
        }))
        .pipe(plugins.csso())
        .pipe(plugins.rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest(dest));
};

/**
 * "minify_js" task = Minify JS + rename to *.min.js (destination -> destination)
 *
 * @returns {*}
 */
var minifyJsTask = function () {
    return gulp.src(srcJsPath + '/*.js')
        .pipe(plugins.plumber({
            handleError: function (err) {
                console.log(err);
                this.emit('end');
            }
        }))
        .pipe(plugins.uglify())
        .pipe(plugins.rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest(distJsPath + '/'));
};

//Task function handling
gulp.task('beautify_css', beautifyCssTask);
gulp.task('minify_css', function () {
    return minifyCssTask(srcCssPath + '/*.css', distCssPath + '/')
});
gulp.task('minify_js', minifyJsTask);

//Task names config
gulp.task('prod', ['beautify_css', 'minify_css', 'minify_js']);
gulp.task('default', ['prod']);

// Watch task
gulp.task('watch', function () {
    gulp.watch(srcCssPath + '/*.css', ['prod']);
});
