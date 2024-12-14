<?php
/**
 * Custom functions that act in the footer.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Sober
 */

/**
 * Display Footer Newsletter
 *
 * @since 1.0.0
 */
function sober_footer_content() {
	if ( ! sober_get_option( 'footer_content_enable' ) || is_404() ) {
		return;
	}

	$content = sober_get_option( 'footer_content' );

	if ( empty( $content ) ) {
		return;
	}
	?>

	<div class="footer-content">
		<div class="sober-container">
			<?php echo do_shortcode( wp_kses_post( $content ) ); ?>
		</div>
	</div>

	<?php
}

add_action( 'sober_footer', 'sober_footer_content', 3 );

/**
 * Display widgets on footer
 */
function sober_footer_widgets() {
	if ( ! sober_get_option( 'footer_widgets' ) ) {
		return;
	}

	$layout = sober_get_option( 'footer_widgets_layout' );
	$colums = array(
		'2-columns'       => 2,
		'3-columns'       => 3,
		'4-columns'       => 4,
		'4-columns-equal' => 4,
	);
	?>

	<div class="footer-widgets widgets-area widgets-<?php echo esc_attr( $layout ) ?>">
		<div class="container">
			<div class="row">

				<?php
				for ( $i = 1; $i <= $colums[$layout]; $i++ ) {
					$column_class = 12 / $colums[$layout];

					if ( '4-columns' == $layout ) {
						$column_class = in_array( $i, array(1, 4) ) ? 4 : 2;
					}

					echo '<div class="footer-widgets-area-' . esc_attr( $i ) . ' footer-widgets-area col-xs-12 col-sm-6 col-md-' . esc_attr( $column_class ) . '">';

					dynamic_sidebar( 'footer-' . $i );

					echo '</div>';
				}
				?>

			</div>
		</div>
	</div>

	<?php
}

add_action( 'sober_footer', 'sober_footer_widgets', 5 );

/**
 * Display Instagram feed on footer
 */
function sober_footer_instagram() {
	if ( ! sober_get_option( 'footer_instagram' ) ) {
		return;
	}

	$media = sober_get_instagram_images( '', 8 );

	if ( is_wp_error( $media ) ) {
		echo trim( $media->get_error_message() );
	} else {
		$current = 1;
		$limit = 8;
		$use_video = apply_filters( 'sober_footer_instagram_video', false );
		?>

		<div class="footer-instagram">
			<div class="sober-container">
				<ul class="instagram-feed instagram-feed--force-square">
					<?php
					foreach ( $media as $item ) {
						if ( $use_video && 'video' == $item['type'] ) {
							continue;
						}

						if ( $current > $limit ) {
							break;
						}

						sober_instagram_image( $item, true );

						$current++;
					}
					?>

				</ul>
			</div>
		</div>

		<?php
	}
}

add_action( 'sober_footer', 'sober_footer_instagram', 7 );

/**
 *  Display site footer
 */
function sober_footer_info() {
	$social_extra  = sober_get_option( 'footer_social_extra' );
	$wrapper       = sober_get_option( 'footer_wrapper' );
	$wrapper_class = 'wrapped' == $wrapper ? 'container' : 'sober-container';
	?>
	<div class="footer-info footer-<?php echo esc_attr( $wrapper ) ?>">
		<div class="<?php echo esc_attr( $wrapper_class ); ?>">
			<div class="row">

				<div class="site-info <?php echo has_nav_menu( 'socials' ) || $social_extra ? 'col-md-6' : 'col-md-12' ?>">
					<div class="copyright">
						<?php echo do_shortcode( wp_kses_post( sober_get_option( 'footer_copyright' ) ) ); ?>
					</div><!-- .site-info -->
					<?php
					if ( has_nav_menu( 'footer' ) ) {
						wp_nav_menu( array(
							'container'       => 'nav',
							'container_class' => 'footer-menu',
							'theme_location'  => 'footer',
							'menu_id'         => 'footer-menu',
							'depth'           => 1,
						) );
					}
					?>
				</div>

				<?php if ( has_nav_menu( 'socials' ) || $social_extra ) : ?>
					<div class="footer-social col-md-6">
						<?php
						if ( has_nav_menu( 'socials' ) ) {
							wp_nav_menu( array(
								'theme_location'  => 'socials',
								'container_class' => 'socials-menu ',
								'menu_id'         => 'footer-socials',
								'depth'           => 1,
							) );
						}

						if ( $social_extra ) {
							printf( '<div class="socials-extra">%s</div>', do_shortcode( wp_kses_post( $social_extra ) ) );
						}
						?>
					</div>
				<?php endif; ?>

			</div>
		</div>
	</div>
	<?php
}

