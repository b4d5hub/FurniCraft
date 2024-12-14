<?php
namespace SoberAddons\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Post grid widget
 */
class Posts_Grid extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'sober-posts-grid';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( '[Sober] Posts Grid', 'sober' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-grid';
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
		return [ 'posts grid', 'posts', 'sober' ];
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
			'section_products',
			[ 'label' => __( 'Posts Grid', 'sober' ) ]
		);

		$this->add_control(
			'limit',
			[
				'label'     => __( 'Number Of Posts', 'sober' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => -1,
				'max'       => 100,
				'step'      => 1,
				'default'   => 3,
			]
		);

		$this->add_control(
			'columns',
			[
				'label' => __( 'Columns', 'sober' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					3 => __( '3 Columns', 'sober' ),
					4 => __( '4 Columns', 'sober' ),
				],
				'default' => 3,
			]
		);

		$this->add_control(
			'category',
			[
				'label' => __( 'Category', 'sober' ),
				'type' => Controls_Manager::SELECT2,
				'options' => $this->get_post_categories(),
				'default' => '',
				'multiple' => true,
			]
		);

		$this->add_control(
			'post_meta',
			[
				'label'        => __( 'Post Meta', 'sober' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'sober' ),
				'label_off'    => __( 'No', 'sober' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
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

		$atts = [
			'per_page'  => $settings['limit'],
			'columns'   => $settings['columns'],
			'category'  => is_array( $settings['category'] ) ? implode( ',', $settings['category'] ): $settings['category'],
			'hide_meta' => 'yes' != $settings['post_meta'],
			'el_class'  => 'sober-post-grid--elementor',
		];

		echo \Sober_Shortcodes::post_grid( $atts );
	}

	/**
	 * Get posts categories
	 *
	 * @param int $parent
	 * @param int $level
	 * @return array
	 */
	protected function get_post_categories( $parent = 0, $level = 0 ) {
		$output = [];
		$level = $parent ? $level : 0; // Level 0 if parent is 0.

		$cats = get_terms( [
			'taxonomy'   => 'category',
			'hide_empty' => false,
			'orderby'    => 'title',
			'order'      => 'ASC',
			'parent'     => $parent,
		] );

		if ( ! $cats || is_wp_error( $cats ) ) {
			return $output;
		}

		foreach ( $cats as $cat ) {
			$output[ $cat->slug ] = str_repeat( '--', $level ) . $cat->name;

			$level++;

			$output += $this->get_post_categories( $cat->term_id, $level );

			$level--;
		}

		return $output;
	}
}
