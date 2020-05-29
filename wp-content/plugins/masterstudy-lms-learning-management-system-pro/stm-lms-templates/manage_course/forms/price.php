<?php STM_LMS_Templates::show_lms_template('manage_course/forms/js/price'); ?>

<stm-price v-bind:course_price="fields['price']"
           v-bind:course_sale_price="fields['sale_price']"
           v-on:price-changed="fields['price'] = $event"
           v-on:sale_price-changed="fields['sale_price'] = $event">
</stm-price>

<input type="hidden" v-model="fields['price']" />
<input type="hidden" v-model="fields['sale_price']" />