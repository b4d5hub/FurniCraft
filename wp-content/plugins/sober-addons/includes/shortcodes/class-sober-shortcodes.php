<?php

class Sober_Shortcodes {
	public static $current_banner = 1;

	/**
	 * Init shortcodes
	 */
	public static function init() {
		$shortcodes = array(
			'button',
			'product_grid',
			'product_carousel',
			'product_tabs',
			'post_grid',
			'countdown',
			'category_banner',
			'product',
			'banner2',
			'banner3',
			'banner4',
			'banner_grid_4',
			'banner_grid_5',
			'banner_grid_5_2',
			'banner_grid_6',
			'chart',
			'message_box',
			'icon_box',
			'pricing_table',
			'map',
			'map2',
			'testimonial',
			'partners',
			'contact_box',
			'info_list',
			'faq',
			'team_member',
			'woocs',
			'wpml',
			'subscribe_box',
			'empty_space',
			'banner_simple',
			'collection_carousel',
			'portfolio_grid',
			'portfolio_masonry',
			'portfolio_metro',
			'social_menu',
		);

		foreach ( $shortcodes as $shortcode ) {
			add_shortcode( 'sober_' . $shortcode, array( __CLASS__, $shortcode ) );
		}

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_filter( 'post_class', array( __CLASS__, 'product_class' ), 10, 3 );

		add_action( 'wp_ajax_nopriv_sober_load_products', array( __CLASS__, 'ajax_load_products' ) );
		add_action( 'wp_ajax_sober_load_products', array( __CLASS__, 'ajax_load_products' ) );
		add_action( 'wc_ajax_sober_load_products', array( __CLASS__, 'ajax_load_products' ) );
	}

	/**
	 * Load scripts
	 */
	public static function enqueue_scripts() {
		/**
		 * CSS
		 */
		wp_register_style( 'leaflet', SOBER_ADDONS_URL . 'assets/css/leaflet.css', array(), '1.7.1' );

		/**
		 * Script
		 */
		wp_deregister_script( 'isotope' );
		wp_register_script( 'isotope', SOBER_ADDONS_URL . 'assets/js/isotope.pkgd.min.js', array(
			'jquery',
			'imagesloaded',
		), '3.0.6', true );
		wp_register_script( 'jquery-countdown', SOBER_ADDONS_URL . 'assets/js/jquery.countdown.js', array( 'jquery' ), '2.0.4', true );
		wp_register_script( 'jquery-circle-progress', SOBER_ADDONS_URL . 'assets/js/circle-progress.js', array( 'jquery' ), '1.1.3', true );

		if ( ! wp_script_is( 'leaflet', 'registered' ) && ! wp_script_is( 'leaflet', 'enqueued' ) ) {
			wp_register_script( 'leaflet', SOBER_ADDONS_URL . 'assets/js/leaflet.js', array( 'jquery' ), '1.7.1', true );
		}

		wp_enqueue_script( 'sober-shortcodes', SOBER_ADDONS_URL . 'assets/js/shortcodes.js', array(
			'isotope',
			'wp-util',
			'jquery-countdown',
			'jquery-circle-progress'
		), '20160725', true );
	}

	/**
	 * Add classes to products which are inside loop of shortcodes
	 *
	 * @param array  $classes
	 * @param string $class
	 * @param int    $post_id
	 *
	 * @return array
	 */
	public static function product_class( $classes, $class, $post_id ) {
		if ( ! $post_id || get_post_type( $post_id ) !== 'product' || is_single( $post_id ) ) {
			return $classes;
		}

		global $woocommerce_loop;
		$accept_products = array(
			'sober_product_grid',
			'sober_ajax_products',
		);

		if ( ! isset( $woocommerce_loop['name'] ) || ! in_array( $woocommerce_loop['name'], $accept_products ) ) {
			return $classes;
		}

		// Add class for new products
		$newness = get_theme_mod( 'product_newness', false );
		if ( $newness && ( time() - ( 60 * 60 * 24 * $newness ) ) < strtotime( get_the_time( 'Y-m-d' ) ) ) {
			$classes[] = 'new';
		}

		return $classes;
	}

	/**
	 * Ajax load products
	 */
	public static function ajax_load_products() {
		// check_ajax_referer( 'sober_get_products', 'nonce' );

		wp_send_json_success( self::products_shortcode( $_POST['atts'] ) );
	}

