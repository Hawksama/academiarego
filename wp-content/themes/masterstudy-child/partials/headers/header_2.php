<?php stm_module_scripts('header_js', 'header_2'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">

            <div class="header_top header2">

                <div class="logo-unit">
					<?php get_template_part('partials/headers/parts/logo'); ?>
                </div>


                <div class="center-unit">
					<?php get_template_part('partials/headers/parts/center'); ?>
                </div>

                <div class="right-unit">
					<?php get_template_part('partials/headers/parts/right'); ?>
                </div>

                <div class="stm_header_top_search sbc">
                    <i class="lnr lnr-magnifier"></i>
                </div>

                <div class="stm_header_top_toggler mbc">
                    <i class="lnr lnr-user"></i>
                </div>

            </div>

        </div>
    </div>
</div>

<?php
$cats = stm_option('header_course_categories_online', array());
if (!empty($cats)): ?>

    <div class="categories-courses">
		<?php get_template_part('partials/headers/parts/courses_categories_with_search'); ?>
    </div>

<?php endif; ?>


<?php get_template_part('partials/headers/mobile/account'); ?>
<?php get_template_part('partials/headers/mobile/search'); ?>
<?php get_template_part('partials/headers/mobile/menu'); ?>