add_action( 'sober_footer', 'sober_footer_info' );

/**
 * Footer bottom content
 *
 * @since 1.1.5
 */
function sober_footer_bottom() {
	if ( ! ( $footer_bottom_content = sober_get_option( 'footer_bottom_content' ) ) ) {
		return;
	}

	printf(
		'<div class="footer-bottom text-%s"><div class="sober-container">%s</div></div>',
		esc_attr( sober_get_option( 'footer_bottom_content_align' ) ),
		do_shortcode( wp_kses_post( $footer_bottom_content ) )
	);
}

add_action( 'sober_footer', 'sober_footer_bottom', 30 );

/**
 * Add this back-to-top button to footer
 */
function sober_gotop_button() {
	if ( sober_get_option( 'footer_gotop' ) ) {
		echo '<a href="#page" id="gotop">' . sober_svg_icon( 'icon=backtotop-arrow&echo=0' ) . '<span class="screen-reader-text">' . esc_html__( 'Go to top', 'sober' ) . '</span></a>';
	}
}

add_action( 'wp_footer', 'sober_gotop_button' );


if ( ! function_exists( 'sober_search_modal' ) ) :
	/**
	 * Add search modal to footer
	 */
	function sober_search_modal() {
		$type = sober_get_option( 'header_search_content' );
		$cats = sober_get_option( 'header_search_' . $type . '_cats' );
		?>
		<div id="search-modal" class="search-modal sober-modal" tabindex="-1" role="dialog">
			<div class="modal-header">
				<a href="#" class="close-modal">
					<?php sober_svg_icon( 'icon=close-delete' ) ?>
					<span class="screen-reader-text"><?php esc_html_e( 'Close', 'sober' ) ?></span>
				</a>

				<h4><?php esc_html_e( 'Search', 'sober' ); ?></h4>
			</div>

			<div class="modal-content">
				<div class="container">
					<form role="search" method="get" class="instance-search" action="<?php echo esc_url( apply_filters( 'sober_search_form_action', home_url( '/' ) ) ) ?>">
						<?php
						if ( '0' !== $cats ) {
							$taxonomy = 'product' == $type ? 'product_cat' : 'category';
							$cats_args = array(
								'taxonomy' => $taxonomy,
								'parent'   => sober_get_option( 'header_search_' . $type . '_cats_top' ) ? 0 : '',
								'orderby'  => 'count',
								'order'    => 'DESC',
							);

							if ( is_numeric( $cats ) ) {
								$cats_args['number'] = $cats;
							} elseif ( ! empty( $cats ) ) {
								$cats_args['name'] = explode( ',', $cats );
								$cats_args['orderby'] = 'include';
								unset( $cats_args['parent'] );
							}

							$categories = get_terms( $cats_args );

							if ( $categories && ! is_wp_error( $categories ) ) :
							?>

								<div class="product-cats">
									<label>
										<input type="radio" name="<?php echo esc_attr( $taxonomy ) ?>" value="" checked="checked">
										<span class="line-hover"><?php esc_html_e( 'All Categories', 'sober' ) ?></span>
									</label>

									<?php foreach ( $categories as $category ) : ?>
										<label>
											<input type="radio" name="<?php echo esc_attr( $taxonomy ) ?>" value="<?php echo esc_attr( $category->slug ); ?>">
											<span class="line-hover"><?php echo esc_html( $category->name ); ?></span>
										</label>
									<?php endforeach; ?>
								</div>

							<?php
							endif;
						}
						?>

						<div class="search-fields">
							<button type="submit" class="search-submit">
								<?php sober_svg_icon( 'icon=search' ) ?>
								<span class="screen-reader-text"><?php esc_html_e( 'Search', 'sober' ) ?></span>
							</button>

							<input type="text" name="s" placeholder="<?php esc_attr_e( 'Search', 'sober' ); ?>" class="search-field" autocomplete="off">
							<input type="hidden" name="post_type" value="<?php echo esc_attr( $type ) ?>">

							<button type="reset" class="search-reset">
								<?php sober_svg_icon( 'icon=close-delete-small&size=14' ) ?>
								<span class="screen-reader-text"><?php esc_html_e( 'Reset', 'sober' ) ?></span>
							</button>
						</div>
					</form>

					<div class="search-results">
						<div class="text-center loading">
							<i class="fa fa-circle-o-notch fa-spin fa-2x fa-fw margin-bottom"></i></div>
						<div class="results-container"></div>
						<div class="view-more-results text-center">
							<a href="#" class="button search-results-button"><?php esc_html_e( 'View more', 'sober' ) ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

endif;

add_action( 'wp_footer', 'sober_search_modal' );

if ( ! function_exists( 'sober_login_modal' ) ) :
	/**
	 * Add login modal to footer
	 */
	function sober_login_modal() {
		if ( ! shortcode_exists( 'woocommerce_my_account' ) ) {
			return;
		}

		if ( is_user_logged_in() ) {
			return;
		}

		if ( 'modal' != sober_get_option( 'account_icon_behaviour' ) ) {
			return;
		}

		if ( is_account_page() ) {
			return;
		}
		?>

		<div id="login-modal" class="login-modal sober-modal woocommerce-account" tabindex="-1" role="dialog">
			<div class="modal-header">
				<a href="#" class="close-modal">
					<?php sober_svg_icon( 'icon=close-delete' ) ?>
					<span class="screen-reader-text"><?php esc_html_e( 'Close', 'sober' ) ?></span>
				</a>

				<h4><?php esc_html_e( 'My Account', 'sober' ); ?></h4>
			</div>

			<div class="modal-content">
				<div class="container">
					<?php echo do_shortcode( '[woocommerce_my_account]' ) ?>
				</div>
			</div>
		</div>

		<?php
	}
endif;

add_action( 'wp_footer', 'sober_login_modal' );

if ( ! function_exists( 'sober_cart_modal' ) ) :
	/**
	 * Add cart modal to footer
	 */
	function sober_cart_modal() {
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		if ( empty( WC()->cart ) ) {
			return;
		}
		?>

		<div id="cart-modal" class="cart-modal sober-modal woocommerce" tabindex="-1" role="dialog">
			<div class="modal-header">
				<a href="#" class="close-modal">
					<?php sober_svg_icon( 'icon=close-delete' ) ?>
					<span class="screen-reader-text"><?php esc_html_e( 'Close', 'sober' ) ?></span>
				</a>

				<h4><?php esc_html_e( 'Cart', 'sober' ); ?></h4>
			</div>

			<div class="modal-content">
				<div class="container">

					<div class="cart-tabs-nav tabs-nav text-center">
						<span class="line-hover tab-nav active" data-tab="cart"><?php esc_html_e( 'Shopping Cart', 'sober' ) ?>
							<span class="count cart-counter"><?php echo WC()->cart->get_cart_contents_count() ?></span>
						</span>

						<?php if ( class_exists( '\WCBoost\Wishlist\Plugin' ) ) : ?>
							<span class="line-hover tab-nav" data-tab="wishlist">
								<?php esc_html_e( 'Wishlist', 'sober' ) ?>
								<span class="count wishlist-counter"><?php echo \WCBoost\Wishlist\Helper::get_wishlist()->count_items(); ?></span>
							</span>
						<?php elseif ( function_exists( 'Soo_Wishlist' ) ) : ?>
							<span class="line-hover tab-nav" data-tab="wishlist">
								<?php esc_html_e( 'Wishlist', 'sober' ) ?>
								<span class="count wishlist-counter"><?php echo soow_count_products() ?></span>
							</span>
						<?php elseif ( defined( 'YITH_WCWL' ) && YITH_WCWL ) : ?>
							<span class="line-hover tab-nav" data-tab="wishlist">
								<?php esc_html_e( 'Wishlist', 'sober' ) ?>
								<span class="count wishlist-counter"><?php echo yith_wcwl_count_products() ?></span>
							</span>
						<?php endif; ?>
					</div>
					<div class="tab-panels">
						<div class="tab-panel woocommerce active" data-tab="cart">
							<div class="widget_shopping_cart_content">
								<?php woocommerce_mini_cart(); ?>
							</div>
						</div>
						<div class="tab-panel woocommerce-wishlist" data-tab="wishlist">
							<?php
							if ( class_exists( '\WCBoost\Wishlist\Plugin' ) ) {
								\WCBoost\Wishlist\Helper::widget_content( array(
									'show_stock'    => true,
									'show_purchase' => true,
								) );
							} elseif ( shortcode_exists( 'soo_wishlist' ) ) {
								echo do_shortcode( '[soo_wishlist]' );
							} elseif ( shortcode_exists( 'yith_wcwl_wishlist' ) ) {
								echo do_shortcode( '[yith_wcwl_wishlist]' );
							}
							?>
						</div>
					</div>

				</div>
			</div>
		</div>

		<?php
	}
endif;

add_action( 'wp_footer', 'sober_cart_modal' );

if ( ! function_exists( 'sober_quick_view_modal' ) ) :
	/**
	 * Adds quick view modal to footer
	 */
	function sober_quick_view_modal() {
		if ( ! sober_get_option( 'product_quickview' ) ) {
			return;
		}
		?>

		<div id="quick-view-modal" class="quick-view-modal sober-modal woocommerce" tabindex="-1" role="dialog">
			<div class="modal-header">
				<a href="#" class="close-modal">
					<?php sober_svg_icon( 'icon=close-delete' ) ?>
					<span class="screen-reader-text"><?php esc_html_e( 'Close', 'sober' ) ?></span>
				</a>

				<h4><?php esc_html_e( 'Product Quick View', 'sober' ); ?></h4>
			</div>

			<div class="modal-content">
				<div class="container">

					<div class="sober-modal-backdrop"></div>
					<div class="product"></div>

				</div>
			</div>

			<div class="sober-modal-backdrop"></div>
			<div class="loader"></div>
		</div>

		<?php
	}
endif;

add_action( 'wp_footer', 'sober_quick_view_modal' );

/**
 * Adds photoSwipe dialog element
 */
function sober_product_images_lightbox() {
	if ( ! get_option( 'woocommerce_enable_lightbox' ) || ! is_singular( array( 'product' ) ) || version_compare( WC()->version, '3.0.0', '>=' ) ) {
		return;
	}

	?>
	<div id="pswp" class="pswp" tabindex="-1" role="dialog" aria-hidden="true">

		<div class="pswp__bg"></div>

		<div class="pswp__scroll-wrap">

			<div class="pswp__container">
				<div class="pswp__item"></div>
				<div class="pswp__item"></div>
				<div class="pswp__item"></div>
			</div>

			<div class="pswp__ui pswp__ui--hidden">

				<div class="pswp__top-bar">


					<div class="pswp__counter"></div>

					<button class="pswp__button pswp__button--close" title="<?php esc_attr_e( 'Close (Esc)', 'sober' ) ?>"></button>

					<button class="pswp__button pswp__button--share" title="<?php esc_attr_e( 'Share', 'sober' ) ?>"></button>

					<button class="pswp__button pswp__button--fs" title="<?php esc_attr_e( 'Toggle fullscreen', 'sober' ) ?>"></button>

					<button class="pswp__button pswp__button--zoom" title="<?php esc_attr_e( 'Zoom in/out', 'sober' ) ?>"></button>

					<div class="pswp__preloader">
						<div class="pswp__preloader__icn">
							<div class="pswp__preloader__cut">
								<div class="pswp__preloader__donut"></div>
							</div>
						</div>
					</div>
				</div>

				<div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
					<div class="pswp__share-tooltip"></div>
				</div>

				<button class="pswp__button pswp__button--arrow--left" title="<?php esc_attr_e( 'Previous (arrow left)', 'sober' ) ?>"></button>

				<button class="pswp__button pswp__button--arrow--right" title="<?php esc_attr_e( 'Next (arrow right)', 'sober' ) ?>"></button>

				<div class="pswp__caption">
					<div class="pswp__caption__center"></div>
				</div>

			</div>

		</div>

	</div>
	<?php
}

add_action( 'wp_footer', 'sober_product_images_lightbox' );

/**
 * Prepare mobile nav at the footer
 */
function sober_mobile_nav() {
	$menu        = has_nav_menu( 'mobile' ) ? 'mobile' : 'primary';
	$menu_top    = sober_get_option( 'mobile_menu_top' );
	$menu_bottom = sober_get_option( 'mobile_menu_bottom' );
	$css_class   = array( 'side-menu', 'mobile-menu' );

	if ( ! empty( $menu_top ) ) {
		$css_class[] = 'has-top-content';
	}

	if ( ! empty( $menu_bottom ) ) {
		$css_class[] = 'has-bottom-content';
	}
	?>

	<div class="side-menu-backdrop"></div>

	<div id="mobile-menu" class="<?php echo esc_attr( implode( ' ', $css_class ) ) ?>">
		<div class="mobile-menu-inner">
			<?php if ( sober_get_option( 'mobile_menu_close' ) ) : ?>
				<span class="toggle-nav active" data-target="mobile-menu"><span class="icon-nav"></span></span>
			<?php endif; ?>

			<?php if ( ! empty( $menu_top ) ) : ?>

				<div class="mobile-menu-top clearfix">
					<?php
					foreach ( (array) $menu_top as $item ) {
						switch ( $item ) {
							case 'currency':
								sober_currency_switcher();
								break;

							case 'language':
								sober_language_switcher();
								break;
						}
					}
					?>
				</div>

			<?php endif; ?>

			<?php if ( sober_get_option( 'mobile_menu_search' ) ) : ?>
				<form role="search" method="get" class="search-form" action="<?php echo esc_url( apply_filters( 'sober_search_form_action', home_url( '/' ) ) ) ?>">
					<label>
						<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'sober' ) ?></span>
						<input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search', 'sober' ) ?>" value="<?php get_search_query() ?>" name="s" />
					</label>
					<?php if ( 'all' !== sober_get_option( 'mobile_menu_search_content' ) ) : ?>
						<input type="hidden" name="post_type" value="<?php echo esc_attr( sober_get_option( 'mobile_menu_search_content' ) ) ?>">
					<?php endif; ?>
					<button type="submit" class="search-submit">
						<span class="screen-reader-text"><?php esc_html_e( 'Search', 'sober' ) ?></span>
						<?php sober_svg_icon( 'icon=search' ) ?>
					</button>
				</form>
			<?php endif; ?>

			<nav id="mobile-nav" class="mobile-nav">
				<?php
				wp_nav_menu( array(
					'theme_location' => $menu,
					'container' => false,
					'after' => '<button class="toggle"><span class="caret"></span><span class="screen-reader-text">' . esc_html__( 'Toggle', 'sober' ) . '</span></button>'
				) );
				?>
			</nav>

			<?php if ( ! empty( $menu_bottom ) && function_exists( 'WC' ) ) : ?>

				<div class="mobile-menu-bottom">
					<ul>
						<?php
						foreach ( (array) $menu_bottom as $item ) {
							switch ( $item ) {
								case 'cart':
									if ( ! function_exists( 'WC' ) || empty( WC()->cart ) ) {
										break;
									}

									printf(
										'<li class="item-cart"><a href="%s">
											%s
											<span>%s</span>
											<span class="count cart-counter">%s</span>
										</a></li>',
										esc_url( wc_get_cart_url() ),
										sober_shopping_cart_icon( false ),
										esc_html__( 'Shopping Cart', 'sober' ),
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
											'<li class="item-wishlist"><a href="%s">
												' . sober_svg_icon( 'icon=heart-wishlist-like&echo=0' ) . '
												<span>%s</span>
												<span class="count wishlist-counter">%s</span>
											</a></li>',
											esc_url( $wishlist_url ),
											esc_html__( 'Wishlist', 'sober' ),
											$wishlist_count
										);
									}
									break;

								case 'login':
									printf(
										'<li class="item-login"><a href="%s" data-toggle="%s" data-target="login-modal">
											' . sober_svg_icon( 'icon=user-account-people&echo=0' ) . '
											<span>%s</span>
										</a></li>',
										esc_url( wc_get_account_endpoint_url( 'dashboard' ) ),
										is_user_logged_in() ? 'link' : sober_get_option( 'account_icon_behaviour' ),
										is_user_logged_in() ? esc_html__( 'My Account', 'sober' ) : esc_html__( 'Login', 'sober' )
									);
									break;
							}
						}
						?>
					</ul>
				</div>

			<?php endif; ?>
		</div>
	</div>

	<?php
}

