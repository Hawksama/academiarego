(function ($) {
    $(document).ready(function () {

        stm_lms_print_message({event_type :'order_created'});

        $('.stm-lms-single_quiz').submit(function (e) {
            e.preventDefault();

            var data = $(this).serialize();
            data += '&nonce=' + stm_lms_nonces['user_answers'];

            $.ajax({
                url: stm_lms_ajaxurl,
                dataType: 'json',
                context: this,
                data: data,
                beforeSend: function () {
                    $('.stm_lms_complete_lesson').addClass('loading');
                    $(this).find('button[type="submit"]').addClass('loading');

                    stm_lms_print_message({event_type :'quiz_sending'});
                },
                complete: function (data) {
                    var data = data['responseJSON'];
                    $(this).find('button[type="submit"]').removeClass('loading');
                    var passed_class = (data.passed) ? 'passed' : 'not-passed';
                    $('.stm-lms-quiz__result_number span').text(data.progress + '%');
                    $('.stm-lms-course__lesson-content').addClass(passed_class);

                    if(data.passed) {
                        $('.stm-lms-quiz__result_passing_grade').after('<div class="stm-lms-quiz__result_actions">' + data.url + '</div>');
                    }

                    stm_lms_print_message({event_type :'quiz_sent'});
                }
            });
        });

        $('.stm-lms-single_question_multi_choice input').on('change', function () {
            var $checkbox_name = $('.stm-lms-single_question_multi_choice input[name="' + $(this).attr('name') + '"]');
            var required = true;
            $checkbox_name.each(function () {
                if ($(this).prop('checked')) required = false;
            });

            $checkbox_name.attr('required', required);
        });

        /*Re-take*/
        $('.btn-retake').on('click', function () {
            $(this).closest('.stm-lms-course__lesson-content').removeClass('not-passed');
            start_quiz();
        });

        $('.stm_lms_start_quiz').on('click', function (e) {
            e.preventDefault();
            $('.stm-lms-single_quiz').slideDown();

            $(this).slideUp(400, function () {
                $(this).remove();
            });

            start_quiz();

        });

        function start_quiz() {
            stm_lms_item_match_resize();
            if($('.stm-lms-course__lesson-content').hasClass('no-timer')) return false;
            $('.stm_lms_timer').addClass('started');
            var source_page = (typeof source !== 'undefined') ? source : '';

            $.ajax({
                url: stm_lms_ajaxurl,
                dataType: 'json',
                context: this,
                data: {
                    'quiz_id': stm_lms_lesson_id,
                    'action': 'stm_lms_start_quiz',
                    'nonce' : stm_lms_nonces['start_quiz'],
                    'source' : source_page
                },
                complete: function (data) {
                    countTo(parseInt(data.responseJSON) * 1000);
                }
            });
        }


        var countInterval;
        var timeOut = false;
        function countTo(countDownDate) {
            clearInterval(countInterval);
            countInterval = setInterval(function () {

                var now = new Date().getTime();

                var distance = countDownDate - now;

                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                if(hours < 10) hours = '0' + hours;
                if(minutes < 10) minutes = '0' + minutes;
                if(seconds < 10) seconds = '0' + seconds;

                if(hours === '00' && minutes < 60) {
                    $('.stm_lms_timer__time_h').text(minutes + ':');
                    $('.stm_lms_timer__time_m').text(seconds);
                } else if (days < 1) {
                    $('.stm_lms_timer__time_h').text(hours + ':');
                    $('.stm_lms_timer__time_m').text(minutes);
                } else {
                    var daysText = $('.stm_lms_timer').attr('data-text-days');
                    $('.stm_lms_timer__time_h').text(days + ' ' + daysText);
                }



                if(!timeOut && distance < 1001) {
                    $('.stm_lms_timer').removeClass('started');
                    clearInterval(countInterval);
                    timeOut = true;

                    checkAnswers();

                    $('.stm_lms_complete_lesson').click();
                } else {
                    var strokex = ((stm_lms_quiz_duration - distance) * 195) / stm_lms_quiz_duration;
                    $('.stm_lms_timer__icon_timered circle').css({
                        'stroke-dasharray' : strokex + ', 300'
                    });

                    var timerArrow = ((stm_lms_quiz_duration - distance) * 360) / stm_lms_quiz_duration;
                    $('.stm_lms_timer__icon_arrow').css({
                        'transform' : 'rotateZ(' + timerArrow + 'deg)'
                    });
                }
            }, 1000);
        }

        $('.stm-lms-single_quiz input').on('change', function (e) {
            var dataAnswers = $('.stm-lms-single_quiz').serializeArray();
            var answered = [];
            dataAnswers.forEach(function (value, index) {

                value.name = value.name.replace(new RegExp("\\[.*?\\]","g"),"");

                if (answered.indexOf(value.name) === -1 && value.value !== '') {
                    answered.push(value.name);
                }

            });

            var answered_num = answered.length - 4;
            $('.stm_lms_timer__answered strong').text(answered_num);

            stm_lms_print_message({
                event_type :'answered_questions',
                answered_questions : {
                    total: _total(),
                    answered: answered_num
                }
            });
        });

        function _total() {
            return $('.stm-lms-single_question:not(.stm-lms-single_question_question_bank)').length;
        }

        function total() {
            var total_answered = _total();
            $('.stm_lms_timer__answered label').text(total_answered);

            stm_lms_print_message({
                event_type :'answered_questions',
                answered_questions : {
                    total: _total(),
                    answered: 0
                }
            });
        }

        total();

        function checkBankQuestions() {
            $('.stm-lms-single_question_question_bank').each(function(){
                if($(this).find('.stm-lms-single_question').length === 0) $(this).prev().hide();
            });
        }
        checkBankQuestions();

        function checkAnswers() {
            $('.stm-lms-single_quiz input').removeAttr('required');
        }

        $('.stm-lms-course__lesson-content.passed .stm-lms-quiz__result__overlay').on('click', function(e){
            e.preventDefault();
            $('.stm-lms-course__lesson-content').removeClass('passed');
        });

    });

    $(window).load(function(){
        stm_lms_item_match_resize();
    });

})(jQuery);

function stm_lms_item_match_resize() {
    var $ = jQuery;
    setTimeout(function(){
        if($('.stm_lms_question_item_match__answers').length && $('.stm_lms_question_item_match__questions').length ) {
            $('.stm_lms_question_item_match__questions > div').each(function(){
                var _this = $(this);
                var itemHeight = _this.outerHeight();
                $('.stm_lms_question_item_match__answers .stm_lms_question_item_match__answer').eq(_this.index()).css({
                    'min-height' : itemHeight + 'px'
                });
            })
        }
    }, 1000);
}

function stm_lms_start_quiz() {
    var $ = jQuery;

    $('.stm_lms_start_quiz').click();

    return null;

}

function stm_lms_accept_quiz() {

    var $ = jQuery;

    $('.stm_lms_complete_lesson').click();

    return $('.stm-lms-single_quiz')[0].checkValidity();
}