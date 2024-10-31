<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://intellasoftplugins.com/
 * @package           ISSSLPG
 *
 * @wordpress-plugin
 * Plugin Name:       IntellaSoft SEO Landing Page Generator
 * Plugin URI:        https://intellasoftplugins.com/
 * Description:       Generate landing pages in bulk based on location with randomized content. Update thousands of landing pages in seconds.
 * Version:           1.66.2
 * Author:            IntellaSoft Solutions
 * Author URI:        https://intellasoftplugins.com/
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl.html
 * Text Domain:       issslpg
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Freemius SDK access.
 */
require_once plugin_dir_path( __FILE__ ) . 'freemius.php';
issslpg_fs()->add_action( 'after_uninstall', 'issslpg_fs_uninstall_cleanup' );

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ISSSLPG_VERSION', '1.66.2' );
define( 'ISSSLPG_BASENAME', plugin_basename(__FILE__) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-issslpg-activator.php
 */
function activate_issslpg() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-issslpg-activator.php';
	ISSSLPG_Activator::activate();
}

register_activation_hook( __FILE__, 'activate_issslpg' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-issslpg-deactivator.php
 */
function deactivate_issslpg() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-issslpg-deactivator.php';
	ISSSLPG_Deactivator::deactivate();
}

register_deactivation_hook( __FILE__, 'deactivate_issslpg' );

/**
 * The code that runs after plugin update.
 * This action is documented in includes/class-issslpg-updater.php
 */
function update_issslpg() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-issslpg-updater.php';
	new ISSSLPG_Updater;
}

add_action( 'plugins_loaded', 'update_issslpg', 10, 0 );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-issslpg.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
function run_issslpg() {
	$plugin = new ISSSLPG();
	$plugin->run();
}
run_issslpg();
