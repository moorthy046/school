var Vue = require('vue');
var VueRouter = require('vue-router');
var VueValidator = require('vue-validator');

Vue.use(require('vue-resource'));
Vue.use(VueRouter);
Vue.use(VueValidator);

Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#token').getAttribute('value');

Vue.directive('image-preview', require('./directives/image-upload-preview'));
Vue.directive('select', require('./directives/select2'));

var App = Vue.extend({
    el: 'body',

    components: {
        'backup-settings': require('./components/backup-settings'),
        'notifications': require('./components/notifications'),
        'mail-notifications': require('./components/mail-notification'),
        'mail': require('./components/mail')
    },

    methods: {
        initPusher: function () {

            // Enable pusher logging - don't include this in production
            //Pusher.log = function (message) {
            //    if (window.console && window.console.log) {
            //        window.console.log(message);
            //    }
            //};

            var pusherKey = document.querySelector('#pusherKey').getAttribute('value');
            var userId = document.querySelector('#userId').getAttribute('value');

            var pusher = new Pusher(pusherKey);

            //Channels
            var channel = pusher.subscribe('sms_channel.user_' + userId);

            //Events
             channel.bind('App\\Events\\Attendance\\AttendanceCreated', function (data) {
             toastr["then"]("New attendance: Attendance - " + data.email.subject);
             });
            channel.bind('App\\Events\\NotificationEvent', function (data) {
                toastr["then"](data.notification.title);
                this.$broadcast('newNotification', data.notification);
            }.bind(this));
        },

        initToastr: function () {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "positionClass": "toast-top-right",
                "onclick": null,
                "showDuration": "1000",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }
        }
    },

    events: {
        readMail: function (id) {
            this.$broadcast('readMail', id);
        }
    },

    ready: function () {
        this.initPusher();
        this.initToastr();
    }
});

var router = new VueRouter({
    hashbang: false,
    linkActiveClass: 'active'
});

router.map({
    '/m': {
        component: require('./components/mail'),

        subRoutes: {
            '/inbox': {
                component: require('./components/mail/mail-inbox')
            },

            '/inbox/:id': {
                name: 'inbox',
                component: require('./components/mail/mail-read'),

                subRoutes: {
                    '/reply': {
                        name: 'reply',
                        component: require('./components/mail/mail-reply')
                    }
                }
            },


            '/compose': {
                component: require('./components/mail/mail-compose')
            },

            '/sent': {
                component: require('./components/mail/mail-sent')
            },

            '/sent/:id': {
                name: 'sent',
                component: require('./components/mail/mail-read-sent')
            }

        }
    }
});

router.redirect({
    // redirect can contain dynamic segments
    // the dynamic segment names must match
    '/m/inboxr/:id': '/m/inbox/:id'
});

router.start(App, 'body');

$(document).ready(function () {
    $('.select2').select2({
        width: '100%',
        theme: 'bootstrap'
    });
    $('#to_email_id').select2({
        width: '100%',
        theme: 'bootstrap',
        placeholder: 'Select'
    });
    $('#phone_form')
        .bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'fa fa-check-circle-o',
                invalid: 'fa fa-times',
                validating: 'fa fa-refresh'
            },
            submitHandler: function (validator, form, submitButton) {
                // Do nothing
            },
            fields: {
                phone_number: {
                    validators: {
                        notEmpty: {
                            message: 'The mobile phone number is required'
                        },
                        digits: {
                            message: 'The mobile phone number is not valid'
                        }
                    }
                }
            }
        })
        .find('button[data-toggle]')
        .on('click', function () {
            var $target = $($(this).attr('data-toggle'));
            // Show or hide the additional fields
            // They will or will not be validated based on their visibilities
            $target.toggle();
            if (!$target.is(':visible')) {
                // Enable the submit buttons in case additional fields are not valid
                $('#togglingForm').data('bootstrapValidator').disableSubmitButtons(false);
            }
        });
});