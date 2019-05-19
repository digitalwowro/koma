var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.styles([
        'bower_components/bootstrap/dist/css/bootstrap.css',
        'bower_components/datatables.net-bs/css/dataTables.bootstrap.css',
        'bower_components/font-awesome/css/font-awesome.css',
        'bower_components/Ionicons/css/ionicons.css',
        'dist/css/AdminLTE.css',
        'dist/css/skins/skin-blue.css',
        'bower_components/select2/dist/css/select2.css',
        '../../resources/assets/css/jquery.growl.css',
        'dist/css/alt/AdminLTE-select2.css',
        'plugins/iCheck/square/blue.css',
        '../../resources/assets/css/custom.css',
    ], './public/css/all.css', 'node_modules/admin-lte');

    mix.scripts([
        'bower_components/jquery/dist/jquery.js',
        'bower_components/bootstrap/dist/js/bootstrap.js',

        '../../resources/assets/js/jquery-ui.custom.js',
        '../../resources/assets/js/js.cookie.js',
        '../../resources/assets/js/bootstrap-datepicker.js',
        '../../resources/assets/js/select2.min.js',
        '../../resources/assets/js/jquery.dataTables.js',
        '../../resources/assets/js/dataTables.fixedHeader.js',
        '../../resources/assets/js/jquery.dataTables.bootstrap.js',
        '../../resources/assets/js/jquery.growl.js',

        'plugins/iCheck/icheck.js',
        'bower_components/fastclick/lib/fastclick.js',
        'dist/js/adminlte.js',
        '../../resources/assets/js/custom.js',
    ], './public/js/all.js', 'node_modules/admin-lte');

    mix.copy('node_modules/admin-lte/plugins/iCheck/square/blue.png', 'public/build/css');
    mix.copy('node_modules/admin-lte/bower_components/bootstrap/dist/fonts/*.*', 'public/build/fonts');
    mix.copy('node_modules/admin-lte/bower_components/font-awesome/fonts/*.*', 'public/build/fonts');

    mix.version(['css/all.css', 'js/all.js']);
});
