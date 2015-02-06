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
    * The current plugin version.
    *
    * @access protected
    * @var string $version A string that represents the current version.
    */
    protected $options;

    /**
    * Initialize the class and set its properties.
    *
    * @param string $plugin_name A string that identifies the plugin.
    * @param string $version The current plugin version.
    */
    public function __construct( $plugin_name, $version, $options ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->options = $options;
    }

    public function add_menu() {
        add_options_page( 'Time To Read Settings', 'Time To Read', 'manage_options', 'my-unique-identifier', array( $this, 'options_page' ) );
    }

    public function settings_init() { 
        register_setting( 'plugin_page', 't2r_options' );

        add_settings_section(
            'time_to_read_plugin_page_section', 
            __( '', 'Time To Read' ), 
            '',
            'plugin_page'
        );

        add_settings_field( 
            'time_to_read_avg_wpm', 
            __( 'Settings field description', 'Time To Read' ), 
            array( $this, 'avg_wpm_render' ), 
            'plugin_page', 
            'time_to_read_plugin_page_section' 
        );
    }


    public function avg_wpm_render() { 
        $options = $this->options;
        ?>
        <input type='text' size="4" name='t2r_options[avg_wpm]' value='<?php echo $options['avg_wpm']; ?>'>
        <?php
    }


    public function settings_section_callback(  ) { 
        echo __( '', 'Time To Read' );
    }

    public function options_page(  ) { 
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
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
}