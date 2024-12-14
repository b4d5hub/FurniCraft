<?php

/**
 * Class Sober_Walker_Mega_Menu
 *
 * Walker class for mega menu
 */
class Sober_Walker_Mega_Menu extends Walker_Nav_Menu {
	/**
	 * Tells child items know it is in a mega menu or not
	 *
	 * @var boolean
	 */
	protected $in_mega = false;

	/**
	 * Store menu item mega data
	 *
	 * @var array
	 */
	protected $mega_data = array();

	/**
	 * Custom CSS for menu items
	 *
	 * @var string
	 */
	protected $css = '';

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see   Walker::start_lvl()
	 *
	 * @since 1.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );

		if ( ! $depth && $this->in_mega ) {
			$style = $this->get_mega_inline_css();
			$output .= "\n$indent<ul class=\"sub-menu mega-menu-container\" $style>\n";
		} else {
			$output .= "\n$indent<ul class=\"sub-menu\">\n";
		}
	}

	/**
	 * Start the element output.
	 * Display item description text and classes
	 *
	 * @see   Walker::start_el()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 * @param int    $id     Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		// Get mega data from post meta
		$item_mega = get_post_meta( $item->ID, '_menu_item_mega', true );
		$item_mega = sober_parse_args( $item_mega, sober_get_mega_menu_setting_default() );

		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		/**
		 * Filters the arguments for a single nav menu item.
		 *
		 * @since 4.4.0
		 *
		 * @param array  $args  An array of arguments.
		 * @param object $item  Menu item data object.
		 * @param int    $depth Depth of menu item. Used for padding.
		 */
		$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

		if ( $item_mega['icon'] ) {
			$classes[] = 'menu-item-has-icon';
		}
		if ( $item_mega['mega'] && ! $depth ) {
			$classes[] = 'menu-item-mega';

			if ( $item_mega['background']['image'] ) {
				$classes[] = 'menu-item-has-background';
			}
		}

		if ( 1 == $depth && $this->in_mega ) {
			$classes[] = 'mega-sub-menu ' . $this->get_css_column( $item_mega['width'] );

			if ( $item_mega['disable_link'] ) {
				$classes[] = 'link-disabled';
			}

			if ( $item_mega['hide_text'] ) {
				$classes[] = 'menu-item-title-hidden';
			}

			if ( $item_mega['border']['left'] ) {
				$classes[] = 'has-border-left';
			}
		}

		// Check if this is top level and is mega menu
		if ( ! $depth ) {
			$this->in_mega = $item_mega['mega'];
			$this->mega_data = $item_mega;
		}

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$item_id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );

		$item_id = $item_id ? ' id="' . esc_attr( $item_id ) . '"' : '';

		$output .= $indent . '<li' . $item_id . $class_names . '>';

		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		// Check if link is disable
		if ( $this->in_mega && $depth == 1 && $item_mega['disable_link'] ) {
			$link_open = '<span>';
		} else {
			$link_open = '<a' . $attributes . '>';
		}

		// Adds icon
		if ( $item_mega['icon'] ) {
			$icon = '<i class="' . esc_attr( $item_mega['icon'] ) . '"></i>';
		} else {
			$icon = '';
		}

		/** This filter is documented in wp-includes/post-template.php */
		$title = apply_filters( 'the_title', $item->title, $item->ID );

		/**
		 * Filters a menu item's title.
		 *
		 * @since 4.4.0
		 *
		 * @param string $title The menu item's title.
		 * @param object $item  The current menu item.
		 * @param array  $args  An array of wp_nav_menu() arguments.
		 * @param int    $depth Depth of menu item. Used for padding.
		 */
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		// Check if link is disable
		if ( $this->in_mega && $depth == 1 && $item_mega['disable_link'] ) {
			$link_close = '</span>';
		} else {
			$link_close = '</a>';
		}

		$item_output = $args->before;
		$item_output .= $link_open;
		$item_output .= $args->link_before . $icon . $title . $args->link_after;
		$item_output .= $link_close;
		$item_output .= $args->after;

		if ( 1 <= $depth && ! empty( $item_mega['content'] ) ) {
			$item_output .= '<div class="menu-item-content">' . do_shortcode( $item_mega['content'] ) . '</div>';
		}

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * Get CSS column class name
	 *
	 * @param string $width
	 *
	 * @return string
	 */
	private function get_css_column( $width = '25.00%' ) {
		$columns = array(
			3  => '25.00%',
			4  => '33.33%',
			6  => '50.00%',
			8  => '66.66%',
			9  => '75.00%',
			12 => '100.00%',
		);

		$column = array_search( $width, $columns );
		$column = false === $column ? 3 : $column;

		return 'col-md-' . $column;
	}

	/**
	 * Get inline css for mega menu container
	 *
	 * @return string
	 */
	private function get_mega_inline_css() {
		if ( ! $this->in_mega ) {
			return '';
		}

		$props = array();

		if ( $this->mega_data['width'] ) {
			$props['width'] = $this->mega_data['width'];
		}

		if ( $this->mega_data['background']['color'] ) {
			$props['background-color'] = $this->mega_data['background']['color'];
		}

		if ( $this->mega_data['background']['image'] ) {
			$props['background-image'] = 'url(' . $this->mega_data['background']['image'] . ')';
			$props['background-attachment'] = $this->mega_data['background']['attachment'];
			$props['background-repeat'] = $this->mega_data['background']['repeat'];

			if ( $this->mega_data['background']['size'] ) {
				$props['background-size'] = $this->mega_data['background']['size'];
			}

			if ( $this->mega_data['background']['position']['x'] == 'custom' ) {
				$position_x = $this->mega_data['background']['position']['custom']['x'];
			} else {
				$position_x = $this->mega_data['background']['position']['x'];
			}

			if ( $this->mega_data['background']['position']['y'] == 'custom' ) {
				$position_y = $this->mega_data['background']['position']['custom']['y'];
			} else {
				$position_y = $this->mega_data['background']['position']['y'];
			}

			$props['background-position'] = $position_x . ' ' . $position_y;
		}

		if ( empty( $props ) ) {
			return '';
		}

		$style = '';
		foreach ( $props as $prop => $value ) {
			$style .= $prop . ':' . esc_attr( $value ) . ';';
		}

		return 'style="' . $style . '"';
	}
}

