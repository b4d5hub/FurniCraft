<?php
/**
 * Custom template tags for this theme.
 *
 * @package Sober
 */


if ( ! function_exists( 'sober_fonts_url' ) ) :
	/**
	 * Register fonts
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	function sober_fonts_url() {
		$fonts_url     = '';
		$font_families = array();
		$font_subsets  = array( 'latin', 'latin-ext' );

		/* Translators: If there are characters in your language that are not
		* supported by Poppins, translate this to 'off'. Do not translate
		* into your own language.
		*/
		if ( 'off' !== _x( 'on', 'Poppins font: on or off', 'sober' ) ) {
			$font_families['Poppins'] = 'Poppins:300,400,500,600,700';
		}

		// Get custom fonts from typography settings
		$settings = array(
			'typo_body',
			'typo_h1',
			'typo_h2',
			'typo_h3',
			'typo_h4',
			'typo_h5',
			'typo_h6',
			'typo_menu',
			'typo_submenu',
			'typo_toggle_menu',
			'typo_toggle_submenu',
			'typo_page_header_title',
			'typo_page_header_minimal_title',
			'typo_breadcrumb',
			'type_widget_title',
			'type_product_title',
			'type_product_excerpt',
			'typo_woocommerce_headers',
			'type_footer_info',
		);

		foreach ( $settings as $setting ) {
			$typography = sober_get_option( $setting );

			if (
				isset( $typography['font-family'] )
				&& ! empty( $typography['font-family'] )
				&& ( 'Sofia Pro' !== $typography['font-family'] )
				&& ! array_key_exists( $typography['font-family'], $font_families )
			) {
				$family = trim( $typography['font-family'] );
				$family = trim( $family, ',' );

				$font_families[ $family ] = $family;

				if ( isset( $typography['subsets'] ) ) {
					if ( is_array( $typography['subsets'] ) ) {
						$font_subsets = array_merge( $font_subsets, $typography['subsets'] );
					} else {
						$font_subsets[] = $typography['subsets'];
					}
				}
			}
		}

		if ( ! empty( $font_families ) ) {
			$font_subsets = array_unique( $font_subsets );
			$query_args   = array(
				'family'  => urlencode( implode( '|', $font_families ) ),
				'subset'  => urlencode( implode( ',', $font_subsets ) ),
				'display' => 'swap',
			);

			$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
		}

		return esc_url_raw( $fonts_url );
	}

endif;

if ( ! function_exists( 'sober_entry_meta' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time and categories.
	 */
	function sober_entry_meta() {
		$entry_meta = (array) sober_get_option( 'entry_meta' );

		if ( empty( $entry_meta ) ) {
			return;
		}

		foreach( $entry_meta as $meta ) {
			switch( $meta ) {
				case 'author' :
					if ( ! in_the_loop() ) {
						$author_id = get_queried_object()->post_author;
						$author = get_the_author_meta( 'display_name', $author_id );
						$author_url = get_author_posts_url( $author_id );
					} else {
						$author = get_the_author();
						$author_url = get_author_posts_url( get_the_author_meta( 'ID' ) );
					}

					echo '<span class="entry-meta__item post-author">' .
						'<a href="' . esc_url( $author_url ) . '">' . $author . '</a>' .
						'</span>';
					break;

				case 'date' :
					$time_string = sprintf(
						'<time class="entry-date published updated" datetime="%1$s">%2$s</time>',
						esc_attr( get_the_date( 'c' ) ),
						esc_html( get_the_date( get_option( 'date_format', 'd.m Y' ) ) )
					);

					$posted_on = is_singular() ? $time_string : '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>';

					echo '<span class="entry-meta__item posted-on">' . $posted_on . '</span>'; // WPCS: XSS OK.
					break;

				case 'cats' :
					/* translators: used between list items, there is a space after the comma */
					$categories_list = get_the_category_list( ' ' );

					echo '<span class="entry-meta__item cat-links"> ' . $categories_list . '</span>'; // WPCS: XSS OK.
					break;
			}
		}
	}
endif;

if ( ! function_exists( 'sober_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for post tags.
	 */
	function sober_entry_footer() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			$tags_list = get_the_tag_list( '', ' ' );

			if ( $tags_list ) {
				printf( '<span class="tags-links">%s</span>', $tags_list ); // WPCS: XSS OK.
			}
		}
	}
endif;

if ( ! function_exists( 'sober_currency_switcher' ) ) :
	/**
	 * Print HTML of currency switcher
	 * It requires plugin WooCommerce Currency Switcher installed
	 */
	function sober_currency_switcher( $args = array() ) {
		if ( ! class_exists( 'WOOCS' ) ) {
			return;
		}

		$args = wp_parse_args( $args, array(
			'show_flag' => false
		) );

		global $WOOCS;

		$currencies    = $WOOCS->get_currencies();
		$currency_list = array();

		foreach ( $currencies as $key => $currency ) {
			if ( $WOOCS->current_currency == $key ) {
				array_unshift( $currency_list, sprintf(
					'<li><a href="#" class="woocs_flag_view_item woocs_flag_view_item_current" data-currency="%s">%s%s</a></li>',
					esc_attr( $currency['name'] ),
					$args['show_flag'] ? '<img src="' . esc_url( $currency['flag'] ) . '" alt="' . esc_attr( $currency['name'] ) . '">' : '',
					esc_html( $currency['name'] )
				) );
			} else {
				$currency_list[] = sprintf(
					'<li><a href="#" class="woocs_flag_view_item" data-currency="%s">%s%s</a></li>',
					esc_attr( $currency['name'] ),
					$args['show_flag'] ? '<img src="' . esc_url( $currency['flag'] ) . '" alt="' . esc_attr( $currency['name'] ) . '">' : '',
					esc_html( $currency['name'] )
				);
			}
		}
		?>
		<div class="currency list-dropdown">
			<span class="current">
				<?php echo intval( $args['show_flag'] ) ? '<img src="' . esc_url( $currencies[ $WOOCS->current_currency ]['flag'] ) . '" alt="' . esc_attr( $currencies[ $WOOCS->current_currency ]['name'] ) . '">' : ''; ?>
				<?php echo esc_html( $currencies[ $WOOCS->current_currency ]['name'] ); ?>
				<span class="caret" aria-hidden="true"></span>
			</span>
			<ul>
				<?php echo implode( "\n\t", $currency_list ); ?>
			</ul>
		</div>
		<?php
	}
endif;

if ( ! function_exists( 'sober_language_switcher' ) ) :
	/**
	 * Print HTML of language switcher
	 * It requires plugin WPML installed
	 */
	function sober_language_switcher( $args = array() ) {
		$languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 1 ) );

		if ( empty( $languages ) ) {
			return;
		}

		$args    = wp_parse_args( $args, array( 'show_flag' => false ) );
		$list    = array();
		$current = '';

		foreach ( (array) $languages as $code => $language ) {
			$item = sprintf(
				'<li class="%s"><a href="%s">%s%s</a></li>',
				esc_attr( $code ),
				esc_url( $language['url'] ),
				$args['show_flag'] ? '<img src="' . esc_url( $language['country_flag_url'] ) . '" alt="' . esc_attr( $language['native_name'] ) . '">' : '',
				! empty( $language['translated_name'] ) ? esc_html( $language['translated_name'] ) : esc_html( $language['native_name'] )
			);

			if ( ! $language['active'] ) {
				$list[] = $item;
			} else {
				$current = $language;
				array_unshift( $list, $item );
			}
		}
		?>

		<div class="language list-dropdown">
			<span class="current">
				<?php echo intval( $args['show_flag'] ) ? '<img src="' . esc_url( $current['country_flag_url'] ) . '" alt="' . esc_attr( $current['native_name'] ) . '">' : ''; ?>
				<?php echo esc_html( $current['language_code'] ) ?>
				<span class="caret" aria-hidden="true"></span>
			</span>
			<ul>
				<?php echo implode( "\n\t", $list ); ?>
			</ul>
		</div>

		<?php
	}
