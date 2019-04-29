const {mix} = require('laravel-mix');
const CleanWebpackPlugin = require('clean-webpack-plugin');

// paths to clean
var pathsToClean = [
    'public/assets/app/js',
    'public/assets/app/css',
    'public/assets/admin/js',
    'public/assets/admin/css',
    'public/assets/auth/css',
];

// the clean options to use
var cleanOptions = {};

mix.webpackConfig({
    plugins: [
        new CleanWebpackPlugin(pathsToClean, cleanOptions)
    ]
});

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

/*
 |--------------------------------------------------------------------------
 | Core
 |--------------------------------------------------------------------------
 |
 */

mix.scripts([
    'node_modules/jquery/dist/jquery.js',
    'node_modules/pace-progress/pace.js',

], 'public/assets/app/js/app.js').version();

mix.styles([
    'node_modules/font-awesome/css/font-awesome.css',
    'node_modules/pace-progress/themes/blue/pace-theme-minimal.css',
], 'public/assets/app/css/app.css').version();

mix.copy([
    'node_modules/font-awesome/fonts/',
], 'public/assets/app/fonts');

/*
 |--------------------------------------------------------------------------
 | Auth
 |--------------------------------------------------------------------------
 |
 */

mix.styles('resources/assets/auth/css/login.css', 'public/assets/auth/css/login.css').version();
mix.styles('resources/assets/auth/css/register.css', 'public/assets/auth/css/register.css').version();
mix.styles('resources/assets/auth/css/passwords.css', 'public/assets/auth/css/passwords.css').version();

mix.styles([
    'node_modules/bootstrap/dist/css/bootstrap.css',
    'node_modules/gentelella/vendors/animate.css/animate.css',
    'node_modules/gentelella/build/css/custom.css',
], 'public/assets/auth/css/auth.css').version();

/*
 |--------------------------------------------------------------------------
 | Admin
 |--------------------------------------------------------------------------
 |
 */

mix.scripts([
    'node_modules/bootstrap/dist/js/bootstrap.js',
    'node_modules/gentelella/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js',
    'node_modules/gentelella/build/js/custom.js',
], 'public/assets/admin/js/admin.js').version();

mix.styles([
    'node_modules/bootstrap/dist/css/bootstrap.css',
    'node_modules/gentelella/vendors/animate.css/animate.css',
    'node_modules/gentelella/build/css/custom.css',
], 'public/assets/admin/css/admin.css').version();


// Bootstrap 4.1.3
mix.styles([
    'resources/assets/admin/css/bootstrap.min.css',
], 'public/assets/admin/css/bootstrap.min.css').version();
mix.scripts([
    'resources/assets/admin/js/popper.min.js',
], 'public/assets/admin/js/popper.min.js').version();
mix.scripts([
    'resources/assets/admin/js/bootstrap.min.js',
], 'public/assets/admin/js/bootstrap.min.js').version();

mix.copy([
    'node_modules/gentelella/vendors/bootstrap/dist/fonts',
], 'public/assets/admin/fonts');


//Bootstrap 3.3.7
mix.scripts([
    'resources/assets/admin/js/bootstrap-3.3.7.min.js',
], 'public/assets/admin/js/bootstrap-3.3.7.min.js').version();


// Bootstrap Table 1.12.1
mix.styles([
    'resources/assets/admin/css/bootstrap-table.min.css',
], 'public/assets/admin/css/bootstrap-table.min.css').version();
mix.scripts([
    'resources/assets/admin/js/bootstrap-table.min.js',
], 'public/assets/admin/js/bootstrap-table.min.js').version();

//Bootstrap Select 1.13.1
mix.styles([
    'resources/assets/admin/css/bootstrap-select.css',
], 'public/assets/admin/css/bootstrap-select.css').version();
mix.scripts([
    'resources/assets/admin/js/bootstrap-select.min.js',
], 'public/assets/admin/js/bootstrap-select.min.js').version();


//JQuery 3.3.1
mix.scripts([
    'resources/assets/admin/js/jquery-3.3.1.min.js',
], 'public/assets/admin/js/jquery-3.3.1.min.js').version();

// JQuery UI 1.12.1
mix.styles([
 'resources/assets/admin/css/jquery-ui.min.css',
], 'public/assets/admin/css/jquery-ui.min.css').version();
mix.scripts([
	'resources/assets/admin/js/jquery-ui.min.js',
], 'public/assets/admin/js/jquery-ui.min.js').version();

