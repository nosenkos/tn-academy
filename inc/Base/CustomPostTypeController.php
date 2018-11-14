<?php
/**
 * @package  tn_academyPlugin
 */

namespace Inc\Base;

use Inc\Api\SettingsApi;
use Inc\Base\BaseController;
use Inc\Api\Callbacks\CptCallbacks;
use Inc\Api\Callbacks\AdminCallbacks;

/**
 *
 */
class CustomPostTypeController extends BaseController
{
    public $settings;

    public $callbacks;

    public $cpt_callbacks;

    public $subpages = array();

    public $custom_post_types = array();

    public $prefix;

    public function register()
    {
        if (!$this->activated('cpt_manager')) return;

        $this->settings = new SettingsApi();

        $this->callbacks = new AdminCallbacks();

        $this->cpt_callbacks = new CptCallbacks();

        // BaseController::data_prefix
        $this->prefix = $this->data_prefix;

        $this->setSubpages();

        $this->setSettings();

        $this->setSections();

        $this->setFields();

        $this->settings->addSubMenuPages($this->subpages)->register();

        $this->storeCustomPostTypes();

        if (!empty($this->custom_post_types)) {
            add_action('init', array($this, 'registerCustomPostTypes'));
        }

        $posts_type = self::getPostTypes();
        foreach ($posts_type as $post_type) {
            add_action('manage_' . $post_type['post_type'] . '_posts_columns', array($this, 'set_custom_columns'));
            add_action('manage_' . $post_type['post_type'] . '_posts_custom_column', array($this, 'set_custom_columns_data'), 10, 2);
            add_filter('manage_edit-' . $post_type['post_type'] . '_sortable_columns', array($this, 'set_custom_columns_sortable'));
        }
        add_filter('request', array($this, 'tn_column_orderby'));
    }

    public function setSubpages()
    {
        $this->subpages = array(
            array(
                'parent_slug' => 'tn_academy_plugin',
                'page_title' => __('Custom Post Types', TNA_PLUGIN_NAME),
                'menu_title' => __('CPT Manager', TNA_PLUGIN_NAME),
                'capability' => 'manage_options',
                'menu_slug' => 'tn_academy_cpt',
                'callback' => array($this->callbacks, 'adminCpt')
            )
        );
    }

    public function setSettings()
    {
        $args = array(
            array(
                'option_group' => 'tn_academy_plugin_cpt_settings',
                'option_name' => 'tn_academy_plugin_cpt',
                'callback' => array($this->cpt_callbacks, 'cptSanitize')
            )
        );

        $this->settings->setSettings($args);
    }

    public function setSections()
    {
        $args = array(
            array(
                'id' => 'tn_academy_cpt_index',
                'title' => __('Custom Post Type Manager', TNA_PLUGIN_NAME),
                'callback' => array($this->cpt_callbacks, 'cptSectionManager'),
                'page' => 'tn_academy_cpt'
            )
        );

        $this->settings->setSections($args);
    }

    public function setFields()
    {
        $args = array(
            array(
                'id' => 'post_type',
                'title' => __('Custom Post Type ID', TNA_PLUGIN_NAME),
                'callback' => array($this->cpt_callbacks, 'textField'),
                'page' => 'tn_academy_cpt',
                'section' => 'tn_academy_cpt_index',
                'args' => array(
                    'option_name' => 'tn_academy_plugin_cpt',
                    'label_for' => 'post_type',
                    'placeholder' => 'eg. product',
                    'array' => 'post_type'
                )
            ),
            array(
                'id' => 'singular_name',
                'title' => __('Singular Name', TNA_PLUGIN_NAME),
                'callback' => array($this->cpt_callbacks, 'textField'),
                'page' => 'tn_academy_cpt',
                'section' => 'tn_academy_cpt_index',
                'args' => array(
                    'option_name' => 'tn_academy_plugin_cpt',
                    'label_for' => 'singular_name',
                    'placeholder' => 'eg. Product',
                    'array' => 'post_type'
                )
            ),
            array(
                'id' => 'plural_name',
                'title' => __('Plural Name', TNA_PLUGIN_NAME),
                'callback' => array($this->cpt_callbacks, 'textField'),
                'page' => 'tn_academy_cpt',
                'section' => 'tn_academy_cpt_index',
                'args' => array(
                    'option_name' => 'tn_academy_plugin_cpt',
                    'label_for' => 'plural_name',
                    'placeholder' => 'eg. Products',
                    'array' => 'post_type'
                )
            ),
            array(
                'id' => 'public',
                'title' => __('Public', TNA_PLUGIN_NAME),
                'callback' => array($this->cpt_callbacks, 'checkboxField'),
                'page' => 'tn_academy_cpt',
                'section' => 'tn_academy_cpt_index',
                'args' => array(
                    'option_name' => 'tn_academy_plugin_cpt',
                    'label_for' => 'public',
                    'class' => 'ui-toggle',
                    'array' => 'post_type'
                )
            ),
            array(
                'id' => 'has_archive',
                'title' => __('Archive', TNA_PLUGIN_NAME),
                'callback' => array($this->cpt_callbacks, 'checkboxField'),
                'page' => 'tn_academy_cpt',
                'section' => 'tn_academy_cpt_index',
                'args' => array(
                    'option_name' => 'tn_academy_plugin_cpt',
                    'label_for' => 'has_archive',
                    'class' => 'ui-toggle',
                    'array' => 'post_type'
                )
            )
        );

        $this->settings->setFields($args);
    }

