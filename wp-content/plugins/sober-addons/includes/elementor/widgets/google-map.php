<?php
namespace SoberAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Google Map widget
 */
class Google_Map extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-google-map';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] Google Map', 'sober' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-google-maps';
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
		return [ 'google', 'map', 'sober' ];
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
			'section_google_map',
			[ 'label' => __( 'Google Map', 'sober' ) ]
		);

		$this->add_control(
			'element_id',
			[
				'label' => __( 'Value', 'sober' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'sober-google-map-' . $this->get_id(),
			]
		);

		$this->add_control(
			'api_key',
			[
				'label' => __( 'API Key', 'sober' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'address',
			[
				'label' => __( 'Address', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'separator' => 'before',
				'default' => _x( 'New York', 'The default address for Google Map', 'sober' ),
				'frontend_available' => true,
				'render_type' => 'ui',
			]
		);

		$this->add_control(
			'latlng',
			[
				'label' => __( 'Or enter coordinates', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Latitude, Longitude', 'sober' ),
				'frontend_available' => true,
				'render_type' => 'ui',
			]
		);

		$this->add_control(
			'height',
			[
				'label'     => __( 'Height', 'sober' ),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'default' => [
					'size' => 625,
				],
				'range' => [
					'px' => [
						'min' => 40,
						'max' => 1440,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sober-google-map' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'zoom',
			[
				'label' => __( 'Zoom', 'sober' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'frontend_available' => true,
				'render_type' => 'ui',
			]
		);

		$this->add_control(
			'color',
			[
				'label' => __( 'Color Scheme', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''           => __( 'Default', 'sober' ),
					'grey'       => __( 'Grey', 'sober' ),
					'inverse'    => __( 'Classic Black', 'sober' ),
					'vista-blue' => __( 'Vista Blue', 'sober' ),

				],
				'frontend_available' => true,
				'render_type' => 'ui',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'address',
			[
				'label'   => __( 'Address', 'sober' ),
				'type'    => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'latlng',
			[
				'label' => __( 'Or enter coordinates', 'sober' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Latitude, Longitude', 'sober' ),
			]
		);

		$repeater->add_control(
			'info',
			[
				'label' => __( 'Infomation', 'sober' ),
				'type' => Controls_Manager::TEXTAREA,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'icon',
			[
				'label' => __( 'Marker Icon', 'sober' ),
				'type' => Controls_Manager::MEDIA,
				'skin' => 'inline',
				'exclude_inline_options' => [ 'icon' ],
			]
		);

		$this->add_control(
			'markers',
			[
				'label'       => __( 'Markers', 'sober' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ address }}}',
				'default' => [
					[ 'address' => _x( 'New York', 'The default address for Google Map', 'sober' ) ]
				],
				'frontend_available' => true,
				'separator' => 'before',
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

		$latlng = explode( ',', $settings['latlng'] );

		if ( count( $latlng ) > 1 ) {
			$coordinates = [
				'lat' => floatval( $latlng[0] ),
				'lng' => floatval( $latlng[1] ),
			];
		}

		if ( ! isset( $coordinates ) ) {
			$coordinates = \Sober_Shortcodes::get_coordinates( $settings['address'], $settings['api_key'] );
		}

		if ( ! empty( $coordinates['error'] ) ) {
			echo $coordinates['error'];
			return;
		}

		if ( isset( $coordinates['address'] ) ) {
			unset( $coordinates['address'] );
		}

		$this->add_render_attribute( 'map', 'id', $settings['element_id'] );
		$this->add_render_attribute( 'map', 'class', ['sober-google-map'] );
		$this->add_render_attribute( 'map', 'data-location', json_encode( $coordinates ) );

		wp_enqueue_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . $settings['api_key'] );
		echo '<div ' . $this->get_render_attribute_string( 'map' ) . '></div>';

		if ( $settings['markers'] ) {
			echo '<div class="sober-google-map__markers">';
			foreach ( $settings['markers'] as $marker ) {
				$info = $marker['info'];
				unset( $marker['info'] );
				printf( '<div class="sober-google-map__marker" data-marker="%s">%s</div>', esc_attr( json_encode( $marker ) ), wpautop( $info ) );
			}
			echo '</div>';
		}
	}

	/**
	 * Render widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 */
	protected function content_template() {
		?>
		<#
		view.addRenderAttribute( 'map', 'id', [ settings.element_id ] );
		view.addRenderAttribute( 'map', 'class', [ 'sober-google-map' ] );
		#>
		<div {{{ view.getRenderAttributeString( 'map' ) }}}></div>
		<# if ( settings.markers ) { #>
			<div class="sober-google-map__markers">
				<# for ( var i = 0; i < settings.markers.length; i++ ) { #>
					<#
					var marker = settings.markers[i];
					var info = marker.info;
					delete marker.info;
					#>
					<div class="sober-google-map__marker" data-marker="{{ JSON.stringify( marker ) }}"><p>{{{ info.replace(/(?:\r\n|\r|\n)/g, '<br>') }}}</p></div>
				<# } #>
			</div>
		<# } #>
		<?php
	}
}
