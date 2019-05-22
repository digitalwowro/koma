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
        '../../../node_modules/admin-lte/bower_components/jquery/dist/jquery.js',
        '../../../node_modules/admin-lte/bower_components/bootstrap/dist/js/bootstrap.js',
        '../../../node_modules/admin-lte/plugins/iCheck/icheck.js',
        '../../../node_modules/admin-lte/bower_components/fastclick/lib/fastclick.js',
        '../../../node_modules/admin-lte/dist/js/adminlte.js',

        'jquery-ui.custom.js',
        'js.cookie.js',
        'bootstrap-datepicker.js',
        'select2.min.js',
        'jquery.dataTables.js',
        'dataTables.fixedHeader.js',
        'jquery.dataTables.bootstrap.js',
        'jquery.growl.js',
        'custom.js',

        'class.sharer.js',
    ], './public/js/all.js');

    mix.copy('node_modules/admin-lte/plugins/iCheck/square/blue.png', 'public/build/css');
    mix.copy('node_modules/admin-lte/bower_components/bootstrap/dist/fonts/*.*', 'public/build/fonts');
    mix.copy('node_modules/admin-lte/bower_components/font-awesome/fonts/*.*', 'public/build/fonts');

    mix.version(['css/all.css', 'js/all.js']);
});
