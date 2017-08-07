<?php

/**
 * Fired during plugin activation
 *
 * @link       https://philsbury.uk
 * @since      1.0.0
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Age_Gate
 * @subpackage Age_Gate/includes
 * @author     Phil Baker <phil@philsbury.co.uk>
 */
class Age_Gate_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 * 
	 */
	public static function activate() {

    $locale = get_locale();
    // 
    // 
    $defaults = array(
	    "wp_age_gate_min_age" => ($locale == 'en_GB') ? 18 : 21,
	    "wp_age_gate_restriction_type" => 'all',
	    "wp_age_gate_input_type" => 'inputs',
	    "wp_age_gate_remember" => 1,
	    "wp_age_gate_date_format" => ($locale == 'en_GB') ? 'ddmmyyyy' : 'mmddyyyy',
	    "wp_age_gate_button_text" => 'Submit',
	    'wp_age_gate_restrict_register' => 1,
	    "wp_age_gate_styling" => 1,
	    "wp_age_gate_invalid_input_msg" => "Your input was invalid",
			"wp_age_gate_under_age_msg" => "You are not old enough to view this content",
			"wp_age_gate_generic_error_msg" => "An error occurred, please try again",
			"wp_age_gate_device_width" => 1,
			"wp_age_gate_use_js" => "standard"
			
	  );
		

		// get any user settings so we don't override
	  $user_settings = get_option('wp_age_gate_general', array());	

		update_option('wp_age_gate_general', array_merge($defaults, $user_settings));
	}

}
