<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$affiliate_id = slicewp_get_current_affiliate_id();

?>

<div class="slicewp-creative-wrapper slicewp-creative-wrapper-<?php echo absint( $creative->get('id') ); ?> slicewp-creative-wrapper-type-<?php echo esc_attr( str_replace( '_', '-', $creative->get('type') ) ); ?> slicewp-creative-affiliate-wrapper">

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

        <textarea class="slicewp-creative-affiliate-textarea" readonly><a href="<?php echo esc_url( slicewp_get_affiliate_url( $affiliate_id, $creative->get('landing_url') ) ); ?>"><img src="<?php echo esc_url( $creative->get('image_url') ); ?>" alt="<?php echo esc_attr( $creative->get('alt_text') ); ?>" /></a></textarea>

    <?php elseif ( $creative->get( 'type' ) == 'text' ):?>

        <h4><?php echo __( 'Preview', 'slicewp' ); ?></h4>

        <div class="slicewp-creative-preview">
            <a href="#"><?php echo( $creative->get('text') ); ?></a>
        </div>

        <h4><?php echo __( 'HTML Embed Code', 'slicewp' ); ?></h4>

        <textarea class="slicewp-creative-affiliate-textarea" readonly><a href="<?php echo esc_url( slicewp_get_affiliate_url( $affiliate_id, $creative->get('landing_url') ) ); ?>"><?php echo esc_textarea( $creative->get('text') ); ?></a></textarea>

    <?php elseif ( $creative->get( 'type' ) == 'long_text' ):?>

        <textarea class="slicewp-creative-affiliate-textarea" readonly><?php echo esc_textarea( $creative->get('text') ); ?></textarea>

    <?php endif; ?>

    <button class="slicewp-button-primary slicewp-input-copy">
        <?php echo slicewp_get_svg( 'outline-duplicate' ); ?>
        <span class="slicewp-input-copy-label"><?php echo __( 'Copy to clipboard', 'slicewp' ); ?></span>
        <span class="slicewp-input-copy-label-copied"><?php echo __( 'Copied!', 'slicewp' ); ?></span>
    </button>

</div>