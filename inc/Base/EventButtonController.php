<?php
/**
 * Created by PhpStorm.
 * User: sergeynosenko
 * Date: 24.09.2018
 * Time: 16:36
 */

namespace Inc\Base;

use Inc\Base\BaseController;
use Inc\Base\CustomPostTypeController;

class EventButtonController extends BaseController
{

    public $btn_settings =array();

    public function register()
    {
        if (!$this->activated('event_button')) return;

        // get Button Settings
        $this->btn_settings = get_option('tn_academy_plugin_btn');

        add_filter('mce_external_plugins', array($this, 'tn_add_buttons'));
        add_filter('mce_buttons', array($this, 'tn_register_new_buttons'));

        // Add am ajax call function to retrieve Courses

        add_action('wp_ajax_call_get_courses', array($this, 'ajax_get_courses'));
        add_action('wp_ajax_nopriv_call_get_courses', array($this, 'ajax_get_courses'));
    }

    function tn_add_buttons($plugin_array)
    {
        ?>
        <script type="text/javascript">
            var name_btn_plugin = "<?php echo ($this->btn_settings['btn_name'] && $this->btn_settings['btn_name'] != "") ? $this->btn_settings['btn_name'] : __("Course Button", TNA_PLUGIN_NAME) ;?>";
        </script>
        <?php
        $plugin_array['tn'] = $this->plugin_url . 'assets/tn-plugin.js';
        return $plugin_array;
    }

    function tn_register_new_buttons($buttons)
    {
        array_push($buttons, 'eventsbutton'); // eventsbutton'
        return $buttons;
    }

    function ajax_get_courses()
    {

        $post_type = array();
        $post_types = CustomPostTypeController::getPostTypes();

        foreach ($post_types as $row) {
            $post_type[] = $row['post_type'];
        }


        $custom_post_data = array();

        $popup_data = array(
            'msg_missing_field' => esc_html__('This field is required.', TNA_PLUGIN_NAME),
            'text_add_tn_academy_course' => esc_html__(($this->btn_settings['add_btn_title'] && $this->btn_settings['add_btn_title'] != "") ? $this->btn_settings['add_btn_title'] : 'Add Course Button' , TNA_PLUGIN_NAME),
            'course_name' => esc_html__(($this->btn_settings['add_btn_name'] && $this->btn_settings['add_btn_name'] != "") ? $this->btn_settings['add_btn_name'] : 'Course Name', TNA_PLUGIN_NAME),
            'link' => esc_html__('Link', TNA_PLUGIN_NAME),
            'text' => esc_html__(($this->btn_settings['add_btn_desc'] && $this->btn_settings['add_btn_desc'] != "") ? $this->btn_settings['add_btn_desc'] : 'Course Short Description', TNA_PLUGIN_NAME),
            'link_text' => esc_html__('Link Text', TNA_PLUGIN_NAME),
            'msg_no_courses' => '<h5>' . esc_html__('No Courses found!', TNA_PLUGIN_NAME) . '</h5><p>' . __('You need to create course before adding them.', TNA_PLUGIN_NAME) . '</p><p><a href="' . esc_url(admin_url('post-new.php?post_type=' . $post_type[0])) . '" class="button">' . esc_html__('Create new course', TNA_PLUGIN_NAME) . '</a></p>',
            'msg_new_course_how_to' => '<p>' . __(($this->btn_settings['add_text_under_link'] && $this->btn_settings['add_text_under_link'] != "") ? $this->btn_settings['add_text_under_link'] : 'You may create and manage courses', TNA_PLUGIN_NAME) . ': <a href="' . esc_url(admin_url('edit.php?post_type=' . $post_type[0])) . '" target="_blank">' . esc_html__('here', TNA_PLUGIN_NAME) . '</a>.</p>'
        );

        $args = array(
            'post_type' => $post_type,
            'posts_per_page' => -1
        );

        $the_query = new \WP_Query($args);

        if ($the_query->have_posts()) {
            while ($the_query->have_posts()) {
                $the_query->the_post();

                $title = get_the_title();
                $id = get_the_ID();
                $link = get_the_permalink();
                $content = get_post_meta(get_the_ID(), $this->data_prefix . 'desc', true);

                array_push($custom_post_data,
                    array(
                        'text' => $title,
                        'value' => strval($id),
                        'link' => $link,
                        'content' => $content
                    ));
            }

            $response = array(
                'data' => array_reverse($custom_post_data),
                'popup_data' => array_reverse($popup_data)
            );

            wp_send_json_success(json_encode($response));
        } else {
            wp_send_json_error(json_encode(
                array(
                    'popup_data' => array_reverse($popup_data)
                )
            ));
        }

        wp_reset_query();
    }
}