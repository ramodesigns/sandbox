<?php

/**
 * REST API endpoints for the data table
 *
 * @link       https://www.paulramotowski.com
 * @since      1.0.0
 *
 * @package    Rd_Post_Republishing
 * @subpackage Rd_Post_Republishing/includes
 */

/**
 * REST API endpoints for the data table.
 *
 * This class defines all REST API endpoints for interacting with the
 * rd_republishing_data table.
 *
 * @since      1.0.0
 * @package    Rd_Post_Republishing
 * @subpackage Rd_Post_Republishing/includes
 * @author     Paul Ramotowski <paulramotowski@gmail.com>
 */
class Rd_Post_Republishing_Data_Api {

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
			'/data',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_all_data' ),
					'permission_callback' => '__return_true',
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'add_data' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'type' => array(
							'required' => true,
							'type'     => 'string',
						),
						'data' => array(
							'required' => true,
							'type'     => 'string',
						),
					),
				),
			)
		);

		register_rest_route(
			'rd-post-republishing/v1',
			'/data/(?P<id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_data_by_id' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Get all data from the data table.
	 *
	 * @since    1.0.0
	 * @param    WP_REST_Request $request The request object.
	 * @return   WP_REST_Response
	 */
	public function get_all_data( WP_REST_Request $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'rd_republishing_data';

		// Check for type filter parameter.
		$type = $request->get_param( 'type' );

		if ( $type ) {
			$query = $wpdb->prepare(
				"SELECT * FROM $table_name WHERE type = %s ORDER BY ID DESC",
				$type
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
					'message' => 'No data found',
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
	 * Get data by ID.
	 *
	 * @since    1.0.0
	 * @param    WP_REST_Request $request The request object.
	 * @return   WP_REST_Response
	 */
	public function get_data_by_id( WP_REST_Request $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'rd_republishing_data';
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
					'message' => 'Data not found',
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
	 * Add data to the data table.
	 *
	 * @since    1.0.0
	 * @param    WP_REST_Request $request The request object.
	 * @return   WP_REST_Response
	 */
	public function add_data( WP_REST_Request $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'rd_republishing_data';

		$type = sanitize_text_field( $request->get_param( 'type' ) );
		$data = sanitize_text_field( $request->get_param( 'data' ) );

		if ( empty( $type ) || empty( $data ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => 'Type and data are required',
				),
				400
			);
		}

		$inserted = $wpdb->insert(
			$table_name,
			array(
				'type' => $type,
				'data' => $data,
			),
			array( '%s', '%s' )
		);

		if ( false === $inserted ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => 'Failed to insert data',
				),
				500
			);
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => 'Data inserted successfully',
				'id'      => $wpdb->insert_id,
			),
			201
		);
	}

}