/**
 * Add a walder object for all nav menu
 *
 * @since  1.0.0
 *
 * @param  array $args The default args
 *
 * @return array
 */
function sober_nav_menu_args( $args ) {
	if ( ! in_array( $args['theme_location'], array( 'topbar', 'footer', 'socials' ) ) ) {
		$args['walker'] = new Sober_Walker_Mega_Menu;
	}

	if ( in_array( $args['theme_location'], array( 'primary', 'secondary' ) ) ) {
		$args['fallback_cb'] = false;
	}

	return $args;
}

add_filter( 'wp_nav_menu_args', 'sober_nav_menu_args' );

if ( ! function_exists( 'sober_menu_social_icon' ) ) :

	/**
	 * Add SVG code of the social icon to the social menu.
	 * This function only adds icons which are not supported in FontAwesome v4.
	 *
	 * @param object $args
	 * @param object $item
	 *
	 * @return string
	 */
	function sober_menu_social_icon( $title, $item, $args ) {
		if ( 'socials' != $args->theme_location ) {
			return $title;
		}

		if ( preg_match( '/tiktok.com/i', $item->url ) ) {
			$svg   = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M448,209.91a210.06,210.06,0,0,1-122.77-39.25V349.38A162.55,162.55,0,1,1,185,188.31V278.2a74.62,74.62,0,1,0,52.23,71.18V0l88,0a121.18,121.18,0,0,0,1.86,22.17h0A122.18,122.18,0,0,0,381,102.39a121.43,121.43,0,0,0,67,20.14Z"/></svg>';
			$title = '<span class="svg-icon svg-icon--tiktok">' . $svg . '</span><span>' . $title . '</span>';
		} elseif ( preg_match( '/discord.com/i', $item->url ) ) {
			$svg   = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path d="M524.531,69.836a1.5,1.5,0,0,0-.764-.7A485.065,485.065,0,0,0,404.081,32.03a1.816,1.816,0,0,0-1.923.91,337.461,337.461,0,0,0-14.9,30.6,447.848,447.848,0,0,0-134.426,0,309.541,309.541,0,0,0-15.135-30.6,1.89,1.89,0,0,0-1.924-.91A483.689,483.689,0,0,0,116.085,69.137a1.712,1.712,0,0,0-.788.676C39.068,183.651,18.186,294.69,28.43,404.354a2.016,2.016,0,0,0,.765,1.375A487.666,487.666,0,0,0,176.02,479.918a1.9,1.9,0,0,0,2.063-.676A348.2,348.2,0,0,0,208.12,430.4a1.86,1.86,0,0,0-1.019-2.588,321.173,321.173,0,0,1-45.868-21.853,1.885,1.885,0,0,1-.185-3.126c3.082-2.309,6.166-4.711,9.109-7.137a1.819,1.819,0,0,1,1.9-.256c96.229,43.917,200.41,43.917,295.5,0a1.812,1.812,0,0,1,1.924.233c2.944,2.426,6.027,4.851,9.132,7.16a1.884,1.884,0,0,1-.162,3.126,301.407,301.407,0,0,1-45.89,21.83,1.875,1.875,0,0,0-1,2.611,391.055,391.055,0,0,0,30.014,48.815,1.864,1.864,0,0,0,2.063.7A486.048,486.048,0,0,0,610.7,405.729a1.882,1.882,0,0,0,.765-1.352C623.729,277.594,590.933,167.465,524.531,69.836ZM222.491,337.58c-28.972,0-52.844-26.587-52.844-59.239S193.056,219.1,222.491,219.1c29.665,0,53.306,26.82,52.843,59.239C275.334,310.993,251.924,337.58,222.491,337.58Zm195.38,0c-28.971,0-52.843-26.587-52.843-59.239S388.437,219.1,417.871,219.1c29.667,0,53.307,26.82,52.844,59.239C470.715,310.993,447.538,337.58,417.871,337.58Z"/></svg>';
			$title = '<span class="svg-icon svg-icon--discord">' . $svg . '</span><span>' . $title . '</span>';
		} elseif ( preg_match( '/x.com/i', $item->url ) ) {
			$svg   = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg>';
			$title = '<span class="svg-icon svg-icon--x">' . $svg . '</span><span>' . $title . '</span>';
		} else {
			$title = '<span>' . $title . '</span>';
		}

		return $title;
	}

endif;

add_filter( 'nav_menu_item_title', 'sober_menu_social_icon', 10, 3 );
