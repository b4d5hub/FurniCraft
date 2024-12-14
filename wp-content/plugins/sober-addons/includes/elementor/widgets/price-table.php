<?php
namespace SoberAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Price table widget
 */
class Price_Table extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-price-table';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] Price Table', 'sober' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-price-table';
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
		return [ 'price table', 'pricing table', 'price', 'sober' ];
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
			'section_table_header',
			[ 'label' => __( 'Header & Pricing', 'sober' ) ]
		);

		$this->add_control(
			'name',
			[
				'label'   => __( 'Title', 'sober' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Pro', 'sober' ),
			]
		);

		$this->add_control(
			'price',
			[
				'label'     => __( 'Price', 'sober' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 29.90,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'price_decimal',
			[
				'label'     => __( 'Price Decimal', 'sober' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 2,
			]
		);

		$this->add_control(
			'currency',
			[
				'label'              => __( 'Currency', 'sober' ),
				'type'               => Controls_Manager::TEXT,
				'default'            => '$',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'recurrence',
			[
				'label'              => __( 'Recurrence', 'sober' ),
				'description'        => __( 'Recurring payment unit', 'sober' ),
				'type'               => Controls_Manager::TEXT,
				'default'            => __( 'Per Month', 'sober' ),
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_features',
			[ 'label' => __( 'Features', 'sober' ) ]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'feature_name',
			[
				'label'   => __( 'Feature Name', 'sober' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Feature name', 'sober' ),
			]
		);

		$repeater->add_control(
			'feature_value',
			[
				'label'   => __( 'Feature Value', 'sober' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Feature value', 'sober' ),
			]
		);

		$this->add_control(
			'features',
			[
				'label'       => __( 'Features List', 'sober' ),
				'description' => __( 'Recurring payment unit', 'sober' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'feature_name' => __( 'Diskspace', 'sober' ),
						'feature_value' => __( '8 GB', 'sober' ),
					],
					[
						'feature_name' => __( 'Bandwidth', 'sober' ),
						'feature_value' => __( '100 GB', 'sober' ),
					],
					[
						'feature_name' => __( 'Sub-domains', 'sober' ),
						'feature_value' => '15',
					],
					[
						'feature_name' => __( 'Emails', 'sober' ),
						'feature_value' => '100',
					],
					[
						'feature_name' => __( 'Support', 'sober' ),
						'feature_value' => __( 'Yes', 'sober' ),
					],
				],
				'title_field' => '{{{ feature_name }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_table_footer',
			[ 'label' => __( 'Footer', 'sober' ) ]
		);

		$this->add_control(
			'button_text',
			[
				'label'   => __( 'Button Text', 'sober' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Get Started', 'sober' ),
			]
		);

		$this->add_control(
			'button_link',
			[
				'label'         => __( 'Button URL', 'sober' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => __( 'https://your-link.com', 'sober' ),
				'show_external' => true,
				'default'       => [
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_table',
			[
				'label' => __( 'Table', 'sober' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'color',
			[
				'label' => __( 'Table Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#7a6ac3',
				'selectors' => [
					'{{WRAPPER}} .sober-pricing-table__header' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .sober-pricing-table__footer .button' => 'background-color: {{VALUE}};',
				],
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

		$price = number_format( $settings['price'], $settings['price_decimal'] );

		$features = array();
		foreach ( $settings['features'] as $feature ) {
			$features[] = sprintf( '<li><span class="feature-name">%s</span><span class="feature-value">%s</span></li>', $feature['feature_name'], $feature['feature_value'] );
		}
		$features = $features ? '<ul>' . implode( '', $features ) . '</ul>' : '';

		$target = $settings['button_link']['is_external'] ? ' target="_blank"' : '';
		$nofollow = $settings['button_link']['nofollow'] ? ' rel="nofollow"' : '';
		$button = ! empty( $settings['button_text'] ) ? '<a href="' . $settings['button_link']['url'] . '" class="button"' . $target . $nofollow . '>' . esc_html( $settings['button_text'] ) . '</a>' : '';
		?>

		<div class="sober-pricing-table sober-pricing-table--elementor">
			<div class="table-header sober-pricing-table__header">
				<h3 class="plan-name"><?php echo esc_html( $settings['name'] ) ?></h3>
				<div class="pricing"><span class="currency"><?php echo esc_html( $settings['currency'] ) ?></span><?php echo esc_html( $price ) ?></div>
				<div class="recurrence"><?php echo esc_html( $settings['recurrence'] ) ?></div>
			</div>
			<div class="table-content sober-pricing-table__content">
				<?php echo $features; ?>
			</div>
			<div class="table-footer sober-pricing-table__footer">
				<?php echo $button; ?>
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
		var target = settings.button_link.is_external ? ' target="_blank"' : '';
		var nofollow = settings.button_link.nofollow ? ' rel="nofollow"' : '';
		#>

		<div class="sober-pricing-table sober-pricing-table--elementor">
			<div class="table-header sober-pricing-table__header">
				<h3 class="plan-name">{{ settings.name }}</h3>
				<div class="pricing"><span class="currency">{{ settings.currency }}</span>{{ settings.price.toFixed( Math.max( 0, settings.price_decimal ) ) }}</div>
				<div class="recurrence">{{ settings.recurrence }}</div>
			</div>
			<div class="table-content sober-pricing-table__content">
				<# if ( ! _.isEmpty( settings.features ) ) { #>
				<ul>
					<# _.each( settings.features, function( feature ) { #>
						<li><span class="feature-name">{{ feature.feature_name }}</span><span class="feature-value">{{ feature.feature_value }}</span></li>
					<#} ); #>
				</ul>
				<# } #>
			</div>
			<div class="table-footer sober-pricing-table__footer">
				<# if ( settings.button_text ) { #>
					<a href="{{ settings.button_link.url }}" class="button" {{ target }}{{ nofollow }}>{{ settings.button_text }}</a>
				<# } #>
			</div>
		</div>
		<?php
	}
}