	/**
	 * Product grid shortcode
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function product_grid( $atts ) {
		$atts = shortcode_atts( array(
			'per_page'      => 15,
			'type'          => 'recent',
			'category'      => '',
			'tag'           => '',
			'columns'       => 4,
			'order'         => 'ASC',
			'orderby'       => '',
			'load_more'     => false,
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-product-grid',
			'sober-products',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( $atts['load_more'] ) {
			$css_class[] = 'loadmore-enabled';
		}

		return sprintf(
			'<div class="sober-product-grid sober-products %s">%s</div>',
			esc_attr( trim( implode( ' ', $css_class ) ) ),
			self::products_shortcode( $atts )
		);
	}

	/**
	 * Product grid filterable
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function product_carousel( $atts ) {
		$atts = shortcode_atts( array(
			'per_page'        => 15,
			'columns'         => 4,
			'type'            => 'recent',
			'category'        => '',
			'tag'             => '',
			'orderby'         => '',
			'order'           => 'ASC',
			'autoplay'        => 5000,
			'loop'            => false,
			'auto_responsive' => true,
			'columns_mobile'  => '2',
			'columns_tablet'  => '3',
			'breakpoints'     => 'mobile=0&tablet=768&desktop=1024',
			'css_animation'   => '',
			'el_class'        => '',
		), $atts, 'sober_' . __FUNCTION__ );


		$css_class = array(
			'sober-product-carousel',
			'sober-products',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$responsive = '';

		if ( ! $atts['auto_responsive'] ) {
			$default_breakpoints = array( 'mobile' => 0, 'tablet' => 768, 'desktop' => 1024 );
			$breakpoints = wp_parse_args( $atts['breakpoints'], $default_breakpoints );
			$breakpoints = array_map( 'intval', $breakpoints );
			$breakpoints = array_intersect_key( $breakpoints, $default_breakpoints );

			$responsive = array(
				'mobile'  => array( 'items' => max( 1, $atts['columns_mobile'] ) ),
				'tablet'  => array( 'items' => max( 1, $atts['columns_tablet'] ) ),
				'desktop' => array( 'items' => max( 1, $atts['columns'] ) ),
			);

			$responsive = array_combine( $breakpoints, $responsive );
			$responsive = json_encode( $responsive );
		}

		return sprintf(
			'<div class="%s" data-columns="%s" data-autoplay="%s" data-loop="%s" data-responsive="%s">%s</div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_attr( $atts['columns'] ),
			esc_attr( $atts['autoplay'] ),
			esc_attr( $atts['loop'] ),
			esc_attr( $responsive ),
			self::products_shortcode( $atts )
		);
	}

	/**
	 * Product grid filterable
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function product_tabs( $atts ) {
		$atts = shortcode_atts( array(
			'per_page'      => 15,
			'columns'       => 4,
			'filter'        => 'group', // group, category, tag.
			'filter_type'   => 'isotope',
			'groups'        => 'best_sellers,new_products,sale_products',
			'category'      => '',
			'tag'           => '',
			'orderby'       => '',
			'order'         => 'ASC',
			'show_all'      => false,
			'load_more'     => false,
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-product-grid',
			'sober-product-tabs',
			'sober-products',
			'sober-products-filterable',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( $atts['filter'] ) {
			$css_class[] = 'filterable';
			$css_class[] = 'filter-by-' . $atts['filter'];
			$css_class[] = 'filter-type-' . $atts['filter_type'];
		}

		if ( $atts['load_more'] ) {
			$css_class[] = 'loadmore-enabled';
		}

		$filter = array();

		if ( 'category' == $atts['filter'] || 'tag' == $atts['filter'] ) {
			$taxonomy = 'category' == $atts['filter'] ? 'product_cat' : 'product_tag';
			$field    = 'category' == $atts['filter'] ? 'category' : 'tag';
			$slugs    = explode( ',', trim( $atts[ $field ] ) );

			if ( 'category' == $field && empty( $atts[ $field ] ) ) {
				$terms = get_terms( $field );
			} elseif ( ! empty( $atts[ $field ] ) ) {
				$terms = get_terms( array(
					'taxonomy' => $taxonomy,
					'slug'     => $slugs,
					'orderby'  => 'slug__in',
				) );
			}

			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				$atts[ $field ] = array();

				foreach ( $terms as $index => $term ) {
					$filter[] = sprintf(
						'<li data-filter=".%s-%s" class="line-hover %s">%s</li>',
						esc_attr( $taxonomy ),
						esc_attr( $term->slug ),
						'ajax' == $atts['filter_type'] && ! $index && ! $atts['show_all'] ? 'active' : '',
						esc_html( $term->name )
					);

					$atts[ $field ][] = $term->slug;
				}

				if ( $atts['show_all'] ) {
					$atts[ $field ] = implode( ',', $atts[ $field ] );
				} else {
					$atts[ $field ] = $slugs[0];
				}

				// First tab
				if ( 'isotope' == $atts['filter_type'] ) {
					array_unshift( $filter, '<li data-filter="*" class="line-hover active">' . esc_html__( 'All Products', 'sober' ) . '</li>' );
				} elseif ( $atts['show_all'] ) {
					array_unshift( $filter, '<li data-filter="' . esc_attr( $atts[ $field ] ) . '" class="line-hover active">' . esc_html__( 'All Products', 'sober' ) . '</li>' );
				}
			}
		} else {
			$groups = array_map( 'trim', explode( ',', $atts['groups'] ) );

			if ( ! empty( $groups ) ) {
				$atts['type'] = $groups[0]; // Prepare for product_loop only.
				$active_tab = $groups[0];
			}

			// "All Products" tab is not supported with isotope effects.
			if ( 'isotope' != $atts['filter_type'] && $atts['show_all'] ) {
				$atts['type'] = 'recent'; // Prepare for product_loop only
				$active_tab = 'recent';
				$filter[] = '<li data-filter=".recent" class="line-hover active">' . esc_html__( 'All Products', 'sober' ) . '</li>';
			}

			$labels = array(
				'best_sellers'      => esc_html__( 'Best Sellers', 'sober' ),
				'new_products'      => esc_html__( 'New Products', 'sober' ),
				'sale_products'     => esc_html__( 'Sale Products', 'sober' ),
				'featured_products' => esc_html__( 'Hot Products', 'sober' ),
			);

			$filter_class = array(
				'best_sellers'      => '.best_sellers',
				'new_products'      => '.new',
				'sale_products'     => '.sale',
				'featured_products' => '.featured',
			);

			foreach ( $groups as $group ) {
				$is_active = $active_tab == $group;
				$filter_selector = $is_active && 'isotope' == $atts['filter_type'] ? '*' : $filter_class[ $group ];

				$filter[] = sprintf(
					'<li data-filter="%s" class="line-hover %s">%s</li>',
					esc_attr( $filter_selector ),
					$is_active ? 'active' : '',
					$labels[ $group ]
				);
			}
		}

		$filter  = empty( $filter ) ? '' : '<div class="product-filter"><ul class="filter">' . implode( "\n\t", $filter ) . '</ul></div>';
		$loading = '';

		if ( 'ajax' == $atts['filter_type'] ) {
			$loading = '
				<div class="products-loading-overlay">
					<span class="loading-icon">
						<span class="bubble"><span class="dot"></span></span>
						<span class="bubble"><span class="dot"></span></span>
						<span class="bubble"><span class="dot"></span></span>
					</span>
				</div>';
		}

		// Unset incorrect attributes for products shortcode.
		unset( $atts['filter'] );
		unset( $atts['filter_type'] );

		return sprintf(
			'<div class="%s" data-columns="%s" data-per_page="%s" data-load_more="%s" data-orderby="%s" data-order="%s" data-nonce="%s">%s<div class="products-grid">%s%s</div></div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_attr( $atts['columns'] ),
			esc_attr( $atts['per_page'] ),
			esc_attr( $atts['load_more'] ),
			esc_attr( $atts['orderby'] ),
			esc_attr( $atts['order'] ),
			esc_attr( wp_create_nonce( 'sober_get_products' ) ),
			$filter,
			$loading,
			self::products_shortcode( $atts )
		);
	}

	/**
	 * Post grid
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function post_grid( $atts ) {
		$atts = shortcode_atts( array(
			'per_page'      => 3,
			'columns'       => 3,
			'category'      => '',
			'hide_meta'     => false,
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-post-grid',
			'post-grid',
			'columns-' . $atts['columns'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$output = array();

		$args = array(
			'post_type'              => 'post',
			'posts_per_page'         => $atts['per_page'],
			'ignore_sticky_posts'    => 1,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
		);

		if ( $atts['category'] ) {
			$args['category_name'] = trim( $atts['category'] );
		}

		$posts = new WP_Query( $args );

		if ( ! $posts->have_posts() ) {
			return '';
		}

		$column_class = 'col-sm-6 col-md-' . ( 12 / absint( $atts['columns'] ) );

		while ( $posts->have_posts() ) : $posts->the_post();
			$post_class = get_post_class( $column_class );
			$thumbnail  = $meta = '';

			if ( has_post_thumbnail() ) :
				$icon = '';

				if ( 'gallery' == get_post_format() ) {
					$icon = '<span class="format-icon"><svg viewBox="0 0 20 20"><use xlink:href="#gallery"></use></svg></span>';
				} elseif ( 'video' == get_post_format() ) {
					$icon = '<span class="format-icon"><svg viewBox="0 0 20 20"><use xlink:href="#play"></use></svg></span>';
				}

				$thumbnail = sprintf(
					'<a href="%s" class="post-thumbnail">%s%s</a>',
					esc_url( get_permalink() ),
					get_the_post_thumbnail( get_the_ID(), 'sober-blog-grid' ),
					$icon
				);
			endif;

			if ( ! $atts['hide_meta'] ) {
				$posted_on = sprintf(
					'<time class="entry-date published updated" datetime="%1$s">%2$s</time>',
					esc_attr( get_the_date( 'c' ) ),
					esc_html( get_the_date( get_option( 'date_format', 'd.m Y' ) ) )
				);

				$categories_list = get_the_category_list( ' ' );

				$meta = '<span class="posted-on">' . $posted_on . '</span><span class="cat-links"> ' . $categories_list . '</span>'; // WPCS: XSS OK.
			}

			$output[] = sprintf(
				'<div class="%s">
					%s
					<div class="post-summary">
						<div class="entry-meta">%s</div>
						<h3 class="entry-title"><a href="%s" rel="bookmark">%s</a></h3>
						<div class="entry-summary">%s</div>
						<a class="line-hover read-more active" href="%s">%s</a>
					</div>
				</div>',
				esc_attr( implode( ' ', $post_class ) ),
				$thumbnail,
				$atts['hide_meta'] ? '' : '<div class="entry-meta">' . $meta . '</div>',
				esc_url( get_permalink() ),
				get_the_title(),
				get_the_excerpt(),
				esc_url( get_permalink() ),
				esc_html__( 'Read More', 'sober' )
			);
		endwhile;

		wp_reset_postdata();

		return sprintf(
			'<div class="sober-post-grid post-grid %s">
				<div class="row">%s</div>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			implode( '', $output )
		);
	}

	/**
	 * Count down
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function countdown( $atts ) {
		$atts = shortcode_atts( array(
			'date'          => '',
			'text_align'    => 'left',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		if ( empty( $atts['date'] ) ) {
			return '';
		}

		$css_class = array(
			'sober-countdown',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( ! empty( $atts['text_align'] ) ) {
			$css_class[] = 'text-' . $atts['text_align'];
		}

		$output   = array();
		$output[] = sprintf( '<div class="timers" data-date="%s">', esc_attr( $atts['date'] ) );
		$output[] = sprintf( '<div class="timer-day box"><span class="time day"></span><span class="title">%s</span></div>', esc_html__( 'Days', 'sober' ) );
		$output[] = sprintf( '<div class="timer-hour box"><span class="time hour"></span><span class="title">%s</span></div>', esc_html__( 'Hours', 'sober' ) );
		$output[] = sprintf( '<div class="timer-min box"><span class="time min"></span><span class="title">%s</span></div>', esc_html__( 'Mins', 'sober' ) );
		$output[] = sprintf( '<div class="timer-secs box"><span class="time secs"></span><span class="title">%s</span></div>', esc_html__( 'Sec', 'sober' ) );
		$output[] = '</div>';

		return sprintf(
			'<div class="%s">%s</div>',
			esc_attr( implode( ' ', $css_class ) ),
			implode( '', $output )
		);
	}

	/**
	 * Button
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function button( $atts ) {
		$atts = shortcode_atts( array(
			'label'         => '',
			'link'          => '',
			'style'         => 'normal',
			'size'          => 'normal',
			'align'         => 'inline',
			'color'         => 'dark',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$attributes = array();

		$css_class = array(
			'sober-button',
			'button-type-' . $atts['style'],
			'button-color-' . $atts['color'],
			'align-' . $atts['align'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( 'light' == $atts['style'] ) {
			$css_class[] = 'button-light line-hover';
		} else {
			$css_class[] = 'button';
			$css_class[] = $atts['size'];
			$css_class[] = 'button-' . $atts['size'];
		}

		if ( ! empty( $atts['link'] ) ) {
			$link = self::build_link( $atts['link'] );

			if ( ! empty( $link['url'] ) ) {
				$attributes['href'] = $link['url'];
			}

			if ( ! empty( $link['title'] ) ) {
				$attributes['title'] = $link['title'];
			}

			if ( ! empty( $link['target'] ) ) {
				$attributes['target'] = $link['target'];
			}

			if ( ! empty( $link['rel'] ) ) {
				$attributes['rel'] = $link['rel'];
			}
		}

		$attributes['class'] = implode( ' ', $css_class );
		$attr                = array();

		foreach ( $attributes as $name => $value ) {
			$attr[] = $name . '="' . esc_attr( $value ) . '"';
		}

		$button = sprintf(
			'<%1$s %2$s>%3$s</%1$s>',
			empty( $attributes['href'] ) ? 'span' : 'a',
			implode( ' ', $attr ),
			esc_html( $atts['label'] )
		);

		if ( 'center' == $atts['align'] ) {
			return '<div class="sober-button-wrapper text-center">' . $button . '</div>';
		}

		return $button;
	}

	/**
	 * Category Banner
	 *
	 * @param string $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function category_banner( $atts, $content ) {
		$atts = shortcode_atts( array(
			'image'          => '',
			'image_position' => 'left',
			'title'          => '',
			'text_position'  => 'top-left',
			'link'           => '',
			'button_text'    => '',
			'css_animation'  => '',
			'el_class'       => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-category-banner',
			'text-position-' . $atts['text_position'],
			'image-' . $atts['image_position'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$link = self::build_link( $atts['link'] );

		$src   = '';
		$image = '';

		if ( is_numeric( $atts['image'] ) ) {
			$image = wp_get_attachment_image_src( $atts['image'], 'full' );

			if ( $image ) {
				$src   = $image[0];
				$image = sprintf( '<img alt="%s" src="%s">',
					esc_attr( $atts['image'] ),
					esc_url( $src )
				);
			}
		} elseif ( $atts['image'] ) {
			$src   = $atts['image'];
			$image = sprintf( '<img alt="%s" src="%s">',
				esc_attr( strip_tags( $atts['title'] ) ),
				esc_url( $src )
			);
		}

		return sprintf(
			'<div class="%s">
				<div class="banner-inner">
					<a href="%s" target="%s" rel="%s" class="banner-image" style="%s">%s</a>
					<div class="banner-content">
						<h2 class="banner-title">%s</h2>
						<div class="banner-text">%s</div>
						<a href="%s" target="%s" rel="%s" class="sober-button button-light line-hover active">%s</a>
					</div>
				</div>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_url( $link['url'] ),
			esc_attr( $link['target'] ),
			esc_attr( $link['rel'] ),
			$src ? 'background-image: url(' . esc_url( $src ) . ');' : '',
			$image,
			esc_html( $atts['title'] ),
			$content,
			esc_url( $link['url'] ),
			esc_attr( $link['target'] ),
			esc_attr( $link['rel'] ),
			esc_html( $atts['button_text'] )
		);
	}

	/**
	 * Product
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function product( $atts, $content ) {
		$atts = shortcode_atts( array(
			'button_behaviour' => 'link',
			'product_id'       => self::get_default_product_id(),
			'image'            => '',
			'title'            => '',
			'price'            => '',
			'link'             => '',
			'scheme'           => 'light',
			'css_animation'    => '',
			'el_class'         => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$button_behaviour = $atts['button_behaviour'];

		if ( 'add_to_cart' == $button_behaviour && ! $atts['product_id'] ) {
			return esc_html__( 'This product is not exist.' );
		}

		$_product = wc_get_product( absint( $atts['product_id'] ) );

		if ( 'add_to_cart' == $button_behaviour && ! $_product ) {
			return esc_html__( 'This product is not exist.' );
		}

		$css_class = array(
			'sober-product',
			'sober-product--' . $button_behaviour . '-behaviour',
			$atts['scheme'] . '-scheme',
			'woocommerce',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$src   = '';
		$image = '';

		if ( is_numeric( $atts['image'] ) ) {
			$image = wp_get_attachment_image_src( $atts['image'], 'full' );
			$src   = $image[0];
			$image = sprintf( '<img alt="%s" src="%s">', esc_attr( $atts['title'] ), esc_url( $image[0] ) );
		} elseif ( $atts['image'] ) {
			$src = $atts['image'];
			$image = '<img src="' . esc_url( $atts['image'] ) . '" alt="' . esc_attr( strip_tags( $atts['title'] ) ) . '">';
		}

		$link  = self::build_link( $atts['link'] );
		$price = floatval( $atts['price'] );

		if ( shortcode_exists( 'woocs_show_custom_price' ) ) {
			$price = do_shortcode( '[woocs_show_custom_price value="' . $price . '"]' );
		} else {
			$price = wc_price( $price );
		}

		$button = sprintf(
			'<span class="button">%s</span>',
			$link['title'] ? esc_html( $link['title'] ) : esc_html__( 'Add to cart', 'sober' )
		);
		$over_link = sprintf(
			'<a href="%s" target="%s" rel="%s" class="overlink">%s</a>',
			esc_url( $link['url'] ),
			esc_attr( $link['target'] ),
			esc_attr( $link['rel'] ),
			esc_html__( 'View Product', 'sober' )
		);

		if ( 'add_to_cart' == $button_behaviour ) {
			$atts['title'] = $atts['title'] ? $atts['title'] : $_product->get_title();
			$content = $content ? $content : wp_trim_words( $_product->get_description(), 10, '...' );
			$price = $_product->get_price_html();

			if ( ! $atts['image'] ) {
				$image = wp_get_attachment_image_src( $_product->get_image_id(), 'full' );
				$src = $image[0];
				$image = sprintf( '<img alt="%s" src="%s">', esc_attr( $atts['title'] ), esc_url( $image[0] ) );
			}

			$link_attributes = array(
				'href'             => $_product->add_to_cart_url(),
				'data-product_id'  => $_product->get_id(),
				'data-product_sku' => $_product->get_sku(),
				'aria-label'       => $_product->add_to_cart_description(),
				'data-quantity'    => '1',
				'rel'              => 'nofollow',
				'class'            => array(
					'button',
					'add-to-cart',
					$_product->is_purchasable() && $_product->is_in_stock() ? 'add_to_cart_button' : '',
					$_product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
				),
			);

			$attributes = array();

			foreach( $link_attributes as $attribute => $value ) {
				if ( is_array( $value ) ) {
					$value = implode( ' ', $value );
				}

				if ( $attribute == 'href' ) {
					$value = esc_url( $value );
				} else {
					$value = esc_attr( $value );
				}

				$attributes[] = sprintf( '%s="%s" ', $attribute, $value );
			}

			$button = sprintf( '<a %s>%s</a>', implode( '', $attributes ), esc_html( $_product->add_to_cart_text() ) );
			$over_link = sprintf(
				'<a href="%s" class="overlink">%s</a>',
				esc_url( $_product->get_permalink() ),
				esc_html__( 'View Product', 'sober' )
			);
		}

		return sprintf(
			'<div class="%s">
				<div class="product-image" style="%s">
					%s
				</div>
				<div class="product-info">
					<h3 class="product-title">%s</h3>
					<div class="product-desc">%s</div>
					<div class="product-price">
						<span class="price">%s</span>
						%s
					</div>
				</div>
				%s
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			$src ? 'background-image: url(' . esc_url( $src ) . ');' : '',
			$image,
			esc_html( $atts['title'] ),
			$content,
			$price,
			$button,
			$over_link
		);
	}

	/**
	 * Banner 2 with buttons
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function banner2( $atts ) {
		$atts = shortcode_atts( array(
			'image'         => '',
			'image_size'    => '',
			'buttons'       => '',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-banner2',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);
		$image     = '';

		if ( is_numeric( $atts['image'] ) ) {
			$size = apply_filters( 'sober_banner_size', $atts['image_size'], $atts, 'sober_banner2' );

			if ( function_exists( 'wpb_getImageBySize' ) ) {
				$image = wpb_getImageBySize( array(
					'attach_id'  => $atts['image'],
					'thumb_size' => $size,
				) );

				$image = $image['thumbnail'];
			} else {
				$size_array = explode( 'x', $size );
				$size       = count( $size_array ) == 1 ? $size : $size_array;

				$image = wp_get_attachment_image_src( $atts['image'], $size );

				if ( $image ) {
					$image = sprintf( '<img alt="%s" src="%s">',
						esc_attr( $atts['image'] ),
						esc_url( $image[0] )
					);
				}
			}
		} elseif ( $atts['image'] ) {
			$image = '<img src="' . esc_url( $atts['image'] ) . '" alt="">';
		}

		$buttons        = vc_param_group_parse_atts( $atts['buttons'] );
		$buttons_output = array();
		foreach ( (array) $buttons as $index => $button ) {
			$link = self::build_link( $button['link'] );

			$buttons_output[] = sprintf(
				'<a href="%s" target="%s" title="%s" rel="%s" class="banner-button banner-button-%s">%s</a>',
				esc_url( $link['url'] ),
				esc_attr( $link['target'] ),
				esc_attr( $link['title'] ),
				esc_attr( $link['rel'] ),
				esc_attr( $index + 1 ),
				esc_html( $button['text'] )
			);
		}

		return sprintf(
			'<div class="%s">%s<div class="banner-buttons">%s</div></div>',
			esc_attr( implode( ' ', $css_class ) ),
			$image,
			implode( '', $buttons_output )
		);
	}

	/**
	 * Banner 3
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function banner3( $atts ) {
		$atts = shortcode_atts( array(
			'image'         => '',
			'image_size'    => '',
			'text'          => '',
			'text_align'    => 'left',
			'scheme'        => 'dark',
			'link'          => '',
			'button_text'   => '',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-banner3',
			'text-align-' . $atts['text_align'],
			$atts['scheme'] . '-scheme',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);
		$link      = self::build_link( $atts['link'] );
		$image     = '';

		if ( is_numeric( $atts['image'] ) ) {
			$size = apply_filters( 'sober_banner_size', $atts['image_size'], $atts, 'sober_banner3' );

			if ( function_exists( 'wpb_getImageBySize' ) ) {
				$image = wpb_getImageBySize( array(
					'attach_id'  => $atts['image'],
					'thumb_size' => $size,
				) );

				$image = $image['thumbnail'];
			} else {
				$size_array = explode( 'x', $size );
				$size       = count( $size_array ) == 1 ? $size : $size_array;

				$image = wp_get_attachment_image_src( $atts['image'], $size );

				if ( $image ) {
					$image = sprintf( '<img alt="%s" src="%s">',
						esc_attr( $atts['text'] ),
						esc_url( $image[0] )
					);
				}
			}
		} elseif ( $atts['image'] ) {
			$image = '<img src="' . esc_url( $atts['image'] ) . '" alt="' . esc_attr( strip_tags( $atts['text'] ) ) . '">';
		}

		return sprintf(
			'<div class="%s">
				<%s>
					%s
					<span class="banner-content">
						<span class="banner-text">%s</span>
						<span class="sober-button button-light line-hover active">%s</span>
					</span>
				</%s>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			empty( $link['url'] ) ? 'div class="banner-wrapper"' : 'a href="' . esc_url( $link['url'] ) . '" target="' . esc_attr( $link['target'] ) . '" rel="' . esc_attr( $link['rel'] ) . '" title="' . esc_attr( $link['title'] ) . '"',
			$image,
			esc_html( $atts['text'] ),
			esc_html( $atts['button_text'] ),
			empty( $link['url'] ) ? 'div' : 'a'
		);
	}

	/**
	 * Banner 4
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function banner4( $atts, $content ) {
		$atts = shortcode_atts( array(
			'image'            => '',
			'image_size'       => 'full',
			'align_vertical'   => 'top',
			'align_horizontal' => 'left',
			'link'             => '',
			'button_text'      => '',
			'button_style'     => 'light',
			'scheme'           => 'dark',
			'css_animation'    => '',
			'el_class'         => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-banner4',
			'horizontal-align-' . $atts['align_horizontal'],
			'vertical-align-' . $atts['align_vertical'],
			$atts['scheme'] . '-scheme',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$link    = self::build_link( $atts['link'] );
		$image   = '';
		$content = function_exists( 'wpb_js_remove_wpautop' ) ? wpb_js_remove_wpautop( $content, true ) : $content;

		if ( is_numeric( $atts['image'] ) ) {
			$size = apply_filters( 'sober_banner_size', $atts['image_size'], $atts, 'sober_banner4' );

			if ( function_exists( 'wpb_getImageBySize' ) ) {
				$image = wpb_getImageBySize( array(
					'attach_id'  => $atts['image'],
					'thumb_size' => $size,
				) );

				$image = $image['thumbnail'];
			} else {
				$size_array = explode( 'x', $size );
				$size       = count( $size_array ) == 1 ? $size : $size_array;

				$image = wp_get_attachment_image_src( $atts['image'], $size );

				if ( $image ) {
					$image = sprintf( '<img alt="%s" src="%s">',
						esc_attr( $atts['text'] ),
						esc_url( $image[0] )
					);
				}
			}
		} elseif ( $atts['image'] ) {
			$image = '<img src="' . esc_url( $atts['image'] ) . '" alt="' . esc_attr( strip_tags( $content ) ) . '">';
		}

		$link = empty( $link['url'] ) ? '' : sprintf( '<a href="%s" target="%s" rel="%s" title="%s">%s</a>',
			esc_url( $link['url'] ),
			esc_attr( $link['target'] ),
			esc_attr( $link['rel'] ),
			esc_attr( $link['title'] ),
			esc_html__( 'View detail', 'sober' ) );

		if ( 'light' == $atts['button_style'] ) {
			$button_class = 'line-hover active';
		} else {
			$button_class = 'button-type-' . $atts['button_style'] . ' button';
		}

		$button_class .= ' button-color-' . $atts['scheme'];

		return sprintf(
			'<div class="%s">
				%s
				<div class="banner-content">
					<span class="banner-text">%s</span>
					<span class="sober-button %s">%s</span>
				</div>
				%s
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			$image,
			do_shortcode( $content ),
			esc_attr( $button_class ),
			esc_html( $atts['button_text'] ),
			$link
		);
	}

	/**
	 * Banner grid 4
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function banner_grid_4( $atts, $content ) {
		$atts = shortcode_atts( array(
			'reverse'  => 'no',
			'el_class' => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array( 'sober-banner-grid-4', $atts['el_class'] );

		if ( 'yes' == $atts['reverse'] ) {
			$css_class[] = 'reverse-order';
		}

		// Reset banner counter
		self::$current_banner = 1;

		add_filter( 'sober_banner_size', array( __CLASS__, 'banner_grid_4_banner_size' ) );
		$content = do_shortcode( $content );
		remove_filter( 'sober_banner_size', array( __CLASS__, 'banner_grid_4_banner_size' ) );

		return '<div class="' . esc_attr( implode( ' ', $css_class ) ) . '">' . $content . '</div>';
	}

	/**
	 * Banner grid 5
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function banner_grid_5( $atts, $content ) {
		$atts = shortcode_atts( array(
			'el_class' => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array( 'sober-banner-grid-5', $atts['el_class'] );

		// Reset banner counter
		self::$current_banner = 1;

		add_filter( 'sober_banner_size', array( __CLASS__, 'banner_grid_5_banner_size' ) );
		$content = do_shortcode( $content );
		remove_filter( 'sober_banner_size', array( __CLASS__, 'banner_grid_5_banner_size' ) );

		return '<div class="' . esc_attr( implode( ' ', $css_class ) ) . '">' . $content . '</div>';
	}

	/**
	 * Banner grid 5 v2
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function banner_grid_5_2( $atts, $content ) {
		$atts = shortcode_atts( array(
			'el_class' => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array( 'sober-banner-grid-5v2', $atts['el_class'] );

		// Reset banner counter
		self::$current_banner = 1;

		add_filter( 'sober_banner_size', array( __CLASS__, 'banner_grid_5_2_banner_size' ) );
		$content = do_shortcode( $content );
		remove_filter( 'sober_banner_size', array( __CLASS__, 'banner_grid_5_2_banner_size' ) );

		return '<div class="' . esc_attr( implode( ' ', $css_class ) ) . '">' . $content . '</div>';
	}

	/**
	 * Banner grid 6
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function banner_grid_6( $atts, $content ) {
		$atts = shortcode_atts( array(
			'reverse'  => 'no',
			'el_class' => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array( 'sober-banner-grid-6', $atts['el_class'] );

		if ( 'yes' == $atts['reverse'] ) {
			$css_class[] = 'reverse-order';
		}

		// Reset banner counter
		self::$current_banner = 1;

		add_filter( 'sober_banner_size', array( __CLASS__, 'banner_grid_6_banner_size' ) );
		$content = do_shortcode( $content );
		remove_filter( 'sober_banner_size', array( __CLASS__, 'banner_grid_6_banner_size' ) );

		return '<div class="' . esc_attr( implode( ' ', $css_class ) ) . '">' . $content . '</div>';
	}

	/**
	 * Chart
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function chart( $atts ) {
		$atts = shortcode_atts( array(
			'value'         => 100,
			'size'          => 200,
			'thickness'     => 8,
			'label_source'  => 'auto',
			'label'         => '',
			'color'         => '#6dcff6',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-chart',
			'sober-chart-' . $atts['value'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$label = 'custom' == $atts['label_source'] ? $atts['label'] : '<span class="unit">%</span>' . esc_html( $atts['value'] );

		return sprintf(
			'<div class="%s" data-value="%s" data-size="%s" data-thickness="%s" data-fill="%s">
				<div class="text" style="color: %s">%s</div>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_attr( intval( $atts['value'] ) / 100 ),
			esc_attr( $atts['size'] ),
			esc_attr( $atts['thickness'] ),
			esc_attr( json_encode( array( 'color' => $atts['color'] ) ) ),
			esc_attr( $atts['color'] ),
			wp_kses_post( $label )
		);
	}

	/**
	 * Message Box
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function message_box( $atts, $content ) {
		$atts = shortcode_atts( array(
			'type'          => 'success',
			'closeable'     => false,
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-message-box',
			$atts['type'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( $atts['closeable'] ) {
			$css_class[] = 'closeable';
		}

		$icon = str_replace( array( 'info', 'danger' ), array( 'information', 'error' ), $atts['type'] );

		return sprintf(
			'<div class="%s">
				<svg viewBox="0 0 20 20" class="message-icon"><use xlink:href="#%s"></use></svg>
				<div class="box-content">%s</div>
				%s
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_attr( $icon ),
			$content,
			$atts['closeable'] ? '<a class="close" href="#"><svg viewBox="0 0 14 14"><use xlink:href="#close-delete-small"></use></svg></a>' : ''
		);
	}

	/**
	 * Icon Box
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function icon_box( $atts, $content ) {
		$atts = shortcode_atts( array(
			'icon_type'        => 'fontawesome',
			'icon_fontawesome' => 'fa fa-adjust',
			'icon_openiconic'  => 'vc-oi vc-oi-dial',
			'icon_typicons'    => 'typcn typcn-adjust-brightness',
			'icon_entypo'      => 'entypo-icon entypo-icon-note',
			'icon_linecons'    => 'vc_li vc_li-heart',
			'icon_monosocial'  => 'vc-mono vc-mono-fivehundredpx',
			'icon_material'    => 'vc-material vc-material-cake',
			'image'            => '',
			'image_url'        => '',
			'style'            => 'normal',
			'title'            => esc_html__( 'I am Icon Box', 'sober' ),
			'css_animation'    => '',
			'el_class'         => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-icon-box',
			'icon-type-' . $atts['icon_type'],
			'icon-style-' . $atts['style'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( 'image' == $atts['icon_type'] ) {
			$icon = '';

			if ( is_numeric( $atts['image'] ) ) {
				$image = wp_get_attachment_image_src( $atts['image'], 'full' );
				$icon  = $image ? sprintf( '<img alt="%s" src="%s">', esc_attr( $atts['title'] ), esc_url( $image[0] ) ) : '';
			} elseif ( $atts['image'] ) {
				$icon = sprintf( '<img alt="%s" src="%s">', esc_attr( strip_tags( $atts['title'] ) ), esc_url( $atts['image'] ) );
			}
		} elseif ( 'external_link' == $atts['icon_type'] ) {
			$icon = '';

			if ( $atts['image_url'] ) {
				$icon = sprintf( '<img alt="%s" src="%s">', esc_attr( $atts['title'] ), esc_url( $atts['image_url'] ) );
			}
		} else {
			vc_icon_element_fonts_enqueue( $atts['icon_type'] );
			$icon = '<i class="' . esc_attr( $atts[ 'icon_' . $atts['icon_type'] ] ) . '"></i>';
		}

		self::enqueue_font_awesome5();

		return sprintf(
			'<div class="%s">
				<div class="box-icon">%s</div>
				<h3 class="box-title">%s</h3>
				<div class="box-content">%s</div>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			$icon,
			esc_html( $atts['title'] ),
			$content
		);
	}

	/**
	 * Pricing Table
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function pricing_table( $atts ) {
		$atts = shortcode_atts( array(
			'name'          => '',
			'price'         => '',
			'currency'      => '$',
			'recurrence'    => esc_html__( 'Per Month', 'sober' ),
			'features'      => '',
			'button_text'   => esc_html__( 'Get Started', 'sober' ),
			'button_link'   => '',
			'color'         => '#6dcff6',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-pricing-table',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$features = vc_param_group_parse_atts( $atts['features'] );
		$list     = array();
		foreach ( $features as $feature ) {
			$list[] = sprintf( '<li><span class="feature-name">%s</span><span class="feature-value">%s</span></li>', $feature['name'], $feature['value'] );
		}

		$features = $list ? '<ul>' . implode( '', $list ) . '</ul>' : '';
		$link     = self::build_link( $atts['button_link'] );

		return sprintf(
			'<div class="%s" data-color="%s">
				<div class="table-header" style="background-color: %s">
					<h3 class="plan-name">%s</h3>
					<div class="pricing"><span class="currency">%s</span>%s</div>
					<div class="recurrence">%s</div>
				</div>
				<div class="table-content">%s</div>
				<div class="table-footer">
					<a href="%s" target="%s" rel="%s" title="%s" class="button" style="background-color: %s">%s</a>
				</div>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_attr( $atts['color'] ),
			esc_attr( $atts['color'] ),
			esc_html( $atts['name'] ),
			esc_html( $atts['currency'] ),
			esc_html( $atts['price'] ),
			esc_html( $atts['recurrence'] ),
			$features,
			esc_url( $link['url'] ),
			esc_attr( $link['target'] ),
			esc_attr( $link['rel'] ),
			esc_attr( $link['title'] ),
			esc_attr( $atts['color'] ),
			esc_html( $atts['button_text'] )
		);
	}

	/**
	 * Google Map
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function map( $atts, $content ) {
		$atts = shortcode_atts( array(
			'api_key'       => '',
			'marker'        => '',
			'address'       => '',
			'lat'           => '',
			'lng'           => '',
			'width'         => '100%',
			'height'        => '625px',
			'zoom'          => 15,
			'color'         => '',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		if ( empty( $atts['api_key'] ) ) {
			return esc_html__( 'Google map requires API Key in order to work.', 'sober' );
		}

		if ( empty( $atts['address'] ) && empty( $atts['lat'] ) && empty( $atts['lng'] ) ) {
			return esc_html__( 'No address', 'sober' );
		}

		if ( ! empty( $atts['address'] ) ) {
			$coordinates = self::get_coordinates( $atts['address'], $atts['api_key'] );
		} else {
			$coordinates = array(
				'lat' => $atts['lat'],
				'lng' => $atts['lng'],
			);
		}

		if ( ! empty( $coordinates['error'] ) ) {
			return $coordinates['error'];
		}

		$css_class = array(
			'sober-map',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$style = array();
		if ( $atts['width'] ) {
			$style[] = 'width: ' . $atts['width'];
		}

		if ( $atts['height'] ) {
			$style[] = 'height: ' . intval( $atts['height'] ) . 'px';
		}

		$marker = '';

		if ( $atts['marker'] ) {
			if ( filter_var( $atts['marker'], FILTER_VALIDATE_URL ) ) {
				$marker = $atts['marker'];
			} else {
				$attachment_image = wp_get_attachment_image_src( intval( $atts['marker'] ), 'full' );
				$marker           = $attachment_image ? $attachment_image[0] : '';
			}
		}

		wp_enqueue_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . $atts['api_key'] );

		return sprintf(
			'<div class="%s" style="%s" data-zoom="%s" data-lat="%s" data-lng="%s" data-color="%s" data-marker="%s">%s</div>',
			implode( ' ', $css_class ),
			implode( ';', $style ),
			absint( $atts['zoom'] ),
			esc_attr( $coordinates['lat'] ),
			esc_attr( $coordinates['lng'] ),
			esc_attr( $atts['color'] ),
			esc_attr( $marker ),
			$content
		);
	}

	/**
	 * Google Map
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function map2( $atts, $content ) {
		$atts = shortcode_atts( array(
			'address'       => '',
			'lat'           => '',
			'lng'           => '',
			'height'        => '625px',
			'zoom'          => 15,
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		if ( empty( $atts['lat'] ) && empty( $atts['lng'] ) && ! empty( $atts['address'] ) ) {
			$coordinates = self::get_coordinates( $atts['address'] );

			if ( ! empty( $coordinates['lat'] ) && ! empty( $coordinates['lng'] ) ) {
				$atts['lat'] = $coordinates['lat'];
				$atts['lng'] = $coordinates['lng'];
			}
		}

		$css_class = array(
			'sober-map-2',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$style = array();

		if ( $atts['height'] ) {
			$style[] = 'height: ' . intval( $atts['height'] ) . 'px';
		}

		$marker_icon = plugins_url( 'assets/images/marker-icon.png', dirname( dirname( __FILE__ ) ) );
		$marker_icon_shadow = plugins_url( 'assets/images/marker-shadow.png', dirname( dirname( __FILE__ ) ) );

		$data = array(
			'lat'    => $atts['lat'],
			'lng'    => $atts['lng'],
			'zoom'   => $atts['zoom'],
			'marker' => array(
				'icon'    => $marker_icon,
				'shadow'  => $marker_icon_shadow,
				'content' => $atts['address'],
			),
		);

		wp_enqueue_style( 'leaflet' );
		wp_enqueue_script( 'leaflet' );

		return sprintf(
			'<div class="%s" data-map="%s"><div id="%s" style="%s"></div></div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_attr( wp_json_encode( $data ) ),
			esc_attr( uniqid( 'sober_map_' ) ),
			implode( ';', $style )
		);
	}

	/**
	 * Testimonial
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function testimonial( $atts, $content ) {
		$atts = shortcode_atts( array(
			'image'         => '',
			'name'          => '',
			'company'       => '',
			'align'         => 'center',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-testimonial',
			'testimonial-align-' . $atts['align'],
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$image = '';

		if ( is_numeric( $atts['image'] ) ) {
			if ( function_exists( 'wpb_getImageBySize' ) ) {
				$image = wpb_getImageBySize( array(
					'attach_id'  => $atts['image'],
					'thumb_size' => '160x160',
				) );

				$image = $image['thumbnail'];
			} else {
				$image = wp_get_attachment_image_src( $atts['image'], 'large' );

				if ( $image ) {
					$image = sprintf( '<img alt="%s" src="%s" width="160" height="160">',
						esc_attr( $atts['image'] ),
						esc_url( $image[0] )
					);
				}
			}
		} elseif ( $atts['image'] ) {
			$image = '<img src="' . esc_url( $atts['image'] ) . '" alt="' . esc_attr( strip_tags( $atts['name'] ) ) . '">';
		}

		$authors = array(
			'<span class="name">' . esc_html( $atts['name'] ) . '</span>',
			'<span class="company">' . esc_html( $atts['company'] ) . '</span>',
		);

		return sprintf(
			'<div class="%s">
				<div class="author-photo">%s</div>
				<div class="testimonial-entry">
					<div class="testimonial-content">%s</div>
					<div class="testimonial-author">%s</div>
				</div>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			$image,
			$content,
			implode( ', ', $authors )
		);
	}

	/**
	 * Partners
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function partners( $atts ) {
		$atts = shortcode_atts( array(
			'source'              => 'media_library',
			'images'              => '',
			'custom_srcs'         => '',
			'image_size'          => 'full',
			'external_img_size'   => '',
			'custom_links'        => '',
			'custom_links_target' => '_self',
			'layout'              => 'bordered',
			'css_animation'       => '',
			'el_class'            => '',
		), $atts, 'sober_' . __FUNCTION__ );

		if ( ! function_exists( 'vc_value_from_safe' ) ) {
			return '';
		}

		$css_class     = array(
			'sober-partners',
			$atts['layout'] . '-layout',
			$atts['el_class'],
		);
		$css_animation = self::get_css_animation( $atts['css_animation'] );
		$images        = $logos = array();
		$custom_links  = explode( ',', vc_value_from_safe( $atts['custom_links'] ) );
		$default_src   = vc_asset_url( 'vc/no_image.png' );

		switch ( $atts['source'] ) {
			case 'media_library':
				$images = explode( ',', $atts['images'] );
				break;

			case 'external_link':
				$images = vc_value_from_safe( $atts['custom_srcs'] );
				$images = explode( ',', $images );

				break;
		}

		foreach ( $images as $i => $image ) {
			$thumbnail = '';

			switch ( $atts['source'] ) {
				case 'media_library':
					if ( $image > 0 ) {
						$img       = wpb_getImageBySize( array(
							'attach_id'  => $image,
							'thumb_size' => $atts['image_size'],
						) );
						$thumbnail = $img['thumbnail'];
					} else {
						$thumbnail = '<img src="' . $default_src . '" />';
					}
					break;

				case 'external_link':
					$image      = esc_attr( $image );
					$dimensions = vc_extract_dimensions( $atts['external_img_size'] );
					$hwstring   = $dimensions ? image_hwstring( $dimensions[0], $dimensions[1] ) : '';
					$thumbnail  = '<img ' . $hwstring . ' src="' . $image . '" />';
					break;
			}

			if ( empty( $custom_links[ $i ] ) ) {
				$logo = '<span class="partner-logo">' . $thumbnail . '</span>';
			} else {
				$logo = sprintf( '<a href="%s" target="%s" class="partner-logo">%s</a>', esc_url( $custom_links[ $i ] ), esc_attr( $atts['custom_links_target'] ), $thumbnail );
			}

			$logos[] = '<div class="partner' . esc_attr( $css_animation ) . '">' . $logo . '</div>';
		}

		return sprintf( '<div class="%s">%s</div>', esc_attr( implode( ' ', $css_class ) ), implode( ' ', $logos ) );
	}

	/**
	 * Contact Box
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function contact_box( $atts ) {
		$atts = shortcode_atts( array(
			'address'       => '',
			'phone'         => '',
			'fax'           => '',
			'email'         => '',
			'website'       => '',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-contact-box',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);
		$contact   = array();

		foreach ( array( 'address', 'phone', 'fax', 'email', 'website' ) as $info ) {
			if ( empty( $atts[ $info ] ) ) {
				continue;
			}

			$icon   = $name = '';
			$detail = esc_html( $atts[ $info ] );
			switch ( $info ) {
				case 'address':
					$name = esc_html__( 'Address', 'sober' );
					$icon = '<svg width="20" height="20" class="info-icon"><use xlink:href="#home"></use></svg>';
					break;

				case 'phone':
					$name   = esc_html__( 'Phone', 'sober' );
					$icon   = '<svg width="20" height="20" class="info-icon"><use xlink:href="#phone"></use></svg>';
					$detail = '<a href="tel:' . esc_attr( $atts[ $info ] ) . '">' . $detail . '</a>';
					break;

				case 'fax':
					$name = esc_html__( 'Fax', 'sober' );
					$icon = '<i class="info-icon fa fa-fax"></i>';
					break;

				case 'email':
					$name   = esc_html__( 'Email', 'sober' );
					$icon   = '<svg width="20" height="20" class="info-icon"><use xlink:href="#mail"></use></svg>';
					$detail = '<a href="mailto:' . esc_attr( $atts[ $info ] ) . '">' . $detail . '</a>';
					break;

				case 'website':
					$name   = esc_html__( 'Website', 'sober' );
					$icon   = '<i class="info-icon fa fa-globe"></i>';
					$detail = '<a href="' . esc_url( $atts[ $info ] ) . '" target="_blank" rel="nofollow">' . $detail . '</a>';
					break;
			}

			$contact[] = sprintf(
				'<div class="contact-info">
					%s
					<span class="info-name">%s</span>
					<span class="info-value">%s</span>
				</div>',
				$icon,
				$name,
				$detail
			);
		}

		return sprintf( '<div class="%s">%s</div>', esc_attr( implode( ' ', $css_class ) ), implode( ' ', $contact ) );
	}

	/**
	 * Info List
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function info_list( $atts ) {
		$atts = shortcode_atts( array(
			'info' => urlencode( json_encode( array(
				array(
					'icon' => 'fa fa-home',
					'label' => esc_html__( 'Address', 'sober' ),
					'value' => '9606 North MoPac Expressway',
				),
				array(
					'icon' => 'fa fa-phone',
					'label' => esc_html__( 'Phone', 'sober' ),
					'value' => '+1 248-785-8545',
				),
				array(
					'icon' => 'fa fa-fax',
					'label' => esc_html__( 'Fax', 'sober' ),
					'value' => '123123123',
				),
				array(
					'icon' => 'fa fa-envelope',
					'label' => esc_html__( 'Email', 'sober' ),
					'value' => 'sober@uix.store',
				),
				array(
					'icon' => 'fa fa-globe',
					'label' => esc_html__( 'Website', 'sober' ),
					'value' => 'http://uix.store',
				),
			) ) ),
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		if ( function_exists( 'vc_param_group_parse_atts' ) ) {
			$info = (array) vc_param_group_parse_atts( $atts['info'] );
		} else {
			$info = json_decode( urldecode( $atts['info'] ), true );
		}

		$css_class = array(
			'sober-info-list',
			$atts['el_class'],
		);

		$animation = self::get_css_animation( $atts['css_animation'] );

		$list = array();
		foreach ( $info as $item ) {
			$list[] = sprintf(
				'<li class="%s">
					<i class="info-icon %s"></i>
					<span class="info-name">%s</span>
					<span class="info-value">%s</span>
				</li>',
				$animation,
				$item['icon'],
				$item['label'],
				$item['value']
			);
		}

		if ( ! $list ) {
			return '';
		}

		self::enqueue_font_awesome5();

		return sprintf( '<div class="%s"><ul>%s</ul></div>', esc_attr( implode( ' ', $css_class ) ), implode( '', $list ) );
	}

	/**
	 * FAQ
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function faq( $atts, $content ) {
		$atts = shortcode_atts( array(
			'title'         => esc_html__( 'Question content goes here', 'sober' ),
			'open'          => 'false',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-faq',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( 'true' == $atts['open'] ) {
			$css_class[] = 'open';
		}

		return sprintf(
			'<div class="%s">
				<div class="question">
					<span class="question-label">%s</span>
					<span class="question-icon"><span class="toggle-icon"></span></span>
					<span class="question-title">%s</span>
				</div>
				<div class="answer"><span class="answer-label">%s</span>%s</div>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_html__( 'Question', 'sober' ),
			esc_html( $atts['title'] ),
			esc_html__( 'Answer', 'sober' ),
			$content
		);
	}

	/**
	 * Team member
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function team_member( $atts ) {
		$atts = shortcode_atts( array(
			'image'         => '',
			'image_size'    => 'full',
			'name'          => '',
			'job'           => '',
			'email'         => '',
			'facebook'      => '',
			'twitter'       => '',
			'pinterest'     => '',
			'linkedin'      => '',
			'youtube'       => '',
			'instagram'     => '',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-team-member',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( is_numeric( $atts['image'] ) ) {
			if ( function_exists( 'wpb_getImageBySize' ) ) {
				$image = wpb_getImageBySize( array(
					'attach_id'  => $atts['image'],
					'thumb_size' => $atts['image_size'],
				) );

				$image = $image['thumbnail'];
			} else {
				$image = wp_get_attachment_image_src( $atts['image'], $atts['image_size'] );

				if ( $image ) {
					$image = sprintf( '<img src="%s" alt="%s" width="%s" height="%s">',
						esc_url( $image[0] ),
						esc_attr( $atts['name'] ),
						esc_attr( $image[1] ),
						esc_attr( $image[2] )
					);
				}
			}
		} elseif ( $atts['image'] ) {
			$image = '<img src="' . esc_url( $atts['image'] ) . '" alt="' . esc_attr( strip_tags( $atts['name'] ) ) . '">';
		} else {
			$image = plugins_url( 'assets/images/man-placeholder.png', dirname( dirname( __FILE__ ) ) );
			$image = sprintf( '<img src="%s" alt="%s" width="360" height="430">',
				esc_url( $image ),
				esc_attr( $atts['name'] )
			);
		}

		$socials = array( 'email', 'facebook', 'twitter', 'pinterest', 'linkedin', 'youtube', 'instagram' );
		$links   = array();

		foreach ( $socials as $social ) {
			if ( empty( $atts[ $social ] ) ) {
				continue;
			}

			$url = 'email' == $social ? 'mailto:' . $atts[ $social ] : $atts[ $social ];

			$icon = str_replace(
				array( 'email', 'pinterest', 'youtube' ),
				array( 'envelope-o', 'pinterest-p', 'youtube-play' ),
				$social
			);

			$links[] = sprintf( '<a href="%s" target="_blank"><i class="fa fa-%s"></i></a>', esc_url( $url ), esc_attr( $icon ) );
		}

		return sprintf(
			'<div class="%s">
				%s
				<div class="member-socials">%s</div>
				<div class="member-info">
					<h4 class="member-name">%s</h4>
					<span class="member-job">%s</span>
				</div>
			</div>',
			esc_attr( implode( ' ', $css_class ) ),
			$image,
			implode( '', $links ),
			esc_html( $atts['name'] ),
			esc_html( $atts['job'] )
		);
	}

	/**
	 * WooComemrce Currency Switcher shortcode.
	 *
	 * @param  array $atts
	 * @return string
	 */
	public static function woocs( $atts ) {
		if ( ! function_exists( 'sober_currency_switcher' ) ) {
			return '';
		}

		ob_start();

		sober_currency_switcher();

		return ob_get_clean();
	}

