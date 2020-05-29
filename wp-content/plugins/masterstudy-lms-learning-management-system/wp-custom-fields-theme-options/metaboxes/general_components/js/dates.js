Vue.component('date-picker', DatePicker.default);
Vue.component('wpcfto_dates', {
    props: ['fields', 'field_label', 'field_name', 'field_id', 'field_value'],
    data: function () {
        return {
            value: [],
            saveValue : []
        }
    },
    mounted: function () {

        console.log(this.field_value);

        if (typeof this.field_value !== 'undefined') {
            if (typeof this.field_value[0] !== 'undefined') {
                this.value.push(new Date(parseInt(this.field_value[0])));
            }
            if (typeof this.field_value[1] !== 'undefined') {
                this.value.push(new Date(parseInt(this.field_value[1])));
            }

            console.log(this.value);
        }
    },
    template: `
        <div class="wpcfto_generic_field">
        
            <label v-html="field_label"></label>
        
            <date-picker v-model="value" range lang="en" @change="dateChanged(value)"></date-picker>
                        
            <input type="hidden" v-bind:name="field_name" v-model="saveValue" />
            <input type="hidden" v-bind:name="field_name + '_start'" v-model="saveValue[0]" />
            <input type="hidden" v-bind:name="field_name + '_end'" v-model="saveValue[1]" />
        </div>
    `,
    methods: {
        dateChanged(newDate) {
            var customDate = [];
            customDate.push(new Date(newDate[0]).getTime());
            customDate.push(new Date(newDate[1]).getTime());
            this.$emit('wpcfto-get-value', customDate);

            this.$set(this, 'saveValue', customDate);
        }
    },
});