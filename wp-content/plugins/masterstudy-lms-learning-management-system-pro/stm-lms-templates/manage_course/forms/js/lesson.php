<?php ob_start(); ?>

    <script>

        function WpcftoIsJsonString(str) {
            try {
                JSON.parse(str);
            } catch (e) {
                return false;
            }
            return true;
        }

        Vue.component('stm-lesson', {
            data: function () {
                return {
                    id: '',
                    title: '',
                    loading: false,
                    fields: {
                        content: '',
                        type: '',
                        duration: '',
                        lesson_excerpt: '',
                        preview: '',
                        lesson_video_poster: '',
                        lesson_video_poster_url: '',
                        lesson_video_url: '',
                        lesson_files_pack : '',
                    },
                    lesson_files_pack_data : stm_lms_manage_course['lesson_file_pack_data']
                }
            },
            mounted() {
                var _this = this;
                STM_LMS_EventBus.$on('STM_LMS_Curriculum_item', function (item) {
                    _this.id = item.id;
                    _this.title = item.title;
                    _this.opened = true;
                    _this.loading = true;

                    var url = stm_lms_ajaxurl + '?action=stm_curriculum_get_item&nonce=' + stm_lms_nonces['stm_curriculum_get_item'] + '&id=' + _this.id;
                    this.$http.get(url).then(function (response) {

                        var json = response.body;
                        var json_meta = response.body['meta'];

                        if (json.content) {
                            _this.$set(_this.fields, 'content', json.content);
                        } else {
                            _this.$set(_this.fields, 'content', '');
                        }

                        if (json_meta['type']) {
                            _this.$set(_this.fields, 'type', json_meta['type']);
                        } else {
                            _this.$set(_this.fields, 'type', '');
                        }

                        if (json_meta['duration']) {
                            _this.$set(_this.fields, 'duration', json_meta['duration']);
                        } else {
                            _this.$set(_this.fields, 'duration', '');
                        }

                        if (json_meta['lesson_excerpt']) {
                            _this.$set(_this.fields, 'lesson_excerpt', json_meta['lesson_excerpt']);
                        } else {
                            _this.$set(_this.fields, 'lesson_excerpt', '');
                        }

                        if (json_meta['preview']) {
                            _this.$set(_this.fields, 'preview', json_meta['preview']);
                        } else {
                            _this.$set(_this.fields, 'preview', '');
                        }

                        if (json_meta['lesson_video_poster_url']) {
                            _this.$set(_this.fields, 'lesson_video_poster_url', json_meta['lesson_video_poster_url']);
                        } else {
                            _this.$set(_this.fields, 'lesson_video_poster_url', '');
                        }


                        if (json_meta['lesson_video_url']) {
                            _this.$set(_this.fields, 'lesson_video_url', json_meta['lesson_video_url']);
                        } else {
                            _this.$set(_this.fields, 'lesson_video_url', '');
                        }

                        if (json_meta['lesson_files_pack']) {
                            _this.$set(_this.fields, 'lesson_files_pack', json_meta['lesson_files_pack']);
                        } else {
                            _this.$set(_this.fields, 'lesson_files_pack', '');
                        }

                        if (typeof json_meta['stream_start_date'] !== 'undefined') {
                            _this.$set(_this.fields, 'stream_start_date', json_meta['stream_start_date']);
                        }

                        if (typeof json_meta['stream_start_time'] !== 'undefined') {
                            _this.$set(_this.fields, 'stream_start_time', json_meta['stream_start_time']);
                        }

                        if (typeof json_meta['lesson_lock_from_start'] !== 'undefined') {
                            _this.$set(_this.fields, 'lesson_lock_from_start', json_meta['lesson_lock_from_start']);
                        }

                        if (typeof json_meta['lesson_start_date'] !== 'undefined') {
                            _this.$set(_this.fields, 'lesson_start_date', json_meta['lesson_start_date']);
                        }

                        if (typeof json_meta['lesson_start_time'] !== 'undefined') {
                            _this.$set(_this.fields, 'lesson_start_time', json_meta['lesson_start_time']);
                        }

                        if (typeof json_meta['lesson_lock_start_days'] !== 'undefined') {
                            _this.$set(_this.fields, 'lesson_lock_start_days', json_meta['lesson_lock_start_days']);
                        }

                        if (typeof json_meta['stream_end_date'] !== 'undefined') {
                            _this.$set(_this.fields, 'stream_end_date', json_meta['stream_end_date']);
                        }

                        if (typeof json_meta['stream_end_time'] !== 'undefined') {
                            _this.$set(_this.fields, 'stream_end_time', json_meta['stream_end_time']);
                        }

                        if (typeof json_meta['timezone'] !== 'undefined') {
                            _this.$set(_this.fields, 'timezone', json_meta['timezone']);
                        }

                        if (json_meta['join_before_host']) {
                            _this.$set(_this.fields, 'join_before_host', json_meta['join_before_host']);
                        } else {
                            _this.$set(_this.fields, 'join_before_host', '');
                        }

                        if (json_meta['option_host_video']) {
                            _this.$set(_this.fields, 'option_host_video', json_meta['option_host_video']);
                        } else {
                            _this.$set(_this.fields, 'option_host_video', '');
                        }

                        if (json_meta['option_participants_video']) {
                            _this.$set(_this.fields, 'option_participants_video', json_meta['option_participants_video']);
                        } else {
                            _this.$set(_this.fields, 'option_participants_video', '');
                        }

                        if (json_meta['option_mute_participants']) {
                            _this.$set(_this.fields, 'option_mute_participants', json_meta['option_mute_participants']);
                        } else {
                            _this.$set(_this.fields, 'option_mute_participants', '');
                        }
                        if (json_meta['option_enforce_login']) {
                            _this.$set(_this.fields, 'option_enforce_login', json_meta['option_enforce_login']);
                        } else {
                            _this.$set(_this.fields, 'option_enforce_login', '');
                        }

                        STM_LMS_EventBus.$emit('STM_LMS_Editor_Changed', _this.fields.content);

                        _this.loading = false;
                    });
                });
            },
            template: '<?php echo preg_replace(
                "/\r|\n/",
                "",
                addslashes(STM_LMS_Templates::load_lms_template('manage_course/forms/html/lesson'))
            ); ?>',
            methods: {
                dateChanged(value, option) {
                    var _this = this;
                    _this.$set(_this.fields, option, value);
                },
                saveChanges: function () {
                    var _this = this;
                    _this.loading = true;

                    var data = new FormData();
                    data.append('action', 'stm_lms_pro_save_lesson');
                    data.append('nonce', stm_lms_pro_nonces['stm_lms_pro_save_lesson']);
                    data.append('post_id', _this.id);
                    data.append('post_title', _this.title);

                    Object.keys(_this.fields).map(function (objectKey) {

                        if (objectKey === 'lesson_video_poster') {
                            if (typeof _this.$refs.video_poster.files[0] !== 'undefined') {
                                data.append('image', _this.$refs.video_poster.files[0]);
                            }
                        } else {
                            data.append(objectKey, _this.fields[objectKey]);
                        }
                    });

                    _this.$http.post(stm_lms_ajaxurl, data, {emulateJSON: true}).then(function (r) {
                        STM_LMS_EventBus.$emit('STM_LMS_Close_Modal');
                        _this.loading = false;
                    });
                },
                discardChanges: function () {
                    STM_LMS_EventBus.$emit('STM_LMS_Close_Modal');
                }
            }
        });
    </script>

<?php wp_add_inline_script('stm-lms-manage_course', str_replace(array('<script>', '</script>'), '', ob_get_clean())); ?>