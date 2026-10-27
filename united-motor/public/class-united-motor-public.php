<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://honduespacios.es
 * @since      1.0.0
 *
 * @package    United_Motor
 * @subpackage United_Motor/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    United_Motor
 * @subpackage United_Motor/public
 * @author     ludwins robles <ludwins36@gmail.com>
 */
class United_Motor_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/united-motor-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/united-motor-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Registrar shortcodes públicos.
	 *
	 * @since    1.0.0
	 */
	public function register_shortcodes() {
		add_shortcode( 'united_motor_login', array( $this, 'render_login_shortcode' ) );
		add_shortcode( 'united_motor_services', array( $this, 'render_services_shortcode' ) );
	}

	/**
	 * Render del shortcode de login.
	 *
	 * @since    1.0.0
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function render_login_shortcode( $atts = array() ) {
		if ( is_user_logged_in() ) {
			$account_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : admin_url();
			ob_start();
			?>
			<div class="um-login">
				<div class="um-login__card">
					<h2 class="um-login__title"><?php esc_html_e( 'Ya has iniciado sesión', 'united-motor' ); ?></h2>
					<p class="um-login__text"><?php esc_html_e( 'Accede a tu cuenta para continuar.', 'united-motor' ); ?></p>
					<a class="um-login__button" href="<?php echo esc_url( $account_url ); ?>">
						<?php esc_html_e( 'Ir a mi cuenta', 'united-motor' ); ?>
					</a>
				</div>
			</div>
			<?php
			return ob_get_clean();
		}

		$defaults = array(
			'redirect' => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' ),
		);
		$atts = shortcode_atts( $defaults, $atts, 'united_motor_login' );

		$redirect = esc_url( $atts['redirect'] );

		ob_start();
		include plugin_dir_path( __FILE__ ) . 'partials/united-motor-login.php';
		return ob_get_clean();
	}

	/**
	 * Render del shortcode de servicios.
	 *
	 * @since    1.0.0
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function render_services_shortcode( $atts = array() ) {
		$defaults = array(
			'login_url'     => '',
			'empty_message' => __( 'No tienes servicios activos por el momento.', 'united-motor' ),
		);
		$atts = shortcode_atts( $defaults, $atts, 'united_motor_services' );

		if ( ! is_user_logged_in() ) {
			$login_url = $atts['login_url'];
			if ( empty( $login_url ) ) {
				$login_url = home_url( '/login' );
			}
			wp_safe_redirect( $login_url );
			exit;
		}

		$user_id = get_current_user_id();
		$services = $this->get_active_services( $user_id );
		$empty_message = $atts['empty_message'];

		ob_start();
		include plugin_dir_path( __FILE__ ) . 'partials/united-motor-services.php';
		return ob_get_clean();
	}

	/**
	 * Obtener servicios activos del usuario.
	 *
	 * @since    1.0.0
	 * @param int $user_id User ID.
	 * @return array
	 */
	private function get_active_services( $user_id ) {
		$services = array();
		$user     = wp_get_current_user();
		$is_driver = in_array( 'driver', (array) $user->roles, true );

		$args = array(
			'post_type'      => 'solicitud_transporte',
			'posts_per_page' => -1,
		);

		if ( $is_driver ) {
			$args['meta_key']   = '_assigned_driver_id';
			$args['meta_value'] = $user_id;
		} else {
			$args['author'] = $user_id;
		}

		$query = new WP_Query( $args );
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post_id = get_the_ID();
				$status  = get_post_status_object( get_post_status( $post_id ) );
				$vin     = get_post_meta( $post_id, 'vin', true );
				$foto_url = get_post_meta( $post_id, 'foto_vehiculo_url', true );
				if ( empty( $foto_url ) ) {
					$thumb_id = get_post_thumbnail_id( $post_id );
					$foto_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'thumbnail' ) : '';
				}

				$services[] = array(
					'title'  => get_the_title(),
					'status' => $status ? $status->label : get_post_status( $post_id ),
					'meta'   => $vin ? sprintf( __( 'VIN: %s', 'united-motor' ), $vin ) : '',
					'date'   => get_the_date( get_option( 'date_format' ), $post_id ),
					'image'  => $foto_url,
				);
			}
			wp_reset_postdata();
		}

		return $services;
	}

}
