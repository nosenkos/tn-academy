<?php
/**
 * @package  tn_academyPlugin
 */

namespace Inc\Base;

use Inc\Base\BaseController;
use Inc\Base\CustomPostTypeController;

/**
 *
 */
class TemplateController extends BaseController
{
    private $is_cpt = false;

    public $templates;

    public function register()
    {
        $this->templates = array(
            'page-templates/tn-academy-landing-page.php' => __('TN Academy Landing Page', TNA_PLUGIN_NAME)
        );

        add_filter('theme_page_templates', array($this, 'custom_template'));
        add_filter('template_include', array($this, 'load_template'));
    }

    public function custom_template($templates)
    {
        $templates = array_merge($templates, $this->templates);

        return $templates;
    }

    public function load_template($template)
    {
        global $post;

        if (!$post) {
            return $template;
        }

        $posts_type = CustomPostTypeController::getPostTypes();

        foreach ($posts_type as $item) {
            if (is_singular($item['post_type'])) {
                $this->is_cpt = true;

            }
        }

        //If is the course page, load a custom template
        if ($this->is_cpt) {
            $file = $this->plugin_path . 'page-templates/single-cpt.php';

            if (file_exists($file)) {
                return $file;
            }
        }

        // Add template to Page
        $template_name = get_post_meta($post->ID, '_wp_page_template', true);

        if (!isset($this->templates[$template_name])) {
            return $template;
        }

        $file = $this->plugin_path . $template_name;

        if (file_exists($file)) {
            return $file;
        }

        return $template;
    }
}