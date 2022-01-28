<?php namespace BCIM;

class Init
{
    public function register_styles()
    {
        add_action('wp_enqueue_scripts', array($this, 'add_custom_styles'));
    }

    public function add_custom_styles()
    {
        wp_register_style('bcim-webinar-style', WEBINAR_PLUGIN_URL . 'assets/css/style.css', '', time());
        wp_enqueue_style('bcim-webinar-style');
    }
}