endif;

if ( ! function_exists( 'sober_has_page_header' ) ) :
	/**
	 * Check if current page has page header
	 *
	 * @return bool
	 */
	function sober_has_page_header() {
		$has = sober_get_option( 'page_header_enable' );

		if ( is_page_template( 'templates/homepage.php' ) || is_page_template( 'templates/full-screen.php' ) ) {
			$has = false;
		} elseif ( is_page() && get_post_meta( get_the_ID(), 'hide_page_header', true ) ) {
			$has = false;
		} elseif ( is_singular( array( 'post', 'product', 'portfolio', 'elementor_library' ) ) ) {
			$has = false;
		} elseif ( is_404() ) {
			$has = false;
		} elseif ( is_home() ) {
			$posts_page_id = get_option( 'page_for_posts' );

			if ( $posts_page_id && get_post_meta( $posts_page_id, 'hide_page_header', true ) ) {
				$has = false;
			}
		} elseif ( is_post_type_archive( 'portfolio' ) ) {
			if ( 'masonry' != sober_get_option( 'portfolio_style' ) ) {
				$has = false;
			} else {
				$has = true;
			}
		} elseif ( class_exists( '\WCBoost\Wishlist\Helper' ) && \WCBoost\Wishlist\Helper::is_wishlist() ) {
			$has = false;
		} elseif ( function_exists( 'yith_wcwl_is_wishlist_page' ) && yith_wcwl_is_wishlist_page() ) {
			$has = false;
		} elseif ( function_exists( 'soow_is_wishlist' ) && soow_is_wishlist() ) {
			$has = false;
		} elseif ( sober_is_order_tracking_page() ) {
			$has = false;
		} elseif ( function_exists( 'WC' ) ) {
			if ( is_account_page() || is_cart() ) {
				$has = false;
			} elseif ( is_shop() && get_post_meta( wc_get_page_id( 'shop' ), 'hide_page_header', true ) ) {
				$has = false;
			}
		} elseif ( is_front_page() && ! is_home() ) {
			$has = false;
		}

		/**
		 * Filter for checking has page header
		 *
		 * @since  2.0.8
		 */
		return apply_filters( 'sober_has_page_header', $has );
	}
endif;

if ( ! function_exists( 'sober_get_page_header_image' ) ) :

	/**
	 * Get page header image URL
	 *
	 * @return string
	 */
	function sober_get_page_header_image() {
		if ( ! sober_has_page_header() ) {
			return '';
		}

		if ( function_exists( 'is_checkout' ) && is_checkout() ) {
			return '';
		}

		if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			$image = sober_get_option( 'shop_page_header_bg' );

			if ( is_shop() ) {
				$shop_image = get_post_meta( wc_get_page_id( 'shop' ), 'page_header_bg', true );
				$shop_image = $shop_image ? wp_get_attachment_image_url( $shop_image, 'full' ) : false;
				$image      = $shop_image ? $shop_image : $image;
			}
		} elseif ( is_home() && ! is_front_page() ) {
			$posts_page_id = get_option( 'page_for_posts' );

			if ( $posts_page_id ) {
				$image = get_post_meta( $posts_page_id, 'page_header_bg', true );
				$image = $image ? wp_get_attachment_image_url( $image, 'full' ) : false;
				$image = $image ? $image : sober_get_option( 'page_header_bg' );
			} else {
				$image = sober_get_option( 'page_header_bg' );
			}
		} elseif ( is_page() ) {
			$image = get_post_meta( get_the_ID(), 'page_header_bg', true );
			$image = $image ? wp_get_attachment_image_url( $image, 'full' ) : get_the_post_thumbnail_url( get_the_ID(), 'full' );
			$image = $image ? $image : sober_get_option( 'page_header_bg' );
		} elseif ( is_post_type_archive( 'portfolio' ) || is_tax( 'portfolio_type' ) ) {
			$image = sober_get_option( 'portfolio_page_header_bg' );
		} else {
			$image = sober_get_option( 'page_header_bg' );
		}

		// Double check for taxonomy page.
		if ( is_tax() || is_category() || is_tag() ) {
			$term_id  = get_queried_object_id();
			$image_id = absint( get_term_meta( $term_id, 'page_header_image_id', true ) );

			if ( $image_id ) {
				$tax_image = wp_get_attachment_image_url( $image_id, 'full' );
				$image     = $tax_image ? $tax_image : $image;
			}
		}

		return $image;
	}
endif;

if ( ! function_exists( 'sober_get_layout' ) ) :
	/**
	 * Get layout base on current page
	 *
	 * @return string
	 */
	function sober_get_layout() {
		$layout = sober_get_option( 'layout_default' );

		if ( is_404() || is_singular( array(
				'product',
				'portfolio',
				'elementor_library',
			) ) || is_post_type_archive( array( 'portfolio' ) ) || is_tax( 'portfolio_type' ) || sober_is_order_tracking_page() || is_page_template( 'templates/homepage.php' )
		) {
			$layout = 'no-sidebar';
		} elseif ( function_exists( 'is_cart' ) && is_cart() ) {
			$layout = 'no-sidebar';
		} elseif ( function_exists( 'is_checkout' ) && is_checkout() ) {
			$layout = 'no-sidebar';
		} elseif ( function_exists( 'is_account_page' ) && is_account_page() ) {
			$layout = 'no-sidebar';
		} elseif ( class_exists( '\WCBoost\Wishlist\Helper' ) && \WCBoost\Wishlist\Helper::is_wishlist() ) {
			$layout = 'no-sidebar';
		} elseif ( function_exists( 'soow_is_wishlist' ) && soow_is_wishlist() ) {
			$layout = 'no-sidebar';
		} elseif ( function_exists( 'yith_wcwl_is_wishlist_page' ) && yith_wcwl_is_wishlist_page() ) {
			$layout = 'no-sidebar';
		} elseif ( is_singular() && get_post_meta( get_the_ID(), 'custom_layout', true ) ) {
			$custom_layout = get_post_meta( get_the_ID(), 'layout', true );
			$layout = $custom_layout ? $custom_layout : $layout;
		} elseif ( is_singular( 'post' ) ) {
			$layout = sober_get_option( 'layout_post' );
		} elseif ( function_exists( 'is_woocommerce' ) && ( is_woocommerce() || is_post_type_archive( 'product' ) ) ) {
			$layout = sober_get_option( 'layout_shop' );

			$shop_page_id = wc_get_page_id( 'shop' );

			if ( $shop_page_id && is_shop() && get_post_meta( $shop_page_id, 'custom_layout', true ) ) {
				$custom_layout = get_post_meta( $shop_page_id, 'layout', true );
				$layout = $custom_layout ? $custom_layout : $layout;
			}
		} elseif ( is_page() ) {
			$layout = sober_get_option( 'layout_page' );
		}

		return $layout;
	}

endif;

