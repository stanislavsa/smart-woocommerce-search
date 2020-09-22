<?php
/**
 * Class Ysm_Setting
 * @author YummyWP
 */
class Ysm_Setting {

	/**
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Ysm_Setting constructor.
	 */
	private function __construct() {}

	/**
	 * Cloning is not allowed
	 */
	private function __clone() {}

	/**
	 * Get instance
	 * @return null|Ysm_Setting
	 */
	public static function init() {
		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/** Check args and retrieve setting html output
	 * @param $id
	 * @param $args
	 */
	public function get_setting_html( $id, $args ) {

		if ( isset( $args['choices'] ) ) {
			$args['choices'] = (array) $args['choices'];
		}

		$defaults = array(
			'type'              => 'text',
			'title'             => '',
			'description'       => '',
			'placeholder'       => '',
			'choices'           => array(),
			'fields'            => array(),
			'value'             => '',
			'disabled'          => false,
			'multiple'          => false,
			'class'             => '',
			'callback'          => null,
			'name'              => 'settings[' . $id . ']',
		);

		$args = wp_parse_args( $args, $defaults );

		if ( method_exists( $this, 'get_' . $args['type'] . '_html' ) ) {
			?>
			<tr valign="top">
				<th scope="row">
					<?php echo wp_kses_post( $args['title'] ); ?>
				</th>
				<td>
					<fieldset class="<?php echo esc_attr( $args['class'] ); ?>">
						<legend class="screen-reader-text"><span><?php echo wp_kses_post( $args['title'] ); ?></span></legend>
						<?php echo $this->{'get_' . $args['type'] . '_html'}( $id, $args ); /* @codingStandardsIgnoreLine */ ?>
					</fieldset>
				</td>
			</tr>
			<?php
		}
	}

	/**
	 * Retrieve text <input> html
	 * @param $id
	 * @param $args
	 * @return string
	 */
	public function get_text_html( $id, $args ) {
		ob_start();
		?>
		<label for="<?php echo esc_attr( $id ); ?>">
			<input value="<?php echo esc_attr( $args['value'] ); ?>" placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>" <?php disabled( $args['disabled'], true ); ?>
				   type="<?php echo esc_attr( $args['type'] ); ?>" class="code" name="<?php echo esc_attr( $args['name'] ); ?>" id="<?php echo esc_attr( $id ); ?>" />
		</label>
		<p class="description">
			<?php echo wp_kses_post( $args['description'] ); ?>
		</p>
		<?php

		return ob_get_clean();
	}

	/**
	 * Retrieve <checkbox> html
	 * @param $id
	 * @param $args
	 * @return string
	 */
	public function get_checkbox_html($id, $args) {
		ob_start();
		?>
		<input type="hidden" name="<?php echo esc_attr( $args['name'] ); ?>" value="<?php echo (int) $args['value']; ?>">
		<input value="1" <?php checked( (int) $args['value'], 1 ); ?> <?php disabled( $args['disabled'], true ); ?>
			   type="<?php echo esc_attr( $args['type'] ); ?>"
			   id="<?php echo esc_attr( $id ); ?>" class="ymapp-switcher" />
		<label for="<?php echo esc_attr( $id ); ?>">
			<?php echo wp_kses_post( $args['description'] ); ?>
		</label>
		<?php

		return ob_get_clean();
	}

	/**
	 * Retrieve <select> html
	 * @param $id
	 * @param $args
	 * @return string
	 */
	public function get_select_html( $id, $args ) {
		if ( ! is_array( $args['value'] ) ) {
			$args['value'] = explode( ',', $args['value'] );
			$args['value'] = array_map( 'trim', $args['value'] );
		}

		ob_start();
		?>
		<select class="select" name="<?php echo esc_attr( $args['name'] ); ?>[]" id="<?php echo esc_attr( $id ); ?>" <?php disabled( $args['disabled'], true ); ?> <?php echo $args['multiple'] ? 'multiple' : ''; ?>>
			<?php foreach ( $args['choices'] as $key => $value ) { ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php echo in_array( $key, $args['value'] ) ? 'selected' : ''; ?>><?php echo esc_html( $value ); ?></option>
			<?php } ?>
		</select>
		<p class="description">
			<?php
			echo wp_kses(
				$args['description'],
				array( 'img' => array( 'src' => 1, 'style' => 1, 'class' => 1 ) )
			);
			?>
		</p>
		<?php

		return ob_get_clean();
	}

	/**
	 * Retrieve <textarea> html
	 * @param $id
	 * @param $args
	 * @return string
	 */
	public function get_textarea_html( $id, $args ) {
		ob_start();
		?>
		<textarea rows="3" cols="20" class="input-text wide-input"
				  type="<?php echo esc_attr( $args['type'] ); ?>" name="<?php echo esc_attr( $args['name'] ); ?>" id="<?php echo esc_attr( $id ); ?>"
				  placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>" <?php disabled( $args['disabled'], true ); ?>><?php echo esc_textarea( $args['value'] ); ?></textarea>
		<p class="description">
			<?php echo wp_kses_post( $args['description'] ); ?>
		</p>
		<?php

		return ob_get_clean();
	}

	/**
	 * Retrieve colorpicker html
	 * @param $id
	 * @param $args
	 * @return string
	 */
	public function get_color_html( $id, $args ) {
		ob_start();
		?>
		<input class="sm-color-picker" type="text" name="<?php echo esc_attr( $args['name'] ); ?>" id="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $args['value'] ); ?>" />
		<p class="description">
			<?php echo wp_kses_post( $args['description'] ); ?>
		</p>
		<?php

		return ob_get_clean();
	}

	/**
	 * Retrieve repeater html
	 * @param $id
	 * @param $args
	 * @return string
	 */
	public function get_repeater_html( $id, $args ) {
		if ( empty( $args['fields'] ) || ! is_array( $args['fields'] ) ) {
			return '';
		}

		$arr = [];

		foreach ( $args['fields'] as $field_slug => $field ) {
			$arr[ $field_slug ] = [];
			if ( ! empty( $args['value'][ $field_slug ] ) ) {
				$arr[ $field_slug ] = $args['value'][ $field_slug ];
			} else {
				$arr[ $field_slug ] = [ '' ];
			}
		}

		ob_start();
		?>
		<p class="description">
			<?php echo wp_kses_post( $args['description'] ); ?>
		</p>
		<br><br>
		<ul class="repeater-holder">
			<?php foreach ( current( $arr ) as $arr_key => $arr_val ) { ?>
				<li>
					<span class="repeater-move dashicons dashicons-move"></span>
					<?php foreach ( $args['fields'] as $f_slug => $f_name ) { ?>
						<label><?php echo esc_html( $f_name ); ?> <input name="<?php echo esc_attr( $args['name'] . '[' . $f_slug . '][]' ); ?>" value="<?php echo esc_attr( $arr[ $f_slug ][ $arr_key ] ); ?>" type="text" class="widefat" /></label>
					<?php } ?>
					<span class="repeater-delete dashicons dashicons-no"></span>
				</li>
			<?php } ?>
		</ul>
		<div class="clear"></div>
		<span class="repeater-add ymapp-button-small">Add</span>
		<?php
		return ob_get_clean();
	}

	/**
	 * Retrieve Pro notification html
	 * @param $id
	 * @param $args
	 * @return string
	 */
	public function get_pro_html( $id, $args ) {
		ob_start();
		?>
		<p class="description" style="color: red;">
			<?php echo esc_html__( 'Available in PRO', 'smart_search' ); ?>
		</p>
		<?php

		return ob_get_clean();
	}

	/**
	 * @param $value
	 * @return string
	 */
	public function validate_text( $value ) {
		$value = trim( $value );
		$value = strip_tags( stripslashes( $value ) );

		return $value;
	}

	/**
	 * @param $value
	 * @return bool|string
	 */
	public function validate_color( $value ) {
		$value = trim( $value );
		$value = strip_tags( stripslashes( $value ) );

		if ( preg_match( '/^#[a-f0-9]{6}$/i', $value ) ) {
			return $value;
		}

		return false;
	}

}
