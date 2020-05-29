(function($){
    $(document).ready(function(){

        $('.stm_lms_courses_search').each(function(){
            var $this = $(this);
            new Vue({
                el: $(this)[0],
                data: function () {
                    return {
                        vue_loaded: true,
                        loading: false,
                        search: '',
                        url: '',
                    }
                },
                mounted() {
                    this.url = stm_lms_search_value;
                },
                components: {
                    autocomplete: Vue2Autocomplete.default
                },
                methods : {
                    goto() {
                        console.log('goto');
                    },
                    searchCourse(obj) {
                        window.location.href = obj.url;
                    },
                    searching(value) {
                        $this.addClass('loading');
                        this.url = value;
                    },
                    loaded() {
                        $this.removeClass('loading');
                    }
                }
            });
        });
    });
})(jQuery);