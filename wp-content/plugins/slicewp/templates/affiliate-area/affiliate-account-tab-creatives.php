<?php
/**
 * Affiliate account tab: Creatives
 *
 * This template can be overridden by copying it to yourtheme/slicewp/affiliate-area/affiliate-account-tab-creatives.php.
 *
 * HOWEVER, on occasion SliceWP will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility.
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Get the creatives page number.
$page_creatives = ( ! empty( $_GET['page_creatives'] ) ? absint( $_GET['page_creatives'] ) : 1 );

// The number of creatives per page.
$creatives_per_page = apply_filters( 'slicewp_items_per_page_creatives', 12 );

?>

<?php

    $creative_args = array(
        'number'	=> $creatives_per_page,
        'offset'	=> ( $page_creatives - 1 ) * $creatives_per_page,
        'status'	=> 'active'
    );

    $creatives_count = slicewp_get_creatives( $creative_args, true );
    $creatives 		 = slicewp_get_creatives( $creative_args );

?>

<?php if ( empty( $creatives ) ): ?>

    <p><?php echo __( "There aren't any creatives available." , 'slicewp' ); ?></p>

<?php else: ?>

    <div class="slicewp-grid slicewp-grid-cols-4">

        <?php foreach ( $creatives as $creative ): ?>

            <div class="slicewp-card slicewp-card-creative slicewp-creative-<?php echo absint( $creative->get('id') ); ?> slicewp-creative-type-<?php echo esc_attr( str_replace( '_', '-', $creative->get('type') ) ); ?>">

                <div class="slicewp-card-inner">

                    <?php if ( $creative->get('type') == 'image' ): ?>

                        <img src="<?php echo esc_url( $creative->get('image_url') ); ?>" alt="<?php echo esc_attr( $creative->get('alt_text') ); ?>" />

                    <?php elseif ( $creative->get('type') == 'text' ):?>

                        <span><?php echo( $creative->get('text') ); ?></span>

                    <?php elseif ( $creative->get('type') == 'long_text' ):?>

                        <div>
                            <?php echo wpautop( wp_trim_words( $creative->get('text'), 35 ) ); ?>
                        </div>

                    <?php endif; ?>

                </div>

                <div class="slicewp-card-footer slicewp-card-creative-actions">
                    <div>
                        <a href="#" class="slicewp-show-creative"><?php echo slicewp_get_svg( 'outline-eye' ) . '<span>' . __( 'View', 'slicewp' ) . '</span>'; ?></a>
                        <a href="#" class="slicewp-copy-creative"><?php echo slicewp_get_svg( 'outline-duplicate' ); ?><span class="slicewp-input-copy-label"><?php echo __( 'Copy', 'slicewp' ); ?></span><span class="slicewp-input-copy-label-copied"><?php echo __( 'Copied!', 'slicewp' ); ?></span></a>
                    </div>
                </div>

                <div class="slicewp-global-overlay">

                    <div class="slicewp-global-overlay-inner">

                        <span class="slicewp-global-overlay-close"><?php echo slicewp_get_svg( 'outline-x' ); ?></span>

                        <div class="slicewp-creative-wrapper slicewp-creative-wrapper-<?php echo absint( $creative->get('id') ); ?> slicewp-creative-wrapper-type-<?php echo esc_attr( str_replace( '_', '-', $creative->get( 'type' ) ) ); ?> slicewp-creative-affiliate-wrapper">

                            <?php if ( ! empty( $creative->get('description') ) ): ?>
                                <div class="slicewp-creative-description">
                                    <?php echo wpautop( $creative->get('description') ); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( $creative->get( 'type' ) == 'image' ): ?>

                                <h4><?php echo __( 'Preview', 'slicewp' ); ?></h4>

                                <div class="slicewp-creative-preview">
                                    <img src="<?php echo esc_url( $creative->get( 'image_url' ) ); ?>" alt="<?php echo esc_attr( $creative->get( 'alt_text' ) ); ?>" data-file-name="<?php echo esc_attr( wp_basename( $creative->get( 'image_url' ) ) ); ?>" />
                                </div>

                                <div class="slicewp-creative-image-details">

                                    <button class="slicewp-button-primary slicewp-download-creative-image">
                                        <?php echo slicewp_get_svg( 'outline-download' ); ?>
                                        <?php echo __( 'Download image', 'slicewp' ); ?>
                                    </button>

                                    <?php
                                        $attachment_id = attachment_url_to_postid( $creative->get( 'image_url' ) );
                                        $image_meta    = wp_get_attachment_metadata( $attachment_id );
                                    ?>

                                    <?php if ( is_array( $image_meta ) ): ?>

                                        <?php $mime_type_data = wp_check_filetype( $creative->get( 'image_url' ) ); ?>

                                        <div class="slicewp-creative-image-metadata">
                                            <strong><?php echo sprintf( __( '%s File', 'slicewp' ), strtoupper( $mime_type_data['ext'] ) ); ?></strong>
                                            <span><?php echo $image_meta['width'] . slicewp_get_svg( 'outline-x' ) . $image_meta['height'] . ( ! empty( $image_meta['filesize'] ) ? '<i>&#183;</i>' . size_format( $image_meta['filesize'], 1 ) : '' ); ?></span>
                                        </div>

                                    <?php endif; ?>

                                </div>

                                <h4><?php echo __( 'HTML Embed Code', 'slicewp' ); ?></h4>

                                <textarea class="slicewp-creative-affiliate-textarea" readonly><a href="<?php echo esc_url( slicewp_get_affiliate_url( $args['affiliate_id'], $creative->get('landing_url') ) ); ?>"><img src="<?php echo esc_url( $creative->get('image_url') ); ?>" alt="<?php echo esc_attr( $creative->get('alt_text') ); ?>" /></a></textarea>

                            <?php elseif ( $creative->get( 'type' ) == 'text' ):?>

                                <h4><?php echo __( 'Preview', 'slicewp' ); ?></h4>

                                <div class="slicewp-creative-preview">
                                    <a href="#"><?php echo( $creative->get('text') ); ?></a>
                                </div>

                                <h4><?php echo __( 'HTML Embed Code', 'slicewp' ); ?></h4>

                                <textarea class="slicewp-creative-affiliate-textarea" readonly><a href="<?php echo esc_url( slicewp_get_affiliate_url( $args['affiliate_id'], $creative->get('landing_url') ) ); ?>"><?php echo esc_textarea( $creative->get('text') ); ?></a></textarea>

                            <?php elseif ( $creative->get( 'type' ) == 'long_text' ):?>

                                <textarea class="slicewp-creative-affiliate-textarea" readonly><?php echo esc_textarea( $creative->get('text') ); ?></textarea>

                            <?php endif; ?>

                            <button class="slicewp-button-primary slicewp-input-copy">
                                <?php echo slicewp_get_svg( 'outline-duplicate' ); ?>
                                <span class="slicewp-input-copy-label"><?php echo __( 'Copy to clipboard', 'slicewp' ); ?></span>
                                <span class="slicewp-input-copy-label-copied"><?php echo __( 'Copied!', 'slicewp' ); ?></span>
                            </button>

                        </div>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>

<?php if ( $creatives_count > $creatives_per_page ): ?>

    <div class="slicewp-page-numbers-wrapper">

        <?php

            // Prepare the pagination of the table.
            $creatives_paginate_args = array(
                'base'		=> '?affiliate-account-tab=creatives%_%',
                'format'	=> '&page_creatives=%#%',
                'total'		=> ceil( $creatives_count / $creatives_per_page ),
                'current'	=> $page_creatives,
                'prev_next'	=> false
            );

            echo paginate_links( $creatives_paginate_args );

        ?>

    </div>

<?php endif; ?>