    public function storeCustomPostTypes()
    {
        $options = get_option('tn_academy_plugin_cpt') ?: array();

        foreach ($options as $option) {

            $this->custom_post_types[] = array(
                'post_type' => $option['post_type'],
                'name' => $option['plural_name'],
                'singular_name' => $option['singular_name'],
                'menu_name' => $option['plural_name'],
                'name_admin_bar' => $option['singular_name'],
                'archives' => $option['singular_name'] . ' Archives',
                'attributes' => $option['singular_name'] . ' Attributes',
                'parent_item_colon' => 'Parent ' . $option['singular_name'],
                'all_items' => 'All ' . $option['plural_name'],
                'add_new_item' => 'Add New ' . $option['singular_name'],
                'add_new' => 'Add New',
                'new_item' => 'New ' . $option['singular_name'],
                'edit_item' => 'Edit ' . $option['singular_name'],
                'update_item' => 'Update ' . $option['singular_name'],
                'view_item' => 'View ' . $option['singular_name'],
                'view_items' => 'View ' . $option['plural_name'],
                'search_items' => 'Search ' . $option['plural_name'],
                'not_found' => 'No ' . $option['singular_name'] . ' Found',
                'not_found_in_trash' => 'No ' . $option['singular_name'] . ' Found in Trash',
                'featured_image' => 'Featured Image',
                'set_featured_image' => 'Set Featured Image',
                'remove_featured_image' => 'Remove Featured Image',
                'use_featured_image' => 'Use Featured Image',
                'insert_into_item' => 'Insert into ' . $option['singular_name'],
                'uploaded_to_this_item' => 'Upload to this ' . $option['singular_name'],
                'items_list' => $option['plural_name'] . ' List',
                'items_list_navigation' => $option['plural_name'] . ' List Navigation',
                'filter_items_list' => 'Filter' . $option['plural_name'] . ' List',
                'label' => $option['singular_name'],
                'description' => $option['plural_name'] . 'Custom Post Type',
                'supports' => array('title', 'thumbnail'),
                'taxonomies' => false, //array('category', 'post_tag'),
                'hierarchical' => false,
                'public' => isset($option['public']) ?: false,
                'show_ui' => true,
                'show_in_menu' => true,
                'menu_position' => 5,
                'menu_icon' => 'dashicons-welcome-learn-more',
                'show_in_admin_bar' => true,
                'show_in_nav_menus' => true,
                'can_export' => true,
                'has_archive' => isset($option['has_archive']) ?: false,
                'exclude_from_search' => false,
                'publicly_queryable' => true,
                'capability_type' => 'post'
            );
        }
    }

    public function registerCustomPostTypes()
    {
        foreach ($this->custom_post_types as $post_type) {
            register_post_type($post_type['post_type'],
                array(
                    'labels' => array(
                        'name' => __($post_type['name'], TNA_PLUGIN_NAME),
                        'singular_name' => __($post_type['singular_name'], TNA_PLUGIN_NAME),
                        'menu_name' => __($post_type['menu_name'], TNA_PLUGIN_NAME),
                        'name_admin_bar' => $post_type['name_admin_bar'],
                        'archives' => $post_type['archives'],
                        'attributes' => $post_type['attributes'],
                        'parent_item_colon' => $post_type['parent_item_colon'],
                        'all_items' => $post_type['all_items'],
                        'add_new_item' => $post_type['add_new_item'],
                        'add_new' => $post_type['add_new'],
                        'new_item' => $post_type['new_item'],
                        'edit_item' => $post_type['edit_item'],
                        'update_item' => $post_type['update_item'],
                        'view_item' => $post_type['view_item'],
                        'view_items' => $post_type['view_items'],
                        'search_items' => $post_type['search_items'],
                        'not_found' => $post_type['not_found'],
                        'not_found_in_trash' => $post_type['not_found_in_trash'],
                        'featured_image' => $post_type['featured_image'],
                        'set_featured_image' => $post_type['set_featured_image'],
                        'remove_featured_image' => $post_type['remove_featured_image'],
                        'use_featured_image' => $post_type['use_featured_image'],
                        'insert_into_item' => $post_type['insert_into_item'],
                        'uploaded_to_this_item' => $post_type['uploaded_to_this_item'],
                        'items_list' => $post_type['items_list'],
                        'items_list_navigation' => $post_type['items_list_navigation'],
                        'filter_items_list' => $post_type['filter_items_list']
                    ),
                    'label' => __($post_type['label'], TNA_PLUGIN_NAME),
                    'description' => $post_type['description'],
                    'supports' => $post_type['supports'],
                    'taxonomies' => $post_type['taxonomies'],
                    'hierarchical' => $post_type['hierarchical'],
                    'public' => $post_type['public'],
                    'show_ui' => $post_type['show_ui'],
                    'show_in_menu' => $post_type['show_in_menu'],
                    'menu_position' => $post_type['menu_position'],
                    'menu_icon' => $post_type['menu_icon'],
                    'show_in_admin_bar' => $post_type['show_in_admin_bar'],
                    'show_in_nav_menus' => $post_type['show_in_nav_menus'],
                    'can_export' => $post_type['can_export'],
                    'has_archive' => $post_type['has_archive'],
                    'exclude_from_search' => $post_type['exclude_from_search'],
                    'publicly_queryable' => $post_type['publicly_queryable'],
                    'capability_type' => $post_type['capability_type']
                )
            );
        }

        flush_rewrite_rules();
    }

