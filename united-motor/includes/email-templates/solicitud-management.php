<?php
/**
 * Plantilla HTML: nueva solicitud para gerencia.
 *
 * Variables: ver United_Motor_Mail::send_new_solicitud_to_management().
 *
 * @package United_Motor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$label = function ( $text ) {
	return esc_html( $text );
};

$val = function ( $text ) {
	return $text !== '' && $text !== null ? esc_html( $text ) : '—';
};

$link = function ( $url, $text ) {
	if ( empty( $url ) ) {
		return '—';
	}
	return '<a href="' . esc_url( $url ) . '" style="color:#1a5fb4;text-decoration:none;">' . esc_html( $text ) . '</a>';
};
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $label( __( 'Nueva solicitud de transporte', 'united-motor' ) ); ?></title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f9;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.5;color:#333333;">
	<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f4f6f9;padding:24px 12px;">
		<tr>
			<td align="center">
				<table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
					<tr>
						<td style="background-color:#1a365d;padding:28px 32px;text-align:center;">
							<p style="margin:0;font-size:22px;font-weight:bold;color:#ffffff;letter-spacing:0.5px;">
								<?php echo $label( 'United Motor Transportation' ); ?>
							</p>
							<p style="margin:8px 0 0;font-size:14px;color:#a8c4e8;">
								<?php echo $label( __( 'Nueva solicitud de transporte', 'united-motor' ) ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<td style="padding:32px;">
							<p style="margin:0 0 20px;font-size:16px;">
								<?php echo $label( __( 'Se ha registrado una nueva solicitud en el sistema. A continuación los detalles para su revisión:', 'united-motor' ) ); ?>
							</p>

							<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:24px;border:1px solid #e2e8f0;border-radius:6px;">
								<tr>
									<td colspan="2" style="background-color:#edf2f7;padding:12px 16px;font-weight:bold;font-size:14px;color:#1a365d;">
										<?php echo $label( __( 'Información de la solicitud', 'united-motor' ) ); ?>
									</td>
								</tr>
								<tr>
									<td width="40%" style="padding:10px 16px;border-top:1px solid #e2e8f0;color:#64748b;font-size:13px;"><?php echo $label( __( 'ID solicitud', 'united-motor' ) ); ?></td>
									<td style="padding:10px 16px;border-top:1px solid #e2e8f0;font-weight:bold;">#<?php echo (int) $post_id; ?></td>
								</tr>
								<tr>
									<td style="padding:10px 16px;border-top:1px solid #e2e8f0;color:#64748b;font-size:13px;"><?php echo $label( __( 'Fecha', 'united-motor' ) ); ?></td>
									<td style="padding:10px 16px;border-top:1px solid #e2e8f0;"><?php echo $val( $date ); ?></td>
								</tr>
								<tr>
									<td style="padding:10px 16px;border-top:1px solid #e2e8f0;color:#64748b;font-size:13px;"><?php echo $label( __( 'Placa / VIN', 'united-motor' ) ); ?></td>
									<td style="padding:10px 16px;border-top:1px solid #e2e8f0;font-weight:bold;"><?php echo $val( $vin ); ?></td>
								</tr>
								<tr>
									<td style="padding:10px 16px;border-top:1px solid #e2e8f0;color:#64748b;font-size:13px;"><?php echo $label( __( 'Comprador', 'united-motor' ) ); ?></td>
									<td style="padding:10px 16px;border-top:1px solid #e2e8f0;"><?php echo $val( $comprador ); ?></td>
								</tr>
								<tr>
									<td style="padding:10px 16px;border-top:1px solid #e2e8f0;color:#64748b;font-size:13px;"><?php echo $label( __( 'País / destino', 'united-motor' ) ); ?></td>
									<td style="padding:10px 16px;border-top:1px solid #e2e8f0;"><?php echo $val( $pais ); ?></td>
								</tr>
								<tr>
									<td style="padding:10px 16px;border-top:1px solid #e2e8f0;color:#64748b;font-size:13px;"><?php echo $label( __( 'Subasta', 'united-motor' ) ); ?></td>
									<td style="padding:10px 16px;border-top:1px solid #e2e8f0;"><?php echo $val( $subasta ); ?></td>
								</tr>
								<tr>
									<td style="padding:10px 16px;border-top:1px solid #e2e8f0;color:#64748b;font-size:13px;"><?php echo $label( __( 'Estado subasta', 'united-motor' ) ); ?></td>
									<td style="padding:10px 16px;border-top:1px solid #e2e8f0;"><?php echo $val( $estado_subasta ); ?></td>
								</tr>
							</table>

							<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:24px;border:1px solid #e2e8f0;border-radius:6px;">
								<tr>
									<td colspan="2" style="background-color:#edf2f7;padding:12px 16px;font-weight:bold;font-size:14px;color:#1a365d;">
										<?php echo $label( __( 'Cliente registrado', 'united-motor' ) ); ?>
									</td>
								</tr>
								<tr>
									<td width="40%" style="padding:10px 16px;border-top:1px solid #e2e8f0;color:#64748b;font-size:13px;"><?php echo $label( __( 'Nombre', 'united-motor' ) ); ?></td>
									<td style="padding:10px 16px;border-top:1px solid #e2e8f0;"><?php echo $val( $author_name ); ?></td>
								</tr>
								<tr>
									<td style="padding:10px 16px;border-top:1px solid #e2e8f0;color:#64748b;font-size:13px;"><?php echo $label( __( 'Correo', 'united-motor' ) ); ?></td>
									<td style="padding:10px 16px;border-top:1px solid #e2e8f0;">
										<?php
										if ( ! empty( $author_email ) ) {
											echo '<a href="mailto:' . esc_attr( $author_email ) . '" style="color:#1a5fb4;">' . esc_html( $author_email ) . '</a>';
										} else {
											echo '—';
										}
										?>
									</td>
								</tr>
							</table>

							<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:28px;border:1px solid #e2e8f0;border-radius:6px;">
								<tr>
									<td colspan="2" style="background-color:#edf2f7;padding:12px 16px;font-weight:bold;font-size:14px;color:#1a365d;">
										<?php echo $label( __( 'Documentos adjuntos', 'united-motor' ) ); ?>
									</td>
								</tr>
								<tr>
									<td width="40%" style="padding:10px 16px;border-top:1px solid #e2e8f0;color:#64748b;font-size:13px;"><?php echo $label( __( 'Factura', 'united-motor' ) ); ?></td>
									<td style="padding:10px 16px;border-top:1px solid #e2e8f0;"><?php echo $link( $factura_url, __( 'Ver factura', 'united-motor' ) ); ?></td>
								</tr>
								<tr>
									<td style="padding:10px 16px;border-top:1px solid #e2e8f0;color:#64748b;font-size:13px;"><?php echo $label( __( 'Foto del vehículo', 'united-motor' ) ); ?></td>
									<td style="padding:10px 16px;border-top:1px solid #e2e8f0;"><?php echo $link( $foto_url, __( 'Ver foto', 'united-motor' ) ); ?></td>
								</tr>
							</table>

							<table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
								<tr>
									<td style="border-radius:6px;background-color:#1a5fb4;">
										<a href="<?php echo esc_url( $admin_url ); ?>" style="display:inline-block;padding:14px 28px;font-size:15px;font-weight:bold;color:#ffffff;text-decoration:none;">
											<?php echo $label( __( 'Ver solicitud en el panel', 'united-motor' ) ); ?>
										</a>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td style="background-color:#f8fafc;padding:20px 32px;text-align:center;border-top:1px solid #e2e8f0;">
							<p style="margin:0;font-size:12px;color:#94a3b8;">
								<?php
								echo esc_html(
									sprintf(
										/* translators: %s: site name */
										__( 'Correo automático de %s. No responda directamente a este mensaje si no es necesario.', 'united-motor' ),
										$site_name
									)
								);
								?>
							</p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