add_action( 'wp_footer', 'sober_mobile_nav' );

/**
 * Place primary menu on footer for header v6
 */
function sober_side_nav() {
	if ( 'v6' !== sober_get_option( 'header_layout' ) ) {
		return;
	}

	$menu_top = sober_get_option( 'menu_top' );
	$menu_bottom = sober_get_option( 'menu_bottom' );

	?>
	<div id="primary-menu" class="side-menu">
		<span class="toggle-nav active" data-target="primary-menu"><span class="icon-nav"></span></span>

		<div class="side-menu__wrapper">
			<?php if ( ! empty( $menu_top ) ) : ?>
				<div class="side-menu__top">
					<?php
					foreach ( (array) $menu_top as $item ) {
						switch ( $item ) {
							case 'search':
								?>
								<form role="search" method="get" class="search-form" action="<?php echo esc_url( apply_filters( 'sober_search_form_action', home_url( '/' ) ) ) ?>">
									<label>
										<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'sober' ) ?></span>
										<input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search', 'sober' ) ?>" value="<?php get_search_query() ?>" name="s" />
									</label>
									<?php if ( 'all' !== sober_get_option( 'header_search_content' ) ) : ?>
										<input type="hidden" name="post_type" value="<?php echo esc_attr( sober_get_option( 'header_search_content' ) ) ?>">
									<?php endif; ?>
									<button type="submit" class="search-submit">
										<span class="screen-reader-text"><?php esc_html_e( 'Search', 'sober' ) ?></span>
										<?php sober_svg_icon( 'icon=search' ) ?>
									</button>
								</form>
								<?php
								break;
						}
					}
					?>
				</div>
			<?php endif; ?>

			<nav class="primary-menu">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container' => false,
					'after' => '<button class="toggle"><span class="caret"></span><span class="screen-reader-text">' . esc_html__( 'Toggle', 'sober' ) . '</span></button>'
				) );
				?>
			</nav>

			<?php if ( ! empty( $menu_bottom ) && function_exists( 'WC' ) ) : ?>

			<div class="side-menu__bottom">
				<ul>
					<?php
					foreach ( (array) $menu_bottom as $item ) {
						switch ( $item ) {
							case 'cart':
								printf(
									'<li class="item-cart"><a href="%s">
										%s
										<span>%s</span>
										<span class="count cart-counter">%s</span>
									</a></li>',
									esc_url( wc_get_cart_url() ),
									sober_shopping_cart_icon( false ),
									esc_html__( 'Shopping Cart', 'sober' ),
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
										'<li class="item-wishlist"><a href="%s">
											' . sober_svg_icon( 'icon=heart-wishlist-like&echo=0' ) . '
											<span>%s</span>
											<span class="count wishlist-counter">%s</span>
										</a></li>',
										esc_url( $wishlist_url ),
										esc_html__( 'Wishlist', 'sober' ),
										$wishlist_count
									);
								}
								break;

							case 'login':
								printf(
									'<li class="item-login"><a href="%s" data-toggle="%s" data-target="login-modal">
										' . sober_svg_icon( 'icon=user-account-people&echo=0' ) . '
										<span>%s</span>
									</a></li>',
									esc_url( wc_get_account_endpoint_url( 'dashboard' ) ),
									is_user_logged_in() ? 'link' : sober_get_option( 'account_icon_behaviour' ),
									is_user_logged_in() ? esc_html__( 'My Account', 'sober' ) : esc_html__( 'Login', 'sober' )
								);
								break;
						}
					}
					?>
				</ul>
			</div>

			<?php endif; ?>
		</div>
	</div>
	<?php
}