	/**
	 * WPML language switcher shortcode.
	 *
	 * @param  array $atts
	 * @return string
	 */
	public static function wpml( $atts ) {
		if ( ! function_exists( 'sober_language_switcher' ) ) {
			return '';
		}

		ob_start();

		sober_language_switcher();

		return ob_get_clean();
	}

	/**
	 * Subscribe box
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function subscribe_box( $atts, $content ) {
		$atts = shortcode_atts( array(
			'title'         => '',
			'form_id'       => '',
			'form_style'    => 'default',
			'css_animation' => '',
			'el_class'      => '',
			'css'           => '',
		), $atts, 'sober_' . __FUNCTION__ );

		if ( ! $atts['form_id'] ) {
			return '';
		}

		$css_class = array(
			'sober-subscribe-box',
			'sober-subscribe-box--' . $atts['form_style'],
			self::get_css_animation($atts['css_animation']),
			$atts['el_class'],
		);

		if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
			$css_class[] = vc_shortcode_custom_css_class( $atts['css'] );
		}

		$title = empty( $atts['title'] ) ? '' : '<h3 class="sober-subscribe-box__title">' . esc_html( $atts['title'] ) . '</h3>';
		$content = function_exists('wpb_js_remove_wpautop' ) ? wpb_js_remove_wpautop( $content ) : do_shortcode( $content );
		$content = $content ? '<div class="sober-subscribe-box__desc">' . $content . '</div>' : '';

		$intro = ! empty( $title ) || ! empty( $content ) ? '<div class="sober-subscribe-box__content">' . $title . $content . '</div>' : '';
		$form = '<div class="sober-subscribe-box__form">' . do_shortcode( '[mc4wp_form id="' . $atts['form_id'] . '"]' ) . '</div>';

		return sprintf(
			'<div class="%s">%s%s</div>',
			esc_attr( implode( ' ', $css_class ) ),
			$intro,
			$form
		);
	}

	/**
	 * Empty space
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function empty_space( $atts ) {
		$atts = shortcode_atts( array(
			'height' => '32px',
			'height_xs' => '',
			'height_md' => '',
			'height_lg' => '',
			'hidden_xs' => '',
			'hidden_md' => '',
			'hidden_lg' => '',
			'el_class' => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-empty-space',
			$atts['el_class'],
		);

		if ( $atts['hidden_xs'] ) {
			$css_class[] = 'hidden-xs';
		}

		if ( $atts['hidden_md'] ) {
			$css_class[] = 'hidden-md';
		}

		if ( $atts['hidden_lg'] ) {
			$css_class[] = 'hidden-lg';
		}

		if ( empty( $atts['height_xs'] ) && empty( $atts['height_md'] ) && empty( $atts['height_lg'] ) ) {
			return sprintf(
				'<div class="%s" style="height: %s"></div>',
				esc_attr( implode( ' ', $css_class ) ),
				esc_attr( $atts['height'] )
			);
		}

		$height = trim( $atts['height'] );
		$height = is_numeric( $height ) ? $height . 'px' : $height;
		$height_xs = empty( $atts['height_xs'] ) ? $height : ( is_numeric( $atts['height_xs'] ) ?  $atts['height_xs'] . 'px' :  $atts['height_xs'] );
		$height_md = empty( $atts['height_md'] ) ? $height : ( is_numeric( $atts['height_md'] ) ?  $atts['height_md'] . 'px' :  $atts['height_md'] );
		$height_lg = empty( $atts['height_lg'] ) ? $height : ( is_numeric( $atts['height_lg'] ) ?  $atts['height_lg'] . 'px' :  $atts['height_lg'] );

		return
			'<div class="' . esc_attr( implode( ' ', $css_class ) ) . '" aria-hidden="true">' .
				'<div class="sober-empty-space__xs visible-xs" style="height:' . esc_attr( $height_xs ) . '"></div>' .
				'<div class="sober-empty-space__sm visible-sm" style="height:' . esc_attr( $height ) . '"></div>' .
				'<div class="sober-empty-space__md visible-md" style="height:' . esc_attr( $height_md ) . '"></div>' .
				'<div class="sober-empty-space__lg visible-lg" style="height:' . esc_attr( $height_lg ) . '"></div>' .
			'</div>';
	}

	/**
	 * Simple banner
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function banner_simple( $atts ) {
		$atts = shortcode_atts( array(
			'image_source'  => 'media_library', // media_library, external_link
			'image'         => '',
			'image_size'    => 'full',
			'image_url'     => '',
			'text'          => '',
			'link'          => '',
			'button_text'   => '',
			'alignment'     => 'center',
			'css_animation' => '',
			'el_class'      => '',
			'css'           => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-banner-simple',
			'sober-banner-simple--' . $atts['alignment'],
			self::get_css_animation($atts['css_animation']),
			$atts['el_class'],
		);

		if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
			$css_class[] = vc_shortcode_custom_css_class( $atts['css'] );
		}

		if ( ! empty( $atts['button_text'] ) ) {
			$css_class[] = 'sober-banner-simple--has-button';
		}

		$link  = self::build_link( $atts['link'] );
		$image = '';
		$link_open = $link_close = '';

		if ( $link['url'] ) {
			$link_open = sprintf(
				'<a href="%s" rel="%s" target="%s">',
				esc_url( $link['url'] ),
				esc_attr( $link['rel'] ),
				esc_attr( $link['target'] )
			);

			$link_close = '</a>';
		}

		if ( 'external_link' == $atts['image_source'] ) {
			$image = $atts['image_url'] ? $atts['image_url'] : '';

			if ( $image ) {
				$image = sprintf( '<img alt="%s" src="%s">',
					esc_attr( $atts['text'] ),
					esc_url( $image )
				);
			}
		} elseif ( $atts['image'] ) {
			if ( function_exists( 'wpb_getImageBySize' ) ) {
				$image = wpb_getImageBySize( array(
					'attach_id'  => $atts['image'],
					'thumb_size' => $atts['image_size'],
				) );

				$image = $image['thumbnail'];
			} else {
				$size_array = explode( 'x', $atts['image_size'] );
				$size       = count( $size_array ) == 1 ? $atts['imate_size'] : $size_array;

				$image = wp_get_attachment_image_src( $atts['image'], $size );

				if ( $image ) {
					$image = sprintf( '<img alt="%s" src="%s">',
						esc_attr( $atts['text'] ),
						esc_url( $image[0] )
					);
				}
			}
		}

		if ( $image ) {
			$image = $link_open . $image . $link_close;
		}

		$text  = empty( $atts['text'] ) ? '': '<h3 class="sober-banner-simple__text">' . $link_open . esc_html( $atts['text'] ) . $link_close . '</h3>';

		$button_text = empty( $atts['button_text'] ) ? '' : '<p class="sober-banner-simple__button">' . $link_open . esc_html( $atts['button_text'] ) . $link_close . '</p>';

		return sprintf(
			'<div class="%s">%s%s%s</div>',
			esc_attr( implode( ' ', $css_class ) ),
			$image,
			$text,
			$button_text
		);
	}

	/**
	 * Collection carousel
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function collection_carousel( $atts ) {
		$atts = shortcode_atts( array(
			'collections'   => '',
			'freemode'      => '',
			'autoplay'      => '5000',
			'navigation'    => 'arrows',
			'loop'          => true,
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-collection-carousel',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		if ( $atts['freemode'] ) {
			$css_class[] = 'sober-collection-carousel--free-mode';
		}

		if ( function_exists( 'vc_param_group_parse_atts' ) ) {
			$collections = (array) vc_param_group_parse_atts( $atts['collections'] );
		} else {
			$collections = json_decode( urldecode( $atts['collections'] ), true );
		}

		$list = array();
		foreach ( $collections as $collection ) {
			if ( ! $collection['image'] ) {
				continue;
			}

			$collection_title = isset( $collection['title'] ) ? $collection['title'] : '';

			if ( ! is_numeric( $collection['image'] ) ) {
				$image = '<img src="' . esc_url( $collection['image'] ) . '" alt="' . esc_attr( strip_tags( $collection_title ) ) . '" class="skip-lazy">';
			} elseif ( function_exists( 'wpb_getImageBySize' ) ) {
				$image = wpb_getImageBySize( array(
					'attach_id'  => $collection['image'],
					'thumb_size' => $collection['image_size'],
					'class'      => 'skip-lazy'
				) );

				$image = $image['thumbnail'];
			} else {
				$size_array = explode( 'x', $collection['image_size'] );
				$size       = count( $size_array ) == 1 ? $collection['image_size'] : $size_array;

				$image = wp_get_attachment_image_src( $collection['image'], $size );

				if ( $image ) {
					$image = sprintf( '<img alt="%s" src="%s" class="skip-lazy">',
						esc_attr( $collection_title ),
						esc_url( $image[0] )
					);
				}
			}

			$title = ! empty( $collection['title'] ) ? '<h3 class="sober-collection-carousel__item-title">' . esc_html( $collection['title'] ) . '</h3>' : '';
			$button = ! empty( $collection['button_text'] ) ? '<span class="sober-collection-carousel__item-button">' . esc_html( $collection['button_text'] ) . '</span>' : '';
			$href = ! empty( $collection['url'] ) ? 'href="' . esc_url( $collection['url'] ) . '"' : '';

			$list[] = '<div class="sober-collection-carousel__item"><a ' . $href . '>' . $image . $title . $button . '</a></div>';
		}

		if ( ! $list ) {
			return '';
		}

		$options = array(
			'autoplay' => intval( $atts['autoplay'] ),
			'freemode' => !! $atts['freemode'],
			'loop' => $atts['loop'],
			'navigation' => $atts['navigation'],
		);

		return sprintf(
			'<div class="%s" data-options="%s">%s</div>',
			esc_attr( implode( ' ', $css_class ) ),
			esc_attr( json_encode( $options ) ),
			implode( '', $list )
		);
	}

	/**
	 * Portfolio Grid
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function portfolio_grid( $atts ) {
		$atts = shortcode_atts( array(
			'per_page'      => 9,
			'filter'        => 'yes',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-portfolio',
			'sober-portfolio--grid',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$filter_html = '';

		if ( function_exists( 'sober_portfolio_filter' ) && $atts['filter'] ) {
			$filter_html = sober_portfolio_filter( 'class=sober-portfolio__filter&echo=0' );
		}

		return sprintf(
			'<div class="%s">%s<div class="sober-portfolio__row portfolio-items portfolio-classic row">%s</div></div>',
			esc_attr( trim( implode( ' ', $css_class ) ) ),
			$filter_html,
			implode( "\n", self::get_portfolio( $atts, 'grid' ) )
		);
	}

	/**
	 * Portfolio Masonry
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function portfolio_masonry( $atts ) {
		$atts = shortcode_atts( array(
			'per_page'      => 8,
			'filter'        => 'yes',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-portfolio',
			'sober-portfolio--masonry',
			'portfolio-masonry',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$filter_html = '';

		if ( function_exists( 'sober_portfolio_filter' ) && $atts['filter'] ) {
			$filter_html = sober_portfolio_filter( 'class=sober-portfolio__filter&echo=0' );
		}

		return sprintf(
			'<div class="%s">%s<div class="sober-portfolio__row portfolio-items portfolio-masonry row">%s</div></div>',
			esc_attr( trim( implode( ' ', $css_class ) ) ),
			$filter_html,
			implode( "\n", self::get_portfolio( $atts, 'masonry' ) )
		);
	}

	/**
	 * Portfolio Metro
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function portfolio_metro( $atts ) {
		$atts = shortcode_atts( array(
			'per_page'      => 8,
			'filter'        => 'yes',
			'css_animation' => '',
			'el_class'      => '',
		), $atts, 'sober_' . __FUNCTION__ );

		$css_class = array(
			'sober-portfolio',
			'sober-portfolio--metro',
			'portfolio-fullwidth',
			self::get_css_animation( $atts['css_animation'] ),
			$atts['el_class'],
		);

		$filter_html = '';

		if ( function_exists( 'sober_portfolio_filter' ) && $atts['filter'] ) {
			$filter_html = sober_portfolio_filter( 'class=sober-portfolio__filter&echo=0' );
		}

		return sprintf(
			'<div class="%s">%s<div class="sober-portfolio__row portfolio-items portfolio-fullwidth row">%s</div></div>',
			esc_attr( trim( implode( ' ', $css_class ) ) ),
			$filter_html,
			implode( "\n", self::get_portfolio( $atts, 'metro' ) )
		);
	}

	/**
	 * Social menu
	 *
	 * @param  array $atts
	 *
	 * @return string
	 */
	public static function social_menu( $atts ) {
		$html = '';

		if ( has_nav_menu( 'socials' ) ) {
			$html = wp_nav_menu( array(
				'theme_location' => 'socials',
				'container'      => '',
				'menu_class'     => 'socials-menu social-menu-shortcode',
				'menu_id'        => '',
				'depth'          => 1,
				'echo'           => false,
			) );
		}

		return $html;
	}

