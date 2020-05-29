(function ($) {
    $(window).load(function(){

        new StickySidebar('.stm-lms-course__sidebar-holder', {
            bottom: 0,
            top: 20,
            resizeSensor: true,
            containerSelector: '.udemy-sidebar-holder',
            innerWrapperSelector: '.stm-lms-course__sidebar'
        });

    });
})(jQuery);