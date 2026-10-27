<?php
/**
 * Template parcial para servicios activos.
 *
 * @link       https://honduespacios.es
 * @since      1.0.0
 *
 * @package    United_Motor
 * @subpackage United_Motor/public/partials
 */
?>

<div class="um-services">
	<div class="um-services__card">
		<div class="um-services__header">
			<h2 class="um-services__title"><?php esc_html_e( 'Solicitudes', 'united-motor' ); ?></h2>
			<p class="um-services__text"><?php esc_html_e( 'Listado informativo de tus solicitudes.', 'united-motor' ); ?></p>
		</div>

		<?php if ( empty( $services ) ) : ?>
			<div class="um-services__empty">
				<?php echo esc_html( $empty_message ); ?>
			</div>
		<?php else : ?>
			<ul class="um-services__list">
				<?php foreach ( $services as $service ) : ?>
					<li class="um-services__item">
						<?php if ( ! empty( $service['image'] ) ) : ?>
							<div class="um-services__thumb">
								<img src="<?php echo esc_url( $service['image'] ); ?>" alt="<?php echo esc_attr( $service['title'] ); ?>" loading="lazy" />
							</div>
						<?php endif; ?>
						<div class="um-services__info">
							<h3 class="um-services__name"><?php echo esc_html( $service['title'] ); ?></h3>
							<?php if ( ! empty( $service['meta'] ) ) : ?>
								<span class="um-services__meta"><?php echo esc_html( $service['meta'] ); ?></span>
							<?php endif; ?>
						</div>
						<div class="um-services__status">
							<span class="um-services__badge"><?php echo esc_html( $service['status'] ); ?></span>
							<?php if ( ! empty( $service['date'] ) ) : ?>
								<span class="um-services__date"><?php echo esc_html( $service['date'] ); ?></span>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</div>
