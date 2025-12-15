<?php

/**
 * REST API endpoints for the settings table
 *
 * @link       https://www.paulramotowski.com
 * @since      1.0.0
 *
 * @package    Rd_Post_Republishing
 * @subpackage Rd_Post_Republishing/includes
 */

/**
 * REST API endpoints for the settings table.
 *
 * This class defines all REST API endpoints for interacting with the
 * rd_republishing_settings table.
 *
 * @since      1.0.0
 * @package    Rd_Post_Republishing
 * @subpackage Rd_Post_Republishing/includes
 * @author     Paul Ramotowski <paulramotowski@gmail.com>
 */
class Rd_Post_Republishing_Settings_Api {

	/**
	 * Initialize the class and register endpoints.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register REST API routes.
	 *
	 * @since    1.0.0
	 */
	public function register_routes() {
		register_rest_route(
			'rd-post-republishing/v1',
			'/settings',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_all_settings' ),
					'permission_callback' => '__return_true',
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'add_setting' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'key'   => array(
							'required' => true,
							'type'     => 'string',
						),
						'value' => array(
							'required' => false,
							'type'     => 'string',
						),
					),
				),
			)
		);

		register_rest_route(
			'rd-post-republishing/v1',
			'/settings/(?P<id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_setting_by_id' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Get all settings from the settings table.
	 *
	 * @since    1.0.0
	 * @param    WP_REST_Request $request The request object.
	 * @return   WP_REST_Response
	 */
	public function get_all_settings( WP_REST_Request $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'rd_republishing_settings';

		// Check for key filter parameter.
		$key = $request->get_param( 'key' );

		if ( $key ) {
			$query = $wpdb->prepare(
				"SELECT * FROM $table_name WHERE `key` = %s ORDER BY ID DESC",
				$key
			);
		} else {
			$query = "SELECT * FROM $table_name ORDER BY ID DESC";
		}

		$results = $wpdb->get_results( $query );

		if ( empty( $results ) ) {
			return new WP_REST_Response(
				array(
					'success' => true,
					'data'    => array(),
					'message' => 'No settings found',
				),
				200
			);
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => $results,
			),
			200
		);
	}

	/**
	 * Get setting by ID.
	 *
	 * @since    1.0.0
	 * @param    WP_REST_Request $request The request object.
	 * @return   WP_REST_Response
	 */
	public function get_setting_by_id( WP_REST_Request $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'rd_republishing_settings';
		$id         = intval( $request->get_param( 'id' ) );

		$result = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $table_name WHERE ID = %d",
				$id
			)
		);

		if ( ! $result ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => 'Setting not found',
				),
				404
			);
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => $result,
			),
			200
		);
	}

	/**
	 * Add a setting to the settings table.
	 *
	 * @since    1.0.0
	 * @param    WP_REST_Request $request The request object.
	 * @return   WP_REST_Response
	 */
	public function add_setting( WP_REST_Request $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'rd_republishing_settings';

		$key   = sanitize_text_field( $request->get_param( 'key' ) );
		$value = sanitize_text_field( $request->get_param( 'value' ) );

		if ( empty( $key ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => 'Key is required',
				),
				400
			);
		}

		$inserted = $wpdb->insert(
			$table_name,
			array(
				'key'   => $key,
				'value' => $value,
			),
			array( '%s', '%s' )
		);

		if ( false === $inserted ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => 'Failed to insert setting',
				),
				500
			);
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => 'Setting inserted successfully',
				'id'      => $wpdb->insert_id,
			),
			201
		);
	}

}
