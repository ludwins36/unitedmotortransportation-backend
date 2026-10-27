<?php
/**
 * Template parcial para el login público.
 *
 * @link       https://honduespacios.es
 * @since      1.0.0
 *
 * @package    United_Motor
 * @subpackage United_Motor/public/partials
 */
?>

<div class="um-login">
	<div class="um-login__card">
		<div class="um-login__brand">
			<h2 class="um-login__title"><?php esc_html_e( 'Bienvenido', 'united-motor' ); ?></h2>
			<p class="um-login__text"><?php esc_html_e( 'Inicia sesión para continuar.', 'united-motor' ); ?></p>
		</div>

		<div class="um-login__form">
			<?php
			if ( function_exists( 'woocommerce_login_form' ) ) {
				woocommerce_login_form(
					array(
						'redirect' => $redirect,
					)
				);
			} else {
				wp_login_form(
					array(
						'redirect'       => $redirect,
						'label_username' => __( 'Correo o usuario', 'united-motor' ),
						'label_password' => __( 'Contraseña', 'united-motor' ),
						'label_log_in'   => __( 'Iniciar sesión', 'united-motor' ),
						'remember'       => true,
					)
				);
			}
			?>

			<div class="um-login__links">
				<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>">
					<?php esc_html_e( '¿Olvidaste tu contraseña?', 'united-motor' ); ?>
				</a>
				<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
					<span aria-hidden="true">•</span>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
						<?php esc_html_e( 'Crear cuenta', 'united-motor' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
