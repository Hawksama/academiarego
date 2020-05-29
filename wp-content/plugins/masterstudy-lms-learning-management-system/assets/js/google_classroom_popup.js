/**
 * @var google_classroom_data
 */

(function ($) {

    $(document).ready(function () {

        new Vue({
            el: '#google_classroom_popup',
            data: function () {
                return {
                    show_popup: false,
                    auditories: google_classroom_popup['auditory'],
                    bg: google_classroom_popup['bg'],
                    search: ''
                }
            },
            methods: {},
            computed: {
                auditoriesList() {
                    return this.auditories.filter(auditory => {
                        return auditory.name.toLowerCase().includes(this.search.toLowerCase())
                    })
                }
            },
            mounted: function () {

                var vm = this;

                setTimeout(function () {

                    vm.$set(vm, 'show_popup', true);

                }, 300);

            },
            watch: {
                show_popup: function (show) {
                    Vue.nextTick(function () {

                        if (show) {

                            var $ = jQuery;

                            $('html, body').css({
                                overflow: 'hidden'
                            });

                            $('.google_classroom_popup__auditories_wrapper').mCustomScrollbar();

                        } else {

                            var $ = jQuery;

                            $('html, body').css({
                                overflow: 'visible'
                            });

                            jQuery.cookie('google_classroom_popup', 'viewed', {path: '/'});

                        }
                    })
                }
            }
        })

    });

})(jQuery);