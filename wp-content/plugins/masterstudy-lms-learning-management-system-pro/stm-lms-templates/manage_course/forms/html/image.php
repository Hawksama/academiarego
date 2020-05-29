<div>
    <div class="stm_lms_manage_course__image" v-bind:class="{'filled' : image['id'], 'loading' : loading}">

        <div class="stm_lms_manage_course__image_empty">
            <i class="lnr lnr-upload"></i>
			<h4 v-html="desc"></h4>
        </div>

        <img v-bind:src="image['url']"  v-if="image['url']" />

        <input v-model="image['file']"
               type="file"
               accept="image/jpeg,image/png,.jpeg,.jpg,.png"
               @change="loadImage()"
               ref="image-file" />

    </div>
    <transition name="slide-fade">
        <div class="stm-lms-message error" v-if="image['message']" v-html="image['message']"></div>
    </transition>
</div>