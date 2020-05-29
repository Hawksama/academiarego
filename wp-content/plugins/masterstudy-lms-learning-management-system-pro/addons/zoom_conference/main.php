<?php
if(class_exists('STM_ZOOM')){
    require_once(STM_LMS_PRO_PATH . '/addons/zoom_conference/zoom_plugins/zoom.php');
}
elseif (class_exists('Zoom_Video_Conferencing_Api')){
    require_once(STM_LMS_PRO_PATH . '/addons/zoom_conference/zoom_plugins/zoom-old.php');
}
else {
    require_once(STM_LMS_PRO_PATH . '/addons/zoom_conference/zoom_plugins/zoom.php');
}