if ( ! function_exists( 'sober_get_content_columns' ) ) :
	/**
	 * Get CSS classes for content columns
	 *
	 * @param string $layout
	 *
	 * @return array
	 */
	function sober_get_content_columns( $layout = null ) {
		$layout = $layout ? $layout : sober_get_layout();

		if ( 'no-sidebar' == $layout ) {
			return array();
		}

		if ( is_page() ) {
			return array( 'col-md-9', 'col-sm-12', 'col-xs-12' );
		}

		return array( 'col-md-8', 'col-sm-12', 'col-xs-12' );
	}

endif;

if ( ! function_exists( 'sober_content_columns' ) ) :

	/**
	 * Display CSS classes for content columns
	 *
	 * @param string $layout
	 */
	function sober_content_columns( $layout = null ) {
		echo implode( ' ', sober_get_content_columns( $layout ) );
	}

endif;

if ( ! function_exists( 'the_comments_pagination' ) ) :
	/**
	 * Back compact function for comments pagination
	 *
	 * @param array $args
	 */
	function the_comments_pagination( $args = array() ) {
		if ( get_comment_pages_count() < 1 || get_option( 'page_comments' ) ) {
			return;
		}
		?>
		<nav class="navigation comments-pagination" role="navigation">
			<h2 class="screen-reader-text"><?php esc_html_e( 'Comments navigation', 'sober' ) ?></h2>
			<div class="nav-links">
				<?php paginate_comments_links( $args ) ?>
			</div>
		</nav>
		<?php
	}

endif;

if ( ! function_exists( 'sober_entry_thumbnail' ) ) :
	/**
	 * Show entry thumbnail base on its format
	 *
	 * @since  1.0
	 */
	function sober_entry_thumbnail( $size = 'thumbnail' ) {
		$html = '';

		switch ( get_post_format() ) {
			case 'gallery':
				$images = get_post_meta( get_the_ID(), 'images' );

				if ( empty( $images ) ) {
					break;
				}

				$gallery = array();
				foreach ( $images as $image ) {
					$gallery[] = wp_get_attachment_image( $image, $size );
				}
				$html .= '<div class="entry-gallery entry-image">' . implode( '', $gallery ) . '</div>';
				break;

			case 'audio':

				$thumb = get_the_post_thumbnail( get_the_ID(), $size );
				if ( ! empty( $thumb ) ) {
					$html .= '<a class="entry-image" href="' . get_permalink() . '">' . $thumb . '</a>';
				}

				$audio = get_post_meta( get_the_ID(), 'audio', true );
				if ( ! $audio ) {
					break;
				}

				// If URL: show oEmbed HTML or jPlayer
				if ( filter_var( $audio, FILTER_VALIDATE_URL ) ) {
					if ( $oembed = @wp_oembed_get( $audio, array( 'width' => 1140 ) ) ) {
						$html .= $oembed;
					} else {
						$html .= '<div class="audio-player">' . wp_audio_shortcode( array( 'src' => $audio ) ) . '</div>';
					}
				} else {
					$html .= $audio;
				}
				break;

			case 'video':
				$video = get_post_meta( get_the_ID(), 'video', true );
				if ( ! $video ) {
					break;
				}

				// If URL: show oEmbed HTML
				if ( filter_var( $video, FILTER_VALIDATE_URL ) ) {
					if ( $oembed = @wp_oembed_get( $video, array( 'width' => 1140 ) ) ) {
						$html .= $oembed;
					} else {
						$atts = array(
							'src'   => $video,
							'width' => 1140,
						);

						if ( has_post_thumbnail() ) {
							$atts['poster'] = get_the_post_thumbnail_url( get_the_ID(), 'full' );
						}
						$html .= wp_video_shortcode( $atts );
					}
				} // If embed code: just display
				else {
					$html .= $video;
				}
				break;

			default:
				$html = get_the_post_thumbnail( get_the_ID(), $size );

				break;
		}

		echo apply_filters( __FUNCTION__, $html, get_post_format() );
	}

endif;

if ( ! function_exists( 'sober_social_share' ) ) :
	/**
	 * Print HTML for post sharing
	 *
	 * @param int $post_id
	 */
	function sober_social_share( $post_id = null ) {
		$post_id = $post_id ? $post_id : get_the_ID();
		?>
		<ul class="socials-share">
			<li class="socials-share__facebook">
				<a target="_blank" class="share-facebook social"
				   href="http://www.facebook.com/sharer.php?u=<?php echo urlencode( get_permalink( $post_id ) ); ?>&t=<?php echo urlencode( get_the_title( $post_id ) ); ?>">
					<i class="fa fa-facebook"></i>
				</a>
			</li>
			<li class="socials-share__twitter">
				<a class="share-twitter social"
				   href="http://twitter.com/share?text=<?php echo esc_attr( get_the_title( $post_id ) ); ?>&url=<?php echo urlencode( get_permalink( $post_id ) ); ?>"
				   target="_blank">
					<i class="fa fa-twitter"></i>
				</a>
			</li>
			<?php if ( has_post_thumbnail( $post_id ) ) : ?>
				<li class="socials-share__pinterest">
					<a target="_blank" class="share-pinterest social"
					   href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode( get_the_post_thumbnail_url( $post_id, 'full' ) ); ?>&description=<?php echo urlencode( get_the_title( $post_id ) ); ?>"><i
							class="fa fa-pinterest-p"></i>
					</a>
				</li>
			<?php endif; ?>
		</ul>
		<?php
	}
endif;

if ( ! function_exists( 'sober_is_order_tracking_page' ) ) :
	/**
	 * Check if current page is order tracking page
	 *
	 * @return bool
	 */
	function sober_is_order_tracking_page() {
		$page_id = get_option( 'sober_order_tracking_page_id' );
		$page_id = sober_get_translated_object_id( $page_id );

		if ( ! $page_id ) {
			return false;
		}

		return is_page( $page_id );
	}
endif;

if ( ! function_exists( 'sober_get_translated_object_id' ) ) :
	/**
	 * Get translated object ID if the WPML plugin is installed
	 * Return the original ID if this plugin is not installed
	 *
	 * @param int    $id            The object ID
	 * @param string $type          The object type 'post', 'page', 'post_tag', 'category' or 'attachment'. Default is 'page'
	 * @param bool   $original      Set as 'true' if you want WPML to return the ID of the original language element if the translation is missing.
	 * @param bool   $language_code If set, forces the language of the returned object and can be different than the displayed language.
	 *
	 * @return mixed
	 */
	function sober_get_translated_object_id( $id, $type = 'page', $original = true, $language_code = false ) {
		return apply_filters( 'wpml_object_id', $id, $type, $original, $language_code );
	}
endif;

if ( ! function_exists( 'sober_get_mega_menu_setting_default' ) ) :
	/**
	 * Get the default mega menu settings of a menu item
	 *
	 * @return array
	 */
	function sober_get_mega_menu_setting_default() {
		return apply_filters(
			'sober_mega_menu_setting_default',
			array(
				'mega'         => false,
				'icon'         => '',
				'hide_text'    => false,
				'disable_link' => false,
				'content'      => '',
				'width'        => '',
				'border'       => array(
					'left' => 0,
				),
				'background'   => array(
					'image'      => '',
					'color'      => '',
					'attachment' => 'scroll',
					'size'       => '',
					'repeat'     => 'no-repeat',
					'position'   => array(
						'x'      => 'left',
						'y'      => 'top',
						'custom' => array(
							'x' => '',
							'y' => '',
						),
					),
				),
			)
		);
	}