	/**
	 * Get portfolio HTML
	 *
	 * @param array $atts
	 * @param string $portfolio_style
	 * @return array
	 */
	public static function get_portfolio( $atts, $portfolio_style = 'grid' ) {
		$output = array();

		$args = array(
			'post_type'              => 'portfolio',
			'posts_per_page'         => $atts['per_page'],
			'ignore_sticky_posts'    => 1,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
		);

		$posts = new WP_Query( $args );

		if ( ! $posts->have_posts() ) {
			return $output;
		}

		$index = 1;
		while ( $posts->have_posts() ) : $posts->the_post();
			$image_size = 'sober-portfolio';
			$classes = 'col-xs-6 col-sm-4 col-md-4';

			if ( 'masonry' == $portfolio_style ) {
				$current_project_mod = $index % 6;
				$image_size = 'sober-portfolio-large';
				$classes = 'col-sm-6 col-md-6';

				if ( in_array( $current_project_mod, array( 2, 4 ) ) ) {
					$image_size = 'sober-portfolio-wide';
				}
			} elseif ( 'metro' == $portfolio_style ) {
				$current_project_mod = $index % 8;
				$image_size = 'sober-portfolio';
				$classes = 'col-md-6';

				if ( in_array( $current_project_mod, array( 1, 6 ) ) ) {
					$image_size = 'sober-portfolio-large';
				} elseif ( in_array( $current_project_mod, array( 2, 5 ) ) ) {
					$image_size = 'sober-portfolio-wide';
				}

				if ( in_array( $current_project_mod, array( 3, 4, 7, 0 ) ) ) {
					$classes = 'col-md-3';
				}

				$classes .= ' col-sm-6';
			}

			$classes .= ' portfolio';

			$post_class = get_post_class( $classes );
			$term_html = $thumbnail = '';

			if ( has_post_thumbnail() ) {
				$thumbnail = sprintf(
					'<a href="%s" class="project-thumbnail">
						%s
						<span class="view-more">
							<svg viewBox="0 0 20 20">
								<use xlink:href="#right-arrow"></use>
							</svg>
						</span>
					</a>',
					esc_url( get_permalink() ),
					get_the_post_thumbnail( get_the_ID(), $image_size )
				);
			}

			if ( has_term( '', 'portfolio_type' ) ) {
				$term_html = get_the_term_list( get_the_ID(), 'portfolio_type', '<div class="portfolio-type project-type">' , ', ', '</div>' );
			}

			$output[] = sprintf(
				'<div class="%s">
					%s
					<div class="project-summary">
						<h3 class="project-title"><a href="%s">%s</a></h3>
						%s
					</div>
				</div>',
				esc_attr( implode( ' ', $post_class ) ),
				$thumbnail,
				esc_url( get_permalink() ),
				get_the_title(),
				$term_html
			);

			$index++;
		endwhile;

		wp_reset_postdata();

		return $output;
	}

