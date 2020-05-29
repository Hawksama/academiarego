(function ($) {

    /**
     * @var stm_lms_restore_password
     */

    $(document).ready(function () {
        $('.remembered_creds a').on('click', function(e){
            e.preventDefault();
            $('#stm-lms-login').slideToggle();
        });
    });

    new Vue({
        el: '#stm-lms-reset-password',
        data: {
            token: stm_lms_restore_password['token'],
            password: '',
            status : '',
            message : '',
            loading : false,
        },
        methods : {
            changePassword : function() {
                var _this = this;
                if(!_this.token || !_this.password) return false;

                _this.loading = true;

                _this.$http.post(stm_lms_ajaxurl + '?action=stm_lms_restore_password', {
                    token : _this.token,
                    password : _this.password,
                }).then(function(r){
                    r = r.body;
                    _this.$set(_this, 'status', r.status);
                    _this.$set(_this, 'message', r.message);
                    _this.loading = false;
                });
            }
        }
    });

})(jQuery);