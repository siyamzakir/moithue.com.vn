<?php 
global $post, $map_street_view, $media_tabs; 
$gallery_active = $map_active = $street_active = $virtual_active = $video_active = "";
$active_tab = houzez_option('prop_default_active_tab', 'image_gallery');
$media_tabs = houzez_get_media_tabs();

// Get available tabs
$available_tabs = array();
foreach ($media_tabs as $key => $value) {
    if ($key == 'video') {
        $prop_video_url = houzez_get_listing_data('video_url');
        if (!empty($prop_video_url)) {
            $available_tabs[] = 'video';
        }
    } elseif ($key == '360_virtual_tour') {
        $virtual_tour = houzez_get_listing_data('virtual_tour');
        if (!empty($virtual_tour)) {
            $available_tabs[] = '360_virtual_tour';
        }
    } elseif ($key == 'gallery') {
        if (has_post_thumbnail($post->ID) || houzez_get_listing_data('gallery')) {
            $available_tabs[] = 'gallery';
        }
    } elseif ($key == 'map') {
        $available_tabs[] = 'map_view';
    } elseif ($key == 'street_view') {
        $available_tabs[] = 'street_view';
    }
}

// If current active tab is not available, select the first available tab
if (!in_array($active_tab, $available_tabs) && !empty($available_tabs)) {
    $active_tab = $available_tabs[0];
}

if ($active_tab == 'map_view') {
    $map_active = 'active';
} elseif ($active_tab == 'street_view') {
    $street_active = 'active';
} elseif ($active_tab == '360_virtual_tour') {
    $virtual_active = 'active';
} elseif ($active_tab == 'video') {
    $video_active = 'active';
} else {
    $gallery_active = 'active';
}

?>
<ul class="nav nav-pills" id="pills-tab" role="tablist">
	
	<?php 
	if ($media_tabs): foreach ($media_tabs as $key=>$value) {
		switch($key) {

        case 'gallery': 
            if(in_array('gallery', $available_tabs)) { ?>
            <li class="nav-item">
				<a class="nav-link <?php echo esc_attr($gallery_active); ?>" id="pills-gallery-tab" data-toggle="pill" href="#pills-gallery" role="tab" aria-controls="pills-gallery" aria-selected="true">
					<i class="houzez-icon icon-picture-sun"></i>
				</a>
			</li>
			<?php }
           	break;

        case 'map':
            if(in_array('map_view', $available_tabs)) { ?>
        	<li class="nav-item">
				<a class="nav-link <?php echo esc_attr($map_active); ?>" id="pills-map-tab" data-toggle="pill" href="#pills-map" role="tab" aria-controls="pills-map" aria-selected="true">
					<i class="houzez-icon icon-maps"></i>
				</a>
			</li>
        	<?php }
        	break;

        case 'street_view':
            if(in_array('street_view', $available_tabs)) { ?>
        	<li class="nav-item">
				<a class="nav-link <?php echo esc_attr($street_active); ?>" id="pills-street-view-tab" data-toggle="pill" href="#pills-street-view" role="tab" aria-controls="pills-street-view" aria-selected="false">
					<i class="houzez-icon icon-location-user"></i>
				</a>
			</li>
        	<?php }
        	break;

        case '360_virtual_tour': 
            if(in_array('360_virtual_tour', $available_tabs)) { ?>
			<li class="nav-item">
				<a class="nav-link <?php echo esc_attr($virtual_active); ?>" id="pills-360tour-tab" data-toggle="pill" href="#pills-360tour" role="tab" aria-controls="pills-360tour" aria-selected="true">
					<i class="houzez-icon icon-surveillance-360-camera"></i>
				</a>
			</li>
			<?php }
        	break;

        case 'video': 
            if(in_array('video', $available_tabs)) { ?>
			<li class="nav-item">
				<a class="nav-link <?php echo esc_attr($video_active); ?>" id="pills-video-tab" data-toggle="pill" href="#pills-video" role="tab" aria-controls="pills-video" aria-selected="true">
					<i class="houzez-icon icon-video-player-movie-1"></i>
				</a>
			</li>
			<?php }
        	break;
    }
}
endif;
?>
</ul><!-- nav -->	