	/**
	 * Get coordinates
	 *
	 * @param string $address
	 * @param bool   $refresh
	 *
	 * @return array
	 */
	public static function get_coordinates( $address, $key = '', $refresh = false ) {
		$address_hash = md5( $address );
		$coordinates  = get_transient( $address_hash );
		$results      = array( 'lat' => '', 'lng' => '' );

		if ( $refresh || $coordinates === false ) {
			if ( $key ) {
				$results = self::get_coordinates_from_google( $address, $key );
			}

			// Try again with OpenStreetMap data.
			if ( empty( $results['lat'] ) || empty( $results['lng'] ) || isset( $results['error'] ) ) {
				$results = self::get_coordinates_from_openstreetmap( $address );
			}

			if ( ! empty( $results['lat'] ) || ! empty( $results['lng'] ) ) {
				set_transient( $address_hash, $results );
			}
		} else {
			$results = $coordinates; // return cached results
		}

		return $results;
	}

	/**
	 * Get coordinates from Google
	 *
	 * @param string $address
	 * @param strin $key
	 * @return array
	 */
	public static function get_coordinates_from_google( $address, $key ) {
		$args     = array( 'address' => urlencode( $address ), 'sensor' => 'false', 'key' => $key );
		$url      = add_query_arg( $args, 'https://maps.googleapis.com/maps/api/geocode/json' );
		$response = wp_remote_get( $url );
		$results  = array();

		if ( is_wp_error( $response ) ) {
			$results['error'] = esc_html__( 'Can not connect to Google Maps APIs', 'sober' );

			return $results;
		}

		$data = wp_remote_retrieve_body( $response );

		if ( is_wp_error( $data ) ) {
			$results['error'] = esc_html__( 'Can not connect to Google Maps APIs', 'sober' );

			return $results;
		}

		if ( $response['response']['code'] == 200 ) {
			/**
			 * @var object
			 */
			$data = json_decode( $data );

			if ( $data->status === 'OK' ) {
				$coordinates = $data->results[0]->geometry->location;

				$results['lat']     = $coordinates->lat;
				$results['lng']     = $coordinates->lng;
				$results['address'] = (string) $data->results[0]->formatted_address;
			} elseif ( $data->status === 'ZERO_RESULTS' ) {
				$results['error'] = esc_html__( 'No location found for the entered address.', 'sober' );
			} elseif ( $data->status === 'INVALID_REQUEST' ) {
				$results['error'] = esc_html__( 'Invalid request. Did you enter an address?', 'sober' );
			} else {
				$results['error'] = $data->error_message;
			}
		} else {
			$results['error'] = esc_html__( 'Unable to contact Google API service.', 'sober' );
		}

		return $results;
	}

