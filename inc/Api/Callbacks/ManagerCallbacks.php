<?php
/**
 * @package  tn_academyPlugin
 */

namespace Inc\Api\Callbacks;

use Inc\Base\BaseController;

class ManagerCallbacks extends BaseController
{
    public function checkboxSanitize($input)
    {
        $output = array();

        foreach ($this->managers as $key => $value) {
            $output[$key] = isset($input[$key]) ? true : false;
        }

        return $output;
    }

    public function btnSanitize($input)
    {

        $output = array();

        foreach ($input as $key => $value) {
            $output[$key] = esc_html($value);
        }

        return $output;
    }

    public function mailSanitize($input)
    {
        $output = array();

        foreach ($input as $key => $value) {
            $output[$key] = esc_html($value);
        }

        return $output;
    }

    public function adminSectionManager()
    {
        echo __('Manage the Sections and Features of this Plugin by activating the checkboxes from the following list.', TNA_PLUGIN_NAME);
    }

    public function adminSectionBtnManager()
    {
        echo __('Manage the Sections and Features of this Plugin by fill fields from the following list.', TNA_PLUGIN_NAME);
    }

    public function adminSectionMailManager()
    {
        echo __('Manage the Mail of this Plugin by fill fields from the following list.', TNA_PLUGIN_NAME);
    }

    public function checkboxField($args)
    {
        $name = $args['label_for'];
        $classes = $args['class'];
        $option_name = $args['option_name'];
        $checkbox = get_option($option_name);
        $checked = isset($checkbox[$name]) ? ($checkbox[$name] ? true : false) : false;

        echo '<div class="' . $classes . '"><input type="checkbox" id="' . $name . '" name="' . $option_name . '[' . $name . ']" value="1" class="" ' . ($checked ? 'checked' : '') . '><label for="' . $name . '"><div></div></label></div>';
    }

    public function textField($args)
    {
        $name = $args['label_for'];
        $classes = $args['class'];
        $placeholder = $args['placeholder'];
        $option_name = $args['option_name'];
        $value = get_option($option_name)[$name];
        $admin_email = (isset($args['admin_email']) && !empty($args['admin_email']) && $args['admin_email'] != "") ? "<span>&lt;" . $args['admin_email'] . "&gt; &lt;--" . __('Admin Email', TNA_PLUGIN_NAME) . "</span>" : "";

        echo '<div class="' . $classes . '"><input type="text" class="regular-text" id="' . $name . '" name="' . $option_name . '[' . $name . ']" value="' . $value . '" placeholder="' . __($placeholder, TNA_PLUGIN_NAME) . '"> ' . __($admin_email, TNA_PLUGIN_NAME) . '</div>';
    }

    public function selectField($args)
    {
        $html = "";

        $name = $args['label_for'];
        $classes = $args['class'];
        $option_name = $args['option_name'];
        $list = $args['selectFields'];
        $select = get_option($option_name)[$name];

        $html .= '<div class="' . $classes . '"><select id="' . $name . '" name="' . $option_name . '[' . $name . ']" >';
        $html .= '<option value="" ' . (($select == "") ? 'selected="selected"' : '') . '>--' . __('Default', TNA_PLUGIN_NAME) . '--</option>';
        foreach ($list as $row):
            $html .= '<option value="' . $row['value'] . '" ' . (($select == $row['value']) ? 'selected="selected"' : '') . '>' . $row['text'] . '</option>';
        endforeach;
        $html .= '</select>';
        $html .= ' <span>' . __('Default leads on Front Page of your site.', TNA_PLUGIN_NAME) . '</span>';
        $html .= '</div>';

        echo $html;
    }

    public function fileUpload($args)
    {
        $html = "";

        $name = $args['label_for'];
        $classes = $args['class'];
        $option_name = $args['option_name'];
        $value = get_option($option_name)[$name];


        $html .= '<div class="' . $classes . '">';
        $html .= '<input type="text" class="regular-text widefat image-upload" id="' . $name . '" name="' . $option_name . '[' . $name . ']" value="' . $value . '">';
        $html .= '<button type="button" class="button button-primary js-image-upload">' . __("Select Image", TNA_PLUGIN_NAME) . '</button>';
        $html .= ' <span>' . __('Image should have resolution 440x94px.', TNA_PLUGIN_NAME) . '</span>';
        $html .= '</div>';

        echo $html;
    }

    public function wysiwygField($args)
    {
        $html = "";

        $name = $args['label_for'];
        $classes = $args['class'];
        $option_name = $args['option_name'];
        $value = get_option($option_name)[$name];


        $html .= '<div class="' . $classes . '">';
        $html .= wp_editor(html_entity_decode($value), esc_attr($name), $settings = array('wpautop' => true, 'media_buttons' => false, 'textarea_name' => esc_attr( $option_name . '[' . $name . ']'), 'teeny' => false));
        $html .= '</div>';

        echo $html;
    }
}