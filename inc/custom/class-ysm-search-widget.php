<?php
/**
 * Widget with search widgets selector
 *
 * @author YummyWP
 */
class Ysm_Search_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'ysm_search_widget',
			__( 'Smart Search', 'smart-woocommerce-search' ),
			array(
				'classname'   => 'ysm_search_widget',
				'description' => __( 'Displays search box', 'smart-woocommerce-search' ),
			)
		);
	}

	function widget( $args, $instance ) {
		$title = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) : '';

		if ( ! isset( $instance['widget_id'] ) ) {
			$instance['widget_id'] = 0;
		}

		echo $args['before_widget'];/* @codingStandardsIgnoreLine */
		if ( $title ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title'];/* @codingStandardsIgnoreLine */
		}
		echo do_shortcode( '[smart_search id="' . intval( $instance['widget_id'] ) . '"]' );
		echo $args['after_widget'];/* @codingStandardsIgnoreLine */
	}

	function form( $instance ) {
		$widgets = ysm_get_custom_widgets();

		if ( ! isset( $instance['title'] ) ) {
			$instance['title'] = '';
		}

		if ( ! isset( $instance['widget_id'] ) ) {
			$instance['widget_id'] = '';
		}
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'smart-woocommerce-search' ); ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'widget_id' ) ); ?>"><?php esc_html_e( 'Select Widget:', 'smart-woocommerce-search' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'widget_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_id' ) ); ?>" class="widefat">
				<?php foreach ( $widgets as $id => $obj ) { ?>
					<option <?php selected( $id, $instance['widget_id'] ); ?> value="<?php echo esc_attr( $id ); ?>">
						<?php echo esc_html( $obj['name'] ); ?>
					</option>
				<?php } ?>
			</select>
		</p>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['widget_id'] = intval( $new_instance['widget_id'] );
		return $instance;
	}
}