	/**
	 * Get coordinates from OpenStreetMap
	 *
	 * @param string $address
	 * @return array
	 */
	public static function get_coordinates_from_openstreetmap( $address ) {
		$url = 'https://nominatim.openstreetmap.org/search?q=' . urlencode( $address ) . '&format=json&limit=1';
		$response = wp_remote_get( $url );
		$results  = array();

		if ( is_wp_error( $response ) ) {
			$results['error'] = esc_html__( 'Can not get the coordinates data', 'sober' );

			return $results;
		}

		$data = wp_remote_retrieve_body( $response );

		if ( is_wp_error( $data ) ) {
			$results['error'] = esc_html__( 'Can not get the coordinates data', 'sober' );

			return $results;
		}

		$data = json_decode( $data );

		if ( empty( $data ) ) {
			$results['error'] = esc_html__( 'Can not get the coordinates data', 'sober' );
		} else {
			$coordinates    = $data[0];
			$results['lat'] = $coordinates->lat;
			$results['lng'] = $coordinates->lon;
		}

		return $results;
	}

	/**
	 * Loop over found products.
	 *
	 * @param  array  $atts
	 * @param  string $loop_name
	 *
	 * @return string
	 * @internal param array $columns
	 */
	protected static function product_loop( $atts, $loop_name = 'sober_product_grid' ) {
		global $woocommerce_loop;

		$query_args = self::get_query( $atts );

		if ( isset( $atts['type'] ) && 'top_rated' == $atts['type'] ) {
			add_filter( 'posts_clauses', array( 'WC_Shortcodes', 'order_by_rating_post_clauses' ) );
		} elseif ( isset( $atts['type'] ) && 'best_sellers' == $atts['type'] ) {
			add_filter( 'posts_clauses', array( __CLASS__, 'order_by_popularity_post_clauses' ) );
		}

		$products = new WP_Query( $query_args );

		if ( isset( $atts['type'] ) && 'top_rated' == $atts['type'] ) {
			remove_filter( 'posts_clauses', array( 'WC_Shortcodes', 'order_by_rating_post_clauses' ) );
		} elseif ( isset( $atts['type'] ) && 'best_sellers' == $atts['type'] ) {
			remove_filter( 'posts_clauses', array( __CLASS__, 'order_by_popularity_post_clauses' ) );
		}

		$woocommerce_loop['name'] = $loop_name;
		$columns                  = isset( $atts['columns'] ) ? absint( $atts['columns'] ) : null;

		if ( $columns ) {
			$woocommerce_loop['columns'] = $columns;
		}

		ob_start();

		if ( $products->have_posts() ) {
			woocommerce_product_loop_start();

			while ( $products->have_posts() ) : $products->the_post();
				wc_get_template_part( 'content', 'product' );
			endwhile; // end of the loop.

			woocommerce_product_loop_end();
		}

		$return = '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';

		if ( isset( $atts['load_more'] ) && $atts['load_more'] && $products->max_num_pages > 1 ) {
			$paged = max( 1, $products->get( 'paged' ) );
			$type  = isset( $atts['type'] ) ? $atts['type'] : 'recent';

			if ( $paged < $products->max_num_pages ) {
				$button = sprintf(
					'<div class="load-more text-center">
						<a href="#" class="button ajax-load-products" data-page="%s" data-columns="%s" data-per_page="%s" data-type="%s" data-category="%s" data-nonce="%s" rel="nofollow">
							<span class="button-text">%s</span>
							<span class="loading-icon">
								<span class="bubble"><span class="dot"></span></span>
								<span class="bubble"><span class="dot"></span></span>
								<span class="bubble"><span class="dot"></span></span>
							</span>
						</a>
					</div>',
					esc_attr( $paged + 1 ),
					esc_attr( $columns ),
					esc_attr( $query_args['posts_per_page'] ),
					esc_attr( $type ),
					isset( $atts['category'] ) ? esc_attr( $atts['category'] ) : '',
					esc_attr( wp_create_nonce( 'sober_get_products' ) ),
					esc_html__( 'Load More', 'sober' )
				);

				$return .= $button;
			}
		}

		woocommerce_reset_loop();
		wp_reset_postdata();

		return $return;
	}

