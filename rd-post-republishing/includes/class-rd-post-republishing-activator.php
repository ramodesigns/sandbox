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
	 * Creates the custom SQL table during plugin activation.
	 *
	 * This method creates a table to store custom data with ID, timestamp,
	 * type, and data columns.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'rd_republishing_data';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			ID mediumint(9) NOT NULL AUTO_INCREMENT,
			timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
			type VARCHAR(255) NOT NULL,
			data LONGTEXT NOT NULL,
			PRIMARY KEY (ID)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

}
