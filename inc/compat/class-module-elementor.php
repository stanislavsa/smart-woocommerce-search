<?php
namespace YSWS\Compat\Elementor;

/**
 * Elementor Smart Search Widget.
 *
 */
class Elementor_Smart_Search_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'smart_search';
	}

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return 'Smart Search';
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-search';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'search' ];
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'general', 'basic' ];
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_smart_search',
			[
				'label' => __( 'Smart Search', 'smart-woocommerce-search' ),
			]
		);

		$widgets_list = ysm_get_custom_widgets();
		$opts = [
			'' => __( 'No value', 'smart-woocommerce-search' ),
		];

		if ( ! empty( $widgets_list ) ) {
			foreach ( $widgets_list as $id => $obj ) {
				$opts[ $id ] = __( $obj['name'], 'smart-woocommerce-search' );
			}
		}

		$this->add_control(
			'ysm_widget_id',
			[
				'label'   => __( 'Select one of search widgets', 'smart-woocommerce-search' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => $opts,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		if ( empty( $settings['ysm_widget_id'] ) && ! empty( $settings['id'] ) ) {
			$settings['ysm_widget_id'] = $settings['id'];
		}

		if ( ! empty( $settings['ysm_widget_id'] ) ) {
			echo '<div class="smart_search-elementor-widget">';
			echo do_shortcode( sprintf( '[smart_search id="%s"]', $settings['ysm_widget_id'] ) );
			echo '</div>';
		}
	}
}
