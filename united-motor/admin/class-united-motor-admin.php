<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://honduespacios.es
 * @since      1.0.0
 *
 * @package    United_Motor
 * @subpackage United_Motor/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    United_Motor
 * @subpackage United_Motor/admin
 * @author     ludwins robles <ludwins36@gmail.com>
 */
class United_Motor_Admin
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
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in United_Motor_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The United_Motor_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/united-motor-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in United_Motor_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The United_Motor_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/united-motor-admin.js', array('jquery'), $this->version, false);

		// Ocultar select de mes por defecto en lista de solicitudes (usamos input type date)
		$screen = get_current_screen();
		if ($screen && $screen->id === 'edit-solicitud_transporte') {
			$hide_month = '.post-type-solicitud_transporte select[name="m"] { display: none !important; }';
			wp_add_inline_style($this->plugin_name, $hide_month);
		}
	}

	/**
	 * Register the custom post type for Transport Requests.
	 *
	 * @since    1.0.0
	 */
	public function register_solicitudes_cpt()
	{

		$labels = array(
			'name'                  => _x('Solicitudes', 'Post Type General Name', 'united-motor'),
			'singular_name'         => _x('Solicitud de Transporte', 'Post Type Singular Name', 'united-motor'),
			'menu_name'             => __('Solicitudes', 'united-motor'),
			'name_admin_bar'        => __('Solicitud de Transporte', 'united-motor'),
			'archives'              => __('Archivo de Solicitudes', 'united-motor'),
			'attributes'            => __('Atributos de Solicitud', 'united-motor'),
			'parent_item_colon'     => __('Solicitud Padre:', 'united-motor'),
			'all_items'             => __('Todas las Solicitudes', 'united-motor'),
			'add_new_item'          => __('Añadir nueva Solicitud', 'united-motor'),
			'add_new'               => __('Añadir nueva', 'united-motor'),
			'new_item'              => __('Nueva Solicitud', 'united-motor'),
			'edit_item'             => __('Editar Solicitud', 'united-motor'),
			'update_item'           => __('Actualizar Solicitud', 'united-motor'),
			'view_item'             => __('Ver Solicitud', 'united-motor'),
			'view_items'            => __('Ver Solicitudes', 'united-motor'),
			'search_items'          => __('Buscar Solicitud', 'united-motor'),
			'not_found'             => __('No encontrada', 'united-motor'),
			'not_found_in_trash'    => __('No encontrada en Papelera', 'united-motor'),
			'featured_image'        => __('Imagen destacada', 'united-motor'),
			'set_featured_image'    => __('Establecer imagen destacada', 'united-motor'),
			'remove_featured_image' => __('Borrar imagen destacada', 'united-motor'),
			'use_featured_image'    => __('Usar como imagen destacada', 'united-motor'),
			'insert_into_item'      => __('Insertar en la solicitud', 'united-motor'),
			'uploaded_to_this_item' => __('Subido a esta solicitud', 'united-motor'),
			'items_list'            => __('Lista de solicitudes', 'united-motor'),
			'items_list_navigation' => __('Navegación de lista de solicitudes', 'united-motor'),
			'filter_items_list'     => __('Filtrar lista de solicitudes', 'united-motor'),
		);
		$args = array(
			'label'                 => __('Solicitud de Transporte', 'united-motor'),
			'description'           => __('Solicitudes de Transporte', 'united-motor'),
			'labels'                => $labels,
			'supports'              => array('title', 'editor', 'custom-fields'),
			'hierarchical'          => false,
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => false,
			'capability_type'       => 'post',
			'menu_icon'             => 'dashicons-car',
		);
		register_post_type('solicitud_transporte', $args);

		// Registrar estados personalizados
		$statuses = array(
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

		foreach ($statuses as $status => $label) {
			register_post_status($status, array(
				'label'                     => $label,
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop($label . ' <span class="count">(%s)</span>', $label . ' <span class="count">(%s)</span>'),
			));
		}

		$this->add_driver_role();
	}

	/**
	 * Add Meta Box for Status.
	 *
	 * @since    1.0.0
	 */
	public function add_status_meta_box()
	{
		add_meta_box(
			'united_motor_status_box',
			__('Estado de la Solicitud', 'united-motor'),
			array($this, 'render_status_meta_box'),
			'solicitud_transporte',
			'side',
			'high'
		);

		add_meta_box(
			'united_motor_foto_box',
			__('Foto del Vehículo', 'united-motor'),
			array($this, 'render_foto_meta_box'),
			'solicitud_transporte',
			'normal',
			'default'
		);

		add_meta_box(
			'united_motor_factura_box',
			__('Factura de Compra', 'united-motor'),
			array($this, 'render_factura_meta_box'),
			'solicitud_transporte',
			'normal',
			'default'
		);
		
		add_meta_box(
			'united_motor_chat_box',
			__( 'Chat con el Conductor/Cliente', 'united-motor' ),
			array( $this, 'render_chat_meta_box' ),
			'solicitud_transporte',
			'normal',
			'high'
		);

		add_meta_box(
			'united_motor_price_box',
			__( 'Precio', 'united-motor' ),
			array( $this, 'render_price_meta_box' ),
			'solicitud_transporte',
			'side',
			'default'
		);

		$this->add_driver_meta_box();
	}

	/**
	 * Render Chat Meta Box.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_chat_meta_box( $post ) {
		$post_id = $post->ID;
		$user_id = get_current_user_id();
		
		// Get messages from custom table
		global $wpdb;
		$table_name = $wpdb->prefix . 'united_motor_messages';
		
		// Fetch messages
		$messages = $wpdb->get_results( $wpdb->prepare( 
			"SELECT * FROM $table_name WHERE post_id = %d ORDER BY created_at ASC", 
			$post_id 
		) );

		echo '<div id="united-motor-chat-container" style="border: 1px solid #ccc; padding: 10px; height: 300px; overflow-y: scroll; background: #fff; margin-bottom: 10px;">';
		
		if ( ! empty( $messages ) ) {
			foreach ( $messages as $msg ) {
				$is_me   = ( (int) $msg->author_id === $user_id );
				$style   = $is_me ? 'float: right; text-align: right; background-color: #dcf8c6;' : 'float: left; text-align: left; background-color: #f1f0f0;';
				$wrapper = 'clear: both; margin-bottom: 10px; max-width: 80%;';
				
				$author      = get_userdata( $msg->author_id );
				$author_name = $author ? $author->display_name : 'Usuario Desconocido';
				
				echo '<div style="' . $wrapper . '">';
				echo '<div style="padding: 8px 12px; border-radius: 10px; ' . $style . '">';
				echo '<strong style="display:block; font-size: 0.85em; color: #555; margin-bottom: 2px;">' . esc_html( $author_name ) . '</strong>';
				echo '<span style="color: #333;">' . nl2br( esc_html( $msg->message ) ) . '</span>';
				echo '<span style="display:block; font-size: 0.7em; color: #999; text-align:right; margin-top:2px;">' . esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $msg->created_at ) ) ) . '</span>';
				echo '</div>'; // End bubble
				echo '</div>'; // End clear wrapper
			}
		} else {
			echo '<p style="text-align: center; color: #999; padding-top: 100px;">' . __( 'No hay mensajes aún.', 'united-motor' ) . '</p>';
		}
		
		echo '</div>'; // End container

		// Form to send message
		echo '<div class="united-motor-chat-controls">';
		echo '<textarea id="united_motor_chat_message" name="united_motor_chat_message" rows="3" style="width: 100%; margin-bottom: 5px;" placeholder="' . __( 'Escribe tu mensaje aquí...', 'united-motor' ) . '"></textarea>';
		echo '<button type="button" id="united_motor_send_chat" class="button button-primary" data-post-id="' . $post_id . '">' . __( 'Enviar Mensaje', 'united-motor' ) . '</button>';
		echo '<span id="united-motor-chat-spinner" class="spinner" style="float: none; margin-left: 5px;"></span>';
		echo '</div>';

		// Local Script for sending messages via AJAX
		?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('#united_motor_send_chat').on('click', function(e) {
				e.preventDefault();
				var message = $('#united_motor_chat_message').val();
				var postId = $(this).data('post-id');
				var spinner = $('#united-motor-chat-spinner');

				if ( ! message.trim() ) {
					alert('<?php _e( "El mensaje no puede estar vacío.", "united-motor" ); ?>');
					return;
				}

				spinner.addClass('is-active');
				$(this).prop('disabled', true);

				$.ajax({
					url: '<?php echo esc_url_raw( rest_url( 'united-motor/v1/solicitudes/' . $post_id . '/chat' ) ); ?>',
					method: 'POST',
					beforeSend: function( xhr ) {
						xhr.setRequestHeader( 'X-WP-Nonce', '<?php echo wp_create_nonce( 'wp_rest' ); ?>' );
					},
					data: {
						message: message
					},
					success: function(response) {
						// Reload page to see message or append dynamically
						// For simplicity in Admin, reloading the page or the div is easiest.
						// Let's just reload the page for now to be safe with state.
						location.reload();
					},
					error: function(response) {
						alert('<?php _e( "Error al enviar el mensaje.", "united-motor" ); ?>');
						console.log(response);
						spinner.removeClass('is-active');
						$('#united_motor_send_chat').prop('disabled', false);
					}
				});
			});
		});
		</script>
		<?php
	}

	/**
	 * Render Foto Meta Box.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_foto_meta_box($post)
	{
		$foto_url = get_post_meta($post->ID, 'foto_vehiculo_url', true);
		if ($foto_url) {
			echo '<div style="text-align:center;">';
			echo '<img src="' . esc_url($foto_url) . '" style="max-width:100%; height:auto; border:1px solid #ddd; padding:5px; background:#fff;" />';
			echo '<p><a href="' . esc_url($foto_url) . '" target="_blank" class="button">' . __('Ver imagen original', 'united-motor') . '</a></p>';
			echo '</div>';
		} else {
			echo '<p>' . __('No se ha adjuntado ninguna foto.', 'united-motor') . '</p>';
		}
	}

	/**
	 * Render Factura Meta Box.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_factura_meta_box($post)
	{
		$factura_url = get_post_meta($post->ID, 'factura_pdf_url', true);
		if ($factura_url) {
			echo '<div style="text-align:center;">';
			echo '<iframe src="' . esc_url($factura_url) . '" style="width:100%; height:500px; border:1px solid #ddd;"></iframe>';
			echo '<p style="margin-top:10px;"><a href="' . esc_url($factura_url) . '" target="_blank" class="button button-primary">' . __('Descargar / Ver PDF Completo', 'united-motor') . '</a></p>';
			echo '</div>';
		} else {
			echo '<p>' . __('No se ha adjuntado ninguna factura.', 'united-motor') . '</p>';
		}
	}

	/**
	 * Render Meta Box Content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_status_meta_box($post)
	{
		// Nonce for verification
		wp_nonce_field('united_motor_save_status', 'united_motor_status_nonce');

		$current_status = $post->post_status;
		$statuses       = array(
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

		$is_driver = false;
		$user = wp_get_current_user();
		if (in_array('driver', (array) $user->roles)) {
			$is_driver = true;
		}

		echo '<label for="united_motor_post_status">' . __('Estado Actual:', 'united-motor') . '</label>';
		echo '<br>';

		if ($is_driver) {
			// Read-Only Display for Driver
			$current_label = isset($statuses[$current_status]) ? $statuses[$current_status] : $current_status;
			echo '<div style="margin-top:5px; padding: 8px; background: #eee; border: 1px solid #ddd; border-radius: 4px; font-weight: bold;">';
			echo esc_html($current_label);
			echo '</div>';
			echo '<p class="description" style="margin-top:10px;">' . __('No tienes permisos para cambiar el estado.', 'united-motor') . '</p>';
		} else {
			// Editable Select for Admins
			echo '<select name="united_motor_post_status" id="united_motor_post_status" style="width:100%; margin-top:5px;">';
			foreach ($statuses as $value => $label) {
				echo '<option value="' . esc_attr($value) . '" ' . selected($current_status, $value, false) . '>' . esc_html($label) . '</option>';
			}
			echo '</select>';
			echo '<p class="description" style="margin-top:10px;">' . __('Selecciona el estado actual del transporte.', 'united-motor') . '</p>';
		}
	}

	/**
	 * Save Meta Box Data.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save_status_meta_box($post_id)
	{
		// Checks safety
		if (! isset($_POST['united_motor_status_nonce'])) {
			return;
		}
		if (! wp_verify_nonce($_POST['united_motor_status_nonce'], 'united_motor_save_status')) {
			return;
		}
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		if (! current_user_can('edit_post', $post_id)) {
			return;
		}

		// Save Logic
		if (isset($_POST['united_motor_post_status'])) {
			$new_status = sanitize_text_field($_POST['united_motor_post_status']);
			$post       = get_post($post_id);

			// Only update if status changed to avoid infinite loop
			if ($post->post_status !== $new_status) {
				// Unhook this function to prevent infinite loop
				remove_action('save_post', array($this, 'save_status_meta_box'));

				wp_update_post(array(
					'ID'          => $post_id,
					'post_status' => $new_status,
				));

				// Re-hook (optional, post processing usually ends here)
				add_action('save_post', array($this, 'save_status_meta_box'));

				// Enviar notificación al usuario (autor de la solicitud)
				$statuses = array(
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
		
				$status_label = isset($statuses[$new_status]) ? $statuses[$new_status] : $new_status;
				$message      = sprintf(__('El estado de tu solicitud "%s" ha cambiado a "%s".', 'united-motor'), $post->post_title, $status_label);
		
				if (class_exists('United_Motor_Notifications')) {
					United_Motor_Notifications::add($post->post_author, $message);
				}
			}
		}

		// Guardar campo Precio (mismo nonce que estado)
		if ( isset( $_POST['united_motor_price'] ) ) {
			$price = sanitize_text_field( wp_unslash( $_POST['united_motor_price'] ) );
			update_post_meta( $post_id, 'price', $price );
		}

		$this->save_driver_meta_box($post_id); // Ensure driver meta is also saved when status is saved
	}

	/**
	 * Render Meta Box Precio.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_price_meta_box( $post ) {
		wp_nonce_field( 'united_motor_save_status', 'united_motor_status_nonce' );
		$price = get_post_meta( $post->ID, 'price', true );
		echo '<label for="united_motor_price">' . esc_html__( 'Precio:', 'united-motor' ) . '</label>';
		echo '<input type="text" name="united_motor_price" id="united_motor_price" value="' . esc_attr( $price ) . '" class="widefat" placeholder="0.00" style="margin-top:5px;" />';
		echo '<p class="description" style="margin-top:8px;">' . esc_html__( 'Precio de la solicitud (ej: 1500.00).', 'united-motor' ) . '</p>';
	}

	/**
	 * Add custom columns to the Transport Requests list.
	 *
	 * @param array $columns The existing columns.
	 * @return array The updated columns.
	 */
	public function add_custom_columns($columns)
	{
		$new_columns = array();
		$new_columns['cb']        = $columns['cb']; // Checkbox
		$new_columns['foto']      = __('Foto', 'united-motor'); // Miniatura
		$new_columns['title']     = $columns['title']; // Título
		$new_columns['vin']       = __('Placa / VIN', 'united-motor'); // VIN explícito
		$new_columns['driver']    = __('Conductor', 'united-motor'); // Driver column
		$new_columns['usuario']   = __('Usuario', 'united-motor');
		$new_columns['status_um'] = __('Estado', 'united-motor'); // Estado personalizado
		$new_columns['comprador'] = __('Comprador', 'united-motor');
		$new_columns['pais']      = __('Dirección / Destino', 'united-motor'); // Cambio de etiqueta
		$new_columns['date']      = $columns['date'];
		return $new_columns;
	}

	/**
	 * Render content for custom columns.
	 *
	 * @param string $column The name of the column.
	 * @param int    $post_id The ID of the post.
	 */
	public function render_custom_columns($column, $post_id)
	{
		switch ($column) {
			case 'foto':
				$foto_url = get_post_meta($post_id, 'foto_vehiculo_url', true);
				if ($foto_url) {
					echo '<img src="' . esc_url($foto_url) . '" style="width:60px; height:auto; border-radius:3px;" />';
				} else {
					echo '<span style="color:#ccc;">No imagen</span>';
				}
				break;

			case 'vin':
				$vin = get_post_meta($post_id, 'vin', true);
				echo $vin ? esc_html($vin) : '—';
				break;

			case 'driver':
				$driver_id = get_post_meta($post_id, '_assigned_driver_id', true);
				if ($driver_id) {
					$user_info = get_userdata($driver_id);
					echo $user_info ? esc_html($user_info->display_name) : '—';
				} else {
					echo '—';
				}
				break;

			case 'usuario':
				$post = get_post($post_id);
				if ($post && $post->post_author) {
					$user_info = get_userdata($post->post_author);
					if ($user_info) {
						$edit_link = get_edit_user_link($post->post_author);
						echo $edit_link ? '<a href="' . esc_url($edit_link) . '">' . esc_html($user_info->display_name) . '</a>' : esc_html($user_info->display_name);
					} else {
						echo '—';
					}
				} else {
					echo '—';
				}
				break;

			case 'status_um':
				$status_slug = get_post_status($post_id);
				$status_obj  = get_post_status_object($status_slug);
				if ($status_obj) {
					// Check for specific colors or classes if needed, for now generic badge
					echo '<span class="badge badge-status-' . esc_attr($status_slug) . '" style="padding: 3px 8px; border-radius: 3px; background: #eee; font-weight: bold;">' . esc_html($status_obj->label) . '</span>';
				} else {
					echo esc_html($status_slug);
				}
				break;

			case 'comprador':
				$comprador = get_post_meta($post_id, 'comprador', true);
				echo $comprador ? esc_html($comprador) : '—';
				break;

			case 'pais': // Corresponds to 'Dirección / Destino' column key
				$pais    = get_post_meta($post_id, 'pais', true);
				$subasta = get_post_meta($post_id, 'subasta', true);
				$estado  = get_post_meta($post_id, 'estado_subasta', true);

				$output = array();
				if ($pais) $output[] = '<strong>' . esc_html($pais) . '</strong>';
				if ($subasta) $output[] = 'Subasta: ' . esc_html($subasta);
				if ($estado) $output[] = 'Estado: ' . esc_html($estado);

				echo ! empty($output) ? implode('<br>', $output) : '—';
				break;
		}
	}

	/**
	 * Add 'Driver' role.
	 */
	public function add_driver_role()
	{
		if (! get_role('driver')) {
			add_role(
				'driver',
				__('Conductor', 'united-motor'),
				array(
					'read'         => true,
					'edit_posts'   => true,
					'delete_posts' => false,
					'upload_files' => true,
				)
			);
		}

		self::sync_driver_role_capabilities();
	}

	/**
	 * Asegura capacidades de subida en conductores ya existentes.
	 */
	public static function sync_driver_role_capabilities()
	{
		$role = get_role('driver');
		if ($role) {
			$role->add_cap('upload_files');
			$role->add_cap('edit_posts');
		}
	}

	/**
	 * Sincroniza capacidades del rol conductor en cada carga (sitios ya activos).
	 */
	public function sync_driver_role_capabilities_on_init()
	{
		self::sync_driver_role_capabilities();
	}

	/**
	 * Add Meta Box for selecting a Driver.
	 */
	public function add_driver_meta_box()
	{
		// Only admins or editors should see this to assign drivers
		if (current_user_can('manage_options') || current_user_can('edit_others_posts')) {
			add_meta_box(
				'united_motor_driver_box',
				__('Asignar Conductor', 'united-motor'),
				array($this, 'render_driver_meta_box'),
				'solicitud_transporte',
				'side',
				'default'
			);
		}
	}

	/**
	 * Render Driver Meta Box.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_driver_meta_box($post)
	{
		wp_nonce_field('united_motor_save_driver', 'united_motor_driver_nonce');

		$current_driver = get_post_meta($post->ID, '_assigned_driver_id', true);

		// Get users with role 'driver'
		$args = array(
			'role'    => 'driver',
			'orderby' => 'display_name',
		);
		$drivers = get_users($args);

		echo '<label for="united_motor_driver_select">' . __('Seleccione un conductor:', 'united-motor') . '</label>';
		echo '<select name="united_motor_driver_id" id="united_motor_driver_select" style="width:100%; margin-top:5px;">';
		echo '<option value="">' . __('— Sin Asignar —', 'united-motor') . '</option>';
		foreach ($drivers as $user) {
			echo '<option value="' . esc_attr($user->ID) . '" ' . selected($current_driver, $user->ID, false) . '>' . esc_html($user->display_name) . ' (' . esc_html($user->user_email) . ')</option>';
		}
		echo '</select>';
	}

	/**
	 * Save Driver Meta Box.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save_driver_meta_box($post_id)
	{
		if (! isset($_POST['united_motor_driver_nonce'])) {
			return;
		}
		if (! wp_verify_nonce($_POST['united_motor_driver_nonce'], 'united_motor_save_driver')) {
			return;
		}
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}
		// Only users who can edit others posts can assign drivers (Admins/Managers)
		if (! current_user_can('edit_others_posts')) {
			return;
		}

		if (isset($_POST['united_motor_driver_id'])) {
			$new_driver_id     = sanitize_text_field($_POST['united_motor_driver_id']);
			$current_driver_id = get_post_meta($post_id, '_assigned_driver_id', true);

			if ($new_driver_id != $current_driver_id) {
				update_post_meta($post_id, '_assigned_driver_id', $new_driver_id);
				
				// Enviar notificaciones si hay nuevo conductor asignado
				if (! empty($new_driver_id) && class_exists('United_Motor_Notifications')) {
					$post   = get_post($post_id);
					$driver = get_userdata($new_driver_id);

					// 1. Notificar al Cliente (Autor de la solicitud)
					$client_message = sprintf(__('Se ha asignado el conductor %s a tu solicitud "%s".', 'united-motor'), $driver->display_name, $post->post_title);
					United_Motor_Notifications::add($post->post_author, $client_message);

					// 2. Notificar al Conductor
					$driver_message = sprintf(__('Se te ha asignado una nueva solicitud de transporte: "%s".', 'united-motor'), $post->post_title);
					United_Motor_Notifications::add($new_driver_id, $driver_message);
				}
			}
		}
	}

	/**
	 * Añade filtros encima de la lista de solicitudes: usuario, conductor y fecha (datepicker).
	 */
	public function add_solicitudes_usuario_filter()
	{
		global $typenow;
		if ($typenow !== 'solicitud_transporte') {
			return;
		}

		global $wpdb;

		// Filtro por usuario (autor)
		$author_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT post_author FROM {$wpdb->posts} WHERE post_type = %s AND post_author > 0",
				'solicitud_transporte'
			)
		);
		$users = array();
		if (! empty($author_ids)) {
			$users = get_users(array(
				'include' => array_map('intval', $author_ids),
				'orderby' => 'display_name',
			));
		}
		$current_author = isset($_GET['author']) ? (int) $_GET['author'] : 0;
		echo '<select name="author" id="um_filter_author" class="postform">';
		echo '<option value="">' . esc_html__('Todos los usuarios', 'united-motor') . '</option>';
		foreach ($users as $user) {
			printf(
				'<option value="%d"%s>%s</option>',
				(int) $user->ID,
				selected($current_author, $user->ID, false),
				esc_html($user->display_name)
			);
		}
		echo '</select>';

		// Filtro por conductor
		$drivers = get_users(array(
			'role'    => 'driver',
			'orderby' => 'display_name',
		));
		$current_driver = isset($_GET['um_driver']) ? (int) $_GET['um_driver'] : 0;
		echo '<select name="um_driver" id="um_filter_driver" class="postform">';
		echo '<option value="">' . esc_html__('Todos los conductores', 'united-motor') . '</option>';
		foreach ($drivers as $driver) {
			printf(
				'<option value="%d"%s>%s</option>',
				(int) $driver->ID,
				selected($current_driver, $driver->ID, false),
				esc_html($driver->display_name)
			);
		}
		echo '</select>';

		// Filtro por fecha (input type date)
		$date_from = isset($_GET['um_date_from']) ? sanitize_text_field(wp_unslash($_GET['um_date_from'])) : '';
		$date_to   = isset($_GET['um_date_to']) ? sanitize_text_field(wp_unslash($_GET['um_date_to'])) : '';
		echo '<label for="um_date_from">' . esc_html__('Desde', 'united-motor') . '</label>';
		echo ' <input type="date" name="um_date_from" id="um_date_from" value="' . esc_attr($date_from) . '" class="postform" style="width:140px;" /> ';
		echo '<label for="um_date_to">' . esc_html__('Hasta', 'united-motor') . '</label>';
		echo ' <input type="date" name="um_date_to" id="um_date_to" value="' . esc_attr($date_to) . '" class="postform" style="width:140px;" />';
	}

	/**
	 * Filter Transport Requests: autor, conductor y rango de fechas.
	 * Los conductores solo ven sus solicitudes asignadas.
	 *
	 * @param WP_Query $query The query object.
	 */
	public function filter_requests_by_driver($query)
	{
		if (! is_admin() || ! $query->is_main_query()) {
			return;
		}

		if ($query->get('post_type') !== 'solicitud_transporte') {
			return;
		}

		// Filtro por usuario (autor de la solicitud)
		if (isset($_GET['author']) && $_GET['author'] !== '') {
			$query->set('author', (int) $_GET['author']);
		}

		// Filtro por conductor asignado
		$user = wp_get_current_user();
		if (in_array('driver', (array) $user->roles)) {
			$query->set('meta_key', '_assigned_driver_id');
			$query->set('meta_value', $user->ID);
		} elseif (isset($_GET['um_driver']) && $_GET['um_driver'] !== '') {
			$query->set('meta_key', '_assigned_driver_id');
			$query->set('meta_value', (int) $_GET['um_driver']);
		}

		// Filtro por rango de fechas (datepicker)
		$date_from = isset($_GET['um_date_from']) ? sanitize_text_field(wp_unslash($_GET['um_date_from'])) : '';
		$date_to   = isset($_GET['um_date_to']) ? sanitize_text_field(wp_unslash($_GET['um_date_to'])) : '';
		if ($date_from !== '' || $date_to !== '') {
			$date_query = array('inclusive' => true);
			if ($date_from !== '' && strtotime($date_from)) {
				$date_query['after'] = $date_from . ' 00:00:00';
			}
			if ($date_to !== '' && strtotime($date_to)) {
				$date_query['before'] = $date_to . ' 23:59:59';
			}
			if (isset($date_query['after']) || isset($date_query['before'])) {
				$query->set('date_query', array($date_query));
			}
		}
	}

	/**
	 * Restrict menu items for Driver role.
	 * Hide "Add New".
	 */
	public function restrict_driver_menu()
	{
		$user = wp_get_current_user();
		if (in_array('driver', (array) $user->roles)) {
			// Remove "Add New" submenu
			remove_submenu_page('edit.php?post_type=solicitud_transporte', 'post-new.php?post_type=solicitud_transporte');
			// Remove default "Posts" menu if not needed (optional, assuming they only need Requests)
			remove_menu_page('edit.php');
		}
	}

	/**
	 * Map capabilities for Driver role.
	 * Ensure they can read but not edit/delete freely.
	 */
	public function restrict_driver_capabilities($caps, $cap, $user_id, $args)
	{
		$user = get_userdata($user_id);
		if (! $user || ! in_array('driver', (array) $user->roles)) {
			return $caps;
		}

		// Deny 'publish_posts'
		if ('publish_posts' == $cap) {
			$caps[] = 'do_not_allow';
		}

		return $caps;
	}

	/**
	 * Add Drivers Menu.
	 */
	public function add_driver_menu()
	{
		add_menu_page(
			__('Conductores', 'united-motor'),
			__('Conductores', 'united-motor'),
			'manage_options',
			'united-motor-drivers',
			array($this, 'render_drivers_page'),
			'dashicons-steering-wheel',
			6
		);
		// Submenu: List
		add_submenu_page(
			'united-motor-drivers',
			__('Todos los Conductores', 'united-motor'),
			__('Todos los Conductores', 'united-motor'),
			'manage_options',
			'united-motor-drivers',
			array($this, 'render_drivers_page')
		);
		// Submenu: Add New
		add_submenu_page(
			'united-motor-drivers',
			__('Añadir Nuevo', 'united-motor'),
			__('Añadir Nuevo', 'united-motor'),
			'manage_options',
			'united-motor-drivers-new',
			array($this, 'render_add_driver_page')
		);
	}

	/**
	 * Render Drivers List Page.
	 */
	public function render_drivers_page()
	{
		// Mensajes de éxito
		if (isset($_GET['message']) && $_GET['message'] == 'created') {
			echo '<div class="notice notice-success is-dismissible"><p>' . __('Conductor creado exitosamente.', 'united-motor') . '</p></div>';
		}
?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php _e('Conductores', 'united-motor'); ?></h1>
			<a href="<?php echo admin_url('admin.php?page=united-motor-drivers-new'); ?>" class="page-title-action"><?php _e('Añadir nuevo', 'united-motor'); ?></a>
			<hr class="wp-header-end">

			<table class="wp-list-table widefat fixed striped table-view-list users">
				<thead>
					<tr>
						<th scope="col" class="manage-column column-username"><?php _e('Conductor', 'united-motor'); ?></th>
						<th scope="col" class="manage-column"><?php _e('Nombre', 'united-motor'); ?></th>
						<th scope="col" class="manage-column"><?php _e('Email', 'united-motor'); ?></th>
						<th scope="col" class="manage-column"><?php _e('Vehículo / Grúa', 'united-motor'); ?></th>
						<th scope="col" class="manage-column"><?php _e('Placa', 'united-motor'); ?></th>
						<th scope="col" class="manage-column"><?php _e('Licencia', 'united-motor'); ?></th>
						<th scope="col" class="manage-column"><?php _e('Documentos', 'united-motor'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$drivers = get_users(array('role' => 'driver'));
					if (! empty($drivers)) {
						foreach ($drivers as $user) {
							$vehiculo = get_user_meta($user->ID, 'um_vehiculo_modelo', true);
							$placa    = get_user_meta($user->ID, 'um_vehiculo_placa', true);
							$licencia = get_user_meta($user->ID, 'um_licencia_num', true);

							$licencia_url = get_user_meta($user->ID, 'um_licencia_file', true);
							$docs_url     = get_user_meta($user->ID, 'um_antecedentes_file', true);

							$edit_link = get_edit_user_link($user->ID);

							echo '<tr>';
							echo '<td class="username column-username">' . get_avatar($user->ID, 32) . ' <strong><a href="' . esc_url($edit_link) . '">' . esc_html($user->user_login) . '</a></strong><br><div class="row-actions"><span class="edit"><a href="' . esc_url($edit_link) . '">Editar</a></span></div></td>';
							echo '<td>' . esc_html($user->display_name) . '</td>';
							echo '<td><a href="mailto:' . esc_attr($user->user_email) . '">' . esc_html($user->user_email) . '</a></td>';
							echo '<td>' . esc_html($vehiculo ? $vehiculo : '—') . '</td>';
							echo '<td>' . esc_html($placa ? $placa : '—') . '</td>';
							echo '<td>' . esc_html($licencia ? $licencia : '—') . '</td>';
							echo '<td>';
							if ($licencia_url) echo '<a href="' . esc_url($licencia_url) . '" target="_blank" class="button button-small">Licencia</a> ';
							if ($docs_url) echo '<a href="' . esc_url($docs_url) . '" target="_blank" class="button button-small">Legales</a>';
							echo '</td>';
							echo '</tr>';
						}
					} else {
						echo '<tr><td colspan="7">' . __('No se encontraron conductores.', 'united-motor') . '</td></tr>';
					}
					?>
				</tbody>
			</table>
		</div>
	<?php
	}

	/**
	 * Render Add New Driver Page (Custom Form).
	 */
	public function render_add_driver_page()
	{
		// Handle Form Submission
		if (isset($_POST['um_create_driver_nonce']) && wp_verify_nonce($_POST['um_create_driver_nonce'], 'um_create_driver')) {

			$username = sanitize_user($_POST['user_login']);
			$email    = sanitize_email($_POST['email']);
			$password = $_POST['pass1']; // Should be validated
			$first_name = sanitize_text_field($_POST['first_name']);
			$last_name  = sanitize_text_field($_POST['last_name']);

			$errors = array();

			if (username_exists($username)) {
				$errors[] = 'El nombre de usuario ya existe.';
			}
			if (email_exists($email)) {
				$errors[] = 'El correo electrónico ya está registrado.';
			}
			if (empty($password)) {
				$errors[] = 'La contraseña es obligatoria.';
			}

			if (empty($errors)) {
				$user_id = wp_create_user($username, $password, $email);

				if (! is_wp_error($user_id)) {
					// Set Role
					$user = new WP_User($user_id);
					$user->set_role('driver');

					// Update Standard Meta
					wp_update_user(array(
						'ID' => $user_id,
						'first_name' => $first_name,
						'last_name'  => $last_name,
					));

					// Update Custom Meta
					if (isset($_POST['um_vehiculo_modelo'])) update_user_meta($user_id, 'um_vehiculo_modelo', sanitize_text_field($_POST['um_vehiculo_modelo']));
					if (isset($_POST['um_vehiculo_placa'])) update_user_meta($user_id, 'um_vehiculo_placa', sanitize_text_field($_POST['um_vehiculo_placa']));
					if (isset($_POST['um_licencia_num'])) update_user_meta($user_id, 'um_licencia_num', sanitize_text_field($_POST['um_licencia_num']));
					if (isset($_POST['um_licencia_file'])) update_user_meta($user_id, 'um_licencia_file', esc_url_raw($_POST['um_licencia_file']));
					if (isset($_POST['um_antecedentes_file'])) update_user_meta($user_id, 'um_antecedentes_file', esc_url_raw($_POST['um_antecedentes_file']));

					// Redirect to driver list with success message
					wp_redirect(admin_url('admin.php?page=united-motor-drivers&message=created'));
					exit;
				} else {
					echo '<div class="notice notice-error is-dismissible"><p>' . $user_id->get_error_message() . '</p></div>';
				}
			} else {
				foreach ($errors as $error) {
					echo '<div class="notice notice-error is-dismissible"><p>' . $error . '</p></div>';
				}
			}
		}

		// Enqueue Media
		wp_enqueue_media();
	?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php _e('Añadir Nuevo Conductor', 'united-motor'); ?></h1>
			<hr class="wp-header-end">

			<form method="post" action="" class="validate" novalidate="novalidate">
				<?php wp_nonce_field('um_create_driver', 'um_create_driver_nonce'); ?>

				<h3><?php _e('Datos de la Cuenta', 'united-motor'); ?></h3>
				<table class="form-table" role="presentation">
					<tr class="form-field form-required">
						<th scope="row"><label for="user_login"><?php _e('Nombre de usuario', 'united-motor'); ?> <span class="description"><?php _e('(obligatorio)', 'united-motor'); ?></span></label></th>
						<td><input name="user_login" type="text" id="user_login" value="" aria-required="true" autocapitalize="none" autocorrect="off" maxlength="60" /></td>
					</tr>
					<tr class="form-field form-required">
						<th scope="row"><label for="email"><?php _e('Correo electrónico', 'united-motor'); ?> <span class="description"><?php _e('(obligatorio)', 'united-motor'); ?></span></label></th>
						<td><input name="email" type="email" id="email" value="" /></td>
					</tr>
					<tr class="form-field">
						<th scope="row"><label for="first_name"><?php _e('Nombre', 'united-motor'); ?></label></th>
						<td><input name="first_name" type="text" id="first_name" value="" /></td>
					</tr>
					<tr class="form-field">
						<th scope="row"><label for="last_name"><?php _e('Apellidos', 'united-motor'); ?></label></th>
						<td><input name="last_name" type="text" id="last_name" value="" /></td>
					</tr>
					<tr class="form-field form-required">
						<th scope="row"><label for="pass1"><?php _e('Contraseña', 'united-motor'); ?> <span class="description"><?php _e('(obligatorio)', 'united-motor'); ?></span></label></th>
						<td><input name="pass1" type="password" id="pass1" class="regular-text" /></td>
					</tr>
				</table>

				<h3><?php _e('Información del Vehículo y Documentación', 'united-motor'); ?></h3>
				<table class="form-table">
					<tr>
						<th><label for="um_vehiculo_modelo"><?php _e('Vehículo / Grúa', 'united-motor'); ?></label></th>
						<td>
							<input type="text" name="um_vehiculo_modelo" id="um_vehiculo_modelo" class="regular-text" />
							<p class="description"><?php _e('Modelo y tipo de grúa o vehículo.', 'united-motor'); ?></p>
						</td>
					</tr>
					<tr>
						<th><label for="um_vehiculo_placa"><?php _e('Placa del Vehículo', 'united-motor'); ?></label></th>
						<td>
							<input type="text" name="um_vehiculo_placa" id="um_vehiculo_placa" class="regular-text" />
						</td>
					</tr>
					<tr>
						<th><label for="um_licencia_num"><?php _e('Número de Licencia', 'united-motor'); ?></label></th>
						<td>
							<input type="text" name="um_licencia_num" id="um_licencia_num" class="regular-text" />
						</td>
					</tr>
					<tr>
						<th><label for="um_licencia_file"><?php _e('Foto de Licencia', 'united-motor'); ?></label></th>
						<td>
							<input type="text" name="um_licencia_file" id="um_licencia_file" class="regular-text" />
							<button type="button" class="button button-secondary upload_image_button" data-target="um_licencia_file"><?php _e('Subir Imagen', 'united-motor'); ?></button>
						</td>
					</tr>
					<tr>
						<th><label for="um_antecedentes_file"><?php _e('Antecedentes / Docs Legales', 'united-motor'); ?></label></th>
						<td>
							<input type="text" name="um_antecedentes_file" id="um_antecedentes_file" class="regular-text" />
							<button type="button" class="button button-secondary upload_image_button" data-target="um_antecedentes_file"><?php _e('Subir Documento', 'united-motor'); ?></button>
						</td>
					</tr>
				</table>

				<p class="submit"><input type="submit" name="createuser" id="createusersub" class="button button-primary" value="<?php _e('Añadir nuevo conductor', 'united-motor'); ?>" /></p>
			</form>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$('.upload_image_button').click(function(e) {
					e.preventDefault();
					var button = $(this);
					var target_input_id = button.data('target');
					var file_frame = wp.media.frames.file_frame = wp.media({
						title: '<?php _e('Seleccionar Archivo', 'united-motor'); ?>',
						button: {
							text: '<?php _e('Usar Archivo', 'united-motor'); ?>'
						},
						multiple: false
					});
					file_frame.on('select', function() {
						var attachment = file_frame.state().get('selection').first().toJSON();
						$('#' + target_input_id).val(attachment.url);
					});
					file_frame.open();
				});
			});
		</script>
	<?php
	}

	/**
	 * Add custom fields to User Profile (Driver Info).
	 *
	 * @param WP_User|string $user User object or 'add-new-user' context.
	 */
	public function add_driver_user_fields($user)
	{
		// Only render for edits, new user is handled by render_add_driver_page
		if (! is_object($user)) {
			return;
		}

		$object = $user;
		// Check role, only show for drivers or if admin creates via standard way (optional, keeping it for robustness)

		$vehiculo_modelo = $object ? get_user_meta($object->ID, 'um_vehiculo_modelo', true) : '';
		$vehiculo_placa  = $object ? get_user_meta($object->ID, 'um_vehiculo_placa', true) : '';
		$licencia_num    = $object ? get_user_meta($object->ID, 'um_licencia_num', true) : '';

		// Files (URLs)
		$licencia_file   = $object ? get_user_meta($object->ID, 'um_licencia_file', true) : '';
		$antecedentes    = $object ? get_user_meta($object->ID, 'um_antecedentes_file', true) : '';

		// Enqueue media if not already available
		wp_enqueue_media();
	?>
		<h3><?php _e('Información de Conductor', 'united-motor'); ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="um_vehiculo_modelo"><?php _e('Vehículo / Grúa', 'united-motor'); ?></label></th>
				<td>
					<input type="text" name="um_vehiculo_modelo" id="um_vehiculo_modelo" value="<?php echo esc_attr($vehiculo_modelo); ?>" class="regular-text" /><br />
					<span class="description"><?php _e('Modelo y tipo de grúa o vehículo.', 'united-motor'); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="um_vehiculo_placa"><?php _e('Placa del Vehículo', 'united-motor'); ?></label></th>
				<td>
					<input type="text" name="um_vehiculo_placa" id="um_vehiculo_placa" value="<?php echo esc_attr($vehiculo_placa); ?>" class="regular-text" />
				</td>
			</tr>
			<tr>
				<th><label for="um_licencia_num"><?php _e('Número de Licencia', 'united-motor'); ?></label></th>
				<td>
					<input type="text" name="um_licencia_num" id="um_licencia_num" value="<?php echo esc_attr($licencia_num); ?>" class="regular-text" />
				</td>
			</tr>
			<tr>
				<th><label for="um_licencia_file"><?php _e('Foto de Licencia', 'united-motor'); ?></label></th>
				<td>
					<input type="text" name="um_licencia_file" id="um_licencia_file" value="<?php echo esc_attr($licencia_file); ?>" class="regular-text" />
					<button type="button" class="button button-secondary upload_image_button" data-target="um_licencia_file"><?php _e('Subir Imagen', 'united-motor'); ?></button>
					<br /><span class="description"><?php _e('URL del archivo de la licencia.', 'united-motor'); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="um_antecedentes_file"><?php _e('Antecedentes Penales / Docs Legales', 'united-motor'); ?></label></th>
				<td>
					<input type="text" name="um_antecedentes_file" id="um_antecedentes_file" value="<?php echo esc_attr($antecedentes); ?>" class="regular-text" />
					<button type="button" class="button button-secondary upload_image_button" data-target="um_antecedentes_file"><?php _e('Subir Documento', 'united-motor'); ?></button>
				</td>
			</tr>
		</table>

		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$('.upload_image_button').off('click').on('click', function(e) {
					e.preventDefault();
					var button = $(this);
					var target_input_id = button.data('target');

					var file_frame = wp.media.frames.file_frame = wp.media({
						title: '<?php _e('Seleccionar Documento', 'united-motor'); ?>',
						button: {
							text: '<?php _e('Usar Documento', 'united-motor'); ?>'
						},
						multiple: false
					});

					file_frame.on('select', function() {
						var attachment = file_frame.state().get('selection').first().toJSON();
						$('#' + target_input_id).val(attachment.url);
					});

					file_frame.open();
				});
			});
		</script>