endif;

if ( ! function_exists( 'sober_parse_args' ) ) :
	/**
	 * Recursive merge user defined arguments into defaults array.
	 *
	 * @param array $args
	 * @param array $default
	 *
	 * @return array
	 */
	function sober_parse_args( $args, $default = array() ) {
		$args   = (array) $args;
		$result = $default;

		foreach ( $args as $key => $value ) {
			if ( is_array( $value ) && isset( $result[ $key ] ) ) {
				$result[ $key ] = sober_parse_args( $value, $result[ $key ] );
			} else {
				$result[ $key ] = $value;
			}
		}

		return $result;
	}

endif;

if ( ! function_exists( 'get_theme_file_path' ) ) :
	/**
	 * Retrieves the path of a file in the theme.
	 *
	 * Searches in the stylesheet directory before the template directory so themes
	 * which inherit from a parent theme can just override one file.
	 *
	 * @param string $file Optional. File to search for in the stylesheet directory.
	 *
	 * @return string The path of the file.
	 */
	function get_theme_file_path( $file = '' ) {
		$file = ltrim( $file, '/' );

		if ( empty( $file ) ) {
			$path = get_stylesheet_directory();
		} elseif ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
			$path = get_stylesheet_directory() . '/' . $file;
		} else {
			$path = get_template_directory() . '/' . $file;
		}

		/**
		 * Filters the path to a file in the theme.
		 *
		 * @param string $path The file path.
		 * @param string $file The requested file to search for.
		 */
		return apply_filters( 'theme_file_path', $path, $file );
	}
endif;

if ( ! function_exists( 'get_theme_file_uri' ) ) :
	/**
	 * Retrieves the URL of a file in the theme.
	 *
	 * Searches in the stylesheet directory before the template directory so themes
	 * which inherit from a parent theme can just override one file.
	 *
	 * @param string $file Optional. File to search for in the stylesheet directory.
	 *
	 * @return string The URL of the file.
	 */
	function get_theme_file_uri( $file = '' ) {
		$file = ltrim( $file, '/' );

		if ( empty( $file ) ) {
			$url = get_stylesheet_directory_uri();
		} elseif ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
			$url = get_stylesheet_directory_uri() . '/' . $file;
		} else {
			$url = get_template_directory_uri() . '/' . $file;
		}

		/**
		 * Filters the URL to a file in the theme.
		 *
		 * @param string $url  The file URL.
		 * @param string $file The requested file to search for.
		 */
		return apply_filters( 'theme_file_uri', $url, $file );
	}
endif;

if ( ! function_exists( 'sober_portfolio_filter' ) ) :
	/**
	 * Get portfolio types and display it as a filter for Isotope script
	 */
	function sober_portfolio_filter( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'class' => '',
			'echo' => true,
		) );

		$types = get_terms( array(
			'taxonomy'   => 'portfolio_type',
			'hide_empty' => true,
		) );

		if ( ! $types || is_wp_error( $types ) || 1 === count( $types ) ) {
			return;
		}

		$filter   = array();
		$filter[] = '<li data-filter="*" class="line-hover active">' . esc_html__( 'All', 'sober' ) . '</li>';

		foreach ( $types as $type ) {
			$filter[] = sprintf( '<li data-filter=".portfolio_type-%s" class="line-hover">%s</li>', esc_attr( $type->slug ), esc_html( $type->name ) );
		}

		$filter = sprintf(
			'<div class="portfolio-filter %s"><ul class="filter">%s</ul></div>',
			esc_attr( $args['class'] ),
			implode( "\n\t", $filter )
		);

		if ( ! $args['echo'] ) {
			return $filter;
		}

		echo ! empty( $filter ) ? $filter : '';
	}
endif;

if ( ! function_exists( 'sober_shopping_cart_icon' ) ) {
	/**
	 * Get shopping cart icon HTML
	 */
	function sober_shopping_cart_icon( $echo = true ) {
		$source = sober_get_option( 'shop_cart_icon_source' );
		$icon   = sober_svg_icon( 'icon=basket-addtocart&echo=0' );

		if ( 'image' == $source ) {
			$width  = floatval( sober_get_option( 'shop_cart_icon_width' ) );
			$height = floatval( sober_get_option( 'shop_cart_icon_height' ) );

			$width  = $width ? ' width="' . $width . 'px"' : '';
			$height = $height ? ' height="' . $height . 'px"' : '';

			$dark  = sober_get_option( 'shop_cart_icon_image' );
			$light = sober_get_option( 'shop_cart_icon_image_light' );
			$light = $light ? $light : $dark;

			if ( $dark ) {
				$icon = sprintf(
					'<span class="shopping-cart-icon"><img src="%1$s" alt="%2$s" %3$s class="icon-dark"><img src="%4$s" alt="%2$s" %3$s class="icon-light"></span>',
					esc_url( $dark ),
					esc_attr__( 'Shopping Cart', 'sober' ),
					$width . $height,
					esc_url( $light )
				);
			}
		} else {
			$svg = sober_get_option( 'shop_cart_icon' );

			if ( $svg ) {
				$icon = sober_svg_icon( array( 'icon' => $svg, 'echo' => false ) );
			}
		}

		if ( ! $echo ) {
			return $icon;
		}

		echo ! empty( $icon ) ? $icon : '';
	}
}

