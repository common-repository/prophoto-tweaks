<div class="wrap pp-customize-page-area area-<?php echo $selectedArea; ?>">
	
	<div id="area-title-wrap" class="sc">
		
		<div class="icon32" id="icon-themes">
			<br/>
		</div>

		<h1 id="prophoto-page-title">
			ProPhoto <b>Tweaks</b>
		</h1>
		
		<?php if ( isset( $updatedMsg ) ) echo $updatedMsg; ?>
		
		<form action="" method="post" id="pp-tweaks">
			
			<?php 

			echo NrHtml::labledCheckbox( 
				'Remove all Facebook open graph (og:) meta tags',
				'remove_facebook_og_meta',
				( self::$opts['remove_facebook_og_meta'] == 'checked' ),
				'checked'
			); 
			
			echo '<br /><br />';
			
			echo NrHtml::labledCheckbox( 
				'Show filenames in Lightbox galleries',
				'show_filenames_in_lightbox_galleries',
				( self::$opts['show_filenames_in_lightbox_galleries'] == 'checked' ),
				'checked'
			);
			
			echo '<br /><br />';
			
			echo NrHtml::labledCheckbox( 
				'Show "From URL" upload tab when uploading post/page images',
				'show_from_url_upload_tab',
				( self::$opts['show_from_url_upload_tab'] == 'checked' ),
				'checked'
			);

			?>
			
			<div class="text-input numeric">
				<?php

				echo NrHtml::labledTextInput( 
					'Override 60 image limit for Masthead Images:', 
					'max_masthead_imgs',
					self::$opts['max_masthead_imgs']
				);

				?>
			</div>
			
			<div class="text-input numeric">
				<?php

				echo NrHtml::labledTextInput( 
					'Override 25 image limit for Widget Images:', 
					'max_widget_imgs',
					self::$opts['max_widget_imgs']
				);

				?>
			</div>
			
			<div class="text-input numeric">
				<?php

				echo NrHtml::labledTextInput( 
					'Override 900px image size limit for Lightbox gallery overlay images:', 
					'max_lightbox_img_size',
					self::$opts['max_lightbox_img_size']
				);

				?>
			</div>
			
			
			<?php echo ppUtil::idAndNonce( 'pp_tweaks' ); ?>

			<p class="submit sc">
				<input id="pp-tweaks-save-changes" type="submit" value="Save Changes" name="Submit" class="button-primary"/>
			</p>
	
		</form>

</div>