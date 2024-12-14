<?php
/**
 * Instagram feed widget
 */

if ( ! class_exists( 'Sober_Instagram_Widget' ) ) :

/**
 * Instagram widget class
 */
class Sober_Instagram_Widget extends WP_Widget {
	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Class constructor
	 * Set up the widget
	 */
	public function __construct() {
		$this->defaults = array(
			'title'    => '',
			'username' => '',
			'token'    => '',
			'number'   => 9,
			'columns'  => 3,
			'size'     => 'large',
			'link'     => '',
		);

		parent::__construct(
			'instagram-feed-widget',
			esc_html__( 'Sober - Instagram', 'sober' ),
			array(
				'classname'                   => 'instagram-feed-widget',
				'description'                 => esc_html__( 'Displays your latest Instagram photos', 'sober' ),
				'customize_selective_refresh' => true,
			)
		);
	}

	/**
	 * Display widget
	 *
	 * @param array $args     Sidebar configuration
	 * @param array $instance Widget settings
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );

		echo $args['before_widget'];

		if ( $title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		// Get photos.
		$media = $this->get_photos_by_token( $instance['token'], $instance['number'] );

		// Display photos.
		if ( is_wp_error( $media ) ) {
			echo wp_kses_post( $media->get_error_message() );
		} else {
			// slice list down to required limit.
			$media = array_slice( $media, 0, intval( $instance['number'] ) );
			$classes = array(
				'instagram-pics',
				'force-square',
				'columns-' . $instance['columns'],
			);
			?>
			<ul class="<?php echo esc_attr( implode( ' ', $classes ) ) ?>">

				<?php
				foreach( $media as $item ) :
					$this->display_media( $item, true );
				endforeach;
				?>

			</ul>
			<?php
		}

		echo $args['after_widget'];
	}

	/**
	 * Update widget
	 *
	 * @param array $new_instance New widget settings
	 * @param array $old_instance Old widget settings
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$new_instance['title']   = strip_tags( $new_instance['title'] );
		$new_instance['number']  = intval( $new_instance['number'] );
		$new_instance['columns'] = intval( $new_instance['columns'] );

		return $new_instance;
	}

	/**
	 * Display widget settings
	 *
	 * @param array $instance Widget settings
	 *
	 * @return void
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'sober' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'token' ) ); ?>"><?php esc_html_e( 'Access Token', 'sober' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'token' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'token' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['token'] ); ?>" placeholder="<?php esc_attr_e( 'Access Token', 'sober' ) ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of Photos', 'sober' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" class="widefat" value="<?php echo intval( $instance['number'] ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>"><?php esc_html_e( 'Columns', 'sober' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'columns' ) ); ?>" class="widefat">
				<option value="1" <?php selected( '1', $instance['columns'] ); ?>><?php esc_html_e( '1 Column', 'sober' ); ?></option>
				<option value="2" <?php selected( '2', $instance['columns'] ); ?>><?php esc_html_e( '2 Columns', 'sober' ); ?></option>
				<option value="3" <?php selected( '3', $instance['columns'] ); ?>><?php esc_html_e( '3 Columns', 'sober' ); ?></option>
				<option value="4" <?php selected( '4', $instance['columns'] ); ?>><?php esc_html_e( '4 Columns', 'sober' ); ?></option>
			</select>
		</p>

		<?php
	}

	/**
	 * Scrape Instagram photos
	 *
	 * @param  string $username
	 * @return array
	 */
	protected function scrape_instagram( $username ) {
		$username = trim( strtolower( $username ) );

		switch ( substr( $username, 0, 1 ) ) {
			case '#':
				$url              = 'https://instagram.com/explore/tags/' . str_replace( '#', '', $username );
				$transient_prefix = 'h';
				break;

			default:
				$url              = 'https://instagram.com/' . str_replace( '@', '', $username );
				$transient_prefix = 'u';
				break;
		}

		$transient_key = 'sober_instagram-' . $transient_prefix . '-' . sanitize_title_with_dashes( $username );

		if ( false === ( $images = get_transient( $transient_key ) ) ) {
			$profile = wp_remote_get( $url );

			if ( is_wp_error( $profile ) ) {
				return new WP_Error( 'site_down', esc_html__( 'Unable to communicate with Instagram.', 'sober' ) );
			}

			if ( 200 != wp_remote_retrieve_response_code( $profile ) ) {
				return new WP_Error( 'invalid_response', esc_html__( 'Instagram did not return a 200.', 'sober' ) );
			}

			$shared  = explode( 'window._sharedData = ', $profile['body'] );
			$data    = explode( ';</script>', $shared[1] );
			$data    = json_decode( $data[0], true );

			if ( ! $data ) {
				return new WP_Error( 'bad_json', esc_html__( 'Instagram has returned invalid data.', 'sober' ) );
			}

			if ( isset( $data['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'] ) ) {
				$nodes = $data['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'];
			} elseif ( isset( $data['entry_data']['TagPage'][0]['graphql']['hashtag']['edge_hashtag_to_media']['edges'] ) ) {
				$nodes = $data['entry_data']['TagPage'][0]['graphql']['hashtag']['edge_hashtag_to_media']['edges'];
			} else {
				return new WP_Error( 'bad_json', esc_html__( 'Instagram has returned invalid data.', 'sober' ) );
			}

			if ( ! is_array( $nodes ) ) {
				return new WP_Error( 'bad_array', esc_html__( 'Instagram has returned invalid data.', 'sober' ) );
			}

			$images = array();
			foreach ( $nodes as $node ) {
				$node = $node['node'];

				if ( isset( $node['edge_media_to_caption']['edges'][0]['node']['text'] ) ) {
					$caption = $node['edge_media_to_caption']['edges'][0]['node']['text'];
				} else {
					$caption = '';
				}

				$images[] = array(
					'description' => $caption,
					'link'        => trailingslashit( '//instagram.com/p/' . $node['shortcode'] ),
					'time'        => $node['taken_at_timestamp'],
					'comments'    => $node['edge_media_to_comment']['count'],
					'likes'       => $node['edge_media_preview_like']['count'],
					'thumbnail'   => $node['thumbnail_resources'][0]['src'],
					'small'       => $node['thumbnail_resources'][2]['src'],
					'large'       => $node['thumbnail_src'],
					'original'    => $node['display_url'],
					'type'        => $node['is_video'] ? 'video' : 'image',
				);
			}

			$images = serialize( $images );
			set_transient( $transient_key, $images, 2 * 3600 );
		}

		if ( ! empty( $images ) ) {
			return unserialize( $images );
		} else {
			return new WP_Error( 'no_images', esc_html__( 'Instagram did not return any images.', 'sober' ) );
		}
	}

	/**
	 * Get Instagram medias by access token
	 *
	 * @param string $access_token
	 * @param int $limit
	 * @return array|WP_Error
	 */
	public function get_photos_by_token( $access_token, $limit = 12 ) {
		if ( ! function_exists( 'sober_fetch_instagram_media' ) ) {
			return new WP_Error( 'not_sober', esc_html__( 'This widget works with the Sober theme only.', 'sober' ) );
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
				$next = $fetched['paging']['cursors']['after'];
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

	/**
	 * Display a single Instagram photo
	 *
	 * @param array $media
	 */
	public function display_media( $media, $as_background = false, $tag = 'li' ) {
		if ( function_exists( 'sober_instagram_image' ) ) {
			sober_instagram_image( $media, $as_background, $tag );
			return;
		}

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

endif;