(function($){

    $(document).ready(function(){

        new Vue({
            el : '#certificate_checker',
            data: function () {
                return {
                    loading : false,
                    code : '',
                    status : '',
                    message : ''
                }
            },
            methods: {
                checkCode : function() {
                    var vm = this;

                    /*Check If added*/
                    if(vm.code === '' || vm.code.length < 5) {
                        vm.status = 'error';
                        vm.message = 'Enter Valide code';

                        return false;
                    }

                    vm.loading = true;
                    vm.status = vm.message = '';

                    var url = stm_lms_ajaxurl + '?action=stm_lms_check_certificate_code&code=' + vm.code + '&nonce=' + stm_lms_nonces['stm_lms_check_certificate_code'];

                    vm.$http.get(url).then(function(res){
                        vm.status = res.body.status;
                        vm.message = res.body.message;
                        vm.loading = false;
                    });

                }
            }
        })

    });

})(jQuery);