	/**
	 * Build query args from shortcode attributes
	 *
	 * @param array $atts
	 *
	 * @return array
	 */
	private static function get_query( $atts ) {
		$args = array(
			'post_type'              => 'product',
			'post_status'            => 'publish',
			'orderby'                => get_option( 'woocommerce_default_catalog_orderby' ),
			'order'                  => 'DESC',
			'ignore_sticky_posts'    => 1,
			'posts_per_page'         => $atts['per_page'],
			'meta_query'             => WC()->query->get_meta_query(),
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
		);

		if( version_compare( WC()->version, '3.0.0', '>=' ) ) {
			$args['tax_query'] = WC()->query->get_tax_query();
		}

		// Ordering
		if ( 'menu_order' == $args['orderby'] || 'price' == $args['orderby'] ) {
			$args['order'] = 'ASC';
		}

		if ( 'price-desc' == $args['orderby'] ) {
			$args['orderby'] = 'price';
		}

		if ( method_exists( WC()->query, 'get_catalog_ordering_args' ) ) {
			$ordering_args   = WC()->query->get_catalog_ordering_args( $args['orderby'], $args['order'] );
			$args['orderby'] = $ordering_args['orderby'];
			$args['order']   = $ordering_args['order'];

			if ( $ordering_args['meta_key'] ) {
				$args['meta_key'] = $ordering_args['meta_key'];
			}
		}

		// Improve performance
		if ( ! isset( $atts['load_more'] ) || ! $atts['load_more'] ) {
			$args['no_found_rows'] = true;
		}

		if ( ! empty( $atts['category'] ) ) {
			$args['product_cat'] = $atts['category'];
			unset( $args['update_post_term_cache'] );
		}

		if ( ! empty( $atts['page'] ) ) {
			$args['paged'] = absint( $atts['page'] );
		}

		if ( isset( $atts['type'] ) ) {
			switch ( $atts['type'] ) {
				case 'featured':
					if( version_compare( WC()->version, '3.0.0', '<' ) ) {
						$args['meta_query'][] = array(
							'key'   => '_featured',
							'value' => 'yes',
						);
					} else {
						$args['tax_query'][] = array(
							'taxonomy' => 'product_visibility',
							'field'    => 'name',
							'terms'    => 'featured',
							'operator' => 'IN',
						);
					}

					unset( $args['update_post_meta_cache'] );
					break;

				case 'sale':
					$args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
					break;

				case 'best_sellers':
					$args['meta_key'] = 'total_sales';
					$args['orderby']  = 'meta_value_num';
					$args['order']    = 'DESC';
					unset( $args['update_post_meta_cache'] );

					add_filter( 'posts_clauses', array( __CLASS__, 'order_by_popularity_post_clauses' ) );
					break;

				case 'new':
					if ( function_exists( 'sober_get_new_product_ids' ) ) {
						$args['post__in'] = array_merge( array( 0 ), sober_get_new_product_ids() );
					} else {
						$newness = intval( sober_get_option( 'product_newness' ) );

						if ( $newness > 0 ) {
							$args['date_query'] = array(
								'after' => date( 'Y-m-d', strtotime( '-' . $newness . ' days' ) )
							);
						} else {
							$args['meta_query'][] = array(
								'key'   => '_is_new',
								'value' => 'yes',
							);
						}
					}
					break;

				case 'top_rated':
					unset( $args['product_cat'] );
					$args          = self::_maybe_add_category_args( $args, $atts['category'] );
					$args['order'] = 'DESC';
					break;

				case 'recent':
					$args['orderby'] = 'date ID';
					$args['order']   = 'DESC';
					break;
			}
		}

		return $args;
	}

