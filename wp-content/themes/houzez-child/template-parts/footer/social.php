<?php if( houzez_option('social-footer') != '0' ) { ?>
<div class="footer-social">

	<?php 
	$text_facebook = $text_twitter = $text_instagram = $text_linkedin = $text_googleplus = $text_youtube = $text_pinterest = $text_yelp = $text_behance = $text_tiktok = $text_whatsapp = $text_telegram = $text_skype = '';

	$agent_whatsapp = houzez_option('fs-whatsapp');
	$agent_whatsapp_call = str_replace(array('(',')',' ','-'),'', $agent_whatsapp);

	$icons_class = "mr-2";
	if(houzez_option('ft-bottom') == 'v2') {
		$text_facebook = esc_html__('Facebook', 'houzez'); 
		$text_twitter = esc_html__('Twitter', 'houzez');
		$text_instagram = esc_html__('Instagram', 'houzez'); 
		$text_linkedin = esc_html__('Linkedin', 'houzez');
		$text_googleplus = esc_html__('Google +', 'houzez');
		$text_youtube = esc_html__('Youtube', 'houzez');
		$text_pinterest = esc_html__('Pinterest', 'houzez');
		$text_yelp = esc_html__('Yelp', 'houzez');
		$text_behance = esc_html__('Behance', 'houzez');
		$text_tiktok = esc_html__('TikTok', 'houzez');
		$text_whatsapp = esc_html__('WhatsApp', 'houzez');
		$text_telegram = esc_html__('Telegram', 'houzez');
		$text_skype = esc_html__('Skype', 'houzez');
	}

	if(houzez_option('ft-bottom') == 'v3') {
		$icons_class = "";
	}
	?>

	<?php if( houzez_option('fs-facebook') != '' ){ ?>
	<span>
		<a class="btn-facebook" target="_blank" href="<?php echo esc_url(houzez_option('fs-facebook')); ?>">
			<i class="houzez-icon icon-social-media-facebook <?php echo esc_attr($icons_class); ?>"></i> <?php echo $text_facebook; ?>
		</a>
	</span>
	<?php } ?>

	<?php if( houzez_option('fs-twitter') != '' ){ ?>
	<span>
		<a class="btn-twitter" target="_blank" href="<?php echo esc_url(houzez_option('fs-twitter')); ?>">
			<i class="houzez-icon icon-x-logo-twitter-logo-2 <?php echo esc_attr($icons_class); ?>"></i> <?php echo $text_twitter; ?>
		</a>
	</span>
	<?php } ?>

	<?php if( $agent_whatsapp != '' ){ ?>
	 <span>
		<a target="_blank" class="btn-whatsapp" href="https://wa.me/<?php echo esc_attr( $agent_whatsapp_call ); ?>">
			<i class="houzez-icon icon-messaging-whatsapp <?php echo esc_attr($icons_class); ?>"></i> <?php echo $text_whatsapp; ?>
		</a>
	</span>
	<?php } ?>

	<?php if( houzez_option('fs-tiktok') != '' ){ ?>
	 <span>
		<a target="_blank" class="btn-tiktok" href="<?php echo esc_url(houzez_option('fs-tiktok')); ?>">
			<i class="houzez-icon icon-tiktok-1-logos-24 <?php echo esc_attr($icons_class); ?>"></i> <?php echo $text_tiktok; ?>
		</a>
	</span>
	<?php } ?>

	<?php if( houzez_option('fs-telegram') != '' ){ ?>
	 <span>
		<a target="_blank" class="btn-telegram" href="https://telegram.me/<?php echo esc_attr(houzez_option('fs-telegram')); ?>">
			<i class="houzez-icon icon-telegram-logos-24 <?php echo esc_attr($icons_class); ?>"></i> <?php echo $text_telegram; ?>
		</a>
	</span>
	<?php } ?>

	<!-- Replace Skype with Zalo By AppsZone -->
	<?php if(0 || !empty(houzez_option('fs-skype'))){ ?>
	 <span>
			<a target="_blank" class="btn-skype" href="skype:<?php echo esc_attr(houzez_option('fs-skype')); ?>?chat/">
				<i class="houzez-icon icon-video-meeting-skype <?php echo esc_attr($icons_class); ?>"></i> <?php echo $text_skype; ?>
			</a>
		</span>
	<?php } ?>

	<?php if(!empty(houzez_option('fs-skype'))){ ?>
	 	<span>
			<a target="_blank" class="btn-skype" href="https://zalo.me/<?php echo esc_attr(houzez_option('fs-skype')); ?>">
				<i class="houzez-icon">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="18" height="18"><path d="M352 256c0 22.2-1.2 43.6-3.3 64l-185.3 0c-2.2-20.4-3.3-41.8-3.3-64s1.2-43.6 3.3-64l185.3 0c2.2 20.4 3.3 41.8 3.3 64zm28.8-64l123.1 0c5.3 20.5 8.1 41.9 8.1 64s-2.8 43.5-8.1 64l-123.1 0c2.1-20.6 3.2-42 3.2-64s-1.1-43.4-3.2-64zm112.6-32l-116.7 0c-10-63.9-29.8-117.4-55.3-151.6c78.3 20.7 142 77.5 171.9 151.6zm-149.1 0l-176.6 0c6.1-36.4 15.5-68.6 27-94.7c10.5-23.6 22.2-40.7 33.5-51.5C239.4 3.2 248.7 0 256 0s16.6 3.2 27.8 13.8c11.3 10.8 23 27.9 33.5 51.5c11.6 26 20.9 58.2 27 94.7zm-209 0L18.6 160C48.6 85.9 112.2 29.1 190.6 8.4C165.1 42.6 145.3 96.1 135.3 160zM8.1 192l123.1 0c-2.1 20.6-3.2 42-3.2 64s1.1 43.4 3.2 64L8.1 320C2.8 299.5 0 278.1 0 256s2.8-43.5 8.1-64zM194.7 446.6c-11.6-26-20.9-58.2-27-94.6l176.6 0c-6.1 36.4-15.5 68.6-27 94.6c-10.5 23.6-22.2 40.7-33.5 51.5C272.6 508.8 263.3 512 256 512s-16.6-3.2-27.8-13.8c-11.3-10.8-23-27.9-33.5-51.5zM135.3 352c10 63.9 29.8 117.4 55.3 151.6C112.2 482.9 48.6 426.1 18.6 352l116.7 0zm358.1 0c-30 74.1-93.6 130.9-171.9 151.6c25.5-34.2 45.2-87.7 55.3-151.6l116.7 0z"/></svg>
				</i> 
				<?php echo $text_skype; ?>
			</a>
		</span>
	<?php } ?>
	<!-- End Replace Skype with Zalo By AppsZone -->

	<?php if( houzez_option('fs-googleplus') != '' ){ ?>
	<span>
		<a class="btn-googleplus" target="_blank" href="<?php echo esc_url(houzez_option('fs-googleplus')); ?>">
			<i class="houzez-icon icon-social-media-google-plus-1 <?php echo esc_attr($icons_class); ?>"></i> <?php echo $text_googleplus; ?>
		</a>
	</span>
	<?php } ?>

	<?php if( houzez_option('fs-linkedin') != '' ){ ?>
	<span>
		<a class="btn-linkedin" target="_blank" href="<?php echo esc_url(houzez_option('fs-linkedin')); ?>">
			<i class="houzez-icon icon-professional-network-linkedin <?php echo esc_attr($icons_class); ?>"></i> <?php echo $text_linkedin; ?>
		</a>
	</span>
	<?php } ?>

	<?php if( houzez_option('fs-instagram') != '' ){ ?>
	<span>
		<a class="btn-instagram" target="_blank" href="<?php echo esc_url(houzez_option('fs-instagram')); ?>">
			<i class="houzez-icon icon-social-instagram <?php echo esc_attr($icons_class); ?>"></i> <?php echo $text_instagram; ?>
		</a>
	</span>
	<?php } ?>

	<?php if( houzez_option('fs-pinterest') != '' ){ ?>
	<span>
		<a class="btn-pinterest" target="_blank" href="<?php echo esc_url(houzez_option('fs-pinterest')); ?>">
			<i class="houzez-icon icon-social-pinterest <?php echo esc_attr($icons_class); ?>"></i> <?php echo $text_pinterest; ?>
		</a>
	</span>
	<?php } ?>

	<?php if( houzez_option('fs-yelp') != '' ){ ?>
	<span>
		<a class="btn-yelp" target="_blank" href="<?php echo esc_url(houzez_option('fs-yelp')); ?>">
			<i class="houzez-icon icon-social-media-yelp <?php echo esc_attr($icons_class); ?>"></i> <?php echo $text_yelp; ?>
		</a>
	</span>
	<?php } ?>

	<?php if( houzez_option('fs-behance') != '' ){ ?>
	<span>
		<a class="btn-behance" target="_blank" href="<?php echo esc_url(houzez_option('fs-behance')); ?>">
			<i class="houzez-icon icon-designer-community-behance <?php echo esc_attr($icons_class); ?>"></i> <?php echo $text_behance; ?>
		</a>
	</span>
	<?php } ?>

	<?php if( houzez_option('fs-youtube') != '' ){ ?>
	<span>
		<a class="btn-youtube" target="_blank" href="<?php echo esc_url(houzez_option('fs-youtube')); ?>">
			<i class="houzez-icon icon-social-video-youtube-clip <?php echo esc_attr($icons_class); ?>"></i> <?php echo $text_youtube; ?>
		</a>
	</span>
	<?php } ?>


</div>
<?php
}
?>