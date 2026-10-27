<?php

/**
 * Envío de correos del plugin United Motor.
 *
 * @package    United_Motor
 * @subpackage United_Motor/includes
 */
class United_Motor_Mail {

	/**
	 * Correos de gerencia para nuevas solicitudes.
	 */
	const MANAGEMENT_EMAILS = array(
		'towing@unitedmotortransportation.com',
		'ludwins36@gmail.com',
	);

	/**
	 * Notifica a gerencia cuando se crea una solicitud de transporte.
	 *
	 * @param int $post_id ID de la solicitud.
	 * @param int $user_id ID del usuario que creó la solicitud.
	 * @return bool Si wp_mail tuvo éxito.
	 */
	public static function send_new_solicitud_to_management( $post_id, $user_id ) {
		$post = get_post( $post_id );
		if ( ! $post || $post->post_type !== 'solicitud_transporte' ) {
			return false;
		}

		$author = get_userdata( $user_id );
		$to     = apply_filters( 'united_motor_management_emails', self::MANAGEMENT_EMAILS, $post_id );
		$to     = array_values( array_filter( array_map( 'sanitize_email', (array) $to ), 'is_email' ) );
		if ( empty( $to ) ) {
			return false;
		}

		$data = array(
			'post_id'        => $post_id,
			'title'          => get_the_title( $post_id ),
			'date'           => get_the_date( 'Y-m-d H:i:s', $post_id ),
			'comprador'      => get_post_meta( $post_id, 'comprador', true ),
			'vin'            => get_post_meta( $post_id, 'vin', true ),
			'pais'           => get_post_meta( $post_id, 'pais', true ),
			'subasta'        => get_post_meta( $post_id, 'subasta', true ),
			'estado_subasta' => get_post_meta( $post_id, 'estado_subasta', true ),
			'factura_url'    => get_post_meta( $post_id, 'factura_pdf_url', true ),
			'foto_url'       => get_post_meta( $post_id, 'foto_vehiculo_url', true ),
			'admin_url'      => admin_url( 'post.php?post=' . $post_id . '&action=edit' ),
			'author_name'    => $author ? $author->display_name : __( 'Usuario desconocido', 'united-motor' ),
			'author_email'   => $author ? $author->user_email : '',
			'site_name'      => get_bloginfo( 'name' ),
		);

		$vin_label = ! empty( $data['vin'] ) ? $data['vin'] : '#' . $post_id;
		$subject   = sprintf(
			/* translators: %s: VIN or solicitud ID */
			__( '[%1$s] Nueva solicitud de transporte — %2$s', 'united-motor' ),
			$data['site_name'],
			$vin_label
		);
		$subject = apply_filters( 'united_motor_new_solicitud_email_subject', $subject, $post_id, $data );

		$body = self::render_template( 'solicitud-management', $data );
		$body = apply_filters( 'united_motor_new_solicitud_email_body', $body, $post_id, $data );

		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
		);
		if ( ! empty( $data['author_email'] ) ) {
			$headers[] = 'Reply-To: ' . $data['author_name'] . ' <' . $data['author_email'] . '>';
		}

		return wp_mail( $to, $subject, $body, $headers );
	}

	/**
	 * Carga una plantilla de correo y devuelve el HTML.
	 *
	 * @param string $template Nombre del archivo sin extensión.
	 * @param array  $data     Variables para la plantilla.
	 * @return string
	 */
	private static function render_template( $template, $data ) {
		$path = plugin_dir_path( dirname( __FILE__ ) ) . 'includes/email-templates/' . $template . '.php';
		if ( ! file_exists( $path ) ) {
			return '';
		}

		ob_start();
		// Variables disponibles en la plantilla: $data y acceso directo vía extract.
		extract( $data, EXTR_SKIP );
		include $path;
		return ob_get_clean();
	}
}