if ( ! function_exists( 'sober_header_icons' ) ) :
	/**
	 * Print header icons base on settings in Customizer
	 *
	 * @param string $header_version
	 * @param string $position
	 */
	function sober_header_icons( $header_version = 'v1', $position = 'right' ) {
		switch ( $header_version ) {
			case 'v4':
				$icons = sober_get_option( 'header_icons_' . $position . '_v4' );
				break;

			case 'v5':
				$icons = sober_get_option( 'header_icons_' . $position );
				break;

			default:
				$icons = sober_get_option( 'header_icons' );
				break;
		}

		if ( empty( $icons ) ) {
			return;
		}

		foreach ( (array) $icons as $icon ) {
			switch ( $icon ) {
				case 'cart':
					if ( ! function_exists( 'WC' ) ) {
						break;
					}

					$cart_behavior = is_cart() || is_checkout() ? 'link' : sober_get_option( 'shop_cart_icon_behaviour' );

					printf(
						'<li class="menu-item menu-item-cart">
							<a href="%s" class="cart-contents" data-toggle="%s" data-target="cart-modal" data-tab="cart">
								%s
								<span class="count cart-counter">%s</span>
							</a>
						</li>',
						esc_url( wc_get_cart_url() ),
						esc_attr( $cart_behavior ),
						sober_shopping_cart_icon( false ),
						intval( WC()->cart->get_cart_contents_count() )
					);
					break;

				case 'wishlist':
					$wishlist_url = '';
					$wishlist_count = 0;

					if ( class_exists( '\WCBoost\Wishlist\Plugin' ) ) {
						$wishlist_url = wc_get_page_permalink( 'wishlist' );
						$wishlist_count = \WCBoost\Wishlist\Helper::get_wishlist()->count_items();
					} elseif ( defined( 'YITH_WCWL' ) ) {
						$wishlist_url = get_permalink( yith_wcwl_object_id( get_option( 'yith_wcwl_wishlist_page_id' ) ) );
						$wishlist_count = yith_wcwl_count_products();
					} elseif ( function_exists( 'Soo_Wishlist' ) ) {
						$wishlist_url = soow_get_wishlist_url();
						$wishlist_count = soow_count_products();
					}

					if ( ! empty( $wishlist_url ) ) {
						printf(
							'<li class="menu-item menu-item-wishlist">
								<a href="%s" class="wishlist-contents" data-toggle="%s" data-target="cart-modal" data-tab="wishlist">
									' . sober_svg_icon( 'icon=heart-wishlist-like&echo=0' ) . '
									<span class="count wishlist-counter">%s</span>
								</a>
							</li>',
							esc_url( $wishlist_url ),
							esc_attr( sober_get_option( 'wishlist_icon_behaviour' ) ),
							$wishlist_count
						);
					}
					break;

				case 'login':
					if ( ! function_exists( 'WC' ) ) {
						break;
					}
					$toggle = is_user_logged_in() ? 'link' : sober_get_option( 'account_icon_behaviour' );
					printf(
						'<li class="menu-item menu-item-account">
							<a href="%s" data-toggle="%s" data-target="login-modal">
								' . sober_svg_icon( 'icon=user-account-people&echo=0' ) . '
								<span class="screen-reader-text">%s</span>
							</a>
						</li>',
						esc_url( wc_get_account_endpoint_url( 'dashboard' ) ),
						esc_attr( $toggle ),
						esc_html__( 'Account', 'sober' )
					);
					break;

				case 'search':
					echo '<li class="menu-item menu-item-search">
							<a href="#" data-toggle="modal" data-target="search-modal">
								' . sober_svg_icon( 'icon=search&echo=0' ) . '
								<span class="screen-reader-text">' . esc_html__( 'Search', 'sober' ) . '</span>
							</a>
						</li>';
					break;

				case 'currency':
					sober_currency_switcher();
					break;

				case 'language':
					sober_language_switcher();
					break;

				case 'socials':
					if ( has_nav_menu( 'socials' ) ) {
						echo '<li class="menu-item menu-item-socials">';
						wp_nav_menu( array(
							'theme_location' => 'socials',
							'container'      => '',
							'menu_class'     => 'socials-menu',
							'menu_id'        => 'header-social-icons-' . $position,
							'depth'          => 1,
						) );
						echo '</li>';
					}
					break;
			}
		}
	}
endif;

if ( ! function_exists( 'sober_typography_css' ) ) :
	/**
	 * Get typography CSS base on settings
	 *
	 * @since 1.1.6
	 */
	function sober_typography_css() {
		$css        = '';
		$properties = array(
			'font-family'    => 'font-family',
			'font-size'      => 'font-size',
			'variant'        => 'font-weight',
			'line-height'    => 'line-height',
			'letter-spacing' => 'letter-spacing',
			'color'          => 'color',
			'text-transform' => 'text-transform',
			'text-align'     => 'text-align',
		);

		$settings = array(
			'typo_body'                      => 'body,button,input,select,textarea',
			'typo_link'                      => 'a',
			'typo_link_hover'                => 'a:hover',
			'typo_h1'                        => 'h1, .h1',
			'typo_h2'                        => 'h2, .h2',
			'typo_h3'                        => 'h3, .h3',
			'typo_h4'                        => 'h4, .h4',
			'typo_h5'                        => 'h5, .h5',
			'typo_h6'                        => 'h6, .h6',
			'typo_menu'                      => '.nav-menu > li > a, .side-menu .menu > li > a',
			'typo_submenu'                   => '.nav-menu .sub-menu a, .side-menu .sub-menu a',
			'typo_toggle_menu'               => '.primary-menu.side-menu .menu > li > a',
			'typo_toggle_submenu'            => '.primary-menu.side-menu .sub-menu li a',
			'typo_mobile_menu'               => '.mobile-menu.side-menu .menu > li > a',
			'typo_mobile_submenu'            => '.mobile-menu.side-menu .sub-menu li a',
			'typo_page_header_title'         => '.page-header .page-title',
			'typo_page_header_minimal_title' => '.page-header-style-minimal .page-header .page-title',
			'typo_breadcrumb'                => '.woocommerce .woocommerce-breadcrumb, .breadcrumb',
			'type_widget_title'              => '.widget-title',
			'type_product_title'             => '.woocommerce div.product .product_title',
			'type_product_excerpt'           => '.woocommerce div.product .woocommerce-product-details__short-description, .woocommerce div.product div[itemprop="description"]',
			'typo_woocommerce_headers'       => '.woocommerce .upsells h2, .woocommerce .related h2',
			'type_footer_info'               => '.footer-info',
		);

		$inherit_settings = array(
			'typo_body'          => '.vc_custom_heading, .sober-section-heading, .sober-banner-image__title, .sober-banner .banner-text, .sober-banner3 .banner-text, .sober-chart .text, .sober-pricing-table .table-header .pricing, .sober-countdown .box',
			'typo_h1'            => 'h1.vc_custom_heading, h1.sober-section-heading, .sober-banner4 .banner-content h1, .project-header .project-title',
			'typo_h2'            => 'h2.vc_custom_heading, h2.sober-section-heading, .sober-banner4 .banner-content h2, .sober-category-banner .banner-title, .sober-modal .modal-header h4, .sober-popup .popup-content h2',
			'typo_h3'            => 'h3.vc_custom_heading, h3.sober-section-heading, .sober-banner4 .banner-content h3, .sober-subscribe-box__title, .sober-banner-simple__text, .sober-popup .popup-content h3, .portfolio-items .portfolio .project-title, .sober-product .product-title, .sober-collection-carousel__item-title, .sober-image-slider__item-text',
			'typo_h4'            => 'h4.vc_custom_heading, h4.sober-section-heading, .sober-banner4 .banner-content h4, .sober-popup .popup-content h4',
			'typo_h5'            => 'h5.vc_custom_heading, h5.sober-section-heading, .sober-banner4 .banner-content h5',
			'typo_h6'            => 'h6.vc_custom_heading, h6.sober-section-heading, .sober-banner4 .banner-content h6',
			'type_product_title' => '.woocommerce div.product .product_title, .woocommerce div.product.layout-style-5 p.price,
									.woocommerce div.product.layout-style-5 span.price, .woocommerce div.product.layout-style-6 p.price,
									.woocommerce div.product.layout-style-6 span.price',
		);

		$sofia_font_settings = array(
			'typo_page_header_title',
			'type_widget_title',
			'type_product_title',
			'typo_woocommerce_headers',
		);

		$load_sofia = false;
		$sofia_css  = '';

		foreach ( $settings as $setting => $selector ) {
			$typography = sober_get_option( $setting );
			$style      = '';

			foreach ( $properties as $key => $property ) {
				if ( ! empty( $typography[ $key ] ) ) {
					$value = 'font-family' == $key ? rtrim( trim( $typography[ $key ] ), ',' ) : $typography[ $key ];
					$value = 'variant' == $key ? str_replace( 'regular', '400', $value ) : $value;

					if ( $value ) {
						$style .= $property . ': ' . $value . ';';
					}
				}
			}

			if ( ! empty( $style ) ) {
				$css .= $selector . '{' . $style . '}';
			}

			if ( in_array( $setting, $sofia_font_settings ) ) {
				if ( empty( $typography['font-family'] ) || 'Sofia Pro' == $typography['font-family'] ) {
					$load_sofia = true;
				}
			}

			if ( in_array( $setting, array_keys( $inherit_settings ) ) && ! empty( $typography['font-family'] ) ) {
				$sofia_css .= $inherit_settings[ $setting ] . '{ font-family:' . rtrim( trim( $typography['font-family'] ), ',' ) . '; }';
			}
		}

		if ( $load_sofia ) {
			$font_face = '
				@font-face {
					font-family: "Sofia Pro";
					src: url( ' . get_theme_file_uri( 'fonts/sofiapro-light-webfont.woff2' ) . ' ) format("woff2");
					font-weight: 300;
					font-style: normal;
					font-display: swap;
				}
			';

			$css = $font_face . $css;
		} else {
			$css .= $sofia_css;
		}

		return $css;
	}
