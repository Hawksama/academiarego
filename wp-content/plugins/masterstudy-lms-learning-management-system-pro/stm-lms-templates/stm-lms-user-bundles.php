<?php
if (!defined('ABSPATH')) exit; //Exit if accessed directly ?>

<?php
get_header();

do_action('stm_lms_template_main');

$style = STM_LMS_Options::get_option('profile_style', 'default');
?>



    <div class="stm-lms-wrapper stm-lms-wrapper--assignments">

        <div class="container">

            <?php if ($style === 'classic'):

                STM_LMS_Templates::show_lms_template(
                    'account/private/classic/parts/header',
                    array('current_user' => STM_LMS_User::get_current_user('', true, true))
                );

            endif; ?>

            <div id="stm_lms_user_bundles_archive">
                <?php STM_LMS_Templates::show_lms_template('bundles/my_bundles'); ?>
            </div>


        </div>

    </div>

<?php get_footer(); ?>