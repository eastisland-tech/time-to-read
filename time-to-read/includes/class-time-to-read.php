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
	* Option name for our one option
	*
	* @access protected
	* @var string $opt1
	*/
	protected static $opt1 = 'avg_wpm';

	/**
	* Default words per minute value
	*
	* @access protected
	* @var string $default_wpm
	*/
	protected static $default_wpm = 250;

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
	* when the plugin is deactivated.
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
			update_option( 't2r_options', array( self::$opt1 => self::$default_wpm ) );
		}
	}

	/**
	* The class constructor.
	*
	* Set the plugin name and the plugin version that can be used throughout the plugin.
	* Load the dependencies, define the locale, and set the hooks for the Dashboard and
	* the public-facing side of the site.
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
	* Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
	* with WordPress.
	*
	* @access private
	*/
	private function set_locale() {
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
	}

	/**
	* Load the plugin text domain for translation.
	*
	* @access public
	*/
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			$this->get_plugin_name(),
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
			$this->get_plugin_name(), 
			$this->get_version(),
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
	* Run the loader to execute all of the hooks with WordPress.
	*
	* @access public
	* @var $content string A post content string.
	*/
	public function add_time_to_read( $content ) {
		$time = round( $this->word_count( $content ) / $this->get_avg_wpm() );
		if ( $time < 1){
			$time = '<1';
		}
		return "<p class='time-to-read'>$time min read</p>" . $content;
	}

	/**
	* Returns the average words per minute setting value.
	*
	* @access private
	*/
	private function get_avg_wpm() {
		$wpm = $this->options[$this::$opt1];
		if( !$wpm ){
			// If we get here, somehow our options were deleted, so add them.
			$this->add_options();
			$wpm = $this->options[$this::$opt1];
		}
		return intval( $wpm );
	}

	/**
	* Returns the word count for a string
	*
	* @access private
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

	/**
	* Retrieve the plugin name.
	*
	* @access public
	* @return string The name of the plugin.
	*/
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	* Retrieve the plugin version.
	*
	* @access public
	* @return string The version number of the plugin.
	*/
	public function get_version() {
		return $this->version;
	}
}