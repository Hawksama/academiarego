<?php ob_start(); ?>

<script>
    Vue.component('stm-modal', {
        data: function () {
            return {
                post_type: '',
                opened: false,
            }
        },
        mounted () {
            var _this = this;
            STM_LMS_EventBus.$on('STM_LMS_Curriculum_item', function (item) {
                _this.post_type = item.post_type;
                _this.opened = true;
            });

            STM_LMS_EventBus.$on('STM_LMS_Close_Modal', function () {
                _this.opened = false;
            })
        },
		template: '<?php echo preg_replace(
			"/\r|\n/",
			"",
			addslashes(STM_LMS_Templates::load_lms_template('manage_course/forms/html/modal'))
		); ?>',
        watch: {
		    opened: function(opened) {
		        var _this = this;
		        if(!opened) {
                    jQuery('html').removeClass('locked');
                } else {
                    jQuery('html').addClass('locked');
                }
            }
        }
    });
</script>

<?php wp_add_inline_script('stm-lms-manage_course', str_replace(array('<script>', '</script>'), '', ob_get_clean())); ?>