endif;

if ( ! function_exists( 'sober_get_instagram_images' ) ) :
	/**
	 * Get Instagram images
	 *
	 * @param string $deprecated
	 * @param int    $limit
	 *
	 * @return array|WP_Error
	 */
	function sober_get_instagram_images( $deprecated = '', $limit = 12 ) {
		if ( ! empty( $deprecated ) ) {
			_deprecated_argument( __FUNCTION__, '2.3.2' );
		}

		$access_token = sober_get_option( 'footer_instagram_access_token' );

		if ( empty( $access_token ) ) {
			return new WP_Error( 'instagram_no_access_token', esc_html__( 'No access token', 'sober' ) );
		}

		$transient_key = 'sober_instagram_photos' . sanitize_title_with_dashes( $access_token ) . '_' . $limit;
		$images        = get_transient( $transient_key );

		if ( false === $images || empty( $images ) ) {
			$images = array();
			$next = false;

			while ( count( $images ) < $limit ) {
				if ( ! $next ) {
					$fetched = sober_fetch_instagram_media( $access_token );
				} else {
					$fetched = sober_fetch_instagram_media( $next );
				}

				if ( is_wp_error( $fetched ) ) {
					break;
				}

				$images = array_merge( $images, $fetched['images'] );

				if ( $fetched['paging'] && $fetched['paging']['cursors']['after'] ) {
					$next = $fetched['paging']['cursors']['after'];
				}
			}

			if ( ! empty( $images ) ) {
				set_transient( $transient_key, $images, 2 * 3600 ); // Cache for 2 hours.
			}
		}

		if ( ! empty( $images ) ) {
			return $images;
		} else {
			return new WP_Error( 'instagram_no_images', esc_html__( 'Instagram did not return any images.', 'sober' ) );
		}
	}
endif;

if ( ! function_exists( 'sober_get_instagram_images_by_access_token' ) ) :
	/**
	 * Get Instagram images by Access Token
	 *
	 * @param string $access_token
	 * @param int    $limit
	 *
	 * @return array|WP_Error
	 */
	function sober_get_instagram_images_by_access_token( $access_token, $limit = 12 ) {
		_deprecated_function( 'sober_get_instagram_images_by_access_token', '2.3.2', 'sober_get_instagram_images' );

		$transient_key = 'sober_instagram_photos_' . md5( $access_token . '__' . $limit );
		$images = get_transient( $transient_key );

		if ( false === $images || empty( $images ) ) {
			$url = add_query_arg( array(
				'count'        => $limit,
				'access_token' => $access_token,
			), 'https://api.instagram.com/v1/users/self/media/recent/' );

			$remote = wp_remote_retrieve_body( wp_remote_get( $url ) );
			$data   = json_decode( $remote, true );
			$images = array();

			if ( $data['meta']['code'] == 200 ) {
				foreach ( $data['data'] as $media ) {
					$images[] = array(
						'description' => $media['caption'],
						'link'        => $media['link'],
						'time'        => $media['created_time'],
						'comments'    => $media['comments']['count'],
						'likes'       => $media['likes']['count'],
						'type'        => $media['type'],
						'thumbnail'   => $media['images']['thumbnail']['url'],
						'small'       => $media['images']['low_resolution']['url'],
						'large'       => $media['images']['standard_resolution']['url'],
						'original'    => $media['images']['standard_resolution']['url'],
					);
				}

				if ( ! empty( $images ) ) {
					set_transient( $transient_key, $images, 2 * 3600 );
				}
			} else {
				return new WP_Error( 'instagram_error', $data['error_message'] );
			}
		}

		if ( ! empty( $images ) ) {
			return $images;
		} else {
			return new WP_Error( 'instagram_no_images', esc_html__( 'Instagram did not return any images.', 'sober' ) );
		}
	}
endif;

if ( ! function_exists( 'sober_fetch_instagram_media' ) ) :
	/**
	 * Fetch photos from Instagram API
	 *
	 * @param  string $access_token
	 * @return array
	 */
	function sober_fetch_instagram_media( $access_token ) {
		$url = add_query_arg( array(
			'fields'       => 'id,caption,media_type,media_url,permalink,thumbnail_url',
			'access_token' => $access_token,
		), 'https://graph.instagram.com/me/media' );

		$remote = wp_remote_retrieve_body( wp_remote_get( $url ) );
		$data   = json_decode( $remote, true );
		$images = array();

		if ( ! $data ) {
			return new WP_Error( 'instagram_error', esc_html__( 'No data', 'sober' ) );
		} elseif ( isset( $data['error'] ) ) {
			return new WP_Error( 'instagram_error', $data['error']['message'] );
		} elseif ( isset( $data['data'] ) ) {
			foreach ( $data['data'] as $media ) {
				$images[] = array(
					'description' => isset( $media['caption'] ) ? $media['caption'] : $media['id'],
					'link'        => $media['permalink'],
					'type'        => $media['media_type'],
					'thumbnail'   => ! empty( $media['thumbnail_url'] ) ? $media['thumbnail_url'] : $media['media_url'],
					'small'       => ! empty( $media['thumbnail_url'] ) ? $media['thumbnail_url'] : $media['media_url'],
					'large'       => $media['media_url'],
					'original'    => $media['media_url'],
				);
			}
		}

		return array(
			'images' => $images,
			'paging' => $data['paging'] ? $data['paging'] : false,
		);
	}
endif;

if ( ! function_exists( 'sober_refresh_instagram_access_token' ) ) :
	/**
	 * Refresh Instagram Access Token
	 */
	function sober_refresh_instagram_access_token() {
		$access_token = sober_get_option( 'footer_instagram_access_token' );

		if ( empty( $access_token ) ) {
			return new WP_Error( 'no_access_token', esc_html__( 'No access token', 'sober' ) );
		}

		$data = wp_remote_get( 'https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token=' . $access_token );
		$data = wp_remote_retrieve_body( $data );
		$data = json_decode( $data, true );

		if ( isset( $data['error'] ) ) {
			return new WP_Error( 'access_token_refresh', $data['error']['message'] );
		}

		$new_access_token = $data['access_token'];

		set_theme_mod( 'footer_instagram_access_token', $new_access_token );

		return $new_access_token;
	}
endif;

