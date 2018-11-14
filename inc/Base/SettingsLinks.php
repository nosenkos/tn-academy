<?php
/**
 * @package  tn_academyPlugin
 */

namespace Inc\Base;

use Inc\Base\BaseController;

class SettingsLinks extends BaseController
{
    public function register()
    {
        add_filter("plugin_action_links_$this->plugin", array($this, 'settings_link'));
    }

    public function settings_link($links)
    {
        $settings_link = '<a href="admin.php?page=tn_academy_plugin">' . __('Settings', TNA_PLUGIN_NAME) . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
}