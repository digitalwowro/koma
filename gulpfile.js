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
        'admin-lte/bower_components/bootstrap/dist/css/bootstrap.css',
        'admin-lte/bower_components/datatables.net-bs/css/dataTables.bootstrap.css',
        'admin-lte/bower_components/font-awesome/css/font-awesome.css',
        'admin-lte/bower_components/Ionicons/css/ionicons.css',
        'admin-lte/dist/css/AdminLTE.css',
        'admin-lte/dist/css/skins/skin-blue.css',
        'admin-lte/bower_components/select2/dist/css/select2.css',
        '../resources/assets/css/jquery.growl.css',
        'admin-lte/dist/css/alt/AdminLTE-select2.css',
        'admin-lte/plugins/iCheck/square/blue.css',
        '../resources/assets/css/custom.css',
        'jstree/dist/themes/default/style.css',
    ], './public/css/all.css', 'node_modules');

    mix.scripts([
        '../../../node_modules/admin-lte/bower_components/jquery/dist/jquery.js',
        '../../../node_modules/admin-lte/bower_components/bootstrap/dist/js/bootstrap.js',
        '../../../node_modules/admin-lte/plugins/iCheck/icheck.js',
        '../../../node_modules/admin-lte/bower_components/fastclick/lib/fastclick.js',
        '../../../node_modules/admin-lte/dist/js/adminlte.js',
        '../../../node_modules/jstree/dist/jstree.js',

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
    mix.copy('node_modules/jstree/dist/themes/default/*.png', 'public/build/css');
    mix.copy('node_modules/jstree/dist/themes/default/*.gif', 'public/build/css');

    mix.version(['css/all.css', 'js/all.js']);
});
