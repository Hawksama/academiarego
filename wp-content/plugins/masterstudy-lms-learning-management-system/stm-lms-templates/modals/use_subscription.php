<?php
/**
 * @var $stm_lms_vars
 */

?>

<div class="modal fade stm-lms-use-subscription" tabindex="-1" role="dialog" aria-labelledby="stm-lms-use-subscription">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <?php
                if (count($stm_lms_vars) > 1) {
                    STM_LMS_Templates::show_lms_template('account/use_membership_multiply', compact('stm_lms_vars'));
                } else {
                    foreach ($stm_lms_vars as $subscription) {
                        extract($subscription);
                        /**
                         * @var $name
                         * @var $course_number
                         * @var $used_quotas
                         * @var $course_id
                         * @var $quotas_left
                         */

                        STM_LMS_Templates::show_lms_template(
                            'account/use_membership',
                            compact('name', 'course_number', 'used_quotas', 'course_id', 'quotas_left')
                        );
                    }
                }

                ?>

            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    (function ($) {
        $('[data-lms-usemembership]').on('click', function (e) {
            e.preventDefault();

            var course_id = $(this).attr('data-lms-course');

            if (typeof stm_lms_course_id !== 'undefined') course_id = stm_lms_course_id;

            var data = {
                action: 'stm_lms_use_membership',
                nonce: stm_lms_nonces['stm_lms_use_membership'],
                course_id: course_id,
            };

            var membership_id = $(this).attr('data-membership-id');

            if(typeof membership_id !== 'undefined') {
                data['membership_id'] = membership_id;
            }

            $.ajax({
                url: stm_lms_ajaxurl,
                dataType: 'json',
                context: this,
                data: data,
                beforeSend: function () {
                    $(this).addClass('loading');
                },
                complete: function (data) {
                    var data = data['responseJSON'];
                    $(this).removeClass('loading');
                    window.location.href = data['url'];
                }
            });
        });
    })(jQuery)
</script>