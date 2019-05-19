<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://chuev.com
 * @since             1.0.0
 * @package           Awpm
 *
 * @wordpress-plugin
 * Plugin Name:       AcademWeb PM
 * Plugin URI:        https://academweb.com/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Andrew A. Chuev
 * Author URI:        https://chuev.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       awpm
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'AWPM_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-awpm-activator.php
 */
function activate_awpm() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-awpm-activator.php';
	Awpm_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-awpm-deactivator.php
 */
function deactivate_awpm() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-awpm-deactivator.php';
	Awpm_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_awpm' );
register_deactivation_hook( __FILE__, 'deactivate_awpm' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-awpm.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_awpm() {

	$plugin = new Awpm();
	$plugin->run();

}
run_awpm();