if ( ! function_exists( 'sober_instagram_image' ) ) {
	/**
	 * Display a single Instagram photo
	 *
	 * @param array $media
	 */
	function sober_instagram_image( $media, $as_background = false, $tag = 'li' ) {
		if ( ! is_array( $media ) ) {
			return;
		}

		$srcset = array(
			$media['thumbnail'] . ' 160w',
			$media['small'] . ' 320w',
			$media['large'] . ' 640w',
			$media['large'] . ' 2x',
		);
		$sizes  = array(
			'(max-width: 1280px) 320px',
			'320px',
		);

		$caption = is_array( $media['description'] ) && isset( $media['description']['text'] ) ? $media['description']['text'] : $media['description'];

		$image  = sprintf(
			'<img src="%s" alt="%s" srcset="%s" sizes="%s">',
			esc_url( $media['small'] ),
			esc_attr( $caption ),
			esc_attr( implode( ', ', $srcset ) ),
			esc_attr( implode( ', ', $sizes ) )
		);

		$style = '';

		if ( $as_background ) {
			$style = 'style="background-image: url(' . esc_url( $media['small'] ) . ')"';
		}

		printf(
			'%s<a href="%s" target="_blank" rel="nofollow" %s>%s</a>%s',
			empty( $tag ) ? '' : '<' . esc_attr( $tag ) . '>',
			esc_url( $media['link'] ),
			$style,
			$image,
			empty( $tag ) ? '' : '</' . esc_attr( $tag ) . '>'
		);
	}
}

if ( ! function_exists( 'sober_mobile_header_icon' ) ) :
	/**
	 * Display the header icon base on settings in Customizer
	 */
	function sober_mobile_header_icon() {
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		$icon = sober_get_option( 'mobile_header_icon' );

		switch ( $icon ) {
			case 'cart':
				printf(
					'<a href="%s" class="cart-contents menu-item-mobile-cart hidden-lg" data-toggle="%s" data-target="cart-modal" data-tab="cart">%s%s</a>',
					esc_url( wc_get_cart_url() ),
					esc_attr( sober_get_option( 'shop_cart_icon_behaviour' ) ),
					sober_shopping_cart_icon( false ),
					sober_get_option( 'mobile_cart_badge' ) ? '<span class="count cart-counter">' . intval( WC()->cart->get_cart_contents_count() ) . '</span>' : ''
				);

				break;

			case 'wishlist':
				$wishlist_url = '';
				$wishlist_count = 0;

				if ( class_exists( '\WCBoost\Wishlist\Plugin' ) ) {
					$wishlist_url = wc_get_page_permalink( 'wishlist' );
					$wishlist_count = \WCBoost\Wishlist\Helper::get_wishlist()->count_items();
				} elseif ( defined( 'YITH_WCWL' ) ) {
					$wishlist_url = get_permalink( yith_wcwl_object_id( get_option( 'yith_wcwl_wishlist_page_id' ) ) );
					$wishlist_count = yith_wcwl_count_products();
				} elseif ( function_exists( 'Soo_Wishlist' ) ) {
					$wishlist_url = soow_get_wishlist_url();
					$wishlist_count = soow_count_products();
				}

				if ( ! empty( $wishlist_url ) ) {
					printf(
						'<a href="%s" class="wishlist-contents menu-item-mobile-wishlist hidden-lg" data-toggle="%s" data-target="cart-modal" data-tab="wishlist">
							' . sober_svg_icon( 'icon=heart-wishlist-like&echo=0' ) . '
							%s
						</a>',
						esc_url( $wishlist_url ),
						esc_attr( sober_get_option( 'wishlist_icon_behaviour' ) ),
						sober_get_option( 'mobile_wishlist_badge' ) ? '<span class="count wishlist-counter">' . $wishlist_count . '</span>' : ''
					);
				}
				break;

			case 'search':
				echo '<a href="#" data-toggle="modal" class="menu-item-mobile-search hidden-lg" data-target="search-modal">
						' . sober_svg_icon( 'icon=search&echo=0' ) . '
						<span class="screen-reader-text">' . esc_html__( 'Search', 'sober' ) . '</span>
					</a>';
				break;
		}
	}
endif;

/**
 * Get new product ids
 *
 * @return array
 */
function sober_get_new_product_ids() {
	// Load from cache.
	$product_ids = get_transient( 'sober_woocommerce_products_new' );

	// Valid cache found.
	if ( false !== $product_ids ) {
		return $product_ids;
	}

	$product_ids = array();

	// Get products which are set as new.
	$meta_query = WC()->query->get_meta_query();
	$meta_query[] = array(
		'key'   => '_is_new',
		'value' => 'yes',
	);
	$new_products = new WP_Query( array(
		'posts_per_page'   => -1,
		'post_type'        => 'product',
		'fields'           => 'ids',
		'meta_query'       => $meta_query,
		'suppress_filters' => true, // Get in all languages.
	) );

	if ( $new_products->have_posts() ) {
		$product_ids = array_merge( $product_ids, $new_products->posts );
	}

	// Get products after selected days.
	$newness = intval( sober_get_option( 'product_newness' ) );

	if ( $newness > 0 ) {
		$new_products = new WP_Query( array(
			'posts_per_page'   => -1,
			'post_type'        => 'product',
			'fields'           => 'ids',
			'suppress_filters' => true, // Get in all languages.
			'date_query'       => array(
				'after' => date( 'Y-m-d', strtotime( '-' . $newness . ' days' ) )
			)
		) );

		if ( $new_products->have_posts() ) {
			$product_ids = array_merge( $product_ids, $new_products->posts );
		}
	}

	set_transient( 'sober_woocommerce_products_new', $product_ids, DAY_IN_SECONDS );

	return $product_ids;
}

/**
 * Check if we are in Elementor editor mode
 *
 * @return bool
 */
function sober_is_elementor_editor_mode() {
	if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
		return false;
	}

	if ( ! version_compare( PHP_VERSION, '5.4', '>=' ) ) {
		return false;
	}

	return \Elementor\Plugin::$instance->editor->is_edit_mode();
}

if ( ! function_exists( 'wp_body_open' ) ) {
	/**
	 * Adds backwards compatibility for wp_body_open() introduced with WordPress 5.2
	 *
	 * @since 3.0.0
	 * @see https://developer.wordpress.org/reference/functions/wp_body_open/
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
}

if ( ! function_exists( 'sober_product_has_video' ) ) {
	/**
	 * Check if product has video
	 *
	 * @param mixed $product_id
	 *
	 * @return string
	 */
	function sober_product_has_video( $product_id = false ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return '';
		}

		global $product;

		if ( ! $product_id ) {
			$product_id = $product ? $product->get_id() : false;
		}

		if ( ! $product_id ) {
			return '';
		}

		$video_url = get_post_meta( $product_id, 'video_url', true );

		return filter_var( $video_url, FILTER_VALIDATE_URL );
	}
}

