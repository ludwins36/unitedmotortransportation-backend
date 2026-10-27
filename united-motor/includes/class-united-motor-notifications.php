<?php

/**
 * The Notifications functionality of the plugin.
 *
 * @link       https://honduespacios.es
 * @since      1.0.0
 *
 * @package    United_Motor
 * @subpackage United_Motor/includes
 */

/**
 * The Notifications functionality of the plugin.
 *
 * Defines methods to manage notifications in the database.
 *
 * @package    United_Motor
 * @subpackage United_Motor/includes
 * @author     ludwins robles <ludwins36@gmail.com>
 */
class United_Motor_Notifications {

	/**
	 * Add a new notification.
	 *
	 * @since    1.0.0
	 * @param    int    $user_id    The user ID to receive the notification.
	 * @param    string $message    The message of the notification.
	 * @param    string $type       The type of notification (info, success, warning, error). Default: info.
	 * @param    string $href       Optional link for the notification.
	 * @return   int|false          The ID of the inserted notification or false on error.
	 */
	public static function add( $user_id, $message, $type = 'info', $href = '' ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'united_motor_notifications';

		$result = $wpdb->insert(
			$table_name,
			array(
				'user_id'    => $user_id,
				'message'    => $message,
				'type'       => $type,
				'href'       => $href,
				'is_read'    => 0,
				'status'     => 1, // Active by default
				'created_at' => current_time( 'mysql' ),
			),
			array(
				'%d',
				'%s',
				'%s',
				'%s',
				'%d',
				'%d',
				'%s',
			)
		);

		return $result ? $wpdb->insert_id : false;
	}

	/**
	 * Update the status of a notification.
	 *
	 * @since    1.0.0
	 * @param    int    $id        The notification ID.
	 * @param    int    $status    The new status (0 = inactive, 1 = active).
	 * @return   int|false         The number of rows updated, or false on error.
	 */
	public static function update_status( $id, $status ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'united_motor_notifications';

		return $wpdb->update(
			$table_name,
			array( 'status' => $status ),
			array( 'id' => $id ),
			array( '%d' ),
			array( '%d' )
		);
	}

	/**
	 * Mark a notification as read.
	 *
	 * @since    1.0.0
	 * @param    int    $id    The notification ID.
	 * @return   int|false     The number of rows updated, or false on error.
	 */
	public static function mark_as_read( $id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'united_motor_notifications';

		return $wpdb->update(
			$table_name,
			array( 'is_read' => 1 ),
			array( 'id' => $id ),
			array( '%d' ),
			array( '%d' )
		);
	}
	
	/**
	 * Delete a notification.
	 *
	 * @since    1.0.0
	 * @param    int    $id    The notification ID.
	 * @return   int|false     The number of rows deleted, or false on error.
	 */
	public static function delete( $id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'united_motor_notifications';

		return $wpdb->delete(
			$table_name,
			array( 'id' => $id ),
			array( '%d' )
		);
	}

}
