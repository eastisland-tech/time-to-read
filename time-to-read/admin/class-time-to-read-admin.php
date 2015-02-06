<?php
/**
* The Admin settings class for this plugin.
*
* Adds a settings option to the WordPress
* admin dashboard.
*
*/
class Time_To_Read_Admin {

	/**
	* The plugin name that identifies this plugin.
	*
	* @access protected
	* @var string $plugin_name A string that identifies the plugin.
	*/
	protected $plugin_name;

	/**
	* The current plugin version.
	*
	* @access protected
	* @var string $version A string that represents the current version.
	*/
	protected $version;

	/**
	* The plugin options
	*
	* @access protected
	* @var string $options An array of the plugin options
	*/
	protected $options;

	/**
	* Initialize the class and set its properties.
	*
	* @access public
	* @param string $plugin_name A string that identifies the plugin.
	* @param string $version The current plugin version.
	* @param array $options The plugin options
	*/
	public function __construct( $plugin_name, $version, $options ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->options = $options;
	}

	/**
	* Registers the options page
	*
	* @access public
	*/
	public function add_menu() {
		add_options_page( 
			'Time To Read Settings',
			'Time To Read', 
			'manage_options', 
			'my-unique-identifier', 
			array( $this, 'options_page' ) 
		);
	}

	/**
	* Registers all the settings API settings
	* and callbacks.
	*
	* @access public
	*/
	public function settings_init() { 
		register_setting( 
			'plugin_page', 
			't2r_options',
			array( $this, 'validate_settings' )
		);

		add_settings_section(
			'time_to_read_plugin_page_section', 
			__( '', $this->plugin_name ), 
			'',
			'plugin_page'
		);

		add_settings_field( 
			'time_to_read_avg_wpm', 
			__( 'Average Words Per Minute', $this->plugin_name ), 
			array( $this, 'avg_wpm_render' ), 
			'plugin_page', 
			'time_to_read_plugin_page_section' 
		);
	}

	/**
	* Average Wpm form field callback.
	*
	* @access public
	*/
	public function avg_wpm_render() { 
		$options = $this->options;
		?>
		<input type='text' size="4" name='t2r_options[avg_wpm]' value='<?php echo $options['avg_wpm']; ?>'>
		<?php
	}

	/**
	* Average Wpm section callback.
	* Not used, but included in case it's 
	* needed later.
	*
	* @access public
	*/
	public function settings_section_callback() { 
		echo __( '', $this->plugin_name );
	}

	/**
	* The actual options page callback.
	*
	* @access public
	*/
	public function options_page() { 
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.', $this->plugin_name ) );
		}
		?>
		<div class="wrap">
			<form action='options.php' method='post'>
				<h2>Time To Read Settings</h2>
				<?php
				settings_fields( 'plugin_page' );
				do_settings_sections( 'plugin_page' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	* A validation callback to keep people
	* from values that aren't a positive integer.
	* 
	* Note:
	* Didn't go too crazy with validation here. More
	* validation for length and such would likely be
	* needed for a production environment.
	*
	* @access public
	* @param array $input Incoming form values.
	* @return array $output Outgoing sanitized values.
	*/
	public function validate_settings( $input ) {
		$output = array();
		$avg_wpm = trim( $input['avg_wpm'] );
		if ( isset( $input['avg_wpm'] ) && ctype_digit( $input['avg_wpm'] ) ) {
			$output['avg_wpm'] = $input['avg_wpm'];
		}else{
			$output['avg_wpm'] = DEFAULT_AVG_WPM;
		}
		return $output;
	}
}