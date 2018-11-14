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
class Enqueue extends BaseController
{
	public function register() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

        /********* Enqueue Scripts Front-End ***********/
        add_action('wp_enqueue_scripts', array($this, 'front_enqueue'));
        add_filter('mce_css', array($this, 'plugin_mce_css'));
	}

	function enqueue() {
		// enqueue all our scripts
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_media();
		wp_enqueue_style( 'mypluginstyle', $this->plugin_url . 'assets/mystyle.css' );
		wp_enqueue_script( 'mypluginscript', $this->plugin_url . 'assets/myscript.js' );
	}

    function front_enqueue() {
	    $post_types = CustomPostTypeController::getPostTypes();
	    $post_type = array();
	    foreach ($post_types as $post){
	        $post_type[] = $post['post_type'];
        }
        // enqueue all our scripts
        wp_enqueue_style( 'tn_new_btn_plugin', $this->plugin_url . 'assets/front-style.css' );

        if(is_page_template('page-templates/tn-academy-landing-page.php')) {
            wp_enqueue_style( 'tn_template_page-css', $this->plugin_url . 'assets/template-page-style.css' );
        }

        if(is_singular($post_type)){
            wp_enqueue_style( 'bts-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css' );
            wp_enqueue_style( 'form-css', $this->plugin_url . 'assets/form.css' );

            wp_enqueue_script( 'bts-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js' ,array(), null, true);
            wp_enqueue_script( 'form-js', $this->plugin_url . 'assets/form.js' ,array(), null, true);
        }
    }

    function plugin_mce_css($mce_css)
    {
        if (!empty($mce_css))
            $mce_css .= ',';

        $mce_css .= $this->plugin_url . "assets/admin-style.css";

        return $mce_css;
    }
}