<?php
namespace SoberAddons\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

/**
 * Tabs widget.
 */
class Tabs extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve tabs widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-tabs';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve tabs widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] Tabs', 'sober' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve tabs widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-tabs';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'tabs', 'accordion', 'toggle', 'sober' ];
	}

	/**
	 * Register tabs widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_tabs',
			[
				'label' => __( 'Tabs', 'sober' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'tab_title',
			[
				'label' => __( 'Title & Description', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Tab Title', 'sober' ),
				'placeholder' => __( 'Tab Title', 'sober' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'tab_content',
			[
				'label' => __( 'Content', 'sober' ),
				'default' => __( 'Tab Content', 'sober' ),
				'placeholder' => __( 'Tab Content', 'sober' ),
				'type' => Controls_Manager::WYSIWYG,
				'show_label' => false,
				'dynamic' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'tabs',
			[
				'label' => __( 'Tabs Items', 'sober' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'tab_title' => __( 'Tab #1', 'sober' ),
						'tab_content' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'sober' ),
					],
					[
						'tab_title' => __( 'Tab #2', 'sober' ),
						'tab_content' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'sober' ),
					],
				],
				'title_field' => '{{{ tab_title }}}',
			]
		);

		$this->add_control(
			'type',
			[
				'label' => __( 'Type', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal' => __( 'Horizontal', 'sober' ),
					'vertical' => __( 'Vertical', 'sober' ),
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tabs_style',
			[
				'label' => __( 'Tabs', 'sober' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'navigation_width',
			[
				'label' => __( 'Navigation Width', 'sober' ),
				'type' => Controls_Manager::SLIDER,
				'separator' => 'after',
				'default' => [
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => 10,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sober-tabs__tabs' => 'width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'type' => 'vertical',
				],
			]
		);

		$this->add_control(
			'heading_title',
			[
				'label' => __( 'Title', 'sober' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'tab_color',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-tab__title, {{WRAPPER}} .sober-tab__title a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .sober-tab__title a:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_active_color',
			[
				'label' => __( 'Active Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-tab__title.sober-tab--active a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .sober-tab__title.sober-tab--active a:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tab_typography',
				'selector' => '{{WRAPPER}} .sober-tab__title a',
			]
		);

		$this->add_control(
			'title_align',
			[
				'label' => __( 'Alignment', 'sober' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'sober' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'sober' ),
						'icon' => 'eicon-text-align-center',
					],
					'justify' => [
						'title' => __( 'Justified', 'sober' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => 'center',
				'condition' => [
					'type' => 'horizontal',
				],
			]
		);

		$this->add_control(
			'heading_content',
			[
				'label' => __( 'Content', 'sober' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'content_color',
			[
				'label' => __( 'Color', 'sober' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sober-tab__content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} .sober-tab__content',
			]
		);

		$this->add_control(
			'content_align',
			[
				'label' => __( 'Alignment', 'sober' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'initial' => [
						'title' => __( 'Left', 'sober' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'sober' ),
						'icon' => 'eicon-text-align-center',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .sober-tab__content' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'type' => 'horizontal',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render tabs widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$tabs = $settings['tabs'];

		$this->add_render_attribute( 'wrapper', [
			'class' => [ 'sober-tabs', 'sober-tabs--' . $settings['type'] ],
			'role'  => 'tablist'
		] );

		$this->add_render_attribute( 'tabs', 'class', [ 'sober-tabs__tabs' ] );

		if ( 'horizontal' == $settings['type'] ) {
			$this->add_render_attribute( 'tabs', 'class', 'sober-tabs__tabs--' . $settings['title_align'] );
		}

		$id_int = substr( $this->get_id_int(), 0, 3 );
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'tabs' ); ?>>
				<ul>
					<?php
					foreach ( $tabs as $index => $item ) :
						$tab_count = $index + 1;

						$tab_title_setting_key = $this->get_repeater_setting_key( 'tab_title', 'tabs', $index );

						$this->add_render_attribute( $tab_title_setting_key, [
							'id' => 'sober-tab-title-' . $id_int . $tab_count,
							'class' => [ 'sober-tab__title' ],
							'data-tab' => $tab_count,
							'role' => 'tab',
							'aria-controls' => 'sober-tab-content-' . $id_int . $tab_count,
						] );
						?>
						<li <?php echo $this->get_render_attribute_string( $tab_title_setting_key ); ?>><a href="#sober-tab-content-<?php echo $id_int . $tab_count; ?>"><?php echo $item['tab_title']; ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div class="sober-tabs__content">
				<?php
				foreach ( $tabs as $index => $item ) :
					$tab_count = $index + 1;

					$tab_content_setting_key = $this->get_repeater_setting_key( 'tab_content', 'tabs', $index );

					$this->add_render_attribute( $tab_content_setting_key, [
						'id' => 'sober-tab-content-' . $id_int . $tab_count,
						'class' => [ 'sober-tab__content', 'clearfix' ],
						'data-tab' => $tab_count,
						'role' => 'tabpanel',
						'aria-labelledby' => 'sober-tab-title-' . $id_int . $tab_count,
					] );

					$this->add_inline_editing_attributes( $tab_content_setting_key, 'advanced' );
					?>
					<div <?php echo $this->get_render_attribute_string( $tab_content_setting_key ); ?>><?php echo $this->parse_text_editor( $item['tab_content'] ); ?></div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render tabs widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 */
	protected function content_template() {
		?>
		<div class="sober-tabs sober-tabs--{{ settings.type }}" role="tablist">
			<#
			if ( settings.tabs ) {
				var tabindex = view.getIDInt().toString().substr( 0, 3 );

				view.addRenderAttribute( 'tabs', 'class', [ 'sober-tabs__tabs' ] );

				if ( 'horizontal' === settings.type ) {
					view.addRenderAttribute( 'tabs', 'class', 'sober-tabs__tabs--' + settings.title_align );
				}
				#>
				<div class="sober-tabs__tabs sober-tabs__tabs--{{ settings.title_align }}">
					<ul>
						<#
						_.each( settings.tabs, function( item, index ) {
							var tabCount = index + 1;
							#>
							<li id="sober-tab-title-{{ tabindex + tabCount }}" class="sober-tab__title" data-tab="{{ tabCount }}" role="tab" aria-controls="sober-tab-content-{{ tabindex + tabCount }}">
								<a href="#sober-tab-content-{{ tabindex + tabCount }}">{{{ item.tab_title }}}</a>
						</li>
						<# } ); #>
					</ul>
				</div>
				<div class="sober-tabs__content">
					<#
					_.each( settings.tabs, function( item, index ) {
						var tabCount = index + 1,
							tabContentKey = view.getRepeaterSettingKey( 'tab_content', 'tabs',index );

						view.addRenderAttribute( tabContentKey, {
							'id': 'sober-tab-content-' + tabindex + tabCount,
							'class': [ 'sober-tab__content', 'clearfix', 'elementor-repeater-item-' + item._id ],
							'data-tab': tabCount,
							'role' : 'tabpanel',
							'aria-labelledby' : 'sober-tab-title-' + tabindex + tabCount
						} );

						view.addInlineEditingAttributes( tabContentKey, 'advanced' );
						#>
						<div {{{ view.getRenderAttributeString( tabContentKey ) }}}>{{{ item.tab_content }}}</div>
					<# } ); #>
				</div>
			<# } #>
		</div>
		<?php
	}
}
