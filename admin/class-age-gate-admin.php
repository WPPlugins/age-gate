<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://philsbury.uk
 * @since      1.0.0
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/admin
 */


/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/admin
 * @author     Phil Baker <phil@philsbury.co.uk>
 */
class Age_Gate_Admin {
	/**
	 * The options name to be used in this plugin
	 *
	 * @since  	1.0.0
	 * @access 	private
	 * @var  	string 		$option_name 	Option name of this plugin
	 */
	private $option_name = 'wp_age_gate';

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->settings = get_option($this->option_name . '_general');

		$this->_updateCheck();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Age_Gate_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Age_Gate_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		global $pagenow;
		if( $pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] === 'age-gate'){

			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/age-gate-admin.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Age_Gate_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Age_Gate_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		global $pagenow;
		if( $pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] === 'age-gate'){
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script('wplink');
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/age-gate-admin.js', array( 'jquery', 'wp-color-picker' ), $this->version, false );

		}

	}

	/**
	 * Register plugin menu item
	 *
	 * @since 1.0.0 Displays the menu
	 */
	public function add_menu_section()
	{
		$this->plugin_page_hook_suffix = add_menu_page(
			__('Age Gate', $this->plugin_name),
			__('Age Gate', $this->plugin_name),
			'edit_pages',
			$this->plugin_name,
			array($this, 'display_options_page'),
			'dashicons-lock',
			60
		);

	}

	/**
	 * Create a settings link in the plugins screen
	 * 
	 * @param  mixed $links The standard links
	 * @return mixed $links	The links updated with our settings
	 * @since 1.0.0
	 */
	public function plugin_add_settings_link( $links ) {
    $settings_link = '<a href="admin.php?page='. $this->plugin_name .'">' . __( 'Settings' ) . '</a>';
    array_unshift( $links, $settings_link );
  	return $links;
	}

	/**
	 * Add manage_options
	 *
	 * In order to save as editor, they need this
	 * @since 1.0.0
	 */
	public function add_manage_options()
	{
		return 'edit_pages';		
		
	}

	/**
	 * Add the option to restrict specific content
	 * 
	 * @since 1.0.0
	 */
	public function restrict_select_content()
	{
		?>
		<div class="misc-pub-section verify-age">

			<?php wp_nonce_field( 'ag_save_post', 'ag_nonce' ); ?>
			<input type="hidden" name="_age-bypass" id="age-bypass" value="0" />
			<input type="checkbox" name="_age-restricted" id="age-restricted" value="1" <?php checked( 1, get_post_meta( get_the_ID(), '_age-restricted', true ) ); ?> />
			<label for="age-restricted" class="selectit">
				<?php esc_html_e( 'Restrict content to' . ' ' . $this->settings['wp_age_gate_min_age'].'+', $this->plugin_name ); ?>
			</label>

		</div>
		<?php 
	}


	/**
	 * Add an option to NOT agegate a page. Useful for terms pages on fully restricted sites
	 *
	 * @since 1.3.0
	 */
	public function bypass_selected_content()
	{
		?>
		<div class="misc-pub-section verify-age">

			<?php wp_nonce_field( 'ag_save_post', 'ag_nonce' ); ?>
			<input type="hidden" name="_age-restricted" id="age-restricted" value="0" />
			<input type="checkbox" name="_age-bypass" id="age-bypass" value="1" <?php checked( 1, get_post_meta( get_the_ID(), '_age-bypass', true ) ); ?> />
			<label for="age-bypass" class="selectit">
				<?php esc_html_e( 'Do not restrict this page', $this->plugin_name ); ?>
			</label>

		</div>
		<?php 
	}

	/**
	 * Save the "restrict" checkbox value.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The current post ID.
	 * @return void
	 */
	public function save_post( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$nonce = ( isset( $_POST['ag_nonce'] ) ) ? $_POST['ag_nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'ag_save_post' ) ) {
			return;
		}

		$needs_verify = ( isset( $_POST['_age-restricted'] ) ) ? (int) $_POST['_age-restricted'] : 0;
		$bypass_verify = ( isset( $_POST['_age-bypass'] ) ) ? (int) $_POST['_age-bypass'] : 0;

		update_post_meta( $post_id, '_age-restricted', $needs_verify );
		update_post_meta( $post_id, '_age-bypass', $bypass_verify );
	}

	/**
	 * Display the plugin options
	 *
	 * @since 1.0.0
	 */
	public function display_options_page()
	{

		include_once 'partials/'. $this->plugin_name .'-admin-display.php';
	}

	/**
	 * Register the settings sections
	 * 
	 * @since 1.0.0
	 */
	public function register_settings_sections()
	{
		$data = get_option($this->option_name . '_general');

		
		/* General Settings */
		add_settings_section(
			$this->option_name . '_general',
			__('Restriction settings', $this->plugin_name),
			array($this, $this->option_name . '_general_cb'),
			$this->plugin_name . '_general'
		);

		/* Message Settings */
		add_settings_section(
			$this->option_name . '_messaging',
			__('Age gate messaging settings', $this->plugin_name),
			array($this, $this->option_name . '_general_cb'),
			$this->plugin_name . '_general'
		);

		/* Style Settings */
		add_settings_section(
			$this->option_name . '_style',
			__('Age gate style settings', $this->plugin_name),
			array($this, $this->option_name . '_general_cb'),
			$this->plugin_name . '_general'
		);

		/* Style Settings */
		add_settings_section(
			$this->option_name . '_caching',
			__('Caching', $this->plugin_name),
			array($this, $this->option_name . '_general_cb'),
			$this->plugin_name . '_general'
		);

		/* Misc Settings */
		add_settings_section(
			$this->option_name . '_misc',
			__('Miscellaneous', $this->plugin_name),
			array($this, $this->option_name . '_general_cb'),
			$this->plugin_name . '_general'
		);

	}

	/**
	 * Register the individual settings
	 * 
	 * @since 1.0.0
	 */
	public function register_settings_section_global()
	{
		$data = get_option($this->option_name . '_general');

		/* RESTRICTIONS */

		add_settings_field(
			$this->option_name . '_min_age',
			__('Users must be', $this->plugin_name),
			array($this, $this->option_name . '_input_field_cb'),
			$this->plugin_name . '_general',
			$this->option_name . '_general',
			array(
				'label_for' => $this->option_name . '_min_age',
				'field_name' => '_min_age',
				'type' => 'text',
				'size' => 'small',
				'setting' => $this->option_name . '_general',
				'value' => (!isset($data[$this->option_name . '_min_age']) ? '' : $data[$this->option_name . '_min_age']),
				'text' => __('years or older to view content', $this->plugin_name)
			)
		);

		add_settings_field(
			$this->option_name . '_restriction_type',
			__('Restrict', $this->plugin_name),
			array($this, $this->option_name . '_radio_field_cb'),
			$this->plugin_name . '_general',
			$this->option_name . '_general',
			array(
				'value' => (!isset($data[$this->option_name . '_restriction_type']) ? '' : $data[$this->option_name . '_restriction_type']),
				'label_for' => $this->option_name . '_restriction_type',
				'field_name' => '_restriction_type',
				'setting' => $this->option_name . '_general',
				'options' => array('all' => __('All content', $this->plugin_name), 'selected' => __('Selected Content', $this->plugin_name))
			)
		);
		

		add_settings_field(
			$this->option_name . '_restrict_register',
			__('Restrict registration', $this->plugin_name),
			array($this, (!get_option('users_can_register') ? $this->option_name . '_na_cb' : $this->option_name . '_checkbox_field_cb')),
			$this->plugin_name . '_general',
			$this->option_name . '_general',
			array(
				'label_for' => $this->option_name . '_restrict_register',
				'field_name' => '_restrict_register',
				'setting' => $this->option_name . '_general',
				'text' => __('Age check users during registering', $this->plugin_name),
				'value' => (!isset($data[$this->option_name . '_restrict_register']) ? '' : $data[$this->option_name . '_restrict_register']),
				'na' => __('Your site does not allow registration, so this option is not applicable', $this->plugin_name)
			)
		);

		add_settings_field(
			$this->option_name . '_input_type',
			__('Validate age using', $this->plugin_name),
			array($this, $this->option_name . '_select_field_cb'),
			$this->plugin_name . '_general',
			$this->option_name . '_general',
			array(
				'label_for' => $this->option_name . '_input_type',
				'field_name' => '_input_type',
				'setting' => $this->option_name . '_general',
				'value' => (!isset($data[$this->option_name . '_input_type']) ? '' : $data[$this->option_name . '_input_type']),
				'options' => array('inputs' => __('Input fields', $this->plugin_name), 'selects' => __('Dropdown boxes', $this->plugin_name), 'buttons' => __('Yes/No', $this->plugin_name))
			)
		);

		add_settings_field(
			$this->option_name . '_remember',
			__('Remember', $this->plugin_name),
			array($this, $this->option_name . '_checkbox_field_cb'),
			$this->plugin_name . '_general',
			$this->option_name . '_general',
			array(
				'label_for' => $this->option_name . '_remember',
				'field_name' => '_remember',
				'setting' => $this->option_name . '_general',
				'text' => __('Enable "remember me" checkbox', $this->plugin_name),
				'value' => (!isset($data[$this->option_name . '_remember']) ? '' : $data[$this->option_name . '_remember']),
			)
		);

		add_settings_field(
			$this->option_name . '_date_format',
			__('Date format', $this->plugin_name),
			array($this, $this->option_name . '_select_field_cb'),
			$this->plugin_name . '_general',
			$this->option_name . '_general',
			array(
				'label_for' => $this->option_name . '_date_format',
				'field_name' => '_date_format',
				'setting' => $this->option_name . '_general',
				'value' => (!isset($data[$this->option_name . '_date_format']) ? '' : $data[$this->option_name . '_date_format']),
				'options' => array('ddmmyyyy' => __('DD MM YYYY', $this->plugin_name), 'mmddyyyy' => __('MM DD YYYY', $this->plugin_name))
			)
		);

		add_settings_field(
			$this->option_name . '_ignore_logged',
			__('Ignore logged in', $this->plugin_name),
			array($this, $this->option_name . '_checkbox_field_cb'),
			$this->plugin_name . '_general',
			$this->option_name . '_general',
			array(
				'label_for' => $this->option_name . '_ignore_logged',
				'field_name' => '_ignore_logged',
				'setting' => $this->option_name . '_general',
				'text' => __('Logged in users will not need to provide their age', $this->plugin_name),
				'value' => (!isset($data[$this->option_name . '_ignore_logged']) ? '' : $data[$this->option_name . '_ignore_logged']),
			)
		);

		add_settings_field(
			$this->option_name . '_no_second_chance',
			__('No second chance', $this->plugin_name),
			array($this, $this->option_name . '_checkbox_field_cb'),
			$this->plugin_name . '_general',
			$this->option_name . '_general',
			array(
				'label_for' => $this->option_name . '_no_second_chance',
				'field_name' => '_no_second_chance',
				'setting' => $this->option_name . '_general',
				'text' => __('If someone fails the age test, they cannot try again until their next session', $this->plugin_name),
				'value' => (!isset($data[$this->option_name . '_no_second_chance']) ? '' : $data[$this->option_name . '_no_second_chance']),
			)
		);

		add_settings_field(
			$this->option_name . '_fail_link',
			__('Redirect failures', $this->plugin_name),
			array($this, $this->option_name . '_link_field_cb'),
			$this->plugin_name . '_general',
			$this->option_name . '_general',
			array(
				'label_for' => $this->option_name . '_fail_link',
				'field_name' => '_fail_link',
				'setting' => $this->option_name . '_general',
				'text' => __('If someone fails the age test, redirect them to a page or external site rather than showing errors.', $this->plugin_name),
				'value' => array(
						'link' => (!isset($data[$this->option_name . '_fail_link']) ? '' : $data[$this->option_name . '_fail_link']),
						'title' => (!isset($data[$this->option_name . '_fail_link_title']) ? '' : $data[$this->option_name . '_fail_link_title'])
					)
			)
		);


		/* MESSAGING */

		add_settings_field(
			$this->option_name . '_instruction',
			__('Headline', $this->plugin_name),
			array($this, $this->option_name . '_input_field_cb'),
			$this->plugin_name . '_general',
			$this->option_name . '_messaging',
			array(
				'label_for' => $this->option_name . '_instruction',
				'field_name' => '_instruction',
				'type' => 'text',
				'size' => 'regular',
				'setting' => $this->option_name . '_general',
				'value' => (!isset($data[$this->option_name . '_instruction']) ? '' : $data[$this->option_name . '_instruction']),
				'text' => ''
			)
		);

		add_settings_field(
			$this->option_name . '_messaging',
			__('Sub headline', $this->plugin_name),
			array($this, $this->option_name . '_input_field_cb'),
			$this->plugin_name . '_general',
			$this->option_name . '_messaging',
			array(
				'label_for' => $this->option_name . '_messaging',
				'field_name' => '_messaging',
				'type' => 'text',
				'size' => 'regular',
				'setting' => $this->option_name . '_general',
				'value' => (!isset($data[$this->option_name . '_messaging']) ? '' : $data[$this->option_name . '_messaging']),
				'text' => ''
			)
		);

		add_settings_field(
			$this->option_name . '_invalid_input_msg',
			__('Invalid inputs', $this->plugin_name),
			array($this, $this->option_name . '_input_field_cb'),
			$this->plugin_name . '_general',
			$this->option_name . '_messaging',
			array(
				'label_for' => $this->option_name . '_invalid_input_msg',
				'field_name' => '_invalid_input_msg',
				'type' => 'text',
				'size' => 'regular',
				'setting' => $this->option_name . '_general',
				'value' => (!isset($data[$this->option_name . '_invalid_input_msg']) ? '' : $data[$this->option_name . '_invalid_input_msg']),
				'text' => ''
			)
		);

		add_settings_field(
			$this->option_name . '_under_age_msg',
			__('Under age', $this->plugin_name),
			array($this, $this->option_name . '_input_field_cb'),
			$this->plugin_name . '_general',
			$this->option_name . '_messaging',
			array(
				'label_for' => $this->option_name . '_under_age_msg',
				'field_name' => '_under_age_msg',
				'type' => 'text',
				'size' => 'regular',
				'setting' => $this->option_name . '_general',
				'value' => (!isset($data[$this->option_name . '_under_age_msg']) ? '' : $data[$this->option_name . '_under_age_msg']),
				'text' => ''
			)
		);

		add_settings_field(
			$this->option_name . '_generic_error_msg',
			__('Generic error', $this->plugin_name),
			array($this, $this->option_name . '_input_field_cb'),
			$this->plugin_name . '_general',
			$this->option_name . '_messaging',
			array(
				'label_for' => $this->option_name . '_generic_error_msg',
				'field_name' => '_generic_error_msg',
				'type' => 'text',
				'size' => 'regular',
				'setting' => $this->option_name . '_general',
				'value' => (!isset($data[$this->option_name . '_generic_error_msg']) ? '' : $data[$this->option_name . '_generic_error_msg']),
				'text' => ''
			)
		);


		/* STYLE */

		add_settings_field(
			$this->option_name . '_logo',
			__('Logo', $this->plugin_name),
			array($this, $this->option_name . '_media_selector_cb'),
			$this->plugin_name . '_general',
			$this->option_name . '_style',
			array(
				'value' => (!isset($data[$this->option_name . '_logo']) ? '' : $data[$this->option_name . '_logo']),
				'label_for' => $this->option_name . '_logo',
				'field_name' => '_logo',
				'setting' => $this->option_name . '_general',
			)
		);

		add_settings_field(
			$this->option_name . '_button_text',
			__('Submit button text', $this->plugin_name),
			array($this, $this->option_name . '_input_field_cb'),
			$this->plugin_name . '_general',
			$this->option_name . '_style',
			array(
				'label_for' => $this->option_name . '_button_text',
				'field_name' => '_button_text',
				'type' => 'text',
				'size' => 'regular',
				'setting' => $this->option_name . '_general',
				'value' => (!isset($data[$this->option_name . '_button_text']) ? '' : $data[$this->option_name . '_button_text']),
				'text' => ''
			)
		);

		add_settings_field(
			$this->option_name . '_background_colour',
			__('Background colour', $this->plugin_name),
			array($this, $this->option_name . '_colour_field_cb'),
			$this->plugin_name . '_general',
			$this->option_name . '_style',
			array(
				'label_for' => $this->option_name . '_background_colour',
				'field_name' => '_background_colour',
				'size' => 'regular',
				'setting' => $this->option_name . '_general',
				'value' => (!isset($data[$this->option_name . '_background_colour']) ? '' : $data[$this->option_name . '_background_colour']),
				'text' => ''
			)
		);

		add_settings_field(
			$this->option_name . '_background_image',
			__('Background image', $this->plugin_name),
			array($this, $this->option_name . '_media_selector_cb'),
			$this->plugin_name . '_general',
			$this->option_name . '_style',
			array(
				'value' => (!isset($data[$this->option_name . '_background_image']) ? '' : $data[$this->option_name . '_background_image']),
				'label_for' => $this->option_name . '_background_image',
				'field_name' => '_background_image',
				'setting' => $this->option_name . '_general',
			)
		);


		add_settings_field(
			$this->option_name . '_foreground_colour',
			__('Foreground colour', $this->plugin_name),
			array($this, $this->option_name . '_colour_field_cb'),
			$this->plugin_name . '_general',
			$this->option_name . '_style',
			array(
				'label_for' => $this->option_name . '_foreground_colour',
				'field_name' => '_foreground_colour',
				'size' => 'regular',
				'setting' => $this->option_name . '_general',
				'value' => (!isset($data[$this->option_name . '_foreground_colour']) ? '' : $data[$this->option_name . '_foreground_colour']),
				'text' => ''
			)
		);

		add_settings_field(
			$this->option_name . '_text_colour',
			__('Text colour', $this->plugin_name),
			array($this, $this->option_name . '_colour_field_cb'),
			$this->plugin_name . '_general',
			$this->option_name . '_style',
			array(
				'label_for' => $this->option_name . '_text_colour',
				'field_name' => '_text_colour',
				'size' => 'regular',
				'setting' => $this->option_name . '_general',
				'value' => (!isset($data[$this->option_name . '_text_colour']) ? '' : $data[$this->option_name . '_text_colour']),
				'text' => ''
			)
		);

		add_settings_field(
			$this->option_name . '_styling',
			__('Layout', $this->plugin_name),
			array($this, $this->option_name . '_checkbox_field_cb'),
			$this->plugin_name . '_general',
			$this->option_name . '_style',
			array(
				'label_for' => $this->option_name . '_styling',
				'field_name' => '_styling',
				'setting' => $this->option_name . '_general',
				'text' => __('Use plugin style on the front end', $this->plugin_name),
				'value' => (!isset($data[$this->option_name . '_styling']) ? '' : $data[$this->option_name . '_styling']),
			)
		);

		add_settings_field(
			$this->option_name . '_device_width',
			__('Viewport meta tag', $this->plugin_name),
			array($this, $this->option_name . '_checkbox_field_cb'),
			$this->plugin_name . '_general',
			$this->option_name . '_style',
			array(
				'label_for' => $this->option_name . '_device_width',
				'field_name' => '_device_width',
				'setting' => $this->option_name . '_general',
				'text' => __('Add viewport meta to Age Gate page <br><i>(width=device-width, minimum-scale=1, maximum-scale=1)</i>', $this->plugin_name),
				'value' => (!isset($data[$this->option_name . '_device_width']) ? '' : $data[$this->option_name . '_device_width']),
			)
		);

		/* CACHING */

		add_settings_field(
			$this->option_name . '_use_js',
			__('Use uncachable version', $this->plugin_name),
			array($this, $this->option_name . '_radio_field_cb'),
			$this->plugin_name . '_general',
			$this->option_name . '_caching',
			array(
				'value' => (!isset($data[$this->option_name . '_use_js']) ? '' : $data[$this->option_name . '_use_js']),
				'label_for' => $this->option_name . '_use_js',
				'field_name' => '_use_js',
				'setting' => $this->option_name . '_general',
				// 'text' => __('Add viewport meta to Age Gate page <br><i>(width=device-width, minimum-scale=1, maximum-scale=1)</i>', $this->plugin_name),
				'options' => array('standard' => __('Standard', $this->plugin_name), 'js' => __('Cache bypass', $this->plugin_name)),
			)
		);

		/* MISCELLANEOUS */

		add_settings_field(
			$this->option_name . '_additional',
			__('Additional content', $this->plugin_name),
			array($this, $this->option_name . '_rich_text_cb'),
			$this->plugin_name . '_general',
			$this->option_name . '_misc',
			array(
				'label_for' => $this->option_name . '_additional',
				'field_name' => '_additional',
				'setting' => $this->option_name . '_general',
				'text' => __('Use plugin style on the front end', $this->plugin_name),
				'value' => (!isset($data[$this->option_name . '_additional']) ? '' : $data[$this->option_name . '_additional']),
			)
		);

		register_setting( $this->plugin_name . '_general', $this->option_name . '_general', array($this, 'sanitize') );

	}

	/**
	 * Sanitize
	 * @param  mixed $input 			The post data
	 * @return mixed $sanitized 	The sanitized data
	 * @since 1.0.0
	 */
	public function sanitize($input)
	{

		$sanitized = array();

		foreach ($input as $option => $value) {
			switch($option){
				case 'wp_age_gate_min_age':
				case 'wp_age_gate_restrict_register':
				case 'wp_age_gate_remember':
				case 'wp_age_gate_ignore_logged':
				case 'wp_age_gate_logo':
				case 'wp_age_gate_background_image':
				case 'wp_age_gate_styling':
				case 'wp_age_gate_no_second_chance':			
				case 'wp_age_gate_device_width':
					$sanitized[$option] = intval($value);
				break;
				case 'wp_age_gate_restriction_type':
				case 'wp_age_gate_input_type':
				case 'wp_age_gate_date_format':
				case 'wp_age_gate_button_text':
				case 'wp_age_gate_instruction':
				case 'wp_age_gate_messaging':
				case 'wp_age_gate_background_colour':
				case 'wp_age_gate_foreground_colour':
				case 'wp_age_gate_text_colour':
				case 'wp_age_gate_fail_link_title':
				case 'wp_age_gate_fail_link':
				case 'wp_age_gate_invalid_input_msg':
				case 'wp_age_gate_under_age_msg':
				case 'wp_age_gate_generic_error_msg':
				case 'wp_age_gate_use_js':
					$sanitized[$option] = esc_html(strval($value));
				break;
				case 'wp_age_gate_additional':
					$sanitized[$option] = $value;
				break;
			}
		}


		return $sanitized;
		
	}

	/**
	 * Callback to display input field
	 * @param  mixed $args
	 * @since 1.0.0
	 */
	public function wp_age_gate_input_field_cb($args)
	{		
		echo '<input class="'. (isset($args['size']) ? $args['size'] : 'regular') .'-text ltr" type="'. (isset($args['type']) ? $args['type'] : 'text') .'" name="' . $args['setting'] . '[' . $this->option_name . $args['field_name'] . ']" id="' . $this->option_name . $args['field_name'] . '" value="' . $args['value'] . '"> ' . $args['text'];
	}

	/**
	 * Callback to display colour picker field
	 * @param  mixed $args
	 * @since 1.0.0
	 */
	public function wp_age_gate_colour_field_cb($args)
	{		
		echo '<input class="colour-picker" type="'. (isset($args['type']) ? $args['type'] : 'text') .'" name="' . $args['setting'] . '[' . $this->option_name . $args['field_name'] . ']" id="' . $this->option_name . $args['field_name'] . '" value="' . $args['value'] . '"> ' . $args['text'];
	}

	/**
	 * Callback to display checkbox field
	 * @param  mixed $args
	 * @since 1.0.0
	 */
	public function wp_age_gate_checkbox_field_cb($args)
	{		
		echo '<label><input type="checkbox" name="' . $args['setting'] . '[' . $this->option_name . $args['field_name'] . ']" id="' . $this->option_name . $args['field_name'] . '" value="1"'. ($args['value'] ? ' checked' : '') .'> ' . $args['text'] . '</label>';
	}

	/**
	 * Callback to display text when setting is not applicable
	 * @param  mixed $args
	 * @since 1.0.0
	 */
	public function wp_age_gate_na_cb($args)
	{
		echo $args['na'];
	}

	/**
	 * Callback to display radio button field
	 * @param  mixed $args
	 * @since 1.0.0
	 */
	public function wp_age_gate_radio_field_cb($args)
	{
		echo '<fieldset><legend class="screen-reader-text">' . __('Select', $this->plugin_name) . '</legend>';
		foreach ($args['options'] as $key => $value) {
			echo '<label><input type="radio" name="' . $args['setting'] . '[' . $this->option_name . $args['field_name'] . ']" id="' . $this->option_name . $args['field_name'] . '" value="' . $key . '"'. ($args['value'] === $key ? ' checked' : '') .'> ' . $value . '</label><br>';	
		}

		echo "</fieldset>";
	}

	/**
	 * Callback to display select field
	 * @param  mixed $args
	 * @since 1.0.0
	 */
	public function wp_age_gate_select_field_cb($args)
	{		
		echo '<select name="' . $args['setting'] . '[' . $this->option_name . $args['field_name'] . ']" id="' . $this->option_name . $args['field_name'] . '"> ';
		foreach ($args['options'] as $key => $value) {
			echo '<option value="' . $key . '"'. ($args['value'] === $key ? ' selected' : '') .'>' . $value . '</option>';
		}
		echo '</select>' . (isset($args['text']) ? $args['text'] : '');
	}

	/**
	 * Callback to display rich text area
	 * @param  mixed $args
	 * @since 1.0.0
	 */
	public function wp_age_gate_rich_text_cb($args)
	{
		echo "<p>" . __('Use this area to add an addtional info or terms of entry.', $this->plugin_name) . '<br /><br /></p>';
		echo '<div class="wysiwyg-wrapper">';
		wp_editor( $args['value'], $this->option_name . $args['field_name'], array( 'media_buttons' => false, 'quicktags' => false, 'tinymce' => array('wp_autoresize_on' => false,  'resize' => false,  'statusbar' => false, 'mce_buttons' => 'bold, italic'), 'textarea_name' => $args['setting'] . '[' . $this->option_name . $args['field_name'] . ']' ) );
		echo '</div>';
	}

	/**
	 * Callback for link picker
	 * @since 1.3.0
	 */
	public function wp_age_gate_link_field_cb($args){

		// 
		// Fix for pages that don't have editors
    add_action('admin_print_footer_scripts', function () {
        if ( ! class_exists('_WP_Editors') and ( ! defined('DOING_AJAX') or ! DOING_AJAX)) {
            require_once ABSPATH.WPINC.'/class-wp-editor.php';
            wp_print_styles('editor-buttons');
            _WP_Editors::wp_link_dialog();
        }
    });

    echo '<div class="link-container">';
    

    if(isset($args['value']['link']) && $args['value']['link']){

    	echo '<p><strong>' . (isset($args['value']['title']) && !empty($args['value']['title']) ? $args['value']['title'] : __('Custom', $this->plugin_name));
    	echo "</strong> (" . $args['value']['link'] . ')</p>';

    }
    


    echo '</div>';
    echo '<a href="#" title="" class="button" data-action="link-modal">'. __('Choose link', $this->plugin_name) . '</a>';

    if(isset($args['value']['title']) && !empty($args['value']['title']) || isset($args['value']['link']) && !empty($args['value']['link'])){
    	echo '<button type="button" class="button remove" data-action="remove-link">'. __('Remove link', $this->plugin_name) . '</button>';
    }

    echo '<p>' . $args['text'] . '</p>';


    echo '<input id="ag_linktitle" type="hidden" value="' . $args['value']['title'] . '" name="wp_age_gate_general[wp_age_gate_fail_link_title]" />' ;
    echo '<input id="ag_linkhref" type="hidden" value="'. $args['value']['link'] .'" name="wp_age_gate_general[wp_age_gate_fail_link]" />' ;
	}

                
	/**
	 * Render the text for the general section
	 *
	 * @since  1.0.0
	 */
	public function wp_age_gate_general_cb($args)
	{

		switch($args['id']){
			case $this->option_name . '_style':
				echo '<p>' . __( 'Update the look of the age gate.', $this->plugin_name ) . '</p>';
			break;
			case $this->option_name . '_messaging':
				echo '<p>' . __( 'Set the messages users see on the age gate.', $this->plugin_name ) . '<br /><strong>Note:</strong> Adding &ldquo;%s&rdquo; to any of these fields will output the minimum age</p>';
			break;
			case $this->option_name . '_misc': 
				echo '<p>' . __( 'Miscellaneous options.', $this->plugin_name ) . '</p>';
			break;
			case $this->option_name . '_caching': 
				echo '<p>' . __( 'If you have a caching solution, it is best to use a JavaScript triggered version of the age gate as this won&rsquo;t be adversely affected by the cache. If you don&rsquo;t have caching, the standard method is recommended.', $this->plugin_name ) . '</p>';
			break;
			default: 
			echo '<p>' . __( 'Set the restrictions for your site.', $this->plugin_name ) . '</p>';
		}
	}

	/**
	 * Callback to display media selector
	 * @param  mixed $args
	 * @since 1.0.0
	 */
	public function wp_age_gate_media_selector_cb($args) {

		wp_enqueue_media();

		?>
			
			<div class='image-preview-wrapper'>
				<img class='image-preview' data-option="<?php echo $this->option_name ?><?php echo $args['field_name'] ?>" src='<?php echo wp_get_attachment_url( $args['value'] ); ?>' style="max-height: 100px">
			</div>	
			
			
			<input data-option="<?php echo $this->option_name ?><?php echo $args['field_name'] ?>" type="button" class="button upload_image_button" value="<?php _e( 'Add image', $this->plugin_name ); ?>" />
			<input type="hidden" data-option="<?php echo $this->option_name ?><?php echo $args['field_name'] ?>" name="<?php echo $args['setting'] ?>[<?php echo $this->option_name ?><?php echo $args['field_name'] ?>]" class="image_attachment_id" value="<?php echo $args['value'] ?>">


			
		<?php
			if ($args['value']) {
				echo '<input type="button" class="button remove-image" value="' . __('Remove image', $this->plugin_name) . '" data-option="'. $this->option_name . $args['field_name'] .'" />';
			}

	}

	/**
	 * Callback to display remove fields from tinymce
	 * @param  mixed $args
	 * @since 1.0.0
	 */
	public function wp_age_gate_customise_tinymce($buttons)
	{
		$removeButtons = array('formatselect','blockquote','alignleft','aligncenter','alignright','wp_more','fullscreen','wp_adv');
		
		foreach ($buttons as $button_key => $button_value) {
			if( in_array($button_value, $removeButtons )){
				unset($buttons[$button_key]);		
			}
		}

		return $buttons;
	}

	/**
	 * Checks the plugin version against the stored version
	 * and updates the settings if mismatched
	 *
	 * @since 1.1.0
	 * 
	 */
	private function _updateCheck()
	{
		if ($this->version !== get_option('wp_age_gate_version')){
			require_once AGE_GATE_DIR . 'includes/class-age-gate-activator.php';
			Age_Gate_Activator::activate();
			update_option('wp_age_gate_version', $this->version);
		}
		
	}
	
	/**
	 * Set notices
	 * @since 1.4.0
	 */
	public function set_notices($value, $option = null, $old_value = null)
	{
		
		if($value['wp_age_gate_use_js'] === 'js'){
			set_transient('wp_age_gate_notice', array(
				array(
					'message' => __('You are using the "Cache Bypass" implementation, if you have caching enabled ensure you purge it to see your changes', $this->plugin_name),
					'class' => 'notice-warning'
				)

			)
			);
		}

		return $value;
	}
	
	
	/**
	 * Show admin notices
	 * @since	1.4.0
	 */
	public function show_admin_notice(){

		global $pagenow;
		if( $pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] === 'age-gate'){
			$notices = get_transient('wp_age_gate_notice');
			if( $notices !== false ){
				foreach( $notices as $notice ){
					echo '<div class="'. $notice['class'] .' settings-error notice is-dismissible"><p><strong>NOTICE:</strong> ' . $notice['message'] . '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
					
				}

				delete_transient('wp_age_gate_notice');
			}
		}

	}

}
