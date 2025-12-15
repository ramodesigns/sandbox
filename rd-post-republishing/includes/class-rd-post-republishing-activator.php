<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.paulramotowski.com
 * @since      1.0.0
 *
 * @package    Rd_Post_Republishing
 * @subpackage Rd_Post_Republishing/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Rd_Post_Republishing
 * @subpackage Rd_Post_Republishing/includes
 * @author     Paul Ramotowski <paulramotowski@gmail.com>
 */
class Rd_Post_Republishing_Activator {

	/**
	 * Creates the custom SQL tables during plugin activation.
	 *
	 * This method creates tables to store custom data with ID, timestamp,
	 * type, and data columns, as well as a settings table for key-value pairs.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Create data table.
		$data_table_name = $wpdb->prefix . 'rd_republishing_data';
		$data_sql = "CREATE TABLE IF NOT EXISTS $data_table_name (
			ID mediumint(9) NOT NULL AUTO_INCREMENT,
			timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
			type VARCHAR(255) NOT NULL,
			data LONGTEXT NOT NULL,
			PRIMARY KEY (ID)
		) $charset_collate;";
		dbDelta( $data_sql );

		// Create settings table.
		$settings_table_name = $wpdb->prefix . 'rd_republishing_settings';
		$settings_sql = "CREATE TABLE IF NOT EXISTS $settings_table_name (
			ID mediumint(9) NOT NULL AUTO_INCREMENT,
			timestamp DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			`key` VARCHAR(255) NOT NULL,
			value VARCHAR(255),
			PRIMARY KEY (ID)
		) $charset_collate;";
		dbDelta( $settings_sql );
	}

}
