<?php

/**
 * Historial de cambios de estado con evidencia fotográfica.
 *
 * @package United_Motor
 */
class United_Motor_Status_History {

	/**
	 * Nombre de la tabla (sin prefijo).
	 */
	const TABLE = 'united_motor_status_history';

	/**
	 * Crea o actualiza la tabla en la base de datos.
	 */
	public static function create_table() {
		global $wpdb;

		$table_name      = $wpdb->prefix . self::TABLE;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			post_id mediumint(9) NOT NULL,
			user_id mediumint(9) NOT NULL,
			status varchar(50) NOT NULL,
			attachment_id mediumint(9) DEFAULT 0 NOT NULL,
			image_url text NOT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id),
			KEY post_id (post_id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Registra una evidencia de cambio de estado.
	 *
	 * @param int    $post_id       ID de la solicitud.
	 * @param int    $user_id       Usuario que realizó el cambio.
	 * @param string $status        Slug del estado.
	 * @param int    $attachment_id ID del adjunto en la biblioteca de medios.
	 * @param string $image_url     URL pública de la imagen.
	 * @return int|false
	 */
	public static function add( $post_id, $user_id, $status, $attachment_id, $image_url ) {
		global $wpdb;

		$table = $wpdb->prefix . self::TABLE;
		$now   = current_time( 'mysql' );

		$result = $wpdb->insert(
			$table,
			array(
				'post_id'       => (int) $post_id,
				'user_id'       => (int) $user_id,
				'status'        => sanitize_key( $status ),
				'attachment_id' => (int) $attachment_id,
				'image_url'     => esc_url_raw( $image_url ),
				'created_at'    => $now,
			),
			array( '%d', '%d', '%s', '%d', '%s', '%s' )
		);

		return $result ? (int) $wpdb->insert_id : false;
	}

	/**
	 * Obtiene el historial de una solicitud (más reciente primero).
	 *
	 * @param int $post_id ID de la solicitud.
	 * @return array
	 */
	public static function get_by_post_id( $post_id ) {
		global $wpdb;

		$table = $wpdb->prefix . self::TABLE;

		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT h.*, u.display_name AS user_name
				 FROM $table h
				 LEFT JOIN {$wpdb->users} u ON h.user_id = u.ID
				 WHERE h.post_id = %d
				 ORDER BY h.created_at DESC, h.id DESC",
				(int) $post_id
			)
		);

		if ( empty( $rows ) ) {
			return array();
		}

		$history = array();
		foreach ( $rows as $row ) {
			$history[] = array(
				'id'         => (int) $row->id,
				'status'     => $row->status,
				'image_url'  => $row->image_url,
				'created_at' => mysql2date( 'Y-m-d\TH:i:s', $row->created_at, false ),
				'user_name'  => $row->user_name ? $row->user_name : '',
			);
		}

		return $history;
	}
}
