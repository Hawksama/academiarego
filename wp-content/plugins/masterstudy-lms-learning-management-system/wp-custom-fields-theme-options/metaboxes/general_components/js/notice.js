Vue.component('wpcfto_notice', {
    props: ['fields', 'field_label', 'field_name', 'field_id', 'field_value'],
    data: function () {
        return {
            value: '',
        }
    },
    template: `
        <div class="wpcfto_generic_field wpcfto_generic_field__notice">
            <label v-html="field_label"></label>
        </div>
    `,
    mounted: function () {

    },
    methods: {},
    watch: {
        value: function (value) {

        }
    }
});