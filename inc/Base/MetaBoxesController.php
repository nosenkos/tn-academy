<?php
/**
 * Created by PhpStorm.
 * User: sergeynosenko
 * Date: 21.09.2018
 * Time: 11:51
 */

namespace Inc\Base;

use Inc\Base\MetaBoxController;
use Inc\Base\CustomPostTypeController;


class MetaBoxesController extends BaseController
{
    /**
     * Class constructor.
     */

    public $meta_box;

    public $post_types = array();

    public function register()
    {

        $this->post_types = CustomPostTypeController::getPostTypes();

        // Add metaboxes
        $this->registerMetaBoxes();

    }

    public function registerMetaBoxes()
    {
        // BaseController::data_prefix
        $prefix = $this->data_prefix;

        $args = array(
            array(
                'type' => 'heading',
                'title' => __('Required', TNA_PLUGIN_NAME)
            ),
            array(
                'title' => __('Title', TNA_PLUGIN_NAME),
                'desc' => __('Enter the course\'s title.', TNA_PLUGIN_NAME),
                'id' => $prefix . 'title',
                'type' => 'text',
                'validate' => array(
                    'required' => true
                ),
            ),
            array(
                'title' => __('Date', TNA_PLUGIN_NAME),
                'desc' => __('Enter the course\'s date.', TNA_PLUGIN_NAME),
                'id' => $prefix . 'date',
                'type' => 'text',
                'validate' => array(
                    'required' => true
                ),
            ),
            array(
                'title' => __('Location', TNA_PLUGIN_NAME),
                'desc' => __('Enter the course\'s location.', TNA_PLUGIN_NAME),
                'id' => $prefix . 'location',
                'type' => 'text',
                'validate' => array(
                    'required' => true
                ),
            ),
            array(
                'title' => __('Course Leader', TNA_PLUGIN_NAME),
                'desc' => __('Enter the course\'s course Leader.', TNA_PLUGIN_NAME),
                'id' => $prefix . 'course-leader',
                'type' => 'text',
                'validate' => array(
                    'required' => true
                ),
            ),
            array(
                'title' => __('Price', TNA_PLUGIN_NAME),
                'desc' => __('Enter the course\'s course Price.', TNA_PLUGIN_NAME),
                'id' => $prefix . 'price',
                'type' => 'text',
                'validate' => array(
                    'required' => true
                ),
            ),
            array(
                'type' => 'heading',
                'title' => __('Optional', TNA_PLUGIN_NAME),
                'desc' => __('Configure the form fields. You may disable fields that are not required just leave them empty.', TNA_PLUGIN_NAME)
            ),
            array(
                'title' => __('Short description', TNA_PLUGIN_NAME),
                'desc' => __('The course\'s short description.', TNA_PLUGIN_NAME),
                'id' => $prefix . 'desc',
                'type' => 'textarea',
            ),
            array(
                'title' => __('Last day of registration', TNA_PLUGIN_NAME),
                'desc' => __('Enter the course\'s Last day of registration.', TNA_PLUGIN_NAME),
                'id' => $prefix . 'last-date',
                'type' => 'text',
            ),
            array(
                'title' => __('Detailed Description', TNA_PLUGIN_NAME),
                'desc' => __('Enter the course\'s Detailed Description.', TNA_PLUGIN_NAME),
                'id' => $prefix . 'det-desc',
                'type' => 'wysiwyg',
            )
        );

        if ($this->activated('member_manager')) {
            $args[] = array(
                'type' => 'heading',
                'title' => __('Settings', TNA_PLUGIN_NAME)
            );
            $args[] = array(
                'id' => $prefix . 'show-form',
                'title' => __('Registration Form', TNA_PLUGIN_NAME),
                'desc' => __('Enable Registration Form?', TNA_PLUGIN_NAME),
                'type' => 'checkbox'
            );
        }


        foreach ($this->post_types as $post_type) {
            $this->meta_box = new MetaBoxController();
            $this->meta_box->register(
                'tn-course-settings',
                __('Course Settings', TNA_PLUGIN_NAME),
                $post_type['post_type'],
                'normal',
                'high',
                $args
            );
        }

    }
}