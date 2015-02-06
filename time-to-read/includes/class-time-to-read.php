<?php
/**
* The main class for this plugin.
*
* This class defines information about the plugin
* and registers any required hooks.
*/
class Time_To_Read {

	/**
	* The plugin name that identifies this plugin.
	*
	* @access protected
	* @var string $plugin_name
	*/
	protected $plugin_name;

	/**
	* The current plugin version.
	*
	* @access protected
	* @var string $version
	*/
	protected $version;

	/**
	* Array of options for the plugin
	*
	* @access protected
	* @var string $options
	*/
	protected $options;

	/**
	* Handles any requirements when the plugin is activated.
	*
	* @access public
	*/
	public static function activate() {
		self::add_options();
	}

	/**
	* Handles any housekeeping that takes place
	* when the plugin is deactivated.
	*
	* @access public
	*/
	public static function deactivate() {
		// Nothing to cleanup here
		return;
	}

	/**
	* Handles any housekeeping that takes place
	* when the plugin is uninstalled.
	*
	* @access public
	*/
	public static function uninstall() {
		delete_option( 't2r_options' );
	}

	/**
	* Adds our options if they don't exist,
	* otherwise does nothing.
	*
	* @access public
	*/
	public static function add_options() {
		if( !get_option( 't2r_options' ) ) {
			update_option( 't2r_options', array( 'avg_wpm' => DEFAULT_AVG_WPM ) );
		}
	}

	/**
	* The class constructor.
	*
	* Set the plugin name and the plugin version that can be used throughout the plugin.
	* Load the dependencies, and grab out plugin options.
	*
	* @access public
	*/
	public function __construct() {
		$this->plugin_name = 'Time To Read';
		$this->version = '1.0.0';
		$this->load_dependencies();
		$this->options = get_option( 't2r_options' );
	}

	/**
	* Load the required dependencies for this plugin.
	*
	* @access private
	*/
	private function load_dependencies() {
	
		// Admin panel class
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-time-to-read-admin.php';
	}

	/**
	* Define the locale for this plugin for internationalization.
	*
	* @access private
	*/
	private function set_locale() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
	}

	/**
	* Load the plugin text domain for translation.
	*
	* @access public
	*/
	public function load_textdomain() {
		load_plugin_textdomain(
			$this->plugin_name,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}

	/**
	* Add all admin hooks for the plugin
	*
	* @access private
	*/
	private function register_admin_hooks() {
		$plugin_admin = new Time_To_Read_Admin( 
			$this->plugin_name, 
			$this->version,
			$this->options
		);
		add_action( 'admin_menu', array( $plugin_admin, 'add_menu' ) );
		add_action( 'admin_init', array( $plugin_admin, 'settings_init' ) );
	}

	/**
	* Add all public hooks for the plugin
	*
	* @access private
	*/
	private function register_public_hooks() {
		add_filter( 'the_content', array( $this, 'add_time_to_read' ) );
	}

	/**
	* Callback to add our actual Time to Read paragraph tag
	* to post content via filter.
	*
	* @access public
	* @param $content string A post content string.
	* @return string Our tag plus the original content.
	*/
	public function add_time_to_read( $content ) {
		$time = round( $this->word_count( $content ) / $this->get_avg_wpm() );
		if ( $time < 1){
			$time = '<1';
		}
		return "<p class='time-to-read'>$time " . __( 'min read', $this->plugin_name ) . "</p>" . $content;
	}

	/**
	* Returns the average words per minute setting value.
	*
	* @access private
	* @return int Average words per minute setting
	*/
	private function get_avg_wpm() {
		return intval( $this->options['avg_wpm'] );
	}

	/**
	* Gets the word count for a string
	*
	* @access private
	* @return int Number of words in the given string.
	*/
	private function word_count( $words ) {
		$count = str_word_count( $words );
		if ( $count < 1 ){
			$count = 1;
		}
		return $count;
	}

	/**
	* Run the loader to execute all of the hooks with WordPress.
	*
	* @access public
	*/
	public function run() {
		$this->set_locale();
		$this->register_admin_hooks();
		$this->register_public_hooks();
	}
}