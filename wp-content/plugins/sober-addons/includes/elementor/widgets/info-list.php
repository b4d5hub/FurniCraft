<?php
namespace SoberAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Info List widget
 */
class Info_List extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-info-list';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] Info List', 'sober' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-bullet-list';
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
		return [ 'info list', 'list', 'sober' ];
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
			'section_info_list',
			[ 'label' => __( 'Info List', 'sober' ) ]
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
			]
		);

		$repeater->add_control(
			'label',
			[
				'label' => __( 'Label', 'sober' ),
				'type' => Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'value',
			[
				'label' => __( 'Value', 'sober' ),
				'type' => Controls_Manager::TEXTAREA,
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
			'list',
			[
				'label'       => __( 'List', 'sober' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default' => [
					[
						'icon' => [
							'value' => 'fas fa-home',
							'library' => 'fa-solid',
						],
						'label' => __( 'Address', 'sober' ),
						'value' => __( '9606 North MoPac Expressway', 'sober' ),
					],
					[
						'icon' => [
							'value' => 'fas fa-phone-alt',
							'library' => 'fa-solid',
						],
						'label' => __( 'Phone', 'sober' ),
						'value' => __( '+1 248-785-8545', 'sober' ),
					],
					[
						'icon' => [
							'value' => 'fas fa-fax',
							'library' => 'fa-solid',
						],
						'label' => __( 'Fax', 'sober' ),
						'value' => __( '+1 248-785-8545', 'sober' ),
					],
					[
						'icon' => [
							'value' => 'fas fa-envelope',
							'library' => 'fa-solid',
						],
						'label' => __( 'Email', 'sober' ),
						'value' => __( 'sober@uix.store', 'sober' ),
					],
				],
				'title_field' => '{{{ label }}}: {{{ value }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_list',
			[
				'label' => __( 'List', 'sober' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'list_space',
			[
				'label' => __( 'Space Between', 'sober' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
					],
				],
				'default' => [
					'size' => 30,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .sober-info-list__info' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => __( 'Icon', 'sober' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-info-list__icon' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Size', 'sober' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 100,
					],
				],
				'default' => [
					'size' => '',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .sober-info-list__icon' => 'font-size: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'icon_space',
			[
				'label' => __( 'Space', 'sober' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 24,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .sober-info-list__icon' => 'margin-right: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_label',
			[
				'label' => __( 'Label', 'sober' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-info-list__label' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .sober-info-list__label',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_value',
			[
				'label' => __( 'Value', 'sober' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'value_color',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-info-list__value' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'value_typography',
				'selector' => '{{WRAPPER}} .sober-info-list__value',
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
		<div class="sober-info-list sober-info-list--elementor">
			<ul>
				<?php foreach ( (array) $settings['list'] as $index => $item ) : ?>
					<li class="sober-info-list__info">
						<span class="sober-info-list__icon info-icon"><?php Icons_Manager::render_icon( $item['icon'] ); ?></span>
						<span class="sober-info-list__label info-name"><?php echo esc_html( $item['label'] ) ?></span>
						<span class="sober-info-list__value info-value"><?php
							if ( $item['link']['url'] ) {
								$link_key = 'link_' . $index;

								$this->add_render_attribute( $link_key, 'href', esc_url( $item['link']['url'] ) );

								if ( $item['link']['is_external'] ) {
									$this->add_render_attribute( $link_key, 'target', '_blank' );
								}

								if ( $item['link']['nofollow'] ) {
									$this->add_render_attribute( $link_key, 'rel', 'nofollow' );
								}

								echo '<a ' . $this->get_render_attribute_string( $link_key ) . '>';
							}

							echo $item['value'];

							if ( $item['link']['url'] ) {
								echo '</a>';
							}
						?></span>
					</li>
				<?php endforeach ?>
			</ul>
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

		<div class="sober-info-list sober-info-list--elementor">
			<ul>
				<# _.each( settings.list, function( item, index ) { #>
					<li class="sober-info-list__info">
						<span class="sober-info-list__icon info-icon"><#
							var icon = elementor.helpers.renderIcon( view, item.icon, { 'aria-hidden': true }, 'i' , 'object' );

							if ( icon.rendered ) { #>{{{ icon.value }}}<# }
						#></span>
						<span class="sober-info-list__label info-name">{{ item.label }}</span>
						<span class="sober-info-list__value info-value"><#
							if ( item.link.url ) {
								var link_key = 'link_' + index.toString();

								view.addRenderAttribute( link_key, 'href', item.link.url );

								if ( item.link.is_external ) {
									view.addRenderAttribute( link_key, 'target', '_blank' )
								}

								if ( item.link.nofollow ) {
									view.addRenderAttribute( link_key, 'rel', 'nofollow' )
								}

								#><a {{{ view.getRenderAttributeString( link_key ) }}}><#
							} #>
							{{{ item.value }}}
							<#
							if ( item.link.url ) { #>
							</a>
							<# }
						#></span>
					</li>
				<# } ); #>
			</ul>
		</div>

		<?php
	}
}