	/**
	 * Adds a tax_query index to the query to filter by category.
	 *
	 * @param array $args
	 * @param string $category
	 *
	 * @return array;
	 */
	protected static function _maybe_add_category_args( $args, $category ) {
		if ( ! empty( $category ) ) {
			if ( empty( $args['tax_query'] ) ) {
				$args['tax_query'] = array();
			}
			$args['tax_query'][] = array(
				array(
					'taxonomy' => 'product_cat',
					'terms'    => array_map( 'sanitize_title', explode( ',', $category ) ),
					'field'    => 'slug',
					'operator' => 'IN',
				),
			);
		}

		return $args;
	}

	/**
	 * WP Core doens't let us change the sort direction for invidual orderby params - https://core.trac.wordpress.org/ticket/17065.
	 *
	 * This lets us sort by meta value desc, and have a second orderby param.
	 *
	 * @access public
	 * @param array $args
	 * @return array
	 */
	public static function order_by_popularity_post_clauses( $args ) {
		global $wpdb;
		$args['orderby'] = "$wpdb->postmeta.meta_value+0 DESC, $wpdb->posts.post_date DESC";
		return $args;
	}

	/**
	 * Change banner size while it is inside a banner grid 4
	 *
	 * @param string $size
	 *
	 * @return string
	 */
	public static function banner_grid_4_banner_size( $size ) {
		switch ( self::$current_banner % 8 ) {
			case 1:
			case 7:
				$size = '920x820';
				break;

			case 2:
			case 3:
			case 5:
			case 6:
				$size = '460x410';
				break;

			case 0:
			case 4:
				$size = '920x410';
				break;
		}

		self::$current_banner ++;

		return $size;
	}

	/**
	 * Change banner size while it is inside a banner grid 5
	 *
	 * @param string $size
	 *
	 * @return string
	 */
	public static function banner_grid_5_banner_size( $size ) {
		switch ( self::$current_banner % 5 ) {
			case 1:
			case 0:
				$size = '520x400';
				break;

			case 3:
				$size = '750x920';
				break;

			case 2:
			case 4:
				$size = '520x500';
				break;
		}

		self::$current_banner ++;

		return $size;
	}

	/**
	 * Change banner size while it is inside a banner grid 5 v2
	 *
	 * @param string $size
	 *
	 * @return string
	 */
	public static function banner_grid_5_2_banner_size( $size ) {
		switch ( self::$current_banner % 5 ) {
			case 1:
				$size = '660x740';
				break;

			case 0:
			case 2:
				$size = '560x360';
				break;

			case 3:
			case 4:
				$size = '460x360';
				break;
		}

		self::$current_banner++;

		return $size;
	}

	/**
	 * Change banner size while it is inside a banner grid 6
	 *
	 * @param string $size
	 *
	 * @return string
	 */
	public static function banner_grid_6_banner_size( $size ) {
		switch ( self::$current_banner % 6 ) {
			case 1:
				$size = '640x800';
				break;

			case 2:
			case 3:
				$size = '640x395';
				break;

			case 4:
			case 5:
			case 0:
				$size = '426x398';
				break;
		}

		self::$current_banner ++;

		return $size;
	}

	/**
	 * Get CSS classes for animation
	 *
	 * @param string $css_animation
	 *
	 * @return string
	 */
	public static function get_css_animation( $css_animation ) {
		$output = '';

		if ( '' !== $css_animation && 'none' !== $css_animation ) {
			wp_enqueue_script( 'waypoints' );
			wp_enqueue_style( 'animate-css' );
			$output = ' wpb_animate_when_almost_visible wpb_' . $css_animation . ' ' . $css_animation;
		}

		return $output;
	}

	/**
	 * Get products grid
	 *
	 * @param array $atts Shortcode attributes
	 * @param string $return Return type. Possible values are 'content', 'query'
	 * @return string
	 */
	protected static function products_shortcode( $atts, $return = 'content' ) {
		if ( ! class_exists( 'WC_Shortcode_Products' ) ) {
			return __( 'Please install or update WooCommerce.', 'sober' );
		}

		$atts = self::parse_product_shortcode_atts( $atts );

		$shortcode = new WC_Shortcode_Products( $atts, $atts['type'] );

		if ( 'query' == $return ) {
			return $shortcode->get_query_args();
		}

		$content = $shortcode->get_content();

		// If has load more button.
		if ( ! empty( $atts['load_more'] ) ) {
			$atts['paginate'] = true;
			$atts['page']     = isset( $atts['page'] ) ? max( 1, $atts['page'] ) : 1;
			$shortcode        = new WC_Shortcode_Products( $atts, $atts['type'] );
			$query            = new WP_Query( $shortcode->get_query_args() );
			$total_pages      = (int) $query->max_num_pages;

			if ( $atts['page'] < $total_pages ) {
				$atts['page']++;
				$atts['paginate'] = false;

				$content .= sprintf(
					'<div class="sober-product-grid__load-more load-more text-center">
						<a href="#" class="button ajax-load-products sober-product-grid__load-more-button" data-atts="%s" data-nonce="%s" rel="nofollow">
							<span class="button-text">%s</span>
							<span class="loading-icon">
								<span class="bubble"><span class="dot"></span></span>
								<span class="bubble"><span class="dot"></span></span>
								<span class="bubble"><span class="dot"></span></span>
							</span>
						</a>
					</div>',
					esc_attr( json_encode( $atts ) ),
					esc_attr( wp_create_nonce( 'sober_get_products' ) ),
					esc_html__( 'Load More', 'sober' )
				);
			}
		}

		return $content;
	}

	/**
	 * Parase shortcode attributes
	 *
	 * @param array $atts
	 * @return array
	 */
	protected static function parse_product_shortcode_atts( $atts ) {
		// Convert old attribute names to new ones.
		// 'per_page', 'oparator', 'filter' attributes are handled by WC_Shortcode_Products.
		if ( isset( $atts['css_animation'] ) ) {
			unset( $atts['css_animation'] );
		}

		if ( isset( $atts['el_class'] ) ) {
			unset( $atts['el_class'] );
		}

		// Correct the type name.
		$types = array(
			'recent'       => 'recent_products',
			'new'          => 'new_products',
			'sale'         => 'sale_products',
			'best_sellers' => 'best_selling_products',
			'top_rated'    => 'top_rated_products',
			'featured'     => 'featured_products',
			'attribute'    => 'product_attribute',
			'product'      => 'product',
		);

		$type = 'products';

		if ( empty( $atts['type'] ) && ! empty( $atts['category'] ) ) {
			$type = 'product_category';
		}

		$type = isset( $atts['type'] ) ? $atts['type'] : $type;
		$type = isset( $types[ $type ] ) ? $types[ $type ] : $type;

		$atts['type'] = $type;

		switch ( $type ) {
			case 'recent_products':
				$atts['order']   = 'DESC';
				$atts['orderby'] = 'date';
				break;

			case 'featured_products':
				$atts['visibility'] = 'featured';
				break;

			case 'product':
				$atts['skus']  = isset( $atts['sku'] ) ? $atts['sku'] : '';
				$atts['ids']   = isset( $atts['id'] ) ? $atts['id'] : '';
				$atts['limit'] = '1';
				break;

			case 'new_products':
				$atts['type'] = 'products';

				if ( function_exists( 'sober_get_new_product_ids' ) ) {
					$ids = sober_get_new_product_ids();
					$atts['ids'] = is_array( $ids ) ? implode( ',', $ids ) : '';
				}

				break;
		}

		// Use the default product order setting.
		if ( empty( $atts['orderby'] ) ) {
			$orderby_value = apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) );
			$orderby_value = is_array( $orderby_value ) ? $orderby_value : explode( '-', $orderby_value );
			$orderby       = esc_attr( $orderby_value[0] );
			$order         = ! empty( $orderby_value[1] ) ? $orderby_value[1] : 'DESC';

			if ( in_array( $orderby, array( 'menu_order', 'price' ) ) ) {
				$order = 'ASC';
			}

			$atts['orderby'] = strtolower( $orderby );
			$atts['order'] = strtoupper( $order );
		}

		return $atts;
	}

	/**
	 * Get product id default
	 */
	protected static function get_default_product_id() {
		$query = new WP_Query( array(
			'posts_per_page'         => 1,
			'post_type'              => 'product',
			'post_status'            => 'publish',
			'ignore_sticky_posts'    => true,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
		) );

		return $query->have_posts() ? $query->posts[0] : 0;
	}

	/**
	 * Build links from WPB Page Builder link attribute
	 *
	 * @param string $string
	 * @return array
	 */
	protected static function build_link( $string ) {
		if ( function_exists( 'vc_build_link' ) ) {
			return vc_build_link( $string );
		} else {
			$result       = array( 'url' => '', 'title' => '', 'target' => '', 'rel' => '' );
			$params_pairs = explode( '|', $string );

			if ( empty( $params_pairs ) ) {
				return $result;
			}

			foreach ( $params_pairs as $pair ) {
				$param = preg_split( '/\:/', $pair );

				if ( ! empty( $param[0] ) && isset( $param[1] ) ) {
					$result[ $param[0] ] = trim( rawurldecode( $param[1] ) );
				}
			}

			return $result;
		}
	}

	/**
	 * Enqueue Font Awesome 5 icons.
	 */
	public static function enqueue_font_awesome5() {
		wp_enqueue_style( 'vc_font_awesome_5_shims' );
		wp_enqueue_style( 'vc_font_awesome_5' );
	}
}
