<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://philsbury.uk
 * @since      1.0.0
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Age_Gate
 * @subpackage Age_Gate/includes
 * @author     Phil Baker <phil@philsbury.co.uk>
 */
class Age_Gate {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Age_Gate_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;


	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'age-gate';
		$this->version = AGE_GATE_VER;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Age_Gate_Loader. Orchestrates the hooks of the plugin.
	 * - Age_Gate_i18n. Defines internationalization functionality.
	 * - Age_Gate_Admin. Defines all hooks for the admin area.
	 * - Age_Gate_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once( ABSPATH . "wp-includes/pluggable.php" );
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-age-gate-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-age-gate-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-age-gate-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-age-gate-public.php';

		$this->loader = new Age_Gate_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Age_Gate_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Age_Gate_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Age_Gate_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// add menu
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu_section');

		// Register sections
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings_sections' );

		// register settings fields
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings_section_global');


		$basename = plugin_basename( AGE_GATE_DIR . $this->plugin_name . '.php' );
		$this->loader->add_filter( "plugin_action_links_" . $basename, $plugin_admin, 'plugin_add_settings_link' );


		$settings = get_option('wp_age_gate_general');

		// Only load with post-specific stuff if enabled.
		if ( 'selected' == $settings['wp_age_gate_restriction_type'] ) {

			// Add a "restrict" checkbox to individual posts/pages.
			$this->loader->add_action( 'post_submitbox_misc_actions', $plugin_admin, 'restrict_select_content' );

		} else {
			// Add a "restrict" checkbox to individual posts/pages.
			$this->loader->add_action( 'post_submitbox_misc_actions', $plugin_admin, 'bypass_selected_content' );
		}

		// Save the "restrict" or "bypass" checkbox value.
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_post' );

		global $pagenow;
		if( $pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] === 'age-gate'){
			$this->loader->add_filter( 'mce_buttons', $plugin_admin, 'wp_age_gate_customise_tinymce');
		}

		// give editors access to change settings
		// General
		$this->loader->add_filter('option_page_capability_age-gate_general', $plugin_admin, 'add_manage_options', 10, 1);

		// Style
		$this->loader->add_filter('option_page_capability_age-gate_style', $plugin_admin, 'add_manage_options', 10, 1);
		
		// Show the Cache warning
		$this->loader->add_action( 'pre_update_option_wp_age_gate_general', $plugin_admin, 'set_notices', 10, 1 );

		// Show admin notices
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'show_admin_notice' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Age_Gate_Public( $this->get_plugin_name(), $this->get_version() );

		// ensure session is up
		$this->loader->add_action('init', $plugin_public, 'start_ag_session', 1);

		

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action('wp_head', $plugin_public, 'customCss');
		
		$this->loader->add_filter( 'template_include', $plugin_public, 'age_gate', 1, 1 );
		$this->loader->add_filter( 'document_title_parts', $plugin_public, 'assignPageTitle' );

		$settings = get_option('wp_age_gate_general');

		if ( isset($settings['wp_age_gate_restrict_register']) && true == $settings['wp_age_gate_restrict_register'] ) {
			$this->loader->add_action( 'register_form',  $plugin_public, 'extend_registration_form' );
			$this->loader->add_action('login_head', $plugin_public, 'extend_registration_form_styles');
			$this->loader->add_filter( 'registration_errors', $plugin_public, 'extend_registration_form_show_errors', 10, 3 );
			$this->loader->add_action( 'user_register', $plugin_public, 'extend_registration_user_data', 10, 1 );
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Age_Gate_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
