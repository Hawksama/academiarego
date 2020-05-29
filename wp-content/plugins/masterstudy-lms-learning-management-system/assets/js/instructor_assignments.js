(function ($) {

    var myVar;

    $(document).ready(function () {

        $('body').addClass('instructor-assignments');

        new Vue({
            el: '#stm_lms_instructor_assignments',
            data: function () {
                return {
                    loading : false,
                    assignments: window['stm_lms_assignments']['tasks'].posts,
                    total : window['stm_lms_assignments']['tasks'].total,
                    pages : window['stm_lms_assignments']['tasks'].pages,
                    page : 1,
                    s : ''
                }
            },
            mounted: function () {
                this.getAssignmentsData();
            },
            methods: {
                getAssignmentsData() {
                    var vm = this;
                    vm.assignments.forEach(function (assignment) {
                        vm.getAssignmentData(assignment);
                    });
                },
                getAssignmentData(assignment) {
                    var vm = this;
                    var url = stm_lms_ajaxurl + '?action=stm_lms_get_assignment_data&id=' + assignment['id'] + '&nonce=' + stm_lms_nonces['stm_lms_get_assignment_data'];
                    vm.$set(assignment, 'loading', true);

                    this.$http.get(url).then(function (response) {

                        response = response.body;

                        vm.$set(assignment, 'loading', false);
                        vm.$set(assignment, 'data', response);

                    });
                },
                getAssignments() {
                    var vm = this;

                    if(vm.loading) return false;

                    var url = stm_lms_ajaxurl + '?action=stm_lms_get_instructor_assingments&nonce='
                        + stm_lms_nonces['stm_lms_get_instructor_assingments'] +
                        '&page=' + vm.page +
                        '&s=' + vm.s;
                    vm.$set(vm, 'loading', true);

                    this.$http.get(url).then(function (response) {
                        response = response.body;


                        vm.$set(vm, 'assignments', response.posts);
                        vm.$set(vm, 'total', response.total);
                        vm.$set(vm, 'pages', response.pages);
                        vm.$set(vm, 'loading', false);

                        vm.getAssignmentsData();

                    });
                },
                initSearch() {
                    var vm = this;
                    clearTimeout(myVar);
                    myVar = setTimeout(function(){
                        vm.$set(vm, 'page', 1);
                        vm.getAssignments();
                    }, 1000);
                }
            }
        });


    });

})(jQuery);