if ( ! function_exists( 'sober_get_product_video' ) ) {
	/**
	 * Get the product video HTML
	 */
	function sober_get_product_video() {
		if ( ! class_exists( 'WooCommerce' ) || ! sober_product_has_video() ) {
			return '';
		}

		global $product;

		$video_image = get_post_meta( $product->get_id(), 'video_thumbnail', true );
		$video_url   = get_post_meta( $product->get_id(), 'video_url', true );
		$video_html  = '';

		$classes = array(
			'woocommerce-product-gallery__image',
			'woocommerce-product-gallery__video',
			'sober-product-video',
		);

		$video_thumb = wp_get_attachment_image_src( $video_image, 'woocommerce_single' );
		$video_thumb = $video_thumb ? $video_thumb[0] : wc_placeholder_img_src( 'woocommerce_single' );

		$atts = array(
			'src'      => $video_url,
			'width'    => 1024,
			'height'   => 768,
			'loop'     => 1,
			'autoplay' => true,
		);

		if ( preg_match( '#^https?://(.+\.)?vimeo\.com/.*#', $video_url ) ) {
			$classes[] = 'sober-product-video--vimeo';
		}

		$video_html = wp_video_shortcode( $atts );

		if ( strpos( $video_html, '<video ' ) !== false ) {
			if ( sober_is_product_video_muted() ) {
				$video_html = str_replace( '<video ', '<video muted playsinline ', $video_html );
			}
		} else {
			$video_html = wp_oembed_get( $video_url, array(
				'width'  => 1024,
				'height' => 768,
				'loop'   => 1,
			) );
		}

		if ( ! $video_html ) {
			return '';
		}

		$html = '<div data-thumb="' . esc_attr( $video_thumb ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . '">';
		$html .= '<div class="sober-product-video__wrapper"><div class="sober-product-video__content">';
		$html .= $video_html;
		$html .= '</div></div></div>';

		return $html;
	}
}

if ( ! function_exists( 'sober_get_product_video_thumbnail' ) ) {
	/**
	 * Get the HTML of product video thumbnail
	 */
	function sober_get_product_video_thumbnail() {
		if ( ! class_exists( 'WooCommerce' ) || ! sober_product_has_video() ) {
			return '';
		}

		global $product;

		$video_image  = get_post_meta( $product->get_id(), 'video_thumbnail', true );
		$video_position = get_post_meta( $product->get_id(), 'video_position', true );

		$full_size       = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
		$full_size_image = wp_get_attachment_image_src( $video_image, $full_size );
		$thumbnail       = wp_get_attachment_image_src( $video_image, 'woocommerce_gallery_thumbnail' );
		$thumbnail_2x    = wp_get_attachment_image_src( $video_image, 'sober_wc_gallery_thumbnail' );
		$src = $thumbnail ? $thumbnail[0] : wc_placeholder_img_src( 'sober_wc_gallery_thumbnail' );

		$image_attr = array(
			'src'        => $src,
			'data-o_src' => $src,
			'srcset'     => $thumbnail_2x ? $thumbnail_2x[0] . ' 2x' : wc_placeholder_img_src( 'sober_wc_gallery_thumbnail' ),
		);

		$image_attr = array_map( 'esc_attr', $image_attr );

		$full_size_image_url = $full_size_image ? $full_size_image[0] : wc_placeholder_img_src( 'woocommerce_gallery_full_size' );

		$html  = '<div class="woocommerce-product-gallery__image '. ( $video_position == 'first' ? 'active' : '' ) .'"><a href="' . esc_url( $full_size_image_url ) . '">';
		$html .= '<img';
		foreach ( $image_attr as $name => $value ) {
			$html .= " $name=" . '"' . $value . '"';
		}
		$html .= ' />';
		$html .= '<span class="play-icon">' . sober_svg_icon( 'icon=play&echo=0' ) . '</span>';
		$html .= '</a></div>';

		return $html;
	}
}

if ( ! function_exists( 'sober_is_product_video_muted' ) ) {
	/**
	 * Check if a product video muted.
	 *
	 * @param int|object $product_id
	 */
	function sober_is_product_video_muted( $product_id = false ) {
		if ( ! class_exists( 'WooCommerce' ) || ! sober_product_has_video() ) {
			return false;
		}

		$_product = $product_id ? wc_get_product( $product_id ) : false;

		if ( ! $_product && is_product() ) {
			$_product = $GLOBALS['product'];
		}

		if ( ! $_product ) {
			return false;
		}

		$video_audio = get_post_meta( $_product->get_id(), 'mute_video', true );

		if ( 'mute' == $video_audio ) {
			return true;
		} elseif ( 'unmute' == $video_audio ) {
			return false;
		}

		return intval( sober_get_option( 'product_mute_video' ) );
	}
}

if ( ! function_exists( 'sober_get_current_page_id' ) ) {
	/**
	 * Get the ID of current page, include the shop and portfolio page.
	 *
	 * @return null|int
	 */
	function sober_get_current_page_id() {
		if ( is_singular() ) {
			$id = get_the_ID();
		} elseif ( is_home() ) {
			$id = get_option( 'page_for_posts' );
		} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
			$id = wc_get_page_id( 'shop' );
		} elseif ( is_post_type_archive( 'portfolio' ) || is_tax( 'portfolio_type' ) ) {
			$id = get_option( 'sober_portfolio_page_id' );
		} else {
			$id = NULL;
		}

		return $id;
	}
}

if ( ! function_exists( 'sober_get_product_attribute_type' ) ) {
	/**
	 * Get attribute base on the plugin support
	 *
	 * @param string $attribute_name
	 * @param string $default
	 *
	 * @return string
	 */
	function sober_get_product_attribute_type( $attribute_name, $product_id = null ) {
		global $product;

		$product_id = $product_id ? $product_id : $product->get_ID();

		if ( class_exists( '\WCBoost\VariationSwatches\Swatches' ) ) {
			$swatches = \WCBoost\VariationSwatches\Swatches::instance()->get_swatches_args( $product_id, $attribute_name );
			return $swatches['swatches_type'];
		} elseif ( function_exists( 'TA_WCVS' ) ) {
			$attribute = TA_WCVS()->get_tax_attribute( $attribute_name );
			return $attribute->attribute_type;
		}

		return 'select';
	}
}


if ( ! function_exists( 'sober_svg_icon' ) ) {
	/**
	 * Render the svg icon
	 *
	 * @param string $name The icon name
	 * @param array  $echo Echo the icon or return the code
	 *
	 * @return void|string
	 */
	function sober_svg_icon( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'icon'     => '',
			'size'     => 20,
			'class'    => '',
			'title'    => '',
			'desc'     => '',
			'echo'     => true,
		) );

		$attributes = array(
			'role' => 'img',
		);

		if ( ! empty( $args['size'] ) ) {
			if ( is_array( $args['size'] ) ) {
				$attributes['viewBox'] = '0 0 ' . implode( ' ', $args['size'] );
				$attributes['width']   = absint( $args['size'][0] );
				$attributes['height']  = absint( $args['size'][1] );
			} else {
				$attributes['viewBox'] = '0 0 ' . absint( $args['size'] ) . ' ' . absint( $args['size'] );
				$attributes['width']   = absint( $args['size'] );
				$attributes['height']  = absint( $args['size'] );
			}
		}

		if ( ! empty( $args['class'] ) ) {
			$attributes['class'] = $args['class'];
		}

		$attribute_string = '';

		foreach ( $attributes as $name => $value ) {
			$attribute_string .= $name . '="' . esc_attr( $value ) . '" ';
		}

		// Begin SVG markup.
		$svg = '<svg ' . $attribute_string . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Display the title.
		if ( $args['title'] ) {
			$unique_id = uniqid();
			$svg       .= '<title id="title-' . $unique_id . '">' . esc_html( $args['title'] ) . '</title>';

			// Display the desc only if the title is already set.
			if ( $args['desc'] ) {
				$svg .= '<desc id="desc-' . $unique_id . '">' . esc_html( $args['desc'] ) . '</desc>';
			}
		}

		$svg .= ' <use href="#' . esc_attr( $args['icon'] ) . '" xlink:href="#' . esc_attr( $args['icon'] ) . '"></use> ';

		$svg .= '</svg>';

		$svg = apply_filters( 'sober_svg_icon', $svg, $args );

		if ( has_filter( 'sober_svg_icon_' . $args['icon'] ) ) {
			$svg = apply_filters( 'sober_svg_icon_' . $args['icon'], $svg, $args );
		}

		if ( ! $args['echo'] ) {
			return $svg;
		}

		echo ! empty( $svg ) ? $svg : '';
	}
}
