<?php
namespace SoberAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Image_Size;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Team member widget
 */
class Team_Member extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-team-member';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] Team Member', 'sober' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-person';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return ['sober'];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'team member', 'member', 'user', 'sober' ];
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_member',
			[ 'label' => __( 'Team Member', 'sober' ) ]
		);

		$this->add_control(
			'image',
			[
				'label' => __( 'Image', 'sober' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => SOBER_ADDONS_URL . '/assets/images/man-placeholder.png',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default' => 'full',
				'separator' => 'none',
			]
		);

		$this->add_control(
			'name',
			[
				'label' => __( 'Full Name', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Andrew Sober', 'sober' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'job',
			[
				'label' => __( 'Job Title', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Co-founder', 'sober' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_socials',
			[ 'label' => __( 'Social Profile', 'sober' ) ]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'icon',
			[
				'label'   => __( 'Icon', 'sober' ),
				'type'    => Controls_Manager::ICONS,
				'label_block' => true,
				'default' => [
					'value' => 'fab fa-wordpress',
					'library' => 'fa-brands',
				],
				'recommended' => [
					'fa-brands' => [
						'android',
						'apple',
						'behance',
						'bitbucket',
						'codepen',
						'delicious',
						'deviantart',
						'digg',
						'dribbble',
						'elementor',
						'facebook',
						'flickr',
						'foursquare',
						'free-code-camp',
						'github',
						'gitlab',
						'globe',
						'houzz',
						'instagram',
						'jsfiddle',
						'linkedin',
						'medium',
						'meetup',
						'mixcloud',
						'odnoklassniki',
						'pinterest',
						'product-hunt',
						'reddit',
						'shopping-cart',
						'skype',
						'slideshare',
						'snapchat',
						'soundcloud',
						'spotify',
						'stack-overflow',
						'steam',
						'stumbleupon',
						'telegram',
						'thumb-tack',
						'tripadvisor',
						'tumblr',
						'twitch',
						'twitter',
						'viber',
						'vimeo',
						'vk',
						'weibo',
						'weixin',
						'whatsapp',
						'wordpress',
						'xing',
						'yelp',
						'youtube',
						'500px',
					],
					'fa-solid' => [
						'envelope',
						'link',
						'rss',
					],
				],
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => __( 'Link', 'sober' ),
				'type' => Controls_Manager::URL,
				'label_block' => true,
				'default' => [
					'is_external' => 'true',
				],
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'sober' ),
			]
		);

		$this->add_control(
			'social_icons',
			[
				'label'       => __( 'Social Icons', 'sober' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'prevent_empty' => false,
				'default' => [
					[
						'icon' => [
							'value' => 'fab fa-facebook',
							'library' => 'fa-brands',
						],
					],
					[
						'icon' => [
							'value' => 'fab fa-twitter',
							'library' => 'fa-brands',
						],
					],
				],
				'title_field' => '<# var migrated = "undefined" !== typeof __fa4_migrated, social = ( "undefined" === typeof social ) ? false : social; #>{{{ elementor.helpers.getSocialNetworkNameFromIcon( icon, social, true, migrated, true ) }}}',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="sober-team-member sober-team-member--elementor">
			<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings ); ?>
			<div class="member-socials sober-team-member__socials">
				<?php
				foreach ( $settings['social_icons'] as $index => $icon ) {
					$link_key = 'link_' . $index;

					$this->add_render_attribute( $link_key, 'href', $icon['link']['url'] );

					if ( $icon['link']['is_external'] ) {
						$this->add_render_attribute( $link_key, 'target', '_blank' );
					}

					if ( $icon['link']['nofollow'] ) {
						$this->add_render_attribute( $link_key, 'rel', 'nofollow' );
					}
					?>
					<a <?php echo $this->get_render_attribute_string( $link_key ); ?>><?php Icons_Manager::render_icon( $icon['icon'] ); ?></a>
					<?php
				}
				?>
			</div>
			<div class="member-info sober-team-member__info">
				<h4 class="member-name sober-team-member__name"><?php echo esc_html( $settings['name'] ) ?></h4>
				<span class="member-job sober-team-member__job"><?php echo esc_html( $settings['job'] ) ?></span>
			</div>
		</div>
		<?php
	}

	/**
	 * Render widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 */
	protected function content_template() {
		?>
		<#
		var iconHTML = {},
			image_url = '<?php echo SOBER_ADDONS_URL . '/assets/images/man-placeholder.png'; ?>';

		if ( settings.image.url ) {
			var image = {
				id: settings.image.id,
				url: settings.image.url,
				size: settings.image_size,
				dimension: settings.image_custom_dimension,
				model: view.getEditModel()
			};

			image_url = elementor.imagesManager.getImageUrl( image );
		}

		#>
		<div class="sober-team-member sober-team-member--elementor">
			<# if ( image_url ) { #>
				<img src="{{ image_url }}">
			<# } #>
			<div class="member-socials sober-team-member__socials">
				<#
				_.each( settings.social_icons, function( item, index ) {
					iconHTML = elementor.helpers.renderIcon( view, item.icon, {}, 'i', 'object' );

					if ( iconHTML.rendered ) { #>
						<a href="{{ item.link.url }}">{{{ iconHTML.value }}}</a>
					<# }
				} );
				#>
			</div>
			<div class="member-info sober-team-member__info">
				<h4 class="member-name sober-team-member__name">{{ settings.name }}</h4>
				<span class="member-job sober-team-member__job">{{ settings.job }}</span>
			</div>
		</div>
		<?php
	}
}
