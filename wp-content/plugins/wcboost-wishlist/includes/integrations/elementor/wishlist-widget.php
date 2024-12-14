<?php
namespace WCBoost\Wishlist\Integrations\Elementor;

defined( 'ABSPATH' ) || exit;

use \Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use \WCBoost\Wishlist\Plugin;
use \WCBoost\Wishlist\Helper;
use \WCBoost\Wishlist\Shortcodes;

class Wishlist_Widget extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wcboost-wishlist';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Wishlist', 'wcboost-wishlist' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-heart-o wcboost-elementor-widget-icon';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return ['wcboost'];
	}

	/**
	 * Get widget keywords.
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'wishlist', 'favourite', 'like', 'wcboost' ];
	}

	/**
	 * Get custom help URL.
	 *
	 * Retrieve a URL where the user can get more information about the widget.
	 *
	 * @access public
	 * @return string Widget help URL.
	 */
	public function get_custom_help_url() {
		return 'https://wcboost.com/docs-category/wcboost-wishlists/?utm_source=elementor&utm_medium=widget_help&utm_campaign=wp-dash';
	}

	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_wishlist',
			[
				'label' => esc_html__( 'Wishlist', 'wcboost-wishlist' )
			]
		);

		$this->add_control(
			'global_settings_notice',
			[
				'type'        => Controls_Manager::NOTICE,
				'notice_type' => 'warning',
				'content'     => wp_kses_post(
					sprintf(
						/* translators: %s: Customizer link */
						__( 'To customize the wishlist layout, please use the global wishlist settings in the <a href="%s" target="_blank">Customizer</a>.', 'wcboost-wishlist' ),
						esc_url( admin_url( 'customize.php?autofocus[section]=wcboost_wishlist_page' ) )
					)
				),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_wishlist_title_style',
			[
				'label' => esc_html__( 'Wishlist title', 'wcboost-wishlist' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'title_align',
			[
				'label'   => esc_html__( 'Alignment', 'wcboost-wishlist' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'wcboost-wishlist' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wcboost-wishlist' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'wcboost-wishlist' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .wcboost-wishlist-title' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Text Color', 'wcboost-wishlist' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wcboost-wishlist-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .wcboost-wishlist-title',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render wishlist widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$this->add_render_attribute(
			'wrapper',
			'class',
			[
				'woocommerce',
				'woocommerce-wishlist',
				'wcboost-wishlist',
				'wcboost-wishlist--elementor',
			]
		);

		$wishlist = Helper::get_wishlist( get_query_var( 'wishlist_token' ) );
		$template = Shortcodes::get_wishlist_template( $wishlist );
		$args     = [
			'wishlist'   => $wishlist,
			'return_url' => apply_filters( 'wcboost_wishlist_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ),
		];

		$args = apply_filters( 'wcboost_wishlist_template_args', $args, $wishlist );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php wc_get_template( $template, $args, '', Plugin::instance()->plugin_path() . '/templates/' ); ?>
		</div>
		<?php
	}
}