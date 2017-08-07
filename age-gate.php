<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://philsbury.uk
 * @since             1.0.0
 * @package           Age_Gate
 *
 * @wordpress-plugin
 * Plugin Name:       Age Gate
 * Plugin URI:        https://philsbury.uk/wordpress-age-gate-plugin
 * Description:       A customisable age gate to block content from younger people
 * Version:           1.4.1
 * Author:            Phil Baker
 * Author URI:        https://philsbury.uk
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       age-gate
 * Domain Path:       /languages
 * Bitbucket Plugin URI: https://bitbucket.org/asylumdigital/wp-age-gate/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('AGE_GATE_DIR', plugin_dir_path(__FILE__));
define( 'AGE_GATE_VER', '1.4.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-age-gate-activator.php
 */
function activate_age_gate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-age-gate-activator.php';
	Age_Gate_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-age-gate-deactivator.php
 */
function deactivate_age_gate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-age-gate-deactivator.php';
	Age_Gate_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_age_gate' );
register_deactivation_hook( __FILE__, 'deactivate_age_gate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-age-gate.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_age_gate() {

	$plugin = new Age_Gate();
	$plugin->run();

}
run_age_gate();
