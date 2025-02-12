<?php 
global $settings, $map_street_view; 
$prop_video_url = houzez_get_listing_data('video_url');
$virtual_tour = houzez_get_listing_data('virtual_tour');
?>
<ul class="nav nav-pills" id="pills-tab" role="tablist">
                        
    <?php if( $settings['btn_gallery'] ) { ?>
    <li class="nav-item">
        <a class="nav-link active" id="pills-gallery-tab" data-toggle="pill" href="#pills-gallery" role="tab" aria-controls="pills-gallery" aria-selected="true">
            <i class="houzez-icon icon-picture-sun"></i>
        </a>
    </li>
    <?php } ?>

    <?php if( houzez_get_listing_data('property_map') ) { ?>
        
        <?php if( $settings['btn_map'] ) { ?>
        <li class="nav-item">
            <a class="nav-link" id="pills-map-tab" data-toggle="pill" href="#pills-map" role="tab" aria-controls="pills-map" aria-selected="true">
                <i class="houzez-icon icon-maps"></i>
            </a>
        </li>
        <?php } ?>

        <?php if( houzez_get_map_system() == 'google' && $map_street_view != 'hide' && $settings['btn_street'] ) { ?>
        <li class="nav-item">
            <a class="nav-link" id="pills-street-view-tab" data-toggle="pill" href="#pills-street-view" role="tab" aria-controls="pills-street-view" aria-selected="false">
                <i class="houzez-icon icon-location-user"></i>
            </a>
        </li>
        <?php } ?>
    <?php } ?>

    <?php if ( $settings['btn_video'] == 'true' && ! empty( $prop_video_url ) ) { ?>
    <li class="nav-item">
        <a class="nav-link" id="pills-video-tab" data-toggle="pill" href="#pills-video" role="tab" aria-controls="pills-video" aria-selected="true">
            <i class="houzez-icon icon-video-player-movie-1"></i>
        </a>
    </li>
    <?php } ?>

    <?php if( $settings['btn_360_tour'] == 'true' && ! empty( $virtual_tour ) ) { ?>
    <li class="nav-item">
        <a class="nav-link" id="pills-360tour-tab" data-toggle="pill" href="#pills-360tour" role="tab" aria-controls="pills-360tour" aria-selected="true">
            <i class="houzez-icon icon-surveillance-360-camera"></i>
        </a>
    </li>
    <?php } ?>

</ul>