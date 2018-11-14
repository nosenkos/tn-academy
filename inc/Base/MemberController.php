<?php
/**
 * @package  tn_academyPlugin
 */

namespace Inc\Base;

use Braintree\Exception;
use Inc\Api\SettingsApi;
use Inc\Base\BaseController;
use Inc\Api\Callbacks\MemberCallbacks;
use Inc\Base\MailController;
use Inc\Base\CustomPostTypeController;

/**
 *
 */
class MemberController extends BaseController
{
    public $settings;

    public $callbacks;

    public $mail;

    public $subpages = array();

    public function register()
    {
        if (!$this->activated('member_manager')) return;

        $this->settings = new SettingsApi();

        $this->callbacks = new MemberCallbacks();

        $this->mail = new MailController();

        $this->setDownloadPage();

        add_action('init', array($this, 'member_cpt'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_box'));
        add_action('manage_member_posts_columns', array($this, 'set_custom_columns'));
        add_action('manage_member_posts_custom_column', array($this, 'set_custom_columns_data'), 10, 2);

        add_action('restrict_manage_posts', array($this, 'tn_admin_order_by_course_top_bar_button'));
        add_filter('request', array($this, 'tn_filter_by_course'));

        $this->settings->addSubMenuPages($this->subpages)->register();

        add_action('wp_ajax_submit_member', array($this, 'submit_member'));
        add_action('wp_ajax_nopriv_submit_member', array($this, 'submit_member'));
    }

    public function submit_member()
    {
        if (!DOING_AJAX || !check_ajax_referer('member-nonce', 'nonce')) {
            return $this->return_json('error');
        }

        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $address = sanitize_text_field($_POST['address']);
        $zip = sanitize_text_field($_POST['zip']);
        $city = sanitize_text_field($_POST['city']);
        $phone = sanitize_text_field($_POST['phone']);
        $email = sanitize_email($_POST['email']);
        $message = sanitize_textarea_field($_POST['message']);
        $course = sanitize_text_field($_POST['course']);
        $course_id = sanitize_text_field($_POST['course_id']);

        $data = array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'address' => $address,
            'zip' => $zip,
            'city' => $city,
            'phone' => $phone,
            'message' => $message,
            'email' => $email,
            'course' => $course,
            'course_id' => $course_id
        );

        $args = array(
            'post_title' => $first_name . ' ' . $last_name,
            'post_author' => 1,
            'post_status' => 'publish',
            'post_type' => 'member',
            'meta_input' => array(
                '_tn_academy_member_key' => $data
            )
        );

        $postID = wp_insert_post($args);

        if ($postID) {
            try {
                $this->mail->register($data);
            } catch (Exception $e) {
                error_log(print_r($e->getMessage(), true));
            }
            return $this->return_json('success');
        }

        return $this->return_json('error');
    }

    public function return_json($status)
    {
        $return = array(
            'status' => $status
        );
        wp_send_json($return);

        wp_die();
    }

    public function setDownloadPage()
    {
        $this->subpages = array(
            array(
                'parent_slug' => 'tn_academy_plugin',
                'page_title' => __('Download', TNA_PLUGIN_NAME),
                'menu_title' => __('Download', TNA_PLUGIN_NAME),
                'capability' => 'manage_options',
                'menu_slug' => 'tn-member-download-list',
                'callback' => array($this->callbacks, 'downloadPage')
            )
        );
    }

    public function member_cpt()
    {
        $labels = array(
            'name' => __('Participants', TNA_PLUGIN_NAME),
            'singular_name' => __('Participant', TNA_PLUGIN_NAME)
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => false,
            'menu_icon' => 'dashicons-testimonial',
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'supports' => array('title'),
            'show_in_menu' => 'tn_academy_plugin',
            'menu_position' => 10,
            'capabilities' => array(
                'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
            ),
            'map_meta_cap' => true,
        );

        register_post_type('member', $args);
    }

    public function add_meta_boxes()
    {
        add_meta_box(
            'member_info',
            __('Member Information', TNA_PLUGIN_NAME),
            array($this, 'render_member_box'),
            'member',
            'normal',
            'high'
        );
    }

    public function render_member_box($post)
    {
        wp_nonce_field('tn_academy_member', 'tn_academy_member_nonce');

        $data = get_post_meta($post->ID, '_tn_academy_member_key', true);
        $first_name = isset($data['first_name']) ? $data['first_name'] : '';
        $last_name = isset($data['last_name']) ? $data['last_name'] : '';
        $address = isset($data['address']) ? $data['address'] : '';
        $zip = isset($data['zip']) ? $data['zip'] : '';
        $city = isset($data['city']) ? $data['city'] : '';
        $phone = isset($data['phone']) ? $data['phone'] : '';
        $message = isset($data['message']) ? $data['message'] : '';
        $email = isset($data['email']) ? $data['email'] : '';
        $course = isset($data['course']) ? $data['course'] : '';
        ?>
        <p>
            <label class="meta-label"
                   for="tn_academy_member_course"><?php echo __('Course', TNA_PLUGIN_NAME); ?></label>
            <input type="text" id="tn_academy_member_course" name="tn_academy_member_course" class="widefat"
                   value="<?php echo esc_attr($course); ?>">
        </p>
        <p>
            <label class="meta-label"
                   for="tn_academy_member_first_name"><?php echo __('First Name', TNA_PLUGIN_NAME); ?></label>
            <input type="text" id="tn_academy_member_first_name" name="tn_academy_member_first_name" class="widefat"
                   value="<?php echo esc_attr($first_name); ?>">
        </p>
        <p>
            <label class="meta-label"
                   for="tn_academy_member_last_name"><?php echo __('Last Name', TNA_PLUGIN_NAME); ?></label>
            <input type="text" id="tn_academy_member_last_name" name="tn_academy_member_last_name" class="widefat"
                   value="<?php echo esc_attr($last_name); ?>">
        </p>
        <p>
            <label class="meta-label"
                   for="tn_academy_member_address"><?php echo __('Address', TNA_PLUGIN_NAME); ?></label>
            <input type="text" id="tn_academy_member_address" name="tn_academy_member_address" class="widefat"
                   value="<?php echo esc_attr($address); ?>">
        </p>
        <p>
            <label class="meta-label" for="tn_academy_member_zip"><?php echo __('ZIP', TNA_PLUGIN_NAME); ?></label>
            <input type="text" id="tn_academy_member_zip" name="tn_academy_member_zip" class="widefat"
                   value="<?php echo esc_attr($zip); ?>">
        </p>
        <p>
            <label class="meta-label" for="tn_academy_member_city"><?php echo __('City', TNA_PLUGIN_NAME); ?></label>
            <input type="text" id="tn_academy_member_city" name="tn_academy_member_city" class="widefat"
                   value="<?php echo esc_attr($city); ?>">
        </p>
        <p>
            <label class="meta-label" for="tn_academy_member_phone"><?php echo __('Phone', TNA_PLUGIN_NAME); ?></label>
            <input type="text" id="tn_academy_member_phone" name="tn_academy_member_phone" class="widefat"
                   value="<?php echo esc_attr($phone); ?>">
        </p>
        <p>
            <label class="meta-label"
                   for="tn_academy_member_email"><?php echo __('Author Email', TNA_PLUGIN_NAME); ?></label>
            <input type="email" id="tn_academy_member_email" name="tn_academy_member_email" class="widefat"
                   value="<?php echo esc_attr($email); ?>">
        </p>
        <p>
            <label class="meta-label"
                   for="tn_academy_member_message"><?php echo __('Short Description', TNA_PLUGIN_NAME); ?></label>
            <textarea name="tn_academy_member_message" id="tn_academy_member_message"
                      class="widefat"><?php echo esc_attr($message); ?></textarea>
        </p>
        <?php
    }

    public function save_meta_box($post_id)
    {
        if (!isset($_POST['tn_academy_member_nonce'])) {
            return $post_id;
        }

        $nonce = $_POST['tn_academy_member_nonce'];
        if (!wp_verify_nonce($nonce, 'tn_academy_member')) {
            return $post_id;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }

        $data = array(
            'first_name' => sanitize_text_field($_POST['tn_academy_member_first_name']),
            'last_name' => sanitize_text_field($_POST['tn_academy_member_last_name']),
            'address' => sanitize_text_field($_POST['tn_academy_member_address']),
            'zip' => sanitize_text_field($_POST['tn_academy_member_zip']),
            'city' => sanitize_text_field($_POST['tn_academy_member_city']),
            'phone' => sanitize_text_field($_POST['tn_academy_member_phone']),
            'email' => sanitize_email($_POST['tn_academy_member_email']),
            'message' => sanitize_text_field($_POST['tn_academy_member_message']),
            'course' => sanitize_text_field($_POST['tn_academy_member_course']),
        );
        update_post_meta($post_id, '_tn_academy_member_key', $data);
    }

    public function set_custom_columns($columns)
    {
        $date = $columns['date'];
        unset($columns['date']);

        $columns['email'] = __('Email', TNA_PLUGIN_NAME);
        $columns['course'] = __('Course', TNA_PLUGIN_NAME);
        $columns['date'] = $date;

        return $columns;
    }

    public function set_custom_columns_data($column, $post_id)
    {
        $data = get_post_meta($post_id, '_tn_academy_member_key', true);
        $course = isset($data['course']) ? $data['course'] : '';
        $email = isset($data['email']) ? $data['email'] : '';

        switch ($column) {
            case 'email':
                echo '<a href="mailto:' . $email . '">' . $email . '</a>';
                break;

            case 'course':
                echo $course;
                break;
        }
    }

    public function tn_admin_order_by_course_top_bar_button()
    {
        $type_post = "";
        if (isset($_GET['post_type'])) {
            $type_post = $_GET['post_type'];
        }

        $post_types = CustomPostTypeController::getPostTypes();
        $post_type = array();
        $posts = array();
        $count_posts = count($post_types);
        $post_titles = "";
        $i = 0;
        foreach ($post_types as $row) {
            $i += 1;
            $post_type[] = $row['post_type'];
            if ($i < $count_posts) {
                $post_titles .= $row['plural_name'] . ", ";
            } elseif ($i == $count_posts) {
                $post_titles .= $row['plural_name'];
            }
        }
        if ($post_type):
            $posts = get_posts(array(
                'post_type' => $post_type,
                'numberposts' => -1
            ));
        endif;

        //only add filter to post type you want
        if ('member' == $type_post) {
            //change this to the list of values you want to show
            //in 'label' => 'value' format
            $values = array();
            if ($posts) {
                foreach ($posts as $value) {
                    $values[$value->post_title] = $value->post_title;
                }
            }

            ?>
            <select name="filter_by_course">
                <option value=""><?php echo ($post_titles) ? __('All ' . $post_titles, TNA_PLUGIN_NAME) : __('All Courses', TNA_PLUGIN_NAME); ?></option>
                <?php
                $current_v = isset($_GET['filter_by_course']) ? $_GET['filter_by_course'] : '';
                foreach ($values as $label => $value) {
                    printf
                    (
                        '<option value="%s"%s>%s</option>',
                        $value,
                        $value == $current_v ? ' selected="selected"' : '',
                        $label
                    );
                }
                ?>
            </select>
            <?php
        }
    }

    public function tn_filter_by_course($vars)
    {
        global $pagenow;
        $type = 'post';
        if (isset($_GET['post_type'])) {
            $type = $_GET['post_type'];
        }
        if ('member' == $type && is_admin() && $pagenow == 'edit.php' && isset($_GET['filter_by_course']) && $_GET['filter_by_course'] != '') {
            $vars = array_merge($vars, array(
                'meta_query' => array(
                    array(
                        'key' => '_tn_academy_member_key',
                        'value' => serialize(strval($_GET['filter_by_course'])),
                        'compare' => 'LIKE'
                    )
                ),
            ));
        }

        return $vars;
    }
}