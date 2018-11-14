<?php 
/**
 * @package  tn_academyPlugin
 */
namespace Inc\Api\Callbacks;

class CptCallbacks
{

	public function cptSectionManager()
	{
		echo __('Create as many Custom Post Types as you want.', TNA_PLUGIN_NAME);
	}

	public function cptSanitize( $input )
	{
		$output = get_option('tn_academy_plugin_cpt');

		if ( isset($_POST["remove"]) ) {
			unset($output[$_POST["remove"]]);

			return $output;
		}

		$input['post_type'] = str_replace(' ', '_', strtolower($this->custom_length($input['post_type'], 20)));

		if ( count($output) == 0 ) {
			$output[$input['post_type']] = $input;

			return $output;
		}

		foreach ($output as $key => $value) {
			if ($input['post_type'] === $key) {
				$output[$key] = $input;
			} else {
				$output[$input['post_type']] = $input;
			}
		}

		flush_rewrite_rules();
		
		return $output;
	}

	public function textField( $args )
	{
		$name = $args['label_for'];
		$option_name = $args['option_name'];
		$value = '';

		if ( isset($_POST["edit_post"]) ) {
			$input = get_option( $option_name );
			$value = $input[$_POST["edit_post"]][$name];
		}

		echo '<input type="text" class="regular-text" id="' . $name . '" name="' . $option_name . '[' . $name . ']" value="' . $value . '" placeholder="' . $args['placeholder'] . '" required>';
	}

	public function checkboxField( $args )
	{
		$name = $args['label_for'];
		$classes = $args['class'];
		$option_name = $args['option_name'];
		$checked = false;

		if ( isset($_POST["edit_post"]) ) {
			$checkbox = get_option( $option_name );
			$checked = isset($checkbox[$_POST["edit_post"]][$name]) ?: false;
		}

		echo '<div class="' . $classes . '"><input type="checkbox" id="' . $name . '" name="' . $option_name . '[' . $name . ']" value="1" class="" ' . ( $checked ? 'checked' : '') . '><label for="' . $name . '"><div></div></label></div>';
	}

    public function custom_length($x, $length)
    {
        if(strlen($x)<=$length)
        {
            return $x;
        }
        else
        {
            $y=substr($x,0,$length);
            return $y;
        }
    }
}