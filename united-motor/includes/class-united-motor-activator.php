<?php

/**
 * Fired during plugin activation
 *
 * @link       https://honduespacios.es
 * @since      1.0.0
 *
 * @package    United_Motor
 * @subpackage United_Motor/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    United_Motor
 * @subpackage United_Motor/includes
 * @author     ludwins robles <ludwins36@gmail.com>
 */
class United_Motor_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'united_motor_messages';
		
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			post_id mediumint(9) NOT NULL,
			author_id mediumint(9) NOT NULL,
			parent_id mediumint(9) DEFAULT 0,
			message text NOT NULL,
			is_read tinyint(1) DEFAULT 0,
			status tinyint(1) DEFAULT 0,
			created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		$table_notifications = $wpdb->prefix . 'united_motor_notifications';

		$sql_notifications = "CREATE TABLE $table_notifications (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			user_id mediumint(9) NOT NULL,
			message text NOT NULL,
			type varchar(50) DEFAULT 'info' NOT NULL,
			href text DEFAULT '' NOT NULL,
			is_read tinyint(1) DEFAULT 0,
			status tinyint(1) DEFAULT 1,
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		self::create_tables();
		dbDelta( $sql );
		dbDelta( $sql_notifications );

		update_option( 'united_motor_db_version', '1.1.0' );

		// Crear página "Services" si no existe
		$services_page = get_page_by_path( 'services' );
		if ( ! $services_page ) {
			wp_insert_post(
				array(
					'post_title'   => 'Services',
					'post_name'    => 'services',
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_content' => '[united_motor_services]',
				)
			);
		}
	}

	/**
	 * Crea todas las tablas personalizadas del plugin.
	 */
	public static function create_tables() {
		require_once plugin_dir_path( __FILE__ ) . 'class-united-motor-status-history.php';
		United_Motor_Status_History::create_table();
	}

	/**
	 * Ejecuta migraciones de BD en sitios ya activos.
	 */
	public static function maybe_upgrade() {
		if ( get_option( 'united_motor_db_version' ) === '1.1.0' ) {
			return;
		}
		self::create_tables();
		update_option( 'united_motor_db_version', '1.1.0' );
	}

}
