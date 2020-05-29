<?php
/**
 * Plugin Name: WordPress Custom Fields & Theme Options
 * Plugin URI:  https://github.com/StylemixThemes/wp-custom-fields-theme-options
 * Description: WordPress Custom Fields & Theme Options with Vue.js.
 * Version:     1.0.0
 * Author:      StylemixThemes
 * Author URI:  https://stylemixthemes.com
 *
 * @package    WordPress Custom Fields & Theme Options
 * @author     StylemixThemes
 * @copyright  Copyright (c) 2011-2020, StylemixThemes
 * @link       https://github.com/StylemixThemes/wp-custom-fields-theme-options
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

if (!class_exists('Stylemix_WPCFTO')) {

    define('STM_WPCFTO_FILE', __FILE__);
    define('STM_WPCFTO_PATH', dirname(STM_WPCFTO_FILE));
    define('STM_WPCFTO_URL', plugin_dir_url(STM_WPCFTO_FILE));

    class Stylemix_WPCFTO
    {
        function __construct()
        {

            require_once STM_WPCFTO_PATH . '/metaboxes/metabox.php';
            require_once STM_WPCFTO_PATH . '/taxonomy_meta/metaboxes.php';
            require_once STM_WPCFTO_PATH . '/settings/settings.php';
        }
    }

    new Stylemix_WPCFTO();
}