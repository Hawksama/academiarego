<?php

new STM_LMS_Point_Distribution;

class STM_LMS_Point_Distribution
{

    function __construct()
    {

    }

    static function points_distribution_url() {
        return home_url('/') . STM_LMS_WP_Router::route_urls('points_distribution');
    }

}