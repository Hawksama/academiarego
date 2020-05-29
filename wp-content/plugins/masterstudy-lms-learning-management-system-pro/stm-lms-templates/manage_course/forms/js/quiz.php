<?php ob_start(); ?>

<script>
    Vue.component('stm-quiz', {
        data: function () {
            return {
                id: '',
                title: '',
                loading: false,
                fields: {
                    content: '',
                    lesson_excerpt: '',
                    questions: '',
                    duration: '',
                    duration_measure: '',
                    correct_answer: '',
                    passing_grade: '',
                    re_take_cut: '',
                }
            }
        },
        mounted () {
            var _this = this;
            STM_LMS_EventBus.$on('STM_LMS_Curriculum_item', function (item) {
                _this.id = item.id;
                _this.title = item.title;
                _this.loading = true;

                var url = stm_lms_ajaxurl + '?action=stm_curriculum_get_item&nonce=' + stm_lms_nonces['stm_curriculum_get_item'] + '&id=' + _this.id;
                this.$http.get(url).then(function(response){

                    var json = response.body;
                    var json_meta = response.body['meta'];

                    if(json.content) {
                        _this.$set(_this.fields, 'content', json.content);
                    } else {
                        _this.$set(_this.fields, 'content', '');
                    }


                    var questions = (json_meta['questions']) ? json_meta['questions'] : '';
                    console.log(questions);
                    STM_LMS_EventBus.$emit('STM_LMS_Questions_Update', questions);
                    _this.$set(_this.fields, 'questions', questions);


                    if(json_meta['lesson_excerpt']) {
                        _this.$set(_this.fields, 'lesson_excerpt', json_meta['lesson_excerpt']);
                    } else {
                        _this.$set(_this.fields, 'lesson_excerpt', '');
                    }

                    if(json_meta['duration']) {
                        _this.$set(_this.fields, 'duration', json_meta['duration']);
                    } else {
                        _this.$set(_this.fields, 'duration', '');
                    }

                    if(json_meta['duration_measure']) {
                        _this.$set(_this.fields, 'duration_measure', json_meta['duration_measure']);
                    } else {
                        _this.$set(_this.fields, 'duration_measure', '');
                    }

                    if(json_meta['correct_answer']) {
                        _this.$set(_this.fields, 'correct_answer', json_meta['correct_answer']);
                    } else {
                        _this.$set(_this.fields, 'correct_answer', '');
                    }

                    if(json_meta['passing_grade']) {
                        _this.$set(_this.fields, 'passing_grade', json_meta['passing_grade']);
                    } else {
                        _this.$set(_this.fields, 'passing_grade', '');
                    }

                    if(json_meta['re_take_cut']) {
                        _this.$set(_this.fields, 're_take_cut', json_meta['re_take_cut']);
                    } else {
                        _this.$set(_this.fields, 're_take_cut', '');
                    }

                    STM_LMS_EventBus.$emit('STM_LMS_Editor_Changed', _this.fields.content);

                    _this.loading = false;
                });
            });
        },
		template: '<?php echo preg_replace(
			"/\r|\n/",
			"",
			addslashes(STM_LMS_Templates::load_lms_template('manage_course/forms/html/quiz'))
		); ?>',
        methods: {
            saveChanges: function() {
                var _this = this;
                _this.loading = true;

                var url = stm_lms_ajaxurl + '?action=stm_lms_pro_save_quiz&nonce=' + stm_lms_pro_nonces['stm_lms_pro_save_quiz'] + '&post_id=' + _this.id + '&post_title=' + _this.title;

                Object.keys(_this.fields).map(function(objectKey) {
                    url += '&' + objectKey + '=' + _this.fields[objectKey];
                });

                _this.$http.get(url).then(function(r){
                    STM_LMS_EventBus.$emit('STM_LMS_Close_Modal');
                    _this.loading = false;
                });
            },
            discardChanges: function() {
                STM_LMS_EventBus.$emit('STM_LMS_Close_Modal');
            }
        }
    });
</script>

<?php wp_add_inline_script('stm-lms-manage_course', str_replace(array('<script>', '</script>'), '', ob_get_clean())); ?>