<?php ob_start(); ?>

    <script>

        Vue.component('date-picker', DatePicker.default);
        Vue.component('stm-date', {
            props: ['current_date'],
            data: function () {
                return {
                    date: []
                }
            },
            mounted: function () {
                if (typeof this.current_date !== 'undefined') {
                    this.date.push(new Date(parseInt(this.current_date)));
                }
            },
            template: `
                <div>
                    <date-picker v-model="date" lang="en" @change="dateChanged(date)"></date-picker>
                </div>
            `,
            methods: {
                dateChanged(newDate) {
                    this.$emit('date-changed', new Date(newDate).getTime());
                }
            },
        });
    </script>

<?php wp_add_inline_script('stm-lms-manage_course', str_replace(array('<script>', '</script>'), '', ob_get_clean())); ?>