    public static function getPostTypes()
    {
        $options = get_option('tn_academy_plugin_cpt') ?: array();

        $post_types = array();

        foreach ($options as $option) {

            $post_types[] = array(
                'post_type' => $option['post_type'],
                'plural_name' => $option['plural_name'],
                'singular_name' => $option['singular_name']
            );
        }

        return $post_types;
    }

    public function set_custom_columns($columns)
    {
        $title = $columns['title'];
        $date = $columns['date'];
        unset($columns['title'], $columns['date']);

        $columns['title'] = $title;
        $columns[$this->prefix . 'course-leader'] = __('Course Leader', TNA_PLUGIN_NAME);
        $columns[$this->prefix . 'price'] = __('Price', TNA_PLUGIN_NAME);
        $columns[$this->prefix . 'date'] = __('Start Date', TNA_PLUGIN_NAME);
        $columns[$this->prefix . 'last-date'] = __('Last day of registration', TNA_PLUGIN_NAME);
        $columns['date'] = $date;

        return $columns;
    }

    public function set_custom_columns_data($column, $post_id)
    {
        $date = get_post_meta($post_id, $this->prefix . 'date', true);
        $c_leader = get_post_meta($post_id, $this->prefix . 'course-leader', true);
        $price = get_post_meta($post_id, $this->prefix . 'price', true);
        $last_date = get_post_meta($post_id, $this->prefix . 'last-date', true);
        $date = isset($date) ? $date : '-';
        $c_leader = isset($c_leader) ? $c_leader : '-';
        $price = isset($price) ? $price : '-';
        $last_date = isset($last_date) ? $last_date : '-';

        switch ($column) {
            case $this->prefix . 'date':
                echo $date;
                break;

            case $this->prefix . 'course-leader':
                echo $c_leader;
                break;

            case $this->prefix . 'price':
                echo $price;
                break;

            case $this->prefix . 'last-date':
                echo $last_date;
                break;
        }
    }

    public function set_custom_columns_sortable($columns)
    {

        $columns[$this->prefix . 'course-leader'] = $this->prefix . 'course-leader';
        $columns[$this->prefix . 'price'] = $this->prefix . 'price';
        $columns[$this->prefix . 'date'] = $this->prefix . 'date';
        $columns[$this->prefix . 'last-date'] = $this->prefix . 'last-date';

        return $columns;
    }

    public function tn_column_orderby($vars)
    {
        if (isset($vars['orderby']) && $this->prefix . 'course-leader' == $vars['orderby']) {
            $vars = array_merge($vars, array(
                'meta_key' => $this->prefix . 'course-leader',
                'orderby' => 'meta_value'
            ));
        } elseif (isset($vars['orderby']) && $this->prefix . 'price' == $vars['orderby']) {
            $vars = array_merge($vars, array(
                'meta_key' => $this->prefix . 'price',
                'orderby' => 'meta_value_num'
            ));
        } elseif (isset($vars['orderby']) && $this->prefix . 'date' == $vars['orderby']) {
            $vars = array_merge($vars, array(
                'meta_key' => $this->prefix . 'date',
                'orderby' => 'meta_value_num'
            ));
        } elseif (isset($vars['orderby']) && $this->prefix . 'last-date' == $vars['orderby']) {
            $vars = array_merge($vars, array(
                'meta_key' => $this->prefix . 'last-date',
                'orderby' => 'meta_value_num'
            ));
        }

        return $vars;
    }
}