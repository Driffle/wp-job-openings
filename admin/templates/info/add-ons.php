<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( get_transient( '_awsm_add_ons_data' ) === false ) {
	$response = wp_remote_get( esc_url( 'http://dev.awsm.in/innovations/wp-json/awsm-plugins/v1/job-add-ons' ) );
	if ( is_wp_error( $response ) ) {
		return;
	}
	$response_body = wp_remote_retrieve_body( $response );
	if ( is_wp_error( $response_body ) ) {
		return;
	}
	if ( wp_remote_retrieve_response_code( $response ) === 200 ) {
		set_transient( '_awsm_add_ons_data', $response_body, DAY_IN_SECONDS );
	}
}
?>

<div id="awsm-add-on" class="awsm-job-addon awsm-tab-item">
	<?php
		$allowed_html = array(
			'a'      => array(
				'href'  => array(),
				'title' => array(),
			),
			'p'      => array(),
			'br'     => array(),
			'em'     => array(),
			'span'   => array(),
			'strong' => array(),
			'small'  => array(),
		);
		$json         = get_transient( '_awsm_add_ons_data' );
		$add_ons_data = json_decode( $json, true );
		if ( ! empty( $add_ons_data ) && is_array( $add_ons_data ) ) :
			foreach ( $add_ons_data as $add_on ) :
				?>
				<div class="awsm-job-addon-item">
					<img src="<?php echo ( ! empty( $add_on['image_src'] ) ) ? esc_url( $add_on['image_src'] ) : esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/placeholder.gif' ); ?>" alt="<?php esc_attr( $add_on['name'] ); ?>" />
					<div class="awsm-job-addon-item-inner">
						<h2 class="awsm-add-ons-name"><?php echo esc_html( $add_on['name'] ); ?></h2>
						<div class="awsm-job-addon-item-content">		
							<?php echo wp_kses( $add_on['content'], $allowed_html ); ?>
						</div><!-- .awsm-job-addon-item-content -->
						<div class="awsm-job-addon-item-features">
							<ul>
								<?php
								if ( ! empty( $add_on['features'] ) ) :
									foreach ( $add_on['features'] as $feature ) :
										?>
											<li><i class="awsm-job-icon-check"></i> <?php echo esc_html( $feature ); ?></li>
										<?php
										endforeach;
									endif;
								?>
							</ul>
						</div><!-- awsm-job-addon-item-features -->
						<div class="awsm-job-addon-item-info">
							<ul>
								<li>
									<p class="awsm-job-addon-price"><?php echo ( $add_on['pricing']['type'] == 'free' || empty( $add_on['pricing']['price'] ) ) ? esc_html__( 'Free', 'wp-job-openings' ) : sprintf( esc_html__( 'Price: %s', 'wp-job-openings' ), $add_on['pricing']['price'] ); ?></p>
								</li>
								<li>
									<?php
									if ( current_user_can( 'install_plugins' ) ) {
										if ( ! empty( $add_on['wp_plugin'] ) ) {
											echo $this->get_add_on_btn_content( $add_on['wp_plugin'] );
										} else {
											printf( '<p>%s</p>', esc_html__( 'Coming soon!', 'wp-job-openings' ) );
										}
									}
									?>
								</li>
								<?php
								if ( ! empty( $add_on['url'] ) ) {
									printf( '<a href="%2$s" target="_blank">%1$s</a>', esc_html__( 'More Details', 'wp-job-openings' ), esc_url( $add_on['url'] ) );
								}
								?>
							</ul>		
						</div><!-- .awsm-job-addon-item-info -->
					</div><!-- .awsm-job-addon-item-inner -->
				</div><!-- .awsm-job-addon-item -->
				<?php
			endforeach;
		else :
			?>
			<div class="awsm-col">
				<div class="awsm-welcome-point-content">
					<p><?php esc_html_e( 'Sorry! Error fetching add-ons data. Please come back later.', 'wp-job-openings' ); ?></p>
				</div><!-- .awsm-welcome-point-image -->
			</div><!-- .col-->
	<?php endif; ?>
	<p><?php printf( esc_html__( 'More add-ons are being developed by our team. If you have any suggestions or feature request, please feel free to reach us through %1$s our website %2$s. ' ), '<a href="https://awsm.in/support/" target="_blank">', '</a>' ); ?></p>
</div><!-- .awsm-job-addon -->
