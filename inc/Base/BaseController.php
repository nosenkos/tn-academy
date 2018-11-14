<?php
/**
 * @package  tn_academyPlugin
 */

namespace Inc\Base;

class BaseController
{
    public $plugin_path;

    public $plugin_url;

    public $plugin;

    public $admin_email;

    public $managers = array();

    public $data_prefix = 'tn-';

    public function __construct()
    {
        if (!defined('TNA_PLUGIN_NAME')) {
            define('TNA_PLUGIN_NAME', 'tn_academy-plugin');
        }

        // Add Image Size for Logo
        add_image_size('email_logo', 440, 94, true);

        //get admin email
        $this->admin_email = get_option('admin_email');

        $this->plugin_path = plugin_dir_path(dirname(__FILE__, 2));
        $this->plugin_url = plugin_dir_url(dirname(__FILE__, 2));
        $this->plugin = plugin_basename(dirname(__FILE__, 3)) . '/tn_academy-plugin.php';

        $this->managers = array(
            'cpt_manager' => __('Activate CPT Manager', TNA_PLUGIN_NAME),
            'event_button' => __('Activate Wysiwyg Button ', TNA_PLUGIN_NAME),
            'member_manager' => __('Activate Participant Manager', TNA_PLUGIN_NAME),
        );
    }

    public function activated(string $key)
    {
        $option = get_option('tn_academy_plugin');

        return isset($option[$key]) ? $option[$key] : false;
    }

    public function getAllPosts( string $post_type){
        $custom_post_data = array();

        $args = array(
            'post_type' => $post_type,
            'posts_per_page' => -1
        );

        $the_query = get_posts($args);

        if ($the_query) {
            foreach ($the_query as $row) {

                $title = $row->post_title;
                $id = $row->ID;

                array_push($custom_post_data,
                    array(
                        'text' => $title,
                        'value' => $id
                    ));
            }
            wp_reset_postdata();
        }

        return $custom_post_data;
    }
}