<?php
	}

	/**
	 * Save Driver Profile Fields.
	 *
	 * @param int $user_id User ID.
	 */
	public function save_driver_user_fields($user_id)
	{
		// Verify permissions
		// 'save_driver_user_fields' hooked to 'edit_user_profile_update' passes $user_id
		// 'personal_options_update' passes $user_id
		// 'user_register' passes $user_id

		if (! current_user_can('edit_user', $user_id)) {
			return false;
		}

		if (isset($_POST['um_vehiculo_modelo'])) {
			update_user_meta($user_id, 'um_vehiculo_modelo', sanitize_text_field($_POST['um_vehiculo_modelo']));
		}
		if (isset($_POST['um_vehiculo_placa'])) {
			update_user_meta($user_id, 'um_vehiculo_placa', sanitize_text_field($_POST['um_vehiculo_placa']));
		}
		if (isset($_POST['um_licencia_num'])) {
			update_user_meta($user_id, 'um_licencia_num', sanitize_text_field($_POST['um_licencia_num']));
		}
		if (isset($_POST['um_licencia_file'])) {
			update_user_meta($user_id, 'um_licencia_file', esc_url_raw($_POST['um_licencia_file']));
		}
		if (isset($_POST['um_antecedentes_file'])) {
			update_user_meta($user_id, 'um_antecedentes_file', esc_url_raw($_POST['um_antecedentes_file']));
		}
	}
}
