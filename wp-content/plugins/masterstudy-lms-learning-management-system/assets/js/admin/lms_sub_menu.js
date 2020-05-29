(function ($) {

    $(document).ready(function () {

        var classes = [
            'post-type-stm-courses',
            'post-type-stm-lessons',
            'post-type-stm-quizzes',
            'post-type-stm-questions',
            'post-type-stm-assignments',
            'post-type-stm-reviews',
            'post-type-stm-orders',
            'post-type-stm-ent-groups',
            'post-type-stm-payout',
            'taxonomy-stm_lms_course_taxonomy',
            'stm-lms_page_stm-lms-online-testing',
            'admin_page_stm_lms_scorm_settings',
        ];

        if($('body').is("." + classes.join(', .'))) {


            $('#adminmenu > li').removeClass('wp-has-current-submenu wp-menu-open');

            $('#toplevel_page_stm-lms-settings')
                .addClass('wp-has-current-submenu wp-menu-open')
                .removeClass('wp-not-current-submenu');

            $('.toplevel_page_stm-lms-settings')
                .addClass('wp-has-current-submenu')
                .removeClass('wp-not-current-submenu');
        }


    });

})(jQuery);