// JQuery Data Table 1.10.19
mix.styles([
    'resources/assets/admin/css/jquery.dataTables.min.css',
], 'public/assets/admin/css/jquery.dataTables.min.css').version();
mix.scripts([
    'resources/assets/admin/js/jquery.dataTables.min.js',
], 'public/assets/admin/js/jquery.dataTables.min.js').version();

// JQuery Mask 1.14.15
mix.scripts([
    'resources/assets/admin/js/jquery.mask.js',
], 'public/assets/admin/js/jquery.mask.js').version();

// Firebase API
mix.scripts([
    'resources/assets/admin/js/firebase-api.js',
], 'public/assets/admin/js/firebase-api.js').version();

// Modal Delete
mix.scripts([
    'resources/assets/admin/js/ModalDelete.js',
], 'public/assets/admin/js/ModalDelete.js').version();

// Checkbox Switch
mix.styles([
    'resources/assets/admin/css/checkbox.switch.css',
], 'public/assets/admin/css/checkbox.switch.css').version();

mix.scripts([
    'node_modules/select2/dist/js/select2.full.js',
    'resources/assets/admin/js/users/edit.js',
], 'public/assets/admin/js/users/edit.js').version();

mix.scripts([
    'resources/assets/admin/js/location.js',
], 'public/assets/admin/js/location.js').version();

mix.scripts([
    'resources/assets/admin/js/validation.js',
], 'public/assets/admin/js/validation.js').version();

mix.scripts([
    'resources/assets/admin/js/validation_config.js',
], 'public/assets/admin/js/validation_config.js').version();


// Store Report CSS
mix.styles([
    'resources/assets/admin/css/daterangepicker.css',
], 'public/assets/admin/css/daterangepicker.css').version();

// Store Report JS
mix.scripts([
    'resources/assets/admin/js/google.charts.js',
], 'public/assets/admin/js/google.charts.js').version();
mix.scripts([
    'resources/assets/admin/js/report.js',
], 'public/assets/admin/js/report.js').version();
mix.scripts([
	'resources/assets/admin/js/moment.min.js',
], 'public/assets/admin/js/moment.min.js').version();
mix.scripts([
	'resources/assets/admin/js/daterangepicker.js',
], 'public/assets/admin/js/daterangepicker.js').version();


// Export reports CSS
mix.styles([
    'resources/assets/admin/css/tableexport.css',
], 'public/assets/admin/css/tableexport.css').version();

//Export reports JS
mix.scripts([
	'resources/assets/admin/js/FileSaver.min.js',
], 'public/assets/admin/js/FileSaver.min.js').version();
mix.scripts([
	'resources/assets/admin/js/Blob.min.js',
], 'public/assets/admin/js/Blob.min.js').version();
mix.scripts([
	'resources/assets/admin/js/xlsx-core.min.js',
], 'public/assets/admin/js/xlsx-core.min.js').version();
mix.scripts([
	'resources/assets/admin/js/tableexport.js',
], 'public/assets/admin/js/tableexport.js').version();


mix.styles([
    'node_modules/select2/dist/css/select2.css',
], 'public/assets/admin/css/users/edit.css').version();

mix.scripts([
    'node_modules/gentelella/vendors/Flot/jquery.flot.js',
    'node_modules/gentelella/vendors/Flot/jquery.flot.time.js',
    'node_modules/gentelella/vendors/Flot/jquery.flot.pie.js',
    'node_modules/gentelella/vendors/Flot/jquery.flot.stack.js',
    'node_modules/gentelella/vendors/Flot/jquery.flot.resize.js',

    'node_modules/gentelella/vendors/flot.orderbars/js/jquery.flot.orderBars.js',
    'node_modules/gentelella/vendors/DateJS/build/date.js',
    'node_modules/gentelella/vendors/flot.curvedlines/curvedLines.js',
    'node_modules/gentelella/vendors/flot-spline/js/jquery.flot.spline.min.js',

    'node_modules/gentelella/production/js/moment/moment.min.js',
    'node_modules/gentelella/vendors/bootstrap-daterangepicker/daterangepicker.js',


    'node_modules/gentelella/vendors/Chart.js/dist/Chart.js',
    'node_modules/jcarousel/dist/jquery.jcarousel.min.js',

    'resources/assets/admin/js/dashboard.js',
], 'public/assets/admin/js/dashboard.js').version();

mix.styles([
    'node_modules/gentelella/vendors/bootstrap-daterangepicker/daterangepicker.css',
    'resources/assets/admin/css/dashboard.css',
], 'public/assets/admin/css/dashboard.css').version();