add_action( 'wp_footer', 'sober_side_nav' );

/**
 * Add the popup HTML to footer
 *
 * @since 2.0
 */
function sober_popup() {
	if ( ! sober_get_option( 'popup' ) ) {
		return;
	}

	$popup_frequency = intval( sober_get_option( 'popup_frequency' ) );

	if ( $popup_frequency > 0 && isset( $_COOKIE['sober_popup'] ) ) {
		return;
	}

	$popup_layout = sober_get_option( 'popup_layout' );

	get_template_part( 'template-parts/popup', $popup_layout );
}

add_action( 'wp_footer', 'sober_popup' );

/**
 * Adds preloader container at the bottom of the site
 */
function sober_preloader() {
	if ( ! sober_get_option( 'preloader' ) ) {
		return;
	}

	get_template_part( 'template-parts/preloader' );
}

add_action( 'wp_footer', 'sober_preloader' );

/**
 * Adds classes of background and text color to footer
 *
 * @param array $classes
 *
 * @return array
 */
function sober_footer_classes( $classes ) {
	if ( is_page_template( 'templates/full-screen.php' ) ) {
		return $classes;
	}

	$background = sober_get_option( 'footer_background' );
	$text_color = 'dark' == $background ? 'light' : ( 'light' == $background ? 'dark' : sober_get_option( 'footer_textcolor' ) );

	$page_id = sober_get_current_page_id();

	if ( $page_id ) {
		$page_background = get_post_meta( $page_id, 'footer_background', true );
		$background = $page_background ? $page_background : $background;
		$text_color = 'dark' == $background ? 'light' : ( 'light' == $background ? 'dark' : $text_color );

		if ( in_array( $background, array( 'custom', 'transparent' ) )  ) {
			$page_text_color = get_post_meta( $page_id, 'footer_textcolor', true );
			$text_color      = $page_text_color ? $page_text_color : $text_color;
		}
	}

	$classes[] = $background;
	$classes[] = 'text-' . $text_color;

	return $classes;
}

add_filter( 'sober_footer_class', 'sober_footer_classes' );
