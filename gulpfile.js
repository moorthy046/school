var gulp = require("gulp");
var bower = require("gulp-bower");
var elixir = require("laravel-elixir");

gulp.task('bower', function () {
    return bower();
});
/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 */
var vendors = '../../assets/vendors/';

var paths = {
    'jquery': vendors + 'jquery/dist',
    'jqueryUi': vendors + 'jquery-ui',
    'bootstrap': vendors + 'bootstrap/dist',
    'dataTables': vendors + 'datatables/media',
    'dataTablesBootstrap3Plugin': vendors + 'datatables-bootstrap3-plugin/media',
    'fontawesome': vendors + 'font-awesome',
    'animate': vendors + 'animate.css',
    'fileinput': vendors + 'bootstrap-fileinput',
    'select': vendors + 'bootstrap-select/dist',
    'bootstrapvalidator': vendors + 'bootstrapvalidator/dist',
    'fullcalendar': vendors + 'fullcalendar/dist',
    'icheck': vendors + 'iCheck',
    'eonasdanBootstrapDatetimepicker': vendors + 'eonasdan-bootstrap-datetimepicker/build',
    'moment': vendors + 'moment/min',
    'noty': vendors + 'noty/js/noty',
    'select2': vendors + 'select2/dist',
    'toastr': vendors + 'toastr',
    'verticaltimeline': vendors + 'vertical-timeline',
    'select2BootstrapTheme': vendors + 'select2-bootstrap-theme/dist',
    'pusher': vendors + 'pusher/dist/',
    'underscore': vendors + 'underscore',
    'jasnyBootstrap': vendors + 'jasny-bootstrap/dist',
    'twitterBootstrapWizard' : vendors + 'twitter-bootstrap-wizard'
};

elixir.config.sourcemaps = false;

elixir(function (mix) {

    // Run bower install
    mix.task('bower');

    //Custom Styles
    mix.styles(
        [
            'sms_bootstrap.css', 'metisMenu.min.css', 'sms.css', 'mail.css'
        ], 'public/css/sms.css');

    //Custom Javascript
    mix.browserify(['sms.js'], 'public/js/sms.js');


    mix.styles([paths.bootstrap + "/css/bootstrap.min.css",
        paths.bootstrap + "/css/bootstrap-theme.min.css",
        paths.fontawesome + "/css/font-awesome.css",
        "install.css",
    ], 'public/css/install.css');
    /**
     * Vendor files
     * run "gulp --production"
     */
    if (elixir.config.production) {
        // Copy fonts straight to public
        mix.copy('resources/assets/vendors/bootstrap/fonts', 'public/fonts');
        mix.copy('resources/assets/vendors/font-awesome/fonts', 'public/fonts');
        mix.copy('resources/assets/css/material-design-icons/iconfont', 'public/fonts');

        // Copy images straight to public
        mix.copy('resources/assets/vendors/bootstrap-fileinput/img', 'public/img');
        mix.copy('resources/assets/vendors/jquery-ui/themes/base/images', 'public/img');
        mix.copy('resources/assets/vendors/datatables/media/images', 'public/img');

        mix.copy('resources/assets/vendors/bootstrap-fileinput/img', 'public/img');
        mix.copy('resources/assets/images', 'public/img');
        mix.copy('resources/assets/avatar', 'public/uploads/avatar');
        mix.copy('resources/assets/logo', 'public/uploads/site');

        mix.copy('resources/assets/js/sms_app.js', 'public/js');
        mix.copy('resources/assets/js/metisMenu.min.js', 'public/js');
        //icheck
        mix.copy('resources/assets/css/icheck.css','public/css');
        mix.copy('resources/assets/vendors/iCheck/icheck.min.js','public/js');
        //c3 chart css and js files
        mix.copy('resources/assets/vendors/c3/c3.min.css', 'public/css');
        mix.copy('resources/assets/vendors/c3/c3.min.js', 'public/js');

        //favicon
        mix.copy('resources/assets/favicon/favicon.ico', 'public/img');


        //Mix styles for login page
        mix.styles([paths.bootstrap + "/css/bootstrap-theme.min.css",
            paths.fontawesome + "/css/font-awesome.css",
            'icheck.css',
        ], 'public/css/login.css');

        //Mix scripts for login page
        mix.scripts([paths.jquery + "/jquery.min.js",
            paths.bootstrap + "/js/bootstrap.min.js",
            "login.js"
        ], 'public/js/login.js')

        //Mix global styles
        mix.styles([ paths.fontawesome + "/css/font-awesome.css",
            paths.dataTables + "/css/dataTables.bootstrap.css",
            paths.dataTablesBootstrap3Plugin + "/css/datatables-bootstrap3.css",
            paths.animate + "/animate.min.css",
            paths.eonasdanBootstrapDatetimepicker + '/css/bootstrap-datetimepicker.css',
            paths.fileinput + "/css/fileinput.min.css",
            paths.select + "/css/bootstrap-select.min.css",
            paths.fullcalendar + "/fullcalendar.min.css",
            paths.bootstrapvalidator + "/css/bootstrapValidator.css",
            paths.select2BootstrapTheme + "/select2-bootstrap.min.css",
            paths.select2 + "/css/select2.min.css",
            paths.toastr + "/toastr.min.css",
            paths.jasnyBootstrap + "/css/jasny-bootstrap.min.css",
            paths.jqueryUi + "/themes/cupertino/jquery-ui.css",
            "panel.css",
            "material-design-icons/material-design-icons.css",
            "dataTables.bootstrap.css",
            "jasny-bootstrap.min.css"], 'public/css/libs.css');

        //Mix global scripts
        mix.scripts([paths.jquery + "/jquery.js",
            paths.jqueryUi + "/jquery-ui.min.js",
            paths.moment + "/moment-with-locales.js",
            paths.bootstrap + "/js/bootstrap.min.js",
            paths.bootstrapvalidator + "/js/bootstrapValidator.js",
            paths.dataTables + "/js/jquery.dataTables.js",
            paths.dataTables + "/js/dataTables.bootstrap.js",
            paths.dataTablesBootstrap3Plugin + "/js/datatables-bootstrap3.js",
            paths.eonasdanBootstrapDatetimepicker + '/js/bootstrap-datetimepicker.min.js',
            paths.fileinput + "/js/fileinput.min.js",
            paths.select + "/js/bootstrap-select.min.js",
            paths.fullcalendar + "/fullcalendar.js",
            paths.noty + "/jquery.noty.js",
            paths.select2 + "/js/select2.min.js",
            paths.toastr + "/toastr.min.js",
            paths.pusher + '/pusher.js',
            paths.jasnyBootstrap + "/js/jasny-bootstrap.js",
            paths.underscore + "/underscore-min.js",
            paths.twitterBootstrapWizard + '/jquery.bootstrap.wizard.js',
            "formValidation.min.js",
            "jasny-bootstrap.min.js",
            "palette.js"], 'public/js/libs.js');

        mix.styles([paths.bootstrap + "/css/bootstrap-theme.min.css",
            paths.bootstrap + "/css/bootstrap.min.css",
            paths.fontawesome + "/css/font-awesome.css"
        ], 'public/css/frontend.css');

    }
});
