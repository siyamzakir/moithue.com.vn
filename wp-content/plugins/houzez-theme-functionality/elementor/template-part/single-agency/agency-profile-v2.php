<?php
global $settings;
$agency_position = get_post_meta( get_the_ID(), 'fave_agency_position', true );

$agency_number = get_post_meta( get_the_ID(), 'fave_agency_phone', true );
$agency_number_call = str_replace(array('(',')',' ','-'),'', $agency_number);
?>
<div class="agent-detail-page-v2">
    <div class="agent-profile-wrap">
        <div class="container">
            <div class="agent-profile-top-wrap">
                <div class="agent-image">
                    <?php get_template_part('template-parts/realtors/agency/image'); ?>
                </div><!-- agent-image -->
                <div class="agent-profile-header">
                    <h1><?php the_title(); ?> </h1>
                    <?php 
                    if( houzez_option( 'agency_review', 0 ) != 0 ) {
                        get_template_part('template-parts/realtors/rating'); 
                    }?> 

                    <?php if( $settings['show_address'] ) {?>
                    <div class="agent-profile-address">
                        <?php get_template_part('template-parts/realtors/agency/address'); ?> 
                    </div><!-- agent-profile-address -->
                    <?php } ?>

                    <div class="agent-profile-cta">
                        <ul class="list-inline">
                            <?php if( houzez_option('agency_form_agency_page', 1) ) { ?>
                            <li class="list-inline-item"><a href="#" data-toggle="modal" data-target="#realtor-form"><i class="houzez-icon icon-messages-bubble mr-1"></i> <?php echo houzez_option('agency_lb_ask_question', esc_html__('Ask a question', 'houzez')); ?></a></li>
                            <?php } ?>
                            <?php if(!empty($agency_number)) { ?>
                            <li class="list-inline-item">
                                <a href="tel:<?php echo esc_attr($agency_number_call); ?>"><i class="houzez-icon icon-phone mr-1"></i> <?php echo esc_attr($agency_number); ?></a>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div><!-- agent-profile-header -->
            </div><!-- agent-profile-top-wrap -->
        </div><!-- container -->
    </div><!-- agent-profile-wrap -->
</div>