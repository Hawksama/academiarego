(function ($) {


    $(document).ready(function () {

        new Vue({
            el: '#stm_lms_points_history',
            data: function () {
                return {
                    points : stm_lms_points_history['result']['points'],
                    pages : stm_lms_points_history['result']['pages'],
                    page : 1,
                    loading: false,
                    sum : stm_lms_points_history['result']['sum'],
                }
            },
            methods: {
                getPoints() {

                    var vm = this;

                    vm.loading = true;

                    var url = stm_lms_ajaxurl + '?action=stm_lms_get_user_points_history&nonce='
                        + stm_lms_nonces['stm_lms_get_user_points_history'] +
                        '&page=' + vm.page;

                    vm.$http.get(url).then(function (r) {

                        var res = r.body;
                        vm.loading = false;

                        vm.$set(vm, 'points', res.points);

                    });
                }
            },
        });
    });

})(jQuery);