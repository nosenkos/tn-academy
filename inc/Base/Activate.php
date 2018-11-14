<?php
/**
 * @package  tn_academyPlugin
 */

namespace Inc\Base;

class Activate
{
    public static function activate()
    {
        flush_rewrite_rules();

        $default = array();
        $default_cpt['course'] =
            array(
                'post_type' => 'course',
                'singular_name' => __('Course', TNA_PLUGIN_NAME),
                'plural_name' => __('Courses', TNA_PLUGIN_NAME),
                'public' => '1'
            );
        $default_mail = array(
            'email_course_data' => '1',
            'email_participant_data' => '1',
            'email_from' => '',
            'email_subject' => '',
            'email_academy_link' => '',
            'email_logo' => ''
        );

        if (!get_option('tn_academy_plugin')) {
            update_option('tn_academy_plugin', $default);
        }

        if (!get_option('tn_academy_plugin_cpt')) {
            update_option('tn_academy_plugin_cpt', $default_cpt);
        }

        if (!get_option('tn_academy_plugin_mail')) {
            update_option('tn_academy_plugin_mail', $default_mail);
        }
    }
}