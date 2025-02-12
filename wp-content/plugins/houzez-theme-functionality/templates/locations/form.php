<?php
$settings = array(); 
?>
<div class="fwl-wrapper">
				
	<div class="fwl-content">
		<h2></h2>
		<div class="fwl-row">
			
			<div class="fwl-box-wrap import-locations-wrap">
				
				<div class="fwl-box">
					<div class="fwl-box-header">
						<h1><?php esc_html_e('Import Locations', 'houzez'); ?></h1>
					</div><!-- fwl-box-header -->
					<div class="fwl-box-content">
						
						<p></p>

						<form class="white-label-wrap fwl-form" method="post" action="">
							<?php //settings_fields( 'favethemes_branding' ); ?>
							<?php wp_nonce_field( 'favethemes-import-locations', 'favethemes-import-locations-nonce' ); ?>

							

							<div class="field-wrap full-width">
								<label for="upload-csv-file"><?php esc_html_e( 'Choose CSV File', 'houzez-theme-functionality' ); ?></label>
								<div class="field-group" style="display: flex; align-items: center;">
								    <input id="locations-csv-file" class="form-field" type="text" name="locations-csv-file" style="flex-grow: 1; min-height: 30px; margin-right:10px;">
								    <input id="upload-locations-csv" class="button-secondary" type="button" value="Choose">
								</div>

							</div>

							<div class="field-wrap">
								<button id="fetch-locations-csv" class="button button-primary"><?php esc_attr_e( 'Fetch CSV', 'houzez-theme-functionality' ); ?></button>
							</div>

							<div class="field-wrap" id="form-messages">
								<?php
								// Updated notice
								if ( isset( $_GET['settings-updated'] ) ) {
								    echo '<div class="settings-updated"><p>Settings updated successfully.</p></div>';
								} ?>
							</div>
							<div style="clear:both;"></div>
						</form>

						<div id="locations-mapping-container"></div>
						<div id="locations-locations-success" style="color:green; margin-bottom: 20px;"></div>
						<div id="locations-locations-error" style="color:red; margin-bottom: 20px;"></div>

					</div><!-- fwl-box-content -->
					
				</div><!-- fwl-box -->

			</div><!-- fwl-box-wrap -->

		</div><!-- fwl-row -->
	</div>
</div>